<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentStoreRequest;
use App\Models\Payment;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Services\PaymentOptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class PaymentController extends Controller
{
    /**
     * Constructor to initialize Stripe API key.
     */
    public function __construct() {}

    /**
     * Show the payment form.
     * GET /payments/create
     */
    public function create(?Service $service = null)
    {
        return view('payments.create', [
            'service' => $service,
            'services' => Service::all(),
            'methods' => PaymentOptions::available(),
        ]);
    }

    /**
     * Store a payment and create a Stripe PaymentIntent.
     * POST /payments
     */
    public function store(PaymentStoreRequest $request)
    {
        $validated = $request->validated();
        
        $amount = $validated['amount'];
        if (! empty($validated['service_id'])) {
            $service = Service::findOrFail($validated['service_id']);
            $amount = $service->price;
        }
        abort_unless(is_numeric($amount) && $amount >= 0.01 && $amount <= 999999.99, 422, 'This service does not have a valid payment price.');

        $payment = Payment::create([
            'user_id' => Auth::id(),
            'service_id' => $validated['service_id'] ?? null,
            'amount' => $amount,
            'currency' => 'PKR',
            'payment_method' => $validated['payment_method'],
            'destination_snapshot' => $validated['payment_method'] === 'card' ? null : PaymentOptions::available()[$validated['payment_method']],
            'description' => $validated['description'] ?? null,
            'status' => 'pending',
        ]);

        // For card payments, create Stripe PaymentIntent
        if ($validated['payment_method'] === 'card') {
            try {
                Stripe::setApiKey(SiteSetting::get('integration_stripe_secret') ?: config('services.stripe.secret'));
                $intent = PaymentIntent::create([
                    'amount' => (int) round($amount * 100),
                    'currency' => 'pkr',
                    'metadata' => [
                        'payment_id' => $payment->id,
                        'user_id' => Auth::id(),
                        'service_id' => $validated['service_id'] ?? null,
                    ],
                ]);

                $payment->update([
                    'stripe_payment_intent_id' => $intent->id,
                    'status' => 'processing',
                ]);

                return response()->json([
                    'client_secret' => $intent->client_secret,
                    'payment_id' => $payment->id,
                ]);
            } catch (\Exception $e) {
                $payment->update(['status' => 'failed']);
                return response()->json(['error' => 'Card provider could not start this payment. Please try again later.'], 502);
            }
        }

        // For other payment methods, mark as pending and redirect
        return redirect()->route('payments.show', $payment)->with('success', 'Payment request created. Transfer the amount and submit your reference for review.');
    }

    /**
     * Display a payment.
     * GET /payments/{payment}
     */
    public function show(Payment $payment)
    {
        // Ensure user owns this payment or is admin
        $this->authorize('view', $payment);

        return view('payments.show', ['payment' => $payment, 'methods' => PaymentOptions::available()]);
    }

    public function submitProof(Request $request, Payment $payment)
    {
        abort_unless($payment->user_id === Auth::id() && $payment->status === 'pending' && $payment->payment_method !== 'card', 403);
        $validated = $request->validate([
            'payment_reference' => ['required','string','min:4','max:120'],
            'payment_proof' => ['nullable','file','mimes:jpg,jpeg,png,pdf','max:5120'],
        ]);
        $changes = ['payment_reference' => $validated['payment_reference'], 'status' => 'processing'];
        if ($request->hasFile('payment_proof')) {
            $changes['payment_proof_path'] = $request->file('payment_proof')->store('payment-proofs', 'local');
        }
        $payment->update($changes);
        return back()->with('success', 'Reference submitted. Your payment is awaiting verification.');
    }

    public function proof(Payment $payment)
    {
        $this->authorize('view', $payment);
        abort_unless($payment->payment_proof_path && Storage::disk('local')->exists($payment->payment_proof_path), 404);
        return Storage::disk('local')->download($payment->payment_proof_path);
    }

    public function review(Request $request, Payment $payment)
    {
        abort_unless(Auth::user()?->isAdmin(), 403);
        $validated = $request->validate([
            'decision' => ['required','in:completed,failed'],
            'notes' => ['nullable','string','max:1000'],
        ]);
        abort_unless($payment->payment_method !== 'card' && in_array($payment->status, ['pending','processing'], true), 422);
        if ($validated['decision'] === 'completed') {
            abort_unless($payment->payment_reference, 422, 'A transfer reference is required before verification.');
        }
        $payment->update([
            'status' => $validated['decision'],
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'paid_at' => $validated['decision'] === 'completed' ? now() : null,
            'notes' => $validated['notes'] ?? $payment->notes,
        ]);
        return back()->with('success', 'Payment review saved.');
    }

    /**
     * Confirm a payment after successful Stripe charge.
     * POST /payments/{payment}/confirm
     */
    public function confirm(Request $request, Payment $payment)
    {
        $this->authorize('update', $payment);
        abort_unless($payment->payment_method === 'card' && $payment->stripe_payment_intent_id, 422);

        if ($payment->isCompleted()) {
            return redirect()
                ->route('payments.show', $payment)
                ->with('success', 'Payment already confirmed.');
        }

        try {
            Stripe::setApiKey(SiteSetting::get('integration_stripe_secret') ?: config('services.stripe.secret'));
            // Retrieve the PaymentIntent from Stripe
            $intent = PaymentIntent::retrieve($payment->stripe_payment_intent_id);

            if ($intent->status === 'succeeded') {
                $payment->update([
                    'status' => 'completed',
                    'stripe_charge_id' => $intent->charges->data[0]->id ?? null,
                    'stripe_response' => $intent->toArray(),
                    'paid_at' => now(),
                ]);

                return redirect()
                    ->route('payments.show', $payment)
                    ->with('success', 'Payment confirmed successfully!');
            }

            return redirect()
                ->back()
                ->with('error', 'Payment failed. Please try again.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * List all payments for the authenticated user.
     * GET /payments
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            // Admins can see all payments
            $payments = Payment::with(['user', 'service'])
                ->latest()
                ->paginate(15);
        } else {
            // Regular users see only their payments
            $payments = Payment::forUser($user->id)
                ->with('service')
                ->latest()
                ->paginate(15);
        }

        return view('payments.index', ['payments' => $payments]);
    }

    /**
     * Refund a payment.
     * POST /payments/{payment}/refund
     */
    public function refund(Request $request, Payment $payment)
    {
        $this->authorize('update', $payment);

        if (!$payment->canBeRefunded() || ! $payment->stripe_charge_id) {
            return redirect()
                ->back()
                ->with('error', 'This payment cannot be refunded.');
        }

        try {
            // Refund via Stripe if it was a card payment
            Stripe::setApiKey(SiteSetting::get('integration_stripe_secret') ?: config('services.stripe.secret'));
            \Stripe\Refund::create([
                'charge' => $payment->stripe_charge_id,
                'reason' => $request->input('reason', 'requested_by_customer'),
            ]);

            $payment->update([
                'status' => 'refunded',
                'refunded_at' => now(),
                'notes' => $request->input('notes') ?? $payment->notes,
            ]);

            return redirect()
                ->back()
                ->with('success', 'Payment refunded successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Refund failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle Stripe webhook.
     * POST /webhooks/stripe
     */
    public function handleStripeWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('stripe-signature');
        $endpoint_secret = SiteSetting::get('integration_stripe_webhook_secret') ?: config('services.stripe.webhook_secret');
        if (! $endpoint_secret) return response()->json(['error' => 'Webhook not configured'], 503);

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $this->handlePaymentIntentSucceeded($event->data->object);
                break;

            case 'payment_intent.payment_failed':
                $this->handlePaymentIntentFailed($event->data->object);
                break;

            case 'charge.refunded':
                $this->handleChargeRefunded($event->data->object);
                break;
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Handle payment_intent.succeeded webhook event.
     */
    private function handlePaymentIntentSucceeded($intent)
    {
        $paymentId = $intent->metadata->payment_id ?? null;

        if (!$paymentId) {
            return;
        }

        $payment = Payment::find($paymentId);
        if ($payment && $payment->payment_method === 'card' && $payment->stripe_payment_intent_id === $intent->id && in_array($payment->status, ['pending', 'processing'], true)) {
            $payment->update([
                'status' => 'completed',
                'stripe_charge_id' => $intent->charges->data[0]->id ?? null,
                'stripe_response' => $intent->toArray(),
                'stripe_webhook_received_at' => now(),
                'paid_at' => now(),
            ]);
        }
    }

    /**
     * Handle payment_intent.payment_failed webhook event.
     */
    private function handlePaymentIntentFailed($intent)
    {
        $paymentId = $intent->metadata->payment_id ?? null;

        if (!$paymentId) {
            return;
        }

        $payment = Payment::find($paymentId);
        if ($payment && $payment->payment_method === 'card' && $payment->stripe_payment_intent_id === $intent->id && !$payment->isCompleted()) {
            $payment->update([
                'status' => 'failed',
                'stripe_response' => $intent->toArray(),
                'stripe_webhook_received_at' => now(),
            ]);
        }
    }

    /**
     * Handle charge.refunded webhook event.
     */
    private function handleChargeRefunded($charge)
    {
        $payment = Payment::where('stripe_charge_id', $charge->id)->first();

        if ($payment) {
            $payment->update([
                'status' => 'refunded',
                'stripe_response' => $charge->toArray(),
                'stripe_webhook_received_at' => now(),
                'refunded_at' => now(),
            ]);
        }
    }
}

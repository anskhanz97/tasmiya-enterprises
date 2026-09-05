<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentStoreRequest;
use App\Models\Payment;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class PaymentController extends Controller
{
    /**
     * Constructor to initialize Stripe API key.
     */
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Show the payment form.
     * GET /payments/create
     */
    public function create(?Service $service = null)
    {
        return view('payments.create', [
            'service' => $service,
            'services' => Service::all(),
        ]);
    }

    /**
     * Store a payment and create a Stripe PaymentIntent.
     * POST /payments
     */
    public function store(PaymentStoreRequest $request)
    {
        $validated = $request->validated();
        
        // Create pending payment record
        $payment = Payment::create([
            'user_id' => Auth::id(),
            'service_id' => $validated['service_id'] ?? null,
            'amount' => $validated['amount'],
            'currency' => $validated['currency'] ?? 'PKR',
            'payment_method' => $validated['payment_method'] ?? 'card',
            'description' => $validated['description'] ?? null,
            'status' => 'pending',
        ]);

        // For card payments, create Stripe PaymentIntent
        if ($validated['payment_method'] === 'card') {
            try {
                $intent = PaymentIntent::create([
                    'amount' => (int)($validated['amount'] * 100), // Convert to cents
                    'currency' => strtolower($validated['currency'] ?? 'pkr'),
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
                return response()->json(['error' => $e->getMessage()], 400);
            }
        }

        // For other payment methods, mark as pending and redirect
        return response()->json([
            'payment_id' => $payment->id,
            'redirect' => route('payments.show', $payment),
        ]);
    }

    /**
     * Display a payment.
     * GET /payments/{payment}
     */
    public function show(Payment $payment)
    {
        // Ensure user owns this payment or is admin
        $this->authorize('view', $payment);

        return view('payments.show', ['payment' => $payment]);
    }

    /**
     * Confirm a payment after successful Stripe charge.
     * POST /payments/{payment}/confirm
     */
    public function confirm(Request $request, Payment $payment)
    {
        $this->authorize('update', $payment);

        if ($payment->isCompleted()) {
            return redirect()
                ->route('payments.show', $payment)
                ->with('success', 'Payment already confirmed.');
        }

        try {
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

        if (!$payment->canBeRefunded()) {
            return redirect()
                ->back()
                ->with('error', 'This payment cannot be refunded.');
        }

        try {
            // Refund via Stripe if it was a card payment
            if ($payment->stripe_charge_id) {
                \Stripe\Refund::create([
                    'charge' => $payment->stripe_charge_id,
                    'reason' => $request->input('reason', 'requested_by_customer'),
                ]);
            }

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
        $endpoint_secret = config('services.stripe.webhook_secret');

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
        if ($payment && $payment->isPending()) {
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
        if ($payment && !$payment->isCompleted()) {
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

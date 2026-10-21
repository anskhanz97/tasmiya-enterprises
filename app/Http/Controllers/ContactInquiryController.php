<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactInquiryStoreRequest;
use App\Models\ContactInquiry;
use App\Models\SiteSetting;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactInquiryController extends Controller
{
    /**
     * Show contact inquiry form
     * GET /contact
     */
    public function create()
    {
        $divisions = \App\Models\Division::whereHas('users', fn ($q) => $q->publicInquiryRecipients())
            ->with(['users' => fn ($q) => $q->publicInquiryRecipients()->orderBy('name')->select('id', 'name', 'division_id')])
            ->get(['id', 'name']);

        return view('contact.create', compact('divisions'));
    }

    /**
     * Store a contact inquiry
     * POST /contact
     */
    public function store(ContactInquiryStoreRequest $request)
    {
        $validated = $request->validated();

        $inquiry = app(\App\Services\LeadWorkflow::class)->website($validated);

        $email = SiteSetting::get('integration_company_email');
        $smtpHost = SiteSetting::get('integration_smtp_host');
        if ($email && $smtpHost && SiteSetting::get('integration_smtp_from_email')) {
            try {
                config([
                    'mail.mailers.smtp.host' => $smtpHost,
                    'mail.mailers.smtp.port' => (int) (SiteSetting::get('integration_smtp_port') ?: 587),
                    'mail.mailers.smtp.username' => SiteSetting::get('integration_smtp_username'),
                    'mail.mailers.smtp.password' => SiteSetting::get('integration_smtp_password'),
                    'mail.mailers.smtp.timeout' => 8,
                    'mail.mailers.smtp.scheme' => SiteSetting::get('integration_smtp_encryption') === 'ssl' ? 'smtps' : 'smtp',
                    'mail.from.address' => SiteSetting::get('integration_smtp_from_email'),
                    'mail.from.name' => 'Tasmiya Enterprises',
                ]);
                Mail::purge('smtp');
                Mail::mailer('smtp')->raw("New website inquiry #{$inquiry->id}\n\nName: {$inquiry->name}\nEmail: {$inquiry->email}\nPhone: {$inquiry->phone}\nSubject: {$inquiry->subject}\n\n{$inquiry->message}", function ($mail) use ($email, $inquiry) {
                    $mail->to($email)->replyTo($inquiry->email, $inquiry->name)->subject('Website inquiry: '.$inquiry->subject);
                });
                $inquiry->email_delivery_status = 'sent';
            } catch (\Throwable $e) {
                Log::error('Inquiry email failed', ['inquiry_id' => $inquiry->id, 'error' => $e->getMessage()]);
                $inquiry->email_delivery_status = 'failed';
            }
        }

        if (SiteSetting::get('integration_company_whatsapp') && SiteSetting::get('integration_whatsapp_template_name')) {
            $inquiry->whatsapp_delivery_status = (new WhatsAppService)->notifyCompanyAboutInquiry($inquiry) ? 'sent' : 'failed';
        }
        $inquiry->save();

        return redirect()
            ->back()
            ->with('success', 'Thank you for your inquiry! We will contact you shortly.');
    }

    /**
     * Display inquiry details (admin only)
     * GET /contact-inquiries/{inquiry}
     */
    public function show(ContactInquiry $inquiry)
    {
        $this->authorize('view', $inquiry);

        return redirect()->route('leads.index', ['lead' => $inquiry->lead_id]);
    }

    /**
     * List all inquiries (admin only)
     * GET /contact-inquiries
     */
    public function index()
    {
        $this->authorize('viewAny', ContactInquiry::class);

        return redirect()->route('leads.index');
    }

    /**
     * Mark inquiry as contacted
     * POST /contact-inquiries/{inquiry}/contact
     */
    public function markAsContacted(ContactInquiry $inquiry)
    {
        $this->authorize('update', $inquiry);

        $inquiry->markAsContacted();

        return redirect()
            ->back()
            ->with('success', 'Marked as contacted.');
    }

    /**
     * Mark inquiry as resolved
     * POST /contact-inquiries/{inquiry}/resolve
     */
    public function markAsResolved(ContactInquiry $inquiry)
    {
        $this->authorize('update', $inquiry);

        $inquiry->markAsResolved();

        return redirect()
            ->back()
            ->with('success', 'Inquiry resolved.');
    }

    /**
     * Assign inquiry to a user
     * POST /contact-inquiries/{inquiry}/assign
     */
    public function assign(Request $request, ContactInquiry $inquiry)
    {
        $this->authorize('update', $inquiry);

        $validated = $request->validate([
            'assigned_to' => ['required', 'exists:users,id'],
        ]);

        $lead = $inquiry->lead;
        abort_unless($lead && $request->user()->isAdmin(), 403);
        $request->validate(['version' => 'required|integer|min:1']);
        app(\App\Services\LeadWorkflow::class)->activity($lead, [
            'version' => $request->integer('version'), 'lead_status' => $lead->lead_status,
            'activity_type' => 'Note', 'assigned_to' => (int) $validated['assigned_to'], 'remarks' => 'Inquiry reassigned.',
        ]);

        return redirect()
            ->back()
            ->with('success', 'Inquiry assigned.');
    }

    /**
     * Reply to inquiry via WhatsApp
     * POST /contact-inquiries/{inquiry}/reply
     */
    public function reply(Request $request, ContactInquiry $inquiry, WhatsAppService $whatsapp)
    {
        $this->authorize('update', $inquiry);

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:4096'],
        ]);

        if (! $inquiry->getContactPhone() || ! $whatsapp->sendMessage($inquiry->getContactPhone(), $validated['message'])) {
            return back()->with('error', 'WhatsApp could not send this reply. Check the Cloud API configuration and the customer conversation window.');
        }

        $lead = $inquiry->lead;
        app(\App\Services\LeadWorkflow::class)->activity($lead, [
            'version' => $lead->version, 'lead_status' => 'Contacted',
            'activity_type' => 'WhatsApp', 'remarks' => 'Replied via WhatsApp: '.$validated['message'],
        ]);

        return redirect()
            ->back()
            ->with('success', 'Reply sent via WhatsApp.');
    }

    /**
     * Delete inquiry
     * DELETE /contact-inquiries/{inquiry}
     */
    public function destroy(ContactInquiry $inquiry)
    {
        $this->authorize('delete', $inquiry);

        $inquiry->delete();

        return redirect()
            ->back()
            ->with('success', 'Inquiry deleted.');
    }
}

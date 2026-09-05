<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactInquiryStoreRequest;
use App\Models\ContactInquiry;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactInquiryController extends Controller
{
    /**
     * Show contact inquiry form
     * GET /contact
     */
    public function create()
    {
        return view('contact.create');
    }

    /**
     * Store a contact inquiry
     * POST /contact
     */
    public function store(ContactInquiryStoreRequest $request)
    {
        $validated = $request->validated();

        // Create inquiry
        $inquiry = ContactInquiry::create([
            'user_id' => Auth::id(),
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'name' => $validated['name'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'source' => 'website',
            'status' => 'new',
        ]);

        // Send WhatsApp notification to admins if WhatsApp is configured
        if (config('services.whatsapp.api_token')) {
            $whatsapp = new WhatsAppService();
            $whatsapp->notifyAdminsAboutInquiry($inquiry);
        }

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

        return view('admin.contact-inquiries.show', ['inquiry' => $inquiry]);
    }

    /**
     * List all inquiries (admin only)
     * GET /contact-inquiries
     */
    public function index()
    {
        $this->authorize('viewAny', ContactInquiry::class);

        $inquiries = ContactInquiry::with(['user', 'assignedTo'])
            ->latest()
            ->paginate(15);

        return view('admin.contact-inquiries.index', ['inquiries' => $inquiries]);
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

        $inquiry->update(['assigned_to' => $validated['assigned_to']]);

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

        // Send WhatsApp message
        if ($inquiry->getContactPhone()) {
            $whatsapp->sendMessage(
                $inquiry->getContactPhone(),
                $validated['message']
            );
        }

        // Update inquiry status
        $inquiry->update([
            'status' => 'contacted',
            'responded_at' => now(),
            'notes' => ($inquiry->notes ? $inquiry->notes . "\n\n" : '') . 
                      "Replied via WhatsApp: " . $validated['message'],
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

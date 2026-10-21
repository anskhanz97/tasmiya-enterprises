<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\WhatsAppMessage;
use App\Services\WhatsAppConversation;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class LeadWhatsAppController extends Controller
{
    public function index(Request $request, Lead $lead, WhatsAppConversation $conversation)
    {
        $this->authorize('view', $lead);
        abort_unless($lead->source === 'whatsapp', 404);
        $messages = WhatsAppMessage::where('lead_id', $lead->id)->with('creator')->latest('id')->paginate(30);
        $window = $conversation->window($lead);
        $configured = app(WhatsAppService::class)->isConfigured();

        return response()->json(['data' => $messages->map(fn ($message) => $this->payload($message)),
            'has_more' => $messages->hasMorePages(), 'page' => $messages->currentPage(),
            'window' => $window, 'configured' => $configured, 'closed' => $lead->isClosed(),
            'can_reply' => $configured && $window['open'] && ! $lead->isClosed(), 'version' => $lead->version])
            ->header('Cache-Control', 'private, no-store');
    }

    public function store(Request $request, Lead $lead, WhatsAppConversation $conversation)
    {
        $this->authorize('update', $lead);
        $data = $request->validate(['message' => 'required|string|min:1|max:4096',
            'client_reference' => 'required|uuid', 'version' => 'required|integer|min:1']);
        $message = $conversation->reply($lead, $data);
        $status = in_array($message->status, ['sent', 'delivered', 'read'], true) ? 200 : ($message->status === 'failed' ? 502 : 202);

        return response()->json(['message' => $message->error_message ?: 'Reply sent to WhatsApp.', 'record' => $this->payload($message)], $status);
    }

    private function payload(WhatsAppMessage $message): array
    {
        return $message->only(['id', 'direction', 'body', 'status', 'error_code', 'error_message']) + [
            'created_at' => $message->created_at->timezone(config('leads.timezone'))->format('d M Y, g:i A'),
            'sender' => $message->direction === 'inbound' ? 'Customer' : ($message->creator?->name ?: 'Automatic acknowledgement'),
            'delivered_at' => $message->delivered_at?->timezone(config('leads.timezone'))->format('d M Y, g:i A'),
            'read_at' => $message->read_at?->timezone(config('leads.timezone'))->format('d M Y, g:i A'),
        ];
    }
}

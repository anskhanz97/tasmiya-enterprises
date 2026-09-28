<?php

namespace App\Http\Controllers;

use App\Services\WhatsAppService;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class WhatsAppController extends Controller
{
    /**
     * Handle incoming WhatsApp webhook
     * POST /webhooks/whatsapp
     * 
     * WhatsApp sends:
     * - Incoming messages
     * - Message delivery status updates
     * - Read receipts
     */
    public function handleWebhook(Request $request, WhatsAppService $whatsapp): JsonResponse
    {
        try {
            if (! $whatsapp->verifyWebhookSignature($request->getContent(), $request->header('X-Hub-Signature-256', ''))) {
                return response()->json(['error' => 'Invalid signature'], 403);
            }

            // Get the incoming webhook data
            $payload = $request->all();

            Log::info('WhatsApp webhook received', ['entries' => count($payload['entry'] ?? [])]);

            // Extract messages from webhook
            $messages = $payload['entry'][0]['changes'][0]['value']['messages'] ?? [];
            $statuses = $payload['entry'][0]['changes'][0]['value']['statuses'] ?? [];

            // Handle incoming messages
            foreach ($messages as $message) {
                $whatsapp->handleIncomingMessage($message);
            }

            // Handle delivery/read statuses
            foreach ($statuses as $status) {
                $this->handleStatusUpdate($status);
            }

            return response()->json(['status' => 'received']);
        } catch (\Exception $e) {
            Log::error('WhatsApp webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Webhook processing failed'], 400);
        }
    }

    /**
     * Verify webhook token when WhatsApp sends GET request
     * This is required for webhook setup in WhatsApp Business API dashboard
     */
    public function verifyWebhook(Request $request)
    {
        $token = SiteSetting::get('integration_whatsapp_verify_token') ?: config('services.whatsapp.webhook_verify_token');
        $challenge = $request->query('hub_challenge', $request->query('hub.challenge'));
        $verify_token = $request->query('hub_verify_token', $request->query('hub.verify_token'));

        if ($token && $request->query('hub_mode', $request->query('hub.mode')) === 'subscribe' && is_string($verify_token) && hash_equals($token, $verify_token)) {
            Log::info('WhatsApp webhook verified');
            return response((string) $challenge, 200)->header('Content-Type', 'text/plain');
        }

        Log::warning('WhatsApp webhook verification failed');

        return response()->json(['error' => 'Invalid token'], 403);
    }

    /**
     * Handle message delivery and read status updates
     */
    private function handleStatusUpdate(array $status): void
    {
        $messageId = $status['id'] ?? null;
        $statusType = $status['status'] ?? null; // 'sent', 'delivered', 'read'
        $timestamp = $status['timestamp'] ?? null;
        $recipientId = $status['recipient_id'] ?? null;

        Log::info('WhatsApp status update', [
            'message_id' => $messageId,
            'status' => $statusType,
            'recipient' => $recipientId,
        ]);

        // You could store this in a message log table if needed
        // For now, just logging for monitoring
    }

    /**
     * Send WhatsApp message to a user
     * POST /api/whatsapp/send
     * 
     * Authenticated request from admin panel to send messages
     */
    public function sendMessage(Request $request, WhatsAppService $whatsapp)
    {
        $this->authorize('create', \App\Models\ContactInquiry::class);

        $validated = $request->validate([
            'phone' => ['required', 'string', 'regex:/^\+?[1-9]\d{1,14}$/'],
            'message' => ['required', 'string', 'max:4096'],
        ]);

        $success = $whatsapp->sendMessage(
            $validated['phone'],
            $validated['message']
        );

        if ($success) {
            return response()->json(['status' => 'sent']);
        }

        return response()->json(['error' => 'Failed to send message'], 500);
    }

    /**
     * Get WhatsApp message history
     * GET /api/whatsapp/history/{phone}
     */
    public function getHistory(string $phone)
    {
        $this->authorize('viewAny', \App\Models\ContactInquiry::class);

        $inquiries = \App\Models\ContactInquiry::where('phone', $phone)
            ->where('source', 'whatsapp')
            ->latest()
            ->limit(50)
            ->get();

        return response()->json($inquiries);
    }
}

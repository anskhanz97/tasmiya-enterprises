<?php

namespace App\Services;

use App\Models\ContactInquiry;
use App\Models\Lead;
use App\Models\WhatsAppMessage;
use App\Support\LeadPhone;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class WhatsAppConversation
{
    public function recordInbound(ContactInquiry $inquiry, array $message): void
    {
        WhatsAppMessage::firstOrCreate(['provider_message_id' => $message['id']], [
            'lead_id' => $inquiry->lead_id, 'inquiry_id' => $inquiry->id, 'direction' => 'inbound',
            'phone' => $inquiry->phone, 'body' => $inquiry->message, 'status' => 'received',
            'provider_timestamp' => $this->timestamp($message['timestamp'] ?? null),
        ]);
    }

    public function window(Lead $lead): array
    {
        $last = WhatsAppMessage::where('lead_id', $lead->id)->where('direction', 'inbound')
            ->where('phone', LeadPhone::normalize($lead->phone))->max('provider_timestamp');
        $until = $last ? CarbonImmutable::parse($last, 'UTC')->addHours(24) : null;

        return ['open' => $until && $until->isFuture(), 'until' => $until?->timezone(config('leads.timezone'))->format('d M Y, g:i A')];
    }

    public function reply(Lead $lead, array $data): WhatsAppMessage
    {
        $record = DB::transaction(function () use ($lead, $data) {
            $locked = Lead::lockForUpdate()->findOrFail($lead->id);
            Gate::authorize('update', $locked);
            $existing = WhatsAppMessage::where('client_reference', $data['client_reference'])->first();
            if ($existing) {
                abort_unless($existing->lead_id === $lead->id && $existing->created_by === auth()->id() && $existing->body === $data['message'], 409, 'This send reference belongs to another message.');

                return $existing;
            }
            if ($locked->source !== 'whatsapp' || $locked->isClosed()) {
                throw ValidationException::withMessages(['message' => 'Use an open WhatsApp case to reply.']);
            }
            if (! $this->window($locked)['open']) {
                throw ValidationException::withMessages(['message' => 'The 24-hour text reply window is closed. The customer must message again, or an approved customer template is needed.']);
            }
            if (! app(WhatsAppService::class)->isConfigured()) {
                throw ValidationException::withMessages(['message' => 'Configure the WhatsApp phone ID and access token in Settings first.']);
            }
            if (WhatsAppMessage::where('lead_id', $lead->id)->where('direction', 'outbound')
                ->whereIn('status', ['sending', 'unknown'])->where('created_at', '>', now()->subMinutes(10))->exists()) {
                throw ValidationException::withMessages(['message' => 'An earlier send is still unconfirmed. Check its delivery status before sending again.']);
            }
            abort_unless(Lead::whereKey($lead->id)->where('version', $data['version'])->update(['version' => DB::raw('version + 1')]), 409, 'This case has changed. Reload its details before replying.');

            return WhatsAppMessage::create([
                'lead_id' => $locked->id, 'created_by' => auth()->id(), 'direction' => 'outbound',
                'phone' => $locked->phone, 'body' => $data['message'], 'client_reference' => $data['client_reference'], 'status' => 'sending',
            ]);
        }, 5);
        if (! $record->wasRecentlyCreated) {
            return $record;
        }
        $this->dispatch($record, app(WhatsAppService::class));

        return $record->refresh();
    }

    public function acknowledge(ContactInquiry $inquiry, WhatsAppService $service): void
    {
        $record = WhatsAppMessage::create([
            'lead_id' => $inquiry->lead_id, 'direction' => 'outbound', 'phone' => $inquiry->phone,
            'body' => 'Thank you for reaching out! We will respond to your inquiry shortly.', 'status' => 'sending',
        ]);
        if (! $this->window($inquiry->lead)['open']) {
            $record->update(['status' => 'failed', 'error_code' => 'window_closed', 'error_message' => 'The text reply window has closed.']);
        } else {
            $this->dispatch($record, $service);
        }
        $inquiry->update(['whatsapp_delivery_status' => in_array($record->fresh()->status, ['sent', 'delivered', 'read'], true) ? 'sent' : 'failed']);
    }

    private function dispatch(WhatsAppMessage $record, WhatsAppService $service): void
    {
        $result = $service->sendMessageResult($record->phone, $record->body);
        $record->update([
            'status' => $result['accepted'] ? 'sent' : ($result['uncertain'] ? 'unknown' : 'failed'),
            'provider_message_id' => $result['id'], 'sent_at' => $result['accepted'] ? now() : null,
            'error_code' => $result['error_code'],
            'error_message' => $result['accepted'] ? null : ($result['uncertain']
                ? 'Delivery is unconfirmed. Check Meta before retrying to avoid sending twice.'
                : 'WhatsApp rejected this send. Check the provider error code and configuration.'),
        ]);
        if ($record->provider_message_id) {
            $this->applyReceipts($record);
        }
    }

    public function receipt(array $status): void
    {
        if (! is_string($status['id'] ?? null) || strlen($status['id']) > 191
            || ! in_array($status['status'] ?? null, ['sent', 'delivered', 'read', 'failed'], true)) {
            return;
        }
        DB::table('whatsapp_delivery_receipts')->insertOrIgnore([
            'provider_message_id' => $status['id'], 'status' => $status['status'],
            'recipient' => LeadPhone::normalize($status['recipient_id'] ?? null),
            'occurred_at' => $this->timestamp($status['timestamp'] ?? null)->format('Y-m-d H:i:s'),
            'error_code' => isset($status['errors'][0]['code']) ? substr((string) $status['errors'][0]['code'], 0, 30) : null,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $message = WhatsAppMessage::where('provider_message_id', $status['id'])->where('direction', 'outbound')->first();
        if ($message) {
            $this->applyReceipts($message);
        }
    }

    private function applyReceipts(WhatsAppMessage $message): void
    {
        DB::transaction(function () use ($message) {
            $message = WhatsAppMessage::lockForUpdate()->findOrFail($message->id);
            $receipts = DB::table('whatsapp_delivery_receipts')->where('provider_message_id', $message->provider_message_id)->orderBy('occurred_at')->orderBy('id')->get();
            $rank = ['sending' => 0, 'unknown' => 0, 'failed' => 0, 'sent' => 1, 'delivered' => 2, 'read' => 3];
            foreach ($receipts as $receipt) {
                if ($receipt->recipient && $receipt->recipient !== $message->phone) {
                    continue;
                }
                if ($receipt->status === 'failed') {
                    if (($rank[$message->status] ?? 0) < 2) {
                        $message->fill(['status' => 'failed', 'error_code' => $receipt->error_code, 'error_message' => 'Meta reported a delivery failure.']);
                    }
                } elseif ($rank[$receipt->status] >= ($rank[$message->status] ?? 0)) {
                    $message->fill(['status' => $receipt->status, $receipt->status.'_at' => $receipt->occurred_at, 'error_code' => null, 'error_message' => null]);
                }
            }
            $message->save();
        }, 5);
    }

    private function timestamp(mixed $value): CarbonImmutable
    {
        $now = CarbonImmutable::now('UTC');
        if ((is_string($value) || is_int($value)) && preg_match('/^\d{1,11}$/', (string) $value)) {
            return CarbonImmutable::createFromTimestampUTC((int) $value)->min($now);
        }

        return $now;
    }
}

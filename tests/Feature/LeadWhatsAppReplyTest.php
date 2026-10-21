<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\User;
use App\Models\WhatsAppMessage;
use App\Services\LeadWorkflow;
use App\Services\WhatsAppConversation;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class LeadWhatsAppReplyTest extends TestCase
{
    use RefreshDatabase;

    private int $upstreamStatus = 200;

    private bool $connectionFailure = false;

    private function conversation(): Lead
    {
        config(['services.whatsapp.business_phone_id' => 'test-phone', 'services.whatsapp.api_token' => 'test-token', 'services.whatsapp.app_secret' => 'test-secret']);
        $owner = User::factory()->create(['role' => 'team_member', 'is_active' => true]);
        $result = app(LeadWorkflow::class)->whatsapp(['id' => 'wamid.inbound', 'from' => '923001234567',
            'type' => 'text', 'timestamp' => (string) now()->timestamp, 'text' => ['body' => 'Please reply about the service.']]);
        $lead = $result['inquiry']->lead;
        $lead->update(['assigned_to' => $owner->id]);
        $this->actingAs($owner);
        Http::preventStrayRequests();
        Http::fake(fn ($request) => $this->connectionFailure ? Http::failedConnection()($request)
            : Http::response($this->upstreamStatus === 200 ? ['messages' => [['id' => 'wamid.outbound']]] : ['error' => ['code' => 131047]], $this->upstreamStatus));

        return $lead;
    }

    private function reply(Lead $lead, array $overrides = []): array
    {
        return array_merge(['version' => $lead->fresh()->version, 'client_reference' => (string) Str::uuid(), 'message' => 'We can discuss your requirements today.'], $overrides);
    }

    public function test_authorized_text_reply_is_recorded_and_retrying_the_same_reference_never_sends_twice(): void
    {
        $lead = $this->conversation();
        $body = $this->reply($lead);
        $this->postJson('/leads/'.$lead->id.'/whatsapp', $body)->assertOk()->assertJsonPath('record.status', 'sent');
        $this->postJson('/leads/'.$lead->id.'/whatsapp', $body)->assertOk();
        $this->postJson('/leads/'.$lead->id.'/whatsapp', array_merge($body, ['message' => 'Different message']))->assertConflict();
        Http::assertSentCount(1);
        $this->assertDatabaseCount('whatsapp_messages', 2);
        $this->getJson('/leads/'.$lead->id.'/whatsapp')->assertOk()->assertJsonPath('data.0.sender', auth()->user()->name)->assertJsonPath('can_reply', true);
        $this->assertSame('New', $lead->fresh()->lead_status);
    }

    public function test_other_owners_closed_cases_missing_configuration_and_expired_windows_cannot_send(): void
    {
        $lead = $this->conversation();
        $owner = auth()->user();
        $peer = User::factory()->create(['role' => 'team_member', 'is_active' => true]);
        $this->actingAs($peer)->postJson('/leads/'.$lead->id.'/whatsapp', $this->reply($lead))->assertForbidden();
        $this->getJson('/leads/'.$lead->id.'/whatsapp')->assertForbidden();
        $this->actingAs($owner);
        config(['services.whatsapp.api_token' => '']);
        $this->postJson('/leads/'.$lead->id.'/whatsapp', $this->reply($lead))->assertUnprocessable();
        config(['services.whatsapp.api_token' => 'test-token']);
        $lead->update(['phone' => '+447700900999']);
        $this->postJson('/leads/'.$lead->id.'/whatsapp', $this->reply($lead))->assertUnprocessable();
        $lead->update(['phone' => '+923001234567']);
        $this->travelTo(CarbonImmutable::now()->addHours(25));
        $this->postJson('/leads/'.$lead->id.'/whatsapp', $this->reply($lead))->assertUnprocessable();
        $this->getJson('/leads/'.$lead->id.'/whatsapp')->assertJsonPath('window.open', false)->assertJsonPath('can_reply', false);
        $lead->update(['lead_status' => 'Lost']);
        $this->postJson('/leads/'.$lead->id.'/whatsapp', $this->reply($lead))->assertUnprocessable();
        Http::assertNothingSent();
    }

    public function test_signed_receipts_are_deduplicated_and_read_cannot_regress_to_sent_or_failed(): void
    {
        $lead = $this->conversation();
        $this->postJson('/leads/'.$lead->id.'/whatsapp', $this->reply($lead))->assertOk();
        $statuses = collect(['delivered', 'read', 'sent', 'failed'])->map(fn ($status) => [
            'id' => 'wamid.outbound', 'status' => $status, 'recipient_id' => '923001234567', 'timestamp' => (string) now()->timestamp,
        ])->all();
        $payload = ['entry' => [['changes' => [['value' => ['statuses' => $statuses]]]]]];
        $json = json_encode($payload);
        $headers = ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json',
            'HTTP_X_HUB_SIGNATURE_256' => 'sha256='.hash_hmac('sha256', $json, 'test-secret')];
        $this->call('POST', '/webhooks/whatsapp', [], [], [], $headers, $json)->assertOk();
        $this->call('POST', '/webhooks/whatsapp', [], [], [], $headers, $json)->assertOk();
        $this->assertDatabaseCount('whatsapp_delivery_receipts', 4);
        $this->assertDatabaseHas('whatsapp_messages', ['provider_message_id' => 'wamid.outbound', 'status' => 'read']);
        $this->assertNotNull(WhatsAppMessage::where('direction', 'outbound')->first()->read_at);
    }

    public function test_early_receipts_are_applied_when_the_send_response_arrives(): void
    {
        $lead = $this->conversation();
        app(WhatsAppConversation::class)->receipt(['id' => 'wamid.outbound', 'status' => 'read', 'recipient_id' => '923001234567']);
        $this->postJson('/leads/'.$lead->id.'/whatsapp', $this->reply($lead))->assertOk()->assertJsonPath('record.status', 'read');
    }

    public function test_rejected_and_unconfirmed_sends_are_visible_and_never_retried_automatically(): void
    {
        $lead = $this->conversation();
        $this->upstreamStatus = 400;
        $this->postJson('/leads/'.$lead->id.'/whatsapp', $this->reply($lead))->assertStatus(502)->assertJsonPath('record.status', 'failed')->assertJsonPath('record.error_code', '131047');
        $this->connectionFailure = true;
        $body = $this->reply($lead);
        $this->postJson('/leads/'.$lead->id.'/whatsapp', $body)->assertStatus(202)->assertJsonPath('record.status', 'unknown');
        $this->postJson('/leads/'.$lead->id.'/whatsapp', $body)->assertStatus(202);
        $this->postJson('/leads/'.$lead->id.'/whatsapp', $this->reply($lead))->assertUnprocessable();
        $this->assertSame(2, WhatsAppMessage::where('direction', 'outbound')->count());
    }
}

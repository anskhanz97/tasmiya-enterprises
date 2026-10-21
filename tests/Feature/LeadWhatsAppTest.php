<?php

namespace Tests\Feature;

use App\Models\Division;
use App\Models\Lead;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\LeadWorkflow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class LeadWhatsAppTest extends TestCase
{
    use RefreshDatabase;

    private int $upstreamStatus = 200;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.whatsapp.app_secret' => 'test-secret', 'services.whatsapp.business_phone_id' => 'test-phone', 'services.whatsapp.api_token' => 'test-token']);
        Http::preventStrayRequests();
        Http::fake(fn () => Http::response($this->upstreamStatus === 200
            ? ['messages' => [['id' => 'outgoing-'.\Illuminate\Support\Str::uuid()]]] : ['error' => ['message' => 'Unavailable']], $this->upstreamStatus));
    }

    private function message(string $id, string $phone = '923001234567'): array
    {
        // Meta Cloud API's messages notification shape (text + contact profile).
        return ['id' => $id, 'from' => $phone, 'timestamp' => (string) now()->timestamp, 'type' => 'text',
            'text' => ['body' => 'Please contact me about your services.']];
    }

    private function payload(array $messages): array
    {
        return ['object' => 'whatsapp_business_account', 'entry' => [['id' => 'account-id', 'changes' => [[
            'field' => 'messages', 'value' => ['messaging_product' => 'whatsapp',
                'metadata' => ['display_phone_number' => '923009999999', 'phone_number_id' => 'test-phone'],
                'contacts' => [['wa_id' => '923001234567', 'profile' => ['name' => 'WhatsApp Prospect']]],
                'messages' => $messages],
        ]]]]];
    }

    private function deliver(array $payload)
    {
        $json = json_encode($payload);

        return $this->call('POST', '/webhooks/whatsapp', [], [], [], [
            'CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json',
            'HTTP_X_HUB_SIGNATURE_256' => 'sha256='.hash_hmac('sha256', $json, 'test-secret'),
        ], $json);
    }

    public function test_signed_batches_group_messages_without_duplicate_records_or_acknowledgements(): void
    {
        $payload = $this->payload([$this->message('wamid.first'), $this->message('wamid.second')]);
        $other = $this->payload([$this->message('wamid.other', '447700900123')]);
        $payload['entry'][] = $other['entry'][0];
        $this->deliver($payload)->assertOk();
        $this->deliver($payload)->assertOk();
        $this->assertDatabaseCount('leads', 2);
        $this->assertDatabaseCount('contact_inquiries', 3);
        $this->assertDatabaseCount('lead_details', 3);
        $this->assertDatabaseHas('leads', ['party_name' => 'WhatsApp Prospect', 'assigned_to' => null]);
        Http::assertSentCount(2);
        $lead = Lead::where('phone_normalized', '+923001234567')->first();
        $this->assertSame('New', $lead->lead_status);
        $this->assertNull($lead->openLeadFollowup);
    }

    public function test_replies_preserve_owner_status_and_scheduled_followup_and_closed_conversations_start_new_cases(): void
    {
        $this->deliver($this->payload([$this->message('wamid.initial')]))->assertOk();
        $lead = Lead::first();
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->actingAs($admin)->postJson('/leads/'.$lead->id.'/follow-ups', [
            'version' => $lead->version, 'lead_status' => 'Interested', 'activity_type' => 'Note',
            'assigned_to' => $admin->id, 'follow_up_date' => now('Asia/Karachi')->addDay()->toDateString(),
        ])->assertOk();
        auth()->forgetGuards();
        $this->deliver($this->payload([$this->message('wamid.reply')]))->assertOk();
        $this->assertSame($admin->id, $lead->fresh()->assigned_to);
        $this->assertSame('Interested', $lead->fresh()->lead_status);
        $this->assertSame(1, $lead->leadDetails()->open()->count());
        $this->actingAs($admin)->postJson('/leads/'.$lead->id.'/convert', ['version' => $lead->fresh()->version])->assertOk();
        auth()->forgetGuards();
        $this->deliver($this->payload([$this->message('wamid.new-conversation')]))->assertOk();
        $this->assertDatabaseCount('leads', 2);
        $this->assertNull(Lead::latest('id')->first()->assigned_to);
        Http::assertSentCount(2);
    }

    public function test_signatures_status_callbacks_and_unsupported_media_do_not_create_cases(): void
    {
        $this->postJson('/webhooks/whatsapp', $this->payload([$this->message('wamid.invalid')]))->assertForbidden();
        $payload = $this->payload([]);
        $payload['entry'][0]['changes'][0]['value']['statuses'] = [['id' => 'wamid.outbound', 'status' => 'delivered', 'recipient_id' => '923001234567']];
        $this->deliver($payload)->assertOk();
        $media = $this->message('wamid.media');
        $media['type'] = 'image';
        unset($media['text']);
        $this->deliver($this->payload([$media]))->assertOk();
        $bad = $this->message('wamid.bad');
        unset($bad['text']);
        $this->deliver($this->payload([$bad]))->assertUnprocessable();
        $this->assertDatabaseCount('leads', 0);
        Http::assertNothingSent();
    }

    public function test_failed_persistence_returns_retryable_failure_without_acknowledging(): void
    {
        $this->mock(LeadWorkflow::class)->shouldReceive('whatsapp')->once()->andThrow(new \RuntimeException('Storage unavailable'));
        $this->deliver($this->payload([$this->message('wamid.retry')]))->assertStatus(500);
        $this->assertDatabaseCount('contact_inquiries', 0);
        Http::assertNothingSent();
    }

    public function test_failed_acknowledgement_keeps_case_and_retry_does_not_send_again(): void
    {
        $this->upstreamStatus = 503;
        $payload = $this->payload([$this->message('wamid.ack-failure')]);
        $this->deliver($payload)->assertOk();
        $this->deliver($payload)->assertOk();
        $this->assertDatabaseCount('leads', 1);
        $this->assertDatabaseHas('contact_inquiries', ['external_message_id' => 'wamid.ack-failure', 'whatsapp_delivery_status' => 'failed']);
        Http::assertSentCount(1);
    }

    public function test_company_notification_failures_do_not_lose_public_inquiry(): void
    {
        $division = Division::create(['name' => 'IT', 'slug' => 'it']);
        $owner = User::factory()->create(['role' => 'team_member', 'is_active' => true, 'division_id' => $division->id]);
        $owner->profile()->create(['is_visible' => true]);
        foreach (['integration_company_email' => 'company@example.test', 'integration_smtp_host' => 'smtp.example.test',
            'integration_smtp_from_email' => 'sender@example.test', 'integration_company_whatsapp' => '+923009999999',
            'integration_whatsapp_template_name' => 'inquiry_notice'] as $key => $value) {
            SiteSetting::set($key, $value);
        }
        Mail::shouldReceive('purge')->with('smtp')->once();
        Mail::shouldReceive('mailer')->with('smtp')->once()->andReturnSelf();
        Mail::shouldReceive('raw')->once()->andThrow(new \RuntimeException('SMTP offline'));
        $this->upstreamStatus = 502;
        $this->post('/contact', ['name' => 'Prospect', 'email' => 'person@example.test', 'phone' => '+447700900123',
            'subject' => 'Service inquiry', 'message' => 'Please help with my website project.',
            'division_id' => $division->id, 'assigned_to' => $owner->id])->assertRedirect()->assertSessionHas('success');
        $this->assertDatabaseCount('leads', 1);
        $this->assertDatabaseHas('contact_inquiries', ['email_delivery_status' => 'failed', 'whatsapp_delivery_status' => 'failed']);
    }
}

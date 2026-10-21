<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WhatsAppMessage;
use App\Services\LeadWorkflow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class LeadWhatsAppReplySimulationTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_reply_uses_real_http_against_the_local_postman_simulation(): void
    {
        $url = getenv('WHATSAPP_SIMULATION_URL');
        if (! $url) {
            $this->markTestSkipped('Opt in with the documented local WhatsApp simulation.');
        }
        $this->assertMatchesRegularExpression('~^http://(127\.0\.0\.1|localhost):\d+/~', $url);
        config(['services.whatsapp.api_base_url' => $url, 'services.whatsapp.business_phone_id' => 'local-phone', 'services.whatsapp.api_token' => 'local-token']);
        $user = User::factory()->create(['role' => 'team_member', 'is_active' => true]);
        $result = app(LeadWorkflow::class)->whatsapp(['id' => 'wamid.simulation.reply', 'from' => '923001234567', 'type' => 'text', 'text' => ['body' => 'Local reply verification.']]);
        $lead = $result['inquiry']->lead;
        $lead->update(['assigned_to' => $user->id]);
        $this->actingAs($user);
        $payload = ['version' => $lead->version, 'client_reference' => (string) Str::uuid(), 'message' => 'This reply goes only to the local mock.'];
        $expected = getenv('WHATSAPP_REPLY_EXPECT') ?: 'sent';
        $status = ['sent' => 200, 'failed' => 502, 'unknown' => 202][$expected];
        $this->postJson('/leads/'.$lead->id.'/whatsapp', $payload)->assertStatus($status)->assertJsonPath('record.status', $expected);
        $this->postJson('/leads/'.$lead->id.'/whatsapp', $payload)->assertStatus($status);
        $this->assertSame(1, WhatsAppMessage::where('direction', 'outbound')->count());
    }
}

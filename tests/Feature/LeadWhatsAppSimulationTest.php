<?php

namespace Tests\Feature;

use App\Models\ContactInquiry;
use App\Models\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadWhatsAppSimulationTest extends TestCase
{
    use RefreshDatabase;

    public function test_real_http_acknowledgement_against_local_postman_simulation(): void
    {
        $url = getenv('WHATSAPP_SIMULATION_URL');
        if (! $url) {
            $this->markTestSkipped('Run the documented local Postman simulation to verify real HTTP.');
        }
        $this->assertMatchesRegularExpression('~^http://(127\.0\.0\.1|localhost):\d+/~', $url);
        config(['services.whatsapp.api_base_url' => $url, 'services.whatsapp.app_secret' => 'local-test-secret',
            'services.whatsapp.business_phone_id' => 'local-phone', 'services.whatsapp.api_token' => 'local-token']);
        $payload = ['object' => 'whatsapp_business_account', 'entry' => [['changes' => [['value' => ['messages' => [[
            'from' => '923001234567', 'id' => 'wamid.simulation', 'type' => 'text', 'text' => ['body' => 'Local simulated inquiry.'],
        ]]]]]]]];
        $json = json_encode($payload);
        $headers = ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json',
            'HTTP_X_HUB_SIGNATURE_256' => 'sha256='.hash_hmac('sha256', $json, 'local-test-secret')];
        $this->call('POST', '/webhooks/whatsapp', [], [], [], $headers, $json)->assertOk();
        $this->call('POST', '/webhooks/whatsapp', [], [], [], $headers, $json)->assertOk();
        $this->assertSame(1, Lead::count());
        $this->assertSame(1, ContactInquiry::count());
        $expected = getenv('WHATSAPP_SIMULATION_EXPECT') ?: 'sent';
        $this->assertSame($expected, ContactInquiry::first()->whatsapp_delivery_status);
    }
}

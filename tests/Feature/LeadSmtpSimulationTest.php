<?php

namespace Tests\Feature;

use App\Models\LeadDigestDelivery;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\LeadReminderService;
use App\Services\LeadWorkflow;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadSmtpSimulationTest extends TestCase
{
    use RefreshDatabase;

    public function test_daily_summary_through_the_real_smtp_transport_against_a_loopback_mailbox(): void
    {
        $port = getenv('LEAD_SMTP_SIMULATION_PORT');
        if (! $port) {
            $this->markTestSkipped('Opt in with the documented loopback SMTP mailbox.');
        }
        $this->assertMatchesRegularExpression('/^\d{4,5}$/', $port);
        $this->travelTo(CarbonImmutable::parse('2026-10-21 09:00:00', 'Asia/Karachi'));
        SiteSetting::set('integration_smtp_host', '127.0.0.1');
        SiteSetting::set('integration_smtp_port', $port);
        SiteSetting::set('integration_smtp_encryption', 'none');
        SiteSetting::set('integration_smtp_from_email', 'no-reply@example.test');
        $user = User::factory()->create(['name' => 'Local SMTP Verification', 'email' => 'staff@example.test', 'role' => 'team_member', 'is_active' => true]);
        $this->actingAs($user);
        app(LeadWorkflow::class)->create(['party_name' => 'Local summary prospect', 'phone' => '+923001234567', 'follow_up_date' => '2026-10-22']);
        app(LeadReminderService::class)->run();
        app(LeadReminderService::class)->run();
        $this->assertDatabaseCount('lead_notifications', 1);
        $this->assertDatabaseCount('lead_digest_deliveries', 1);
        $this->assertSame(getenv('LEAD_SMTP_EXPECT') ?: 'sent', LeadDigestDelivery::first()->status);
        $this->assertSame(1, LeadDigestDelivery::first()->attempts);
    }
}

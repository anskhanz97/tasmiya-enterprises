<?php

namespace Tests\Feature;

use App\Mail\LeadFollowupDigest;
use App\Models\LeadDigestDelivery;
use App\Models\LeadNotification;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\LeadReminderService;
use App\Services\LeadWorkflow;
use Carbon\CarbonImmutable;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class LeadRemindersTest extends TestCase
{
    use RefreshDatabase;

    private function followup(User $owner, string $date, string $name = 'Assigned prospect')
    {
        $this->actingAs($owner);

        return app(LeadWorkflow::class)->create(['party_name' => $name, 'phone' => '+923001234567',
            'follow_up_date' => $date, 'assigned_to' => $owner->id]);
    }

    private function smtp(): void
    {
        SiteSetting::set('integration_smtp_host', 'smtp.example.test');
        SiteSetting::set('integration_smtp_from_email', 'no-reply@example.test');
    }

    public function test_daily_summary_runs_at_9_karachi_and_sends_once_only_to_each_assigned_person(): void
    {
        Mail::fake();
        $this->smtp();
        $owner = User::factory()->create(['role' => 'team_member', 'is_active' => true]);
        $other = User::factory()->create(['role' => 'team_member', 'is_active' => true]);
        User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->followup($owner, '2026-10-20');
        $this->followup($owner, '2026-10-21');
        $this->followup($owner, '2026-10-22');
        $this->followup($other, '2026-10-23', 'Private other case');
        $this->travelTo(CarbonImmutable::parse('2026-10-21 08:59:00', 'Asia/Karachi'));
        app(LeadReminderService::class)->run();
        $this->assertDatabaseCount('lead_digest_deliveries', 0);
        $this->travelTo(CarbonImmutable::parse('2026-10-21 09:00:00', 'Asia/Karachi'));
        app(LeadReminderService::class)->run();
        app(LeadReminderService::class)->run();
        Mail::assertSentCount(2);
        Mail::assertSent(LeadFollowupDigest::class, fn ($mail) => $mail->hasTo($owner->email) && count($mail->items) === 3
            && collect($mail->items)->countBy('category')->all() === ['Overdue' => 1, 'Today' => 1, 'Upcoming' => 1]);
        $this->assertSame(2, LeadDigestDelivery::where('status', 'sent')->count());
        $this->assertSame(2, LeadNotification::where('kind', 'digest')->count());
    }

    public function test_unconfigured_email_keeps_website_summary_and_sends_after_configuration_without_a_backlog(): void
    {
        Mail::fake();
        $this->travelTo(CarbonImmutable::parse('2026-10-21 09:05:00', 'Asia/Karachi'));
        $owner = User::factory()->create(['role' => 'team_member', 'is_active' => true]);
        $this->followup($owner, '2026-10-20');
        LeadDigestDelivery::create(['user_id' => $owner->id, 'digest_date' => '2026-10-20', 'status' => 'unconfigured']);
        app(LeadReminderService::class)->run();
        $this->assertDatabaseHas('lead_digest_deliveries', ['digest_date' => '2026-10-20', 'status' => 'skipped']);
        $this->assertDatabaseHas('lead_digest_deliveries', ['digest_date' => '2026-10-21', 'status' => 'unconfigured', 'attempts' => 0]);
        $this->assertDatabaseCount('lead_notifications', 1);
        Mail::assertNothingSent();
        $this->smtp();
        app(LeadReminderService::class)->run();
        Mail::assertSentCount(1);
        $this->assertSame(1, LeadDigestDelivery::where('status', 'sent')->count());
    }

    public function test_delivery_failure_retries_and_does_not_duplicate_the_website_notice(): void
    {
        $this->travelTo(CarbonImmutable::parse('2026-10-21 09:00:00', 'Asia/Karachi'));
        $this->smtp();
        $owner = User::factory()->create(['role' => 'team_member', 'is_active' => true]);
        $this->followup($owner, '2026-10-22');
        $manager = Mail::getFacadeRoot();
        Mail::shouldReceive('purge')->andReturnNull();
        Mail::shouldReceive('mailer')->andReturnSelf();
        Mail::shouldReceive('to')->andReturnSelf();
        Mail::shouldReceive('send')->once()->andThrow(new \RuntimeException('Local SMTP unavailable'));
        app(LeadReminderService::class)->run();
        app(LeadReminderService::class)->run();
        $this->assertDatabaseHas('lead_digest_deliveries', ['status' => 'failed', 'attempts' => 1]);
        $this->assertDatabaseCount('lead_notifications', 1);
        Mail::swap($manager);
        Mail::fake();
        $this->travelTo(CarbonImmutable::parse('2026-10-21 09:16:00', 'Asia/Karachi'));
        app(LeadReminderService::class)->run();
        Mail::assertSentCount(1);
        $this->assertDatabaseHas('lead_digest_deliveries', ['status' => 'sent', 'attempts' => 2]);
    }

    public function test_reassignment_and_closure_are_rechecked_before_email_and_inactive_people_are_excluded(): void
    {
        Mail::fake();
        $this->travelTo(CarbonImmutable::parse('2026-10-21 09:00:00', 'Asia/Karachi'));
        $owner = User::factory()->create(['role' => 'team_member', 'is_active' => true]);
        $other = User::factory()->create(['role' => 'team_member', 'is_active' => true]);
        $lead = $this->followup($owner, '2026-10-22');
        app(LeadReminderService::class)->run();
        $lead->update(['assigned_to' => $other->id]);
        $this->smtp();
        app(LeadReminderService::class)->run();
        Mail::assertSent(LeadFollowupDigest::class, fn ($mail) => $mail->hasTo($other->email));
        Mail::assertNotSent(LeadFollowupDigest::class, fn ($mail) => $mail->hasTo($owner->email));
        $this->assertDatabaseHas('lead_digest_deliveries', ['user_id' => $owner->id, 'status' => 'skipped']);
        $this->travelTo(CarbonImmutable::parse('2026-10-22 09:00:00', 'Asia/Karachi'));
        $lead->update(['lead_status' => 'Converted']);
        app(LeadReminderService::class)->run();
        Mail::assertSentCount(1);
        $other->update(['is_active' => false]);
        $lead->update(['lead_status' => 'New']);
        app(LeadReminderService::class)->run();
        Mail::assertSentCount(1);
    }

    public function test_settings_preview_and_delivery_oversight_are_admin_only(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $staff = User::factory()->create(['role' => 'team_member', 'is_active' => true]);
        $this->actingAs($staff)->get('/notifications/email-preview')->assertForbidden();
        $this->post('/notifications/test-email')->assertForbidden();
        $this->get('/notifications?view=followups&user_id='.$admin->id)->assertForbidden();
        $this->actingAs($admin)->get('/notifications?view=deliveries')->assertOk()->assertSee('SMTP is not configured');
        $this->get('/notifications/email-preview')->assertOk()->assertSee('Example lead (preview only)')->assertSee('Asia/Karachi');
        $this->put('/settings/integrations', ['section' => 'reminders', 'enable_lead_reminders' => 1, 'lead_reminder_time' => '09:00', 'lead_upcoming_days' => 7])->assertSessionHasNoErrors();
        $this->assertSame('09:00', app(LeadReminderService::class)->settings()['time']);
        $this->putJson('/settings/integrations', ['section' => 'reminders', 'lead_reminder_time' => '25:00', 'lead_upcoming_days' => 0])->assertUnprocessable();
        $this->assertTrue(collect(app(Schedule::class)->events())->contains(fn ($event) => str_contains($event->command ?? '', 'leads:remind') && $event->expression === '* * * * *'));
    }

    public function test_disabled_summaries_do_not_send_and_manual_retries_cannot_resend_sent_or_old_summaries(): void
    {
        Mail::fake();
        $this->travelTo(CarbonImmutable::parse('2026-10-21 09:00:00', 'Asia/Karachi'));
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->followup($admin, '2026-10-22');
        SiteSetting::set('integration_enable_lead_reminders', '0');
        app(LeadReminderService::class)->run();
        $this->assertDatabaseCount('lead_digest_deliveries', 0);
        $sent = LeadDigestDelivery::create(['user_id' => $admin->id, 'digest_date' => '2026-10-21', 'status' => 'sent']);
        $this->actingAs($admin)->post('/notifications/deliveries/'.$sent->id.'/retry')->assertUnprocessable();
        $old = LeadDigestDelivery::create(['user_id' => $admin->id, 'digest_date' => '2026-10-20', 'status' => 'failed']);
        $this->post('/notifications/deliveries/'.$old->id.'/retry')->assertUnprocessable();
        $sent->update(['status' => 'failed', 'attempts' => 5]);
        $this->post('/notifications/deliveries/'.$sent->id.'/retry')->assertRedirect();
        $this->assertDatabaseHas('lead_digest_deliveries', ['id' => $sent->id, 'status' => 'pending', 'attempts' => 0]);
        Mail::assertNothingSent();
    }
}

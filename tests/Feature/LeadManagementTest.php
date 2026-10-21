<?php

namespace Tests\Feature;

use App\Models\ContactInquiry;
use App\Models\Division;
use App\Models\Lead;
use App\Models\LeadContact;
use App\Models\User;
use App\Services\LeadWorkflow;
use App\Support\LeadPhone;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadManagementTest extends TestCase
{
    use RefreshDatabase;

    private function staff(string $name = 'Ans', string $role = 'team_member', ?Division $division = null): User
    {
        $division ??= Division::create(['name' => $name.' Division', 'slug' => strtolower($name).'-'.uniqid()]);
        $user = User::factory()->create(['name' => $name, 'role' => $role, 'division_id' => $division->id, 'is_active' => true]);
        $user->profile()->create(['bio' => 'Team specialist', 'is_visible' => true]);

        return $user;
    }

    private function inquiry(User $owner, array $overrides = []): array
    {
        return array_merge(['name' => 'Inquiry Person', 'email' => 'person@example.test', 'phone' => '+92 300 1234567',
            'subject' => 'Website development', 'message' => 'Please help me with a new website.',
            'division_id' => $owner->division_id, 'assigned_to' => $owner->id], $overrides);
    }

    private function createLead(User $owner, array $overrides = []): Lead
    {
        $this->actingAs($owner);

        return app(LeadWorkflow::class)->create(array_merge(['party_name' => 'Example Lead', 'party_email' => 'lead@example.test',
            'phone' => '03001234567', 'assigned_to' => $owner->id, 'follow_up_date' => now('Asia/Karachi')->addDay()->toDateString()], $overrides));
    }

    private function activity(Lead $lead, array $overrides = []): array
    {
        return array_merge(['version' => $lead->fresh()->version, 'lead_status' => 'Contacted', 'activity_type' => 'Call',
            'assigned_to' => $lead->assigned_to, 'remarks' => 'Discussed the requirements.'], $overrides);
    }

    public function test_public_form_routes_separate_cases_and_preserves_original_inquiries(): void
    {
        $ans = $this->staff();
        $this->get('/contact')->assertOk()->assertSee('Choose a department')->assertSee('Specialist');
        $payload = $this->inquiry($ans);
        $this->post('/contact', $payload)->assertRedirect()->assertSessionHas('success');
        $this->post('/contact', $payload)->assertRedirect()->assertSessionHas('success');
        $this->assertDatabaseCount('leads', 2);
        $this->assertDatabaseCount('lead_contacts', 1);
        $this->assertDatabaseCount('contact_inquiries', 2);
        $this->assertDatabaseCount('lead_details', 2);
        $lead = Lead::first();
        $this->assertSame('New', $lead->lead_status);
        $this->assertSame($ans->id, $lead->assigned_to);
        $this->assertSame('+923001234567', $lead->phone);
        $this->assertNull($lead->openLeadFollowup);
        $this->actingAs($ans)->getJson('/leads/'.$lead->id)->assertOk()
            ->assertJsonPath('inquiries.0.subject', $payload['subject'])->assertJsonPath('inquiries.0.message', $payload['message']);
    }

    public function test_public_recipient_validation_and_single_person_auto_assignment(): void
    {
        $ans = $this->staff();
        $nazim = $this->staff('Nazim');
        $this->postJson('/contact', $this->inquiry($ans, ['assigned_to' => $nazim->id]))->assertUnprocessable()->assertJsonValidationErrors('assigned_to');
        $payload = $this->inquiry($ans);
        unset($payload['assigned_to']);
        $this->post('/contact', $payload)->assertSessionHasNoErrors();
        $this->assertDatabaseHas('leads', ['assigned_to' => $ans->id]);
        $ans->update(['is_active' => false]);
        $this->postJson('/contact', $this->inquiry($ans))->assertUnprocessable();
        $this->assertDatabaseCount('leads', 1);
    }

    public function test_international_phones_are_not_truncated_and_conflicting_identities_are_not_merged(): void
    {
        $ans = $this->staff();
        foreach (['+44 7700 900123', '+1 (415) 555-2671', '00923001234567'] as $phone) {
            $this->post('/contact', $this->inquiry($ans, ['phone' => $phone]))->assertSessionHasNoErrors();
        }
        $this->assertDatabaseHas('leads', ['phone' => '+447700900123']);
        $this->assertDatabaseHas('leads', ['phone' => '+14155552671']);
        $this->assertSame('+923001234567', LeadPhone::normalize('0300-1234567'));
        $this->post('/contact', $this->inquiry($ans, ['phone' => '+44 7700 900123', 'email' => 'another@example.test']))->assertSessionHasNoErrors();
        $this->assertDatabaseCount('lead_contacts', 4);
        $this->postJson('/contact', $this->inquiry($ans, ['phone' => '+1234567890123456']))->assertUnprocessable();
    }

    public function test_all_read_and_write_interfaces_enforce_ownership(): void
    {
        $ans = $this->staff();
        $waseem = $this->staff('Waseem', division: $ans->division);
        $nazim = $this->staff('Nazim');
        $admin = $this->staff('Atif', 'admin');
        $lead = $this->createLead($ans);
        $this->actingAs($waseem)->get('/leads')->assertOk();
        $this->getJson('/leads/data?scope=all')->assertJsonPath('recordsTotal', 0);
        $this->getJson('/leads/events?start=2020-01-01&end=2040-01-01&scope=all')->assertJsonCount(0);
        $this->getJson('/leads/duplicates?phone=03001234567')->assertJsonCount(0);
        $this->getJson('/leads/'.$lead->id)->assertForbidden();
        $this->postJson('/leads/'.$lead->id.'/follow-ups', $this->activity($lead))->assertForbidden();
        $this->getJson('/leads/data?assigned_to='.$ans->id)->assertForbidden();
        $this->getJson('/leads/data?scope=unassigned')->assertForbidden();
        $this->actingAs($nazim)->getJson('/leads/'.$lead->id)->assertForbidden();
        $this->getJson('/leads/data')->assertJsonPath('recordsTotal', 0);
        $this->getJson('/leads/duplicates?phone=03001234567')->assertJsonCount(0);
        $this->actingAs($admin)->getJson('/leads/'.$lead->id)->assertOk();
        $this->getJson('/leads/data?scope=all')->assertJsonPath('recordsTotal', 1);
        $admin->update(['is_active' => false]);
        $this->get('/leads')->assertForbidden();
    }

    public function test_manual_creation_detects_duplicates_and_allows_reuse_or_separate_cases(): void
    {
        $owner = $this->staff();
        $this->actingAs($owner);
        $payload = ['party_name' => 'Manual Lead', 'phone' => '0300-1234567', 'assigned_to' => (string) $owner->id, 'follow_up_date' => now('Asia/Karachi')->addDay()->toDateString()];
        $first = $this->postJson('/leads', $payload)->assertOk()->json('party_id');
        $firstFollowup = Lead::find($first)->openLeadFollowup;
        $this->postJson('/leads', $payload)->assertConflict()->assertJsonCount(1, 'duplicates');
        $this->postJson('/leads', $payload + ['reuse_party_id' => $first, 'version' => 1])->assertOk();
        $this->assertDatabaseCount('leads', 1);
        $this->assertNotNull($firstFollowup->fresh()->cancelled_at);
        $this->assertSame(1, Lead::find($first)->leadDetails()->open()->count());
        $this->postJson('/leads', $payload + ['force_duplicate' => true])->assertOk();
        $this->assertDatabaseCount('leads', 2);
        $this->getJson('/leads/data?search[value]=Manual&length=1&start=1&draw=7')->assertOk()
            ->assertJsonPath('draw', 7)->assertJsonPath('recordsFiltered', 2)->assertJsonCount(1, 'data');
    }

    public function test_followups_keep_one_open_event_and_distinguish_completion_from_rescheduling(): void
    {
        $owner = $this->staff();
        $lead = $this->createLead($owner);
        $first = $lead->openLeadFollowup;
        $date = now('Asia/Karachi')->addDays(2)->toDateString();
        $this->postJson('/leads/'.$lead->id.'/follow-ups', $this->activity($lead, ['follow_up_date' => $date, 'follow_up_time' => '14:00']))->assertOk();
        $this->assertNotNull($first->fresh()->completed_at);
        $second = $lead->fresh()->openLeadFollowup;
        $this->postJson('/leads/'.$lead->id.'/follow-ups', $this->activity($lead, ['follow_up_date' => $date, 'reschedule_only' => true]))->assertOk();
        $this->assertNotNull($second->fresh()->cancelled_at);
        $this->assertSame(1, $lead->leadDetails()->open()->count());
        $this->assertSame($second->id, $lead->fresh()->openLeadFollowup->supersedes_id);
        $this->postJson('/leads/'.$lead->id.'/follow-ups', $this->activity($lead, ['follow_up_time' => '12:30']))->assertUnprocessable();
        $this->postJson('/leads/'.$lead->id.'/follow-ups', $this->activity($lead, ['follow_up_date' => '2000-01-01']))->assertUnprocessable();
        $this->postJson('/leads/'.$lead->id.'/follow-ups', $this->activity($lead, ['lead_status' => 'Lost', 'follow_up_date' => $date]))->assertUnprocessable();
    }

    public function test_calendar_overdue_logic_and_dashboard_use_karachi_boundaries(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-10-21 00:30:00', 'Asia/Karachi'));
        try {
            $owner = $this->staff();
            $timed = $this->createLead($owner, ['follow_up_date' => '2026-10-21', 'follow_up_time' => '00:15']);
            $allDay = $this->createLead($owner, ['phone' => '+14155552671', 'follow_up_date' => '2026-10-21']);
            $this->assertTrue($timed->openLeadFollowup->isOverdue());
            $this->assertFalse($allDay->openLeadFollowup->isOverdue());
            $events = $this->getJson('/leads/events?start=2026-10-21&end=2026-10-22')->assertOk()->assertJsonCount(2)->json();
            $this->assertStringContainsString('+05:00', $events[0]['start']);
            $this->assertFalse($events[0]['allDay']);
            $this->assertTrue($events[1]['allDay']);
            $this->getJson('/leads/data?overdue=1')->assertJsonPath('recordsTotal', 1);
            $this->get('/dashboard')->assertOk()->assertSee('Overdue follow-ups: 1');
        } finally {
            CarbonImmutable::setTestNow();
        }
    }

    public function test_reassignment_moves_access_and_records_old_and_new_owners(): void
    {
        $ans = $this->staff();
        $nazim = $this->staff('Nazim');
        $admin = $this->staff('Atif', 'admin');
        $lead = $this->createLead($ans);
        $this->postJson('/leads/'.$lead->id.'/follow-ups', $this->activity($lead, ['assigned_to' => $nazim->id]))->assertForbidden();
        $this->assertSame(1, $lead->fresh()->version);
        $this->actingAs($admin)->postJson('/leads/'.$lead->id.'/follow-ups', $this->activity($lead, ['assigned_to' => $nazim->id]))->assertOk();
        $this->assertSame($nazim->division_id, $lead->fresh()->division_id);
        $this->assertDatabaseHas('lead_details', ['activity_type' => 'Assignment', 'remarks' => 'Ans → Nazim']);
        $this->actingAs($ans)->getJson('/leads/'.$lead->id)->assertForbidden();
        $this->actingAs($nazim)->getJson('/leads/'.$lead->id)->assertOk();
        $nazim->update(['is_active' => false]);
        $this->actingAs($admin)->getJson('/leads/data?scope=unassigned')->assertJsonPath('recordsTotal', 1);
    }

    public function test_conversion_reopening_and_repeat_customer_cases_preserve_identity_without_creating_logins(): void
    {
        $owner = $this->staff();
        $lead = $this->createLead($owner);
        $users = User::count();
        $this->postJson('/leads/'.$lead->id.'/convert', ['version' => 1, 'remarks' => 'Agreed to proceed.'])->assertOk();
        $this->assertSame($users, User::count());
        $this->assertNotNull($lead->contact->fresh()->converted_at);
        $this->assertSame(0, $lead->leadDetails()->open()->count());
        $this->postJson('/leads/'.$lead->id.'/convert', ['version' => $lead->fresh()->version])->assertUnprocessable();
        $this->postJson('/leads/'.$lead->id.'/follow-ups', $this->activity($lead))->assertUnprocessable();
        $this->postJson('/leads/'.$lead->id.'/reopen', ['version' => $lead->fresh()->version, 'reason' => ''])->assertUnprocessable();
        $this->postJson('/leads/'.$lead->id.'/reopen', ['version' => $lead->fresh()->version, 'reason' => 'Additional work requested.'])->assertOk();
        $this->assertSame('New', $lead->fresh()->lead_status);
        $this->assertNotNull($lead->contact->fresh()->converted_at);
        $this->assertSame(0, $lead->leadDetails()->open()->count());
        $otherCase = $this->createLead($owner);
        $this->postJson('/leads/'.$otherCase->id.'/convert', ['version' => $otherCase->version])->assertOk();
        $this->assertDatabaseCount('lead_contacts', 1);
        $this->assertSame(1, LeadContact::whereNotNull('converted_at')->count());
    }

    public function test_contact_edits_are_scoped_and_stale_writes_cannot_overwrite_changes(): void
    {
        $owner = $this->staff();
        $inquiry = app(LeadWorkflow::class)->website($this->inquiry($owner));
        $second = app(LeadWorkflow::class)->website($this->inquiry($owner));
        $this->actingAs($owner);
        $lead = $inquiry->lead;
        $payload = ['version' => 1, 'party_name' => 'Updated Person', 'phone' => '+44 7700 900123', 'party_email' => 'updated@example.test'];
        $this->patchJson('/leads/'.$lead->id, $payload)->assertOk();
        $this->patchJson('/leads/'.$lead->id, $payload + ['address' => 'Stale address'])->assertConflict();
        $this->assertSame(2, $lead->fresh()->version);
        $this->assertSame('Inquiry Person', $inquiry->fresh()->name);
        $this->assertSame('Inquiry Person', $second->lead->party_name);
        $this->assertDatabaseHas('lead_details', ['activity_type' => 'Contact Updated', 'lead_id' => $lead->id]);
    }

    public function test_lost_cases_require_explicit_reopening(): void
    {
        $owner = $this->staff();
        $lead = $this->createLead($owner);
        $event = $lead->openLeadFollowup;
        $this->postJson('/leads/'.$lead->id.'/follow-ups', $this->activity($lead, ['lead_status' => 'Lost']))->assertOk();
        $this->assertNotNull($event->fresh()->cancelled_at);
        $this->postJson('/leads/'.$lead->id.'/follow-ups', $this->activity($lead))->assertUnprocessable();
        $this->postJson('/leads/'.$lead->id.'/reopen', ['version' => $lead->fresh()->version, 'reason' => 'Prospect replied.'])->assertOk();
        $this->assertDatabaseHas('lead_details', ['activity_type' => 'Reopened', 'remarks' => 'Reopened from Lost: Prospect replied.']);
    }

    public function test_backfill_is_repeat_safe_preserves_content_and_does_not_invent_customers(): void
    {
        $owner = $this->staff();
        foreach (['new', 'contacted', 'in_progress', 'resolved', 'closed'] as $status) {
            ContactInquiry::create(['name' => 'Legacy', 'email' => 'legacy@example.test', 'message' => 'Original '.$status,
                'source' => 'website', 'status' => $status, 'assigned_to' => $owner->id]);
        }
        $guest = User::factory()->create(['role' => 'guest', 'is_active' => true]);
        ContactInquiry::create(['name' => 'Legacy guest owner', 'message' => 'Original unassigned inquiry',
            'source' => 'website', 'status' => 'new', 'assigned_to' => $guest->id]);
        $this->artisan('leads:backfill')->assertSuccessful();
        $this->artisan('leads:backfill')->assertSuccessful();
        $this->assertDatabaseCount('leads', 6);
        $this->assertDatabaseCount('lead_details', 6);
        $this->assertDatabaseHas('leads', ['party_name' => 'Legacy guest owner', 'assigned_to' => null]);
        $this->assertSame(2, Lead::where('lead_status', 'Lost')->count());
        $this->assertSame(0, LeadContact::whereNotNull('converted_at')->count());
        $this->assertDatabaseHas('contact_inquiries', ['message' => 'Original resolved']);
    }

    public function test_hidden_profiles_and_multi_person_departments_require_valid_public_selection(): void
    {
        $ans = $this->staff();
        $waseem = $this->staff('Waseem', division: $ans->division);
        $payload = $this->inquiry($ans);
        unset($payload['assigned_to']);
        $this->postJson('/contact', $payload)->assertUnprocessable()->assertJsonValidationErrors('assigned_to');
        $waseem->profile->update(['is_visible' => false]);
        $this->postJson('/contact', $this->inquiry($waseem))->assertUnprocessable();
    }

    public function test_competing_followup_edits_and_invalid_rescheduling_do_not_lose_the_existing_event(): void
    {
        $admin = $this->staff('Atif', 'admin');
        $lead = $this->createLead($admin);
        $payload = $this->activity($lead, ['follow_up_date' => now('Asia/Karachi')->addDays(3)->toDateString()]);
        $this->postJson('/leads/'.$lead->id.'/follow-ups', $payload)->assertOk();
        $this->postJson('/leads/'.$lead->id.'/follow-ups', $payload)->assertConflict();
        $this->assertSame(1, $lead->leadDetails()->open()->count());
        $this->postJson('/leads/'.$lead->id.'/follow-ups', $this->activity($lead, ['reschedule_only' => true]))->assertUnprocessable();
        $this->postJson('/leads/'.$lead->id.'/follow-ups', $this->activity($lead, [
            'assigned_to' => null, 'follow_up_date' => now('Asia/Karachi')->addDays(3)->toDateString(),
        ]))->assertUnprocessable();
        $this->assertSame(1, $lead->leadDetails()->open()->count());
        $this->assertSame(2, $lead->fresh()->version);
    }
}

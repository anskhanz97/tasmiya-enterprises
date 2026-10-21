<?php

namespace Tests\Feature;

use App\Models\Division;
use App\Models\Lead;
use App\Models\LeadNotification;
use App\Models\User;
use App\Services\LeadWorkflow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadReturningInquiryTest extends TestCase
{
    use RefreshDatabase;

    private function staff(string $name, ?Division $division = null, string $role = 'team_member'): User
    {
        $division ??= Division::create(['name' => $name.' department', 'slug' => strtolower($name)]);
        $user = User::factory()->create(['name' => $name, 'role' => $role, 'division_id' => $division->id, 'is_active' => true]);
        $user->profile()->create(['bio' => 'Specialist', 'is_visible' => true]);

        return $user;
    }

    private function inquiry(User $owner, string $email = 'visitor@example.test'): Lead
    {
        return app(LeadWorkflow::class)->website(['name' => 'Returning Visitor', 'email' => $email,
            'phone' => '+923001234567', 'subject' => 'Website question', 'message' => 'Please discuss these requirements with me.',
            'division_id' => $owner->division_id, 'assigned_to' => $owner->id])->lead;
    }

    public function test_returning_labels_and_history_are_limited_to_the_department_and_person(): void
    {
        $ans = $this->staff('Ans');
        $waseem = $this->staff('Waseem', $ans->division);
        $nazim = $this->staff('Nazim');
        $first = $this->inquiry($ans);
        $this->inquiry($waseem);
        $this->inquiry($nazim);
        $second = $this->inquiry($ans);
        $this->actingAs($ans)->getJson('/leads/'.$second->id)->assertOk()
            ->assertJsonPath('inquiry_classification', 'Returning Inquiry')->assertJsonPath('previous_inquiry_count', 1);
        $this->getJson('/leads/'.$second->id.'/previous-inquiries')->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $first->id);
        $this->postJson('/leads/'.$first->id.'/convert', ['version' => $first->version])->assertOk();
        $third = $this->inquiry($ans);
        $this->getJson('/leads/'.$third->id)->assertJsonPath('inquiry_classification', 'Returning Customer');
        $differentPerson = $this->inquiry($waseem, 'another@example.test');
        $this->actingAs($waseem)->getJson('/leads/'.$differentPerson->id)->assertJsonPath('inquiry_classification', 'New Inquiry');
        $this->getJson('/leads/'.$third->id.'/previous-inquiries')->assertForbidden();
        $this->assertSame('New', $third->lead_status);
    }

    public function test_conversion_in_another_department_does_not_create_a_returning_customer_here(): void
    {
        $ans = $this->staff('Ans');
        $nazim = $this->staff('Nazim');
        $tax = $this->inquiry($nazim);
        $this->actingAs($nazim)->postJson('/leads/'.$tax->id.'/convert', ['version' => $tax->version])->assertOk();
        $it = $this->inquiry($ans);
        $this->actingAs($ans)->getJson('/leads/'.$it->id)->assertJsonPath('inquiry_classification', 'New Inquiry');
        $this->getJson('/leads/data')->assertJsonPath('recordsTotal', 1);
    }

    public function test_all_previous_cases_can_be_paged_without_leaking_other_owners(): void
    {
        $owner = $this->staff('Ans');
        for ($i = 0; $i < 13; $i++) {
            $lead = $this->inquiry($owner);
        }
        $this->actingAs($owner)->getJson('/leads/'.$lead->id.'/previous-inquiries')
            ->assertJsonPath('total', 12)->assertJsonCount(10, 'data')->assertJsonPath('has_more', true);
        $this->getJson('/leads/'.$lead->id.'/previous-inquiries?page=2')->assertJsonCount(2, 'data')->assertJsonPath('has_more', false);
    }

    public function test_bell_read_actions_and_reassignment_obey_current_access(): void
    {
        $ans = $this->staff('Ans');
        $waseem = $this->staff('Waseem', $ans->division);
        $admin = $this->staff('Atif', role: 'admin');
        $lead = $this->inquiry($ans);
        $notice = LeadNotification::where('user_id', $ans->id)->firstOrFail();
        $this->actingAs($waseem)->getJson('/notifications/feed')->assertJsonPath('unread', 0);
        $this->post('/notifications/'.$notice->id.'/open')->assertNotFound();
        $this->actingAs($ans)->getJson('/notifications/feed')->assertJsonPath('unread', 1);
        $this->post('/notifications/'.$notice->id.'/open')->assertRedirect(route('leads.index', ['lead' => $lead->id]));
        $this->getJson('/notifications/feed')->assertJsonPath('unread', 0);
        $this->actingAs($admin)->postJson('/leads/'.$lead->id.'/follow-ups', [
            'version' => $lead->version, 'lead_status' => 'New', 'activity_type' => 'Note', 'assigned_to' => $waseem->id,
        ])->assertOk();
        $this->actingAs($ans)->getJson('/notifications/feed')->assertJsonCount(0, 'items');
        $this->post('/notifications/'.$notice->id.'/open')->assertNotFound();
        $this->actingAs($waseem)->getJson('/notifications/feed')->assertJsonPath('unread', 1)->assertJsonPath('items.0.title', 'Case assigned to you');
        $this->postJson('/notifications/read-all')->assertOk();
        $this->assertNotNull(LeadNotification::where('user_id', $waseem->id)->first()->read_at);
        $this->get('/notifications?view=deliveries')->assertForbidden();
        $waseem->update(['is_active' => false]);
        $this->getJson('/notifications/feed')->assertForbidden();
    }

    public function test_legacy_whatsapp_backfill_preserves_messages_and_original_reply_window_without_notices(): void
    {
        $owner = $this->staff('Ans');
        $inquiry = \App\Models\ContactInquiry::create(['name' => 'Legacy WhatsApp', 'phone' => '+923001234567',
            'source' => 'whatsapp', 'status' => 'new', 'assigned_to' => $owner->id,
            'message' => 'Original legacy message', 'external_message_id' => 'wamid.legacy']);
        $inquiry->forceFill(['created_at' => now()->subHours(25)])->save();
        $this->artisan('leads:backfill')->assertSuccessful();
        $this->artisan('leads:backfill')->assertSuccessful();
        $this->assertDatabaseCount('whatsapp_messages', 1);
        $this->assertDatabaseCount('lead_notifications', 0);
        $this->assertFalse(app(\App\Services\WhatsAppConversation::class)->window($inquiry->fresh()->lead)['open']);
    }
}

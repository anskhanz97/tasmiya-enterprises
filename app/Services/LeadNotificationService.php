<?php

namespace App\Services;

use App\Models\ContactInquiry;
use App\Models\Lead;
use App\Models\LeadNotification;
use App\Models\User;

class LeadNotificationService
{
    public function inquiry(ContactInquiry $inquiry): void
    {
        $lead = $inquiry->lead;
        $recipients = $lead->assigned_to ? User::whereKey($lead->assigned_to)
            : User::whereIn('role', ['admin', 'super_admin']);
        foreach ($recipients->where('is_active', true)->whereIn('role', ['team_member', 'admin', 'super_admin'])->get() as $recipient) {
            $this->create($recipient, $lead, 'inquiry:'.$inquiry->id, 'inquiry',
                $lead->assigned_to ? 'New inquiry' : 'Inquiry needs assignment',
                $lead->party_name.' · '.($inquiry->subject ?: 'WhatsApp message'));
        }
    }

    public function assignment(Lead $lead, int $activityId): void
    {
        $recipient = $lead->assignee()->where('is_active', true)->first();
        if ($recipient) {
            $this->create($recipient, $lead, 'assignment:'.$activityId, 'assignment', 'Case assigned to you', $lead->party_name);
        }
    }

    private function create(User $user, Lead $lead, string $key, string $kind, string $title, string $message): void
    {
        LeadNotification::firstOrCreate(['user_id' => $user->id, 'event_key' => $key], [
            'lead_id' => $lead->id, 'kind' => $kind, 'title' => $title, 'message' => $message,
        ]);
    }
}

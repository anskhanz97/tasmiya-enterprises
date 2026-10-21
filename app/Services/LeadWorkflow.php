<?php

namespace App\Services;

use App\Models\ContactInquiry;
use App\Models\Lead;
use App\Models\LeadContact;
use App\Models\LeadDetail;
use App\Models\User;
use App\Support\LeadPhone;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class LeadWorkflow
{
    public function contactFor(array $data, ?string $fallback = null): LeadContact
    {
        $phone = LeadPhone::normalize($data['phone'] ?? null);
        $email = filled($data['party_email'] ?? $data['email'] ?? null)
            ? strtolower(trim($data['party_email'] ?? $data['email'])) : null;
        // A shared phone alone is not proof that two people are the same contact.
        $identity = $phone || $email ? ($phone ?? '')."\0".($email ?? '') : 'legacy:'.$fallback;

        return LeadContact::firstOrCreate(['identity_key' => hash('sha256', $identity)], [
            'name' => $data['party_name'] ?? $data['name'] ?? 'Unknown contact',
            'phone' => $phone, 'email' => $email, 'address' => $data['address'] ?? null,
        ]);
    }

    public function website(array $data): ContactInquiry
    {
        return DB::transaction(function () use ($data) {
            $owner = User::publicInquiryRecipients()->where('division_id', $data['division_id'])
                ->lockForUpdate()->find($data['assigned_to']);
            if (! $owner) {
                throw ValidationException::withMessages(['assigned_to' => 'This specialist is no longer available. Please choose again.']);
            }
            $inquiry = ContactInquiry::create([
                'user_id' => Auth::id(), 'name' => $data['name'], 'email' => $data['email'],
                'phone' => LeadPhone::normalize($data['phone']), 'subject' => $data['subject'],
                'message' => $data['message'], 'division_id' => $owner->division_id,
                'assigned_to' => $owner->id, 'source' => 'website', 'status' => 'new',
            ]);
            $this->fromInquiry($inquiry);
            app(LeadNotificationService::class)->inquiry($inquiry->refresh());

            return $inquiry->refresh();
        }, 5);
    }

    public function fromInquiry(ContactInquiry $inquiry, string $status = 'New', ?string $migrationNote = null): Lead
    {
        return DB::transaction(function () use ($inquiry, $status, $migrationNote) {
            $inquiry = ContactInquiry::lockForUpdate()->findOrFail($inquiry->id);
            if ($inquiry->lead_id) {
                return $inquiry->lead;
            }
            $owner = $inquiry->assigned_to ? User::whereIn('role', ['team_member', 'admin', 'super_admin'])->find($inquiry->assigned_to) : null;
            $contact = $this->contactFor($inquiry->toArray(), (string) $inquiry->id);
            $lead = Lead::create([
                'lead_contact_id' => $contact->id, 'division_id' => $inquiry->division_id ?? $owner?->division_id,
                'assigned_to' => $owner?->id, 'party_name' => $inquiry->name ?: ($inquiry->source === 'whatsapp' ? 'WhatsApp contact' : 'Unknown contact'),
                'party_email' => $inquiry->email, 'phone' => LeadPhone::normalize($inquiry->phone),
                'phone_normalized' => LeadPhone::normalize($inquiry->phone), 'source' => $inquiry->source,
                'lead_status' => $status, 'created_at' => $inquiry->created_at,
            ]);
            $this->record($lead, 'Lead Created', $migrationNote ?: $inquiry->message, [
                'created_at' => $inquiry->created_at,
            ]);
            $inquiry->update(['lead_id' => $lead->id, 'division_id' => $lead->division_id]);
            $this->syncInquiries($lead);

            return $lead;
        }, 5);
    }

    public function whatsapp(array $message, ?string $senderName = null): ?array
    {
        if (($message['type'] ?? 'text') !== 'text') {
            return null;
        }
        $phone = LeadPhone::normalize($message['from'] ?? null);
        $body = $message['text']['body'] ?? null;
        $id = $message['id'] ?? null;
        if (! is_string($id) || $id === '' || strlen($id) > 191 || ! is_string($body) || trim($body) === ''
            || ! preg_match('/^\+[1-9]\d{1,14}$/', $phone ?? '') || mb_strlen($body) > 5000) {
            throw ValidationException::withMessages(['message' => 'Malformed WhatsApp text message.']);
        }
        try {
            return DB::transaction(function () use ($phone, $body, $id, $senderName, $message) {
                if ($existing = ContactInquiry::where('external_message_id', $id)->first()) {
                    return ['inquiry' => $existing, 'new_case' => false, 'duplicate' => true];
                }
                $contact = $this->contactFor(['phone' => $phone, 'name' => mb_substr($senderName ?: 'WhatsApp contact', 0, 100)]);
                // Serialize conversation creation for this phone, including the first message.
                LeadContact::whereKey($contact->id)->lockForUpdate()->firstOrFail();
                $lead = Lead::where('source', 'whatsapp')->where('phone_normalized', $phone)
                    ->whereNotIn('lead_status', ['Converted', 'Lost'])->latest('id')->lockForUpdate()->first();
                $inquiry = ContactInquiry::create([
                    'name' => $senderName ? mb_substr($senderName, 0, 100) : null, 'phone' => $phone, 'message' => $body,
                    'source' => 'whatsapp', 'source_type' => 'text', 'external_message_id' => $id,
                    'lead_id' => $lead?->id, 'assigned_to' => $lead?->assigned_to,
                    'division_id' => $lead?->division_id, 'status' => Lead::legacyStatus($lead?->lead_status ?? 'New'),
                ]);
                $newCase = ! $lead;
                if (! $lead) {
                    $lead = $this->fromInquiry($inquiry);
                } else {
                    $lead->increment('version');
                    $this->record($lead, 'WhatsApp', $body);
                }
                app(WhatsAppConversation::class)->recordInbound($inquiry->refresh(), $message);
                app(LeadNotificationService::class)->inquiry($inquiry);

                return ['inquiry' => $inquiry->refresh(), 'new_case' => $newCase, 'duplicate' => false];
            }, 5);
        } catch (UniqueConstraintViolationException $e) {
            $existing = ContactInquiry::where('external_message_id', $id)->first();
            if (! $existing) {
                throw $e;
            }

            return ['inquiry' => $existing, 'new_case' => false, 'duplicate' => true];
        }
    }

    public function create(array $data): Lead
    {
        $owner = $this->assignee($data['assigned_to'] ?? null);

        return DB::transaction(function () use ($data, $owner) {
            $contact = $this->contactFor($data);
            $lead = Lead::create([
                'lead_contact_id' => $contact->id, 'party_name' => $data['party_name'],
                'party_email' => isset($data['party_email']) ? strtolower(trim($data['party_email'])) : null,
                'phone' => LeadPhone::normalize($data['phone']), 'phone_normalized' => LeadPhone::normalize($data['phone']),
                'address' => $data['address'] ?? null, 'assigned_to' => $owner->id, 'division_id' => $owner->division_id,
                'source' => 'manual', 'created_by' => Auth::id(), 'lead_status' => 'New',
            ]);
            $activity = $this->record($lead, 'Lead Created', $data['remarks'] ?? null, $this->schedule($data));
            if ($owner->id !== Auth::id()) {
                app(LeadNotificationService::class)->assignment($lead, $activity->id);
            }

            return $lead;
        }, 5);
    }

    public function updateContact(Lead $lead, array $data): Lead
    {
        return $this->mutate($lead, $data['version'], function (Lead $locked) use ($data) {
            $contact = $this->contactFor($data);
            $changes = [];
            foreach (['party_name', 'party_email', 'phone', 'address'] as $field) {
                if (($locked->$field ?? '') !== ($data[$field] ?? '')) {
                    $changes[] = $field;
                }
            }
            $locked->fill([
                'lead_contact_id' => $contact->id, 'party_name' => $data['party_name'],
                'party_email' => filled($data['party_email'] ?? null) ? strtolower(trim($data['party_email'])) : null,
                'phone' => LeadPhone::normalize($data['phone']), 'phone_normalized' => LeadPhone::normalize($data['phone']),
                'address' => $data['address'] ?? null,
            ])->save();
            $this->record($locked, 'Contact Updated', 'Updated: '.implode(', ', $changes));
        });
    }

    public function activity(Lead $lead, array $data): Lead
    {
        return $this->mutate($lead, $data['version'], function (Lead $locked) use ($data) {
            if ($locked->isClosed()) {
                throw ValidationException::withMessages(['lead_status' => 'Reopen this case before adding another activity.']);
            }
            $requested = array_key_exists('assigned_to', $data)
                ? (filled($data['assigned_to']) ? (int) $data['assigned_to'] : null) : $locked->assigned_to;
            $owner = $requested ? $this->assignee($requested) : null;
            if (! Auth::user()->isAdmin() && $requested !== Auth::id()) {
                abort(403);
            }
            if ($owner?->id !== $locked->assigned_to) {
                $old = $locked->assignee?->name ?: 'Unassigned';
                $locked->assigned_to = $owner?->id;
                $locked->division_id = $owner?->division_id;
                $assignment = $this->record($locked, 'Assignment', $old.' → '.($owner?->name ?: 'Unassigned'));
                app(LeadNotificationService::class)->assignment($locked, $assignment->id);
            }
            $open = $locked->leadDetails()->open()->lockForUpdate()->get();
            foreach ($open as $detail) {
                $detail->update([
                    ! empty($data['reschedule_only']) || $data['lead_status'] === 'Lost' ? 'cancelled_at' : 'completed_at' => now(),
                ]);
            }
            $locked->lead_status = $data['lead_status'];
            $locked->save();
            $this->record($locked, $data['activity_type'], $data['remarks'] ?? null, array_merge($this->schedule($data), [
                'supersedes_id' => $open->last()?->id,
            ]));
            $this->syncInquiries($locked);
        });
    }

    public function convert(Lead $lead, array $data): Lead
    {
        return $this->mutate($lead, $data['version'], function (Lead $locked) use ($data) {
            if ($locked->isClosed()) {
                throw ValidationException::withMessages(['lead_status' => 'This case is closed. Reopen it first.']);
            }
            $contact = LeadContact::whereKey($locked->lead_contact_id)->lockForUpdate()->firstOrFail();
            if (! $contact->converted_at) {
                $contact->update([
                    'name' => $locked->party_name, 'phone' => $locked->phone, 'email' => $locked->party_email,
                    'address' => $locked->address, 'converted_at' => now(), 'converted_by' => Auth::id(),
                ]);
            }
            $locked->leadDetails()->open()->update(['cancelled_at' => now()]);
            $locked->update(['lead_status' => 'Converted']);
            $this->record($locked, 'Conversion', $data['remarks'] ?? null);
            $this->syncInquiries($locked);
        });
    }

    public function reopen(Lead $lead, array $data): Lead
    {
        return $this->mutate($lead, $data['version'], function (Lead $locked) use ($data) {
            if (! $locked->isClosed()) {
                throw ValidationException::withMessages(['reason' => 'Only Converted or Lost cases can be reopened.']);
            }
            $old = $locked->lead_status;
            $locked->update(['lead_status' => 'New']);
            $this->record($locked, 'Reopened', 'Reopened from '.$old.': '.$data['reason']);
            $this->syncInquiries($locked);
        });
    }

    private function mutate(Lead $lead, int $version, callable $operation): Lead
    {
        return DB::transaction(function () use ($lead, $version, $operation) {
            // The conditional write also serializes SQLite writes, where FOR UPDATE is unavailable.
            $changed = Lead::whereKey($lead->id)->where('version', $version)->update(['version' => DB::raw('version + 1')]);
            abort_unless($changed, 409, 'This case has changed. Reload its details before saving again.');
            $locked = Lead::lockForUpdate()->findOrFail($lead->id);
            Gate::authorize('update', $locked);
            $operation($locked);

            return $locked->refresh();
        }, 5);
    }

    public function assignee(?int $id): User
    {
        $user = Auth::user();
        if (! $user->isAdmin() && $id && $id !== $user->id) {
            abort(403);
        }
        $owner = User::where('is_active', true)->whereIn('role', ['team_member', 'admin', 'super_admin'])->find($id ?: $user->id);
        if (! $owner) {
            throw ValidationException::withMessages(['assigned_to' => 'Choose an active specialist.']);
        }

        return $owner;
    }

    private function record(Lead $lead, string $type, ?string $remarks, array $extra = []): LeadDetail
    {
        return $lead->leadDetails()->create(array_merge([
            'lead_status' => $lead->lead_status, 'activity_type' => $type, 'assigned_to' => $lead->assigned_to,
            'assigned_to_name' => $lead->assigned_to ? User::find($lead->assigned_to)?->name : null,
            'remarks' => $remarks, 'created_by' => Auth::id(),
        ], $extra));
    }

    private function schedule(array $data): array
    {
        return ['follow_up_date' => $data['follow_up_date'] ?? null, 'follow_up_time' => $data['follow_up_time'] ?? null];
    }

    private function syncInquiries(Lead $lead): void
    {
        $updates = ['status' => Lead::legacyStatus($lead->lead_status), 'assigned_to' => $lead->assigned_to];
        if (in_array($lead->lead_status, ['Converted', 'Lost'], true)) {
            $updates['resolved_at'] = now();
        } else {
            $updates['resolved_at'] = null;
        }
        $lead->inquiries()->update($updates);
        if ($lead->lead_status !== 'New') {
            $lead->inquiries()->whereNull('responded_at')->update(['responded_at' => now()]);
        }
    }
}

<?php

namespace App\Services;

use App\Mail\LeadFollowupDigest;
use App\Models\LeadDetail;
use App\Models\LeadDigestDelivery;
use App\Models\LeadNotification;
use App\Models\SiteSetting;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Mail;

class LeadReminderService
{
    public function settings(): array
    {
        return [
            'enabled' => SiteSetting::get('integration_enable_lead_reminders', config('leads.reminders.enabled') ? '1' : '0') === '1',
            'time' => SiteSetting::get('integration_lead_reminder_time') ?: config('leads.reminders.time'),
            'upcoming_days' => (int) (SiteSetting::get('integration_lead_upcoming_days') ?? config('leads.reminders.upcoming_days')),
        ];
    }

    public function followups(User $user, ?CarbonImmutable $now = null)
    {
        $now ??= CarbonImmutable::now(config('leads.timezone'));
        $lastDay = $now->addDays($this->settings()['upcoming_days'])->toDateString();

        return LeadDetail::open()->whereDate('follow_up_date', '<=', $lastDay)
            ->whereHas('lead', fn ($q) => $q->where('assigned_to', $user->id)->whereNotIn('lead_status', ['Converted', 'Lost']))
            ->with('lead.division')->orderBy('follow_up_date')->orderBy('follow_up_time')->get()
            ->map(function ($detail) use ($now) {
                $day = $detail->follow_up_date->format('Y-m-d');
                $due = CarbonImmutable::parse($day.' '.($detail->follow_up_time ?: $this->settings()['time']), config('leads.timezone'));
                $category = $detail->isOverdue() ? 'Overdue' : ($day === $now->toDateString() ? 'Today' : 'Upcoming');

                return ['id' => $detail->id, 'lead_id' => $detail->lead_id, 'name' => $detail->lead->party_name,
                    'department' => $detail->lead->division?->name ?: 'Unassigned department',
                    'due' => $due->format('D, d M Y').($detail->follow_up_time ? ' · '.$due->format('g:i A') : ' · All day'),
                    'category' => $category, 'remarks' => $detail->remarks,
                    'url' => route('leads.index', ['lead' => $detail->lead_id])];
            });
    }

    public function run(int $limit = 25): array
    {
        $now = CarbonImmutable::now(config('leads.timezone'));
        $settings = $this->settings();
        $result = ['prepared' => 0, 'sent' => 0, 'failed' => 0, 'unconfigured' => 0];
        if (! $settings['enabled'] || $now->format('H:i') < $settings['time']) {
            return $result;
        }
        LeadDigestDelivery::whereDate('digest_date', '<', $now->toDateString())
            ->whereNotIn('status', ['sent', 'skipped'])->update(['status' => 'skipped', 'locked_until' => null, 'last_error' => 'Expired daily summary; a current summary replaces it.']);

        User::where('is_active', true)->whereIn('role', ['team_member', 'admin', 'super_admin'])->orderBy('id')
            ->chunkById(100, function ($users) use ($now, &$result) {
                foreach ($users as $user) {
                    $items = $this->followups($user, $now);
                    if ($items->isEmpty()) {
                        continue;
                    }
                    $counts = $items->countBy('category');
                    LeadNotification::firstOrCreate(['user_id' => $user->id, 'event_key' => 'digest:'.$now->toDateString()], [
                        'kind' => 'digest', 'title' => 'Your daily follow-up summary',
                        'message' => ($counts['Overdue'] ?? 0).' overdue · '.($counts['Today'] ?? 0).' today · '.($counts['Upcoming'] ?? 0).' upcoming',
                    ]);
                    $delivery = LeadDigestDelivery::firstOrCreate(['user_id' => $user->id, 'digest_date' => $now->toDateString()]);
                    $result['prepared'] += (int) $delivery->wasRecentlyCreated;
                }
            });

        $deliveries = LeadDigestDelivery::whereDate('digest_date', $now->toDateString())
            ->whereIn('status', ['pending', 'unconfigured', 'failed', 'sending'])
            ->where('attempts', '<', 5)->where(fn ($q) => $q->whereNull('retry_at')->orWhere('retry_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('locked_until')->orWhere('locked_until', '<', now()))
            ->orderBy('id')->limit($limit)->get();
        foreach ($deliveries as $delivery) {
            $status = $this->deliver($delivery);
            if (isset($result[$status])) {
                $result[$status]++;
            }
        }

        return $result;
    }

    private function deliver(LeadDigestDelivery $delivery): string
    {
        $claimed = LeadDigestDelivery::whereKey($delivery->id)->whereNotIn('status', ['sent', 'skipped'])
            ->where(fn ($q) => $q->whereNull('locked_until')->orWhere('locked_until', '<', now()))
            ->update(['status' => 'sending', 'locked_until' => now()->addMinutes(10)]);
        if (! $claimed) {
            return 'skipped';
        }
        $delivery->refresh();
        $user = $delivery->user;
        $items = $user && $user->is_active && in_array($user->role, ['team_member', 'admin', 'super_admin'], true) ? $this->followups($user) : collect();
        if ($items->isEmpty() || ! filter_var($user?->email, FILTER_VALIDATE_EMAIL)) {
            $delivery->update(['status' => 'skipped', 'locked_until' => null, 'last_error' => 'No current assigned follow-ups or active recipient.']);

            return 'skipped';
        }
        $configuration = app(LeadMailConfiguration::class)->configure();
        if (! $configuration) {
            $delivery->update(['status' => 'unconfigured', 'locked_until' => null, 'last_error' => 'Configure SMTP and a sender email in Settings.']);

            return 'unconfigured';
        }
        $delivery->increment('attempts');
        try {
            Mail::mailer($configuration['mailer'])->to($user->email)->send(
                (new LeadFollowupDigest($user, $items->all(), $delivery->digest_date->format('Y-m-d')))
                    ->from($configuration['from'], $configuration['name'])
            );
            $delivery->update(['status' => 'sent', 'sent_at' => now(), 'locked_until' => null, 'retry_at' => null, 'last_error' => null]);

            return 'sent';
        } catch (\Throwable $e) {
            $delivery->update(['status' => 'failed', 'locked_until' => null,
                'retry_at' => now()->addMinutes(min(60, 15 * $delivery->attempts)),
                'last_error' => 'SMTP delivery failed. Check the configured account and connection.']);
            report($e);

            return 'failed';
        }
    }
}

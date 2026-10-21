<?php

namespace App\Http\Controllers;

use App\Mail\LeadFollowupDigest;
use App\Models\Lead;
use App\Models\LeadDigestDelivery;
use App\Models\LeadNotification;
use App\Models\User;
use App\Services\LeadMailConfiguration;
use App\Services\LeadReminderService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class LeadNotificationController extends Controller
{
    private function authorizeInbox(): User
    {
        $this->authorize('viewAny', Lead::class);

        return auth()->user();
    }

    public function index(Request $request, LeadReminderService $reminders)
    {
        $user = $this->authorizeInbox();
        $view = $request->validate(['view' => 'nullable|in:inbox,followups,deliveries', 'user_id' => 'nullable|integer'])['view'] ?? 'inbox';
        abort_if($view === 'deliveries' && ! $user->isAdmin(), 403);
        $owner = $user;
        if ($request->filled('user_id')) {
            abort_unless($user->isAdmin() || $request->integer('user_id') === $user->id, 403);
            $owner = User::whereIn('role', ['team_member', 'admin', 'super_admin'])->findOrFail($request->integer('user_id'));
        }

        return view('notifications.index', [
            'section' => $view, 'owner' => $owner,
            'notices' => LeadNotification::forUser($user)->latest('id')->paginate(20)->withQueryString(),
            'followups' => $view === 'followups' ? $reminders->followups($owner) : collect(),
            'deliveries' => $view === 'deliveries' ? LeadDigestDelivery::with('user')->latest('id')->paginate(20)->withQueryString() : null,
            'staff' => $user->isAdmin() ? User::where('is_active', true)->whereIn('role', ['team_member', 'admin', 'super_admin'])->orderBy('name')->get() : collect(),
            'reminderSettings' => $reminders->settings(),
            'mailConfigured' => app(LeadMailConfiguration::class)->configure() !== null,
            'lastRun' => Cache::get('lead-reminders-last-run'),
        ]);
    }

    public function feed()
    {
        $user = $this->authorizeInbox();
        $query = LeadNotification::forUser($user);

        return response()->json(['unread' => (clone $query)->whereNull('read_at')->count(),
            'items' => $query->latest('id')->limit(8)->get()->map(fn ($notice) => [
                'id' => $notice->id, 'title' => $notice->title, 'message' => $notice->message,
                'read' => $notice->read_at !== null, 'created_at' => $notice->created_at->diffForHumans(),
                'open_url' => route('notifications.open', $notice),
            ])])->header('Cache-Control', 'private, no-store');
    }

    public function open(LeadNotification $notification)
    {
        $user = $this->authorizeInbox();
        abort_unless(LeadNotification::forUser($user)->whereKey($notification->id)->exists(), 404);
        $notification->update(['read_at' => $notification->read_at ?: now()]);

        return redirect($notification->targetUrl());
    }

    public function readAll(Request $request)
    {
        $user = $this->authorizeInbox();
        LeadNotification::forUser($user)->whereNull('read_at')->update(['read_at' => now()]);

        return $request->expectsJson() ? response()->json(['unread' => 0]) : back();
    }

    public function retry(LeadDigestDelivery $delivery)
    {
        $user = $this->authorizeInbox();
        abort_unless($user->isAdmin(), 403);
        abort_unless($delivery->digest_date->format('Y-m-d') === CarbonImmutable::now(config('leads.timezone'))->toDateString()
            && in_array($delivery->status, ['failed', 'unconfigured'], true), 422, 'Only today’s failed or unconfigured summaries can be retried.');
        $delivery->update(['status' => 'pending', 'attempts' => 0, 'retry_at' => null, 'locked_until' => null, 'last_error' => null]);

        return back()->with('success', 'Summary queued for the next scheduler run.');
    }

    public function emailPreview(LeadReminderService $reminders)
    {
        $user = $this->authorizeInbox();
        abort_unless($user->isAdmin(), 403);

        return $this->previewMail($reminders)->render();
    }

    public function testEmail(LeadReminderService $reminders)
    {
        $user = $this->authorizeInbox();
        abort_unless($user->isAdmin(), 403);
        $configuration = app(LeadMailConfiguration::class)->configure();
        if (! $configuration) {
            return back()->with('error', 'Configure SMTP and a sender email in Settings first.');
        }
        try {
            Mail::mailer($configuration['mailer'])->to($user->email)->send($this->previewMail($reminders)->from($configuration['from'], $configuration['name']));

            return back()->with('success', 'Test summary sent to '.$user->email.'.');
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'The test email could not be sent. Check the SMTP account and connection.');
        }
    }

    private function previewMail(LeadReminderService $reminders): LeadFollowupDigest
    {
        $items = $reminders->followups(auth()->user());
        if ($items->isEmpty()) {
            $items = collect([['name' => 'Example lead (preview only)', 'department' => 'Example department', 'category' => 'Upcoming',
                'due' => 'Example follow-up · 10:00 AM', 'remarks' => 'Discuss the service requirements.', 'lead_id' => 'example', 'url' => route('leads.index')]]);
        }

        return new LeadFollowupDigest(auth()->user(), $items->all(), CarbonImmutable::now(config('leads.timezone'))->toDateString());
    }
}

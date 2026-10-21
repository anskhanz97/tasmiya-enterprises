@extends('layouts.app')
@section('title', 'Notifications & follow-ups')
@section('content')
<main class="notification-page"><div class="notification-page-inner">
    <a href="{{ route('dashboard') }}">Back to workspace</a>
    <h1>Notifications & follow-ups</h1>
    <p>Your inquiries, assignments, and daily follow-up summary in one place.</p>
    @if(session('success'))<div class="notification-alert" role="status">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="notification-alert error" role="alert">{{ session('error') }}</div>@endif
    <nav class="notification-tabs" aria-label="Notification views">
        <a href="{{ route('notifications.index') }}" @if($section==='inbox') aria-current="page" @endif>My notifications</a>
        <a href="{{ route('notifications.index', ['view'=>'followups']) }}" @if($section==='followups') aria-current="page" @endif>Follow-ups</a>
        @if(auth()->user()->isAdmin())<a href="{{ route('notifications.index', ['view'=>'deliveries']) }}" @if($section==='deliveries') aria-current="page" @endif>Email delivery</a>@endif
    </nav>
    @if($section==='inbox')
        <div class="notification-actions"><form action="{{ route('notifications.read-all') }}" method="POST">@csrf<button class="notification-action">Mark all as read</button></form><a href="{{ route('leads.index') }}">Open Lead Management</a></div>
        @forelse($notices as $notice)
            <article class="notification-card {{ $notice->read_at ? '' : 'is-unread' }}"><div class="notification-card-header"><h2>{{ $notice->title }}</h2><time datetime="{{ $notice->created_at->toIso8601String() }}">{{ $notice->created_at->timezone(config('leads.timezone'))->format('d M Y, g:i A') }}</time></div><p>{{ $notice->message }}</p><form action="{{ route('notifications.open', $notice) }}" method="POST">@csrf<button class="notification-action">{{ $notice->lead_id ? 'Open case' : 'View follow-ups' }}</button></form></article>
        @empty<div class="notification-card"><h2>You’re all caught up.</h2><p>New inquiries, assignments, and daily follow-up summaries will appear here.</p></div>@endforelse
        {{ $notices->links() }}
    @elseif($section==='followups')
        <div class="notification-card"><h2>{{ $owner->name }}’s follow-ups</h2><p>Overdue, today, and the next {{ $reminderSettings['upcoming_days'] }} days · Asia/Karachi. Summaries run daily at {{ $reminderSettings['time'] }}{{ $reminderSettings['enabled'] ? '' : ' (currently disabled)' }}.</p>
        @if(auth()->user()->isAdmin())<form method="GET" class="notification-actions"><input type="hidden" name="view" value="followups"><label for="followup-owner">Assigned person</label><select id="followup-owner" name="user_id">@foreach($staff as $person)<option value="{{ $person->id }}" @selected($owner->id===$person->id)>{{ $person->name }}</option>@endforeach</select><button class="notification-action">Show follow-ups</button></form>@endif
        </div>
        @forelse($followups as $item)
        <article class="notification-card"><div class="notification-card-header"><h2>{{ $item['name'] }}</h2><span class="notification-chip {{ strtolower($item['category']) }}">{{ $item['category'] }}</span></div><p>{{ $item['department'] }} · {{ $item['due'] }}</p>@if($item['remarks'])<p>{{ $item['remarks'] }}</p>@endif<a class="notification-action" href="{{ $item['url'] }}">Open case #{{ $item['lead_id'] }}</a></article>
        @empty<div class="notification-card"><h2>No follow-ups in this window.</h2><p>Schedule a follow-up on a lead to include it here and in the daily summary.</p></div>@endforelse
    @else
        <section class="notification-card"><h2>Daily summary delivery</h2><p>{{ $mailConfigured ? 'SMTP is configured.' : 'SMTP is not configured. Website notifications continue to work; emails wait for configuration.' }} One summary goes to each assigned person at {{ $reminderSettings['time'] }} Asia/Karachi. Admins oversee delivery here.</p><p>Scheduler last checked: {{ $lastRun ? \Carbon\CarbonImmutable::parse($lastRun)->timezone(config('leads.timezone'))->format('d M Y, g:i A') : 'Not observed yet. Start the application scheduler.' }}</p><div class="notification-actions"><a class="notification-action" href="{{ route('settings.index') }}#ig-email">Configure email</a><a class="notification-action" href="{{ route('settings.index') }}#ig-reminders">Reminder settings</a><a class="notification-action" href="{{ route('notifications.email-preview') }}" target="_blank" rel="noopener">Preview template</a><form method="POST" action="{{ route('notifications.test-email') }}">@csrf<button class="notification-action" @disabled(!$mailConfigured)>Send test to my email</button></form></div><small>The test sends only to your account email: {{ auth()->user()->email }}. Failed summaries retry up to five times. Expired summaries are replaced by the current day’s summary.</small></section>
        <section class="notification-card"><div class="notification-table-scroll"><table><thead><tr><th>Person</th><th>Summary date</th><th>Status</th><th>Attempts</th><th>Delivery details</th></tr></thead><tbody>@forelse($deliveries as $delivery)<tr><td>{{ $delivery->user?->name ?? 'Former user' }}</td><td>{{ $delivery->digest_date->format('d M Y') }}</td><td><span class="notification-chip {{ $delivery->status }}">{{ ucfirst($delivery->status) }}</span></td><td>{{ $delivery->attempts }}</td><td>{{ $delivery->sent_at?->timezone(config('leads.timezone'))->format('d M Y, g:i A') }}@if($delivery->last_error)<small>{{ $delivery->last_error }}</small>@endif @if(in_array($delivery->status,['failed','unconfigured']) && $delivery->digest_date->format('Y-m-d')===now(config('leads.timezone'))->toDateString())<form method="POST" action="{{ route('notifications.retry', $delivery) }}">@csrf<button class="notification-action">Retry current summary</button></form>@endif</td></tr>@empty<tr><td colspan="5">No daily summaries prepared yet.</td></tr>@endforelse</tbody></table></div>{{ $deliveries->links() }}</section>
    @endif
</div></main>
@endsection

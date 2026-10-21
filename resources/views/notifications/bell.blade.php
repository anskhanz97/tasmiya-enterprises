@php($unreadNotices = \App\Models\LeadNotification::forUser(auth()->user())->whereNull('read_at')->count())
<div class="notification-bell" data-notification-bell data-feed="{{ route('notifications.feed') }}" data-read-all="{{ route('notifications.read-all') }}">
    <button class="notification-trigger" type="button" aria-label="Notifications, {{ $unreadNotices }} unread" aria-expanded="false" aria-controls="notificationMenu">
        <svg viewBox="0 0 24 24" width="21" height="21" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/></svg>
        <span class="notification-count" @if(!$unreadNotices) hidden @endif>{{ $unreadNotices > 99 ? '99+' : $unreadNotices }}</span>
    </button>
    <section class="notification-menu" id="notificationMenu" aria-label="Recent notifications" hidden>
        <header><strong>Notifications</strong><button type="button" class="notification-read-all">Mark all read</button></header>
        <div class="notification-items" aria-live="polite"><p class="notification-empty">Loading notifications…</p></div>
        <a class="notification-view-all" href="{{ route('notifications.index') }}">View all notifications</a>
    </section>
</div>

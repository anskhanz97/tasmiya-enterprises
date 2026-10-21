export function initializeNotifications() {
    const bell = document.querySelector('[data-notification-bell]');
    if (!bell) return;
    const trigger = bell.querySelector('.notification-trigger');
    const menu = bell.querySelector('.notification-menu');
    const count = bell.querySelector('.notification-count');
    const items = bell.querySelector('.notification-items');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    let loading = false;
    let stopped = false;
    let lastFeed = '';
    const close = () => { menu.hidden = true; trigger.setAttribute('aria-expanded', 'false'); };
    const setCount = unread => {
        count.textContent = unread > 99 ? '99+' : unread;
        count.hidden = unread === 0;
        trigger.setAttribute('aria-label', `Notifications, ${unread} unread`);
    };
    const load = async () => {
        if (loading || stopped || document.hidden) return;
        loading = true;
        try {
            const response = await fetch(bell.dataset.feed, {headers: {'Accept': 'application/json'}, signal: AbortSignal.timeout(10000)});
            if ([401, 403, 419].includes(response.status)) { stopped = true; close(); return; }
            if (!response.ok) throw new Error('Unable to load');
            const data = await response.json();
            setCount(data.unread);
            const fingerprint = JSON.stringify(data.items.map(item => [item.id, item.read]));
            if (fingerprint === lastFeed || items.contains(document.activeElement)) return;
            lastFeed = fingerprint;
            items.replaceChildren();
            if (!data.items.length) {
                const empty = document.createElement('p');
                empty.className = 'notification-empty';
                empty.textContent = 'You’re all caught up. New inquiries and reminders will appear here.';
                items.append(empty);
            }
            for (const item of data.items) {
                const form = document.createElement('form');
                form.method = 'POST'; form.action = item.open_url;
                const token = document.createElement('input');
                token.type = 'hidden'; token.name = '_token'; token.value = csrf;
                const button = document.createElement('button');
                button.type = 'submit'; button.className = `notification-item${item.read ? '' : ' is-unread'}`;
                button.setAttribute('aria-label', `${item.read ? 'Read' : 'Unread'}: ${item.title}. ${item.message}. ${item.created_at}`);
                for (const [tag, value] of [['strong', item.title], ['span', item.message], ['small', item.created_at]]) {
                    const node = document.createElement(tag); node.textContent = value; button.append(node);
                }
                form.append(token, button); items.append(form);
            }
        } catch {
            lastFeed = '';
            if (!menu.hidden) items.textContent = 'Notifications could not load. Close and reopen to retry.';
        } finally { loading = false; }
    };
    trigger.addEventListener('click', () => {
        menu.hidden = !menu.hidden;
        trigger.setAttribute('aria-expanded', String(!menu.hidden));
        if (!menu.hidden) load();
    });
    bell.querySelector('.notification-read-all').addEventListener('click', async event => {
        event.currentTarget.disabled = true;
        try {
            const response = await fetch(bell.dataset.readAll, {method: 'POST', headers: {'Accept': 'application/json', 'X-CSRF-TOKEN': csrf}});
            if (!response.ok) throw new Error('Unable to update');
            await load();
        } catch { items.textContent = 'Could not mark notifications as read. Please try again.'; }
        finally { event.target.disabled = false; }
    });
    document.addEventListener('click', event => { if (!bell.contains(event.target)) close(); });
    document.addEventListener('keydown', event => { if (event.key === 'Escape' && !menu.hidden) { close(); trigger.focus(); } });
    document.addEventListener('visibilitychange', () => { if (!document.hidden) load(); });
    setInterval(load, 30000);
}

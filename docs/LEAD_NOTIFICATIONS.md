# Lead notifications and email setup

## Daily use

The bell shows the signed-in person's unread count and latest eight notifications. It refreshes every 30 seconds while the page is visible. Opening a notice marks it read and opens its case or follow-up list. Mark all read changes only that person's accessible notices. Former owners lose access to notices about transferred cases; inactive accounts cannot use the feed.

New website inquiries and incoming WhatsApp text notify the assigned person. Unassigned WhatsApp inquiries notify active admins for allocation. Assignment changes notify the new owner. Existing company email/WhatsApp inquiry alerts remain separate.

At **09:00 Asia/Karachi**, active assigned people receive a daily summary of overdue items, today's follow-ups, and the next **seven days**. No assigned follow-ups means no summary. Admins are not copied: they can select another owner in **Notifications → Follow-ups** and inspect the global **Email delivery** ledger. All-day follow-ups remain Today throughout their date and become overdue the next day. Completed/cancelled/Converted/Lost items are excluded. Assignment and active-account checks run again before email preparation.

Returning classifications match normalized contact identity, department, and current recipient. The second case is Returning Inquiry unless an earlier matching case has a Conversion activity, in which case it is Returning Customer. Other departments/people start as New Inquiry. The chip does not replace the six workflow statuses. Earlier cases are paginated and remain subject to current owner/admin access. A shared phone with a different email is not proof of the same person.

## Configure email and reminders

1. In **Settings → Email delivery**, save SMTP host, port, username/password, From email, and connection security. Use the provider's verified sender. Existing secret storage encrypts the password.
2. In **Settings → Lead reminders**, enable summaries and choose the time/upcoming window. Defaults are 09:00 and seven days. Disabling summaries leaves inquiry/assignment notifications enabled.
3. Set the deployment's `APP_URL` correctly for email links.
4. In **Notifications → Email delivery**, preview the template and use **Send test to my email**. Tests go only to the signed-in admin's account email, not an arbitrary recipient.
5. Confirm the scheduler's last check and delivery records on that page.

Before configuration, website notices still work and email records remain Unconfigured. Configuring SMTP permits the current day's summary to send after the configured time. Old unsent summaries expire instead of arriving as a backlog. The default log mailer is never counted as delivery; explicitly configured environment SMTP is also supported.

Failed sends retry up to five attempts with 15/30/45/60-minute backoff. Admins may retry today's failed/unconfigured summary; successful or old summaries cannot be manually resent. Claims and scheduler locks prevent overlapping sends. SMTP disconnects after remote acceptance can leave acceptance uncertain; a successful transport response does not prove inbox/read delivery.

Templates: `resources/views/mail/lead-followup-digest.blade.php` and `lead-followup-digest-text.blade.php`. They contain the current assigned items, dates, remarks, and case links, with HTML escaping and a plain-text alternative.

## Scheduler

For local Windows development on D:

```powershell
& .\scripts\start-scheduler.ps1
php artisan schedule:list
php artisan leads:remind
```

The existing local `storage/app/local-runtime/start.ps1` also starts it. The tracked scheduler helper avoids starting duplicates and launches a hidden `schedule:work` process. Logs live in `storage/app/local-runtime/scheduler*.log`. After restarting the computer, run the start script again. Late starts catch up the current day only. No queue worker is required.

For a future Linux deployment, install the standard once-per-minute Laravel scheduler job with the real project path (documentation placeholder, not a Windows command):

```cron
* * * * * cd /path/to/project && php artisan schedule:run >> /path/to/project/storage/logs/scheduler.log 2>&1
```

Environment defaults: `LEAD_REMINDERS_ENABLED=true`, `LEAD_REMINDER_TIME=09:00`, `LEAD_UPCOMING_DAYS=7`. Saved settings take precedence.

## WhatsApp readiness

Save phone number ID, access token, app secret, and verify token in Settings. Configure the application's public HTTPS `/webhooks/whatsapp` callback in Meta and subscribe to messages/statuses. Localhost is not a public callback. The WABA ID and approved company-alert template belong to the separate company-alert feature.

The lead composer sends authorized text replies during the last inbound message's 24-hour service window, following [WhatsApp's Business Messaging Policy](https://whatsappbusiness.com/policy/). Outside that window, customer-template sending is still required and is not part of this text-only composer. Media remains excluded. A company-alert template is never reused to contact a customer.

Incoming text, outgoing replies, and automatic acknowledgements are recorded. Sent means API acceptance; Delivered/Read require signed receipts. Early receipts are retained, duplicate receipts are ignored, and Read cannot regress to Sent/Failed. Unknown connection results remain Delivery unconfirmed. They block another send for ten minutes to avoid accidental duplication; confirmed rejected replies can be explicitly retried. Closed cases require reopening.

## Interface contracts

All interfaces use active-account policies and normal session/CSRF protection. Admin oversight never grants permission to mark another person's notices read.

| Interface | Behavior |
|---|---|
| `GET /notifications?view=inbox` | Own inbox, 20 per page |
| `GET /notifications?view=followups&user_id=...` | Current follow-ups; another owner requires admin |
| `GET /notifications?view=deliveries` | Global email ledger; admin only |
| `GET /notifications/feed` | Unread count and latest eight items: id/title/message/read/created_at/open_url; private/no-store |
| `POST /notifications/{notification}/open` | Mark own accessible notice read and redirect; other owners' IDs return 404 |
| `POST /notifications/read-all` | Mark own accessible notices read |
| `POST /notifications/deliveries/{delivery}/retry` | Retry today's failed/unconfigured summary; admin only |
| `GET /notifications/email-preview` | HTML template preview; admin only |
| `POST /notifications/test-email` | Throttled test to the current admin's email only |
| `GET /leads/{lead}/previous-inquiries?page=1` | Ten earlier cases per page for the same identity/department/person; data/total/page/has_more |
| `GET /leads/{lead}/whatsapp?page=1` | Thirty messages per page, newest first; data/page/has_more/window/configured/closed/can_reply/version |
| `POST /leads/{lead}/whatsapp` | message (1–4096), UUID client_reference, case version; repeated identical reference reuses the recorded result |

Reply results: 200 accepted/confirmed, 202 unconfirmed, 502 rejected, 422 window/configuration/validation, 409 stale/conflicting reference. No automatic resend. Case access applies to message/history routes.

## Transport verification

The regular suite uses isolated databases and fake outbound services. Three integration tests are opt-in.

```powershell
python -B scripts/smtp-test-server.py --port 2526 --output 'D:\Code\5. Projects\TasmiyaEnterprises\storage\app\local-runtime\smtp-success'
# In another terminal:
$env:LEAD_SMTP_SIMULATION_PORT='2526'
php artisan test --filter LeadSmtpSimulationTest
```

This loopback-only mailbox captures MIME emails and never forwards them. Repeat on another port with `--reject`, setting `LEAD_SMTP_EXPECT=failed`. Only reserved example.test addresses and the isolated test database are used.

For real HTTP replies, use the existing Postman simulation with `WHATSAPP_SIMULATION_URL=http://127.0.0.1:4900/whatsapp/v23.0` and run `LeadWhatsAppReplySimulationTest`. `WHATSAPP_REPLY_EXPECT` is sent, failed for 503, or unknown for timeout/unreachable scenarios. See the main module guide for simulation startup.

## Data

The additive migration stores notices, summary delivery records, WhatsApp messages, and signed receipts. Existing identifiable incoming WhatsApp records are imported without contacting providers. The local database was backed up on D: before migration. Rollback refuses to discard accumulated history; export new data and restore an explicit backup if needed. No source-project customers are imported.

# Lead management

This module ports the lead screen from the personal `crm-itlife` project into Tasmiya's Laravel 12 application. The source's calendar, table, contact forms, duplicate dialog, and activity timeline are retained. A subsequent requested theme refinement uses Tasmiya's purple/navy palette, DM Sans/Outfit workspace typography, rounded controls and panels, and softer semantic status colors. These overrides live in `public/vendor/leads/tasmiya-theme.css`, scoped to the lead layout; the source assets and workflow remain intact. ERP accounting and customer logins are deliberately not imported. Logging out returns to the public homepage and ends dashboard access.

## Daily flow

Contact Us requires a department. An active specialist with a visible profile is assigned automatically when the department has one eligible person; otherwise the visitor chooses the specialist. Each website submission creates a separate **New** case. The original submission remains attached even if staff edit contact details later.

Active team members manage their assigned cases. Admins and super admins see all cases, filter by staff, and allocate unassigned cases. The dashboard links to new, overdue, and (admins only) unassigned/unavailable-owner views. Inactive owners are preserved in history and excluded from new public routing.

The source statuses are **New**, **Contacted**, **No Response**, **Interested**, **Converted**, and **Lost**. Call, Meeting, WhatsApp, Email, and Note record interactions; those selections do not send messages. Follow-ups use Asia/Karachi local dates/times, including all-day events; audit timestamps are stored in UTC. There can be only one open follow-up per case. Activities complete the previous follow-up; the explicit reschedule checkbox cancels it and requires a replacement date.

Convert to Customer creates or reuses the case's internally linked CRM contact/customer record and closes its follow-ups. It creates no login, invoice, accounting code, or product permissions. Lost also closes follow-ups. Both closed statuses require **Reopen Case** with a reason before further activities; reopening returns to New without undoing the customer record or restoring cancelled follow-ups.

Repeat website cases are not combined. The same normalized contact identity, department, and assigned person produces a **Returning Inquiry** chip, or **Returning Customer** when an earlier case in that scope has conversion history. Different departments/recipients start as New Inquiry. These chips are separate from the six statuses. The detail screen offers paginated earlier cases and links to their complete inquiries and activities, subject to current access rules. A shared phone with a different email is not automatically merged. Contact edits affect that case, not another specialist's case or the original inquiry. Manual creation retains duplicate reuse/create-separate choices; closed cases require reopening before reuse.

## Architecture and interface contract

`lead_contacts` holds identity/customer facts; `leads` holds case snapshots, status, department, owner, and an optimistic version; `lead_details` holds append-only activities and follow-up completion/cancellation. `contact_inquiries` retains the original message, source, original department, delivery results, and case link. Existing inquiry status is a compatibility projection, never an independent workflow.

`LeadWorkflow` performs transactional creation, updates, assignment, scheduling, conversion, and reopening. Conditional version updates reject competing edits with HTTP **409** and a reload action. Ownership is checked again inside the write transaction. Source ERP voucher/menu permissions are replaced with Tasmiya policies.

| Interface | Contract |
|---|---|
| `GET /contact` | Public form and eligible department/person choices |
| `POST /contact` | Existing fields plus required `division_id`, `assigned_to`; server fills the sole eligible recipient. Success redirects with the existing confirmation. CSRF and existing throttling remain. |
| `GET /leads` | Authenticated source-style workspace; optional `lead`, `status`, `scope`, `overdue` deep links |
| `GET /leads/data` | DataTables `draw`, `start`, `length` (maximum 100), `search[value]`, name/phone ordering; JSON `draw`, `recordsTotal`, `recordsFiltered`, `data` |
| `GET /leads/events` | Required date `start`, exclusive `end`; open follow-ups only, timed events include `+05:00`, date-only events are all-day |
| `GET /leads/duplicates?phone=...` | Only accessible matching cases, including current versions; no cross-owner contact disclosure |
| `POST /leads` | `party_name`, international `phone`, optional `party_email`, `address`, `remarks`, required `follow_up_date`, optional time/assignee. Duplicate warning: **409** with `duplicates`; retry with `force_duplicate=1` or `reuse_party_id` plus `version`. |
| `GET /leads/{lead}` | `party`, `version`, `current_status`, assignment, `closed`, source, `open_follow_up`, `history`, and immutable `inquiries` snapshots |
| `PATCH /leads/{lead}` | Contact fields plus required `version`; changes only this case's editable contact snapshot |
| `POST /leads/{lead}/follow-ups` | Required `version`, `lead_status` (not Converted), `activity_type`; optional remarks/date/time/assignee/`reschedule_only`. Only admins may change the owner. |
| `POST /leads/{lead}/convert` | Required `version`, optional `remarks`; creates/reuses CRM customer, records Conversion, cancels open follow-ups |
| `POST /leads/{lead}/reopen` | Required `version`, `reason` (3–2000 characters); closed cases only |

The source UI's `party`/`party_id`/`reuse_party_id` JSON names identify **Tasmiya lead cases**, not ERP parties. There is no dependency on the source database. Mutations return a message and current version. Validation returns **422**, insufficient access **403**, missing records **404**, stale writes **409**; guests must sign in. International numbers are canonicalized without truncation; local Pakistani `03...`, `3...`, `0092...`, and `+92...` formats match.

### WhatsApp

Existing GET/POST `/webhooks/whatsapp` verification and callback URLs are retained. Signed text notifications use Meta's [Cloud API message notification structure](https://www.postman.com/meta/whatsapp-business-platform/documentation/wlk6lh4/whatsapp-cloud-api). All entries/changes are processed. Provider message IDs have a unique constraint.

Incoming text appends to the latest non-Converted/non-Lost **WhatsApp** case for that phone, retaining owner, status, and scheduled follow-up. No ongoing WhatsApp case means a New unassigned case. Website cases are never absorbed into WhatsApp conversations. Unsupported media and delivery-status callbacks do not create cases. Each new conversation gets one best-effort acknowledgement; retries do not repeat it. Persistence errors return **500** for retry; invalid signatures **403**, malformed text **422**. Authorized staff can reply through the text composer; signed receipts update message delivery history. Attachments and campaigns remain excluded.

Existing configured company inquiry notifications remain after database persistence. Delivery failures are recorded and do not discard the inquiry. `WHATSAPP_API_BASE_URL` defaults to the existing Graph v23.0 URL and permits **local simulation only** during development; keep the default in deployed environments.

## Installation, backfill, and rollback

Use the already configured PHP/runtime and package cache directories on **D:**. Before updating an existing installation, back up the database on D:, then:

```powershell
php artisan migrate
php artisan leads:backfill
npm run build
```

Backfill is repeat-safe. It preserves message content and valid assignments, sends unassigned/inactive-owner cases to admin allocation, and records an import note. Legacy `new` maps to New; `contacted`/`in_progress` to Contacted; `resolved`/`closed` to Lost. Resolution alone never manufactures a customer. The current local database was backed up before migration; no reset or business-data reseeding is required.

Reverting application code does not safely undo accumulated CRM data. The migration refuses destructive rollback when cases exist. Export/back up newer CRM data and restore the pre-CRM database explicitly if a full data rollback is needed. Do not use `migrate:fresh` on an installation containing business data.

Bundled lead assets load only on the lead layout. The public site keeps its existing Vite/Tailwind bundle and shared navigation. Third-party attribution is in `public/vendor/leads/NOTICE.md`.

## Verification

```powershell
php artisan test
npm run build
```

The regular suite uses an isolated SQLite database and fake outbound services. It covers public routing, ownership, duplicates, contact snapshots, legacy import, scheduling, Karachi date boundaries, reassignment, stale/competing edits, conversion/reopen, webhook batching/grouping/idempotency, invalid signatures, persistence failures, and failed notifications.

For real HTTP through the WhatsApp client without contacting customers, start a local Postman simulation in another terminal:

```powershell
postman simulation run postman/simulations/whatsapp.sim.yaml --port 4900 --no-history
$env:WHATSAPP_SIMULATION_URL='http://127.0.0.1:4900/whatsapp/v23.0'
$env:WHATSAPP_SIMULATION_EXPECT='sent'
php artisan test --filter LeadWhatsAppSimulationTest
```

Repeat with `whatsapp-error.sim.yaml` (503) and `whatsapp-timeout.sim.yaml` (9-second delay), setting expected status `failed`. An unused loopback port tests unreachable upstreams. The application's timeout is 8 seconds. These tests use fake provider credentials and an isolated test database. Simulation logs prove the real client request reached the dependency. The normal suite skips this one opt-in test unless its URL is set.

The source's eight focused tests passed. Its live local module cannot pass the legacy menu middleware because a required rights row is missing. Visual reference checks therefore use its **unmodified module template** in an isolated, read-only fixture with matching data and the approved Tasmiya shell, rather than modifying that source installation. Check desktop/mobile calendar, table, Add Lead, detail/timeline, duplicate, conversion/reopen, and keyboard/validation states.

Live Meta/SMTP delivery requires real credentials and a reachable public callback; local tests do not establish provider configuration or actual customer delivery.

## Returning inquiries, website notifications, and communication

Private earlier-case lookup, the notification bell/inbox, daily assigned-person email summaries, the WhatsApp text composer, and persisted delivery history are documented in [Lead notifications and email setup](LEAD_NOTIFICATIONS.md), including scheduling, retries, configuration, interface contracts, and local transport tests.

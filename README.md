# Tasmiya Enterprises

A Laravel website and team workspace for Tasmiya Enterprises' taxation, IT and digital, and technical-support divisions. Visitors can explore services and specialists, while signed-in team members manage their own profiles and service cards.

## What is included

- Responsive Home, Services, Team, About, FAQ, Contact, Privacy Policy, and Terms of Service pages, with optimized WebP imagery and a shared visual theme.
- Team profiles with editable presentation details and images, plus profile-owned service cards. Each specialist can add or remove cards and set their own title, description, tags, icon, currency, and starting price without changing another specialist's listing.
- An authenticated dashboard and per-card admin settings for company contact details, social links, inquiry email, cloud image pickers, WhatsApp notifications, and payment destinations.
- Manual bank, Raast, Easypaisa, and JazzCash payment instructions with customer references/proof and admin review. Stripe checkout is optional and requires separate credentials. Manual transfer fields do **not** activate provider API payments.
- Optional Google Drive and OneDrive image import. Both require credentials and provider-side configuration; local browser testing is supported.

## Stack

PHP 8.2+, Laravel 12, Blade, SQLite by default (or another Laravel-supported database), Vite, and JavaScript/CSS. Stripe's PHP SDK is included for the optional card flow.

## Run locally

Install PHP/Composer and Node.js/npm, then from the project root:

```powershell
composer install
npm ci
Copy-Item .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link
```

The example environment uses SQLite. Create `database/database.sqlite` if Laravel asks for it, or configure another database in `.env`. Set `APP_URL=http://localhost:8000` when using the local server.

For sample divisions, team profiles, and services **in a disposable local database only**, run `php artisan db:seed`. The seeders contain demonstration accounts and must not be used as production credentials.

Start the PHP server and Vite in separate terminals:

```powershell
php artisan serve --host=localhost --port=8000
npm run dev
```

Open `http://localhost:8000`. For a compiled frontend instead of the Vite development server, run `npm run build`.

## Optional integrations

Admins configure integrations at `/settings`; leave unused services unconfigured. Google Drive Picker needs a Google OAuth web client, browser API key, Cloud project number, and enabled Picker and Drive APIs. For local testing, add `http://localhost:8000` as an Authorized JavaScript origin and use that same hostname in the browser. OneDrive needs a Microsoft Entra SPA application and its redirect URI. SMTP, Meta WhatsApp, and Stripe each need their own provider credentials and setup. Keep secrets in `.env` or the protected settings fields, never in Git.

## Check changes

```powershell
php artisan test
npm run build
```

Further architecture and implementation notes are in [`docs/`](docs/DOCUMENTATION_INDEX.md). This repository is a personal project mirror, separate from company-owned XEPOS source code.

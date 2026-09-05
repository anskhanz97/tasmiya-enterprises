# Tasmiya Enterprises

Laravel-based company website and profile management system for a multi-division business.

## What it demonstrates

- Laravel application structure with Blade views, routing, controllers, models, and migrations
- Authentication and role-based profile management
- Admin-controlled team member profiles and content
- Modular layouts and distinct themed sections for business divisions
- Responsive presentation layer with project-owned assets and documentation

## Stack

- PHP and Laravel
- Blade templates
- MySQL-compatible persistence
- Vite and frontend asset tooling

## Local setup

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
```

Configure the database and application settings in `.env` before running migrations. Never commit `.env` or production credentials.

## Project notes

Additional product and implementation notes are available in `docs/`. This repository is a personal project mirror and is kept separate from company-owned XEPOS source code.

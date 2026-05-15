# ChrizFasa EMS

ChrizFasa EMS is a Laravel 11 based education management platform for KG, Primary, and Secondary schools, with support for single-school branding and multi-school administration workflows.

This project includes:
- Academic operations (sessions, terms, classes, subjects, attendance, timetables)
- Admissions and enrollment flow
- Result management (submission, approval, report cards, imports)
- Finance (fees, invoices, payment workflows, receipts, webhooks)
- Student and parent portals
- Public website/CMS blocks (hero slides, testimonials, themed pages)
- Role-based access and audit logging

## Table of Contents

1. [Tech Stack](#tech-stack)
2. [Architecture Overview](#architecture-overview)
3. [Feature Modules](#feature-modules)
4. [Project Structure](#project-structure)
5. [Requirements](#requirements)
6. [Local Setup](#local-setup)
7. [Environment Configuration](#environment-configuration)
8. [Seed Data and Default Accounts](#seed-data-and-default-accounts)
9. [Running the Application](#running-the-application)
10. [Route Entry Points](#route-entry-points)
11. [API (Sanctum, v1)](#api-sanctum-v1)
12. [Payments and Webhooks](#payments-and-webhooks)
13. [Scheduling and Background Processing](#scheduling-and-background-processing)
14. [Storage and File Handling](#storage-and-file-handling)
15. [Testing and Quality](#testing-and-quality)
16. [Production Notes](#production-notes)
17. [Known Caveats](#known-caveats)
18. [Troubleshooting](#troubleshooting)
19. [License](#license)

## Tech Stack

- PHP: `^8.2`
- Framework: `laravel/framework ^11.0`
- Frontend build: Vite 5 + Tailwind CSS 3
- Auth/API tokens: Laravel Sanctum
- RBAC: Spatie Permission
- Audit logs: Spatie Activitylog
- Media management: Spatie Medialibrary
- Backup tooling: Spatie Laravel Backup
- Multi-tenancy package: Spatie Laravel Multitenancy (plus project-level `SchoolContext`)
- PDF generation: Barryvdh DomPDF
- Excel import/export: Maatwebsite Excel
- Payments: Paystack + Flutterwave
- SMS: Termii + Twilio
- Queue support: Laravel Horizon (when using Redis queue)

## Architecture Overview

- Core app type: Monolithic Laravel web app with API endpoints.
- Auth guards:
  - `web` for admin/staff area
  - `portal` for student/parent area
- Domain model: school-scoped data with role-based access checks and explicit school context resolution.
- Config style: this repo intentionally keeps a minimal `config/` surface (`app.php`, `auth.php`, `brand.php`, `ems.php`, `mail.php`, `services.php`).
- Security middleware includes:
  - custom security headers
  - proxy trust control via `TRUSTED_PROXIES`
  - forced HTTPS behavior via `APP_FORCE_HTTPS`

## Feature Modules

- Academic:
  - Sessions/terms
  - Classes/arms
  - Subjects + teaching assignments
  - Student records + promotion
  - Attendance + attendance sheet import
  - Timetable
  - Assignments and submissions
- Admissions:
  - Public online application (`/apply`)
  - Admin review and enrollment into active student accounts
- Examinations/Results:
  - Result submissions
  - Template download/import
  - Review/approval workflows
  - Result sheets and report cards
  - Feedback loop with students/parents
  - Optional AI teacher comment generation (`OPENAI_*`)
- Financial:
  - Fee structures
  - Invoice generation and printing
  - Offline and online payment lifecycle
  - Receipt generation
  - Bank account and payment method management
  - Webhook verification (Paystack/Flutterwave)
- Communication:
  - Announcements
  - Internal messaging
  - SMS dispatch service (Termii/Twilio)
- Supporting modules:
  - Library
  - Transport
  - Hostel
  - Health records
  - Inventory/assets
- System/CMS:
  - Public page settings
  - Hero slides
  - Testimonials moderation
  - Theme/public navigation customization
- Multi-school admin:
  - Super-admin onboarding and domain management endpoints

## Project Structure

```text
app/
  Enums/                  # Role, term, grade level, payment status enums
  Http/
    Controllers/          # 48 controllers across domain modules
    Middleware/           # Role checks, portal guard, security headers, proxies
  Models/                 # 59 Eloquent models
  Notifications/          # Payment, assignment, announcement notifications
  Services/               # Domain and integration services
  Support/                # SchoolContext, DomainHelper, content/theme helpers
  Traits/                 # School scoping and audit trail traits

bootstrap/
  app.php                 # Laravel 11 bootstrap + middleware + routing wiring

config/
  app.php
  auth.php
  brand.php
  ems.php
  mail.php
  services.php

database/
  migrations/             # 42 migration files
  seeders/                # School + role + academic + subject + grading bootstrap

resources/
  views/                  # Admin, portal, public, and PDF blade templates
  css/app.css             # Tailwind + theme variables
  js/app.js               # Vite entrypoint

routes/
  web.php                 # Main web + portal + webhook routes
  api.php                 # API v1 routes (Sanctum)
  console.php             # Scheduler definitions
```

## Requirements

- PHP 8.2+
- Composer 2+
- Node.js 18+ and npm
- MySQL/MariaDB
- Redis (optional but recommended for async queue/Horizon)

## Local Setup

1. Install dependencies:

```bash
composer install
npm install
```

2. Prepare environment:

```bash
cp .env.example .env
php artisan key:generate
```

3. Configure DB credentials in `.env`.

4. Run migrations and seeders:

```bash
php artisan migrate --seed
```

5. Link storage:

```bash
php artisan storage:link
```

6. Start app + assets:

```bash
php artisan serve
npm run dev
```

## Environment Configuration

### Core

Set these for all environments:

- `APP_NAME`
- `APP_ENV`
- `APP_KEY`
- `APP_DEBUG`
- `APP_URL`
- `APP_FORCE_HTTPS`
- `TRUSTED_PROXIES`
- `DB_*`
- `CACHE_STORE`
- `QUEUE_CONNECTION`
- `SESSION_*`
- `MAIL_*`

### School/Brand Context

- `SINGLE_SCHOOL_MODE` (default true)
- `DEFAULT_SCHOOL_ID` (optional)

### Payments

- Paystack:
  - `PAYSTACK_PUBLIC_KEY`
  - `PAYSTACK_SECRET_KEY`
  - `PAYSTACK_PAYMENT_URL`
  - `PAYSTACK_MERCHANT_EMAIL`
- Flutterwave:
  - `FLUTTERWAVE_PUBLIC_KEY`
  - `FLUTTERWAVE_SECRET_KEY`
  - `FLUTTERWAVE_SECRET_HASH`
  - `FLUTTERWAVE_ENCRYPTION_KEY`
  - `FLUTTERWAVE_BASE_URL`
- TLS options (optional):
  - `PAYMENT_GATEWAY_SSL_VERIFY`
  - `PAYMENT_GATEWAY_CA_BUNDLE`

### Messaging

- Termii:
  - `SMS_DRIVER=termii`
  - `TERMII_API_KEY`
  - `TERMII_SENDER_ID`
  - `TERMII_BASE_URL`
- Twilio:
  - `SMS_DRIVER=twilio`
  - `TWILIO_SID`
  - `TWILIO_AUTH_TOKEN`
  - `TWILIO_FROM`

### AI Comment Generation (optional)

- `OPENAI_API_KEY`
- `OPENAI_MODEL` (default `gpt-4o-mini`)
- `OPENAI_BASE_URL`
- `OPENAI_TIMEOUT`

### Seeding Safety

- `SEED_DEFAULT_PASSWORD`
  - Required when seeding outside `local`/`testing`
  - In `local`/`testing`, defaults to `password`

## Seed Data and Default Accounts

`DatabaseSeeder` runs:
- `RolePermissionSeeder`
- `SchoolSeeder`
- `AcademicStructureSeeder`
- `SubjectSeeder`
- `ExamTypeSeeder`
- `GradingScaleSeeder`

Default seeded users:
- `admin@chrizfasa.ng` (role: `school_admin`)
- `principal@chrizfasa.ng` (role: `principal`)

Password:
- Uses `SEED_DEFAULT_PASSWORD` if provided
- Otherwise `password` in local/testing

Note: seeded users are created with `must_change_password=true`.

## Running the Application

Development:

```bash
php artisan serve
npm run dev
```

Build assets for deployment:

```bash
npm run build
```

Useful maintenance commands:

```bash
php artisan optimize:clear
php artisan optimize
php artisan config:clear
php artisan route:list
php artisan about
```

## Route Entry Points

Public:
- `/` home
- `/contact`
- `/privacy-policy`
- `/cookies-policy`
- `/apply` (online admission)

Authentication:
- `/admin-access` (admin/staff web guard login)
- `/staff` (staff login view)
- `/portal` (student/parent portal login)

Protected areas:
- `/dashboard` admin/staff dashboard
- `/student/dashboard` student portal
- `/parent/dashboard` parent portal

## API (Sanctum, v1)

Base prefix: `/api/v1`

Public endpoint:
- `POST /api/v1/login`

Authenticated examples:
- `GET /api/v1/user`
- `GET /api/v1/students`
- `GET /api/v1/students/{student}`
- `GET /api/v1/students/{student}/results`
- `GET /api/v1/students/{student}/attendance`
- `GET /api/v1/students/{student}/invoices`
- `GET /api/v1/timetable/{classId}`
- `GET /api/v1/announcements`
- `POST /api/v1/logout`

Auth details:
- Uses Sanctum personal access tokens
- Login creates/replaces a `mobile-app` token
- Ability set depends on role (`portal:self` vs `school:read`)

## Payments and Webhooks

Web routes:
- `POST /webhooks/paystack`
- `POST /webhooks/flutterwave`

API route:
- `POST /api/v1/webhooks/paystack`

Security behavior:
- Paystack: validates `x-paystack-signature` using HMAC SHA512
- Flutterwave: validates `verif-hash`
- Both perform server-side transaction verification before approval
- Both enforce amount/reference checks

## Scheduling and Background Processing

Configured schedules are in `routes/console.php`:
- `ems:attendance-report` daily 18:00
- `ems:apply-late-fees` daily 00:00
- `backup:run` daily 02:00
- `backup:clean` daily 03:00
- `ems:check-subscriptions` daily 08:00

Cron entry example:

```cron
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

Queue mode:
- `QUEUE_CONNECTION=sync`: no worker required
- `QUEUE_CONNECTION=redis`: run worker/Horizon

```bash
php artisan horizon
```

## Storage and File Handling

Uses storage for:
- Admission uploads
- Payment proofs
- Result import payloads/raw files
- Rich-text/media assets

Required for public asset access:

```bash
php artisan storage:link
```

Project also includes explicit media fallback routes in `routes/web.php` for public disk file delivery.

## Testing and Quality

Run tests:

```bash
php artisan test
```

Current suite status:
- Unit: basic sanity test exists
- Feature: placeholder directory present

Formatting (optional):

```bash
./vendor/bin/pint
```

## Production Notes

See [`PRODUCTION_RUNBOOK.md`](PRODUCTION_RUNBOOK.md) for deployment and hardening steps.

Typical deploy flow:

```bash
git pull origin <branch>
composer install --no-dev --prefer-dist --optimize-autoloader
php artisan migrate --force
php artisan storage:link
php artisan optimize:clear
php artisan optimize
npm ci
npm run build
```

Recommended production baseline:
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_FORCE_HTTPS=true`
- secure session cookie settings enabled
- trusted proxy list configured

## Known Caveats

- `.env.example` currently contains legacy Flutterwave keys (`RAVE_*`), while runtime config expects `FLUTTERWAVE_*` keys from `config/services.php`.
- Scheduled `ems:*` commands are referenced in `routes/console.php`, but the current command namespace is not registered in this snapshot (`php artisan ems:...` returns namespace missing). Add/register these commands if you plan to run those schedules.
- Test coverage is minimal and should be expanded before high-risk releases.

## Troubleshooting

- Login loop or wrong login screen:
  - Check guards and intended entry route (`/admin-access`, `/staff`, `/portal`)
  - Clear caches: `php artisan optimize:clear`
- 419/session mismatch around logout:
  - The bootstrap exception handler contains custom stale-token logout handling; clear cookies/session and retry.
- Payment callback failures:
  - Re-check webhook secret/signature keys and gateway environment URLs
  - Confirm callback domain matches `APP_URL` and TLS/proxy settings
- Media not loading:
  - Ensure `php artisan storage:link` ran successfully
  - Verify file exists in `storage/app/public`

## License

Proprietary.

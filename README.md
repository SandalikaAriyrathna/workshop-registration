# Workshop Registration Service

A Laravel 12 application for a community training centre. Admins create staff accounts; Managers schedule workshops; Managers and Staff register or cancel attendees and inspect history. Public signup is disabled.

## Requirements

- PHP 8.2 or newer, Composer 2, and PHP extensions required by Laravel (including PDO SQLite for the default setup).
- Node.js 20.19+ or 22.12+ and npm for building frontend assets.
- SQLite is the default database. MySQL with InnoDB is also supported by the application's transaction strategy; concurrency tests currently cover SQLite.

## Local setup

From the repository root:

```sh
composer install
php -r "file_exists('.env') || copy('.env.example', '.env');"
php artisan key:generate
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

Open http://127.0.0.1:8000 and choose Log in. The backend serves the Blade frontend, so only the Laravel server is needed after building assets. For frontend development, run `npm run dev` in a second terminal while `php artisan serve` runs.

The default `.env.example` uses `DB_CONNECTION=sqlite`. Leave `DB_DATABASE` unset to use `database/database.sqlite`. Set `APP_URL=http://127.0.0.1:8000` locally. Datetimes use Laravel's configured timezone (UTC by default); enter and interpret workshop times in that timezone. Password-reset emails use the log mailer locally and appear in `storage/logs/laravel.log`.

## Demo login and data

Development-only seeded Admin:

- Email: `admin@workshop.com`
- Password: `Admin@12345`

Never use these credentials in production. The Admin dashboard links to User Management. Create a Manager and a Staff account there, then log out and log in with those credentials to access workshops. Admins intentionally cannot view workshops or register attendees, matching the challenge's permission matrix.

The seed command creates three future sample workshops, at three locations. Sample workshops are preserved when seeding again. The Admin seeder resets the development Admin password when rerun; use it only for local/demo setup.

## Try the workflows

1. As Admin, create Manager and Staff accounts.
2. As Manager, browse, add, or edit workshops. Filter by inclusive start/end dates, status, and available seats.
3. As Staff or Manager, open a future scheduled workshop and register an attendee by name and email.
4. Cancel a registration from its history table. The seat becomes available and the cancelled record retains both actors and timestamps.
5. Attempt to register into a full workshop, or reduce capacity below active bookings. The backend refuses the operation.

The available-seats filter means `capacity > active registrations`; combine it with Scheduled and an appropriate date range to find upcoming workshops accepting bookings. Past, completed, or cancelled workshops never accept registrations.

## Verification

```sh
php artisan test
npm run build
```

Tests use an isolated in-memory SQLite database. The concurrency test uses a temporary SQLite file and four independent PHP processes synchronized before booking the last seat. It requires `proc_open` to be enabled. It cleans up its temporary database and barrier files and does not modify the local application database.

Tests cover the permission matrix, disabled public signup, Admin-created accounts, capacity edits, full/closed workshops, cancellation/history, filter combinations, repeatable seeding, and simultaneous bookings.

## Backend and frontend design

Laravel web endpoints supply the server-rendered frontend using session authentication, CSRF protection, backend role middleware, validation, and redirects. This implementation has no separate JSON API or SPA. See [DESIGN.md](DESIGN.md) for rationale, concurrency guarantees, assumptions, and skipped work. If a separately consumable API is required by the reviewer, authenticated JSON endpoints remain an additional deliverable.

Waitlists, a general audit trail, and live deployment are optional and not implemented.

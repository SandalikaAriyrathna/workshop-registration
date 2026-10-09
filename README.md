# Workshop Registration Service

## Requirements

- PHP 8.2 or newer with Laravel's required extensions, including `pdo_mysql`.
- Composer 2.
- Node.js 20.19+ or 22.12+ and npm.
- MySQL with InnoDB (for example, the database server included with XAMPP).

## Local setup

Run all commands from the repository root. Start your MySQL server before continuing.

### 1. Install dependencies and prepare the environment

```sh
composer install
npm install
php -r "file_exists('.env') || copy('.env.example', '.env');"
php artisan key:generate
```

### 2. Create the database

In phpMyAdmin, create a database named `workshop-registration`, or run this SQL in your MySQL client:

```sql
CREATE DATABASE IF NOT EXISTS `workshop-registration`
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Update these settings in `.env` to use MySQL rather than the SQLite default in `.env.example`:

```dotenv
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=workshop-registration
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
MAIL_MAILER=log
```

Use your own MySQL username and password if they differ from the local XAMPP defaults above. Keep the `APP_KEY` generated in step 1.

### 3. Create tables and seed demo data

```sh
php artisan config:clear
php artisan migrate --seed
```

This creates the application tables, Admin, Manager, and Staff accounts, and three sample workshops. For an existing installation, run `php artisan db:seed` to add the demo accounts.

### 4. Run the backend and frontend

In your first terminal:

```sh
php artisan serve
```

In a second terminal, from the same repository root:

```sh
npm run dev
```

Keep both terminals running. Open http://127.0.0.1:8000 in your browser; the Laravel server serves the application, and Vite supplies the frontend assets.

Alternatively, run `npm run build` to build the frontend once, then run only `php artisan serve` to use the application without the Vite development server.

## Seeded login accounts

| Role | Email | Password |
|---|---|---|
| Admin | `admin@workshop.com` | `Admin@12345` |
| Manager | `manager@workshop.com` | `Manager@12345` |
| Staff | `staff@workshop.com` | `Staff@12345` |

These credentials are for local development only. Running the seed command again resets the passwords and roles of these demo accounts.

Log in as Manager or Staff to access the sample workshops straight away. Log in as Admin and open **User Management** to create additional Manager or Staff accounts. Admins manage accounts; Managers and Staff access workshops. Public signup is disabled.

## Sample workshops

The seed command provides:

| Code | Workshop | Instructor | Capacity | Location |
|---|---|---|---|---|
| DEMO-1 | Pottery Basics | Alex Silva | 12 | Training Centre 1 |
| DEMO-2 | Introduction to Coding | Sam Perera | 20 | Training Centre 2 |
| DEMO-3 | Community Fitness | Jamie Fernando | 15 | Training Centre 3 |

On first seeding, these workshops are Scheduled for 10:00 AM, two, three, and four days after the seed command runs, respectively. Times use the application timezone, UTC by default. Re-running the seeder preserves existing sample workshops; a Manager can edit their dates if needed.

As Manager or Staff, open **Workshops**, select a sample workshop, and register an attendee using a name and email. Cancel the registration from its history table to see the seat become available while the original record remains visible. Managers can also create and edit workshops.

# ZoneNext

A Laravel 11 app with two parts:

- **API** — `/api` (Sanctum token auth)
- **Admin dashboard** — `/admin` (Filament 3)

## Requirements

PHP 8.2+, Composer, Node.js 18+, and MySQL 8 or SQLite.

## Setup

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate
# set DB_* in .env

php artisan migrate --seed
php artisan storage:link
npm run build
```

## Run

```bash
php artisan serve
php artisan queue:work   # in a second terminal, for notifications and jobs
```

- API: http://localhost:8000/api
- Admin: http://localhost:8000/admin

Seeded admin login: `naungyehtet.zonenextadmin@gmail.com` / `admin@123`

## Docker (Sail)

```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate --seed
```

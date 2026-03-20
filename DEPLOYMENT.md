# LearnFlow Deployment Guide

This guide covers deploying LearnFlow LMS for production and preparing for first-time installation by end users.

## Prerequisites

- **PHP 8.2+** with extensions: `pdo`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `openssl`
- **Database**: MySQL 5.7+, MariaDB 10.3+, PostgreSQL 10+, or SQLite
- **Node.js 18+** and npm (for building assets)
- **Composer** 2.x

## Pre-Deployment Checklist

### 1. Build frontend assets

```bash
npm ci
npm run build
```

### 2. Install dependencies (production)

```bash
composer install --optimize-autoloader --no-dev
```

### 3. Ensure writable directories

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache  # Adjust user/group for your server
```

### 4. Configure web server

#### Apache (.htaccess)

The project includes `.htaccess` files. Ensure `mod_rewrite` is enabled.

**Root deployment** (e.g. `https://example.com`):
- Document root: `public/` folder (recommended) or project root with RewriteBase configured
- Set `APP_URL=https://example.com` in `.env`

**Subdirectory deployment** (e.g. `https://example.com/learnflow`):
- Update `RewriteBase` in `.htaccess` to `/learnflow/`
- Set `APP_URL=https://example.com/learnflow` in `.env`

#### Nginx

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### 5. Security

- Set `APP_DEBUG=false` and `APP_ENV=production`
- Use a strong `APP_KEY` (generated during install)
- For HTTPS sites, set `SESSION_SECURE_COOKIE=true` in `.env`
- Do not expose `.env`, `storage/`, or `vendor/` publicly
- Ensure the document root points to `public/` when possible

## First-Time Installation (Web Installer)

1. Upload the application files to your server
2. Ensure `storage/` and `bootstrap/cache/` are writable
3. Visit `https://your-domain.com/install` (or `https://your-domain.com/learnflow/install` for subdirectory)
4. Follow the wizard:
   - **Requirements**: Verifies PHP version and extensions
   - **Database**: Configure SQLite (file) or MySQL/PostgreSQL
   - **Application**: Set app name, URL, and create admin account
   - **Install**: Run migrations and complete setup
5. After installation, the admin can log in and configure settings under Admin → Settings

## Post-Install Configuration

- **Admin → Settings → General**: Site name, currency, timezone
- **Admin → Settings → Email**: SMTP for transactional emails
- **Admin → Settings → Payment**: Stripe, PayPal, Paystack, etc.
- **Environment variables**: Update `.env` for mail, payments, OAuth (Google/GitHub) as needed

## Optional: Redis (for scaling)

For better performance on production, switch to Redis:

```env
SESSION_DRIVER=redis
CACHE_STORE=redis
QUEUE_CONNECTION=redis
```

Install Redis and the `phpredis` extension, then update Redis connection in `.env`.

## Optional: Queue worker

If using `QUEUE_CONNECTION=redis` or `database`:

```bash
php artisan queue:work --daemon
```

Or use Laravel Horizon (included) for Redis queues.

## Reinstalling

To run the installer again (e.g. for testing), delete:
```
storage/framework/installed
```

## Troubleshooting

- **500 error**: Check `storage/logs/laravel.log`; ensure storage and bootstrap/cache are writable
- **Routes not found (404)**: Verify `mod_rewrite` (Apache) or equivalent; check `APP_URL` matches your base URL
- **Assets not loading**: Run `npm run build`; ensure `ASSET_URL` matches `APP_URL` if in subdirectory
- **Database connection failed**: Verify DB credentials; for SQLite, ensure the database file path is writable

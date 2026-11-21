# Radix deployment checklist

## Production environment

Set these values in the production secret manager:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://radix.example.com
APP_KEY=base64:GENERATE_UNIQUE_KEY
LOG_LEVEL=warning
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
```

Use a managed MySQL/MariaDB database. Set `DB_*` values. Configure real mail delivery; `MAIL_MAILER=log` is development-only.

## Release

```powershell
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan storage:link
php artisan optimize
```

## Pre-release checks

```powershell
php artisan test
composer audit
npm audit --audit-level=high
php artisan about
```

## Health check

Laravel exposes `/up` for load balancers and uptime monitors. Expect HTTP 200 only when the application boots successfully.

## Backup and rollback

1. Take a database backup before migration.
2. Run migrations during the release window.
3. Verify `/up`, login, dashboard, quiz, SQL Lab, ERD Puzzle.
4. Keep the previous release available for rollback.
5. Restore the backup only after confirming data-loss impact.

Never run `migrate:fresh` in production.

# Production Runbook

## 1) Server Prerequisites

1. Linux server with PHP `8.3+` (matching app runtime).
2. Required PHP extensions:
   - Core app: `curl`, `mbstring`, `openssl`, `pdo_mysql`, `xml`, `zip`, `gd`, `fileinfo`.
   - If running Horizon: `pcntl`, `posix`.
3. MySQL reachable from app host.
4. Redis available if using async queues / Horizon.
5. Web server document root must point to `public/` only.

## 2) Environment Settings

Update `.env` on server before first deploy:

1. `APP_ENV=production`
2. `APP_DEBUG=false`
3. `APP_URL=https://<your-real-domain>`
4. `APP_FORCE_HTTPS=true`
5. `SESSION_SECURE_COOKIE=true`
6. `SESSION_HTTP_ONLY=true`
7. `SESSION_SAME_SITE=lax`
8. `TRUSTED_PROXIES=<comma-separated proxy IPs/CIDRs>` (or `*` only behind a trusted edge)
9. `CACHE_STORE=file` (or `redis` if configured)
10. `QUEUE_CONNECTION=sync` (or `redis` for async workers)
11. Set real credentials for DB, mail, payment gateways, SMS, OpenAI.

## 3) Deployment Commands

Run from project root:

```bash
git pull origin <branch>
composer install --no-dev --prefer-dist --optimize-autoloader
php artisan migrate --force
php artisan storage:link
php artisan optimize:clear
php artisan optimize
```

## 4) Queue/Worker Mode

If keeping `QUEUE_CONNECTION=sync`:

1. No worker process is required.
2. Requests will handle jobs inline.

If using `QUEUE_CONNECTION=redis` (recommended for scale):

1. Ensure Redis is reachable.
2. Start Horizon:

```bash
php artisan horizon
```

3. Run Horizon under `systemd` (recommended) for auto-restart.

## 5) Scheduler

Add cron entry:

```cron
* * * * * cd /var/www/chrizfasaedu && php artisan schedule:run >> /dev/null 2>&1
```

Scheduled tasks currently include:
1. `ems:attendance-report` (18:00 daily)
2. `ems:apply-late-fees` (00:00 daily)
3. `backup:run` (02:00 daily)
4. `backup:clean` (03:00 daily)
5. `ems:check-subscriptions` (08:00 daily)

## 6) Post-Deploy Verification

Run:

```bash
php artisan about --only=environment
php artisan route:list --path=_ignition -v
php artisan route:list --path=webhooks -v
composer audit --locked --abandoned=report
```

Expected:
1. Environment is `production`, debug is `OFF`.
2. No `_ignition` routes.
3. Webhook routes present and throttled.
4. No known security advisories.

## 7) Security Checklist Before Go-Live

1. Confirm no real secrets are committed to git history.
2. Rotate all production secrets at cutover (DB/mail/payments/API keys).
3. Enforce HTTPS at load balancer and app layer.
4. Restrict DB/Redis ports to private network only.
5. Ensure backups run and restore is tested.
6. Monitor `storage/logs/laravel.log` after release.

## 8) Rollback

1. Re-deploy previous stable tag/commit.
2. `php artisan optimize:clear && php artisan optimize`
3. If needed, rollback latest migration:

```bash
php artisan migrate:rollback --step=1 --force
```

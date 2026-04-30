## Switch from SQLite to PostgreSQL (E-kampot Shop)

This document shows the exact steps to switch the project from `sqlite` to `pgsql` (PostgreSQL).

Prerequisites

- PostgreSQL server reachable (local, managed, or Supabase)
- PHP `pdo_pgsql` extension installed
- `composer` and `npm`/`node` available for builds

1. Update `.env`

Change the `DB_CONNECTION` and add the Postgres credentials. Example values:

```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=ekampot_db
DB_USERNAME=ekampot_user
DB_PASSWORD=secret_password
DB_SSLMODE=prefer
```

If you use Supabase, either set the `SUPABASE_*` vars and use `DB_CONNECTION=supabase` or set the `DB_URL` to the provided connection string.

2. Ensure PHP has pdo_pgsql

- Ubuntu/Debian:

```bash
sudo apt-get install php-pgsql
sudo systemctl restart php-fpm    # or restart your PHP service
```

- Windows (XAMPP/WAMP): enable `pdo_pgsql` in `php.ini` and restart Apache

3. Create the database and user (example for local Postgres)

```bash
sudo -u postgres psql
CREATE ROLE ekampot_user WITH LOGIN PASSWORD 'secret_password';
CREATE DATABASE ekampot_db OWNER ekampot_user;
\q
```

4. Clear Laravel caches and refresh config

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

5. Run migrations

If this is a new DB:

```bash
php artisan migrate --force
```

If you have existing data in SQLite and want to migrate it:

- Option A: export/import via `pgloader` (recommended for larger datasets)
- Option B: dump SQLite to SQL, transform types, and import into Postgres

Quick `pgloader` example (install pgloader first):

```bash
pgloader sqlite:///absolute/path/to/database.sqlite postgresql://ekampot_user:secret_password@127.0.0.1/ekampot_db
```

6. Run post-deploy tasks

```bash
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

7. Optional production settings

- Use `QUEUE_CONNECTION=redis` and set `CACHE_DRIVER=redis` for performance
- Use managed Postgres (Supabase, Neon, RDS) for backups and HA
- Use connection pooling (PgBouncer) if your host needs it

Troubleshooting

- If migrations fail with permission errors, check DB user privileges
- If app can't connect, set `DB_SSLMODE=require` or `disable` depending on provider
- Check `storage/logs/laravel.log` for exceptions

Testing the connection

```bash
php artisan migrate:status
php artisan tinker
>>> DB::connection()->getPdo();
```

If you want, I can update the `.env` in your workspace with placeholder Postgres values or create a `database.pgsql.env` template. Provide the DB credentials if you want me to set them and run migrations locally.

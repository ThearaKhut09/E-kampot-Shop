# Supabase Setup for E-Kampot Shop (Laravel)

This guide configures this project to run on Supabase PostgreSQL.

## Quick Toggle (Local Development)

Use these commands from the project root:

```powershell
powershell -NoProfile -ExecutionPolicy Bypass -File scripts/switch-db.ps1 sqlite
powershell -NoProfile -ExecutionPolicy Bypass -File scripts/switch-db.ps1 supabase
```

Or run VS Code tasks:

1. Switch DB: SQLite (Local Fast)
2. Switch DB: Supabase

The script updates `DB_CONNECTION` in `.env` and clears Laravel caches automatically.

## 1. Create Supabase project

1. Go to Supabase and create a new project.
2. Open Project Settings -> Database.
3. Copy your connection details.

## 2. Configure Laravel environment

In your production .env, use PostgreSQL:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=pgsql

# Option A: Full URL (recommended)
DB_URL=postgresql://postgres.<project-ref>:<password>@aws-0-<region>.pooler.supabase.com:6543/postgres?sslmode=require

# Option B: Discrete values (if not using DB_URL)
# DB_HOST=aws-0-<region>.pooler.supabase.com
# DB_PORT=6543
# DB_DATABASE=postgres
# DB_USERNAME=postgres.<project-ref>
# DB_PASSWORD=<password>

DB_SCHEMA=public
DB_SSLMODE=require
```

Notes:

- For app traffic, Supabase pooler endpoint on port 6543 is recommended.
- If your host supports direct DB connections better, you can use direct endpoint/port from Supabase settings.

## 3. Run migrations

```bash
php artisan migrate --force
```

If you also want seed data:

```bash
php artisan db:seed --force
```

## 4. Production optimization

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 5. Verify DB connection

```bash
php artisan tinker
```

Then in tinker:

```php
DB::connection()->getPdo();
```

If no exception appears, connection is working.

## 6. Required PHP extension

Ensure your hosting runtime has PostgreSQL PDO enabled:

- pdo_pgsql

Without this extension, Laravel cannot connect to Supabase.

## 7. Optional: migrate existing SQLite data

If your current app data is in SQLite and you want to move it to Supabase:

1. Set up Supabase env as above.
2. Run migrations on Supabase.
3. Import old data via custom migration script or export/import workflow.

---

Project files already prepared for this:

- config/database.php reads DB_SCHEMA and DB_SSLMODE.
- .env.example includes Supabase examples.

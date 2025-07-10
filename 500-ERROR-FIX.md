# Fixing 500 Server Error - Laravel on Render

## Current Issue
You're getting a 500 server error and Supervisor warnings. This means your deployment is using the original Dockerfile with Supervisor instead of the simpler Apache-based one.

## Quick Fix Options

### Option 1: Use Clean Dockerfile (Recommended)
Update your `render-docker.yaml` to use the cleanest version:

```yaml
services:
  - type: web
    name: e-kampot-shop
    env: docker
    dockerfilePath: ./Dockerfile.clean
    dockerContext: .
    # ... rest of your config
```

### Option 2: Use Minimal Dockerfile
```yaml
dockerfilePath: ./Dockerfile.minimal
```

### Option 3: Fix Supervisor (If you want to keep it)
The Supervisor configuration has been fixed, but you need to redeploy.

## Environment Setup Issues

The 500 error is likely caused by:

1. **Missing .env file** - Fixed in new Dockerfiles
2. **Uncached Laravel configuration** - Fixed with optimization commands
3. **Permission issues** - Fixed with proper chmod commands
4. **Database not initialized** - Fixed with migration commands

## Steps to Fix

### 1. Update Dockerfile Reference
In `render-docker.yaml`, change:
```yaml
dockerfilePath: ./Dockerfile.clean
```

### 2. Ensure Environment Variables
Make sure these are set in Render:
```
APP_NAME=E-Kampot Shop
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app.onrender.com
APP_KEY=base64:your-key-here
DB_CONNECTION=sqlite
LOG_CHANNEL=errorlog
```

### 3. Redeploy
- Push changes to GitHub
- Render will automatically redeploy
- Check build logs for any errors

## Debugging Steps

### 1. Check Build Logs
Look for these successful steps:
- ✅ Composer install completed
- ✅ NPM build completed
- ✅ Laravel optimization completed
- ✅ Database created
- ✅ Permissions set

### 2. Check Runtime Logs
Look for:
- PHP errors
- Apache startup messages
- Laravel configuration issues

### 3. Test Health Check
Your app should respond at:
- `https://your-app.onrender.com/`
- Health check endpoint should return 200

## Common 500 Error Causes

1. **APP_KEY not set** - Fixed by generateValue: true
2. **Database connection issues** - Fixed with SQLite setup
3. **Missing storage directories** - Fixed in Dockerfile
4. **Permission denied errors** - Fixed with proper chmod
5. **Cached configuration conflicts** - Fixed with cache clearing

## File Structure Check

Make sure you have:
```
├── Dockerfile.clean (recommended)
├── Dockerfile.minimal (alternative)
├── docker-entrypoint.sh
├── render-docker.yaml
├── .env.production
└── database/database.sqlite (created automatically)
```

## Expected Success Messages

After fixing, you should see:
```
✅ Laravel deployment completed!
✅ Apache is running
✅ Database migrations completed
✅ Storage link created
✅ Permissions set correctly
```

## Alternative: Non-Docker Approach

If Docker continues to cause issues, switch to native PHP:

```yaml
services:
  - type: web
    name: e-kampot-shop
    env: php
    buildCommand: "./render-build.sh"
    startCommand: "php artisan serve --host=0.0.0.0 --port=$PORT"
    # ... rest of config
```

## Most Likely Solution

Use `Dockerfile.clean` - it's the most straightforward and handles all the Laravel requirements properly without Supervisor complexity.

The key fixes:
- ✅ Uses Apache instead of Nginx+PHP-FPM
- ✅ Handles environment setup automatically
- ✅ Runs Laravel optimization commands
- ✅ Sets proper permissions
- ✅ Creates database automatically
- ✅ Includes proper error handling

# Troubleshooting Docker Build Issues

## The Error You're Getting

The error "failed to solve: process '/bin/sh -c composer install --no-dev --optimize-autoloader' did not complete successfully: exit code 1" typically happens due to:

1. **Memory issues** during composer installation
2. **Permission problems** with files
3. **Missing dependencies** or PHP extensions
4. **Network issues** downloading packages

## Solutions to Try

### Option 1: Use the Minimal Dockerfile (Recommended)

I've created `Dockerfile.minimal` which should work better. Update your `render-docker.yaml`:

```yaml
services:
  - type: web
    name: e-kampot-shop
    env: docker
    dockerfilePath: ./Dockerfile.minimal
    dockerContext: .
    # ... rest of your config
```

### Option 2: Use Simple Dockerfile

If minimal doesn't work, try `Dockerfile.simple`:

```yaml
dockerfilePath: ./Dockerfile.simple
```

### Option 3: Non-Docker Deployment

If Docker continues to fail, use the native approach:

1. **Rename `render.yaml` to `render-blueprint.yaml`**
2. **Create new `render.yaml`**:

```yaml
services:
  - type: web
    name: e-kampot-shop
    env: php
    buildCommand: "./render-build.sh"
    startCommand: "php artisan serve --host=0.0.0.0 --port=$PORT"
    plan: free
    region: oregon
    healthCheckPath: /
    envVars:
      - key: APP_NAME
        value: E-Kampot Shop
      - key: APP_ENV
        value: production
      - key: APP_DEBUG
        value: "false"
      - key: APP_URL
        sync: false
      - key: APP_KEY
        generateValue: true
      - key: LOG_CHANNEL
        value: errorlog
      - key: LOG_LEVEL
        value: info
      - key: DB_CONNECTION
        value: sqlite
      - key: SESSION_DRIVER
        value: file
      - key: QUEUE_CONNECTION
        value: sync
      - key: CACHE_DRIVER
        value: file
      - key: FILESYSTEM_DISK
        value: local
      - key: BROADCAST_DRIVER
        value: log
      - key: MAIL_MAILER
        value: smtp
      - key: VITE_APP_NAME
        value: E-Kampot Shop
```

### Option 4: Manual Render Setup

Instead of using YAML files, manually create the service in Render:

1. **Go to Render Dashboard**
2. **Click "New +" → "Web Service"**
3. **Connect your GitHub repo**
4. **Settings:**
   - **Environment**: `Docker`
   - **Dockerfile Path**: `./Dockerfile.minimal`
   - **Build Command**: (leave empty)
   - **Start Command**: (leave empty)
5. **Add environment variables** as listed above

## Debugging Steps

### 1. Check Build Logs
- Look for specific error messages in Render's build logs
- Common issues: memory limits, missing extensions, permission errors

### 2. Test Locally
Build the Docker image locally to see detailed errors:

```bash
docker build -t e-kampot-shop -f Dockerfile.minimal .
```

### 3. Simplify Dependencies
If the issue persists, temporarily remove some packages from `composer.json`:

```json
{
    "require": {
        "php": "^8.2",
        "laravel/framework": "^12.0",
        "laravel/sanctum": "^4.1",
        "laravel/tinker": "^2.10.1"
    }
}
```

Add back other packages one by one after successful deployment.

## Alternative: Use Render's Native PHP

If Docker continues to fail, use Render's native PHP runtime:

1. **Create `render-native.yaml`**:

```yaml
services:
  - type: web
    name: e-kampot-shop
    runtime: php
    buildCommand: |
      composer install --no-dev --optimize-autoloader
      npm ci
      npm run build
      php artisan config:cache
      php artisan route:cache
      php artisan view:cache
      touch database/database.sqlite
      php artisan migrate --force
    startCommand: php artisan serve --host=0.0.0.0 --port=$PORT
    plan: free
    region: oregon
    healthCheckPath: /
    envVars:
      # ... same environment variables as above
```

## Quick Fix Commands

Try these one by one:

### 1. Update Dockerfile path in render-docker.yaml
```yaml
dockerfilePath: ./Dockerfile.minimal
```

### 2. If that fails, try simple version
```yaml
dockerfilePath: ./Dockerfile.simple
```

### 3. If Docker fails completely, switch to native
```yaml
env: php
buildCommand: "./render-build.sh"
startCommand: "php artisan serve --host=0.0.0.0 --port=$PORT"
```

## Most Likely Solution

The `Dockerfile.minimal` should work best. It:
- Uses Apache instead of Nginx+FPM
- Installs dependencies in the correct order
- Handles permissions properly
- Uses optimized caching layers

Try that first, and if it still fails, switch to the native PHP approach.

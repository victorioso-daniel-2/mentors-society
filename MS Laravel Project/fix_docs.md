# Laravel Project Setup Troubleshooting Guide

This document outlines the steps taken to fix various issues encountered when setting up this Laravel project.

## Issue 1: Bootstrap Cache Directory Error

### Problem
When running commands like `composer install`, `php artisan key:generate`, or `php artisan serve`, the following error appeared:
```
The D:\path\to\project\bootstrap\cache directory must be present and writable.
```

### Solution
1. Manually create the bootstrap/cache directory:
```powershell
mkdir bootstrap/cache -Force
```

2. Set proper permissions on critical directories:
```powershell
# In PowerShell, the && operator doesn't work, so run these separately
icacls "bootstrap/cache" /grant Everyone:F /T
icacls "storage" /grant Everyone:F /T
```

## Issue 2: Missing Application Key Error

### Problem
When accessing the application in the browser, the following error appeared:
```
Illuminate\Encryption\MissingAppKeyException
No application encryption key has been specified.
```

### Solution
1. Create a `.env` file with a valid application key:
```powershell
# Generate a valid key
php -r "echo 'base64:'.base64_encode(random_bytes(32)).PHP_EOL;"

# Create .env file with the key (use Set-Content for proper encoding)
Set-Content -Path .env -Value "APP_KEY=base64:YOUR_GENERATED_KEY_HERE" -Encoding UTF8
```

2. Add more complete environment configuration:
```powershell
Set-Content -Path .env -Value @"
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:YOUR_GENERATED_KEY_HERE
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
"@ -Encoding UTF8
```

## Issue 3: Vite Manifest Not Found Error

### Problem
After fixing the application key issue, a new error appeared:
```
Illuminate\Foundation\ViteManifestNotFoundException
Vite manifest not found at: path\to\project\public\build/manifest.json
```

### Solution
1. Install Node.js dependencies:
```powershell
npm install
```

2. Build the frontend assets:
```powershell
npm run build
```

## Complete Setup Process

For a fresh installation, follow these steps in order:

1. Create the bootstrap/cache directory:
```powershell
mkdir bootstrap/cache -Force
```

2. Set proper permissions:
```powershell
icacls "bootstrap/cache" /grant Everyone:F /T
icacls "storage" /grant Everyone:F /T
```

3. Create the .env file with a valid key:
```powershell
# Generate a key
php -r "echo 'base64:'.base64_encode(random_bytes(32)).PHP_EOL;"

# Create .env file with minimal configuration
Set-Content -Path .env -Value "APP_KEY=YOUR_GENERATED_KEY
APP_ENV=local
APP_DEBUG=true" -Encoding UTF8
```

4. Install dependencies:
```powershell
composer install
npm install
```

5. Build frontend assets:
```powershell
npm run build
```

6. Start the development server:
```powershell
php artisan serve
```

## Database Setup

If you need to set up the database:

1. Configure the database connection in the `.env` file
2. Run migrations:
```powershell
php artisan migrate
```

3. Seed the database if needed:
```powershell
php artisan db:seed
``` 
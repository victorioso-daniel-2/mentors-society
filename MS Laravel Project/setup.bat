@echo off
echo ========================================
echo Mentor Society System Setup Script
echo ========================================
echo.

echo Checking prerequisites...
echo.

REM Check if PHP is installed
php --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ PHP is not installed or not in PATH
    echo Please install PHP from: https://www.php.net/downloads.php
    pause
    exit /b 1
) else (
    echo ✅ PHP is installed
)

REM Check if Composer is installed
composer --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Composer is not installed or not in PATH
    echo Please install Composer from: https://getcomposer.org/download/
    pause
    exit /b 1
) else (
    echo ✅ Composer is installed
)

REM Check if Node.js is installed
node --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Node.js is not installed or not in PATH
    echo Please install Node.js from: https://nodejs.org/
    pause
    exit /b 1
) else (
    echo ✅ Node.js is installed
)

REM Check if npm is installed
npm --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ npm is not installed or not in PATH
    pause
    exit /b 1
) else (
    echo ✅ npm is installed
)

echo.
echo All prerequisites are satisfied!
echo.
echo Starting setup process...
echo.

REM Install PHP dependencies
echo Installing PHP dependencies with Composer...
composer install
if %errorlevel% neq 0 (
    echo ❌ Failed to install PHP dependencies
    pause
    exit /b 1
)
echo ✅ PHP dependencies installed successfully

REM Copy environment file
echo.
echo Setting up environment file...
if not exist .env (
    copy .env.example .env
    echo ✅ Environment file created
) else (
    echo ✅ Environment file already exists
)

REM Generate application key
echo.
echo Generating application key...
php artisan key:generate
if %errorlevel% neq 0 (
    echo ❌ Failed to generate application key
    pause
    exit /b 1
)
echo ✅ Application key generated

REM Install Node.js dependencies
echo.
echo Installing Node.js dependencies...
npm install
if %errorlevel% neq 0 (
    echo ❌ Failed to install Node.js dependencies
    pause
    exit /b 1
)
echo ✅ Node.js dependencies installed successfully

echo.
echo ========================================
echo Setup completed successfully!
echo ========================================
echo.
echo Next steps:
echo 1. Configure your database in the .env file
echo 2. Run: php artisan migrate
echo 3. Start Laravel server: php artisan serve
echo 4. Start Vite dev server: npm run dev
echo 5. Open http://127.0.0.1:8000 in your browser
echo.
echo For detailed instructions, see README.md
echo.
pause 
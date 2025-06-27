#!/bin/bash

echo "========================================"
echo "Mentor Society System Setup Script"
echo "========================================"
echo

echo "Checking prerequisites..."
echo

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo "❌ PHP is not installed or not in PATH"
    echo "Please install PHP from: https://www.php.net/downloads.php"
    exit 1
else
    echo "✅ PHP is installed"
fi

# Check if Composer is installed
if ! command -v composer &> /dev/null; then
    echo "❌ Composer is not installed or not in PATH"
    echo "Please install Composer from: https://getcomposer.org/download/"
    exit 1
else
    echo "✅ Composer is installed"
fi

# Check if Node.js is installed
if ! command -v node &> /dev/null; then
    echo "❌ Node.js is not installed or not in PATH"
    echo "Please install Node.js from: https://nodejs.org/"
    exit 1
else
    echo "✅ Node.js is installed"
fi

# Check if npm is installed
if ! command -v npm &> /dev/null; then
    echo "❌ npm is not installed or not in PATH"
    exit 1
else
    echo "✅ npm is installed"
fi

echo
echo "All prerequisites are satisfied!"
echo
echo "Starting setup process..."
echo

# Install PHP dependencies
echo "Installing PHP dependencies with Composer..."
composer install
if [ $? -ne 0 ]; then
    echo "❌ Failed to install PHP dependencies"
    exit 1
fi
echo "✅ PHP dependencies installed successfully"

# Copy environment file
echo
echo "Setting up environment file..."
if [ ! -f .env ]; then
    cp .env.example .env
    echo "✅ Environment file created"
else
    echo "✅ Environment file already exists"
fi

# Generate application key
echo
echo "Generating application key..."
php artisan key:generate
if [ $? -ne 0 ]; then
    echo "❌ Failed to generate application key"
    exit 1
fi
echo "✅ Application key generated"

# Install Node.js dependencies
echo
echo "Installing Node.js dependencies..."
npm install
if [ $? -ne 0 ]; then
    echo "❌ Failed to install Node.js dependencies"
    exit 1
fi
echo "✅ Node.js dependencies installed successfully"

echo
echo "========================================"
echo "Setup completed successfully!"
echo "========================================"
echo
echo "Next steps:"
echo "1. Configure your database in the .env file"
echo "2. Run: php artisan migrate"
echo "3. Start Laravel server: php artisan serve"
echo "4. Start Vite dev server: npm run dev"
echo "5. Open http://127.0.0.1:8000 in your browser"
echo
echo "For detailed instructions, see README.md"
echo 
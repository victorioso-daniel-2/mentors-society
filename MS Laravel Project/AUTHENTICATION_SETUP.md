# Authentication Setup Guide

This guide explains how to set up and test the authentication system for the Mentors Society application.

## Features Implemented

### 1. Login Component (`Login.vue`)
- Form validation for student number and password
- Loading states with spinner animation
- Error handling with user-friendly messages
- Success feedback with automatic redirect
- CSRF token handling for Laravel Sanctum

### 2. API Service (`api.js`)
- Centralized API communication
- Token management (store/retrieve/remove)
- CSRF cookie handling
- Error handling and logging

### 3. Authentication Flow
- User enters student number and password
- Form validates input
- API call to `/api/auth/login` endpoint
- Token stored in localStorage
- User data and roles stored in localStorage
- Redirect to dashboard on success

## Setup Instructions

### 1. Install Dependencies
```bash
composer install
npm install
```

### 2. Environment Setup
Make sure your `.env` file has the correct database configuration:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 3. Database Setup
```bash
php artisan migrate
php artisan db:seed
```

### 4. Create Test User
Run the test user creation script:
```bash
php create_test_user.php
```

This will create a test user with:
- Student Number: `2021-00001-TEST`
- Password: `TestUser`
- Email: `test@example.com`

### 5. Build Assets
```bash
npm run build
```

### 6. Start Development Server
```bash
php artisan serve
```

## Testing the Authentication

### 1. Access the Application
Navigate to `http://localhost:8000` in your browser.

### 2. Login with Test Credentials
- Student Number: `2021-00001-TEST`
- Password: `TestUser`

### 3. Expected Behavior
- Form should validate input
- Loading spinner should appear during login
- On success: "Login successful! Redirecting..." message
- Automatic redirect to `/dashboard` after 1.5 seconds
- Dashboard should display user information

### 4. Error Testing
Try these scenarios:
- Empty fields (should show validation errors)
- Invalid student number (should show "Student number not found")
- Invalid password (should show "Invalid password")
- Network errors (should show generic error message)

## API Endpoints

### Authentication Endpoints
- `POST /api/auth/login` - Login with student number and password
- `POST /api/auth/logout` - Logout (requires authentication)
- `GET /api/auth/me` - Get current user info (requires authentication)

### Request Format for Login
```json
{
    "student_number": "2021-00001-TEST",
    "password": "TestUser"
}
```

### Response Format
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "student_number": "2021-00001-TEST",
            "first_name": "Test",
            "last_name": "User",
            "middle_initial": "T",
            "email": "test@example.com"
        },
        "roles": [...],
        "token": "1|abc123...",
        "token_type": "Bearer"
    }
}
```

## Security Features

### 1. CSRF Protection
- Laravel Sanctum CSRF tokens
- Automatic CSRF cookie retrieval
- Secure cookie handling

### 2. Token Management
- Bearer token authentication
- Token storage in localStorage
- Automatic token inclusion in API requests

### 3. Input Validation
- Client-side form validation
- Server-side validation in AuthController
- SQL injection prevention

## Troubleshooting

### Common Issues

1. **CSRF Token Mismatch**
   - Ensure `/sanctum/csrf-cookie` endpoint is accessible
   - Check CORS configuration in `config/cors.php`

2. **Database Connection Issues**
   - Verify database credentials in `.env`
   - Ensure database exists and is accessible

3. **Asset Loading Issues**
   - Run `npm run build` to compile assets
   - Check if Vite is running in development mode

4. **API Endpoint Not Found**
   - Ensure routes are properly defined in `routes/api.php`
   - Check if Laravel is running on the correct port

### Debug Mode
Enable debug mode in `.env`:
```env
APP_DEBUG=true
```

This will show detailed error messages for troubleshooting.

## Next Steps

After successful authentication implementation, consider adding:

1. **Password Reset Functionality**
   - Implement forgot password flow
   - Email verification system

2. **Remember Me Feature**
   - Persistent login sessions
   - Token refresh mechanism

3. **Role-Based Access Control**
   - Implement route guards based on user roles
   - Permission-based UI elements

4. **Session Management**
   - Token expiration handling
   - Automatic logout on inactivity

5. **Security Enhancements**
   - Rate limiting for login attempts
   - Two-factor authentication
   - Audit logging 
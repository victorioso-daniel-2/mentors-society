# Student Organization Management System Setup Guide

## Technology Stack

- **Backend**: Laravel 10.x (PHP 8.1+)
- **Frontend**: Vue.js 3.x with Composition API
- **CSS Framework**: Tailwind CSS 3.x
- **Database**: MySQL 8.0+
- **Authentication**: Laravel Sanctum
- **Development Environment**: Laravel Sail (Docker)

## System Requirements

- PHP 8.1 or higher
- Composer
- Node.js 16+ and NPM
- Docker and Docker Compose (for development)
- MySQL 8.0+
- Git

## Initial Setup

### 1. Clone the Repository

```bash
git clone https://github.com/your-org/organization-management-system.git
cd organization-management-system
```

### 2. Install Backend Dependencies

```bash
composer install
```

### 3. Set Up Environment Variables

```bash
cp .env.example .env
php artisan key:generate
```

Edit the `.env` file with your database credentials:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=org_management
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4. Install Frontend Dependencies

```bash
npm install
```

### 5. Database Setup

```bash
php artisan migrate --seed
```

This will create the database tables and seed them with initial data including:
- Default roles (Admin, President, Treasurer, etc.)
- Permissions
- Academic year settings
- Transaction types

### 6. Start the Development Server

```bash
php artisan serve
```

In a separate terminal:

```bash
npm run dev
```

## Docker Setup (Alternative)

If you prefer using Docker:

```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

## Project Structure

```
├── app/                  # PHP backend code
│   ├── Http/             # Controllers, Middleware, Requests
│   ├── Models/           # Database models
│   ├── Services/         # Business logic
│   └── Repositories/     # Data access layer
├── database/             # Migrations and seeders
├── resources/            # Frontend assets
│   ├── js/               # Vue components and logic
│   │   ├── components/   # Reusable Vue components
│   │   ├── pages/        # Page components
│   │   ├── store/        # Vuex store modules
│   │   └── router/       # Vue Router configuration
│   └── views/            # Blade templates
├── routes/               # API and web routes
└── tests/                # Automated tests
```

## Key Features Configuration

### Role Management

The system uses a hierarchical role-permission system:

1. Default roles are created during installation
2. Permissions are assigned to roles
3. Custom permissions can be added per user-role

To modify default roles and permissions, edit:
- `database/seeders/RoleSeeder.php`
- `database/seeders/PermissionSeeder.php`

### Financial Module

Cash-based transactions are configured in:
- `database/seeders/TransactionTypeSeeder.php`

Modify this file to add or change transaction types.

### Event Management

Event status workflow is defined in:
- `app/Enums/EventStatus.php`

### Tailwind CSS Configuration

Tailwind is configured in `tailwind.config.js`. The system uses a custom color palette matching your organization's branding:

```js
module.exports = {
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#f0f9ff',
          100: '#e0f2fe',
          // ... other shades
          900: '#0c4a6e',
        },
        secondary: {
          // ... your secondary color palette
        }
      }
    }
  },
  plugins: [
    require('@tailwindcss/forms'),
  ]
}
```

## Deployment

### Server Requirements

- PHP 8.1+
- Nginx or Apache
- MySQL 8.0+
- Composer
- Node.js and NPM (for building assets)

### Production Deployment Steps

1. Clone the repository on your production server
2. Install dependencies: `composer install --no-dev --optimize-autoloader`
3. Set up environment: `cp .env.example .env` and configure for production
4. Generate key: `php artisan key:generate`
5. Optimize: `php artisan optimize`
6. Build frontend: `npm ci && npm run build`
7. Set proper permissions: `chmod -R 775 storage bootstrap/cache`
8. Set up the database: `php artisan migrate --seed`
9. Configure your web server to point to the `public` directory

### Web Server Configuration

#### Nginx Example

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/your-project/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## Maintenance

### Regular Updates

To update the application:

```bash
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate
npm ci && npm run build
php artisan optimize:clear
php artisan optimize
```

### Backup Strategy

1. Database: Set up automated MySQL backups
   ```bash
   mysqldump -u username -p org_management > backup_$(date +%Y%m%d).sql
   ```

2. Files: Back up uploaded files (receipts, photos)
   ```bash
   tar -czf storage_backup_$(date +%Y%m%d).tar.gz storage/app/public
   ```

3. Store backups off-site or in cloud storage

## Troubleshooting

### Common Issues

1. **Permission Denied**: Ensure proper directory permissions
   ```bash
   chmod -R 775 storage bootstrap/cache
   chown -R www-data:www-data .
   ```

2. **Database Connection Issues**: Verify credentials in `.env`

3. **White Screen**: Check logs at `storage/logs/laravel.log`

4. **Missing Images/Uploads**: Ensure symbolic link is created
   ```bash
   php artisan storage:link
   ```

## Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Vue.js Documentation](https://vuejs.org/guide/introduction.html)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)

## Support

For issues or questions, contact the system administrator at admin@your-organization.com

# Mentor Society System

A Laravel-based web application with Vue.js frontend and Tailwind CSS styling.

## 🚀 Tech Stack

- **Backend**: Laravel 10
- **Frontend**: Vue.js 3
- **Styling**: Tailwind CSS v4
- **Build Tool**: Vite
- **Package Manager**: npm

## 📋 Prerequisites

Before you begin, ensure you have the following installed on your machine:

- **PHP** (version 8.1 or higher)
- **Composer** (PHP package manager)
- **Node.js** (version 18 or higher)
- **npm** (comes with Node.js)
- **Git**

### Installation Links:
- [PHP](https://www.php.net/downloads.php)
- [Composer](https://getcomposer.org/download/)
- [Node.js](https://nodejs.org/)

## 🛠️ Project Setup

### Step 1: Clone the Repository

```bash
git clone <your-repository-url>
cd mentors-society
cd "MS Laravel Project"
```

### Step 2: Install PHP Dependencies

```bash
composer install
```

### Step 3: Environment Configuration

1. Copy the environment file:
```bash
cp .env.example .env
```

2. Generate application key:
```bash
php artisan key:generate
```

3. Configure your database in the `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mentor_society
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Step 4: Install Node.js Dependencies

```bash
npm install
```

### Step 5: Database Setup

1. Create your database (if using MySQL):
```sql
CREATE DATABASE mentor_society;
```

2. Run migrations:
```bash
php artisan migrate
```

3. (Optional) Seed the database:
```bash
php artisan db:seed
```

## 🚀 Running the Application

### Development Mode

You need to run both the Laravel server and Vite development server simultaneously.

#### Terminal 1: Start Laravel Server
```bash
php artisan serve
```
This will start the Laravel server at `http://127.0.0.1:8000`

#### Terminal 2: Start Vite Development Server
```bash
npm run dev
```
This will start the Vite development server (usually at `http://localhost:5173` or `http://localhost:5174`)

### Production Build

To build the assets for production:
```bash
npm run build
```

## 🧪 Testing

### Running Tests
```bash
php artisan test
```

### Running Specific Test Files
```bash
php artisan test --filter=ExampleTest
```

## 📁 Project Structure

```
MS Laravel Project/
├── app/                    # Laravel application logic
│   ├── Console/           # Console commands
│   ├── Exceptions/        # Exception handling
│   ├── Http/              # HTTP layer
│   │   ├── Controllers/   # Controllers
│   │   └── Middleware/    # Middleware
│   ├── Models/            # Eloquent models
│   └── Providers/         # Service providers
├── bootstrap/             # Application bootstrap
├── config/                # Configuration files
├── database/              # Database files
├── public/                # Public assets
├── resources/
│   ├── css/
│   │   └── app.css        # Main CSS file with Tailwind imports
│   ├── js/
│   │   ├── app.js         # Main JavaScript entry point
│   │   └── components/
│   │       └── App.vue    # Main Vue component
│   └── views/
│       └── welcome.blade.php  # Main Blade template
├── routes/
│   ├── api.php            # API routes
│   ├── console.php        # Console routes
│   └── web.php            # Web routes
├── storage/               # Application storage
├── tests/                 # Test files
├── vite.config.js         # Vite configuration
├── tailwind.config.js     # Tailwind CSS configuration
├── postcss.config.cjs     # PostCSS configuration
└── package.json           # Node.js dependencies
```

## 🎨 Tailwind CSS Configuration

This project uses **Tailwind CSS v4** with the following configuration:

### Key Files:
- `tailwind.config.js` - Tailwind configuration
- `postcss.config.cjs` - PostCSS configuration
- `resources/css/app.css` - Main CSS file with Tailwind imports

### Tailwind CSS v4 Setup:
The project uses the new Tailwind CSS v4 syntax:
```css
@import "tailwindcss";
```

Instead of the old v3 syntax:
```css
@tailwind base;
@tailwind components;
@tailwind utilities;
```

## 🔧 Troubleshooting

### Common Issues:

#### 1. Styles Not Loading
- Ensure both servers are running (Laravel + Vite)
- Check browser console for errors
- Verify `@vite` directive is in your Blade template
- Clear browser cache

#### 2. Vite Port Already in Use
If you see "Port 5173 is in use", Vite will automatically try the next available port.

#### 3. Node Modules Issues
```bash
rm -rf node_modules package-lock.json
npm install
```

#### 4. Composer Issues
```bash
composer clear-cache
composer install
```

#### 5. Laravel Cache Issues
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Browser Developer Tools:
1. Open browser developer tools (F12)
2. Check the Console tab for JavaScript errors
3. Check the Network tab to ensure CSS/JS files are loading
4. Check the Elements tab to see if Tailwind classes are applied

## 📝 Development Workflow

1. **Start Development Servers**:
   ```bash
   # Terminal 1
   php artisan serve
   
   # Terminal 2
   npm run dev
   ```

2. **Make Changes**:
   - Edit Vue components in `resources/js/components/`
   - Edit CSS in `resources/css/app.css`
   - Edit Blade templates in `resources/views/`

3. **View Changes**:
   - Open `http://127.0.0.1:8000` in your browser
   - Changes will automatically reload

## 🚀 Deployment

### Building for Production:
```bash
npm run build
```

### Environment Variables:
Ensure all production environment variables are set in your `.env` file.

## 📞 Support

If you encounter any issues:
1. Check the troubleshooting section above
2. Review Laravel, Vue.js, and Tailwind CSS documentation
3. Check browser console for error messages
4. Ensure all prerequisites are properly installed

## 📚 Useful Links

- [Laravel Documentation](https://laravel.com/docs)
- [Vue.js Documentation](https://vuejs.org/guide/)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Vite Documentation](https://vitejs.dev/guide/)

---

**Happy Coding! 🎉**

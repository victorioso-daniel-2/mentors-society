# Mentors Society - Laravel Project

A Laravel 10 application with Vue 3 and Tailwind CSS v4 for managing the Mentors Society organization.

## 🚀 Quick Start

### Prerequisites

- **PHP 8.1+**
- **Composer**
- **Node.js 18+**
- **npm**
- **MySQL**

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd mentors-society
   ```

2. **Navigate to the Laravel project**
   ```bash
   cd "MS Laravel Project"
   ```

3. **Install PHP dependencies**
   ```bash
   composer install
   ```

4. **Install Node.js dependencies**
   ```bash
   npm install
   ```

5. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

6. **Configure database**
   - Edit `.env` file with your database credentials
   - **Import database structure**: Run `database_updated.sql` in your DBMS (MySQL Workbench, phpMyAdmin, etc.)
   - Run seeders: `php artisan db:seed` (no migrations needed)

## 🏃‍♂️ Running the Application

### Development Mode

1. **Start the Laravel development server**
   ```bash
   php artisan serve
   ```
   The application will be available at `http://localhost:8000`

2. **Start the Vite development server** (in a separate terminal)
   ```bash
   npm run dev
   ```
   Vite will run on `http://localhost:5173` for asset compilation

### Production Build

1. **Build assets for production**
   ```bash
   npm run build
   ```

2. **Start the Laravel server**
   ```bash
   php artisan serve
   ```

## 🛠️ Technology Stack

- **Backend**: Laravel 10
- **Frontend**: Vue 3
- **Styling**: Tailwind CSS v4
- **Build Tool**: Vite
- **Database**: MySQL

## 📁 Project Structure

```
mentors-society/
├── MS Laravel Project/          # Main Laravel application
│   ├── app/                     # Application logic
│   │   ├── css/
│   │   │   └── app.css         # Main CSS file with Tailwind
│   │   ├── js/
│   │   │   ├── app.js          # Main JavaScript entry point
│   │   │   └── components/
│   │   │       └── App.vue     # Vue root component
│   │   └── views/
│   │       └── welcome.blade.php
│   ├── database/
│   │   ├── migrations/         # Database migrations
│   │   └── seeders/           # Database seeders
│   ├── routes/
│   │   └── web.php            # Web routes
│   ├── config/                # Configuration files
│   ├── package.json           # Node.js dependencies
│   ├── composer.json          # PHP dependencies
│   ├── vite.config.js         # Vite configuration
│   ├── tailwind.config.js     # Tailwind CSS configuration
│   └── postcss.config.cjs     # PostCSS configuration
└── project documentations/     # Project documentation
    ├── documentation.md
    ├── final_architecture.md
    ├── setup_documentation.md
    └── sample-ui/             # UI mockups
```

## 🎨 Styling

The project uses **Tailwind CSS v4** with the following configuration:

- **CSS Entry Point**: `resources/css/app.css`
- **Configuration**: `tailwind.config.js`
- **PostCSS**: `postcss.config.cjs`

### Current Styles
- Black background (`bg-black`)
- Yellow text (`text-yellow-400`)
- Pulsing animation on headings (`animate-pulse`)
- Centered layout with flexbox

## 🗄️ Database

### Setup
The project requires importing the database structure first, then populating it with seeders.

### Database Structure Import
1. **Import SQL file**: Run `database_updated.sql` in your database management system:
   - **MySQL Workbench**: File → Open SQL Script → Select `database_updated.sql` → Execute
   - **phpMyAdmin**: Import → Choose File → Select `database_updated.sql` → Go
   - **Command Line**: `mysql -u username -p database_name < database_updated.sql`

### Seeders
After importing the database structure, populate it with sample data:
```bash
php artisan db:seed
```

### CSV Data Import
The project includes CSV files for importing student data:
- Located in `database/seeders/data/`
- Use `CsvStudentImportSeeder` to import the data

## 🔧 Configuration Files

### Vite Configuration (`vite.config.js`)
```javascript
import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
        vue(),
    ],
});
```

### Tailwind Configuration (`tailwind.config.js`)
```javascript
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {},
    },
    plugins: [],
};
```

### PostCSS Configuration (`postcss.config.cjs`)
```javascript
module.exports = {
    plugins: {
        "@tailwindcss/postcss": {},
        autoprefixer: {},
    },
};
```

## 🐛 Troubleshooting

### Common Issues

1. **"Cannot find package '@tailwindcss/vite'"**
   - Solution: Remove `@tailwindcss/vite` from `vite.config.js` (Tailwind v4 uses PostCSS)

2. **CSS not applying**
   - Ensure both servers are running (Laravel + Vite)
   - Check browser console for errors
   - Verify CSS import in `app.js`

3. **Port conflicts**
   - Vite will automatically find an available port
   - Laravel server can be changed with `php artisan serve --port=8080`

4. **Node modules issues**
   - Delete `node_modules` and `package-lock.json`
   - Run `npm install` again

### Development Commands

```bash
# Install dependencies
composer install
npm install

# Database setup (in order)
# 1. Import database structure: Run database_updated.sql in your DBMS
# 2. Seed database
php artisan db:seed

# Start development servers
php artisan serve
npm run dev

# Build for production
npm run build

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## 📚 Documentation

Additional documentation can be found in the `project documentations/` folder:
- `documentation.md` - General project documentation
- `final_architecture.md` - System architecture
- `setup_documentation.md` - Detailed setup instructions
- `workflow_documentation.md` - Development workflow

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📄 License

This project is for educational purposes and internal use by the Mentors Society organization.

---

**Note**: Make sure to run both the Laravel server (`php artisan serve`) and Vite development server (`npm run dev`) for the best development experience. 
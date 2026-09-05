# PHASE 1: Environment Setup & Laravel Installation

**Duration:** Week 1-2  
**Objective:** Set up development environment, initialize Laravel project, configure database, and prepare for Phase 2  
**Learning Focus:** Project initialization, Laravel structure, development environment setup

---

## 1. WHY THIS PHASE?

Before writing any application code, we need a solid foundation:

- **Environment Setup** ensures consistent development
- **Laravel Scaffolding** provides structure and conventions
- **Database Configuration** prepares data layer
- **Git Repository** enables version control and rollback
- **Documentation** establishes learning foundation

Think of this phase as building the house foundation before constructing walls.

---

## 2. WHAT WE'LL DO

### 2.1 Prerequisites Installation
- [ ] XAMPP installation and configuration
- [ ] Composer installation
- [ ] Verify PHP and MySQL

### 2.2 Laravel Project Creation
- [ ] Create fresh Laravel project
- [ ] Install project dependencies
- [ ] Configure environment variables

### 2.3 Database Setup
- [ ] Create MySQL database
- [ ] Configure database connection
- [ ] Run initial migrations

### 2.4 Project Structure
- [ ] Organize folder structure
- [ ] Create placeholder files and directories
- [ ] Set up Git repository and branches

---

## 3. HOW - STEP BY STEP INSTRUCTIONS

### Step 1: Install XAMPP (if not already installed)

XAMPP is an all-in-one package containing Apache, MySQL, PHP, and phpMyAdmin.

**Instructions:**

1. Download XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Choose version: PHP 8.2 or higher recommended
3. Run installer (default installation path: `C:\xampp`)
4. Accept defaults during installation
5. After installation, start XAMPP Control Panel

**Verification:**

Once installed, you should have:
- Apache (web server)
- MySQL (database)
- PHP (server-side language)
- phpMyAdmin (database UI)

**Important Files/Paths:**
- PHP executable: `C:\xampp\php\php.exe`
- MySQL: `C:\xampp\mysql\bin\mysql.exe`
- Apache config: `C:\xampp\apache\conf\httpd.conf`

---

### Step 2: Add PHP and Composer to System PATH

**Why:** Allows running PHP and Composer commands from any terminal location.

**Windows Instructions:**

1. Open "Environment Variables":
   - Press `Win + R` → type `sysdm.cpl` → press Enter
   - Go to "Advanced" tab → Click "Environment Variables..."

2. Under "System variables", click "New..." and add:
   - Variable name: `XAMPP_HOME`
   - Variable value: `C:\xampp`

3. Edit "Path" variable in System variables:
   - Click "Edit..."
   - Click "New"
   - Add: `C:\xampp\php`
   - Add: `C:\xampp\mysql\bin`
   - Click OK

4. Restart your terminal/PowerShell

**Verification:**
```powershell
php -v
mysql --version
```

Should display PHP and MySQL versions respectively.

---

### Step 3: Install Composer

**What is Composer?**
Composer is PHP's dependency manager (like npm for JavaScript). It installs and manages Laravel and all its dependencies.

**Installation:**

1. Download Composer installer from [https://getcomposer.org/download/](https://getcomposer.org/download/)
2. Run the installer (`Composer-Setup.exe`)
3. When prompted for PHP executable, select:
   ```
   C:\xampp\php\php.exe
   ```
4. Complete installation (can take several minutes)

**Verification:**
```powershell
composer --version
```

Should display Composer version.

---

### Step 4: Create Fresh Laravel Project

**Command:**
```powershell
composer create-project laravel/laravel tasmiya-enterprise
cd tasmiya-enterprise
```

**What happens:**
- Downloads Laravel framework (11.x LTS)
- Installs all dependencies
- Generates application structure
- Creates `.env` configuration file

**Expected output:** Completion message "Application ready! Build something amazing."

---

### Step 5: Configure Environment Variables

**File:** `.env` in project root

**Important configurations:**

```env
# Application Details
APP_NAME="Tasmiya Enterprises"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tasmiya_enterprise
DB_USERNAME=root
DB_PASSWORD=

# Mail Configuration (we'll use Mailtrap for testing)
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_FROM_ADDRESS=noreply@tasmiya-enterprise.local
MAIL_FROM_NAME="Tasmiya Enterprises"

# Cache Configuration
CACHE_DRIVER=file
SESSION_DRIVER=file

# Queue Configuration
QUEUE_CONNECTION=database
```

**Key Points:**
- `APP_ENV=local` for development
- `APP_DEBUG=true` shows detailed errors
- Database name: `tasmiya_enterprise`
- Default MySQL has no password (empty string)

---

### Step 6: Create MySQL Database

**Option A: Using phpMyAdmin (Easiest)**

1. Start XAMPP Control Panel (Apache and MySQL should be running)
2. Open browser → http://localhost/phpmyadmin
3. Click "New" in left sidebar
4. Database name: `tasmiya_enterprise`
5. Collation: `utf8mb4_unicode_ci`
6. Click "Create"

**Option B: Using Command Line**

```powershell
mysql -u root -e "CREATE DATABASE tasmiya_enterprise CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

**Verification:**
- Log into phpMyAdmin
- You should see `tasmiya_enterprise` in database list

---

### Step 7: Generate Laravel Application Key

**Command:**
```powershell
cd d:\Code\TamiyaEnterprises\tasmiya-enterprise
php artisan key:generate
```

**What it does:**
- Generates unique `APP_KEY` in `.env`
- Required for encryption/decryption
- Automatically run during `composer create-project`

**Verification:**
- Check `.env` file
- Should see `APP_KEY=base64:xxxxxxxxxxxxx`

---

### Step 8: Run Database Migrations

**What are migrations?**
Migrations are version-controlled database schema changes. They define tables, columns, and relationships.

**Command:**
```powershell
php artisan migrate
```

**What happens:**
- Creates default Laravel tables (users, migrations, password_resets, sessions)
- Establishes database structure
- Logs migration history in `migrations` table

**Verification:**
- Open phpMyAdmin
- Check `tasmiya_enterprise` database
- Should see tables like `users`, `migrations`, `personal_access_tokens`, etc.

---

### Step 9: Verify Laravel Installation

**Command:**
```powershell
php artisan serve
```

**What it does:**
- Starts development server
- Typically runs on `http://127.0.0.1:8000`

**Verification:**
1. Open browser → http://127.0.0.1:8000
2. You should see Laravel welcome page
3. Press `Ctrl+C` to stop server

---

### Step 10: Initialize Git Repository

**Commands:**
```powershell
git init
git config user.name "TasmiyaDev"
git config user.email "dev@tasmiya.local"

# Create main branch
git checkout -b main

# Create feature branch
git checkout -b feature
```

**Why branching?**
- `main`: Production-ready code
- `feature`: Development work
- Merge `feature` → `main` after phase completion

---

### Step 11: Create .gitignore File

**.gitignore purpose:** Tells Git which files/folders to exclude from version control.

**Create file:** `.gitignore` in project root

**Content:**
```
# Laravel
/vendor/
/node_modules/
/.env
/.env.local
.env.backup
/.vscode/
/.idea/

# Storage
/storage/logs/
/storage/framework/cache/

# Testing
/tests/
.phpunit.result.cache

# OS
.DS_Store
Thumbs.db

# IDE
.vscode/
.idea/
*.swp
*.swo

# XAMPP
*.log
```

---

### Step 12: Create Initial Project Structure

**Directory structure to create:**

```
app/
├── Models/           # Eloquent models (already exists)
├── Controllers/      # Controllers (already exists)
├── Middleware/       # Custom middleware
└── Traits/          # Reusable trait classes

resources/
├── views/
│   ├── layouts/
│   │   ├── app.blade.php          # Main layout
│   │   └── auth.blade.php         # Auth layout
│   ├── divisions/                 # Division-specific views
│   │   ├── division1/             # Tax division
│   │   ├── division2/             # IT division
│   │   └── division3/             # Technical support
│   ├── profiles/                  # Team member profiles
│   ├── components/                # Reusable components
│   └── auth/                      # Auth pages (already exists)
├── css/
│   ├── main.css                   # Global styles
│   ├── variables.css              # CSS variables
│   ├── division1.css              # Tax division theme
│   ├── division2.css              # IT division theme
│   └── division3.css              # Technical support theme
└── js/
    ├── app.js                     # Main JS file
    ├── api.js                     # API calls
    └── utils.js                   # Utility functions

database/
├── migrations/       # Database schema (already exists)
├── seeders/         # Dummy data generators
└── schema.sql       # Schema documentation

routes/
├── web.php          # Web routes (already exists)
├── api.php          # API routes (already exists)
└── admin.php        # Admin-specific routes

config/
└── [Laravel default configs]

tests/
├── Unit/            # Unit tests
├── Feature/         # Integration tests
└── CreatesApplication.php

docs/
├── PROJECT_CHARTER.md
├── PHASE_1_SETUP.md (this file)
├── PHASE_2_AUTH.md
├── ARCHITECTURE.md
├── DATABASE_SCHEMA.md
├── API_DOCUMENTATION.md
├── LEARNING_PATH.md
├── DEPLOYMENT_GUIDE.md
└── MAINTENANCE.md

.github/
└── workflows/       # CI/CD (future)
```

**Create directories:**
```powershell
mkdir app\Traits
mkdir app\Middleware (if not exists)
mkdir resources\views\layouts
mkdir resources\views\divisions\division1
mkdir resources\views\divisions\division2
mkdir resources\views\divisions\division3
mkdir resources\views\profiles
mkdir resources\views\components
mkdir resources\css
mkdir resources\js
mkdir database\seeders (if not exists)
mkdir routes
mkdir config
mkdir docs
mkdir tests\Feature
mkdir tests\Unit
```

---

## 4. ARCHITECTURE DECISIONS IN PHASE 1

### Why Laravel?

**Advantages:**
- Elegant syntax and conventions (Laravel way vs. custom code)
- Rich ecosystem of packages
- Built-in authentication, authorization, validation
- Excellent documentation and community
- Perfect for building professional applications quickly

### Project Structure Choice

**MVC Pattern (Model-View-Controller):**

```
Request → Route → Controller → Model (Database) → View (Response)
```

- **Models:** Represent data and business logic
- **Controllers:** Handle requests and responses
- **Views:** Display data to users
- **Routes:** Define URL endpoints

**Why MVC?**
- Separation of concerns (easier to maintain)
- Reusable code
- Testable components
- Industry standard

### Database Design Approach

**Normalization:**
- Tables represent entities
- Foreign keys establish relationships
- Reduces data duplication
- Improves query performance

**More detail in `docs/DATABASE_SCHEMA.md`** (created in Phase 1)

---

## 5. COMMON ISSUES & SOLUTIONS

### Issue 1: "php is not recognized"

**Solution:**
- Add `C:\xampp\php` to System PATH
- Restart terminal after modifying PATH
- Verify with `php -v`

### Issue 2: "Composer command not found"

**Solution:**
- Install Composer from official website
- Ensure installation completed successfully
- Restart terminal
- Verify with `composer --version`

### Issue 3: "SQLSTATE[HY000]: General error: 1030"

**Solution:**
- Check MySQL is running in XAMPP Control Panel
- Verify database name matches `.env`
- Check DB_USERNAME and DB_PASSWORD in `.env`
- Try: `php artisan migrate:fresh`

### Issue 4: "Port 8000 already in use"

**Solution:**
```powershell
php artisan serve --port=8001
# Use different port number
```

### Issue 5: "Class not found" errors

**Solution:**
```powershell
composer dump-autoload
# Regenerates autoload file after file changes
```

---

## 6. VERIFICATION CHECKLIST

Complete this checklist to verify Phase 1 completion:

- [ ] XAMPP installed and running (Apache + MySQL)
- [ ] PHP accessible from terminal (`php -v` works)
- [ ] Composer installed (`composer --version` works)
- [ ] Laravel project created at `d:\Code\TamiyaEnterprises\tasmiya-enterprise`
- [ ] `.env` configured with database details
- [ ] Database `tasmiya_enterprise` created in MySQL
- [ ] `php artisan migrate` completed successfully
- [ ] `php artisan serve` launches welcome page at http://127.0.0.1:8000
- [ ] Git repository initialized with main and feature branches
- [ ] `.gitignore` file created
- [ ] All directories created per structure
- [ ] PROJECT_CHARTER.md in root
- [ ] PHASE_1_SETUP.md in docs/

---

## 7. WHAT YOU'VE LEARNED

### Concepts
- **Development Environment:** Why we need XAMPP, PHP, and MySQL
- **Package Management:** How Composer manages dependencies
- **Laravel Structure:** Understanding MVC and directory organization
- **Database Setup:** Creating and connecting to MySQL
- **Version Control:** Git repository and branching strategy

### Skills
- Installing and configuring development tools
- Creating a fresh Laravel project
- Configuring environment variables
- Managing databases with phpMyAdmin
- Using command-line tools (PHP Artisan, Composer)
- Git initialization and branching

### Completed Deliverables
- ✓ Working Laravel development environment
- ✓ MySQL database configured
- ✓ Git repository with proper structure
- ✓ Project documentation (PROJECT_CHARTER.md, this file)
- ✓ Organized folder structure

---

## 8. NEXT PHASE PREVIEW

**Phase 2: Authentication & Authorization**

Next, we'll build:
- Admin login system
- Team member accounts
- Role-based access control
- Permission middleware
- Secure password handling

---

## 9. ADDITIONAL RESOURCES

### Laravel Documentation
- Official Laravel: https://laravel.com/docs/11.x
- Laravel Blade Templates: https://laravel.com/docs/11.x/blade
- Eloquent ORM: https://laravel.com/docs/11.x/eloquent

### Development Tools
- XAMPP: https://www.apachefriends.org/
- Composer: https://getcomposer.org/
- Git: https://git-scm.com/

### Learning Resources
- Laracasts: https://laracasts.com (video tutorials)
- Laravel News: https://laravel-news.com

---

**Phase Status:** Setup Complete  
**Last Updated:** February 4, 2026  
**Next:** Phase 2 - Authentication System

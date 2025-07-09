# Database Setup Instructions

## Quick Start (For Demo)
The application will work with demo data even if the database is not set up. You'll see blue notification messages indicating "Using demo data".

## Database Setup (For Production Use)

### Requirements
- XAMPP, WAMP, or MAMP (or standalone MySQL/MariaDB server)
- PHP 7.4+ with mysqli extension

### Steps

1. **Start MySQL Server**
   - XAMPP: Start Apache and MySQL from XAMPP Control Panel
   - WAMP: Start WAMP server
   - MAMP: Start MAMP server

2. **Create Database** (Optional - will be auto-created)
   - Open phpMyAdmin (usually at http://localhost/phpmyadmin)
   - Create database named `alok_crm`
   - Or run: `php create_database.php` from the project directory

3. **Configure Database Connection**
   - Edit `inc/db.php` if needed
   - Default settings work for XAMPP/WAMP with default MySQL installation

4. **Initialize Tables**
   - Tables will be auto-created when you first access the application
   - Or manually run: `php -r "require 'inc/config.php';"`

### Database Configuration Details

**Local Development (Default):**
- Host: localhost
- User: root
- Password: (empty)
- Database: alok_crm

**Production Server:**
- Host: localhost
- User: fnkjyinw_alok_crm
- Password: )joaUE#f~h6F
- Database: fnkjyinw_alok_crm

### Troubleshooting

**"Error loading customers/invoices" messages:**
1. Make sure MySQL/MariaDB is running
2. Check if you can access phpMyAdmin
3. Verify database credentials in `inc/db.php`
4. Check if `alok_crm` database exists

**Blue "Using demo data" messages:**
- This is normal when database is not available
- All features work with demo data for testing
- Set up database for persistent data storage

### Test Database Setup
Run this command to test your database setup:
```bash
php test_init.php
```

### Add Sample Data
Run this command to add sample customers and invoices:
```bash
php add_sample_data.php
```

## Current Status
- ✅ Application works with demo data (no database required)
- ✅ Full AJAX functionality with mock data
- ✅ Modern UI with toasts and responsive design
- ✅ Auto-fallback to demo data when database unavailable
- 🔄 Database setup optional for testing, required for production

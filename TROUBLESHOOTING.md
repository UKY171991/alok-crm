# Troubleshooting Guide for "Error loading invoices" and "Error loading customers"

## Quick Fix Summary

The errors you're seeing are caused by database connection issues. Here's how to fix them:

### 1. Check if MySQL is Running

**For XAMPP users:**
- Open XAMPP Control Panel
- Start "Apache" and "MySQL" services

**For WAMP users:**
- Start WAMP server
- Ensure MySQL service is green/running

**For manual MySQL installation:**
- Open Command Prompt as Administrator
- Run: `net start mysql`

### 2. Verify Database Setup

1. Open: `http://localhost/alok-crm/database_diagnostics.php`
2. This will show you:
   - Current environment detection
   - Database connection status
   - Available tables
   - Record counts

### 3. Common Solutions

**If MySQL service won't start:**
- Check if port 3306 is in use: `netstat -an | find "3306"`
- Try different port in MySQL configuration
- Restart your computer

**If database doesn't exist:**
- Create database `alok_crm` in phpMyAdmin
- Import the SQL file: `fnkjyinw_alok_crm.sql`

**If tables don't exist:**
- Run the database setup scripts in this order:
  1. `create_database.php`
  2. `test_database_setup.php`
  3. `add_sample_data.php`

### 4. Fallback Mode

If database is unavailable, the system will automatically use demo data. You'll see a blue notification banner indicating "Using demo data".

### 5. Test the Fix

1. Ensure MySQL is running
2. Refresh the Generate Invoice page
3. Check browser console (F12) for any remaining errors
4. The red error messages should disappear

### 6. Files Modified

- `inc/db.php` - Improved database connection handling
- `api_fallback.php` - Better error handling and fallback logic
- `generate_invoice.php` - Enhanced error messages and debugging

### 7. Debugging Commands

**Check MySQL service status:**
```cmd
sc query mysql
```

**Start MySQL service:**
```cmd
net start mysql
```

**Check if web server is running:**
- Visit: `http://localhost/`
- Should show XAMPP/WAMP dashboard

If you continue to have issues, check the error log at: `logs/error.log`

# Courier CRM - Quick Installation Guide

## Prerequisites
- Web server with PHP 7.4+ support
- MySQL 5.7+ or MariaDB 10.3+
- PHP extensions: PDO, PDO_MySQL, mbstring

## Installation Steps

### 1. Download & Extract
- Extract the CRM files to your web server directory
- Ensure all files have proper read/write permissions

### 2. Database Setup
- Create a new MySQL database (e.g., `courier_crm`)
- Create a database user with full privileges on this database

### 3. Configure Database Connection
Edit `inc/db.php` with your database credentials:
```php
<?php
$host = 'localhost';          // Your database host
$dbname = 'courier_crm';      // Your database name
$username = 'your_username';   // Your database username
$password = 'your_password';   // Your database password
?>
```

### 4. Access the Application
- Open your web browser and navigate to the application URL
- The system will automatically create all required tables and sample data
- **That's it!** No manual SQL script execution needed

### 5. First Login
- **Username**: `admin`
- **Password**: `admin123`
- **IMPORTANT**: Change this password immediately after first login

### 6. Verify Installation (Optional)
- Navigate to `your-domain.com/test_database_setup.php`
- This will show you a comprehensive report of all tables and data
- All items should show "✓ EXISTS" status

## Quick Feature Tour

### Customer Rate Master
- Navigate to "Customer Rate Master" from the sidebar
- Modern AJAX-based interface with filtering and CRUD operations
- Supports complex rate structures with zones, modes, and weight ranges

### Zone Master
- Navigate to "Zone Master" from the sidebar
- Manage destinations with zone classification and status control
- Supports bulk operations and status toggles

### Other Features
- Customer management
- Invoice generation
- Order tracking
- Rate management
- Comprehensive reporting

## Troubleshooting

### Database Connection Issues
- Verify database credentials in `inc/db.php`
- Ensure database server is running and accessible
- Check that the database user has proper privileges

### Tables Not Created
- Check PHP error logs for specific errors
- Ensure PHP has sufficient memory and execution time
- Verify database user has CREATE and ALTER privileges

### Permission Issues
- Ensure web server has read/write access to application files
- Check that log directory can be created and written to

## Support Files

### Documentation
- `DATABASE_SETUP_DOCUMENTATION.md` - Comprehensive database documentation
- `CUSTOMER_RATE_MASTER_README.md` - Customer Rate Master feature documentation

### Test Scripts
- `test_database_setup.php` - Database verification tool
- `test_customer_rate_setup.php` - Customer rate system verification

### SQL Scripts (Manual Installation Only)
If you prefer manual installation, these scripts are available:
- `fnkjyinw_alok_crm.sql` - Complete database dump
- `setup_customer_rates_final.sql` - Customer rate tables only
- `create_customer_rate_tables.sql` - Individual table creation

## Security Notes

### Default Credentials
- Change the default admin password immediately
- Consider creating additional user accounts with appropriate roles

### Database Security
- Use strong database passwords
- Limit database user privileges to only what's needed
- Consider using SSL for database connections in production

### File Permissions
- Ensure sensitive files like `inc/db.php` are not publicly accessible
- Set appropriate file permissions (644 for files, 755 for directories)

## Need Help?

### Check These First
1. PHP error logs
2. Database error logs
3. Browser console for JavaScript errors
4. `test_database_setup.php` verification report

### Common Solutions
- Clear browser cache for JavaScript/CSS issues
- Restart web server if configuration changes don't take effect
- Check database connection settings if pages fail to load

The system is designed to be self-configuring and should work out of the box with minimal setup. The automatic table creation ensures consistency across different deployment environments.

# Database Auto-Setup Documentation

## Overview

The Courier CRM system now includes automatic database table creation functionality that ensures all required tables are created when the application is accessed. This eliminates the need for manual SQL script execution and makes deployment seamless.

## How It Works

### Auto-Initialization
- The `inc/config.php` file includes an `initialize_database()` function that runs automatically when the config is loaded in a web context
- This function checks for the existence of all required tables and creates them if they don't exist
- It also inserts sample data for dropdown options and creates necessary indexes and foreign key constraints

### Manual Initialization
- For CLI or manual setup, you can call `create_customer_rate_tables()` function directly
- A test script `test_database_setup.php` is available to verify the setup

## Tables Created

### Core System Tables

#### 1. `customers`
- **Purpose**: Master customer data
- **Key Fields**: id, name, email, phone, address, gst_no, etc.
- **Sample Data**: 5 demo customers with complete information

#### 2. `destinations`
- **Purpose**: Destination/zone master with enhanced fields
- **Key Fields**: id, name, code, zone_name, type, status
- **Enhancements**: Added zone_name, type, and status columns for better categorization
- **Sample Data**: 15 major Indian cities with zones and types

#### 3. `users`
- **Purpose**: System authentication
- **Key Fields**: id, username, password, role
- **Default User**: admin/admin123 (should be changed after first login)

### Transaction Tables

#### 4. `invoices`
- **Purpose**: Invoice header information
- **Key Fields**: id, invoice_no, customer_id, invoice_date, total_amount, status
- **Features**: Proper indexing and foreign key to customers

#### 5. `invoice_items`
- **Purpose**: Invoice line items
- **Key Fields**: id, invoice_id, consignment_no, destination, weight, rate, amount
- **Features**: Foreign key to invoices table

#### 6. `orders`
- **Purpose**: Order/shipment tracking
- **Key Fields**: id, customer_id, date, docket, destination, mode, weight, etc.
- **Features**: Comprehensive shipment information storage

#### 7. `rates`
- **Purpose**: General rate master
- **Key Fields**: id, destination, mode, weight_from, weight_to, rate
- **Features**: Weight-based rate calculation support

### Customer Rate Master Tables

#### 8. `cr_modes`
- **Purpose**: Customer rate shipping modes
- **Key Fields**: mode_id, mode_name, status
- **Sample Data**: Air, Surface, Express, Economy, Standard

#### 9. `cr_consignment_types`
- **Purpose**: Customer rate consignment types
- **Key Fields**: consignment_type_id, type_name, status
- **Sample Data**: Document, Non-Document, Both, Parcel, Cargo

#### 10. `customer_rates`
- **Purpose**: Customer-specific rate matrix
- **Key Fields**: id, customer_id, mode, consignment_type, zone_wise, weight ranges, rates
- **Features**: Complex rate structure with multiple categorization options

## Foreign Key Relationships

```sql
customer_rates.customer_id → customers.id (CASCADE)
invoices.customer_id → customers.id (CASCADE)
invoice_items.invoice_id → invoices.id (CASCADE)
orders.customer_id → customers.id (SET NULL)
```

## Column Enhancements

### Destinations Table Enhancements
- **zone_name**: Categorizes destinations by geographical zones (North, South, East, West)
- **type**: Categorizes by destination type (Metro, City, Local, Tourist)
- **status**: Active/Inactive status for controlling visibility

## Sample Data Included

### Customers
- ABC Logistics (Noida)
- XYZ Enterprises (Bangalore) 
- Quick Courier Ltd (Pune)
- Express Delivery Co (Delhi)
- Fast Track Logistics (Kolkata)

### Destinations
- 15 major Indian cities with proper zone classifications
- Includes metros, tier-1 cities, and regional centers
- Proper zone distribution across India

### Master Data
- 5 shipping modes for flexibility
- 5 consignment types for different parcel categories
- Sample customer rates linking customers to destinations

## Error Handling

### Graceful Failures
- All table creation attempts are wrapped in try-catch blocks
- Missing dependencies are handled gracefully (e.g., foreign keys when parent tables don't exist)
- Errors are logged for debugging but don't break the application

### Validation
- Unique constraints prevent duplicate data
- Foreign key constraints maintain data integrity
- Default values ensure consistent data entry

## Testing and Verification

### Test Script
- `test_database_setup.php` provides comprehensive verification
- Checks table existence, structure, and sample data
- Verifies foreign key constraints
- Shows record counts and sample records

### Verification Steps
1. Access `test_database_setup.php` in your browser
2. Check that all tables show "EXISTS" status
3. Verify that sample data is populated
4. Confirm foreign key constraints are in place

## Deployment Notes

### Fresh Installation
1. Ensure database credentials are correct in `inc/db.php`
2. Access any page of the application - tables will be created automatically
3. Optionally run `test_database_setup.php` to verify setup

### Existing Installation
- The system safely adds missing tables and columns
- Existing data is preserved
- Only missing components are created

### Manual Setup (if needed)
```php
require_once 'inc/config.php';
$result = create_customer_rate_tables();
```

## Security Considerations

### Default Credentials
- Default admin user: `admin` / `admin123`
- **IMPORTANT**: Change this password immediately after first login

### Data Protection
- Foreign key constraints with CASCADE DELETE protect against orphaned records
- Input validation and sanitization built into the system
- Proper indexing for performance and security

## Customization

### Adding New Tables
1. Add CREATE TABLE statement to `initialize_database()` function
2. Include appropriate indexes and foreign keys
3. Add sample data insertion if needed
4. Update the test script to verify the new table

### Modifying Existing Tables
1. Add column existence checks using `SHOW COLUMNS`
2. Use `ALTER TABLE` statements within try-catch blocks
3. Handle migration of existing data if needed

## Troubleshooting

### Common Issues
1. **Database connection fails**: Check credentials in `inc/db.php`
2. **Tables not created**: Check PHP error logs for specific errors
3. **Foreign key failures**: Ensure parent tables exist before child tables
4. **Permission errors**: Ensure database user has CREATE and ALTER privileges

### Debug Information
- All errors are logged using PHP's error_log function
- Check application logs for detailed error messages
- Use the test script to identify specific issues

## Performance Considerations

### Indexing Strategy
- Primary keys on all tables
- Foreign key indexes for join performance
- Composite indexes on frequently queried combinations (e.g., weight ranges)

### Data Volume
- Tables are designed to handle enterprise-level data volumes
- Proper normalization reduces storage requirements
- Efficient querying through strategic indexing

## Future Enhancements

### Planned Features
- Audit trail tables for change tracking
- Configuration tables for system settings
- Backup and restore functionality
- Data archival for historical records

### Migration Support
- Version-aware migrations for database schema updates
- Backward compatibility maintenance
- Data transformation utilities for major changes

This auto-setup system ensures that the Courier CRM can be deployed easily across different environments while maintaining data integrity and providing a consistent experience.

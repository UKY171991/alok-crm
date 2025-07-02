# Customer Rate Master Implementation

## Overview
A complete Customer Rate Master system has been implemented for the Courier CRM application, matching the legacy Windows-style UI shown in the screenshot. The system includes full CRUD operations, AJAX functionality, toaster notifications, and a print feature.

## Files Created/Modified

### Backend PHP Scripts
1. **fetch_customers_rate.php** - Fetches dropdown data (customers, zones, modes, consignment types)
2. **fetch_customer_rates.php** - Retrieves customer rate data with filtering
3. **add_customer_rate.php** - Adds new customer rates with validation
4. **edit_customer_rate.php** - Updates existing customer rates
5. **delete_customer_rate.php** - Deletes customer rates
6. **print_rate_chart.php** - Generates printable rate charts
7. **customer_rate_master.php** - Main page UI (already existed, now fully functional)
8. **setup_customer_rate_master.sql** - Database setup script
9. **test_customer_rate_setup.php** - Database testing script

### Updated Files
- **inc/sidebar.php** - Added Customer Rate Master menu item (already existed)

## Database Structure

### New Tables Created
1. **cr_modes** - Shipping modes (Air, Surface, Express, etc.)
2. **cr_consignment_types** - Consignment types (Document, Non-Document, etc.)
3. **customer_rates** - Main rates table with foreign key relationships

### Existing Tables Used
- **customers** - Customer information
- **destinations** - Zones/destinations for shipping

## Features Implemented

### UI Features
- ✅ Legacy Windows-style blue interface matching screenshot
- ✅ Filter section with customer, mode, consignment type, and zone dropdowns
- ✅ Add rate form with weight ranges and rate inputs
- ✅ Data table showing all customer rates
- ✅ Edit and delete buttons for each rate
- ✅ Print Rate Chart functionality
- ✅ Responsive design with proper styling

### AJAX Functionality
- ✅ All operations (add, edit, delete, load) are AJAX-based
- ✅ No page refreshes required
- ✅ Real-time data loading and updates
- ✅ Dynamic dropdown population

### Toaster Notifications
- ✅ Success messages for all operations
- ✅ Error messages with detailed feedback
- ✅ Warning messages for validation issues
- ✅ Info messages for user guidance

### CRUD Operations
- ✅ **Create** - Add new customer rates with validation
- ✅ **Read** - View rates with filtering options
- ✅ **Update** - Edit existing rates (both simple and full edit)
- ✅ **Delete** - Remove rates with confirmation

### Data Validation
- ✅ Required field validation
- ✅ Weight range validation (from < to)
- ✅ Rate validation (must be positive)
- ✅ Overlap detection for weight ranges
- ✅ Foreign key validation

### Print Functionality
- ✅ Generate printable rate charts
- ✅ Filter-based printing
- ✅ Professional HTML layout
- ✅ Customer grouping and details

## Setup Instructions

### 1. Database Setup
Run the SQL script to create necessary tables:
```sql
-- Run setup_customer_rate_master.sql in your MySQL database
```

### 2. Test Database Setup
Access the test script to verify all tables exist:
```
http://your-domain/test_customer_rate_setup.php
```

### 3. Access the System
Navigate to the Customer Rate Master page:
```
http://your-domain/customer_rate_master.php
```

## API Endpoints

### GET Endpoints
- **fetch_customers_rate.php** - Get dropdown data
  - `?type=customers` - Get customer list
  - `?type=zones` - Get zone list  
  - `?type=modes` - Get mode list
  - `?type=consignment_types` - Get consignment type list
  - No parameters - Get all data as JSON

- **fetch_customer_rates.php** - Get rate data
  - `?customer_id=X` - Filter by customer
  - `?zone_id=X` - Filter by zone
  - `?mode_id=X` - Filter by mode
  - `?consignment_type_id=X` - Filter by consignment type

### POST Endpoints
- **add_customer_rate.php** - Add new rate
- **edit_customer_rate.php** - Update existing rate
- **delete_customer_rate.php** - Delete rate

## Compatibility

### Backward Compatibility
The implementation supports both:
- Existing UI parameter names (for current customer_rate_master.php)
- New standardized parameter names (for future development)

### Browser Support
- Modern browsers with JavaScript enabled
- jQuery 3.6.0+ required
- Bootstrap compatible (if used)

## Security Features
- ✅ SQL injection prevention using prepared statements
- ✅ Input validation and sanitization
- ✅ Error handling with proper HTTP status codes
- ✅ Session-based authentication (inherited from existing system)

## Performance Considerations
- ✅ Efficient database queries with proper indexing
- ✅ Foreign key relationships for data integrity
- ✅ Minimal AJAX requests with bulk data loading
- ✅ Optimized SQL with JOINs instead of multiple queries

## Future Enhancements
- State-wise and city-wise filtering (placeholders implemented)
- Bulk import/export functionality
- Rate calculation wizard
- Historical rate tracking
- Advanced reporting features

## Testing Checklist
- [ ] Database tables created successfully
- [ ] Dropdown data loads correctly
- [ ] Add rate functionality works with validation
- [ ] Edit rate functionality works
- [ ] Delete rate functionality works with confirmation
- [ ] Filter functionality works correctly
- [ ] Print functionality generates proper output
- [ ] Toaster notifications appear for all actions
- [ ] Error handling works for edge cases

## Troubleshooting

### Common Issues
1. **Dropdowns not loading**: Check database connection and table existence
2. **AJAX errors**: Check PHP error logs and browser console
3. **Foreign key errors**: Ensure all referenced tables have data
4. **Print not working**: Check popup blocker settings

### Debug Mode
Add `?debug=1` to any PHP script URL for detailed error output.

## Support
All backend scripts include comprehensive error handling and logging. Check server error logs for detailed debugging information.

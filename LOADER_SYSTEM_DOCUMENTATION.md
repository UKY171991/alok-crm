# CRM Loader System Documentation

## Overview
The CRM Loader System provides comprehensive loading states and user feedback across all pages of the Courier CRM application. It includes multiple types of loaders for different scenarios.

## Features

### 1. Page Loader
- **Purpose**: Shows during initial page load
- **Design**: Full-screen overlay with animated spinner and progress bar
- **Auto-hides**: After page content is loaded

### 2. Action Loader
- **Purpose**: Shows during form submissions, AJAX requests, and other actions
- **Design**: Centered modal-style overlay
- **Manual control**: Show/hide programmatically

### 3. Page Transition Loader
- **Purpose**: Shows during navigation between pages
- **Design**: Top progress bar
- **Auto-triggered**: On link clicks and form submissions

### 4. Table Loader
- **Purpose**: Shows while loading table data
- **Design**: Overlay on table container with spinner

### 5. Button Loader
- **Purpose**: Shows loading state on buttons during actions
- **Design**: Replaces button content with spinner

### 6. Form Loader
- **Purpose**: Shows loading state on entire forms
- **Design**: Animated sweep effect across form

## Implementation

### Files Added
```
css/loader.css      - All loader styles
js/loader.js        - Core loader functionality
js/loader-enhanced.js - Enhanced utilities and page-specific functions
```

### Auto-Setup
The loader system is automatically included on all pages through:
- `inc/header.php` - Includes loader.css
- `inc/footer.php` - Includes loader.js and loader-enhanced.js

## Usage Examples

### 1. Basic Page Loaders

#### Show Page Loader
```javascript
showLoader('Loading Data', 'Please wait while we fetch information...');
```

#### Hide Page Loader
```javascript
hideLoader();
```

#### Page-Specific Loaders
```javascript
// Direct Party Booking
initDirectPartyBookingLoader();

// Invoice Generation
initInvoiceGenerationLoader();

// Customer Management
initCustomerManagementLoader();

// Reports
initReportsLoader();
```

### 2. Action Loaders

#### Show Action Loader
```javascript
showActionLoader('Processing request...', 'This may take a few moments');
```

#### Hide Action Loader
```javascript
hideActionLoader();
```

### 3. Button Loaders

#### Set Button Loading State
```javascript
const button = document.getElementById('submitBtn');
setButtonLoading(button, true);  // Show loading
setButtonLoading(button, false); // Hide loading
```

#### Using jQuery
```javascript
$('#submitBtn').setLoading(true);  // Show loading
$('#submitBtn').setLoading(false); // Hide loading
```

### 4. Form Loaders

#### Set Form Loading State
```javascript
const form = document.getElementById('myForm');
setFormLoading(form, true);  // Show loading
setFormLoading(form, false); // Hide loading
```

#### Using jQuery
```javascript
$('#myForm').setLoading(true);  // Show loading
$('#myForm').setLoading(false); // Hide loading
```

### 5. Table Loaders

#### Show Table Loader
```javascript
const table = document.getElementById('dataTable');
const overlay = window.CRMLoader.showTableLoader(table);

// Later, hide the loader
window.CRMLoader.hideTableLoader(overlay);
```

#### Using jQuery
```javascript
const overlay = $('#dataTable').showTableLoader();
$('#dataTable').hideTableLoader(overlay);
```

### 6. AJAX with Loaders
```javascript
ajaxWithLoader({
    url: 'api/fetch-data.php',
    method: 'GET',
    success: function(data) {
        // Handle success
        console.log('Data loaded:', data);
    },
    error: function() {
        // Handle error
        console.error('Failed to load data');
    }
});
```

### 7. Automatic Loading Classes

#### Auto-Loading Forms
Add class `loader-form` to any form:
```html
<form class="loader-form" action="save_data.php" method="POST">
    <!-- Form fields -->
</form>
```

#### Auto-Loading Buttons
Add class `loader-btn` to any button:
```html
<button class="btn btn-primary loader-btn" onclick="processData()">
    Process Data
</button>
```

#### Auto-Loading Navigation
Add class `loader-nav` to any navigation link:
```html
<a href="customers.php" class="loader-nav">Customers</a>
```

## Page-Specific Implementations

### 1. Direct Party Booking (direct_party_booking.php)
```javascript
$(document).ready(function() {
    initDirectPartyBookingLoader();
    // ... rest of page initialization
});
```

### 2. Invoice Generation (generate_invoice.php)
```javascript
$(document).ready(function() {
    initInvoiceGenerationLoader();
    // ... rest of page initialization
});
```

### 3. Customer Management (customers.php)
```javascript
document.addEventListener('DOMContentLoaded', function() {
    initCustomerManagementLoader();
});
```

### 4. Dashboard (index.php)
```javascript
document.addEventListener('DOMContentLoaded', function() {
    if (typeof showLoader !== 'undefined') {
        showLoader('Loading Dashboard', 'Preparing charts and statistics...');
        setTimeout(() => hideLoader(), 1500);
    }
});
```

## Customization

### Custom Loader Messages
```javascript
// Custom loading steps for complex operations
const steps = [
    'Initializing connection...',
    'Fetching user data...',
    'Loading preferences...',
    'Finalizing setup...'
];

window.CRMLoader.simulateAsyncOperation(3000, steps);
```

### Custom Loader Styling
Override CSS variables in your custom stylesheet:
```css
:root {
    --loader-primary-color: #your-color;
    --loader-secondary-color: #your-color;
    --loader-background: #your-background;
}
```

## Performance Considerations

### 1. Minimum Display Time
Loaders have minimum display times to prevent flickering:
- Page loaders: 500ms minimum
- Action loaders: 300ms minimum
- Button loaders: 200ms minimum

### 2. Memory Management
- Loaders automatically clean up after hiding
- Event listeners are properly removed
- No memory leaks in long-running sessions

### 3. Accessibility
- Proper ARIA labels for screen readers
- Respects `prefers-reduced-motion` setting
- High contrast mode support
- Keyboard navigation friendly

## Browser Support
- Chrome 60+
- Firefox 55+
- Safari 11+
- Edge 16+
- Mobile browsers (iOS Safari, Chrome Mobile)

## Troubleshooting

### Loader Not Showing
1. Check if loader.css is included
2. Verify loader.js is loaded
3. Check for JavaScript console errors

### Loader Not Hiding
1. Ensure `hideLoader()` is called
2. Check for JavaScript errors preventing execution
3. Verify setTimeout callbacks are working

### Button Loader Issues
1. Make sure button has `dataset.originalText` available
2. Check if button is properly referenced
3. Ensure button is not disabled by other scripts

## Examples in Production

### Form Submission with Validation
```javascript
$('#customerForm').on('submit', function(e) {
    e.preventDefault();
    
    // Validate form
    if (!validateForm(this)) {
        return;
    }
    
    // Show loader
    setFormLoading(this, true);
    showActionLoader('Saving customer...', 'Validating data');
    
    // Submit form
    $.ajax({
        url: 'save_customer.php',
        method: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            setFormLoading('#customerForm', false);
            hideActionLoader();
            showToast('Customer saved successfully!', 'success');
        },
        error: function() {
            setFormLoading('#customerForm', false);
            hideActionLoader();
            showToast('Error saving customer', 'error');
        }
    });
});
```

### Table Data Loading
```javascript
function loadCustomerData() {
    const table = $('#customersTable');
    const overlay = table.showTableLoader();
    
    $.ajax({
        url: 'fetch_customers.php',
        method: 'GET',
        success: function(data) {
            table.find('tbody').html(data);
            table.hideTableLoader(overlay);
        },
        error: function() {
            table.hideTableLoader(overlay);
            showToast('Error loading customers', 'error');
        }
    });
}
```

## Advanced Features

### Skeleton Loading
For complex layouts, use skeleton loading:
```javascript
// Create skeleton in container
window.CRMLoader.createSkeleton(document.getElementById('content'), 'table');

// Remove skeleton when data loads
window.CRMLoader.removeSkeleton(document.getElementById('content'));
```

### Progress Tracking
```javascript
// Manual progress control
const progressBar = document.getElementById('mainProgressBar');
let progress = 0;

const interval = setInterval(() => {
    progress += 10;
    progressBar.style.width = progress + '%';
    
    if (progress >= 100) {
        clearInterval(interval);
        hideLoader();
    }
}, 200);
```

This comprehensive loader system ensures consistent user experience across all pages while providing flexibility for custom implementations.

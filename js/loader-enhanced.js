/**
 * Enhanced page-specific loader implementations
 * Add these functions to individual pages for custom loader behavior
 */

// Enhanced Direct Party Booking Loader
function initDirectPartyBookingLoader() {
    if (typeof window.CRMLoader !== 'undefined') {
        // Show specific loading for booking data
        showLoader('Loading Direct Party Booking', 'Fetching customer data and bookings...');
        
        // Simulate loading steps specific to booking page
        setTimeout(() => {
            window.CRMLoader.showActionLoader('Loading customers...', 'Please wait');
            
            setTimeout(() => {
                window.CRMLoader.showActionLoader('Loading destinations...', 'Almost ready');
                
                setTimeout(() => {
                    hideActionLoader();
                    hideLoader();
                }, 1000);
            }, 800);
        }, 500);
    }
}

// Enhanced Invoice Generation Loader
function initInvoiceGenerationLoader() {
    if (typeof window.CRMLoader !== 'undefined') {
        showLoader('Loading Invoice System', 'Preparing invoice generation tools...');
        
        setTimeout(() => {
            window.CRMLoader.showActionLoader('Loading invoice templates...', 'Setting up forms');
            
            setTimeout(() => {
                hideActionLoader();
                hideLoader();
            }, 1200);
        }, 800);
    }
}

// Enhanced Customer Management Loader
function initCustomerManagementLoader() {
    if (typeof window.CRMLoader !== 'undefined') {
        showLoader('Loading Customer Management', 'Fetching customer database...');
        
        setTimeout(() => {
            hideLoader();
        }, 1000);
    }
}

// Enhanced Reports Loader
function initReportsLoader() {
    if (typeof window.CRMLoader !== 'undefined') {
        showLoader('Loading Reports Dashboard', 'Preparing analytics and charts...');
        
        setTimeout(() => {
            window.CRMLoader.showActionLoader('Generating charts...', 'Processing data');
            
            setTimeout(() => {
                hideActionLoader();
                hideLoader();
            }, 1500);
        }, 700);
    }
}

// Form submission with loader
function handleFormSubmissionWithLoader(form, successMessage = 'Form submitted successfully!') {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        setFormLoading(form, true);
        showActionLoader('Submitting form...', 'Processing your request');
        
        // Simulate form submission
        setTimeout(() => {
            setFormLoading(form, false);
            hideActionLoader();
            
            // Show success message (you can integrate with your toast system)
            if (typeof showToast !== 'undefined') {
                showToast(successMessage, 'success');
            } else {
                alert(successMessage);
            }
        }, 2000);
    });
}

// Table data loading with loader
function loadTableDataWithLoader(tableId, loadFunction) {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    const overlay = window.CRMLoader.showTableLoader(table);
    
    // Execute your data loading function
    if (typeof loadFunction === 'function') {
        loadFunction().then(() => {
            window.CRMLoader.hideTableLoader(overlay);
        }).catch(() => {
            window.CRMLoader.hideTableLoader(overlay);
        });
    } else {
        // Default simulation
        setTimeout(() => {
            window.CRMLoader.hideTableLoader(overlay);
        }, 1500);
    }
}

// Button action with loader
function handleButtonActionWithLoader(button, actionFunction, loadingText = 'Processing...') {
    button.addEventListener('click', function() {
        setButtonLoading(button, true);
        
        if (typeof actionFunction === 'function') {
            actionFunction().then(() => {
                setButtonLoading(button, false);
            }).catch(() => {
                setButtonLoading(button, false);
            });
        } else {
            // Default simulation
            setTimeout(() => {
                setButtonLoading(button, false);
            }, 2000);
        }
    });
}

// AJAX request with loader
function ajaxWithLoader(options) {
    if (!window.jQuery) return;
    
    const defaultOptions = {
        beforeSend: function() {
            showActionLoader('Loading...', 'Please wait');
        },
        complete: function() {
            hideActionLoader();
        },
        error: function() {
            hideActionLoader();
            if (typeof showToast !== 'undefined') {
                showToast('An error occurred. Please try again.', 'error');
            }
        }
    };
    
    const mergedOptions = Object.assign(defaultOptions, options);
    return $.ajax(mergedOptions);
}

// Page navigation with loader
function navigateWithLoader(url, delay = 500) {
    window.CRMLoader.showPageTransition();
    
    setTimeout(() => {
        window.location.href = url;
    }, delay);
}

// Auto-apply loaders to common elements
document.addEventListener('DOMContentLoaded', function() {
    // Auto-apply loaders to forms with class 'loader-form'
    document.querySelectorAll('.loader-form').forEach(form => {
        handleFormSubmissionWithLoader(form);
    });
    
    // Auto-apply loaders to buttons with class 'loader-btn'
    document.querySelectorAll('.loader-btn').forEach(button => {
        handleButtonActionWithLoader(button);
    });
    
    // Auto-apply loaders to navigation links with class 'loader-nav'
    document.querySelectorAll('.loader-nav').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            navigateWithLoader(this.href);
        });
    });
});

console.log('🚀 Enhanced loader functions loaded successfully!');

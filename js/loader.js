/**
 * Global Loader System for CRM Application
 * Provides consistent loading states across all pages
 */

class LoaderManager {
    constructor() {
        this.isInitialized = false;
        this.pageLoader = null;
        this.actionLoader = null;
        this.pageTransition = null;
        this.loadingSteps = [
            'Initializing application...',
            'Loading user data...',
            'Fetching resources...',
            'Preparing interface...',
            'Almost ready...'
        ];
        this.currentStep = 0;
        this.init();
    }

    init() {
        if (this.isInitialized) return;
        
        this.createLoaderElements();
        this.bindEvents();
        this.showPageLoader();
        this.isInitialized = true;
    }

    createLoaderElements() {
        // Create main page loader
        this.pageLoader = document.createElement('div');
        this.pageLoader.className = 'page-loader';
        this.pageLoader.innerHTML = `
            <div class="loader-content">
                <div class="main-spinner">
                    <div class="spinner-ring"></div>
                </div>
                <div class="loader-title">Loading CRM System</div>
                <div class="loader-subtitle">Please wait while we prepare everything for you</div>
                <div class="loader-dots">
                    <div class="loader-dot"></div>
                    <div class="loader-dot"></div>
                    <div class="loader-dot"></div>
                </div>
                <div class="progress-container">
                    <div class="progress-bar" id="mainProgressBar"></div>
                </div>
                <div class="loader-progress" id="loadingStep">Initializing...</div>
            </div>
        `;

        // Create action loader
        this.actionLoader = document.createElement('div');
        this.actionLoader.className = 'action-loader';
        this.actionLoader.innerHTML = `
            <div class="action-spinner"></div>
            <div class="action-loader-text" id="actionLoaderText">Processing...</div>
            <div class="action-loader-subtitle" id="actionLoaderSubtitle">Please wait</div>
        `;

        // Create page transition loader
        this.pageTransition = document.createElement('div');
        this.pageTransition.className = 'page-transition';

        // Append to body
        document.body.appendChild(this.pageLoader);
        document.body.appendChild(this.actionLoader);
        document.body.appendChild(this.pageTransition);
    }

    bindEvents() {
        // Show loader on form submissions
        document.addEventListener('submit', (e) => {
            if (!e.target.hasAttribute('data-no-loader')) {
                this.showActionLoader('Submitting form...', 'Processing your request');
            }
        });

        // Show loader on AJAX requests
        if (window.jQuery) {
            $(document).ajaxStart(() => {
                this.showPageTransition();
            });

            $(document).ajaxStop(() => {
                this.hidePageTransition();
            });
        }

        // Show loader on navigation
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a[href]');
            if (link && !link.hasAttribute('data-no-loader') && 
                !link.href.includes('#') && !link.href.includes('javascript:')) {
                this.showPageTransition();
            }
        });

        // Hide loader when page is fully loaded
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                this.simulateLoading();
            });
        } else {
            this.simulateLoading();
        }
    }

    simulateLoading() {
        let progress = 0;
        const progressBar = document.getElementById('mainProgressBar');
        const stepElement = document.getElementById('loadingStep');
        
        const interval = setInterval(() => {
            progress += Math.random() * 15 + 5;
            if (progress > 100) progress = 100;
            
            if (progressBar) {
                progressBar.style.width = progress + '%';
            }
            
            // Update loading step
            const stepIndex = Math.floor((progress / 100) * this.loadingSteps.length);
            if (stepIndex < this.loadingSteps.length && stepElement) {
                stepElement.textContent = this.loadingSteps[stepIndex];
            }
            
            if (progress >= 100) {
                clearInterval(interval);
                setTimeout(() => {
                    this.hidePageLoader();
                }, 500);
            }
        }, 200);
    }

    showPageLoader(title = null, subtitle = null) {
        if (!this.pageLoader) return;
        
        if (title) {
            const titleElement = this.pageLoader.querySelector('.loader-title');
            if (titleElement) titleElement.textContent = title;
        }
        
        if (subtitle) {
            const subtitleElement = this.pageLoader.querySelector('.loader-subtitle');
            if (subtitleElement) subtitleElement.textContent = subtitle;
        }
        
        this.pageLoader.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    hidePageLoader() {
        if (!this.pageLoader) return;
        
        this.pageLoader.classList.add('hidden');
        document.body.style.overflow = '';
        
        // Remove loader after animation
        setTimeout(() => {
            if (this.pageLoader && this.pageLoader.classList.contains('hidden')) {
                this.pageLoader.style.display = 'none';
            }
        }, 500);
    }

    showActionLoader(text = 'Processing...', subtitle = 'Please wait') {
        if (!this.actionLoader) return;
        
        const textElement = document.getElementById('actionLoaderText');
        const subtitleElement = document.getElementById('actionLoaderSubtitle');
        
        if (textElement) textElement.textContent = text;
        if (subtitleElement) subtitleElement.textContent = subtitle;
        
        this.actionLoader.classList.add('show');
    }

    hideActionLoader() {
        if (!this.actionLoader) return;
        this.actionLoader.classList.remove('show');
    }

    showPageTransition() {
        if (!this.pageTransition) return;
        this.pageTransition.classList.add('active');
    }

    hidePageTransition() {
        if (!this.pageTransition) return;
        this.pageTransition.classList.remove('active');
    }

    // Table loading methods
    showTableLoader(tableElement) {
        if (!tableElement) return;
        
        const container = tableElement.closest('.table-responsive') || tableElement.parentElement;
        if (!container) return;
        
        container.style.position = 'relative';
        
        const overlay = document.createElement('div');
        overlay.className = 'table-loading-overlay';
        overlay.innerHTML = '<div class="table-spinner"></div>';
        
        container.appendChild(overlay);
        return overlay;
    }

    hideTableLoader(overlay) {
        if (overlay && overlay.parentElement) {
            overlay.parentElement.removeChild(overlay);
        }
    }

    // Button loading methods
    setButtonLoading(button, isLoading = true) {
        if (!button) return;
        
        if (isLoading) {
            button.classList.add('btn-loading');
            button.disabled = true;
            
            // Store original text
            if (!button.dataset.originalText) {
                button.dataset.originalText = button.innerHTML;
            }
            
            button.innerHTML = '<span class="btn-text">' + button.dataset.originalText + '</span>';
        } else {
            button.classList.remove('btn-loading');
            button.disabled = false;
            
            if (button.dataset.originalText) {
                button.innerHTML = button.dataset.originalText;
            }
        }
    }

    // Form loading methods
    setFormLoading(form, isLoading = true) {
        if (!form) return;
        
        if (isLoading) {
            form.classList.add('form-loading');
            const inputs = form.querySelectorAll('input, select, textarea, button');
            inputs.forEach(input => {
                input.disabled = true;
            });
        } else {
            form.classList.remove('form-loading');
            const inputs = form.querySelectorAll('input, select, textarea, button');
            inputs.forEach(input => {
                input.disabled = false;
            });
        }
    }

    // Skeleton loader methods
    createSkeleton(element, type = 'text') {
        if (!element) return;
        
        element.innerHTML = '';
        element.classList.add('skeleton');
        
        if (type === 'text') {
            element.innerHTML = `
                <div class="skeleton-text long"></div>
                <div class="skeleton-text medium"></div>
                <div class="skeleton-text short"></div>
            `;
        } else if (type === 'table') {
            const rows = 5;
            let html = '';
            for (let i = 0; i < rows; i++) {
                html += '<div class="skeleton-text" style="height: 20px; margin-bottom: 10px;"></div>';
            }
            element.innerHTML = html;
        }
    }

    removeSkeleton(element) {
        if (!element) return;
        element.classList.remove('skeleton');
    }

    // Utility methods
    delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    async simulateAsyncOperation(duration = 2000, steps = null) {
        if (steps) {
            const stepDuration = duration / steps.length;
            for (let i = 0; i < steps.length; i++) {
                this.showActionLoader(steps[i], `Step ${i + 1} of ${steps.length}`);
                await this.delay(stepDuration);
            }
        } else {
            await this.delay(duration);
        }
    }
}

// Helper functions for global use
window.CRMLoader = new LoaderManager();

// Convenience functions
window.showLoader = (title, subtitle) => window.CRMLoader.showPageLoader(title, subtitle);
window.hideLoader = () => window.CRMLoader.hidePageLoader();
window.showActionLoader = (text, subtitle) => window.CRMLoader.showActionLoader(text, subtitle);
window.hideActionLoader = () => window.CRMLoader.hideActionLoader();
window.setButtonLoading = (button, isLoading) => window.CRMLoader.setButtonLoading(button, isLoading);
window.setFormLoading = (form, isLoading) => window.CRMLoader.setFormLoading(form, isLoading);

// jQuery integration if available
if (window.jQuery) {
    $.fn.showTableLoader = function() {
        return window.CRMLoader.showTableLoader(this[0]);
    };
    
    $.fn.hideTableLoader = function(overlay) {
        window.CRMLoader.hideTableLoader(overlay);
    };
    
    $.fn.setLoading = function(isLoading = true) {
        this.each(function() {
            if (this.tagName === 'BUTTON' || this.tagName === 'INPUT' && this.type === 'submit') {
                window.CRMLoader.setButtonLoading(this, isLoading);
            } else if (this.tagName === 'FORM') {
                window.CRMLoader.setFormLoading(this, isLoading);
            }
        });
        return this;
    };
}

// Auto-hide loader on window load
window.addEventListener('load', () => {
    setTimeout(() => {
        if (window.CRMLoader) {
            window.CRMLoader.hidePageLoader();
        }
    }, 1000);
});

// Handle page visibility changes
document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible') {
        window.CRMLoader.hidePageTransition();
    }
});

console.log('🔄 CRM Loader System initialized successfully!');

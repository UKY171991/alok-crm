/**
 * Global Page Loader Helper Script
 * Include this script in any page to enable the global page loader
 */

(function() {
    'use strict';
    
    // Global loader configuration
    const LOADER_CONFIG = {
        showDuration: 1000,
        hideDuration: 500,
        autoHide: true,
        debug: true
    };
    
    // Utility functions
    const utils = {
        log: function(message) {
            if (LOADER_CONFIG.debug) {
                console.log('[Page Loader]', message);
            }
        },
        
        error: function(message) {
            console.error('[Page Loader]', message);
        }
    };
    
    // Main loader functions
    const pageLoader = {
        element: null,
        progressBar: null,
        stepElement: null,
        isVisible: false,
        progressInterval: null,
        
        init: function() {
            utils.log('Initializing page loader...');
            this.findElements();
            this.show();
            this.setupAutoHide();
        },
        
        findElements: function() {
            this.element = document.getElementById('globalPageLoader');
            this.progressBar = document.getElementById('globalProgressBar');
            this.stepElement = document.getElementById('globalLoadingStep');
            
            if (!this.element) {
                utils.error('Global page loader element not found');
                return false;
            }
            
            utils.log('Loader elements found successfully');
            return true;
        },
        
        show: function() {
            if (!this.element) return;
            
            utils.log('Showing page loader');
            this.element.style.display = 'flex';
            this.element.style.visibility = 'visible';
            this.element.style.opacity = '1';
            this.element.classList.remove('hidden');
            this.isVisible = true;
            
            // Start progress animation if elements exist
            if (this.progressBar) {
                this.startProgressAnimation();
            }
        },
        
        hide: function() {
            if (!this.element || !this.isVisible) return;
            
            utils.log('Hiding page loader');
            
            // Complete progress bar
            if (this.progressBar) {
                this.progressBar.style.width = '100%';
            }
            
            if (this.stepElement) {
                this.stepElement.textContent = 'Complete!';
            }
            
            // Clear progress interval
            if (this.progressInterval) {
                clearInterval(this.progressInterval);
                this.progressInterval = null;
            }
            
            setTimeout(() => {
                this.element.classList.add('hidden');
                setTimeout(() => {
                    this.element.style.display = 'none';
                    this.isVisible = false;
                    utils.log('Page loader hidden');
                }, LOADER_CONFIG.hideDuration);
            }, 300);
        },
        
        startProgressAnimation: function() {
            let progress = 0;
            const steps = [
                'Loading page components...',
                'Fetching user data...',
                'Setting up interface...',
                'Almost ready...'
            ];
            
            this.progressInterval = setInterval(() => {
                progress += Math.random() * 20 + 5;
                if (progress > 95) progress = 95;
                
                if (this.progressBar) {
                    this.progressBar.style.width = progress + '%';
                }
                
                const stepIndex = Math.floor((progress / 100) * steps.length);
                if (stepIndex < steps.length && this.stepElement) {
                    this.stepElement.textContent = steps[stepIndex];
                }
                
                if (progress >= 95) {
                    clearInterval(this.progressInterval);
                    this.progressInterval = null;
                }
            }, 200);
        },
        
        setupAutoHide: function() {
            if (!LOADER_CONFIG.autoHide) return;
            
            const hideLoader = () => {
                setTimeout(() => {
                    this.hide();
                }, LOADER_CONFIG.showDuration);
            };
            
            // Hide loader when everything is ready
            if (document.readyState === 'loading') {
                utils.log('Document is loading, setting up DOMContentLoaded listener');
                document.addEventListener('DOMContentLoaded', () => {
                    utils.log('DOMContentLoaded fired');
                    hideLoader();
                });
            } else {
                utils.log('Document already loaded, hiding loader immediately');
                hideLoader();
            }
            
            // Also hide on window load
            window.addEventListener('load', () => {
                utils.log('Window load event fired');
                setTimeout(() => {
                    this.hide();
                }, LOADER_CONFIG.showDuration + 200);
            });
        }
    };
    
    // Form submission loader
    const formLoader = {
        show: function(message = 'Processing...') {
            utils.log('Showing form loader: ' + message);
            pageLoader.show();
            if (pageLoader.stepElement) {
                pageLoader.stepElement.textContent = message;
            }
        },
        
        hide: function() {
            pageLoader.hide();
        }
    };
    
    // Action loader for buttons
    const actionLoader = {
        show: function(button, message = 'Loading...') {
            if (!button) return;
            
            button.disabled = true;
            button.classList.add('btn-loading');
            
            const originalText = button.textContent;
            button.setAttribute('data-original-text', originalText);
            button.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>' + message;
        },
        
        hide: function(button) {
            if (!button) return;
            
            button.disabled = false;
            button.classList.remove('btn-loading');
            
            const originalText = button.getAttribute('data-original-text');
            if (originalText) {
                button.textContent = originalText;
                button.removeAttribute('data-original-text');
            }
        }
    };
    
    // Make functions globally available
    window.PageLoader = {
        show: pageLoader.show.bind(pageLoader),
        hide: pageLoader.hide.bind(pageLoader),
        form: formLoader,
        action: actionLoader,
        config: LOADER_CONFIG
    };
    
    // Auto-initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            pageLoader.init();
        });
    } else {
        pageLoader.init();
    }
    
    utils.log('Page loader helper script loaded');
})();

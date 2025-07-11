<?php 
// Simple test page for loader functionality
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprehensive Loader Test - Courier CRM</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Loader CSS -->
    <link rel="stylesheet" href="css/loader.css">
    <!-- Page Loader Helper -->
    <script src="js/page-loader-helper.js"></script>
</head>
<body>
    <!-- Global Page Loader -->
    <div id="globalPageLoader" class="page-loader">
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
                <div class="progress-bar" id="globalProgressBar"></div>
            </div>
            <div class="loader-progress" id="globalLoadingStep">Initializing...</div>
        </div>
    </div>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center mb-4">
                    <i class="fas fa-cog fa-spin text-primary"></i>
                    Comprehensive Loader Test
                </h1>
                
                <div class="alert alert-info">
                    <h5><i class="fas fa-info-circle"></i> Test Information</h5>
                    <p class="mb-2">This page tests all loader functionality in the CRM system.</p>
                    <ul class="mb-0">
                        <li>Page should show a loader when first loaded</li>
                        <li>You can manually test different loader scenarios below</li>
                        <li>Check browser console for debug information</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5><i class="fas fa-play"></i> Basic Loader Tests</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary" onclick="testShowLoader()">
                                <i class="fas fa-spinner"></i> Show Global Loader
                            </button>
                            <button class="btn btn-secondary" onclick="testHideLoader()">
                                <i class="fas fa-eye-slash"></i> Hide Global Loader
                            </button>
                            <button class="btn btn-info" onclick="testPageLoadSimulation()">
                                <i class="fas fa-refresh"></i> Simulate Page Load
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5><i class="fas fa-tasks"></i> Advanced Loader Tests</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button class="btn btn-success" onclick="testFormLoader()">
                                <i class="fas fa-file-alt"></i> Test Form Loader
                            </button>
                            <button class="btn btn-warning" id="actionTestBtn" onclick="testActionLoader(this)">
                                <i class="fas fa-download"></i> Test Action Loader
                            </button>
                            <button class="btn btn-danger" onclick="testMultiStepLoader()">
                                <i class="fas fa-step-forward"></i> Multi-Step Loader
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h5><i class="fas fa-terminal"></i> Test Results Console</h5>
                        <button class="btn btn-sm btn-outline-light float-end" onclick="clearTestResults()">
                            <i class="fas fa-trash"></i> Clear
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="testResults" class="bg-light p-3 rounded" style="height: 300px; overflow-y: auto; font-family: monospace;">
                            <p class="text-success"><strong>[INIT]</strong> Page loaded successfully</p>
                            <p class="text-info"><strong>[INFO]</strong> Loader test page ready</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5><i class="fas fa-check-circle"></i> System Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                Global Loader Element
                                <span class="badge bg-success" id="loaderElementStatus">✓ Found</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                CSS Styles
                                <span class="badge bg-success" id="cssStatus">✓ Loaded</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                JavaScript Helper
                                <span class="badge bg-success" id="jsStatus">✓ Active</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                PageLoader API
                                <span class="badge bg-success" id="apiStatus">✓ Available</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h5><i class="fas fa-link"></i> Navigation Test</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">Test loader on different pages:</p>
                        <div class="d-grid gap-2">
                            <a href="index.php" class="btn btn-outline-primary">
                                <i class="fas fa-home"></i> Dashboard
                            </a>
                            <a href="login.php" class="btn btn-outline-secondary">
                                <i class="fas fa-sign-in-alt"></i> Login Page
                            </a>
                            <a href="loader-test.html" class="btn btn-outline-info">
                                <i class="fas fa-flask"></i> Basic Test Page
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let testCounter = 0;
        
        function logTest(message, type = 'info') {
            testCounter++;
            const time = new Date().toLocaleTimeString();
            const results = document.getElementById('testResults');
            const colors = {
                info: 'text-info',
                success: 'text-success',
                warning: 'text-warning',
                error: 'text-danger'
            };
            
            const logEntry = document.createElement('p');
            logEntry.className = colors[type] || 'text-info';
            logEntry.innerHTML = `<strong>[${time}]</strong> ${message}`;
            results.appendChild(logEntry);
            results.scrollTop = results.scrollHeight;
        }

        function testShowLoader() {
            logTest('Manual show loader test started', 'info');
            if (window.PageLoader) {
                window.PageLoader.show();
                logTest('PageLoader.show() called successfully', 'success');
            } else {
                logTest('PageLoader API not available!', 'error');
            }
        }

        function testHideLoader() {
            logTest('Manual hide loader test started', 'info');
            if (window.PageLoader) {
                window.PageLoader.hide();
                logTest('PageLoader.hide() called successfully', 'success');
            } else {
                logTest('PageLoader API not available!', 'error');
            }
        }

        function testPageLoadSimulation() {
            logTest('Page load simulation started', 'info');
            window.PageLoader.show();
            
            setTimeout(() => {
                window.PageLoader.hide();
                logTest('Page load simulation completed', 'success');
            }, 3000);
        }

        function testFormLoader() {
            logTest('Form loader test started', 'info');
            if (window.PageLoader && window.PageLoader.form) {
                window.PageLoader.form.show('Processing form data...');
                logTest('Form loader shown', 'success');
                
                setTimeout(() => {
                    window.PageLoader.form.hide();
                    logTest('Form loader hidden', 'success');
                }, 2500);
            } else {
                logTest('Form loader API not available!', 'error');
            }
        }

        function testActionLoader(button) {
            logTest('Action loader test started', 'info');
            if (window.PageLoader && window.PageLoader.action) {
                window.PageLoader.action.show(button, 'Processing...');
                logTest('Action loader shown on button', 'success');
                
                setTimeout(() => {
                    window.PageLoader.action.hide(button);
                    logTest('Action loader hidden from button', 'success');
                }, 2000);
            } else {
                logTest('Action loader API not available!', 'error');
            }
        }

        function testMultiStepLoader() {
            logTest('Multi-step loader test started', 'info');
            const loader = document.getElementById('globalPageLoader');
            const progressBar = document.getElementById('globalProgressBar');
            const stepElement = document.getElementById('globalLoadingStep');
            
            if (loader) {
                loader.style.display = 'flex';
                loader.classList.remove('hidden');
                
                const steps = [
                    'Initializing system...',
                    'Connecting to database...',
                    'Loading user preferences...',
                    'Preparing dashboard...',
                    'Finalizing setup...'
                ];
                
                let currentStep = 0;
                let progress = 0;
                
                const interval = setInterval(() => {
                    progress += 20;
                    
                    if (progressBar) {
                        progressBar.style.width = progress + '%';
                    }
                    
                    if (stepElement && currentStep < steps.length) {
                        stepElement.textContent = steps[currentStep];
                        logTest(`Step ${currentStep + 1}: ${steps[currentStep]}`, 'info');
                    }
                    
                    currentStep++;
                    
                    if (progress >= 100) {
                        clearInterval(interval);
                        setTimeout(() => {
                            loader.classList.add('hidden');
                            setTimeout(() => {
                                loader.style.display = 'none';
                            }, 500);
                            logTest('Multi-step loader test completed', 'success');
                        }, 500);
                    }
                }, 1000);
            } else {
                logTest('Global loader element not found!', 'error');
            }
        }

        function clearTestResults() {
            const results = document.getElementById('testResults');
            results.innerHTML = '<p class="text-success"><strong>[INIT]</strong> Test results cleared</p>';
            testCounter = 0;
        }

        function checkSystemStatus() {
            // Check loader element
            const loaderElement = document.getElementById('globalPageLoader');
            const loaderStatus = document.getElementById('loaderElementStatus');
            if (loaderElement) {
                loaderStatus.textContent = '✓ Found';
                loaderStatus.className = 'badge bg-success';
            } else {
                loaderStatus.textContent = '✗ Missing';
                loaderStatus.className = 'badge bg-danger';
            }

            // Check PageLoader API
            const apiStatus = document.getElementById('apiStatus');
            if (window.PageLoader) {
                apiStatus.textContent = '✓ Available';
                apiStatus.className = 'badge bg-success';
            } else {
                apiStatus.textContent = '✗ Missing';
                apiStatus.className = 'badge bg-danger';
            }

            logTest('System status check completed', 'info');
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            logTest('DOM Content Loaded', 'success');
            setTimeout(checkSystemStatus, 500);
        });

        window.addEventListener('load', function() {
            logTest('Window Load Complete', 'success');
        });
    </script>
</body>
</html>

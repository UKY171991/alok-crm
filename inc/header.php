<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courier CRM</title>
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/custom.css">
    <!-- Loader CSS -->
    <link rel="stylesheet" href="css/loader.css">
    <!-- Page Loader Helper -->
    <script src="js/page-loader-helper.js"></script>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
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
    
    <!-- Inline script to show loader immediately -->
    <script>
    (function() {
        // Ensure the page loader is visible from the start
        console.log('Initializing global page loader...');
        
        // Show loader immediately when page starts loading
        const loader = document.getElementById('globalPageLoader');
        if (loader) {
            loader.style.display = 'flex';
            loader.style.visibility = 'visible';
            loader.style.opacity = '1';
            loader.classList.remove('hidden');
            console.log('Global loader displayed');
        } else {
            console.error('Global loader element not found');
        }
        
        // Simple progress simulation
        let progress = 0;
        const progressBar = document.getElementById('globalProgressBar');
        const stepElement = document.getElementById('globalLoadingStep');
        
        const steps = [
            'Loading page components...',
            'Fetching user data...',
            'Setting up interface...',
            'Almost ready...'
        ];
        
        const progressInterval = setInterval(() => {
            progress += Math.random() * 20 + 5;
            if (progress > 95) progress = 95;
            
            if (progressBar) {
                progressBar.style.width = progress + '%';
            }
            
            const stepIndex = Math.floor((progress / 100) * steps.length);
            if (stepIndex < steps.length && stepElement) {
                stepElement.textContent = steps[stepIndex];
            }
            
            if (progress >= 95) {
                clearInterval(progressInterval);
            }
        }, 200);
        
        // Hide loader when page is fully loaded
        function hideGlobalLoader() {
            console.log('Hiding global loader...');
            if (progressBar) {
                progressBar.style.width = '100%';
            }
            if (stepElement) {
                stepElement.textContent = 'Complete!';
            }
            
            setTimeout(() => {
                if (loader) {
                    loader.classList.add('hidden');
                    setTimeout(() => {
                        loader.style.display = 'none';
                        console.log('Global loader hidden');
                    }, 500);
                }
                clearInterval(progressInterval);
            }, 300);
        }
        
        // Hide loader when everything is ready
        if (document.readyState === 'loading') {
            console.log('Document is loading, setting up DOMContentLoaded listener');
            document.addEventListener('DOMContentLoaded', () => {
                console.log('DOMContentLoaded fired');
                setTimeout(hideGlobalLoader, 800);
            });
        } else {
            console.log('Document already loaded, hiding loader immediately');
            setTimeout(hideGlobalLoader, 800);
        }
        
        // Also hide on window load
        window.addEventListener('load', () => {
            console.log('Window load event fired');
            setTimeout(hideGlobalLoader, 1000);
        });
    })();
    </script>
    
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="index.php" class="nav-link">Home</a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="logout.php" role="button">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->
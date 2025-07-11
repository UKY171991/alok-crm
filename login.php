<?php
session_start();

// Redirect to index.php if already logged in
if (isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

include 'inc/db.php'; 

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Dummy check
    if ($_POST['username'] === 'admin' && $_POST['password'] === 'admin') {
        $_SESSION['user'] = 'admin';
        header('Location: index.php');
        exit;
    } else {
        $error = 'Invalid login!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Courier CRM</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Loader CSS -->
    <link rel="stylesheet" href="css/loader.css">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 1000;
        }
        .login-card h2 {
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Global Page Loader for Login -->
    <div id="globalPageLoader" class="page-loader">
        <div class="loader-content">
            <div class="main-spinner">
                <div class="spinner-ring"></div>
            </div>
            <div class="loader-title">Loading Login Page</div>
            <div class="loader-subtitle">Preparing secure authentication</div>
            <div class="loader-dots">
                <div class="loader-dot"></div>
                <div class="loader-dot"></div>
                <div class="loader-dot"></div>
            </div>
        </div>
    </div>
    
    <!-- Inline script to show loader immediately -->
    <script>
    (function() {
        // Show loader immediately when page starts loading
        const loader = document.getElementById('globalPageLoader');
        if (loader) {
            loader.style.display = 'flex';
            loader.style.visibility = 'visible';
            loader.style.opacity = '1';
            loader.classList.remove('hidden');
        }
        
        // Hide loader when page is fully loaded
        function hideGlobalLoader() {
            setTimeout(() => {
                if (loader) {
                    loader.classList.add('hidden');
                    setTimeout(() => {
                        loader.style.display = 'none';
                    }, 500);
                }
            }, 300);
        }
        
        // Hide loader when everything is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                setTimeout(hideGlobalLoader, 1200);
            });
        } else {
            setTimeout(hideGlobalLoader, 1200);
        }
        
        // Also hide on window load
        window.addEventListener('load', () => {
            setTimeout(hideGlobalLoader, 1500);
        });
    })();
    </script>
    <div class="login-card">
        <h2>Login</h2>
        <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
                <?= $error ?>
            </div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control" placeholder="Enter your username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<!DOCTYPE html>
<html>
<head>
    <title>API Endpoint Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
        pre { background: #f5f5f5; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>API Endpoint Test Results</h1>
    
    <div class="test-section">
        <h2>1. Testing api_fallback.php?endpoint=customers</h2>
        <?php
        try {
            $_GET['endpoint'] = 'customers';
            ob_start();
            include 'api_fallback.php';
            $result = ob_get_clean();
            $json = json_decode($result, true);
            if ($json && $json['success']) {
                echo '<p class="success">✓ Working! Source: ' . $json['source'] . '</p>';
                echo '<p>Found ' . count($json['data']) . ' customers</p>';
            } else {
                echo '<p class="error">✗ Failed</p>';
            }
            echo '<pre>' . htmlspecialchars($result) . '</pre>';
        } catch (Exception $e) {
            echo '<p class="error">✗ Error: ' . $e->getMessage() . '</p>';
        }
        ?>
    </div>
    
    <div class="test-section">
        <h2>2. Testing api_fallback.php?endpoint=invoices</h2>
        <?php
        try {
            $_GET['endpoint'] = 'invoices';
            $_GET['page'] = '1';
            ob_start();
            include 'api_fallback.php';
            $result = ob_get_clean();
            $json = json_decode($result, true);
            if ($json && $json['success']) {
                echo '<p class="success">✓ Working! Source: ' . $json['source'] . '</p>';
                echo '<p>Found ' . count($json['data']) . ' invoices</p>';
            } else {
                echo '<p class="error">✗ Failed</p>';
            }
            echo '<pre>' . htmlspecialchars($result) . '</pre>';
        } catch (Exception $e) {
            echo '<p class="error">✗ Error: ' . $e->getMessage() . '</p>';
        }
        ?>
    </div>
    
    <div class="test-section">
        <h2>3. Testing fetch_invoices_advanced.php</h2>
        <?php
        try {
            $_GET['page'] = '1';
            ob_start();
            include 'fetch_invoices_advanced.php';
            $result = ob_get_clean();
            $json = json_decode($result, true);
            if ($json && $json['success']) {
                echo '<p class="success">✓ Working! Source: ' . $json['source'] . '</p>';
                echo '<p>Found ' . count($json['data']) . ' invoices</p>';
            } else {
                echo '<p class="error">✗ Failed</p>';
            }
            echo '<pre>' . htmlspecialchars($result) . '</pre>';
        } catch (Exception $e) {
            echo '<p class="error">✗ Error: ' . $e->getMessage() . '</p>';
        }
        ?>
    </div>
    
    <h2>Next Steps:</h2>
    <ul>
        <li>If all endpoints show "Working!" with "Source: mock", the fallback is working correctly</li>
        <li>Clear your browser cache and refresh the Generate Invoice page</li>
        <li>If you see "Source: database", then MySQL is working</li>
        <li>To start MySQL: Open XAMPP/WAMP or run <code>net start mysql</code></li>
    </ul>
</body>
</html>

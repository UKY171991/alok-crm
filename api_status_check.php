<!DOCTYPE html>
<html>
<head>
    <title>API Status Check - Courier CRM</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .success { 
            color: #28a745; 
            font-weight: bold;
        }
        .error { 
            color: #dc3545; 
            font-weight: bold;
        }
        .warning {
            color: #ffc107;
            font-weight: bold;
        }
        .test-section { 
            margin: 20px 0; 
            padding: 15px; 
            border: 1px solid #ddd; 
            border-radius: 5px;
            background: #f9f9f9;
        }
        pre { 
            background: #f8f9fa; 
            padding: 10px; 
            overflow-x: auto; 
            border-radius: 3px;
            border: 1px solid #e9ecef;
            max-height: 300px;
            overflow-y: auto;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            color: white;
            font-size: 12px;
            font-weight: bold;
        }
        .status-success { background-color: #28a745; }
        .status-error { background-color: #dc3545; }
        .status-warning { background-color: #ffc107; color: #000; }
        
        .ajax-test {
            margin: 20px 0;
            padding: 15px;
            border: 2px solid #007bff;
            border-radius: 5px;
            background: #e7f3ff;
        }
        
        button {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
        }
        
        button:hover {
            background: #0056b3;
        }
        
        #ajaxResults {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        <h1>🔧 Courier CRM - API Status Check</h1>
        <p><strong>Date:</strong> <?= date('Y-m-d H:i:s') ?> | <strong>Server:</strong> <?= $_SERVER['HTTP_HOST'] ?? 'Unknown' ?></p>
        
        <div class="ajax-test">
            <h2>🌐 Live AJAX Tests</h2>
            <p>These tests simulate the actual browser requests that were failing:</p>
            <button onclick="testCustomersAPI()">Test Customers API</button>
            <button onclick="testInvoicesAPI()">Test Invoices API</button>
            <button onclick="testBothAPIs()">Test Both APIs</button>
            <div id="ajaxResults"></div>
        </div>
        
        <div class="test-section">
            <h2>1. 📡 Testing api_fallback.php - Customers Endpoint</h2>
            <?php
            try {
                // Simulate the exact AJAX call for customers
                $_GET['endpoint'] = 'customers';
                $_GET['_t'] = time(); // Cache busting
                
                ob_start();
                include 'api_fallback.php';
                $result = ob_get_clean();
                
                $json = json_decode($result, true);
                if ($json && $json['success']) {
                    $source = $json['source'] ?? 'unknown';
                    $count = count($json['data']);
                    $message = $json['message'] ?? '';
                    
                    if ($source === 'database') {
                        echo '<span class="status-badge status-success">✓ DATABASE CONNECTED</span>';
                        echo '<p class="success">Perfect! Database is working and returned ' . $count . ' customers.</p>';
                    } else {
                        echo '<span class="status-badge status-warning">⚠ USING DEMO DATA</span>';
                        echo '<p class="warning">API is working with demo data (' . $count . ' customers). Database connection not available.</p>';
                        if ($message) echo '<p><em>' . htmlspecialchars($message) . '</em></p>';
                    }
                } else {
                    echo '<span class="status-badge status-error">✗ FAILED</span>';
                    echo '<p class="error">API failed to return valid data.</p>';
                }
                echo '<details><summary>View Full Response</summary><pre>' . htmlspecialchars($result) . '</pre></details>';
            } catch (Exception $e) {
                echo '<span class="status-badge status-error">✗ ERROR</span>';
                echo '<p class="error">Exception: ' . htmlspecialchars($e->getMessage()) . '</p>';
            }
            ?>
        </div>
        
        <div class="test-section">
            <h2>2. 📊 Testing api_fallback.php - Invoices Endpoint</h2>
            <?php
            try {
                // Reset and set up for invoices test
                unset($_GET['endpoint']);
                $_GET['endpoint'] = 'invoices';
                $_GET['page'] = '1';
                $_GET['_t'] = time();
                
                ob_start();
                include 'api_fallback.php';
                $result = ob_get_clean();
                
                $json = json_decode($result, true);
                if ($json && $json['success']) {
                    $source = $json['source'] ?? 'unknown';
                    $count = count($json['data']);
                    $pagination = $json['pagination'] ?? [];
                    $message = $json['message'] ?? '';
                    
                    if ($source === 'database') {
                        echo '<span class="status-badge status-success">✓ DATABASE CONNECTED</span>';
                        echo '<p class="success">Perfect! Database is working and returned ' . $count . ' invoices.</p>';
                    } else {
                        echo '<span class="status-badge status-warning">⚠ USING DEMO DATA</span>';
                        echo '<p class="warning">API is working with demo data (' . $count . ' invoices). Database connection not available.</p>';
                        if ($message) echo '<p><em>' . htmlspecialchars($message) . '</em></p>';
                    }
                    
                    if ($pagination) {
                        echo '<p><strong>Pagination:</strong> Page ' . $pagination['current_page'] . ' of ' . $pagination['total_pages'] . 
                             ' (Total: ' . $pagination['total_records'] . ' records)</p>';
                    }
                } else {
                    echo '<span class="status-badge status-error">✗ FAILED</span>';
                    echo '<p class="error">API failed to return valid data.</p>';
                }
                echo '<details><summary>View Full Response</summary><pre>' . htmlspecialchars($result) . '</pre></details>';
            } catch (Exception $e) {
                echo '<span class="status-badge status-error">✗ ERROR</span>';
                echo '<p class="error">Exception: ' . htmlspecialchars($e->getMessage()) . '</p>';
            }
            ?>
        </div>
        
        <div class="test-section">
            <h2>3. 🔍 Environment Information</h2>
            <table border="1" cellpadding="5" style="border-collapse: collapse; width: 100%;">
                <tr><td><strong>PHP Version</strong></td><td><?= PHP_VERSION ?></td></tr>
                <tr><td><strong>Server Software</strong></td><td><?= $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' ?></td></tr>
                <tr><td><strong>Document Root</strong></td><td><?= $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown' ?></td></tr>
                <tr><td><strong>Current Script</strong></td><td><?= $_SERVER['SCRIPT_NAME'] ?? 'Unknown' ?></td></tr>
                <tr><td><strong>MySQL Extension</strong></td><td><?= extension_loaded('mysqli') ? '✓ Loaded' : '✗ Not Loaded' ?></td></tr>
                <tr><td><strong>Error Reporting</strong></td><td><?= error_reporting() ?></td></tr>
            </table>
        </div>
        
        <div class="test-section">
            <h2>4. 📋 Next Steps</h2>
            <div style="background: #e7f3ff; padding: 15px; border-radius: 5px; border-left: 4px solid #007bff;">
                <h3>✅ If APIs show "DATABASE CONNECTED":</h3>
                <ul>
                    <li>Everything is working perfectly!</li>
                    <li>Go back to your Generate Invoice page and refresh</li>
                    <li>The error messages should be gone</li>
                </ul>
                
                <h3>⚠️ If APIs show "USING DEMO DATA":</h3>
                <ul>
                    <li>The fallback system is working correctly</li>
                    <li>Your Generate Invoice page should now work with demo data</li>
                    <li>You'll see a blue "demo data" notification instead of error messages</li>
                    <li>To enable database: Start MySQL service or XAMPP/WAMP</li>
                </ul>
                
                <h3>❌ If you see any "FAILED" or "ERROR" status:</h3>
                <ul>
                    <li>Check the error details in the response</li>
                    <li>Verify file permissions</li>
                    <li>Check PHP error logs</li>
                </ul>
            </div>
        </div>
    </div>
    
    <script>
    function testCustomersAPI() {
        $('#ajaxResults').html('<p>Testing customers API...</p>');
        
        $.ajax({
            url: 'api_fallback.php?endpoint=customers&_t=' + new Date().getTime(),
            type: 'GET',
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                if (response.success) {
                    $('#ajaxResults').html(
                        '<h3 style="color: green;">✓ Customers API Success</h3>' +
                        '<p><strong>Source:</strong> ' + response.source + '</p>' +
                        '<p><strong>Count:</strong> ' + response.data.length + ' customers</p>' +
                        (response.message ? '<p><strong>Message:</strong> ' + response.message + '</p>' : '') +
                        '<details><summary>View Data</summary><pre>' + JSON.stringify(response, null, 2) + '</pre></details>'
                    );
                } else {
                    $('#ajaxResults').html('<h3 style="color: red;">✗ Customers API Failed</h3><p>' + response.message + '</p>');
                }
            },
            error: function(xhr, status, error) {
                $('#ajaxResults').html(
                    '<h3 style="color: red;">✗ Customers API Error</h3>' +
                    '<p><strong>Status:</strong> ' + xhr.status + '</p>' +
                    '<p><strong>Error:</strong> ' + error + '</p>' +
                    '<p><strong>Response:</strong> ' + xhr.responseText + '</p>'
                );
            }
        });
    }
    
    function testInvoicesAPI() {
        $('#ajaxResults').html('<p>Testing invoices API...</p>');
        
        $.ajax({
            url: 'api_fallback.php?endpoint=invoices&page=1&_t=' + new Date().getTime(),
            type: 'GET',
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                if (response.success) {
                    $('#ajaxResults').html(
                        '<h3 style="color: green;">✓ Invoices API Success</h3>' +
                        '<p><strong>Source:</strong> ' + response.source + '</p>' +
                        '<p><strong>Count:</strong> ' + response.data.length + ' invoices</p>' +
                        (response.pagination ? '<p><strong>Pagination:</strong> Page ' + response.pagination.current_page + ' of ' + response.pagination.total_pages + '</p>' : '') +
                        (response.message ? '<p><strong>Message:</strong> ' + response.message + '</p>' : '') +
                        '<details><summary>View Data</summary><pre>' + JSON.stringify(response, null, 2) + '</pre></details>'
                    );
                } else {
                    $('#ajaxResults').html('<h3 style="color: red;">✗ Invoices API Failed</h3><p>' + response.message + '</p>');
                }
            },
            error: function(xhr, status, error) {
                $('#ajaxResults').html(
                    '<h3 style="color: red;">✗ Invoices API Error</h3>' +
                    '<p><strong>Status:</strong> ' + xhr.status + '</p>' +
                    '<p><strong>Error:</strong> ' + error + '</p>' +
                    '<p><strong>Response:</strong> ' + xhr.responseText + '</p>'
                );
            }
        });
    }
    
    function testBothAPIs() {
        $('#ajaxResults').html('<p>Testing both APIs...</p>');
        
        Promise.all([
            $.ajax({ url: 'api_fallback.php?endpoint=customers&_t=' + new Date().getTime(), type: 'GET', dataType: 'json' }),
            $.ajax({ url: 'api_fallback.php?endpoint=invoices&page=1&_t=' + new Date().getTime(), type: 'GET', dataType: 'json' })
        ]).then(function(results) {
            const customersResult = results[0];
            const invoicesResult = results[1];
            
            $('#ajaxResults').html(
                '<h3 style="color: green;">✓ Both APIs Success</h3>' +
                '<p><strong>Customers:</strong> ' + customersResult.data.length + ' items (Source: ' + customersResult.source + ')</p>' +
                '<p><strong>Invoices:</strong> ' + invoicesResult.data.length + ' items (Source: ' + invoicesResult.source + ')</p>' +
                '<p style="color: green; font-weight: bold;">🎉 Your CRM APIs are working correctly!</p>'
            );
        }).catch(function(error) {
            $('#ajaxResults').html('<h3 style="color: red;">✗ API Test Failed</h3><p>One or both APIs failed. Check individual tests above.</p>');
        });
    }
    </script>
</body>
</html>

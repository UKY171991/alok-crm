<?php
require_once 'inc/config.php';
require_once 'inc/db.php';

// Get filter parameters
$customer_id = isset($_GET['customer_id']) ? $_GET['customer_id'] : '';
$zone_id = isset($_GET['zone_id']) ? $_GET['zone_id'] : '';
$mode_id = isset($_GET['mode_id']) ? $_GET['mode_id'] : '';
$consignment_type_id = isset($_GET['consignment_type_id']) ? $_GET['consignment_type_id'] : '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Build the WHERE clause
    $where_conditions = array();
    $params = array();
    
    if (!empty($customer_id)) {
        $where_conditions[] = "cr.customer_id = :customer_id";
        $params[':customer_id'] = $customer_id;
    }
    
    if (!empty($zone_id)) {
        $where_conditions[] = "cr.zone_id = :zone_id";
        $params[':zone_id'] = $zone_id;
    }
    
    if (!empty($mode_id)) {
        $where_conditions[] = "cr.mode_id = :mode_id";
        $params[':mode_id'] = $mode_id;
    }
    
    if (!empty($consignment_type_id)) {
        $where_conditions[] = "cr.consignment_type_id = :consignment_type_id";
        $params[':consignment_type_id'] = $consignment_type_id;
    }
    
    $where_clause = '';
    if (!empty($where_conditions)) {
        $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
    }
    
    // Main query to fetch customer rates for printing
    $sql = "SELECT 
                cr.rate_id,
                cr.customer_id,
                c.customer_name,
                c.customer_email,
                c.customer_phone,
                c.customer_address,
                cr.zone_id,
                d.zone_name,
                cr.mode_id,
                m.mode_name,
                cr.consignment_type_id,
                ct.type_name,
                cr.weight_from,
                cr.weight_to,
                cr.rate_per_kg,
                cr.minimum_rate,
                cr.status,
                cr.created_at,
                cr.updated_at
            FROM customer_rates cr
            LEFT JOIN customers c ON cr.customer_id = c.customer_id
            LEFT JOIN destinations d ON cr.zone_id = d.id
            LEFT JOIN cr_modes m ON cr.mode_id = m.mode_id
            LEFT JOIN cr_consignment_types ct ON cr.consignment_type_id = ct.consignment_type_id
            $where_clause
            ORDER BY c.customer_name, d.zone_name, m.mode_name, ct.type_name, cr.weight_from";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Set headers for PDF download
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="customer_rate_chart_' . date('Y-m-d') . '.pdf"');

// You can use a PDF library like TCPDF or mPDF here
// For now, let's create a simple HTML that can be converted to PDF
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Customer Rate Chart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #333;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            margin: 0;
        }
        .filters {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .filters h3 {
            margin-top: 0;
            color: #333;
        }
        .filter-item {
            display: inline-block;
            margin-right: 20px;
            margin-bottom: 10px;
        }
        .filter-label {
            font-weight: bold;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            color: #333;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .customer-section {
            page-break-inside: avoid;
            margin-bottom: 30px;
        }
        .customer-header {
            background-color: #e9ecef;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .customer-header h3 {
            margin: 0;
            color: #333;
        }
        .customer-details {
            font-size: 11px;
            color: #666;
        }
        .no-data {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 40px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Customer Rate Chart</h1>
        <p>Generated on: <?php echo date('F j, Y g:i A'); ?></p>
    </div>
    
    <?php if (!empty($customer_id) || !empty($zone_id) || !empty($mode_id) || !empty($consignment_type_id)): ?>
    <div class="filters">
        <h3>Applied Filters:</h3>
        <?php if (!empty($customer_id)): ?>
            <?php
            $customer_stmt = $pdo->prepare("SELECT customer_name FROM customers WHERE customer_id = :customer_id");
            $customer_stmt->execute([':customer_id' => $customer_id]);
            $customer = $customer_stmt->fetch(PDO::FETCH_ASSOC);
            ?>
            <div class="filter-item">
                <span class="filter-label">Customer:</span> <?php echo htmlspecialchars($customer['customer_name']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($zone_id)): ?>
            <?php
            $zone_stmt = $pdo->prepare("SELECT zone_name FROM destinations WHERE id = :zone_id");
            $zone_stmt->execute([':zone_id' => $zone_id]);
            $zone = $zone_stmt->fetch(PDO::FETCH_ASSOC);
            ?>
            <div class="filter-item">
                <span class="filter-label">Zone:</span> <?php echo htmlspecialchars($zone['zone_name']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($mode_id)): ?>
            <?php
            $mode_stmt = $pdo->prepare("SELECT mode_name FROM cr_modes WHERE mode_id = :mode_id");
            $mode_stmt->execute([':mode_id' => $mode_id]);
            $mode = $mode_stmt->fetch(PDO::FETCH_ASSOC);
            ?>
            <div class="filter-item">
                <span class="filter-label">Mode:</span> <?php echo htmlspecialchars($mode['mode_name']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($consignment_type_id)): ?>
            <?php
            $type_stmt = $pdo->prepare("SELECT type_name FROM cr_consignment_types WHERE consignment_type_id = :consignment_type_id");
            $type_stmt->execute([':consignment_type_id' => $consignment_type_id]);
            $type = $type_stmt->fetch(PDO::FETCH_ASSOC);
            ?>
            <div class="filter-item">
                <span class="filter-label">Consignment Type:</span> <?php echo htmlspecialchars($type['type_name']); ?>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <?php if (empty($rates)): ?>
        <div class="no-data">
            <p>No customer rates found for the selected criteria.</p>
        </div>
    <?php else: ?>
        <?php
        // Group rates by customer
        $grouped_rates = array();
        foreach ($rates as $rate) {
            $customer_key = $rate['customer_id'];
            if (!isset($grouped_rates[$customer_key])) {
                $grouped_rates[$customer_key] = array(
                    'customer_info' => $rate,
                    'rates' => array()
                );
            }
            $grouped_rates[$customer_key]['rates'][] = $rate;
        }
        ?>
        
        <?php foreach ($grouped_rates as $customer_data): ?>
            <div class="customer-section">
                <div class="customer-header">
                    <h3><?php echo htmlspecialchars($customer_data['customer_info']['customer_name']); ?></h3>
                    <div class="customer-details">
                        <?php if (!empty($customer_data['customer_info']['customer_email'])): ?>
                            Email: <?php echo htmlspecialchars($customer_data['customer_info']['customer_email']); ?> | 
                        <?php endif; ?>
                        <?php if (!empty($customer_data['customer_info']['customer_phone'])): ?>
                            Phone: <?php echo htmlspecialchars($customer_data['customer_info']['customer_phone']); ?>
                        <?php endif; ?>
                        <?php if (!empty($customer_data['customer_info']['customer_address'])): ?>
                            <br>Address: <?php echo htmlspecialchars($customer_data['customer_info']['customer_address']); ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>Zone</th>
                            <th>Mode</th>
                            <th>Consignment Type</th>
                            <th class="text-center">Weight From (kg)</th>
                            <th class="text-center">Weight To (kg)</th>
                            <th class="text-right">Rate/kg (₹)</th>
                            <th class="text-right">Min Rate (₹)</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customer_data['rates'] as $rate): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($rate['zone_name']); ?></td>
                                <td><?php echo htmlspecialchars($rate['mode_name']); ?></td>
                                <td><?php echo htmlspecialchars($rate['type_name']); ?></td>
                                <td class="text-center"><?php echo number_format($rate['weight_from'], 2); ?></td>
                                <td class="text-center"><?php echo number_format($rate['weight_to'], 2); ?></td>
                                <td class="text-right">₹<?php echo number_format($rate['rate_per_kg'], 2); ?></td>
                                <td class="text-right">₹<?php echo number_format($rate['minimum_rate'], 2); ?></td>
                                <td class="text-center">
                                    <span style="color: <?php echo $rate['status'] == 'active' ? 'green' : 'red'; ?>">
                                        <?php echo ucfirst($rate['status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <script>
        // Auto-print when page loads
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

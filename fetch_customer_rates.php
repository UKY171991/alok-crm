<?php
require_once 'inc/config.php';
require_once 'inc/db.php';

// Check if we should return HTML or JSON
$return_html = !isset($_GET['json']) || $_GET['json'] !== 'true';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get filter parameters (supporting both old and new parameter names)
    $customer_id = isset($_GET['customer_id']) ? $_GET['customer_id'] : '';
    $zone_id = isset($_GET['zone_id']) ? $_GET['zone_id'] : (isset($_GET['zone_wise']) ? $_GET['zone_wise'] : '');
    $mode_id = isset($_GET['mode_id']) ? $_GET['mode_id'] : (isset($_GET['mode']) ? $_GET['mode'] : '');
    $consignment_type_id = isset($_GET['consignment_type_id']) ? $_GET['consignment_type_id'] : (isset($_GET['consignment_type']) ? $_GET['consignment_type'] : '');
    
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
    
    // Main query to fetch customer rates
    $sql = "SELECT 
                cr.rate_id,
                cr.customer_id,
                c.customer_name,
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
    
    if ($return_html) {
        // Return HTML for the existing UI
        if (empty($rates)) {
            echo '<tr><td colspan="13" style="text-align:center;">No customer rates found.</td></tr>';
        } else {
            foreach ($rates as $rate) {
                $statusClass = $rate['status'] === 'active' ? 'active' : 'inactive';
                echo '<tr>';
                echo '<td>' . htmlspecialchars($rate['mode_name'] ?: 'N/A') . '</td>';
                echo '<td>' . htmlspecialchars($rate['type_name'] ?: 'N/A') . '</td>';
                echo '<td>' . htmlspecialchars($rate['zone_name'] ?: 'N/A') . '</td>';
                echo '<td>N/A</td>'; // State - not implemented yet
                echo '<td>N/A</td>'; // City - not implemented yet
                echo '<td>' . number_format($rate['weight_from'], 3) . '</td>';
                echo '<td>' . number_format($rate['weight_to'], 3) . '</td>';
                echo '<td>' . number_format($rate['rate_per_kg'], 2) . '</td>';
                echo '<td>0.000</td>'; // Additional weight - not implemented yet
                echo '<td>0.000</td>'; // Additional weight kg - not implemented yet
                echo '<td>' . number_format($rate['minimum_rate'], 2) . '</td>';
                echo '<td><button class="edit-rate-btn" data-id="' . $rate['rate_id'] . '" data-rate="' . $rate['rate_per_kg'] . '">Edit</button></td>';
                echo '<td><button class="delete-rate-btn" data-id="' . $rate['rate_id'] . '">Delete</button></td>';
                echo '</tr>';
            }
        }
    } else {
        // Return JSON for API usage
        header('Content-Type: application/json');
        echo json_encode($rates);
    }
    
} catch(PDOException $e) {
    if ($return_html) {
        echo '<tr><td colspan="13" style="text-align:center;color:red;">Database error: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
    } else {
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(array('error' => 'Database error: ' . $e->getMessage()));
    }
}
?>

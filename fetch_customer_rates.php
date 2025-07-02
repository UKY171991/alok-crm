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
    $zone_wise = isset($_GET['zone_id']) ? $_GET['zone_id'] : (isset($_GET['zone_wise']) ? $_GET['zone_wise'] : '');
    $mode = isset($_GET['mode_id']) ? $_GET['mode_id'] : (isset($_GET['mode']) ? $_GET['mode'] : '');
    $consignment_type = isset($_GET['consignment_type_id']) ? $_GET['consignment_type_id'] : (isset($_GET['consignment_type']) ? $_GET['consignment_type'] : '');
    
    // Build the WHERE clause
    $where_conditions = array();
    $params = array();
    
    if (!empty($customer_id)) {
        $where_conditions[] = "cr.customer_id = :customer_id";
        $params[':customer_id'] = $customer_id;
    }
    
    if (!empty($zone_wise)) {
        // Handle zone filtering - could be zone name or zone id
        if (is_numeric($zone_wise)) {
            // If it's numeric, try to find the zone name from destinations table
            $zone_stmt = $pdo->prepare("SELECT zone_name FROM destinations WHERE id = :zone_id");
            $zone_stmt->execute([':zone_id' => $zone_wise]);
            $zone_result = $zone_stmt->fetch(PDO::FETCH_ASSOC);
            if ($zone_result) {
                $where_conditions[] = "cr.zone_wise = :zone_wise";
                $params[':zone_wise'] = $zone_result['zone_name'];
            }
        } else {
            $where_conditions[] = "cr.zone_wise = :zone_wise";
            $params[':zone_wise'] = $zone_wise;
        }
    }
    
    if (!empty($mode)) {
        $where_conditions[] = "cr.mode = :mode";
        $params[':mode'] = $mode;
    }
    
    if (!empty($consignment_type)) {
        $where_conditions[] = "cr.consignment_type = :consignment_type";
        $params[':consignment_type'] = $consignment_type;
    }
    
    $where_clause = '';
    if (!empty($where_conditions)) {
        $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
    }
    
    // Main query to fetch customer rates
    $sql = "SELECT 
                cr.id as rate_id,
                cr.customer_id,
                c.name as customer_name,
                cr.zone_wise as zone_id,
                d.zone_name,
                cr.mode,
                cr.mode as mode_name,
                cr.consignment_type,
                cr.consignment_type as type_name,
                cr.from_weight as weight_from,
                cr.to_weight as weight_to,
                cr.rate as rate_per_kg,
                cr.additional_rate as minimum_rate,
                'active' as status,
                cr.created_at,
                cr.updated_at
            FROM customer_rates cr
            LEFT JOIN customers c ON cr.customer_id = c.id
            LEFT JOIN destinations d ON cr.zone_wise = d.zone_name
            $where_clause
            ORDER BY c.name, cr.zone_wise, cr.mode, cr.consignment_type, cr.from_weight";
    
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

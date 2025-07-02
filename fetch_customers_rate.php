<?php
require_once 'inc/config.php';
require_once 'inc/db.php';

// Check if type parameter is provided (for the existing UI)
$type = isset($_GET['type']) ? $_GET['type'] : '';

if ($type) {
    // Handle single type requests for existing UI
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $html = '';
        
        switch ($type) {
            case 'customers':
                $stmt = $pdo->prepare("SELECT id as customer_id, name as customer_name FROM customers ORDER BY name");
                $stmt->execute();
                $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($customers as $customer) {
                    $html .= '<option value="' . $customer['customer_id'] . '">' . htmlspecialchars($customer['customer_name']) . '</option>';
                }
                break;
                
            case 'zones':
                $stmt = $pdo->prepare("SELECT id, zone_name FROM destinations ORDER BY zone_name");
                $stmt->execute();
                $zones = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($zones as $zone) {
                    $html .= '<option value="' . $zone['id'] . '">' . htmlspecialchars($zone['zone_name']) . '</option>';
                }
                break;
                
            case 'modes':
                $stmt = $pdo->prepare("SELECT mode_id, mode_name FROM cr_modes WHERE status = 'active' ORDER BY mode_name");
                $stmt->execute();
                $modes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($modes as $mode) {
                    $html .= '<option value="' . $mode['mode_id'] . '">' . htmlspecialchars($mode['mode_name']) . '</option>';
                }
                break;
                
            case 'consignment_types':
                $stmt = $pdo->prepare("SELECT consignment_type_id, type_name FROM cr_consignment_types WHERE status = 'active' ORDER BY type_name");
                $stmt->execute();
                $types = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($types as $ct) {
                    $html .= '<option value="' . $ct['consignment_type_id'] . '">' . htmlspecialchars($ct['type_name']) . '</option>';
                }
                break;
                
            default:
                $html = '<option value="">No data available</option>';
        }
        
        echo $html;
        
    } catch(PDOException $e) {
        echo '<option value="">Error loading data</option>';
    }
} else {
    // Return JSON response for new API format
    header('Content-Type: application/json');
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $response = array();
        
        // Fetch customers for dropdown
        $stmt = $pdo->prepare("SELECT id as customer_id, name as customer_name FROM customers ORDER BY name");
        $stmt->execute();
        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response['customers'] = $customers;
        
        // Fetch zones for dropdown
        $stmt = $pdo->prepare("SELECT id, zone_name FROM destinations ORDER BY zone_name");
        $stmt->execute();
        $zones = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response['zones'] = $zones;
        
        // Fetch modes for dropdown
        $stmt = $pdo->prepare("SELECT mode_id, mode_name FROM cr_modes WHERE status = 'active' ORDER BY mode_name");
        $stmt->execute();
        $modes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response['modes'] = $modes;
        
        // Fetch consignment types for dropdown
        $stmt = $pdo->prepare("SELECT consignment_type_id, type_name FROM cr_consignment_types WHERE status = 'active' ORDER BY type_name");
        $stmt->execute();
        $consignment_types = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response['consignment_types'] = $consignment_types;
        
        echo json_encode($response);
        
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(array('error' => 'Database error: ' . $e->getMessage()));
    }
}
?>

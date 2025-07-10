<?php
// Mock data for when database is not available
$mockCustomers = [
    ['id' => 1, 'name' => 'ABC Corporation', 'email' => 'abc@example.com', 'phone' => '9876543210', 'address' => '123 Business Street, Mumbai', 'gst_no' => 'GST001', 'hsn_code' => 'HSN001', 'pan_no' => 'PAN001', 'cin_no' => 'CIN001', 'aadhaar_no' => 'ADHAAR001', 'destination' => 'Mumbai', 'parcel_type' => 'Document', 'weight' => '1kg', 'price' => '100', 'service' => 'Express', 'shipment_type' => 'Standard'],
    ['id' => 2, 'name' => 'XYZ Ltd', 'email' => 'xyz@example.com', 'phone' => '9876543211', 'address' => '456 Commercial Road, Delhi', 'gst_no' => 'GST002', 'hsn_code' => 'HSN002', 'pan_no' => 'PAN002', 'cin_no' => 'CIN002', 'aadhaar_no' => 'ADHAAR002', 'destination' => 'Delhi', 'parcel_type' => 'Package', 'weight' => '2kg', 'price' => '150', 'service' => 'Standard', 'shipment_type' => 'Express'],
    ['id' => 3, 'name' => 'PQR Enterprises', 'email' => 'pqr@example.com', 'phone' => '9876543212', 'address' => '789 Industrial Area, Bangalore', 'gst_no' => 'GST003', 'hsn_code' => 'HSN003', 'pan_no' => 'PAN003', 'cin_no' => 'CIN003', 'aadhaar_no' => 'ADHAAR003', 'destination' => 'Bangalore', 'parcel_type' => 'Document', 'weight' => '0.5kg', 'price' => '80', 'service' => 'Express', 'shipment_type' => 'Standard']
];

try {
    include 'inc/db.php';
    
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $per_page = isset($_GET['per_page']) ? max(1, intval($_GET['per_page'])) : 10;
    $offset = ($page - 1) * $per_page;

    // Add search filter logic
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $where = '';
    if ($search !== '') {
        $search_esc = $conn->real_escape_string($search);
        $where = "WHERE name LIKE '%$search_esc%' OR phone LIKE '%$search_esc%' OR email LIKE '%$search_esc%' OR gst_no LIKE '%$search_esc%'";
    }

    $count_result = $conn->query("SELECT COUNT(*) as cnt FROM customers $where");
    $total = ($count_result && $row = $count_result->fetch_assoc()) ? intval($row['cnt']) : 0;
    $sql = "SELECT * FROM customers $where ORDER BY id DESC LIMIT $per_page OFFSET $offset";
$result = $conn->query($sql);
echo '<tr style="display:none"><td colspan="6" id="pagination-info" data-total="' . $total . '" data-page="' . $page . '" data-per_page="' . $per_page . '"></td></tr>';
$sr = 1;
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$sr}</td>";
    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
    echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
    echo "<td>" . htmlspecialchars($row['gst_no']) . "</td>";
    echo "<td>";
    echo "<button type='button' class='btn btn-info btn-sm view-details-btn me-1' data-id='{$row['id']}'>View</button> ";
    echo "<button type='button' class='btn btn-warning btn-sm edit-btn me-1' data-id='{$row['id']}' data-name='".htmlspecialchars($row['name'], ENT_QUOTES)."' data-address='".htmlspecialchars($row['address'], ENT_QUOTES)."' data-phone='".htmlspecialchars($row['phone'], ENT_QUOTES)."' data-email='".htmlspecialchars($row['email'], ENT_QUOTES)."' data-gst='".htmlspecialchars($row['gst_no'], ENT_QUOTES)."' data-hsn='".htmlspecialchars($row['hsn_code'], ENT_QUOTES)."' data-pan='".htmlspecialchars($row['pan_no'], ENT_QUOTES)."' data-cin='".htmlspecialchars($row['cin_no'], ENT_QUOTES)."' data-aadhaar='".htmlspecialchars($row['aadhaar_no'], ENT_QUOTES)."' data-destination='".htmlspecialchars($row['destination'], ENT_QUOTES)."' data-parcel_type='".htmlspecialchars($row['parcel_type'], ENT_QUOTES)."' data-weight='".htmlspecialchars($row['weight'], ENT_QUOTES)."' data-price='".htmlspecialchars($row['price'], ENT_QUOTES)."' data-service='".htmlspecialchars($row['service'], ENT_QUOTES)."' data-shipment_type='".htmlspecialchars($row['shipment_type'], ENT_QUOTES)."'>Edit</button> ";
    echo "<button type='button' class='btn btn-danger btn-sm delete-btn' data-id='{$row['id']}'>Delete</button>";
    echo "</td>";
    echo "</tr>";
    $sr++;
}

} catch (Exception $e) {
    // Database not available, use mock data
    error_log("fetch_customers.php using mock data: " . $e->getMessage());
    
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $per_page = isset($_GET['per_page']) ? max(1, intval($_GET['per_page'])) : 10;
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    
    // Filter mock data if search term provided
    $filteredCustomers = $mockCustomers;
    if ($search !== '') {
        $filteredCustomers = array_filter($mockCustomers, function($customer) use ($search) {
            return stripos($customer['name'], $search) !== false || 
                   stripos($customer['phone'], $search) !== false || 
                   stripos($customer['email'], $search) !== false;
        });
    }
    
    $total = count($filteredCustomers);
    $offset = ($page - 1) * $per_page;
    $pageData = array_slice($filteredCustomers, $offset, $per_page);
    
    echo '<tr style="display:none"><td colspan="6" id="pagination-info" data-total="' . $total . '" data-page="' . $page . '" data-per_page="' . $per_page . '"></td></tr>';
    
    $sr = 1;
    foreach ($pageData as $row) {
        echo "<tr>";
        echo "<td>{$sr}</td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . htmlspecialchars($row['gst_no']) . "</td>";
        echo "<td>";
        echo "<button type='button' class='btn btn-info btn-sm view-details-btn me-1' data-id='{$row['id']}'>View</button> ";
        echo "<button type='button' class='btn btn-warning btn-sm edit-btn me-1' data-id='{$row['id']}' data-name='".htmlspecialchars($row['name'], ENT_QUOTES)."' data-address='".htmlspecialchars($row['address'], ENT_QUOTES)."' data-phone='".htmlspecialchars($row['phone'], ENT_QUOTES)."' data-email='".htmlspecialchars($row['email'], ENT_QUOTES)."' data-gst='".htmlspecialchars($row['gst_no'], ENT_QUOTES)."' data-hsn='".htmlspecialchars($row['hsn_code'], ENT_QUOTES)."' data-pan='".htmlspecialchars($row['pan_no'], ENT_QUOTES)."' data-cin='".htmlspecialchars($row['cin_no'], ENT_QUOTES)."' data-aadhaar='".htmlspecialchars($row['aadhaar_no'], ENT_QUOTES)."' data-destination='".htmlspecialchars($row['destination'], ENT_QUOTES)."' data-parcel_type='".htmlspecialchars($row['parcel_type'], ENT_QUOTES)."' data-weight='".htmlspecialchars($row['weight'], ENT_QUOTES)."' data-price='".htmlspecialchars($row['price'], ENT_QUOTES)."' data-service='".htmlspecialchars($row['service'], ENT_QUOTES)."' data-shipment_type='".htmlspecialchars($row['shipment_type'], ENT_QUOTES)."'>Edit</button> ";
        echo "<button type='button' class='btn btn-danger btn-sm delete-btn' data-id='{$row['id']}'>Delete</button>";
        echo "</td>";
        echo "</tr>";
        $sr++;
    }
}
$conn->close();

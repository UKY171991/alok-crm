<?php
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
$conn->close();

<?php
include 'inc/db.php';

if (!isset($_GET['id'])) {
    echo "<p>Invalid request. Customer ID is missing.</p>";
    exit;
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM customers WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "<div class='card shadow rounded-4 border-0 mb-3 position-relative animate__animated animate__fadeIn'>";
    // Close button (top right)
    echo "<button type='button' class='btn-close position-absolute top-0 end-0 m-3' data-bs-dismiss='modal' aria-label='Close'></button>";
    echo "<div class='card-header bg-primary text-white rounded-top-4 d-flex align-items-center' style='min-height:56px;'>";
    echo "<h4 class='mb-0 flex-grow-1'><i class='fas fa-user-circle me-2'></i>Customer Details</h4>";
    echo "</div>";
    echo "<div class='card-body bg-light-subtle p-4'>";
    echo "<div class='row g-3 mb-2'>";
    // Info cards with colored border and icon
    $fields = [
        ['icon' => 'fa-user', 'label' => 'Name', 'val' => $row['name'], 'color' => 'primary'],
        ['icon' => 'fa-map-marker-alt', 'label' => 'Address', 'val' => $row['address'], 'color' => 'info'],
        ['icon' => 'fa-phone', 'label' => 'Phone', 'val' => $row['phone'], 'color' => 'success'],
        ['icon' => 'fa-envelope', 'label' => 'Email', 'val' => $row['email'], 'color' => 'warning'],
        ['icon' => 'fa-file-invoice', 'label' => 'GST No', 'val' => $row['gst_no'], 'color' => 'danger'],
        ['icon' => 'fa-barcode', 'label' => 'HSN Code', 'val' => $row['hsn_code'], 'color' => 'secondary'],
        ['icon' => 'fa-id-badge', 'label' => 'PAN No', 'val' => $row['pan_no'], 'color' => 'primary'],
        ['icon' => 'fa-building', 'label' => 'CIN No', 'val' => $row['cin_no'], 'color' => 'info'],
        ['icon' => 'fa-id-card-alt', 'label' => 'Aadhaar No', 'val' => $row['aadhaar_no'], 'color' => 'success'],
    ];
    foreach ($fields as $f) {
        echo "<div class='col-md-4 col-12 mb-2'>";
        echo "<div class='info-card d-flex align-items-center p-3 h-100 bg-white rounded-3 shadow-sm border-start border-4 border-{$f['color']} animate__animated animate__fadeInUp' style='min-height:70px;'>";
        echo "<span class='me-3 fs-3 text-{$f['color']}'><i class='fas {$f['icon']}'></i></span>";
        echo "<div><div class='fw-bold text-{$f['color']} small'>{$f['label']}</div>";
        echo (!empty($f['val']) ? "<div class='fs-6 text-dark fw-semibold'>" . nl2br(htmlspecialchars($f['val'])) . "</div>" : "<span class='badge bg-secondary'>N/A</span>");
        echo "</div></div></div>";
    }
    echo "</div>"; // row
    echo "<div class='d-flex align-items-center my-4'><hr class='flex-grow-1'><span class='mx-3 text-primary fw-bold'><i class='fas fa-boxes me-2'></i>Parcel Details</span><hr class='flex-grow-1'></div>";
    $destinations = json_decode($row['destination'], true);
    $parcelTypes = json_decode($row['parcel_type'], true);
    $weights = json_decode($row['weight'], true);
    $prices = json_decode($row['price'], true);
    $services = isset($row['service']) ? json_decode($row['service'], true) : [];
    $shipmentTypes = isset($row['shipment_type']) ? json_decode($row['shipment_type'], true) : [];
    if ($destinations && is_array($destinations)) {
        echo "<div class='table-responsive'><table class='table table-bordered table-striped align-middle rounded-3 overflow-hidden'>";
        echo "<thead class='table-primary'><tr><th>Destination</th><th>Parcel Type</th><th>Weight</th><th>Service</th><th>Shipment Type</th><th>Price</th></tr></thead><tbody>";
        foreach ($destinations as $index => $destination) {
            echo "<tr>";
            echo "<td>" . (!empty($destination) ? htmlspecialchars($destination) : "<span class='badge bg-secondary'>N/A</span>") . "</td>";
            echo "<td>" . (!empty($parcelTypes[$index]) ? htmlspecialchars($parcelTypes[$index]) : "<span class='badge bg-secondary'>N/A</span>") . "</td>";
            echo "<td>" . (!empty($weights[$index]) ? htmlspecialchars($weights[$index]) : "<span class='badge bg-secondary'>N/A</span>") . "</td>";
            echo "<td>" . (!empty($services[$index]) ? htmlspecialchars($services[$index]) : "<span class='badge bg-secondary'>N/A</span>") . "</td>";
            echo "<td>" . (!empty($shipmentTypes[$index]) ? htmlspecialchars($shipmentTypes[$index]) : "<span class='badge bg-secondary'>N/A</span>") . "</td>";
            echo "<td>" . (!empty($prices[$index]) ? htmlspecialchars($prices[$index]) : "<span class='badge bg-secondary'>N/A</span>") . "</td>";
            echo "</tr>";
        }
        echo "</tbody></table></div>";
    }
    echo "</div>"; // card-body
    echo "</div>"; // card
    echo "<style>.info-card:hover{box-shadow:0 0.5rem 1.5rem rgba(0,0,0,0.10)!important;transform:translateY(-2px);transition:all .2s;}</style>";
} else {
    echo "<p>Customer not found.</p>";
}

$conn->close();
?>
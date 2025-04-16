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
    echo "<h5>Customer Details</h5>";
    echo "<p><strong>Name:</strong> " . htmlspecialchars($row['name']) . "</p>";
    echo "<p><strong>Address:</strong> " . nl2br(htmlspecialchars($row['address'])) . "</p>";
    echo "<p><strong>Phone:</strong> " . htmlspecialchars($row['phone']) . "</p>";
    echo "<p><strong>Email:</strong> " . htmlspecialchars($row['email']) . "</p>";
    echo "<p><strong>GST No:</strong> " . htmlspecialchars($row['gst_no']) . "</p>";
    echo "<p><strong>HSN Code:</strong> " . htmlspecialchars($row['hsn_code']) . "</p>";
    echo "<p><strong>PAN No:</strong> " . htmlspecialchars($row['pan_no']) . "</p>";
    echo "<p><strong>CIN No:</strong> " . htmlspecialchars($row['cin_no']) . "</p>";
    echo "<p><strong>Aadhaar No:</strong> " . htmlspecialchars($row['aadhaar_no']) . "</p>";

    $destinations = json_decode($row['destination'], true);
    $parcelTypes = json_decode($row['parcel_type'], true);
    $weights = json_decode($row['weight'], true);
    $prices = json_decode($row['price'], true);

    if ($destinations && is_array($destinations)) {
        echo "<h5>Parcel Details</h5>";
        echo "<table class='table table-bordered'>";
        echo "<thead><tr><th>Destination</th><th>Parcel Type</th><th>Weight</th><th>Price</th></tr></thead><tbody>";
        foreach ($destinations as $index => $destination) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($destination) . "</td>";
            echo "<td>" . htmlspecialchars($parcelTypes[$index]) . "</td>";
            echo "<td>" . htmlspecialchars($weights[$index]) . "</td>";
            echo "<td>" . htmlspecialchars($prices[$index]) . "</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    }
} else {
    echo "<p>Customer not found.</p>";
}

$conn->close();
?>
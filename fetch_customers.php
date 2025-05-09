<?php
include 'inc/db.php';
$sql = "SELECT * FROM customers ORDER BY id DESC";
$result = $conn->query($sql);
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
    echo "<button type='button' class='btn btn-warning btn-sm edit-btn me-1' data-id='{$row['id']}' data-name='".htmlspecialchars($row['name'], ENT_QUOTES)."' data-address='".htmlspecialchars($row['address'], ENT_QUOTES)."' data-phone='".htmlspecialchars($row['phone'], ENT_QUOTES)."' data-email='".htmlspecialchars($row['email'], ENT_QUOTES)."' data-gst='".htmlspecialchars($row['gst_no'], ENT_QUOTES)."' data-hsn='".htmlspecialchars($row['hsn_code'], ENT_QUOTES)."' data-pan='".htmlspecialchars($row['pan_no'], ENT_QUOTES)."' data-cin='".htmlspecialchars($row['cin_no'], ENT_QUOTES)."' data-aadhaar='".htmlspecialchars($row['aadhaar_no'], ENT_QUOTES)."' data-destination='".htmlspecialchars($row['destination'], ENT_QUOTES)."' data-parcel_type='".htmlspecialchars($row['parcel_type'], ENT_QUOTES)."' data-weight='".htmlspecialchars($row['weight'], ENT_QUOTES)."' data-price='".htmlspecialchars($row['price'], ENT_QUOTES)."'>Edit</button> ";
    echo "<button type='button' class='btn btn-danger btn-sm delete-btn' data-id='{$row['id']}'>Delete</button>";
    echo "</td>";
    echo "</tr>";
    $sr++;
}
$conn->close();

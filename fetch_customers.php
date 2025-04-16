<?php
include 'inc/db.php';

$sql = "SELECT * FROM customers ORDER BY id DESC";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $row['destination'] = json_encode(explode(',', $row['destination']));
    $row['parcel_type'] = json_encode(explode(',', $row['parcel_type']));
    $row['weight'] = json_encode(explode(',', $row['weight']));
    $row['price'] = json_encode(explode(',', $row['price']));

    $created_at = !empty($row['created_at']) ? date('d-m-Y H:i:s', strtotime($row['created_at'])) : 'N/A';
    echo "<tr>
        <td>{$row['id']}</td>
        <td>" . htmlspecialchars($row['name']) . "</td>
        <td>" . nl2br(htmlspecialchars($row['address'])) . "</td>
        <td>{$row['phone']}</td>
        <td>{$row['email']}</td>
        <td>{$row['gst_no']}</td>
        <td>{$row['hsn_code']}</td>
        <td>{$row['pan_no']}</td>
        <td>{$row['cin_no']}</td>
        <td>{$row['aadhaar_no']}</td>
        <td>{$created_at}</td>
        <td>
            <button type='button' class='btn btn-info view-details-btn btn-sm' data-id='{$row['id']}' data-bs-toggle='modal' data-bs-target='#customerDetailsModal'>View</button>
            <button class='btn btn-sm btn-warning edit-btn'
                data-id='{$row['id']}'
                data-name=\"" . htmlspecialchars($row['name']) . "\"
                data-address=\"" . htmlspecialchars($row['address']) . "\"
                data-phone='{$row['phone']}'
                data-email='{$row['email']}'
                data-gst='{$row['gst_no']}'
                data-hsn='{$row['hsn_code']}'
                data-pan='{$row['pan_no']}'
                data-cin='{$row['cin_no']}'
                data-aadhaar='{$row['aadhaar_no']}'
                data-destination='{$row['destination']}'
                data-parcel_type='{$row['parcel_type']}'
                data-weight='{$row['weight']}'
                data-price='{$row['price']}'
            >Edit</button>
            <button class='btn btn-sm btn-danger delete-btn' data-id='{$row['id']}'>Delete</button>
        </td>
    </tr>";
}
$conn->close();

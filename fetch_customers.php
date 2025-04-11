<?php
include 'inc/db.php';

$sql = "SELECT * FROM customers ORDER BY id DESC";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
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
        <td>{$row['created_at']}</td>
        <td>
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
            >Edit</button>
            <button class='btn btn-sm btn-danger delete-btn' data-id='{$row['id']}'>Delete</button>
        </td>
    </tr>";
}
$conn->close();

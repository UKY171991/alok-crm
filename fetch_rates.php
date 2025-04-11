<?php
include 'inc/db.php';

$sql = "SELECT * FROM rates ORDER BY id DESC";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    echo "<tr>
        <td>{$row['id']}</td>
        <td>{$row['destination']}</td>
        <td>{$row['type']}</td>
        <td>{$row['weight_category']}</td>
        <td>{$row['rate']}</td>
        <td>{$row['created_at']}</td>
        <td>
            <button class='btn btn-warning btn-sm edit-btn'
                data-id='{$row['id']}'
                data-destination=\"{$row['destination']}\"
                data-type=\"{$row['type']}\"
                data-weight=\"{$row['weight_category']}\"
                data-rate=\"{$row['rate']}\"
            >Edit</button>
            <button class='btn btn-danger btn-sm delete-btn' data-id='{$row['id']}'>Delete</button>
        </td>
    </tr>";
}
$conn->close();

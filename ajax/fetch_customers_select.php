<?php
include '../inc/db.php';
$sql = "SELECT id, name FROM customers ORDER BY name ASC";
$result = $conn->query($sql);
$options = "<option value=''>Select Customer</option>";
while ($row = $result->fetch_assoc()) {
    $options .= "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['name']) . "</option>";
}
echo $options;

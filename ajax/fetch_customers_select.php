<?php
$inc_path = dirname(__DIR__) . '/inc/db.php';
include $inc_path;

// Check connection
if (!$conn || $conn->connect_error) {
    http_response_code(500);
    echo "<option value=''>DB connection error</option>";
    exit;
}

$sql = "SELECT id, name FROM customers ORDER BY name ASC";
$result = $conn->query($sql);

if (!$result) {
    http_response_code(500);
    echo "<option value=''>Query error: " . htmlspecialchars($conn->error) . "</option>";
    exit;
}

$options = "<option value=''>Select Customer</option>";
while ($row = $result->fetch_assoc()) {
    $options .= "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['name']) . "</option>";
}
echo $options;
?>
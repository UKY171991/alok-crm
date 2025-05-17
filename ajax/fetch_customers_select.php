$inc_path = dirname(__DIR__) . '/inc/db.php';
include $inc_path;
$sql = "SELECT id, name FROM customers ORDER BY name ASC";
$result = $conn->query($sql);
$options = "<option value=''>Select Customer</option>";
while ($row = $result->fetch_assoc()) {
    $options .= "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['name']) . "</option>";
}
echo $options;

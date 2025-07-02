<?php
include '../inc/db.php';

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where = '';
$join = '';
if ($search !== '') {
    $search_esc = $conn->real_escape_string($search);
    $join = "LEFT JOIN customers ON orders.customer_id = customers.id";
    $where = "WHERE 
        orders.docket LIKE '%$search_esc%' OR
        orders.location LIKE '%$search_esc%' OR
        orders.destination LIKE '%$search_esc%' OR
        orders.mode LIKE '%$search_esc%' OR
        orders.pincode LIKE '%$search_esc%' OR
        orders.content LIKE '%$search_esc%' OR
        orders.sender_detail LIKE '%$search_esc%' OR
        orders.t_receiver_name LIKE '%$search_esc%' OR
        customers.name LIKE '%$search_esc%'";
}

$for_invoice = isset($_GET['for_invoice']) && $_GET['for_invoice'] == 1;
$customer_id = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : 0;

if ($for_invoice && $customer_id > 0) {
    // Exclude orders already in invoice_items by order_id
    $sql = "SELECT * FROM orders WHERE customer_id = $customer_id AND id NOT IN (SELECT order_id FROM invoice_items WHERE order_id IS NOT NULL) ORDER BY id DESC";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        echo '<div class="table-responsive"><table class="table table-bordered table-hover align-middle">';
        echo '<thead class="table-primary"><tr><th></th><th>Sr.</th><th>Booking Date</th><th>Consignment No.</th><th>Destination City</th><th>Dox / Non Dox</th><th>Service</th><th>No of Pcs</th><th>Weight or No</th><th>Amt.</th><th>Way Bill Value</th></tr></thead><tbody>';
        $sr = 1;
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td><input type="checkbox" class="order-checkbox" value="' . $row['id'] . '"></td>';
            echo '<td>' . $sr . '</td>';
            echo '<td class="editable-cell" data-name="booking_date" data-type="date">' . htmlspecialchars($row['date']) . '</td>';
            echo '<td class="editable-cell" data-name="consignment_no">' . htmlspecialchars($row['docket']) . '</td>';
            echo '<td class="editable-cell" data-name="destination_city">' . htmlspecialchars($row['destination']) . '</td>';
            echo '<td class="editable-cell" data-name="dox_non_dox">' . htmlspecialchars($row['dox_non_dox']) . '</td>';
            echo '<td class="editable-cell" data-name="service">' . htmlspecialchars($row['mode']) . '</td>';
            echo '<td class="editable-cell" data-name="quantity">' . htmlspecialchars($row['no_of_pcs']) . '</td>';
            echo '<td class="editable-cell" data-name="weight">' . htmlspecialchars($row['weight'] ?? '') . '</td>';
            echo '<td class="editable-cell" data-name="amt">' . htmlspecialchars($row['clinet_billing_value'] ?? '') . '</td>';
            echo '<td class="editable-cell" data-name="way_bill_value">' . htmlspecialchars($row['material_value'] ?? '') . '</td>';
            echo '</tr>';
            $sr++;
        }
        echo '</tbody></table></div>';
    } else {
        echo '<div class="alert alert-info">No orders found for this customer.</div>';
    }
    $conn->close();
    return;
}

$totalResult = $conn->query("SELECT COUNT(*) as cnt FROM orders $join $where");
$totalRows = $totalResult ? intval($totalResult->fetch_assoc()['cnt']) : 0;
$totalPages = ceil($totalRows / $limit);

$result = $conn->query("SELECT orders.* FROM orders $join $where ORDER BY orders.id DESC LIMIT $limit OFFSET $offset");
$srNo = $offset + 1;
?>
<table class="table table-hover table-striped align-middle table-bordered">
    <thead class="table-primary sticky-top">
        <tr>
            <th>Sr. No.</th>
            <th>Customer</th>
            <th>Date</th>
            <th>Docket</th>
            <th>Location</th>
            <th>Destination</th>
            <th>Mode</th>
            <th>No of Pcs</th>
            <th>Pincode</th>
            <th>Content</th>
            <th>Sender</th>
            <th>Receiver</th>
            <th style="min-width:120px;">Action</th>
        </tr>
    </thead>
    <tbody>
    <?php if ($result && $result->num_rows > 0):
        // Pre-fetch all customer names for the orders in this page
        $customerIds = array();
        $orders = array();
        $result->data_seek(0);
        while($row = $result->fetch_assoc()) {
            $orders[] = $row;
            if (!empty($row['customer_id'])) $customerIds[] = intval($row['customer_id']);
        }
        $customerNames = array();
        if (!empty($customerIds)) {
            $ids = implode(',', array_unique($customerIds));
            if (!empty($ids)) {
                $q = $conn->query("SELECT id, name FROM customers WHERE id IN ($ids)");
                if ($q) {
                    while($c = $q->fetch_assoc()) $customerNames[$c['id']] = $c['name'];
                }
            }
        }
        foreach($orders as $row): ?>
        <tr>
            <td><?= $srNo++ ?></td>
            <td><?= isset($customerNames[$row['customer_id']]) ? htmlspecialchars($customerNames[$row['customer_id']]) : '' ?></td>
            <td><?= htmlspecialchars($row['date']) ?></td>
            <td><?= htmlspecialchars($row['docket']) ?></td>
            <td><?= htmlspecialchars($row['location']) ?></td>
            <td><?= htmlspecialchars($row['destination']) ?></td>
            <td><?= htmlspecialchars($row['mode']) ?></td>
            <td><?= htmlspecialchars($row['no_of_pcs']) ?></td>
            <td><?= htmlspecialchars($row['pincode']) ?></td>
            <td><?= htmlspecialchars($row['content']) ?></td>
            <td><?= htmlspecialchars($row['sender_detail']) ?></td>
            <td><?= htmlspecialchars($row['t_receiver_name']) ?></td>
            <td class="d-flex flex-column gap-1">
                <button class="btn btn-info btn-sm view-order mb-1 w-100" data-id="<?= $row['id'] ?>"><i class="fas fa-eye"></i> View</button>
                <button class="btn btn-warning btn-sm edit-order mb-1 w-100" data-id="<?= $row['id'] ?>"><i class="fas fa-edit"></i> Edit</button>
                <button class="btn btn-danger btn-sm delete-order w-100" data-id="<?= $row['id'] ?>"><i class="fas fa-trash"></i> Delete</button>
            </td>
        </tr>
    <?php endforeach; else: ?>
        <tr><td colspan="13" class="text-center text-muted">No orders found.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
<?php if ($totalPages > 1): ?>
<nav>
    <ul class="pagination order-pagination justify-content-center">
        <li class="page-item<?= $page == 1 ? ' disabled' : '' ?>">
            <a class="page-link" data-page="1" href="#">First</a>
        </li>
        <li class="page-item<?= $page == 1 ? ' disabled' : '' ?>">
            <a class="page-link" data-page="<?= $page-1 ?>" href="#">Previous</a>
        </li>
        <?php
        $range = 2; // how many pages to show around current
        $start = max(1, $page - $range);
        $end = min($totalPages, $page + $range);
        if ($start > 1) {
            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        for ($i = $start; $i <= $end; $i++) {
            $active = $i == $page ? ' active' : '';
            echo '<li class="page-item'.$active.'"><a class="page-link" data-page="'.$i.'" href="#">'.$i.'</a></li>';
        }
        if ($end < $totalPages) {
            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        ?>
        <li class="page-item<?= $page == $totalPages ? ' disabled' : '' ?>">
            <a class="page-link" data-page="<?= $page+1 ?>" href="#">Next</a>
        </li>
        <li class="page-item<?= $page == $totalPages ? ' disabled' : '' ?>">
            <a class="page-link" data-page="<?= $totalPages ?>" href="#">Last</a>
        </li>
    </ul>
</nav>
<?php endif; ?>
<?php $conn->close(); ?>

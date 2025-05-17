<?php
include '../inc/db.php';

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$totalResult = $conn->query("SELECT COUNT(*) as cnt FROM orders");
$totalRows = $totalResult ? intval($totalResult->fetch_assoc()['cnt']) : 0;
$totalPages = ceil($totalRows / $limit);

$result = $conn->query("SELECT * FROM orders ORDER BY id DESC LIMIT $limit OFFSET $offset");
$srNo = $offset + 1;

?>
<table class="table table-hover table-bordered align-middle">
    <thead class="table-light">
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
            <td>
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

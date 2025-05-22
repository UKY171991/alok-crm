<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
include 'inc/db.php';
include 'inc/header.php';
include 'inc/sidebar.php';

?>

<main class="content-wrapper">
    <div class="container-fluid p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Invoice Details</h2>
        </div>

        <!-- Invoice Header -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Awdhoot Global Solutions</h5>
                        <p>Shop No.: 570/326, VIP Road, Sainik Nagar,<br>
                        Lucknow - 226002 - Uttar Pradesh<br>
                        Phone: 8853099924<br>
                        GST No: 09BLUPS9727E1ZT, Uttar Pradesh</p>
                    </div>
                    <div class="col-md-6 text-end">
                        <p>Invoice No: <strong>AG525-26/783</strong></p>
                        <p>Invoice Date: <strong>30-Apr-25</strong></p>
                        <p>Period: <strong>1 Apr 25 to 30 Apr 25</strong></p>
                        <p>SAC Code: <strong>---</strong></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Details -->
        <div class="card mb-3">
            <div class="card-body">
                <h5>To,</h5>
                <p>Aronva Healthcare<br>
                Address: Ashiyana, Lucknow - 226012<br>
                Phone: ---<br>
                GSTN No: 09BQIPS8917H1ZS</p>
            </div>
        </div>

        <!-- Invoice Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Sr.</th>
                                <th>Booking Date</th>
                                <th>Consignment No.</th>
                                <th>Destination City</th>
                                <th>Weight or N</th>
                                <th>Amt.</th>
                                <th>Way Bill Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>3-Apr-25</td>
                                <td>D2001151437</td>
                                <td>GORAKHPUR</td>
                                <td>2.820</td>
                                <td>---</td>
                                <td>---</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>5-Apr-25</td>
                                <td>D2001151472</td>
                                <td>RAEBARELI</td>
                                <td>2.590</td>
                                <td>---</td>
                                <td>---</td>
                            </tr>
                            <!-- Add more rows as needed -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'inc/footer.php'; ?>

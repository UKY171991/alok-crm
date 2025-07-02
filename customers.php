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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0 fw-bold"><i class="fas fa-users text-primary me-2"></i>Customer Management</h2>
            <button class="btn btn-primary btn-lg rounded-pill shadow-sm" id="addCustomerBtn" data-bs-toggle="modal" data-bs-target="#customerModal">
                <i class="fas fa-plus me-1"></i> Add Customer
            </button>
        </div>
        <!-- Customers Table -->
        <div class="card shadow rounded-4 mb-4">
            <div class="card-header bg-white border-bottom-0 d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0 fw-bold text-primary"><i class="fas fa-users me-2"></i>Customers List</h3>
            </div>
            <div class="card-body">
                <div class="mb-3 d-flex justify-content-end">
                    <input type="text" id="customerSearch" class="form-control w-auto" placeholder="Search customers..." style="min-width:250px;">
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle table-bordered border-light mb-0" id="customerTable">
                        <thead class="table-primary">
                            <tr>
                                <th style="width: 40px;">#</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>GST</th>
                                <th style="width: 180px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="customerTableBody">
                            <!-- Data will be loaded here via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Pagination Controls -->
        <nav aria-label="Customers pagination" class="d-flex justify-content-center mb-4">
            <ul class="pagination pagination-lg" id="customerPagination"></ul>
        </nav>
    </div>
</main>

<!-- Customer Form Modal -->
<div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content rounded-4 shadow-lg">
            <div class="modal-header bg-primary text-white rounded-top-4">
                <h5 class="modal-title fw-bold" id="customerModalLabel"><i class="fas fa-user-plus me-2"></i><span id="formTitle">Add New Customer</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="customerForm">
                <input type="hidden" name="id" id="customer_id">
                <div class="modal-body bg-light-subtle p-4 rounded-4">
                    <div class="row g-4">
                        <div class="col-12 mb-2"><h5 class="fw-bold text-primary"><i class="fas fa-user me-2"></i>Basic Info</h5></div>
                        <div class="col-md-6 position-relative">
                            <div class="form-floating mb-3">
                                <input type="text" name="name" id="name" class="form-control rounded-pill shadow-sm" placeholder="Name" required aria-required="true">
                                <label for="name">Name <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-6 position-relative">
                            <div class="form-floating mb-3">
                                <textarea name="address" id="address" class="form-control rounded-pill shadow-sm" placeholder="Address" style="height:56px" required aria-required="true"></textarea>
                                <label for="address">Address <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-12 mb-2"><h5 class="fw-bold text-primary"><i class="fas fa-id-card me-2"></i>Contact & Tax Info</h5></div>
                        <div class="col-md-4 position-relative">
                            <div class="form-floating mb-3">
                                <input type="text" name="phone" id="phone" class="form-control rounded-pill shadow-sm ps-5" placeholder="Phone" required pattern="^[0-9\-\+\s]{10,15}$" aria-required="true">
                                <label for="phone">Phone <span class="text-danger">*</span></label>
                                <span class="position-absolute top-50 start-0 translate-middle-y ps-3 text-secondary"><i class="fas fa-phone"></i></span>
                                <div class="form-text">Enter a valid phone number.</div>
                            </div>
                        </div>
                        <div class="col-md-4 position-relative">
                            <div class="form-floating mb-3">
                                <input type="email" name="email" id="email" class="form-control rounded-pill shadow-sm ps-5" placeholder="Email">
                                <label for="email">Email</label>
                                <span class="position-absolute top-50 start-0 translate-middle-y ps-3 text-secondary"><i class="fas fa-envelope"></i></span>
                                <div class="form-text">Optional. We'll never share your email.</div>
                            </div>
                        </div>
                        <div class="col-md-4 position-relative">
                            <div class="form-floating mb-3">
                                <input type="text" name="gst_no" id="gst_no" class="form-control rounded-pill shadow-sm ps-5" placeholder="GST No" pattern="^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$">
                                <label for="gst_no">GST No</label>
                                <span class="position-absolute top-50 start-0 translate-middle-y ps-3 text-secondary"><i class="fas fa-file-invoice"></i></span>
                                <div class="form-text">Format: 22AAAAA0000A1Z0</div>
                            </div>
                        </div>
                        <div class="col-md-4 position-relative">
                            <div class="form-floating mb-3">
                                <input type="text" name="hsn_code" id="hsn_code" class="form-control rounded-pill shadow-sm" placeholder="HSN Code">
                                <label for="hsn_code">HSN Code</label>
                                <div class="form-text">Optional. Harmonized System Nomenclature.</div>
                            </div>
                        </div>
                        <div class="col-md-4 position-relative">
                            <div class="form-floating mb-3">
                                <input type="text" name="pan_no" id="pan_no" class="form-control rounded-pill shadow-sm ps-5" placeholder="PAN No" pattern="^[A-Z]{5}[0-9]{4}[A-Z]{1}$">
                                <label for="pan_no">PAN No</label>
                                <span class="position-absolute top-50 start-0 translate-middle-y ps-3 text-secondary"><i class="fas fa-id-badge"></i></span>
                                <div class="form-text">Format: ABCDE1234F</div>
                            </div>
                        </div>
                        <div class="col-md-4 position-relative">
                            <div class="form-floating mb-3">
                                <input type="text" name="cin_no" id="cin_no" class="form-control rounded-pill shadow-sm" placeholder="CIN No">
                                <label for="cin_no">CIN No</label>
                                <div class="form-text">Optional. Corporate Identification Number.</div>
                            </div>
                        </div>
                        <div class="col-md-4 position-relative">
                            <div class="form-floating mb-3">
                                <input type="text" name="aadhaar_no" id="aadhaar_no" class="form-control rounded-pill shadow-sm ps-5" placeholder="Aadhaar No" pattern="^(\d{12}|(\d{4} \d{4} \d{4}))$">
                                <label for="aadhaar_no">Aadhaar No</label>
                                <span class="position-absolute top-50 start-0 translate-middle-y ps-3 text-secondary"><i class="fas fa-id-card-alt"></i></span>
                                <div class="form-text">12 digit Aadhaar number (with or without spaces).</div>
                            </div>
                        </div>
                        <div class="col-12 mb-2"><h5 class="fw-bold text-primary"><i class="fas fa-boxes me-2"></i>Destinations, Parcel Types, and Weights</h5></div>
                        <div class="col-12 mb-3">
                            <table class="table table-bordered" id="multiOptionsTable">
                                <thead>
                                    <tr>
                                        <th>Destination</th>
                                        <th>Parcel Type</th>
                                        <th>Weight</th>
                                        <th>Service</th>
                                        <th>Shipment Type</th>
                                        <th>Price</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <select name="destination[]" class="form-control select2-destination" required>
                                                <option value="">Select Destination</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="parcel_type[]" class="form-control" required>
                                                <option value="">Select Parcel Type</option>
                                                <option value="Document & Light Parcel">Document & Light Parcel</option>
                                                <option value="Premium">Premium</option>
                                                <option value="Bulk Load Parcel">Bulk Load Parcel</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="weight[]" class="form-control weight-select" required="">
                                                <option value="">Select Weight</option>
                                                <option value="Upto 100 gm">Upto 100 gm</option>
                                                <option value="Upto 250 gm">Upto 250 gm</option>
                                                <option value="Upto 500 gm">Upto 500 gm</option>
                                                <option value="Addl 500 gm">Addl 500 gm</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="service[]" class="form-control" required>
                                                <option value="">Select Service</option>
                                                <option value="By Air">By Air</option>
                                                <option value="By Surface">By Surface</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="shipment_type[]" class="form-control" required>
                                                <option value="">Select Shipment Type</option>
                                                <option value="Dox">Dox</option>
                                                <option value="Non Dox">Non Dox</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="price[]" class="form-control" placeholder="Enter price (e.g., 100)" required>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger remove-row">Remove</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-success" id="addRow">Add More</button>
                        </div>
                    </div>
                    <div id="message" class="mt-2"></div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Add Customer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Customer Details Modal -->
<div class="modal fade" id="customerDetailsModal" tabindex="-1" aria-labelledby="customerDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customerDetailsModalLabel">Customer Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">+</span>
                </button>
            </div>
            <div class="modal-body" id="customerDetailsBody">
                <!-- Customer details will be loaded here dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php include 'inc/footer.php'; ?>

<style>
    table th, table td {
        white-space: nowrap;
    }
    .btn {
        width: auto; /* Set a fixed width for all buttons */
        text-align: center;
    }
    .form-floating > .form-control, .form-floating > .form-select { border-radius: 2rem; box-shadow: 0 0.1rem 0.3rem rgba(0,0,0,0.04); }
    .form-floating > label { left: 1.2rem; }
    .modal-body.bg-light-subtle { background: #f8fafc; }
    .select2-container .select2-selection--single { border-radius: 2rem !important; height: 48px !important; padding: 8px 16px !important; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 32px !important; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 48px !important; }
    .form-text { font-size: 0.95em; color: #6c757d; }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css"/>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8/jquery.inputmask.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/js/select2.min.js"></script>

<script>
$(function () {
    function loadCustomers(page = 1, per_page = 10, search = "") {
        $.get("fetch_customers.php", { page: page, per_page: per_page, search: search }, function (data) {
            $("#customerTableBody").html(data);
            // Read pagination info from hidden row
            var $info = $('#pagination-info');
            if ($info.length) {
                var total = parseInt($info.data('total'));
                var page = parseInt($info.data('page'));
                var per_page = parseInt($info.data('per_page'));
                renderCustomerPagination(total, page, per_page);
            } else {
                $('#customerPagination').empty();
            }
        });
    }

    // Debounced search
    let searchTimeout;
    $("#customerSearch").on("input", function () {
        clearTimeout(searchTimeout);
        const searchVal = $(this).val();
        searchTimeout = setTimeout(function () {
            loadCustomers(1, 10, searchVal);
        }, 300);
    });

    // Update pagination to use search value
    $(document).on('click', '#customerPagination .page-link', function(e) {
        e.preventDefault();
        var page = parseInt($(this).data('page'));
        if (!isNaN(page) && page > 0) {
            var searchVal = $("#customerSearch").val();
            loadCustomers(page, 10, searchVal);
        }
    });

    // Initial load
    loadCustomers();

    $("#customerForm").on("submit", function (e) {
        e.preventDefault();
        $.post("save_customer.php", $(this).serialize(), function (res) {
            $("#message").html(res);
            $("#customerForm")[0].reset();
            $("#formTitle").text("Add New Customer");
            $("#submitBtn").text("Add Customer");
            $("#customer_id").val('');
            loadCustomers();
        });
    });

    $(document).on("click", ".edit-btn", function () {
        console.log("Raw data-destination:", $(this).attr("data-destination"));
        console.log("Raw data-parcel_type:", $(this).attr("data-parcel_type"));
        console.log("Raw data-weight:", $(this).attr("data-weight"));
        console.log("Raw data-price:", $(this).attr("data-price"));

        $("#formTitle").text("Update Customer");
        $("#submitBtn").text("Update Customer");

        $("#customer_id").val($(this).data("id"));
        $("#name").val($(this).data("name"));
        $("#address").val($(this).data("address"));
        $("#phone").val($(this).data("phone"));
        $("#email").val($(this).data("email"));
        $("#gst_no").val($(this).data("gst"));
        $("#hsn_code").val($(this).data("hsn"));
        $("#pan_no").val($(this).data("pan"));
        $("#cin_no").val($(this).data("cin"));
        $("#aadhaar_no").val($(this).data("aadhaar"));

        // Clear existing rows
        $("#multiOptionsTable tbody").html("");

        function parseField(val) {
            if (!val) return [];
            try {
                var arr = JSON.parse(val);
                if (Array.isArray(arr)) return arr;
            } catch (e) {}
            if (typeof val === 'string' && val.startsWith("[") && val.endsWith("]")) {
                val = val.replace(/^[\[]|[\]]$/g, '').replace(/['\"]/g, '');
            }
            if (!val) return [];
            // Prefer splitting by comma, then newline
            if (val.indexOf(',') !== -1) {
                return val.split(',').map(function(x){ return x.trim(); }).filter(Boolean);
            }
            if (val.indexOf('\n') !== -1) {
                return val.split(/\r?\n/).map(function(x){ return x.trim(); }).filter(Boolean);
            }
            return [val];
        }
        let destinations = parseField($(this).attr("data-destination"));
        let parcelTypes = parseField($(this).attr("data-parcel_type"));
        let weights = parseField($(this).attr("data-weight"));
        let prices = parseField($(this).attr("data-price"));
        let services = parseField($(this).attr("data-service"));
        let shipmentTypes = parseField($(this).attr("data-shipment_type"));
        // Only show rows where at least one field is not empty
        const maxLength = Math.max(destinations.length, parcelTypes.length, weights.length, prices.length, services.length, shipmentTypes.length);
        destinations = destinations.concat(Array(maxLength - destinations.length).fill(""));
        parcelTypes = parcelTypes.concat(Array(maxLength - parcelTypes.length).fill(""));
        weights = weights.concat(Array(maxLength - weights.length).fill(""));
        prices = prices.concat(Array(maxLength - prices.length).fill(""));
        services = services.concat(Array(maxLength - services.length).fill(""));
        shipmentTypes = shipmentTypes.concat(Array(maxLength - shipmentTypes.length).fill(""));

        // Load destination options first, then build rows
        $.getJSON("fetch_destinations.php?mode=json", function (data) {
            if (data.error) {
                alert(data.error);
                return;
            }
            const options = data.map(destination => `<option value="${destination.name}">${destination.name}</option>`).join("");
            let anyRow = false;
            for (let index = 0; index < maxLength; index++) {
                const destVal = destinations[index] || "";
                const parcelType = parcelTypes[index] || "";
                const weight = weights[index] || "";
                const price = prices[index] || "";
                const service = services[index] || "";
                const shipmentType = shipmentTypes[index] || "";
                // Only add row if at least one field is not empty
                if (destVal || parcelType || weight || price || service || shipmentType) {
                    anyRow = true;
                    const formattedPrice = price.replace(/\s+/g, '').replace(/,+$/, '');
                    const newRow = `
                        <tr>
                            <td>
                                <select name="destination[]" class="form-control select2-destination" required>
                                    <option value="">Select Destination</option>
                                    ${options}
                                </select>
                            </td>
                            <td>
                                <select name="parcel_type[]" class="form-control" required>
                                    <option value="">Select Parcel Type</option>
                                    <option value="Document & Light Parcel">Document & Light Parcel</option>
                                    <option value="Premium">Premium</option>
                                    <option value="Bulk Load Parcel">Bulk Load Parcel</option>
                                </select>
                            </td>
                            <td>
                                <select name="weight[]" class="form-control weight-select" required="">
                                    <option value="">Select Weight</option>
                                    <option value="Upto 100 gm">Upto 100 gm</option>
                                    <option value="Upto 250 gm">Upto 250 gm</option>
                                    <option value="Upto 500 gm">Upto 500 gm</option>
                                    <option value="Addl 500 gm">Addl 500 gm</option>
                                </select>
                            </td>
                            <td>
                                <select name="service[]" class="form-control" required>
                                    <option value="">Select Service</option>
                                    <option value="By Air">By Air</option>
                                    <option value="By Surface">By Surface</option>
                                </select>
                            </td>
                            <td>
                                <select name="shipment_type[]" class="form-control" required>
                                    <option value="">Select Shipment Type</option>
                                    <option value="Dox">Dox</option>
                                    <option value="Non Dox">Non Dox</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" name="price[]" class="form-control" value="${formattedPrice}" required>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger remove-row">Remove</button>
                            </td>
                        </tr>
                    `;
                    $("#multiOptionsTable tbody").append(newRow);
                    // Set the selected values
                    const $lastRow = $("#multiOptionsTable tbody tr:last");
                    $lastRow.find("select[name='destination[]']").val(destVal);
                    $lastRow.find("select[name='parcel_type[]']").val(parcelType);
                    $lastRow.find("select[name='service[]']").val(service);
                    $lastRow.find("select[name='shipment_type[]']").val(shipmentType);
                    updateWeightSelect($lastRow);
                    setTimeout(function() {
                        $lastRow.find("select[name='weight[]']").val(weight);
                    }, 10);
                }
            }
            // If no rows, add one empty row
            if (!anyRow) {
                const newRow = `
                    <tr>
                        <td>
                            <select name="destination[]" class="form-control select2-destination" required>
                                <option value="">Select Destination</option>
                                ${options}
                            </select>
                        </td>
                        <td>
                            <select name="parcel_type[]" class="form-control" required>
                                <option value="">Select Parcel Type</option>
                                <option value="Document & Light Parcel">Document & Light Parcel</option>
                                <option value="Premium">Premium</option>
                                <option value="Bulk Load Parcel">Bulk Load Parcel</option>
                            </select>
                        </td>
                        <td>
                            <select name="weight[]" class="form-control weight-select" required="">
                                <option value="">Select Weight</option>
                                <option value="Upto 100 gm">Upto 100 gm</option>
                                <option value="Upto 250 gm">Upto 250 gm</option>
                                <option value="Upto 500 gm">Upto 500 gm</option>
                                <option value="Addl 500 gm">Addl 500 gm</option>
                            </select>
                        </td>
                        <td>
                            <select name="service[]" class="form-control" required>
                                <option value="">Select Service</option>
                                <option value="By Air">By Air</option>
                                <option value="By Surface">By Surface</option>
                            </select>
                        </td>
                        <td>
                            <select name="shipment_type[]" class="form-control" required>
                                <option value="">Select Shipment Type</option>
                                <option value="Dox">Dox</option>
                                <option value="Non Dox">Non Dox</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" name="price[]" class="form-control" value="" required>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger remove-row">Remove</button>
                        </td>
                    </tr>
                `;
                $("#multiOptionsTable tbody").append(newRow);
            }
            loadDestinationsForAllRows();
        });
        $("#customerModal").modal("show");
    });

    // Reset form when modal is closed
    $('#customerModal').on('hidden.bs.modal', function () {
        $('#customerForm')[0].reset();
        $('#formTitle').text('Add New Customer');
        $('#submitBtn').text('Add Customer');
        $('#customer_id').val('');
        $('#message').html('');
        // Optionally reset multiOptionsTable to one empty row
        // $("#multiOptionsTable tbody").html('');
    });

    $(document).on("click", ".delete-btn", function () {
        if (confirm("Are you sure you want to delete this customer?")) {
            $.post("delete_customer.php", { id: $(this).data("id") }, function () {
                loadCustomers();
            });
        }
    });

    function loadDestinations() {
        $.getJSON("fetch_destinations.php?mode=json", function (data) {
            if (data.error) {
                alert(data.error);
                return;
            }

            const options = data.map(destination => `<option value="${destination.name}">${destination.name}</option>`);
            $("select[name='destination[]']").html('<option value="">Select Destination</option>' + options.join(""));
        });
    }

    function loadDestinationsForAllRows() {
        $.getJSON("fetch_destinations.php?mode=json", function (data) {
            if (data.error) {
                alert(data.error);
                return;
            }

            const options = data.map(destination => `<option value="${destination.name}">${destination.name}</option>`).join("");

            $("select[name='destination[]']").each(function () {
                const currentValue = $(this).val();
                $(this).html('<option value="">Select Destination</option>' + options);
                $(this).val(currentValue); // Retain the current value
            });
        });

        setAllWeightOptions();
    }

    function getWeightOptions(parcelType, service, shipmentType, destination) {
        // For Non Dox, check destination for min weight
        if (shipmentType === 'Non Dox') {
            const threeKgStates = ['UP', 'Uttarakhand', 'Delhi', 'Haryana', 'Rajasthan', 'Punjab'];
            if (threeKgStates.includes(destination)) {
                return [
                    { value: '3 kg', text: '3 kg' },
                    { value: '5 kg', text: '5 kg' },
                    { value: '10 kg', text: '10 kg' },
                    { value: '20 kg', text: '20 kg' },
                    { value: '30 kg', text: '30 kg' },
                    { value: '50 kg', text: '50 kg' },
                    { value: 'Above 50 kg', text: 'Above 50 kg' }
                ];
            } else {
                return [
                    { value: '5 kg', text: '5 kg' },
                    { value: '10 kg', text: '10 kg' },
                    { value: '20 kg', text: '20 kg' },
                    { value: '30 kg', text: '30 kg' },
                    { value: '50 kg', text: '50 kg' },
                    { value: 'Above 50 kg', text: 'Above 50 kg' }
                ];
            }
        }
        if (parcelType === 'Document & Light Parcel') {
            return [
                { value: 'Upto 100 gm', text: 'Upto 100 gm' },
                { value: 'Upto 250 gm', text: 'Upto 250 gm' },
                { value: 'Upto 500 gm', text: 'Upto 500 gm' },
                { value: 'Addl 500 gm', text: 'Addl 500 gm' }
            ];
        } else if (parcelType === 'Premium') {
            return [
                { value: 'Upto 500 gm', text: 'Upto 500 gm' },
                { value: 'Addl 500 gm', text: 'Addl 500 gm' },
                { value: 'Upto 250 gm', text: 'Upto 250 gm' }
            ];
        } else if (parcelType === 'Bulk Load Parcel') {
            return [
                { value: 'Up to 10 kg (Surface)', text: 'Up to 10 kg (Surface)' },
                { value: '10 kg - 50 kg (Surface)', text: '10 kg - 50 kg (Surface)' },
                { value: 'Above 50 kg (Surface)', text: 'Above 50 kg (Surface)' },
                { value: 'Up to 10 kg (Air)', text: 'Up to 10 kg (Air)' },
                { value: '10 kg - Above (Air)', text: '10 kg - Above (Air)' }
            ];
        } else {
            return [
                { value: '', text: 'Select Weight' },
                { value: 'Upto 100 gm', text: 'Upto 100 gm' },
                { value: 'Upto 250 gm', text: 'Upto 250 gm' },
                { value: 'Upto 500 gm', text: 'Upto 500 gm' },
                { value: 'Addl 500 gm', text: 'Addl 500 gm' }
            ];
        }
    }

    function updateWeightSelect($row) {
        var parcelType = $row.find("select[name='parcel_type[]']").val();
        var service = $row.find("select[name='service[]']").val();
        var shipmentType = $row.find("select[name='shipment_type[]']").val();
        var destination = $row.find("select[name='destination[]']").val();
        var $weightSelect = $row.find("select[name='weight[]']");
        var currentValue = $weightSelect.val();
        var options = getWeightOptions(parcelType, service, shipmentType, destination);
        $weightSelect.empty();
        options.forEach(function(opt) {
            $weightSelect.append(`<option value="${opt.value}">${opt.text}</option>`);
        });
        $weightSelect.val(currentValue);
    }

    $(document).on('change', "select[name='parcel_type[]'], select[name='service[]'], select[name='shipment_type[]'], select[name='destination[]']", function () {
        var $row = $(this).closest('tr');
        updateWeightSelect($row);
    });

    $(document).on("click", "#addRow", function () {
        const newRow = `
            <tr>
                <td>
                    <select name="destination[]" class="form-control select2-destination" required>
                        <option value="">Select Destination</option>
                    </select>
                </td>
                <td>
                    <select name="parcel_type[]" class="form-control" required>
                        <option value="">Select Parcel Type</option>
                        <option value="Document & Light Parcel">Document & Light Parcel</option>
                        <option value="Premium">Premium</option>
                        <option value="Bulk Load Parcel">Bulk Load Parcel</option>
                    </select>
                </td>
                <td>
                    <select name="weight[]" class="form-control weight-select" required="">
                        <option value="">Select Weight</option>
                        <option value="Upto 100 gm">Upto 100 gm</option>
                        <option value="Upto 250 gm">Upto 250 gm</option>
                        <option value="Upto 500 gm">Upto 500 gm</option>
                        <option value="Addl 500 gm">Addl 500 gm</option>
                    </select>
                </td>
                <td>
                    <select name="service[]" class="form-control" required>
                        <option value="">Select Service</option>
                        <option value="By Air">By Air</option>
                        <option value="By Surface">By Surface</option>
                    </select>
                </td>
                <td>
                    <select name="shipment_type[]" class="form-control" required>
                        <option value="">Select Shipment Type</option>
                        <option value="Dox">Dox</option>
                        <option value="Non Dox">Non Dox</option>
                    </select>
                </td>
                <td>
                    <input type="text" name="price[]" class="form-control" placeholder="Enter price (e.g., 100)" required>
                </td>
                <td>
                    <button type="button" class="btn btn-danger remove-row">Remove</button>
                </td>
            </tr>
        `;
        $("#multiOptionsTable tbody").append(newRow);
        setTimeout(function() {
            var $lastRow = $("#multiOptionsTable tbody tr").last();
            updateWeightSelect($lastRow);
        }, 10);
        loadDestinationsForAllRows();
    });

    $(document).on("click", ".remove-row", function () {
        $(this).closest("tr").remove();
    });

    $(document).on("click", ".view-details-btn", function () {
        const customerId = $(this).data("id");
        $.get("fetch_customer_details.php", { id: customerId }, function (data) {
            $("#customerDetailsBody").html(data);
            $("#customerDetailsModal").modal("show");
        }).fail(function () {
            alert("Failed to fetch customer details. Please try again.");
        });
    });

    function setAllWeightOptions() {
        $("#multiOptionsTable tbody tr").each(function () {
            updateWeightSelect($(this));
        });
    }

    loadDestinations();

    // Input masks
    $('#phone').inputmask({ mask: "9999999999", greedy: false, showMaskOnHover: false });
    $('#gst_no').inputmask({ mask: "99AAAAA9999A1Z9", greedy: false, showMaskOnHover: false });
    $('#pan_no').inputmask({ mask: "AAAAA9999A", greedy: false, showMaskOnHover: false });
    $('#aadhaar_no').inputmask({ mask: '9999 9999 9999', greedy: false, showMaskOnHover: false, removeMaskOnSubmit: true });
    $('#aadhaar_no').attr('pattern', '^(\\d{12}|(\\d{4} \\d{4} \\d{4}))$');
    // Select2 for destination dropdowns
    $('.select2-destination').select2({ dropdownParent: $('#customerModal'), width: '100%' });

    // When adding new row, re-init select2
    $(document).on("click", "#addRow", function () {
        setTimeout(function() {
            $('.select2-destination').select2({ dropdownParent: $('#customerModal'), width: '100%' });
        }, 10);
    });

    // Custom validation for phone field
    $('#customerForm').on('submit', function(e) {
        var phone = $('#phone').val().replace(/\D/g, '');
        if (phone.length !== 10) {
            e.preventDefault();
            $('#phone').addClass('is-invalid');
            if ($('#phone').next('.invalid-feedback').length === 0) {
                $('#phone').after('<div class="invalid-feedback">Phone number must be exactly 10 digits.</div>');
            }
            $('#phone').focus();
            return false;
        } else {
            $('#phone').removeClass('is-invalid');
            $('#phone').next('.invalid-feedback').remove();
        }
    });

    // Custom validation for Aadhaar field
    $('#customerForm').on('submit', function(e) {
        var aadhaar = $('#aadhaar_no').val().replace(/\s/g, '');
        if (aadhaar && !/^\d{12}$/.test(aadhaar)) {
            e.preventDefault();
            $('#aadhaar_no').addClass('is-invalid');
            if ($('#aadhaar_no').next('.invalid-feedback').length === 0) {
                $('#aadhaar_no').after('<div class="invalid-feedback">Aadhaar number must be exactly 12 digits (with or without spaces).</div>');
            }
            $('#aadhaar_no').focus();
            return false;
        } else {
            $('#aadhaar_no').removeClass('is-invalid');
            $('#aadhaar_no').next('.invalid-feedback').remove();
        }
    });

    // Add JS for AJAX pagination
    function renderCustomerPagination(total, page, per_page) {
        var totalPages = Math.ceil(total / per_page);
        var $pagination = $('#customerPagination');
        $pagination.empty();
        if (totalPages <= 1) return;
        var prevDisabled = (page <= 1) ? 'disabled' : '';
        var nextDisabled = (page >= totalPages) ? 'disabled' : '';
        $pagination.append('<li class="page-item ' + prevDisabled + '"><a class="page-link" href="#" data-page="' + (page - 1) + '">Previous</a></li>');
        for (var i = 1; i <= totalPages; i++) {
            var active = (i === page) ? 'active' : '';
            $pagination.append('<li class="page-item ' + active + '"><a class="page-link" href="#" data-page="' + i + '">' + i + '</a></li>');
        }
        $pagination.append('<li class="page-item ' + nextDisabled + '"><a class="page-link" href="#" data-page="' + (page + 1) + '">Next</a></li>');
    }
});
</script>

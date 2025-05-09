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
        <h2>Customer Management</h2>

        <!-- Shared Form -->
        <div class="card mb-4">
            <div class="card-header"><h3 class="card-title" id="formTitle">Add New Customer</h3></div>
            <div class="card-body">
                <form id="customerForm">
                    <input type="hidden" name="id" id="customer_id">
                    <div class="row">
                        <div class="col-md-6"><label>Name</label><input type="text" name="name" id="name" class="form-control" required></div>
                        <div class="col-md-6"><label>Address</label><textarea name="address" id="address" class="form-control" rows="2" required></textarea></div>
                        <div class="col-md-4"><label>Phone</label><input type="text" name="phone" id="phone" class="form-control" required></div>
                        <div class="col-md-4"><label>Email</label><input type="email" name="email" id="email" class="form-control"></div>
                        <div class="col-md-4"><label>GST No</label><input type="text" name="gst_no" id="gst_no" class="form-control"></div>
                        <div class="col-md-4"><label>HSN Code</label><input type="text" name="hsn_code" id="hsn_code" class="form-control"></div>
                        <div class="col-md-4"><label>PAN No</label><input type="text" name="pan_no" id="pan_no" class="form-control"></div>
                        <div class="col-md-4"><label>CIN No</label><input type="text" name="cin_no" id="cin_no" class="form-control"></div>
                        <div class="col-md-4"><label>Aadhaar No</label><input type="text" name="aadhaar_no" id="aadhaar_no" class="form-control"></div>
                        <div class="col-md-12">
                            <label>Destinations, Parcel Types, and Weights</label>
                            <table class="table table-bordered" id="multiOptionsTable">
                                <thead>
                                    <tr>
                                        <th>Destination</th>
                                        <th>Parcel Type</th>
                                        <th>Weight</th>
                                        <th>Price</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <select name="destination[]" class="form-control" required>
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
                                            <select name="weight[]" class="form-control weight-select" required>
                                                <option value="">Select Weight</option>
                                                <option value="Upto 100 gm">Upto 100 gm</option>
                                                <option value="Upto 500 gm">Upto 500 gm</option>
                                                <option value="Addl 500 gm">Addl 500 gm</option>
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
                    <button type="submit" class="btn btn-primary mt-3" id="submitBtn">Add Customer</button>
                </form>
                <div id="message" class="mt-2"></div>
            </div>
        </div>

        <!-- Customers Table -->
        <div class="card">
            <div class="card-header"><h3 class="card-title">Customers List</h3></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="table-dark">
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
                            <?php
                            $query = "SELECT * FROM customers ORDER BY id DESC";
                            $result = mysqli_query($conn, $query);
                            $sr = 1;
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td>{$sr}</td>";
                                echo "<td>".htmlspecialchars($row['name'])."</td>";
                                echo "<td>".htmlspecialchars($row['phone'])."</td>";
                                echo "<td>".htmlspecialchars($row['email'])."</td>";
                                echo "<td>".htmlspecialchars($row['gst_no'])."</td>";
                                echo "<td>";
                                echo "<button type='button' class='btn btn-info btn-sm view-details-btn me-1' data-id='{$row['id']}'>View</button> ";
                                echo "<button type='button' class='btn btn-warning btn-sm edit-btn me-1' data-id='{$row['id']}' data-name='".htmlspecialchars($row['name'], ENT_QUOTES)."' data-address='".htmlspecialchars($row['address'], ENT_QUOTES)."' data-phone='".htmlspecialchars($row['phone'], ENT_QUOTES)."' data-email='".htmlspecialchars($row['email'], ENT_QUOTES)."' data-gst='".htmlspecialchars($row['gst_no'], ENT_QUOTES)."' data-hsn='".htmlspecialchars($row['hsn_code'], ENT_QUOTES)."' data-pan='".htmlspecialchars($row['pan_no'], ENT_QUOTES)."' data-cin='".htmlspecialchars($row['cin_no'], ENT_QUOTES)."' data-aadhaar='".htmlspecialchars($row['aadhaar_no'], ENT_QUOTES)."' data-destination='".htmlspecialchars($row['destination'], ENT_QUOTES)."' data-parcel_type='".htmlspecialchars($row['parcel_type'], ENT_QUOTES)."' data-weight='".htmlspecialchars($row['weight'], ENT_QUOTES)."' data-price='".htmlspecialchars($row['price'], ENT_QUOTES)."'>Edit</button> ";
                                echo "<button type='button' class='btn btn-danger btn-sm delete-btn' data-id='{$row['id']}'>Delete</button>";
                                echo "</td>";
                                echo "</tr>";
                                $sr++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Customer Details Modal -->
<div class="modal fade" id="customerDetailsModal" tabindex="-1" aria-labelledby="customerDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customerDetailsModalLabel">Customer Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
</style>


<script>
$(function () {
    function loadCustomers() {
        $.get("fetch_customers.php", function (data) {
            $("#customerTableBody").html(data);
        });
    }

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
        console.log("Raw data-destination:", $(this).data("destination"));
        console.log("Raw data-parcel_type:", $(this).data("parcel_type"));
        console.log("Raw data-weight:", $(this).data("weight"));
        console.log("Raw data-price:", $(this).data("price"));

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

        try {
            let destinations = JSON.parse($(this).data("destination") || "[]");
            let parcelTypes = JSON.parse($(this).data("parcel_type") || "[]");
            let weights = JSON.parse($(this).data("weight") || "[]");
            let prices = JSON.parse($(this).data("price") || "[]");

            // Ensure all arrays have the same length by truncating to the shortest array
            const minLength = Math.min(destinations.length, parcelTypes.length, weights.length, prices.length);
            destinations = destinations.slice(0, minLength);
            parcelTypes = parcelTypes.slice(0, minLength);
            weights = weights.slice(0, minLength);
            prices = prices.slice(0, minLength);

            parcelTypes.forEach((parcelType, index) => {
                const newRow = `
                    <tr>
                        <td>
                            <select name="destination[]" class="form-control" required>
                                <option value="${destinations[index]}" selected>${destinations[index]}</option>
                            </select>
                        </td>
                        <td>
                            <select name="parcel_type[]" class="form-control" required>
                                <option value="Document & Light Parcel" ${parcelType === 'Document & Light Parcel' ? 'selected' : ''}>Document & Light Parcel</option>
                                <option value="Premium" ${parcelType === 'Premium' ? 'selected' : ''}>Premium</option>
                                <option value="Bulk Load Parcel" ${parcelType === 'Bulk Load Parcel' ? 'selected' : ''}>Bulk Load Parcel</option>
                            </select>
                        </td>
                        <td>
                            <select name="weight[]" class="form-control weight-select" required>
                                <option value="">Select Weight</option>
                                <option value="Upto 100 gm" ${weights[index] === 'Upto 100 gm' ? 'selected' : ''}>Upto 100 gm</option>
                                <option value="Upto 500 gm" ${weights[index] === 'Upto 500 gm' ? 'selected' : ''}>Upto 500 gm</option>
                                <option value="Addl 500 gm" ${weights[index] === 'Addl 500 gm' ? 'selected' : ''}>Addl 500 gm</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" name="price[]" class="form-control" value="${prices[index]}" required>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger remove-row">Remove</button>
                        </td>
                    </tr>
                `;
                $("#multiOptionsTable tbody").append(newRow);
            });

            loadDestinationsForAllRows();
        } catch (error) {
            console.error("Error parsing dynamic fields: ", error);
            alert("Failed to load dynamic fields. Please check the data format.");
        }
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

    function getWeightOptions(parcelType) {
        if (parcelType === 'Document & Light Parcel') {
            return [
                { value: 'Upto 100 gm', text: 'Upto 100 gm' },
                { value: 'Upto 500 gm', text: 'Upto 500 gm' },
                { value: 'Addl 500 gm', text: 'Addl 500 gm' }
            ];
        } else if (parcelType === 'Premium') {
            return [
                { value: 'Upto 500 gm', text: 'Upto 500 gm' },
                { value: 'Addl 500 gm', text: 'Addl 500 gm' }
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
            return [{ value: '', text: 'Select Weight' }];
        }
    }

    function updateWeightSelect($row) {
        var parcelType = $row.find("select[name='parcel_type[]']").val();
        var $weightSelect = $row.find("select[name='weight[]']");
        var currentValue = $weightSelect.val();
        var options = getWeightOptions(parcelType);
        $weightSelect.empty();
        options.forEach(function(opt) {
            $weightSelect.append(`<option value="${opt.value}">${opt.text}</option>`);
        });
        $weightSelect.val(currentValue);
    }

    $(document).on('change', "select[name='parcel_type[]']", function () {
        var $row = $(this).closest('tr');
        updateWeightSelect($row);
    });

    $(document).on("click", "#addRow", function () {
        const newRow = `
            <tr>
                <td>
                    <select name="destination[]" class="form-control" required>
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
                    <select name="weight[]" class="form-control weight-select" required>
                        <option value="">Select Weight</option>
                        <option value="Upto 100 gm">Upto 100 gm</option>
                        <option value="Upto 500 gm">Upto 500 gm</option>
                        <option value="Addl 500 gm">Addl 500 gm</option>
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
});
</script>

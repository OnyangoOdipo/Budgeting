<?php
session_start();
include('../../backend/db.php');

// Retrieve department id from session
$department_id = $_SESSION['department_id']; // Ensure this session variable is set correctly

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form inputs
    $currency_id = $_POST['currency_id'];
    $descriptions = $_POST['description'];
    $quantities = $_POST['quantity'];
    $unit_prices = $_POST['unit_price'];
    $brands = $_POST['brand'];
    $colours = $_POST['colour'];

    // Set date_created to the current timestamp
    $date_created = date("Y-m-d H:i:s");

    // Loop through the items and insert each one
    foreach ($descriptions as $index => $description) {
        $quantity = $quantities[$index];
        $unit_price = $unit_prices[$index];
        $brand = $brands[$index];
        $colour = $colours[$index];

        // Calculate the total amount for this item
        $total_amount = $quantity * $unit_price;

        // Insert into items table
        $stmt = $conn->prepare("INSERT INTO items (description, quantity, unit_price, brand, colour) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sidss", $description, $quantity, $unit_price, $brand, $colour);

        if ($stmt->execute()) {
            $item_id = $stmt->insert_id;
        } else {
            echo "Error creating item: " . $stmt->error;
            exit;
        }

        $stmt->close();

        // Insert into budgets table
        $approved_by = null; // Initially null, will be updated later
        $stmt = $conn->prepare("INSERT INTO budgets (approved_by, date_modified, date_created, department_id, currency_id, item_id, total_amount) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issiiid", $approved_by, $date_created, $date_created, $department_id, $currency_id, $item_id, $total_amount);

        if ($stmt->execute()) {
            $budget_id = $stmt->insert_id;
            echo "New budget created successfully with ID: " . $budget_id . "<br>";
        } else {
            echo "Error creating budget: " . $stmt->error;
            exit;
        }

        $stmt->close();

        // Insert into budget_items table
        $stmt = $conn->prepare("INSERT INTO budget_items (budget_id, item_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $budget_id, $item_id);

        if ($stmt->execute()) {
            echo "Budget linked to item successfully.<br>";
        } else {
            echo "Error linking budget to item: " . $stmt->error;
        }

        $stmt->close();

        // Insert message into messages table
        $message = "A new budget has been created for your department.";
        $stmt = $conn->prepare("INSERT INTO messages (department_id, message, created_date) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $department_id, $message, $date_created);

        if ($stmt->execute()) {
            echo "Message inserted successfully.<br>";
        } else {
            echo "Error inserting message: " . $stmt->error;
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>EDITOR | Budget System</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="assets/vendors/feather/feather.css">
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="assets/vendors/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/vendors/typicons/typicons.css">
    <link rel="stylesheet" href="assets/vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css">
    <link rel="stylesheet" type="text/css" href="assets/js/select.dataTables.min.css">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="assets/images/favicon.png" />

        <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap JS (with Popper) -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container-scroller">
        <nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row">
            <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
                <a class="navbar-brand brand-logo" href="indexe.php"><img src="assets/images/logo.svg" alt="logo" /></a>
                <a class="navbar-brand brand-logo-mini" href="indexe.php"><img src="assets/images/logo-mini.svg" alt="logo" /></a>
            </div>
            <div class="navbar-menu-wrapper d-flex align-items-top">
                <ul class="navbar-nav">
                    <li class="nav-item fw-semibold d-none d-lg-block ms-0">
                        <h1 class="welcome-text"><?php echo $greeting; ?>, <span class="text-black fw-bold"><?php echo $first_name . ' ' . $last_name; ?></span></h1>
                        <h3 class="welcome-sub-text">This is your Editor Dashboard</h3>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown d-none d-lg-block user-dropdown">
                        <a class="nav-link" id="UserDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                            <img class="img-xs rounded-circle" src="<?php echo $image_url; ?>" alt="Profile image"> 
                        </a>
                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
                            <div class="dropdown-header text-center">
                                <img class="img-md rounded-circle" src="<?php echo $image_url; ?>" alt="Profile image">
                                <p class="mb-1 mt-3 fw-semibold"><?php echo $first_name . ' ' . $last_name; ?></p>
                                <p class="fw-light text-muted mb-0"><?php echo $email; ?></p>
                            </div>
                            <a class="dropdown-item"><i class="dropdown-item-icon mdi mdi-account-outline text-primary me-2"></i> My Profile</a>
                            <a class="dropdown-item"><i class="dropdown-item-icon mdi mdi-message-text-outline text-primary me-2"></i> Messages</a>
                            <a class="dropdown-item"><i class="dropdown-item-icon mdi mdi-calendar-check-outline text-primary me-2"></i> Activity</a>
                            <a class="dropdown-item"><i class="dropdown-item-icon mdi mdi-help-circle-outline text-primary me-2"></i> FAQ</a>
                            <a class="dropdown-item"><i class="dropdown-item-icon mdi mdi-power text-primary me-2"></i>Sign Out</a>
                        </div>
                    </li>
                </ul>
                <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-bs-toggle="offcanvas">
                    <span class="mdi mdi-menu"></span>
                </button>
            </div>
        </nav>

        <div class="container-fluid page-body-wrapper">
            <nav class="sidebar sidebar-offcanvas" id="sidebar">
                <ul class="nav">
                    <li class="nav-item"><a class="nav-link" href="indexe.php"><i class="mdi mdi-grid-large menu-icon"></i><span class="menu-title">Dashboard</span></a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic"><i class="menu-icon mdi mdi-floor-plan"></i><span class="menu-title">Department</span><i class="menu-arrow"></i></a>
                        <div class="collapse" id="ui-basic"><ul class="nav flex-column sub-menu"><li class="nav-item"> <a class="nav-link" href="viewmembers.php">View Members</a></li></ul></div>
                    </li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="collapse" href="#form-elements" aria-expanded="false" aria-controls="form-elements"><i class="menu-icon mdi mdi-card-text-outline"></i><span class="menu-title">Budgets</span><i class="menu-arrow"></i></a>
                        <div class="collapse" id="form-elements"><ul class="nav flex-column sub-menu"><li class="nav-item"><a class="nav-link" href="createbudget.php">Create Budgets</a></li><li class="nav-item"><a class="nav-link" href="reviewbudget.php">Review Budgets</a></li></ul></div>
                    </li>
                </ul>
            </nav>

            <div class="main-panel">
                <div class="content-wrapper">
                    <h4 class="card-title">Create Budget</h4>
                    <div class="card">
                        <div class="card-body">
                            <form id="budget-form" action="createbudget.php" method="POST">
                                <input type="hidden" name="department_id" value="<?php echo $department_id; ?>">

                                <div id="item-container">
                                    <div class="budget-item">
                                        <div class="form-group">
                                            <label for="currency_id">Currency:</label>
                                            <select name="currency_id" id="currency_id" class="form-control" required>
                                                <option value="1">Kshs</option>
                                                <option value="2">Dollars</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="description">Item Description:</label>
                                            <textarea name="description[]" id="description" class="form-control" required></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="quantity">Quantity:</label>
                                            <input type="number" name="quantity[]" id="quantity" class="form-control" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="unit_price">Unit Price:</label>
                                            <input type="number" step="0.01" name="unit_price[]" id="unit_price" class="form-control" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="brand">Brand:</label>
                                            <input type="text" name="brand[]" id="brand" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label for="colour">Colour:</label>
                                            <input type="text" name="colour[]" id="colour" class="form-control">
                                        </div>
                                        <hr>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="button" id="add-item" class="btn btn-primary">Add More Items</button>
                                </div>

                                <button type="submit" class="btn btn-success">Submit Budget</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

             <!-- Data Submissions -->
    

    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="assets/vendors/chart.js/chart.umd.js"></script>
    <script src="assets/vendors/progressbar.js/progressbar.min.js"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="assets/js/off-canvas.js"></script>
    <script src="assets/js/template.js"></script>
    <script src="assets/js/settings.js"></script>
    <script src="assets/js/hoverable-collapse.js"></script>
    <script src="assets/js/todolist.js"></script>
    <!-- endinject -->
    <!-- Custom js for this page-->
    <script src="assets/js/jquery.cookie.js" type="text/javascript"></script>
    <script src="assets/js/dashboard.js"></script>
    <!-- <script src="assets/js/Chart.roundedBarCharts.js"></script> -->

    <!-- Ensure you have included the necessary libraries -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/progressbar.js"></script>

            <script>
                document.getElementById('add-item').addEventListener('click', function() {
                    var itemContainer = document.getElementById('item-container');
                    var newItem = document.createElement('div');
                    newItem.classList.add('budget-item');
                    newItem.innerHTML = `
                        <div class="form-group">
                            <label for="currency_id">Currency:</label>
                            <select name="currency_id" class="form-control" required>
                                <option value="1">Kshs</option>
                                <option value="2">Dollars</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="description">Item Description:</label>
                            <textarea name="description[]" class="form-control" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="quantity">Quantity:</label>
                            <input type="number" name="quantity[]" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="unit_price">Unit Price:</label>
                            <input type="number" step="0.01" name="unit_price[]" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="brand">Brand:</label>
                            <input type="text" name="brand[]" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="colour">Colour:</label>
                            <input type="text" name="colour[]" class="form-control">
                        </div>
                        <hr>
                    `;
                    itemContainer.appendChild(newItem);
                });
            </script>
        </div>
    </div>
</body>
</html>

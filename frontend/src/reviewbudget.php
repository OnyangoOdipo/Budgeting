<?php
session_start();
include('../../backend/db.php');

// Retrieve department id and user id from session
$department_id = $_SESSION['department_id'] ?? null;
$requested_by = $_SESSION['user_id'] ?? null;

if (!$department_id || !$requested_by) {
    die("Department ID or User ID is not set. Please log in again.");
}

// Initialize messages
$success_message = '';
$error_message = '';
$first_name = '';
$last_name = '';
$email = '';

// Request Review Logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['request_review'])) {
    $budget_id = $_POST['budget_id'];
    $item_id = $_POST['item_id'];

    // Ensure requested_by exists in the users table
    $check_user_stmt = $conn->prepare("SELECT user_id FROM users WHERE user_id = ?");
    $check_user_stmt->bind_param("s", $requested_by);
    $check_user_stmt->execute();
    $check_user_result = $check_user_stmt->get_result();

    if ($check_user_result->num_rows > 0) {
        // Check if a review request already exists for this item and user
        $check_request_stmt = $conn->prepare("SELECT * FROM requests WHERE item_id = ? AND requested_by = ?");
        $check_request_stmt->bind_param("is", $item_id, $requested_by);
        $check_request_stmt->execute();
        $check_request_result = $check_request_stmt->get_result();

        if ($check_request_result->num_rows > 0) {
            $error_message = "You have already requested a review for this item.";
        } else {
            // Insert into requests table
            $stmt = $conn->prepare("INSERT INTO requests (department_id, date_created, requested_by, review_status, item_id) VALUES (?, NOW(), ?, 'requested', ?)");
            $stmt->bind_param("isi", $department_id, $requested_by, $item_id);

            if ($stmt->execute()) {
                $success_message = "Review requested successfully.";
            } else {
                $error_message = "Error requesting review: " . htmlspecialchars($stmt->error);
            }

            $stmt->close();
        }

        $check_request_stmt->close();
    } else {
        $error_message = "Error: User ID does not exist.";
    }

    $check_user_stmt->close();
}

// Delete Budget Logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_budget'])) {
    $budget_id = $_POST['budget_id'];

    $stmt = $conn->prepare("DELETE FROM budgets WHERE id = ?");
    if ($stmt === false) {
        $error_message = 'Prepare failed: ' . htmlspecialchars($conn->error);
    } else {
        $stmt->bind_param("i", $budget_id);

        if ($stmt->execute()) {
            $success_message = "Budget deleted successfully.";

            $delete_items_stmt = $conn->prepare("DELETE FROM budget_items WHERE budget_id = ?");
            $delete_items_stmt->bind_param("i", $budget_id);
            $delete_items_stmt->execute();
            $delete_items_stmt->close();

            $delete_requests_stmt = $conn->prepare("DELETE FROM requests WHERE item_id IN (SELECT id FROM items WHERE budget_id = ?)");
            $delete_requests_stmt->bind_param("i", $budget_id);
            $delete_requests_stmt->execute();
            $delete_requests_stmt->close();
        } else {
            $error_message = "Error deleting budget: " . htmlspecialchars($stmt->error);
        }

        $stmt->close();
    }
}

// Retrieve budgets for review
$sql = "SELECT b.id, b.date_created, SUM(i.quantity * i.unit_price) as total_amount, i.id as item_id, i.description, r.review_status
        FROM budgets b
        JOIN items i ON b.item_id = i.id
        LEFT JOIN requests r ON r.item_id = i.id
        WHERE b.department_id = ? AND (r.review_status IS NULL OR r.review_status NOT IN ('approved', 'rejected'))
        GROUP BY b.id";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('<p class="error-message">Prepare failed: ' . htmlspecialchars($conn->error) . '</p>');
}
$stmt->bind_param("i", $department_id);
if (!$stmt->execute()) {
    die('<p class="error-message">Execute failed: ' . htmlspecialchars($stmt->error) . '</p>');
}
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>EDITOR | Review Budget</title>
    <link rel="stylesheet" href="assets/vendors/feather/feather.css">
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="assets/vendors/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/vendors/typicons/typicons.css">
    <link rel="stylesheet" href="assets/vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css">
    <link rel="stylesheet" type="text/css" href="assets/js/select.dataTables.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="shortcut icon" href="assets/images/favicon.png" />

    <style>
        .success-message {
            color: green;
            font-weight: bold;
        }
        .error-message {
            color: red;
            font-weight: bold;
        }
    </style>
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
                            <a href="../../backend/logout.php" class="dropdown-item"><i class="dropdown-item-icon mdi mdi-power text-primary me-2"></i>Sign Out</a>
                        </div>
                    </li>
                </ul>
                <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-bs-toggle="offcanvas">
                    <span class="mdi mdi-menu"></span>
                </button>
            </div>
        </nav>

        <div class="container-fluid page-body-wrapper">
            <!-- Sidebar -->
            <nav class="sidebar sidebar-offcanvas" id="sidebar">
          <ul class="nav">
            <li class="nav-item">
              <a class="nav-link" href="index.php">
                <i class="mdi mdi-grid-large menu-icon"></i>
                <span class="menu-title">Dashboard</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
                <i class="menu-icon mdi mdi-floor-plan"></i>
                <span class="menu-title">Department</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="ui-basic">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"> <a class="nav-link" href="viewmembers.php">View Members</a></li>
                </ul>
              </div>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#form-elements" aria-expanded="false" aria-controls="form-elements">
                <i class="menu-icon mdi mdi-card-text-outline"></i>
                <span class="menu-title">Budgets</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="form-elements">
                <ul class="nav flex-column sub-menu">
                <li class="nav-item"><a class="nav-link" href="createbudget.php">Create Budgets</a></li>
                  <li class="nav-item"><a class="nav-link" href="reviewbudget.php">Review Budgets</a></li>
                </ul>
              </div>
            </li>
          </ul>
        </nav>

            <!-- Main Content -->
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="row">
                        <div class="col-md-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Review Budgets</h4>
                                    
                                    <?php if ($success_message): ?>
                                        <p class="success-message"><?php echo $success_message; ?></p>
                                    <?php endif; ?>
                                    
                                    <?php if ($error_message): ?>
                                        <p class="error-message"><?php echo $error_message; ?></p>
                                    <?php endif; ?>

                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Date Created</th>
                                                    <th>Item Description</th>
                                                    <th>Total Amount</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($row = $result->fetch_assoc()): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($row['date_created']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                                                        <td><?php echo number_format($row['total_amount'], 2); ?></td>
                                                        <td><?php echo htmlspecialchars($row['review_status']); ?></td>
                                                        <td>
                                                            <?php if ($row['review_status'] !== 'approved' && $row['review_status'] !== 'rejected'): ?>
                                                                <form method="POST" style="display:inline;">
                                                                    <input type="hidden" name="budget_id" value="<?php echo $row['id']; ?>">
                                                                    <input type="hidden" name="item_id" value="<?php echo $row['item_id']; ?>">
                                                                    <button type="submit" name="request_review" class="btn btn-primary btn-sm">Request Review</button>
                                                                </form>
                                                            <?php endif; ?>
                                                            <form method="POST" style="display:inline;">
                                                                <input type="hidden" name="budget_id" value="<?php echo $row['id']; ?>">
                                                                <button type="submit" name="delete_budget" class="btn btn-danger btn-sm">Delete</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <footer class="footer">
                    <div class="container-fluid clearfix">
                        <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright © <?php echo date("Y"); ?> <a href="https://www.example.com" target="_blank">Budgeting System</a>. All rights reserved.</span>
                    </div>
                </footer>
            </div>
        </div>
    </div>

    <script src="assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <script src="assets/vendors/datatables.net/jquery.dataTables.js"></script>
    <script src="assets/vendors/datatables.net-bs4/dataTables.bootstrap4.js"></script>
    <script src="assets/js/off-canvas.js"></script>
    <script src="assets/js/hoverable-collapse.js"></script>
    <script src="assets/js/template.js"></script>
    <script src="assets/js/settings.js"></script>
    <script src="assets/js/todolist.js"></script>
    <script src="assets/js/datatables.js"></script>
</body>
</html>

<?php
$conn->close();
?>

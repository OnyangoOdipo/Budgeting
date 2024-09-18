<?php
session_start();
include('../../backend/db.php');

$first_name = '';
$last_name = '';
$email = '';


if (!isset($_SESSION['user_id'])) {
    die("User not logged in. Please log in again.");
}

$admin_id = $_SESSION['user_id'];

// Update Request Status Logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $request_id = $_POST['request_id'];
    $action = $_POST['action'];

    if ($action == 'approve') {
        $status = 'approved';
        $message_content = "Your budget request ID $request_id has been approved.";
    } elseif ($action == 'reject') {
        $status = 'rejected';
        $message_content = "Your budget request ID $request_id has been rejected.";
    } else {
        die('Invalid action.');
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Update the request status
        $stmt = $conn->prepare("UPDATE requests SET review_status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $request_id);

        if ($stmt->execute()) {
            // Retrieve the department_id for the message
            $stmt = $conn->prepare("SELECT department_id FROM requests WHERE id = ?");
            $stmt->bind_param("i", $request_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $department_id = $row['department_id'];

            // Insert a message into the messages table
            $insert_message_stmt = $conn->prepare("INSERT INTO messages (department_id, message, created_date) VALUES (?, ?, NOW())");
            $insert_message_stmt->bind_param("is", $department_id, $message_content);
            $insert_message_stmt->execute();

            // Commit transaction
            $conn->commit();
            echo "<p class='success-message'>Request status updated to " . htmlspecialchars($status) . " and message sent successfully.</p>";
        } else {
            // Rollback transaction on error
            $conn->rollback();
            echo "<p class='error-message'>Error updating request status: " . htmlspecialchars($stmt->error) . "</p>";
        }

        $stmt->close();
    } catch (Exception $e) {
        // Rollback transaction if an error occurred
        $conn->rollback();
        echo "<p class='error-message'>Transaction failed: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

// Retrieve all requests
$sql = "SELECT r.id, r.date_created, r.requested_by, r.review_status, i.description
        FROM requests r
        JOIN items i ON r.item_id = i.id
        WHERE r.review_status IN ('processing')";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>CEO | Requests</title>
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

        .btn-action {
            margin-right: 5px;
        }
    </style>
</head>

<body>
    <div class="container-scroller">
        <nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row">
            <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
                <a class="navbar-brand brand-logo" href="indexc.php"><img src="assets/images/logo.svg" alt="logo" /></a>
                <a class="navbar-brand brand-logo-mini" href="indexc.php"><img src="assets/images/logo-mini.svg" alt="logo" /></a>
            </div>
            <div class="navbar-menu-wrapper d-flex align-items-top">
                <ul class="navbar-nav">
                    <li class="nav-item fw-semibold d-none d-lg-block ms-0">
                        <h1 class="welcome-text"><?php echo $greeting; ?>, <span class="text-black fw-bold"><?php echo $first_name . ' ' . $last_name; ?></span></h1>
                        <h3 class="welcome-sub-text">CEO Dashboard</h3>
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
            <nav class="sidebar sidebar-offcanvas" id="sidebar">
                <ul class="nav">
                    <li class="nav-item"><a class="nav-link" href="indexc.php"><i class="mdi mdi-grid-large menu-icon"></i><span class="menu-title">Dashboard</span></a></li>
                </ul>
            </nav>

            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Budget Requests</h4>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Description</th>
                                                    <th>Date Created</th>
                                                    <th>Requested By</th>
                                                    <th>Current Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if ($result->num_rows > 0) { ?>
                                                    <?php while ($row = $result->fetch_assoc()) { ?>
                                                        <tr>
                                                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['date_created']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['requested_by']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['review_status']); ?></td>
                                                            <td>
                                                                <form method="POST" action="review_budgets.php" style="display:inline;">
                                                                    <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                                                    <button type="submit" name="action" value="approve" class="btn btn-success btn-action">Approve</button>
                                                                </form>
                                                                <form method="POST" action="review_budgets.php" style="display:inline;">
                                                                    <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                                                    <button type="submit" name="action" value="reject" class="btn btn-danger btn-action">Reject</button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <tr>
                                                        <td colspan="6">No requests to display.</td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <footer class="footer">
                    <div class="container-fluid">
                        <div class="d-sm-flex justify-content-center justify-content-sm-between">
                            <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">© 2024 Budgeting System</span>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    </div>

    <script src="assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="assets/vendors/chart.js/Chart.min.js"></script>
    <script src="assets/vendors/datatables.net/jquery.dataTables.js"></script>
    <script src="assets/vendors/datatables.net-bs4/dataTables.bootstrap4.js"></script>
    <script src="assets/js/off-canvas.js"></script>
    <script src="assets/js/hoverable-collapse.js"></script>
    <script src="assets/js/template.js"></script>
    <script src="assets/js/settings.js"></script>
    <script src="assets/js/todolist.js"></script>
</body>

</html>
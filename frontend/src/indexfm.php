<?php
session_start();
include('../../backend/db.php');

$first_name = '';
$last_name = '';
$email = '';

// Check if the user is logged in and has the role_id of 2 (Viewer)
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 4) {
    header("Location: pages/samples/unauthorized.html");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['allocate_funds'])) {
    $budget_id = $_POST['budget_id'];
    $allocated_amount = $_POST['allocated_amount'];

    $conn->begin_transaction();

    try {
        $check_budget_stmt = $conn->prepare("
            SELECT b.id, b.total_amount, r.department_id
            FROM budgets b 
            JOIN requests r ON b.item_id = r.item_id
            WHERE b.id = ? AND r.review_status = 'approved'
        ");
        $check_budget_stmt->bind_param("i", $budget_id);
        $check_budget_stmt->execute();
        $budget_result = $check_budget_stmt->get_result();
        
        if ($budget_result->num_rows > 0) {
            $budget_row = $budget_result->fetch_assoc();
            $total_amount = $budget_row['total_amount'];
            $department_id = $budget_row['department_id'];

            if ($allocated_amount > 0 && $allocated_amount <= $total_amount) {
                $stmt = $conn->prepare("INSERT INTO fund_allocations (budget_id, allocated_amount) VALUES (?, ?)");
                $stmt->bind_param("id", $budget_id, $allocated_amount);
                
                if ($stmt->execute()) {
                    $update_budget_stmt = $conn->prepare("UPDATE budgets SET total_amount = total_amount - ? WHERE id = ?");
                    $update_budget_stmt->bind_param("di", $allocated_amount, $budget_id);
                    $update_budget_stmt->execute();

                    $message = "Allocated Kshs " . number_format($allocated_amount, 2) . " to budget ID " . $budget_id;
                    $insert_message_stmt = $conn->prepare("INSERT INTO messages (department_id, message, created_date) VALUES (?, ?, NOW())");
                    $insert_message_stmt->bind_param("is", $department_id, $message);
                    $insert_message_stmt->execute();

                    $conn->commit();
                    
                    echo "<p class='success-message'>Funds allocated and message recorded successfully.</p>";
                } else {
                    $conn->rollback();
                    echo "<p class='error-message'>Error allocating funds: " . htmlspecialchars($stmt->error) . "</p>";
                }
                
                $stmt->close();
            } else {
                echo "<p class='error-message'>Invalid allocated amount.</p>";
            }
        } else {
            echo "<p class='error-message'>Budget not found or not approved.</p>";
        }

        $check_budget_stmt->close();
    } catch (Exception $e) {
        $conn->rollback();
        echo "<p class='error-message'>Transaction failed: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

$sql = "SELECT b.id AS budget_id, r.id AS request_id, r.date_created, r.requested_by, r.review_status, i.description, b.total_amount
        FROM requests r
        JOIN items i ON r.item_id = i.id
        JOIN budgets b ON b.item_id = i.id
        WHERE r.review_status = 'approved'";
        
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Finance Manager | Allocate Funds</title>
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
          <div class="me-3">
            <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-bs-toggle="minimize">
              <span class="icon-menu"></span>
            </button>
          </div>
          <div>
            <a class="navbar-brand brand-logo" href="index.php">
              <img src="assets/images/logo.svg" alt="logo" />
            </a>
            <a class="navbar-brand brand-logo-mini" href="index.php">
              <img src="assets/images/logo-mini.svg" alt="logo" />
            </a>
          </div>
        </div>
        <div class="navbar-menu-wrapper d-flex align-items-top">
          <ul class="navbar-nav">
            <li class="nav-item fw-semibold d-none d-lg-block ms-0">
            <h1 class="welcome-text"><?php echo $greeting; ?>, <span class="text-black fw-bold"><?php echo $first_name . ' ' . $last_name; ?></span></h1>
            <h3 class="welcome-sub-text">This is the Finance Managers Dashboard</h3>
            </li>
          </ul>
          <ul class="navbar-nav ms-auto">
            <li class="nav-item d-none d-lg-block">
              <div id="datepicker-popup" class="input-group date datepicker navbar-date-picker">
                <span class="input-group-addon input-group-prepend border-right">
                  <span class="icon-calendar input-group-text calendar-icon"></span>
                </span>
                <input type="text" class="form-control">
              </div>
            </li>
            <li class="nav-item">
              <form class="search-form" action="#">
                <i class="icon-search"></i>
                <input type="search" class="form-control" placeholder="Search Here" title="Search here">
              </form>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link count-indicator" id="notificationDropdown" href="#" data-bs-toggle="dropdown">
                <i class="icon-bell"></i>
                <span class="count"></span>
              </a>
              <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list pb-0" aria-labelledby="notificationDropdown">
                <a class="dropdown-item py-3 border-bottom">
                  <p class="mb-0 fw-medium float-start">You have 4 new notifications </p>
                  <span class="badge badge-pill badge-primary float-end">View all</span>
                </a>
                <a class="dropdown-item preview-item py-3">
                  <div class="preview-thumbnail">
                    <i class="mdi mdi-alert m-auto text-primary"></i>
                  </div>
                  <div class="preview-item-content">
                    <h6 class="preview-subject fw-normal text-dark mb-1">Application Error</h6>
                    <p class="fw-light small-text mb-0"> Just now </p>
                  </div>
                </a>
                <a class="dropdown-item preview-item py-3">
                  <div class="preview-thumbnail">
                    <i class="mdi mdi-lock-outline m-auto text-primary"></i>
                  </div>
                  <div class="preview-item-content">
                    <h6 class="preview-subject fw-normal text-dark mb-1">Settings</h6>
                    <p class="fw-light small-text mb-0"> Private message </p>
                  </div>
                </a>
                <a class="dropdown-item preview-item py-3">
                  <div class="preview-thumbnail">
                    <i class="mdi mdi-airballoon m-auto text-primary"></i>
                  </div>
                  <div class="preview-item-content">
                    <h6 class="preview-subject fw-normal text-dark mb-1">New user registration</h6>
                    <p class="fw-light small-text mb-0"> 2 days ago </p>
                  </div>
                </a>
              </div>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link count-indicator" id="countDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="icon-mail icon-lg"></i>
              </a>
              <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list pb-0" aria-labelledby="countDropdown">
                <a class="dropdown-item py-3">
                  <p class="mb-0 fw-medium float-start">You have 7 unread mails </p>
                  <span class="badge badge-pill badge-primary float-end">View all</span>
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item preview-item">
                  <div class="preview-thumbnail">
                    <img src="assets/images/faces/face10.jpg" alt="image" class="img-sm profile-pic">
                  </div>
                  <div class="preview-item-content flex-grow py-2">
                    <p class="preview-subject ellipsis fw-medium text-dark">Marian Garner </p>
                    <p class="fw-light small-text mb-0"> The meeting is cancelled </p>
                  </div>
                </a>
                <a class="dropdown-item preview-item">
                  <div class="preview-thumbnail">
                    <img src="assets/images/faces/face12.jpg" alt="image" class="img-sm profile-pic">
                  </div>
                  <div class="preview-item-content flex-grow py-2">
                    <p class="preview-subject ellipsis fw-medium text-dark">David Grey </p>
                    <p class="fw-light small-text mb-0"> The meeting is cancelled </p>
                  </div>
                </a>
                <a class="dropdown-item preview-item">
                  <div class="preview-thumbnail">
                    <img src="assets/images/faces/face1.jpg" alt="image" class="img-sm profile-pic">
                  </div>
                  <div class="preview-item-content flex-grow py-2">
                    <p class="preview-subject ellipsis fw-medium text-dark">Travis Jenkins </p>
                    <p class="fw-light small-text mb-0"> The meeting is cancelled </p>
                  </div>
                </a>
              </div>
            </li>
            <li class="nav-item dropdown d-none d-lg-block user-dropdown">
              <a class="nav-link" id="UserDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                <img class="img-xs rounded-circle" src="<? echo $image_url; ?>" alt="Profile image"> </a>
              <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
                <div class="dropdown-header text-center">
                <img class="img-md rounded-circle" src="<?php echo $image_ur; ?>" alt="Profile image">
                <p class="mb-1 mt-3 fw-semibold"><?php echo $first_name . ' ' . $last_name; ?></p>
                <p class="fw-light text-muted mb-0"><?php echo $email; ?></p>
                </div>
                <a class="dropdown-item"><i class="dropdown-item-icon mdi mdi-account-outline text-primary me-2"></i> My Profile <span class="badge badge-pill badge-danger">1</span></a>
                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                    <i class="dropdown-item-icon mdi mdi-calendar-check-outline text-primary me-2"></i> Change Password
                </a>
                <a class="dropdown-item"><i class="dropdown-item-icon mdi mdi-help-circle-outline text-primary me-2"></i> FAQ</a>
                <a class="dropdown-item" href="../../backend/logout.php" ><i class="dropdown-item-icon mdi mdi-power text-primary me-2"></i>Sign Out</a>
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
                    <li class="nav-item"><a class="nav-link" href="indexfm.php"><i class="mdi mdi-grid-large menu-icon"></i><span class="menu-title">Dashboard</span></a></li>
                </ul>
            </nav>
            
            <div class="main-panel">
                <div class="content-wrapper">
                    <h4 class="card-title">Approve and Allocate Funds</h4>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Budget ID</th>
                                                    <th>Request ID</th>
                                                    <th>Date Created</th>
                                                    <th>Requested By</th>
                                                    <th>Description</th>
                                                    <th>Total Amount</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if ($result->num_rows > 0) : ?>
                                                    <?php while ($row = $result->fetch_assoc()) : ?>
                                                        <tr>
                                                            <td><?php echo htmlspecialchars($row['budget_id']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['request_id']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['date_created']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['requested_by']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['total_amount']); ?></td>
                                                            <td>
                                                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#allocateFundsModal" data-budget-id="<?php echo htmlspecialchars($row['budget_id']); ?>" data-amount="<?php echo htmlspecialchars($row['total_amount']); ?>">
                                                                    Allocate Funds
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                <?php else : ?>
                                                    <tr><td colspan="7">No approved budgets available.</td></tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php include('modals.php'); ?>

                <!-- Allocation Modal -->
                <div class="modal fade" id="allocateFundsModal" tabindex="-1" aria-labelledby="allocateFundsModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="allocateFundsModalLabel">Allocate Funds</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="indexfm.php">
                                    <input type="hidden" name="budget_id" id="budget_id">
                                    <div class="mb-3">
                                        <label for="allocated_amount" class="form-label">Allocated Amount</label>
                                        <input type="number" class="form-control" id="allocated_amount" name="allocated_amount" required>
                                    </div>
                                    <button type="submit" name="allocate_funds" class="btn btn-primary">Allocate</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="assets/vendors/chart.js/Chart.min.js"></script>
    <script src="assets/vendors/jquery/jquery.min.js"></script>
    <script src="assets/js/off-canvas.js"></script>
    <script src="assets/js/hoverable-collapse.js"></script>
    <script src="assets/js/template.js"></script>
    <script src="assets/js/settings.js"></script>
    <script src="assets/js/todolist.js"></script>
    <script>
        // Pass data to modal
        $('#allocateFundsModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var budgetId = button.data('budget-id'); // Extract info from data-* attributes
            var amount = button.data('amount');
            var modal = $(this);
            modal.find('.modal-body #budget_id').val(budgetId);
            modal.find('.modal-body #allocated_amount').attr('max', amount); // Set max limit for allocated amount
        });
    </script>
</body>
</html>

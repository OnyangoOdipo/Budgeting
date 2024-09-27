<?php
session_start();
include('../../backend/db.php'); // Include your database connection file

// Check if the user is logged in and has the role of 'Editor'
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 3) {
  header("Location: pages/samples/unauthorized.html");
}

// Get current time in Kenya
date_default_timezone_set('Africa/Nairobi');
$current_hour = date('H');

// Determine greeting based on time
if ($current_hour < 12) {
    $greeting = "Good Morning";
} elseif ($current_hour < 18) {
    $greeting = "Good Afternoon";
} else {
    $greeting = "Good Evening";
}

// Get logged-in user's details
$user_id = $_SESSION['user_id'];

// Retrieve logged-in editor's details
$editor_query = "SELECT first_name, last_name, email FROM users WHERE user_id = ?";
$stmt = $conn->prepare($editor_query);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$editor = $result->fetch_assoc();

$editor_first_name = $editor['first_name'] ?? 'N/A';
$editor_last_name = $editor['last_name'] ?? 'N/A';
$editor_email = $editor['email'] ?? 'N/A';

// Retrieve department details
$department_id = $_SESSION['department_id']; // Assuming department_id is stored in session
$department_query = "SELECT d.department, u.first_name, u.last_name, u.email
                     FROM departments d 
                     LEFT JOIN users u ON d.head_of_department = u.id 
                     WHERE d.id = ?";
$stmt = $conn->prepare($department_query);
$stmt->bind_param("i", $department_id);
$stmt->execute();
$result = $stmt->get_result();
$department = $result->fetch_assoc();

$department_name = $department['department'] ?? 'N/A';
$first_name = $department['first_name'] ?? 'N/A';
$last_name = $department['last_name'] ?? 'N/A';
$email = $department['email'] ?? 'N/A';

// Retrieve member count
$member_query = "SELECT COUNT(*) as member_count FROM users WHERE department_id = ?";
$stmt = $conn->prepare($member_query);
$stmt->bind_param("i", $department_id);
$stmt->execute();
$result = $stmt->get_result();
$member_data = $result->fetch_assoc();
$member_count = $member_data['member_count'] ?? 0;

// Retrieve budget stats
$budgets_query = "SELECT COUNT(*) as budgets_created FROM budgets WHERE department_id = ?";
$stmt = $conn->prepare($budgets_query);
$stmt->bind_param("i", $department_id);
$stmt->execute();
$result = $stmt->get_result();
$budgets_data = $result->fetch_assoc();
$budgets_created = $budgets_data['budgets_created'] ?? 0;

// Retrieve recent budgets
$recent_budgets_query = "SELECT i.description as item, b.date_created 
                         FROM budgets b 
                         LEFT JOIN items i ON b.item_id = i.id 
                         WHERE b.department_id = ? 
                         ORDER BY b.date_created DESC 
                         LIMIT 5";
$stmt = $conn->prepare($recent_budgets_query);
$stmt->bind_param("i", $department_id);
$stmt->execute();
$result = $stmt->get_result();
$recent_budgets = $result->fetch_all(MYSQLI_ASSOC);

// Retrieve budget status counts
$status_query = "SELECT 
                SUM(CASE WHEN r.review_status = 'requested' THEN 1 ELSE 0 END) AS pending_approval_count,
                SUM(CASE WHEN r.review_status = 'approved' THEN 1 ELSE 0 END) AS approved_count,
                SUM(CASE WHEN r.review_status = 'rejected' THEN 1 ELSE 0 END) AS rejected_count
                FROM requests r 
                WHERE r.department_id = ?";
$stmt = $conn->prepare($status_query);
$stmt->bind_param("i", $department_id);
$stmt->execute();
$result = $stmt->get_result();
$status_data = $result->fetch_assoc();
$pending_approval_count = $status_data['pending_approval_count'] ?? 0;
$approved_count = $status_data['approved_count'] ?? 0;
$rejected_count = $status_data['rejected_count'] ?? 0;

// Retrieve recent activities
$recent_activities_query = "SELECT u.first_name AS member_name 
                            FROM process_history ph 
                            LEFT JOIN users u ON ph.approved_by = u.id 
                            WHERE ph.budget_id IN (
                              SELECT id FROM budgets WHERE department_id = ?
                            )
                            ORDER BY ph.created_date DESC 
                            LIMIT 5";
$stmt = $conn->prepare($recent_activities_query);
$stmt->bind_param("i", $department_id);
$stmt->execute();
$result = $stmt->get_result();
$recent_activities = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>EDITOR | BS </title>
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
  <body class="with-welcome-text">
    <div class="container-scroller">
      <div class="row p-0 m-0 proBanner" id="proBanner">
        <div class="col-md-12 p-0 m-0">
        </div>
      </div>
      <!-- partial:partials/_navbar.html -->
      <nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row">
        <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
          <div class="me-3">
            <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-bs-toggle="minimize">
              <span class="icon-menu"></span>
            </button>
          </div>
          <div>
            <a class="navbar-brand brand-logo" href="indexe.php">
              <img src="assets/images/logo.svg" alt="logo" />
            </a>
            <a class="navbar-brand brand-logo-mini" href="indexe.php">
              <img src="assets/images/logo-mini.svg" alt="logo" />
            </a>
          </div>
        </div>
        <div class="navbar-menu-wrapper d-flex align-items-top">
          <ul class="navbar-nav">
            <li class="nav-item fw-semibold d-none d-lg-block ms-0">
            <h1 class="welcome-text"><?php echo $greeting; ?>, <span class="text-black fw-bold"><?php echo $editor_first_name . ' ' . $editor_last_name; ?></span></h1>
            <h3 class="welcome-sub-text">This is your Editor Dashboard</h3>
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

            <?php include('messages.php'); ?>

            <li class="nav-item dropdown d-none d-lg-block user-dropdown">
              <a class="nav-link" id="UserDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                <img class="img-xs rounded-circle" src="<? echo $image_url; ?>" alt="Profile image"> </a>
              <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
                <div class="dropdown-header text-center">
                <img class="img-md rounded-circle" src="<?php echo $image_url; ?>" alt="Profile image">
                <p class="mb-1 mt-3 fw-semibold"><?php echo $editor_first_name . ' ' . $editor_last_name; ?></p>
                <p class="fw-light text-muted mb-0"><?php echo $editor_email; ?></p>
                </div>
                <a class="dropdown-item"><i class="dropdown-item-icon mdi mdi-account-outline text-primary me-2"></i> My Profile <span class="badge badge-pill badge-danger">1</span></a>
                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                    <i class="dropdown-item-icon mdi mdi-calendar-check-outline text-primary me-2"></i> Change Password
                </a>
                <a class="dropdown-item"><i class="dropdown-item-icon mdi mdi-help-circle-outline text-primary me-2"></i> FAQ</a>
                <a class="dropdown-item" href="../../backend/logout.php"><i class="dropdown-item-icon mdi mdi-power text-primary me-2"></i>Sign Out</a>
              </div>
            </li>
          </ul>
          <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-bs-toggle="offcanvas">
            <span class="mdi mdi-menu"></span>
          </button>
        </div>
      </nav>
      <!-- partial -->
      <div class="container-fluid page-body-wrapper">
        <!-- partial:partials/_sidebar.html -->
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

        <div class="main-panel">
  <div class="content-wrapper">
    <!-- Dashboard Cards -->
    <div class="row">
      <div class="col-md-4 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Department Overview</h4>
            <p class="card-description">Department: <?php echo $department_name; ?></p>
            <ul class="list-unstyled">
              <li><strong>HOD</strong> <?php echo $first_name . ' ' . $last_name; ?></li>
              <li><strong>Members</strong> <?php echo $member_count; ?></li>
              <li><strong>Budgets Created</strong> <?php echo $budgets_created; ?></li>
            </ul>
          </div>
        </div>
      </div>

      <div class="col-md-4 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Recent Budget Submissions</h4>
            <p class="card-description">Latest budgets submitted by your department</p>
            <ul class="list-unstyled">
              <?php foreach ($recent_budgets as $budget): ?>
                <li>
                  <strong><?php echo $budget['item']; ?>:</strong> Submitted on <?php echo $budget['date_created']; ?>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
      </div>

      <div class="col-md-4 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Budget Status</h4>
            <p class="card-description">Overview of the status of your budgets</p>
            <ul class="list-unstyled">
              <li><strong>Pending Approval:</strong> <?php echo $pending_approval_count; ?></li>
              <li><strong>Approved:</strong> <?php echo $approved_count; ?></li>
              <li><strong>Rejected:</strong> <?php echo $rejected_count; ?></li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- Budget Actions -->
    <div class="row">
      <div class="col-md-6 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Create a New Budget</h4>
            <p class="card-description">Start a new budget for your department</p>
            <a href="createbudget.php" class="btn btn-primary">Create Budget</a>
          </div>
        </div>
      </div>

      <div class="col-md-6 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Review Budgets</h4>
            <p class="card-description">Edit or review budgets previously created</p>
            <a href="reviewbudget.php" class="btn btn-secondary">Review Budgets</a>
          </div>
        </div>
      </div>
    </div>

    <!-- Department Members and Activities -->
    <div class="row">
      <div class="col-md-6 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Department Members</h4>
            <p class="card-description">View and manage members of your department</p>
            <a href="viewmembers.php" class="btn btn-info">View Members</a>
          </div>
        </div>
      </div>

      <?php include('modals.php'); ?>

      <div class="col-md-6 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Recent Activities</h4>
            <p class="card-description">Recent actions taken by department members</p>
            <ul class="list-unstyled">
              <?php foreach ($recent_activities as $activity): ?>
                <li>
                  <strong><?php echo $activity['member_name']; ?>:</strong> <?php echo $activity['action']; ?>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
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

    </body>
</html>
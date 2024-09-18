<?php
session_start();
include('../../backend/db.php');

// Check if the user is logged in and has the role_id of 2 (Viewer)
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
  header("Location: pages/samples/unauthorized.html");
}

$user_id = $_SESSION['user_id'];
$query = $conn->prepare("SELECT first_name, last_name, email, image FROM users WHERE user_id = ?");
$query->bind_param("s", $user_id);
$query->execute();
$query->bind_result($first_name, $last_name, $email, $image);
$query->fetch();
$query->close();

date_default_timezone_set('Africa/Nairobi');
$current_hour = date('H');

if ($current_hour < 12) {
  $greeting = "Good Morning";
} elseif ($current_hour < 18) {
  $greeting = "Good Afternoon";
} else {
  $greeting = "Good Evening";
}

$image_url = !empty($image) ? '../../uploads/' . $image : 'assets/images/faces/default-face.jpg';

$query1 = "SELECT COUNT(*) as total_budgets FROM budgets";
$result1 = $conn->query($query1);

$total_budgets = 0;
if ($result1->num_rows > 0) {
  $row = $result1->fetch_assoc();
  $total_budgets = $row['total_budgets'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>ADMIN | BS </title>
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
  <!-- endinject -->
  <link rel="shortcut icon" href="assets/images/favicon.png" />

  <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">

  <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>

</head>

<body class="with-welcome-text">
  <div class="container-scroller">
    <div class="row p-0 m-0 proBanner" id="proBanner">
      <div class="col-md-12 p-0 m-0">
      </div>
    </div>
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
            <h3 class="welcome-sub-text">This is your Admin Dashboard</h3>
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
              <a class="dropdown-item"><i class="dropdown-item-icon mdi mdi-message-text-outline text-primary me-2"></i> Messages</a>
              <a class="dropdown-item"><i class="dropdown-item-icon mdi mdi-calendar-check-outline text-primary me-2"></i> Activity</a>
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
    <div class="container-fluid page-body-wrapper">
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
              <span class="menu-title">Users</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="ui-basic">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item"> <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#registerModal">Add Users</a></li>
                <li class="nav-item"> <a class="nav-link" href="manage_users.php">Manage Users</a></li>
              </ul>
            </div>
          </li>
        </ul>
      </nav>
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-sm-12">
              <div class="home-tab">
                <div class="d-sm-flex align-items-center justify-content-between border-bottom">
                  <div>

                  </div>
                </div>
                <div class="tab-content tab-content-basic">
                  <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview">
                    <div class="row">
                      <div class="col-lg-8 d-flex flex-column">
                        <?php
                        $sql = "SELECT r.types_of_role AS role, COUNT(u.id) AS total_users
                          FROM users u
                          JOIN roles r ON u.role_id = r.id
                          GROUP BY r.types_of_role";
                        $result = $conn->query($sql);
                        ?>
                        <div class="row flex-grow">
                          <div class="col-12 col-lg-4 col-lg-12 grid-margin stretch-card">
                            <div class="card card-rounded">
                              <div class="card-body">
                                <div class="d-sm-flex justify-content-between align-items-start">
                                  <div>
                                    <h4 class="card-title card-title-dash">Total Users by Role</h4>
                                    <h5 class="card-subtitle card-subtitle-dash">Overview of users based on their roles</h5>
                                  </div>
                                </div>
                                <div class="mt-4">
                                  <?php if ($result->num_rows > 0): ?>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                      <div class="wrapper d-flex align-items-center justify-content-between py-2 border-bottom">
                                        <div class="d-flex">
                                          <div class="wrapper ms-3">
                                            <p class="ms-1 mb-1 fw-bold"><?php echo htmlspecialchars($row['role']); ?></p>
                                            <small class="text-muted mb-0"><?php echo htmlspecialchars($row['total_users']); ?> users</small>
                                          </div>
                                        </div>
                                      </div>
                                    <?php endwhile; ?>
                                  <?php else: ?>
                                    <p>No users found.</p>
                                  <?php endif; ?>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="col-lg-4 d-flex flex-column">
                        <div class="row flex-grow">
                          <div class="col-md-6 col-lg-12 grid-margin stretch-card">
                            <div class="card bg-primary card-rounded">
                              <div class="card-body pb-0">
                                <h4 class="card-title card-title-dash text-white mb-4">Status Summary</h4>
                                <div class="row">
                                  <div class="col-sm-4">
                                    <p class="status-summary-ight-white mb-1">Total Budgets Submitted</p>
                                    <h2 class="text-info"><?php echo $total_budgets; ?></h2>
                                  </div>
                                  <div class="col-sm-8">
                                    <div class="status-summary-chart-wrapper pb-4">
                                      <canvas id="status-summary"></canvas>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <?php
                          $totalBudgetsQuery = "SELECT COUNT(*) as total_budgets FROM budgets";
                          $totalBudgetsResult = $conn->query($totalBudgetsQuery);
                          $totalBudgets = $totalBudgetsResult->fetch_assoc()['total_budgets'];

                          $approvedBudgetsQuery = "SELECT COUNT(*) as approved_budgets FROM budgets WHERE approved_by IS NOT NULL";
                          $approvedBudgetsResult = $conn->query($approvedBudgetsQuery);
                          $approvedBudgets = $approvedBudgetsResult->fetch_assoc()['approved_budgets'];

                          if ($totalBudgets > 0) {
                            $approvalPercentage = ($approvedBudgets / $totalBudgets) * 100;
                            $pendingApproval = $totalBudgets - $approvedBudgets;
                          } else {
                            $approvalPercentage = 0;
                            $pendingApproval = 0;
                          }
                          ?>

                          <div class="col-md-6 col-lg-12 grid-margin stretch-card">
                            <div class="card card-rounded">
                              <div class="card-body">
                                <div class="row">
                                  <div class="col-lg-6">
                                    <div class="d-flex justify-content-between align-items-center mb-2 mb-sm-0">
                                      <div class="circle-progress-width">
                                        <div id="totalVisitors" class="progressbar-js-circle pr-2"></div>
                                      </div>
                                      <div>
                                        <p class="text-small mb-2">Approved Budgets</p>
                                        <?php if ($totalBudgets > 0): ?>
                                          <h4 class="mb-0 fw-bold"><?php echo round($approvalPercentage, 2); ?>%</h4>
                                        <?php else: ?>
                                          <h4 class="mb-0 fw-bold">No data submitted yet</h4>
                                        <?php endif; ?>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col-lg-6">
                                    <div class="d-flex justify-content-between align-items-center">
                                      <div class="circle-progress-width">
                                        <div id="visitperday" class="progressbar-js-circle pr-2"></div>
                                      </div>
                                      <div>
                                        <p class="text-small mb-2">Pending Approvals</p>
                                        <?php if ($totalBudgets > 0): ?>
                                          <h4 class="mb-0 fw-bold"><?php echo $pendingApproval; ?></h4>
                                        <?php else: ?>
                                          <h4 class="mb-0 fw-bold">No data submitted yet</h4>
                                        <?php endif; ?>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-lg-8 d-flex flex-column">
                        <div class="row flex-grow">
                          <div class="col-12 grid-margin stretch-card">
                            <div class="card card-rounded">
                            </div>
                          </div>
                        </div>
                        <div class="row flex-grow">
                          <div class="col-12 grid-margin stretch-card">
                            <div class="card card-rounded table-darkBGImg">
                              <div class="card-body">
                                <div class="col-sm-8">
                                  <h3 class="text-white upgrade-info mb-0"> Manage Your <span class="fw-bold">Budgets</span> Efficiently </h3>
                                  <a href="#" class="btn btn-info upgrade-btn">Review Budgets</a>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <?php
                        $sql = "SELECT d.department, u.first_name, u.image
                                    FROM departments d
                                    JOIN users u ON d.head_of_department = u.id";
                        $result = $conn->query($sql);
                        ?>

                        <div class="row flex-grow">
                          <div class="col-12 grid-margin stretch-card">
                            <div class="card card-rounded">
                              <div class="card-body">
                                <div class="d-sm-flex justify-content-between align-items-start">
                                  <div>
                                    <h4 class="card-title card-title-dash">Heads of Departments</h4>
                                  </div>
                                </div>
                                <div class="table-responsive mt-1">
                                  <table class="table select-table">
                                    <thead>
                                      <tr>
                                        <th>Department</th>
                                        <th>Head of Department</th>
                                        <th>Image</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                      <?php if ($result->num_rows > 0): ?>
                                        <?php while ($row = $result->fetch_assoc()): ?>
                                          <tr>
                                            <td><?php echo htmlspecialchars($row['department']); ?></td>
                                            <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                                            <td><img src="../uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="HOD Image" style="width: 50px; height: 50px; object-fit: cover;"></td>
                                          </tr>
                                        <?php endwhile; ?>
                                      <?php else: ?>
                                        <tr>
                                          <td colspan="3">No Heads of Departments found.</td>
                                        </tr>
                                      <?php endif; ?>
                                    </tbody>
                                  </table>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="row flex-grow">
                          <div class="col-md-6 col-lg-6 grid-margin stretch-card">
                            <div class="card card-rounded">
                              <div class="card-body card-rounded">
                                <h4 class="card-title  card-title-dash">Recent Events</h4>
                                <div class="list align-items-center border-bottom py-2">
                                  <div class="wrapper w-100">
                                    <p class="mb-2 fw-medium"> Change in Directors </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                      <div class="d-flex align-items-center">
                                        <i class="mdi mdi-calendar text-muted me-1"></i>
                                        <p class="mb-0 text-small text-muted">Mar 14, 2019</p>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="list align-items-center border-bottom py-2">
                                  <div class="wrapper w-100">
                                    <p class="mb-2 fw-medium"> Other Events </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                      <div class="d-flex align-items-center">
                                        <i class="mdi mdi-calendar text-muted me-1"></i>
                                        <p class="mb-0 text-small text-muted">Mar 14, 2019</p>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="list align-items-center border-bottom py-2">
                                  <div class="wrapper w-100">
                                    <p class="mb-2 fw-medium"> Quarterly Report </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                      <div class="d-flex align-items-center">
                                        <i class="mdi mdi-calendar text-muted me-1"></i>
                                        <p class="mb-0 text-small text-muted">Mar 14, 2019</p>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="list align-items-center border-bottom py-2">
                                  <div class="wrapper w-100">
                                    <p class="mb-2 fw-medium"> Change in Directors </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                      <div class="d-flex align-items-center">
                                        <i class="mdi mdi-calendar text-muted me-1"></i>
                                        <p class="mb-0 text-small text-muted">Mar 14, 2019</p>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="list align-items-center pt-3">
                                  <div class="wrapper w-100">
                                    <p class="mb-0">
                                      <a href="#" class="fw-bold text-primary">Show all <i class="mdi mdi-arrow-right ms-2"></i></a>
                                    </p>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-6 col-lg-6 grid-margin stretch-card">
                            <div class="card card-rounded">
                              <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                  <h4 class="card-title card-title-dash">Activities</h4>
                                  <p class="mb-0">
                                    <?php
                                    $query = "SELECT COUNT(*) as total FROM budgets";
                                    $result = $conn->query($query);
                                    $row = $result->fetch_assoc();
                                    echo $row['total'] . " activities recorded";
                                    ?>
                                  </p>
                                </div>
                                <ul class="bullet-line-list">
                                  <?php
                                  $query = "SELECT budgets.date_created, users.username FROM budgets
                                              JOIN users ON budgets.approved_by = users.id
                                              ORDER BY budgets.date_created DESC LIMIT 7";
                                  $result = $conn->query($query);

                                  while ($row = $result->fetch_assoc()) {
                                    echo '<li>';
                                    echo '<div class="d-flex justify-content-between">';
                                    echo '<div><span class="text-light-green">' . htmlspecialchars($row['username']) . '</span> approved a budget</div>';
                                    echo '<p>' . time_elapsed_string($row['date_created']) . '</p>';
                                    echo '</div>';
                                    echo '</li>';
                                  }

                                  function time_elapsed_string($datetime, $full = false)
                                  {
                                    $now = new DateTime;
                                    $ago = new DateTime($datetime);
                                    $diff = $now->diff($ago);

                                    $diff->w = floor($diff->d / 7);
                                    $diff->d -= $diff->w * 7;

                                    $string = array(
                                      'y' => 'year',
                                      'm' => 'month',
                                      'w' => 'week',
                                      'd' => 'day',
                                      'h' => 'hour',
                                      'i' => 'minute',
                                      's' => 'second',
                                    );
                                    foreach ($string as $k => &$v) {
                                      if ($diff->$k) {
                                        $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
                                      } else {
                                        unset($string[$k]);
                                      }
                                    }

                                    if (!$full) $string = array_slice($string, 0, 1);
                                    return $string ? implode(', ', $string) . ' ago' : 'just now';
                                  }
                                  ?>
                                </ul>
                                <div class="list align-items-center pt-3">
                                  <div class="wrapper w-100">
                                    <p class="mb-0">
                                      <a href="#" class="fw-bold text-primary">Show all <i class="mdi mdi-arrow-right ms-2"></i></a>
                                    </p>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-4 d-flex flex-column">
                        <?php
                        $query = "SELECT d.department, SUM(b.item_id * b.currency_id) as total_amount
                                    FROM budgets b
                                    JOIN departments d ON b.department_id = d.id
                                    GROUP BY d.department";
                        $result = $conn->query($query);

                        $departments = [];
                        $amounts = [];

                        while ($row = $result->fetch_assoc()) {
                          $departments[] = $row['department'];
                          $amounts[] = $row['total_amount'];
                        }
                        ?>
                        <div class="row flex-grow">
                          <div class="col-12 grid-margin stretch-card">
                            <div class="card card-rounded">
                              <div class="card-body">
                                <div class="row">
                                  <div class="col-lg-12">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                      <h4 class="card-title card-title-dash">Budget Allocation by Department</h4>
                                    </div>
                                    <div>
                                      <canvas class="my-auto" id="doughnutChart"></canvas>
                                    </div>
                                    <div id="doughnutChart-legend" class="mt-5 text-center"></div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <?php
                        // Fetch departments with their total budgets, ordered by highest budget first
                        $sql = "SELECT d.department, SUM(b.total_amount) as total_budget
                                  FROM departments d
                                  JOIN budgets b ON d.id = b.department_id
                                  GROUP BY d.department
                                  ORDER BY total_budget DESC";
                        $result = $conn->query($sql);

                        ?>
                        <div class="row flex-grow">
                          <div class="col-12 grid-margin stretch-card">
                            <div class="card card-rounded">
                              <div class="card-body">
                                <div class="row">
                                  <div class="col-lg-12">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                      <div>
                                        <h4 class="card-title card-title-dash">Departments by Budget</h4>
                                      </div>
                                    </div>
                                    <div class="mt-3">
                                      <?php if ($result->num_rows > 0): ?>
                                        <?php while ($row = $result->fetch_assoc()): ?>
                                          <div class="wrapper d-flex align-items-center justify-content-between py-2 border-bottom">
                                            <div class="d-flex">
                                              <img class="img-sm rounded-10" src="assets/images/departments/<?php echo htmlspecialchars($row['department']); ?>.jpg" alt="profile">
                                              <div class="wrapper ms-3">
                                                <p class="ms-1 mb-1 fw-bold"><?php echo htmlspecialchars($row['department']); ?></p>
                                                <small class="text-muted mb-0"><?php echo htmlspecialchars(number_format($row['total_budget'], 2)); ?></small>
                                              </div>
                                            </div>
                                            <div class="text-muted text-small">Updated</div>
                                          </div>
                                        <?php endwhile; ?>
                                      <?php else: ?>
                                        <p>No departments found.</p>
                                      <?php endif; ?>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <footer class="footer">
          <div class="d-sm-flex justify-content-center justify-content-sm-between">
            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Budgeting System.</span>
            <span class="float-none float-sm-end d-block mt-1 mt-sm-0 text-center">Copyright © 2024. All rights reserved.</span>
          </div>
        </footer>
        <!-- partial -->
      </div>
      <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>

  <!-- Modal -->
  <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="registerModalLabel">Register New User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          <?php
          if (isset($_SESSION['success']) && !empty($_SESSION['success'])): ?>
            <div class="success-message">
              <p style="color: green;"><?php echo $_SESSION['success']; ?></p>
            </div>
            <?php unset($_SESSION['success']); ?>
          <?php endif; ?>

          <!-- Display errors if there are any -->
          <?php
          if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])): ?>
            <div class="error-messages">
              <?php foreach ($_SESSION['errors'] as $error): ?>
                <p style="color: red;"><?php echo $error; ?></p>
              <?php endforeach; ?>
            </div>
            <?php unset($_SESSION['errors']); ?>
          <?php endif; ?>
        </div>
        <div class="modal-body">
          <form id="registerForm" action="../../backend/register.php" method="post" enctype="multipart/form-data">
            <div class="mb-3">
              <label for="username" class="form-label">Username</label>
              <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
              <label for="first_name" class="form-label">First Name</label>
              <input type="text" class="form-control" id="first_name" name="first_name" required>
            </div>
            <div class="mb-3">
              <label for="last_name" class="form-label">Last Name</label>
              <input type="text" class="form-control" id="last_name" name="last_name" required>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
              <label for="phone_number" class="form-label">Phone Number</label>
              <input type="text" class="form-control" id="phone_number" name="phone_number" required>
            </div>
            <div class="mb-3">
              <label for="role" class="form-label">Role</label>
              <select class="form-select" id="role" name="role" required onchange="toggleDepartmentField()">
                <option value="admin">Admin</option>
                <option value="viewer">Viewer</option>
                <option value="editor">Editor</option>
                <option value="finance_manager">Finance Manager</option>
                <option value="budget_controller">Budget Controller</option>
              </select>
            </div>
            <!-- HOD Checkbox -->
            <div class="mb-3" id="hod-section" style="display: none;">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="is_hod" name="is_hod">
                <label class="form-check-label" for="is_hod">Is Head of Department</label>
              </div>
            </div>
            <div class="mb-3" id="department-section">
              <label for="department" class="form-label">Department</label>
              <select class="form-select" id="department" name="department">
                <option value="Admin&Finance">Admin & Finance</option>
                <option value="Human Resource">Human Resource</option>
                <option value="Sales&Marketing">Sales & Marketing</option>
                <option value="Technical">Technical</option>
                <option value="Operation">Operation</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="image" class="form-label">Profile Image</label>
              <input type="file" class="form-control" id="image" name="image">
            </div>
            <div class="mb-3" id="password-section">
              <label for="password" class="form-label">Password</label>
              <input type="password" class="form-control" id="password" name="password">
            </div>
            <button type="submit" name="register" class="btn btn-primary">Register</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="assets/vendors/js/vendor.bundle.base.js"></script>
  <script src="assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
  <script src="assets/vendors/chart.js/chart.umd.js"></script>
  <script src="assets/vendors/progressbar.js/progressbar.min.js"></script>
  <script src="assets/js/off-canvas.js"></script>
  <script src="assets/js/template.js"></script>
  <script src="assets/js/settings.js"></script>
  <script src="assets/js/hoverable-collapse.js"></script>
  <script src="assets/js/todolist.js"></script>
  <script src="assets/js/jquery.cookie.js" type="text/javascript"></script>
  <script src="assets/js/dashboard.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/progressbar.js"></script>

  <script>
    // Initialize Progress Bars
    var progressBarOptions = {
      strokeWidth: 10,
      color: '#4CAF50',
      trailColor: '#f4f4f4',
      trailWidth: 10,
      easing: 'easeInOut',
      duration: 1400,
      svgStyle: {
        width: '100%',
        height: '100%'
      },
      text: {
        style: {
          color: '#4CAF50',
          position: 'absolute',
          right: '50%',
          top: '50%',
          padding: 0,
          margin: 0,
          transform: {
            prefix: true,
            value: 'translate(0, 0)'
          }
        },
        autoStyleContainer: false
      },
      from: {
        color: '#FF6384'
      },
      to: {
        color: '#36A2EB'
      },
      step: (state, circle) => {
        circle.path.setAttribute('stroke', state.color);
      }
    };

    // Total Visitors Circle Progress
    var totalVisitorsCircle = new ProgressBar.Circle('#totalVisitors', {
      ...progressBarOptions,
      from: {
        color: '#4CAF50'
      },
      to: {
        color: '#4CAF50'
      },
    });

    // Check if totalBudgets is greater than 0 to avoid division by zero
    var approvalPercentage = <?php echo $totalBudgets > 0 ? $approvalPercentage / 100 : 0; ?>;
    totalVisitorsCircle.animate(approvalPercentage);

    // Visits per Day Circle Progress
    var visitPerDayCircle = new ProgressBar.Circle('#visitperday', {
      ...progressBarOptions,
      from: {
        color: '#FF6384'
      },
      to: {
        color: '#FF6384'
      },
    });

    // Ensure pendingApproval is 0 or a valid value to avoid incorrect progress
    var pendingApproval = <?php echo $totalBudgets > 0 ? $pendingApproval / $totalBudgets : 0; ?>;
    visitPerDayCircle.animate(pendingApproval);

    // Doughnut Chart
    var ctx = document.getElementById('doughnutChart').getContext('2d');
    var doughnutChart = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: <?php echo json_encode($departments); ?>,
        datasets: [{
          label: 'Budget Allocation',
          data: <?php echo json_encode($amounts); ?>,
          backgroundColor: [
            '#FF6384',
            '#36A2EB',
            '#FFCE56',
            '#4BC0C0',
            '#9966FF'
          ],
          hoverOffset: 4
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'bottom',
          }
        }
      }
    });

    document.getElementById('role').addEventListener('change', function() {
      var passwordSection = document.getElementById('password-section');
      if (this.value === 'admin') {
        passwordSection.style.display = 'block';
      } else {
        passwordSection.style.display = 'none';
      }
    });

    document.getElementById('role').addEventListener('change', function() {
      const hodSection = document.getElementById('hod-section');
      if (this.value === 'editor') {
        hodSection.style.display = 'block';
      } else {
        hodSection.style.display = 'none';
      }
    });

    function toggleDepartmentField() {
      var role = document.getElementById('role').value;
      var departmentSection = document.getElementById('department-section');

      if (role === 'finance_manager' || role === 'budget_controller') {
        departmentSection.style.display = 'none';
        document.getElementById('department').removeAttribute('required');
      } else {
        departmentSection.style.display = 'block';
        document.getElementById('department').setAttribute('required', 'required');
      }
    }
  </script>
</body>

</html>
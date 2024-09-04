<?php
session_start();
include('../../backend/db.php');

// Check if the user is logged in and has the role_id of 2 (Viewer)
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 3) {
    header("Location: pages/samples/unauthorized.html");
}

$department_id = isset($_SESSION['department_id']) ? $_SESSION['department_id'] : null;
$user_id = $_SESSION['user_id'];

$query = $conn->prepare("SELECT first_name, last_name, email, image FROM users WHERE user_id = ?");
$query->bind_param("s", $user_id);
$query->execute();
$query->bind_result($first_name, $last_name, $email, $image);
$query->fetch();
$query->close();

if (!$department_id) {
    die("Department ID is not set. Please log in again.");
}

date_default_timezone_set('Africa/Nairobi');
$current_hour = date('H');

if ($current_hour < 12) {
    $greeting = "Good Morning";
} elseif ($current_hour < 18) {
    $greeting = "Good Afternoon";
} else {
    $greeting = "Good Evening";
}

// Fetch the department's budgets
$sql = "SELECT b.id AS budget_id, b.date_created, b.date_modified, 
        b.department_id, b.currency_id, i.description, i.quantity, i.unit_price
        FROM budgets b
        JOIN items i ON b.item_id = i.id
        WHERE b.department_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $department_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- (head content remains unchanged) -->
</head>
<body class="with-welcome-text">
    <div class="container-scroller">
        <!-- (navigation and sidebar code remain unchanged) -->
        <div class="container-fluid page-body-wrapper">
            <nav class="sidebar sidebar-offcanvas" id="sidebar">
                <!-- (sidebar content remains unchanged) -->
            </nav>
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Your Department's Budgets</h4>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Budget ID</th>
                                                    <th>Date Created</th>
                                                    <th>Date Modified</th>
                                                    <th>Description</th>
                                                    <th>Quantity</th>
                                                    <th>Unit Price</th>
                                                    <th>Currency</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if ($result->num_rows > 0) {
                                                    while ($row = $result->fetch_assoc()) {
                                                        echo "<tr>";
                                                        echo "<td>" . htmlspecialchars($row['budget_id']) . "</td>";
                                                        echo "<td>" . htmlspecialchars($row['date_created']) . "</td>";
                                                        echo "<td>" . htmlspecialchars($row['date_modified']) . "</td>";
                                                        echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                                                        echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                                                        echo "<td>" . htmlspecialchars($row['unit_price']) . "</td>";
                                                        echo "<td>" . htmlspecialchars($row['currency_id']) . "</td>";
                                                        echo "</tr>";
                                                    }
                                                } else {
                                                    echo "<tr><td colspan='7'>No budgets found for your department.</td></tr>";
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- (footer remains unchanged) -->
            </div>
        </div>
    </div>
    <!-- (scripts remain unchanged) -->
</body>
</html>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../../backend/db.php');

if (!isset($_SESSION['department_id'])) {
    die("Department ID is not set. Please log in again.");
}

$department_id = $_SESSION['department_id'];

// Fetch unread message count
$stmt1 = $conn->prepare("SELECT COUNT(*) as unread_count FROM messages WHERE department_id = ?");
if ($stmt1 === false) {
    die("Prepare failed: " . $conn->error);
}
$stmt1->bind_param("i", $department_id);
$stmt1->execute();
$result1 = $stmt1->get_result();
$row = $result1->fetch_assoc();
$unread_count = $row['unread_count'];
$stmt1->close(); // Close first statement

// Fetch recent messages
$stmt2 = $conn->prepare("SELECT message, created_date FROM messages WHERE department_id = ? ORDER BY created_date DESC LIMIT 3");
if ($stmt2 === false) {
    die("Prepare failed: " . $conn->error);
}
$stmt2->bind_param("i", $department_id);
$stmt2->execute();
$messages_result = $stmt2->get_result();
?>

<li class="nav-item dropdown d-none d-lg-block">
    <a class="nav-link" id="countDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="icon-mail icon-lg"></i>
        <span class="count"><?php echo htmlspecialchars($unread_count); ?></span>
    </a>
    <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list pb-0" aria-labelledby="countDropdown">
        <a class="dropdown-item py-3">
            <p class="mb-0 fw-medium float-start">You have <?php echo htmlspecialchars($unread_count); ?> unread messages</p>
            <span class="badge badge-pill badge-primary float-end">View all</span>
        </a>
        <div class="dropdown-divider"></div>
        <?php while ($row = $messages_result->fetch_assoc()): ?>
            <a class="dropdown-item preview-item">
                <div class="preview-item-content flex-grow py-2">
                    <p class="preview-subject ellipsis fw-medium text-dark"><?php echo htmlspecialchars($row['message']); ?></p>
                    <p class="fw-light small-text mb-0"><?php echo date('F j, Y, g:i a', strtotime($row['created_date'])); ?></p>
                </div>
            </a>
        <?php endwhile; ?>
    </div>
</li>

<?php
$stmt2->close(); // Close second statement
?>

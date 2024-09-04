<?php
session_start();
include('../../backend/db.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $user_id = $_SESSION['user_id'];

    // Validate that new passwords match
    if ($new_password !== $confirm_password) {
        echo "<p class='error-message'>New passwords do not match.</p>";
        exit();
    }

    // Fetch the current password from the database
    $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();
    $stmt->close();

    // Verify the current password
    if (!password_verify($current_password, $hashed_password)) {
        echo "<p class='error-message'>Current password is incorrect.</p>";
        exit();
    }

    // Hash the new password
    $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Update the password in the database
    $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
    $update_stmt->bind_param("ss", $new_hashed_password, $user_id);

    if ($update_stmt->execute()) {
        echo "<p class='success-message'>Password changed successfully.</p>";
    } else {
        echo "<p class='error-message'>Error changing password: " . htmlspecialchars($update_stmt->error) . "</p>";
    }

    $update_stmt->close();
}

?>

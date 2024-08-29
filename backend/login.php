<?php
session_start();
require_once 'db.php'; // Include your database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and execute the SQL query
    $stmt = $conn->prepare("SELECT id, user_id, username, password, role_id, department_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $user_id, $username, $hashed_password, $role_id, $department_id);
        $stmt->fetch();
        
        // Verify the password
        if (password_verify($password, $hashed_password)) {
            // Set session variables
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['role_id'] = $role_id;
            $_SESSION['department_id'] = $department_id;

            // Redirect based on role
            if ($role_id == 1) { // Admin
                header("Location: ../frontend/admin_dashboard.php");
            } elseif ($role_id == 2) { // Viewer
                header("Location: ../frontend/viewer_dashboard.php");
            } elseif ($role_id == 3) { // Editor
                header("Location: ../frontend/editor_dashboard.php");
            } else {
                header("Location: ../frontend/login.html?error=Invalid role");
            }
        } else {
            header("Location: ../frontend/login.html?error=Incorrect password");
        }
    } else {
        header("Location: ../frontend/login.php?error=User not found");
    }
    $stmt->close();
}
$conn->close();
?>

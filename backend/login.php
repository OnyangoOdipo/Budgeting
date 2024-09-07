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
            switch ($role_id) {
                case 1: // Admin
                    header("Location: ../frontend/src/index.php?success=Login successful");
                    break;
                case 2: // Viewer
                    header("Location: ../frontend/src/indexv.php?success=Login successful");
                    break;
                case 3: // Editor
                    header("Location: ../frontend/src/indexe.php?success=Login successful");
                    break;
                case 4: // Finance Manager
                    header("Location: ../frontend/src/indexfm.php?success=Login successful");
                    break;
                case 5: // Budget Controller
                    header("Location: ../frontend/src/indexbc.php?success=Login successful");
                    break;
                case 6: // CEO
                    header("Location: ../frontend/src/indexc.php?success=Login successful");
                    break;
                default:
                    header("Location: ../frontend/login.php?error=Invalid role");
                    break;
            }
        } else {
            header("Location: ../frontend/login.php?error=Incorrect password");
        }
    } else {
        header("Location: ../frontend/login.php?error=User not found");
    }
    $stmt->close();
}
$conn->close();
?>

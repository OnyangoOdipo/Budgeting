<?php
require_once 'db.php';

$errors = [];
$success = '';

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $role = $_POST['role'];
    $password = $_POST['password'];

    // Set department_id to null if user is CEO or Admin
    $department_id = null;
    if ($role !== 'CEO' && $role !== 'admin') {
        $department = $_POST['department'];

        $query = $conn->prepare("SELECT id FROM departments WHERE department = ?");
        $query->bind_param("s", $department);
        $query->execute();
        $query->bind_result($department_id);
        $query->fetch();
        $query->close();

        // Check if department has an HOD
        if ($role === 'editor' && isset($_POST['is_hod'])) {
            $hodCheck = $conn->prepare("SELECT head_of_department FROM departments WHERE id = ? AND head_of_department IS NOT NULL");
            $hodCheck->bind_param("i", $department_id);
            $hodCheck->execute();
            $hodCheck->store_result();
            if ($hodCheck->num_rows > 0) {
                $errors[] = "This department already has a head of department.";
            }
            $hodCheck->close();
        }
    }

    // Check Admin limit (maximum of 3 admins)
    if ($role === 'admin') {
        $adminCheck = $conn->prepare("SELECT COUNT(*) FROM users WHERE role_id = (SELECT id FROM roles WHERE types_of_role = 'admin')");
        $adminCheck->execute();
        $adminCheck->bind_result($admin_count);
        $adminCheck->fetch();
        if ($admin_count >= 3) {
            $errors[] = "The maximum number of admins has been reached.";
        }
        $adminCheck->close();
    }

    // Check CEO limit (only one CEO)
    if ($role === 'CEO') {
        $ceoCheck = $conn->prepare("SELECT COUNT(*) FROM users WHERE role_id = (SELECT id FROM roles WHERE types_of_role = 'CEO')");
        $ceoCheck->execute();
        $ceoCheck->bind_result($ceo_count);
        $ceoCheck->fetch();
        if ($ceo_count >= 1) {
            $errors[] = "There can only be one CEO.";
        }
        $ceoCheck->close();
    }

    // Continue if no errors
    if (empty($errors)) {
        // Set default password for roles and generate user ID
        $prefix = '';
        $password_hashed = '';

        switch ($role) {
            case 'admin':
                $prefix = 'ADM';
                $password_hashed = password_hash($password, PASSWORD_DEFAULT); // Admin sets own password
                break;
            case 'CEO':
                $prefix = 'CEO';
                $password_hashed = password_hash($password, PASSWORD_DEFAULT); // CEO sets own password
                break;
            case 'viewer':
                $prefix = 'VWR';
                $password_hashed = password_hash('Viewer#123', PASSWORD_DEFAULT); // Default password for Viewer
                break;
            case 'editor':
                $prefix = 'EDT';
                $password_hashed = password_hash('Editor#123', PASSWORD_DEFAULT); // Default password for Editor
                break;
            case 'finance_manager':
                $prefix = 'FMG';
                $password_hashed = password_hash('Finance#123', PASSWORD_DEFAULT); // Default password for Finance Manager
                break;
            case 'budget_controller':
                $prefix = 'BGC';
                $password_hashed = password_hash('Controller#123', PASSWORD_DEFAULT); // Default password for Budget Controller
                break;
        }

        $user_id = $prefix . rand(1000, 9999);

        // Handle file upload for profile image
        $image = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image = '../uploads/' . basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $image);
        }

        // Get role_id from roles table
        $query = $conn->prepare("SELECT id FROM roles WHERE types_of_role = ?");
        $query->bind_param("s", $role);
        $query->execute();
        $query->bind_result($role_id);
        $query->fetch();
        $query->close();

        // Insert new user into the users table
        $stmt = $conn->prepare("INSERT INTO users (user_id, username, phone_number, role_id, image, email, first_name, last_name, department_id, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssissssis", $user_id, $username, $phone_number, $role_id, $image, $email, $first_name, $last_name, $department_id, $password_hashed);

        if ($stmt->execute()) {
            if ($role === 'editor' && isset($_POST['is_hod'])) {
                $updateDepartment = $conn->prepare("UPDATE departments SET head_of_department = ? WHERE id = ?");
                $updateDepartment->bind_param("ii", $stmt->insert_id, $department_id);
                if ($updateDepartment->execute()) {
                    $success = "User registered successfully and set as HOD";
                } else {
                    $success = "User registered but failed to set as HOD.";
                }
                $updateDepartment->close();
            } else {
                $success = "User registered successfully";
            }
        } else {
            $errors[] = "Error: " . $stmt->error;
        }

        $stmt->close();
    }

    // Pass errors to the frontend
    session_start();
    $_SESSION['errors'] = $errors;
    $_SESSION['success'] = $success;

    // Redirect to registration page
    header("Location: ../frontend/register.php");
    exit();
}
?>
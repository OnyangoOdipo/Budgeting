<?php

require_once 'db.php';

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $role = $_POST['role'];

    // Handle department based on role
    if ($role === 'finance_manager' || $role === 'budget_controller') {
        $department_id = null;
    } else {
        $department = $_POST['department'];

        $query = $conn->prepare("SELECT id FROM departments WHERE department = ?");
        $query->bind_param("s", $department);
        $query->execute();
        $query->bind_result($department_id);
        $query->fetch();
        $query->close();
    }

    $prefix = '';
    $password = '';

    switch ($role) {
        case 'admin':
            $prefix = 'ADM';
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Admin sets their own password
            break;
        case 'viewer':
            $prefix = 'VWR';
            $password = password_hash('Viewer#123', PASSWORD_DEFAULT); // Default password for Viewer
            break;
        case 'editor':
            $prefix = 'EDT';
            $password = password_hash('Editor#123', PASSWORD_DEFAULT); // Default password for Editor
            break;
        case 'finance_manager':
            $prefix = 'FMG';
            $password = password_hash('Finance#123', PASSWORD_DEFAULT); // Default password for Finance Manager
            break;
        case 'budget_controller':
            $prefix = 'BGC';
            $password = password_hash('Controller#123', PASSWORD_DEFAULT); // Default password for Budget Controller
            break;
    }

    $user_id = $prefix . rand(1000, 9999);

    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = '../uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }

    $query = $conn->prepare("SELECT id FROM roles WHERE types_of_role = ?");
    $query->bind_param("s", $role);
    $query->execute();
    $query->bind_result($role_id);
    $query->fetch();
    $query->close();

    $stmt = $conn->prepare("INSERT INTO users (user_id, username, phone_number, role_id, image, email, first_name, last_name, department_id, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssissssis", $user_id, $username, $phone_number, $role_id, $image, $email, $first_name, $last_name, $department_id, $password);

    if ($stmt->execute()) {
        $new_user_id = $stmt->insert_id;

        // Handle HOD update if role is editor
        if ($role === 'editor' && isset($_POST['is_hod'])) {
            $updateDepartment = $conn->prepare("UPDATE departments SET head_of_department = ? WHERE id = ?");
            $updateDepartment->bind_param("ii", $new_user_id, $department_id);
            if ($updateDepartment->execute()) {
                echo "User registered successfully and set as HOD!";
            } else {
                echo "User registered but failed to set as HOD.";
            }
            $updateDepartment->close();
        } else {
            echo "User registered successfully!";
        }
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
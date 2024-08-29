<?php
require_once 'db.php';

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $role = $_POST['role'];
    $department = $_POST['department'];

    // Determine role prefix and default password
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
    }

    // Generate user_id
    $user_id = $prefix . rand(1000, 9999);

    // Handle image upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = '../uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }

    // Fetch department_id based on department name
    $query = $conn->prepare("SELECT id FROM departments WHERE department = ?");
    $query->bind_param("s", $department);
    $query->execute();
    $query->bind_result($department_id);
    $query->fetch();
    $query->close();

    // Fetch role_id based on role name
    $query = $conn->prepare("SELECT id FROM roles WHERE types_of_role = ?");
    $query->bind_param("s", $role);
    $query->execute();
    $query->bind_result($role_id);
    $query->fetch();
    $query->close();

    // Insert user details into the database
    $stmt = $conn->prepare("INSERT INTO users (user_id, username, phone_number, role_id, image, email, first_name, last_name, department_id, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssissssis", $user_id, $username, $phone_number, $role_id, $image, $email, $first_name, $last_name, $department_id, $password);

    if ($stmt->execute()) {
        echo "User registered successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

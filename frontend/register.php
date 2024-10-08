<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration | BS</title>
    <link rel="stylesheet" href="css/styles.css">
    <!-- icons  -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" crossorigin="anonymous">
    <link rel="shortcut icon" href="src/assets/images/favicon.png" />

    <style>
        .error-message {
            color: red;
            font-size: 0.9em;
            display: block;
            margin-top: 5px;
        }
    </style>

</head>

<body>
    <div class="flex-r container">
        <div class="flex-r login-wrapper">
            <div class="login-text">
                <div class="logo">
                    <span><i class="fab fa-speakap"></i></span>
                    <span>Budgeting System</span>
                </div>
                <h1>Register</h1>
                <p>Create your account</p>

                <?php
                session_start();
                if (isset($_SESSION['success']) && !empty($_SESSION['success'])): ?>
                    <div class="success-message">
                        <p style="color: green;"><?php echo $_SESSION['success']; ?></p>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php
                if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])): ?>
                    <div class="error-messages">
                        <?php foreach ($_SESSION['errors'] as $error): ?>
                            <p style="color: red;"><?php echo $error; ?></p>
                        <?php endforeach; ?>
                    </div>
                    <?php unset($_SESSION['errors']); ?>
                <?php endif; ?>

                <form class="flex-c" action="../backend/register.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                    <div class="input-box">
                        <span class="label">Username</span>
                        <div class="flex-r input">
                            <input type="text" id="username" name="username" required>
                            <i class="fas fa-user"></i>
                        </div>
                    </div>

                    <div class="input-box">
                        <span class="label">First Name</span>
                        <div class="flex-r input">
                            <input type="text" id="first_name" name="first_name" required oninput="validateName('first_name')">
                            <i class="fas fa-user"></i>
                        </div>
                        <span id="first_name_error" class="error-message"></span>
                    </div>

                    <div class="input-box">
                        <span class="label">Last Name</span>
                        <div class="flex-r input">
                            <input type="text" id="last_name" name="last_name" required oninput="validateName('last_name')">
                            <i class="fas fa-user"></i>
                        </div>
                        <span id="last_name_error" class="error-message"></span>
                    </div>

                    <div class="input-box">
                        <span class="label">Email</span>
                        <div class="flex-r input">
                            <input type="email" id="email" name="email" required>
                            <i class="fas fa-at"></i>
                        </div>
                    </div>

                    <div class="input-box">
                        <span class="label">Phone Number</span>
                        <div class="flex-r input">
                            <input type="text" id="phone_number" name="phone_number" required oninput="validatePhoneNumber()">
                            <i class="fas fa-phone"></i>
                        </div>
                        <span id="phone_error" class="error-message"></span>
                    </div>

                    <div class="input-box">
                        <span class="label">Role</span>
                        <div class="flex-r input">
                            <select id="role" name="role" required>
                                <option value="admin">Admin</option>
                                <option value="CEO">CEO</option>
                            </select>
                            <i class="fas fa-user-tag"></i>
                        </div>
                    </div>

                    <div class="input-box">
                        <span class="label">Password</span>
                        <div class="flex-r input">
                            <input type="password" id="password" name="password" class="form-control" required oninput="validatePassword()">
                            <i class="fas fa-lock"></i>
                        </div>
                        <span id="password_error" class="error-message"></span>
                    </div>

                    <div class="input-box">
                        <span class="label">Profile Image</span>
                        <div class="flex-r input">
                            <input type="file" id="image" name="image">
                            <i class="fas fa-image"></i>
                        </div>
                    </div>

                    <input class="btn" type="submit" name="register" value="Register">
                </form>
            </div>
        </div>
    </div>

    <script>
        function validateName(fieldId) {
            const nameInput = document.getElementById(fieldId);
            const errorMessage = document.getElementById(fieldId + '_error');
            const regex = /^[A-Za-z]+$/;

            if (!regex.test(nameInput.value)) {
                errorMessage.textContent = "Only letters are allowed.";
            } else {
                errorMessage.textContent = "";
            }
        }

        function validatePhoneNumber() {
            const phoneInput = document.getElementById('phone_number');
            const phoneError = document.getElementById('phone_error');
            const regex = /^[0-9]{1,10}$/;

            if (!regex.test(phoneInput.value)) {
                phoneError.textContent = "Phone number must be digits only and not more than 10.";
            } else {
                phoneError.textContent = "";
            }
        }

        function validatePassword() {
            const passwordInput = document.getElementById('password');
            const passwordError = document.getElementById('password_error');
            const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

            if (!regex.test(passwordInput.value)) {
                passwordError.textContent = "Password must be at least 8 characters long and contain uppercase, lowercase, number, and a special character.";
            } else {
                passwordError.textContent = "";
            }
        }

        function validateForm() {
            validateName('first_name');
            validateName('last_name');
            validatePhoneNumber();
            validatePassword();

            const firstNameError = document.getElementById('first_name_error').textContent;
            const lastNameError = document.getElementById('last_name_error').textContent;
            const phoneError = document.getElementById('phone_error').textContent;
            const passwordError = document.getElementById('password_error').textContent;

            if (firstNameError || lastNameError || phoneError || passwordError) {
                return false;
            }

            return true;
        }
    </script>

</body>

</html>
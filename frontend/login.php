<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGIN | BS</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <!-- icons  -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" crossorigin="anonymous">
    <link rel="shortcut icon" href="src/assets/images/favicon.png" />
</head>

<body>
  <div class=" flex-r container">
    <div class="flex-r login-wrapper">
      <div class="login-text">
        <div class="logo">
          <span><i class="fab fa-speakap"></i></span>
          <span>Budgeting System</span>
        </div>
        <h1>Sign In</h1>
        <p>Login to get more of this app</p>

        <?php
        if (isset($_GET['error'])) {
            echo '<p class="error">' . htmlspecialchars($_GET['error']) . '</p>';
        }
        ?>

        <form class="flex-c" action="../backend/login.php" method="POST">
          <div class="input-box">
            <span class="label">E-mail</span>
            <div class=" flex-r input">
              <input type="email" id="email" name="email" placeholder="name@abc.com">
              <i class="fas fa-at"></i>
            </div>
          </div>

          <div class="input-box">
            <span class="label">Password</span>
            <div class="flex-r input">
              <input type="password" id="password" name="password" placeholder="8+ (a, A, 1, #)">
              <i class="fas fa-lock"></i>
            </div>
          </div>

          <input class="btn" type="submit" value="Login">
          <span class="extra-line">
            <span>Don't have an account?</span>
            <a href="register.html">Sign Up</a>
          </span>
        </form>

      </div>
    </div>
  </div>
  </body>
</html>
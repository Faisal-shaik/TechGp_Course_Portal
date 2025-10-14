<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>TECHGP - Login</title>
  <link rel="stylesheet" href="interface.css">
</head>
<body>
  <div class="navbar">
    <div class="logo"><a href="interface.html"><img src="TECHGP.png"></a>TECHGP</div>
    <div class="nav-links">
      <a href="interface.html">Home</a>
      <a href="courses.html">Courses</a>
      <a href="about.html">About</a>
      <a href="contact.html">Contact</a>
      <a href="login.php">Login</a>
      <a href="registration.html">Registration</a>
    </div>
  </div>

  <div class="about">
    <h2>Login</h2>

    <!-- Error Message -->
    <?php if (isset($_GET['error'])): ?>
      <p style="color:red; font-weight:bold;">
        <?= htmlspecialchars($_GET['error']); ?>
      </p>
    <?php endif; ?>

    <form action="../COURSES/connect2.php" method="post">
      <label>Email:</label><br>
      <input type="email" name="email" required><br><br>

      <label>Password:</label><br>
      <input type="password" name="password" required><br><br>

      <button type="submit">Login</button><br><br>

      <a href="forgot_password.html">Forgot Password?</a>
    </form>
  </div>
</body>
</html>

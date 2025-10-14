<?php
include 'connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Please login first. <a href='../INTERFACE/login.html'>Go back</a>");
}

$user_id = $_SESSION['user_id'];

// ✅ If form is submitted, insert/update profile
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName  = $_POST['firstName'];
    $lastName   = $_POST['lastName'];
    $dob        = $_POST['dob'];
    $university = $_POST['university'];
    $email      = $_POST['email'];
    $rollno     = $_POST['rollno'];
    $mobile     = $_POST['mobile'];
    $address    = $_POST['address'];

    // Check if profile exists
    $checkSql = "SELECT profile_id FROM profile WHERE user_id = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("i", $user_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        // Update profile
        $sql = "UPDATE profile 
                   SET first_name=?, last_name=?, dob=?, university_name=?, 
                       university_rollno=?, student_mobileno=?, permanent_address=?, email=? 
                 WHERE user_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssi", $firstName, $lastName, $dob, $university, $rollno, $mobile, $address, $email, $user_id);
    } else {
        // Insert profile
        $sql = "INSERT INTO profile (user_id, first_name, last_name, dob, university_name, university_rollno, student_mobileno, permanent_address, email)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssssss", $user_id, $firstName, $lastName, $dob, $university, $rollno, $mobile, $address, $email);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Profile saved successfully'); window.location='profile.php';</script>";
        exit;
    } else {
        echo "❌ Error: " . $stmt->error;
    }
}

// ✅ Fetch username & email from user table
$sql = "SELECT username, email FROM user WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $username = $row['username'];
    $email = $row['email'];
} else {
    $username = "";
    $email = "";
}
$stmt->close();

// ✅ Fetch profile details if already saved
$sql = "SELECT first_name, last_name, dob, university_name, university_rollno, student_mobileno, permanent_address, email 
        FROM profile WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $firstName  = $row['first_name'];
    $lastName   = $row['last_name'];
    $dob        = $row['dob'];
    $university = $row['university_name'];
    $rollno     = $row['university_rollno'];
    $mobile     = $row['student_mobileno'];
    $address    = $row['permanent_address'];
    $email      = $row['email']; // overwrite with latest email
} else {
    $firstName = $lastName = $dob = $university = $rollno = $mobile = $address = "";
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Profile Section - TECHGP</title>
  <link rel="stylesheet" href="home.css">
  <link rel="stylesheet" href="profile.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

  <!-- Navbar -->
  <div class="navbar">
    <div class="logo">
      <a href="home.html">
        <img src="TECHGP.png" alt="TECHGP Logo">
      </a>
      TECHGP
    </div>
    <div class="nav-links">
      <a href="home.html">Home</a>
      <a href="about.html">About</a>
      <a href="courses.php">Courses</a>
      <a href="contact.html">Contact</a>
      <div class="dropdown">
        <button class="dropbtn" id="profileBtn">
          <img src="profile.jpg" alt="Profile Logo">
        </button>
        <div class="dropdown-content" id="dropdownMenu">
          <a href="profile.php">Profile</a>
          <a href="my_courses.php">My Courses</a>
          <a href="#">Progress</a>
          <a href="#">Settings</a>
          <a href="../INTERFACE/interface.html">Logout</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Profile Form -->
  <div class="account-container">
    <h2>Profile Section</h2>
    <form action="profile.php" method="post">
      
      <div class="form-group">
        <label>Username:</label>
        <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" readonly>
      </div>

      <div class="form-group">
        <label for="firstName">First Name</label>
        <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($firstName); ?>" required>
      </div>

      <div class="form-group">
        <label for="lastName">Last Name</label>
        <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($lastName); ?>" required>
      </div>

      <div class="form-group">
        <label for="dob">Date of Birth</label>
        <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($dob); ?>" required>
      </div>

      <div class="form-group">
        <label for="university">University Name</label>
        <input type="text" id="university" name="university" value="<?php echo htmlspecialchars($university); ?>" required>
      </div>

      <div class="form-group">
        <label for="email">Personal Email</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
      </div>

      <div class="form-group">
        <label for="rollno">University Roll No</label>
        <input type="text" id="rollno" name="rollno" value="<?php echo htmlspecialchars($rollno); ?>" required>
      </div>

      <div class="form-group">
        <label for="mobile">Student Mobile Number</label>
        <input type="tel" id="mobile" name="mobile" pattern="[0-9]{10}" placeholder="10-digit number" value="<?php echo htmlspecialchars($mobile); ?>" required>
      </div>

      <div class="form-group">
        <label for="address">Permanent Address</label>
        <textarea id="address" name="address" required><?php echo htmlspecialchars($address); ?></textarea>
      </div>

      <div class="form-actions">
        <button type="submit">Save Details</button>
      </div>
    </form>
  </div>

  <!-- Footer -->
  <footer>
    <p>&copy; 2025 TECHGP - Campus Course Portal | All Rights Reserved</p>
  </footer>

  <!-- Dropdown JS -->
  <script>
    const profileBtn = document.getElementById("profileBtn");
    const dropdownMenu = document.getElementById("dropdownMenu");

    profileBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      dropdownMenu.classList.toggle("show");
    });

    document.addEventListener("click", () => {
      dropdownMenu.classList.remove("show");
    });
  </script>

</body>
</html>

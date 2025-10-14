<?php
include 'db_connect.php';

session_start();
$user_id   =$_SESSION["user_id"]; // Replace with session variable after login

// Fetch enrolled courses
$sql = "
    SELECT c.course_id, c.course_title, c.course_description, e.enroll_date, e.state
    FROM enrollment e
    JOIN courses c ON e.course_id = c.course_id
    WHERE e.user_id = $user_id
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Courses</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="home.css">
    <style>
        body { background-color: #f8f9fa; }
        .card {
            border-radius: 10px;
            transition: 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
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

      <!-- Profile Dropdown -->
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

<div class="container mt-5">
    <h2 class="mb-4 text-center">📚 My Courses</h2>
    <div class="row">
        <?php if ($result->num_rows > 0) { ?>
            <?php while($row = $result->fetch_assoc()) { ?>
                <div class="col-md-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $row['course_title']; ?></h5>
                            <p class="card-text"><?php echo substr($row['course_description'], 0, 120) . "..."; ?></p>
                            <p><small>Enrolled on: <strong><?php echo $row['enroll_date']; ?></strong></small></p>
                            <p><small>Status: <span class="badge bg-info text-dark"><?php echo ucfirst($row['state']); ?></span></small></p>
                            <a href="course_details.php?id=<?php echo $row['course_id']; ?>" class="btn btn-primary w-100">Continue Learning</a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="col-12 text-center">
                <p class="text-muted">⚠️ You haven’t enrolled in any courses yet.</p>
                <a href="courses.php" class="btn btn-success">Browse Courses</a>
            </div>
        <?php } ?>
    </div>
</div>
 <script>
    // Dropdown
    const profileBtn = document.getElementById("profileBtn");
    const dropdownMenu = document.getElementById("dropdownMenu");
    profileBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      dropdownMenu.classList.toggle("show");
    });
    document.addEventListener("click", () => {
      dropdownMenu.classList.remove("show");
    });

    // Slider
    const slider = document.getElementById("slider");
    const prev = document.getElementById("prevBtn");
    const next = document.getElementById("nextBtn");

    prev.addEventListener("click", () => {
      slider.scrollBy({ left: -250, behavior: "smooth" });
    });
    next.addEventListener("click", () => {
      slider.scrollBy({ left: 250, behavior: "smooth" });
    });
  </script>

</body>
</html>

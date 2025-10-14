<?php
include 'db_connect.php'; // your DB connection file

// Fetch all published courses
$sql = "SELECT course_id, course_title, course_description FROM courses WHERE is_published = 1";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Courses</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="home.css">
</head>
<body class="bg-light">
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
    <h2 class="mb-4 text-center">Available Courses</h2>
    <div class="row">
        <?php while($row = $result->fetch_assoc()) { ?>
            <div class="col-md-4">
                <div class="card shadow-lg mb-4">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $row['course_title']; ?></h5>
                        <p class="card-text"><?php echo substr($row['course_description'], 0, 100) . "..."; ?></p>
                        <a href="course_details.php?id=<?php echo $row['course_id']; ?>" target="_blank" class="btn btn-primary">View Details</a>
                    </div>
                </div>
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

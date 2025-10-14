    <?php
include 'db_connect.php';

$user_id = $_GET['user_id'];   // Ideally get this from session after login
$course_id = $_GET['course_id'];

// Check if already enrolled
$check_sql = "SELECT * FROM enrollment WHERE user_id = $user_id AND course_id = $course_id";
$check = $conn->query($check_sql);

if ($check->num_rows > 0) {
    echo "<script>alert('You are already enrolled in this course!'); window.location.href='course_details.php?id=$course_id';</script>";
} else {
    $sql = "INSERT INTO enrollment (user_id, course_id, enroll_date, state) 
            VALUES ($user_id, $course_id, CURDATE(), 'in_progress')";
    if ($conn->query($sql)) {
        echo "<script>alert('Enrolled successfully!'); window.location.href='course_details.php?id=$course_id';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

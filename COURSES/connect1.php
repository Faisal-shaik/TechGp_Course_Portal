<?php
include 'connection.php';
session_start();



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname         = $_POST['fullname'];
    $username         = $_POST['username'];
    $email            = $_POST['email'];
    $phone            = $_POST['phone'];
    $password         = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        die("❌ Passwords do not match. <a href='../INTERFACE/registration.html'>Go back</a>");
    }

    // ✅ Hash password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $sql = "INSERT INTO user (fullname, email, password, username, phone) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $fullname, $email, $hashed_password, $username, $phone);

    if ($stmt->execute()) {
        $user_id=$stmt->insert_id;
        $_SESSION['user_id']=$user_id;
        // Redirect to login after success
        header("Location: ../COURSES/home.html");
        exit;
    } else {
        die("❌ Error: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
} else {
    // If someone accesses this directly without POST
    die("❌ Invalid request method.");
}
?>

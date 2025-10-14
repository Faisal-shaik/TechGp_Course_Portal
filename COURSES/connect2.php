<?php
include 'connection.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Check empty fields
    if (empty($email) || empty($password)) {
        header("Location: ../INTERFACE/login.php?error=❌ Please fill in both fields.");
        exit();
    }

    // Prepare and execute SQL
    $sql = "SELECT * FROM user WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // If user found
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            $_SESSION["user_id"] = $row['user_id'];
            header("Location: ../COURSES/home.html");
            exit();
        } else {
            header("Location: ../INTERFACE/login.php?error=❌ Invalid password.");
            exit();
        }
    } else {
        // Email not found → show inline message
        header("Location: ../INTERFACE/login.php?error=❌ Email is invalid. Please enter.");
        exit();
    }

    $stmt->close();
    $conn->close();
} else {
    echo "❌ Invalid request method.";
}
?>

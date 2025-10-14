<?php
$servername = "localhost";
$username = "root";
$password = "";   // XAMPP MySQL root has no password
$port = 3307;     // XAMPP MySQL port
$dbname = "techgp";

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

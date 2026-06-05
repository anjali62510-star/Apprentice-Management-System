<?php
$host = "localhost";
$user = "root";
$pass = "root"; // default in XAMPP
$db   = "attendance";

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Optional but recommended
$conn->set_charset("utf8mb4");
?>
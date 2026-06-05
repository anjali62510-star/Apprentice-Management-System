<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Prevent false logout on quick page load
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
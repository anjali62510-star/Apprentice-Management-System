<?php
session_start();

if($_SERVER['REQUEST_METHOD']=='POST'){

    $_SESSION['ga_ta'] = $_POST['ga_ta'];
    $_SESSION['trade'] = $_POST['trade'];

    header("Location: section_dashboard.php");
    exit();
}
?>
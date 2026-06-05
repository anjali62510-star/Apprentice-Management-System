<?php
include 'config/db.php';

$stmt = $conn->prepare("UPDATE apprentices SET 
name=?, father_name=?, trade=?, t_no=?, mobile=?, dob=?, doj=?, doe=?, stipend=? 
WHERE id=?");

$stmt->bind_param("ssssssssdi",
    $_POST['name'],
    $_POST['father_name'],
    $_POST['trade'],
    $_POST['t_no'],
    $_POST['mobile'],
    $_POST['dob'],
    $_POST['doj'],
    $_POST['doe'],
    $_POST['stipend'],
    $_POST['id']
);

$stmt->execute();

echo "<script>alert('Record Updated Successfully'); window.location='index.php';</script>";
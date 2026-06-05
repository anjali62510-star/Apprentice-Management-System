<?php
include 'auth.php';
include 'config/db.php';

$user_id = $_SESSION['user_id'];
$date = date('Y-m-d');

// Check if already marked
$sql = "SELECT * FROM attendance 
        WHERE user_id='$user_id' AND attendance_date='$date'";

$result = $conn->query($sql);
$data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Attendance</title>
</head>
<body>

<h2>Welcome</h2>

<?php if (!$data) { ?>

    <form action="../attendance/checkin.php" method="POST">
        <button type="submit">Check In</button>
    </form>

<?php } elseif ($data && empty($data['out_time'])) { ?>

    <form action="../attendance/checkout.php" method="POST">
        <button type="submit">Check Out</button>
    </form>

<?php } else { ?>

    <p>Attendance Completed for Today</p>

<?php } ?>

</body>
</html>
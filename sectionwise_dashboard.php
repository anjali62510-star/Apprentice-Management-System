<?php
include 'auth.php';
include 'config/db.php';
include 'navbar.php';

$date = $_GET['date'] ?? date('Y-m-d');

$section_id = $_SESSION['section_id'] ?? null;

/* ================= BASE QUERY ================= */
if($section_id){

    // SECTION HEAD VIEW (only own section)
    $sections = $conn->query("
        SELECT id, section_name 
        FROM sections 
        WHERE id='$section_id'
    ");

} else {

    // ADMIN VIEW (all sections)
    $sections = $conn->query("
        SELECT id, section_name 
        FROM sections 
        ORDER BY section_name
    ");
}

?>

<!DOCTYPE html>
<html>
<head>
<title>Section Dashboard</title>

<link href="bootstrap.min.css" rel="stylesheet">

<style>
body{
    background:#f4f6f9;
}

.card-box{
    border-radius:12px;
    padding:15px;
    margin-bottom:15px;
    color:#fff;
}

.present{ background:#28a745; }
.absent{ background:#dc3545; }
.total{ background:#0d6efd; }

.section-title{
    font-weight:600;
}
</style>
</head>

<body>

<div class="container mt-4">

<h3>📊 Section Wise Attendance Dashboard</h3>

<!-- DATE FILTER -->
<form method="GET" class="mb-3">
    <input type="date" name="date" value="<?= $date ?>" class="form-control" style="width:200px; display:inline;">
    <button class="btn btn-primary">Filter</button>
</form>

<div class="row">

<?php while($sec = $sections->fetch_assoc()){

    $sid = $sec['id'];

    /* ================= TOTAL ================= */
    $total = $conn->query("
        SELECT COUNT(*) as cnt 
        FROM apprentices 
        WHERE section_id='$sid'
    ")->fetch_assoc()['cnt'];

    /* ================= PRESENT ================= */
    $present = $conn->query("
        SELECT COUNT(*) as cnt
        FROM attendance a
        JOIN apprentices ap ON ap.id = a.user_id
        WHERE ap.section_id='$sid'
        AND a.attendance_date='$date'
        AND a.status='present'
    ")->fetch_assoc()['cnt'];

    /* ================= ABSENT ================= */
    $absent = $conn->query("
        SELECT COUNT(*) as cnt
        FROM apprentices ap
        LEFT JOIN attendance a 
            ON ap.id = a.user_id 
            AND a.attendance_date='$date'
        WHERE ap.section_id='$sid'
        AND (a.status='absent' OR a.status IS NULL)
    ")->fetch_assoc()['cnt'];

?>

<!-- ================= SECTION CARD ================= -->
<div class="col-md-4">

<div class="card shadow p-3">

<h5 class="section-title">
    <?= htmlspecialchars($sec['section_name']) ?>
</h5>

<div class="card-box total">
    Total Apprentices: <?= $total ?>
</div>

<div class="card-box present">
    Present: <?= $present ?>
</div>

<div class="card-box absent">
    Absent: <?= $absent ?>
</div>

<a href="attendance_list.php?date=<?= $date ?>&section_id=<?= $sid ?>" 
   class="btn btn-dark btn-sm w-100 mt-2">
   View Details
</a>

</div>

</div>

<?php } ?>

</div>

</div>

</body>
</html>
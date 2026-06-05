<?php
include 'auth.php';
include 'config/db.php';
include 'navbar.php';

$date = $_GET['date'] ?? date('Y-m-d');

$month = date('m', strtotime($date));
$year  = date('Y', strtotime($date));

$currentMonth = date('m');
$currentYear  = date('Y');

/* ================= TOTAL DAYS ================= */

if($month == $currentMonth && $year == $currentYear){

    $totalDays = date('d', strtotime($date));

} else {

    $totalDays = cal_days_in_month(
        CAL_GREGORIAN,
        $month,
        $year
    );
}

/* ================= MONTHLY QUERY ================= */

$monthlyRes = $conn->query("

SELECT 
    a.id,
    a.name,
    a.trade,
    a.ga_ta,
    a.section_new,

    COUNT(
        CASE 
            WHEN b.status='present'
            THEN 1
        END
    ) AS present_days

FROM apprentices a

LEFT JOIN attendance b
    ON a.id = b.user_id
    AND MONTH(b.attendance_date) = '$month'
    AND YEAR(b.attendance_date) = '$year'
    AND DAY(b.attendance_date) <= '$totalDays'

GROUP BY a.id

ORDER BY a.name ASC

");

?>

<!DOCTYPE html>
<html>

<head>

<title>Monthly Attendance Report</title>

<link href="bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:#f4f6f9;
}

.progress{
    border-radius:20px;
    overflow:hidden;
}

.progress-bar{
    font-weight:bold;
}

.table-hover tbody tr:hover{
    background:#f1f1f1;
}

.card{
    border:none;
    border-radius:15px;
}

</style>

</head>

<body>

<div class="container mt-4">

<div class="d-flex justify-content-between align-items-center mb-4">

<h2>

📅 Monthly Attendance Report

</h2>

<div>

<a href="attendance_list.php?date=<?= $date ?>"
   class="btn btn-secondary">

   ⬅ Back

</a>

<a href="print_monthly.php?date=<?= $date ?>"
   target="_blank"
   class="btn btn-dark">

   🖨️ Print

</a>

</div>

</div>

<!-- ================= FILTER ================= -->

<div class="card shadow-sm mb-4">

<div class="card-body">

<form method="GET" class="row g-3 align-items-end">

<div class="col-md-3">

<label class="fw-bold">
Select Date
</label>

<input type="date"
       name="date"
       value="<?= $date ?>"
       class="form-control">

</div>

<div class="col-md-2">

<button class="btn btn-primary w-100">

    View Report

</button>

</div>

</form>

</div>

</div>

<!-- ================= TABLE ================= -->

<div class="card shadow-lg">

<div class="card-header bg-dark text-white">

<h4 class="mb-0">

Monthly Register - <?= date('F Y', strtotime($date)) ?>

</h4>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered table-hover align-middle">

<thead class="table-dark text-center">

<tr>

<th>#</th>
<th>Name</th>
<th>Trade</th>
<th>Type</th>
<th>Section</th>
<th>Total Days</th>
<th>Present</th>
<th>Absent</th>
<th>Attendance %</th>
<th>Status</th>

</tr>

</thead>

<tbody>

<?php

$sr = 1;

while($m = $monthlyRes->fetch_assoc()) {

    $present = intval($m['present_days']);

    $absent = $totalDays - $present;

    if($absent < 0){
        $absent = 0;
    }

    $percent = 0;

    if($totalDays > 0){

        $percent = round(
            ($present / $totalDays) * 100
        );
    }

    $status = "Poor";
    $badge  = "danger";

    if($percent >= 75){

        $status = "Good";
        $badge  = "success";

    }
    elseif($percent >= 50){

        $status = "Average";
        $badge  = "warning";
    }

?>

<tr>

<td class="text-center fw-bold">

<?= $sr++ ?>

</td>

<td>

<?= htmlspecialchars($m['name']) ?>

</td>

<td class="text-center">

<?= htmlspecialchars($m['trade']) ?>

</td>

<td class="text-center">

<?= htmlspecialchars($m['ga_ta']) ?>

</td>

<td class="text-center">

<?= htmlspecialchars($m['section_new']) ?>

</td>

<td class="text-center">

<span class="badge bg-dark fs-6">

<?= $totalDays ?>

</span>

</td>

<td class="text-center">

<span class="badge bg-success fs-6">

<?= $present ?>

</span>

</td>

<td class="text-center">

<span class="badge bg-danger fs-6">

<?= $absent ?>

</span>

</td>

<td class="text-center">

<div class="progress" style="height:24px;">

<div class="progress-bar bg-info"
     style="width:<?= $percent ?>%">

<?= $percent ?>%

</div>

</div>

</td>

<td class="text-center">

<span class="badge bg-<?= $badge ?>">

<?= $status ?>

</span>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>

</div>

</body>
</html>
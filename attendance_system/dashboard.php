<?php
include 'auth.php';
include 'config/db.php';
include 'navbar.php';

$date = date('Y-m-d');

/* ================= TOTAL ================= */
$total = $conn->query("SELECT COUNT(*) as c FROM apprentices")->fetch_assoc()['c'];

/* ================= GA / TA ================= */
$ga = $conn->query("SELECT COUNT(*) as c FROM apprentices WHERE ga_ta='GA'")->fetch_assoc()['c'];
$ta = $conn->query("SELECT COUNT(*) as c FROM apprentices WHERE ga_ta='TA'")->fetch_assoc()['c'];

/* ================= ATTENDANCE ================= */

$present = $conn->query("
    SELECT COUNT(*) as c 
    FROM attendance 
    WHERE attendance_date='$date'
    AND (status='P' OR status='Present' OR status='1' OR status='IN')
")->fetch_assoc()['c'];

$absent = $conn->query("
    SELECT COUNT(*) as c 
    FROM attendance 
    WHERE attendance_date='$date'
    AND (status='A' OR status='Absent' OR status='0' OR status='OUT')
")->fetch_assoc()['c'];

/* ================= LEAVE ================= */
$pendingLeave = $conn->query("SELECT COUNT(*) as c FROM leaves WHERE status='Pending'")->fetch_assoc()['c'];
$approvedLeave = $conn->query("SELECT COUNT(*) as c FROM leaves WHERE status='Approved'")->fetch_assoc()['c'];
$todayLeave = $conn->query("SELECT COUNT(*) as c FROM leaves WHERE '$date' BETWEEN from_date AND to_date")->fetch_assoc()['c'];

/* ================= TRADE WISE ================= */
$tradeData = $conn->query("
    SELECT trade, COUNT(*) as total 
    FROM apprentices 
    GROUP BY trade
");

$labels = [];
$values = [];

while($row = $tradeData->fetch_assoc()){
    $labels[] = $row['trade'];
    $values[] = $row['total'];
}

/* ================= SECTION WISE ================= */
$sectionData = $conn->query("
    SELECT section_new as section, COUNT(*) as total 
    FROM apprentices 
    WHERE section_new IS NOT NULL AND section_new != ''
    GROUP BY section_new
");

$sectionLabels = [];
$sectionValues = [];

while($row = $sectionData->fetch_assoc()){
    $sectionLabels[] = $row['section'];
    $sectionValues[] = $row['total'];
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>

<link href="bootstrap.min.css" rel="stylesheet">
<script src="chart.js"></script>

<style>
body { background:#f4f6f9; }

.card {
    border-radius:12px;
    box-shadow:0px 2px 10px rgba(0,0,0,0.08);
}

.stat-card {
    padding:20px;
    border-radius:12px;
    color:white;
    text-align:center;
    transition:0.3s;
}

.stat-card:hover { transform:scale(1.05); }

a { text-decoration:none; }

canvas {
    background:#fff;
    padding:10px;
    border-radius:12px;
}
</style>
</head>

<body>

<div class="container mt-4">

<h3>📊 AVNL VFJ - Apprentice Dashboard</h3>

<!-- ================= TOP CARDS ================= -->
<div class="row mt-3">

<div class="col-md-3">
<a href="list.php">
<div class="stat-card bg-primary">
<h2><?= $total ?></h2>
<p>Total Apprentices</p>
</div>
</a>
</div>

<div class="col-md-3">
<a href="attendance_list.php?type=present">
<div class="stat-card bg-success">
<h2><?= $present ?></h2>
<p>Present Today</p>
</div>
</a>
</div>

<div class="col-md-3">
<a href="attendance_list.php?type=absent">
<div class="stat-card bg-danger">
<h2><?= $absent ?></h2>
<p>Absent Today</p>
</div>
</a>
</div>

<div class="col-md-3">
<div class="stat-card bg-warning text-dark">
<h5>GA: <?= $ga ?> | TA: <?= $ta ?></h5>
<p>Category</p>
</div>
</div>

</div>

<br>

<!-- ================= LEAVE STATS (ADDED CLEANLY) ================= -->
<div class="row text-center mb-3">

<div class="col-md-4">
<div class="stat-card bg-secondary">
<h2><?= $pendingLeave ?></h2>
<p>Pending Leaves</p>
</div>
</div>

<div class="col-md-4">
<div class="stat-card bg-info">
<h2><?= $approvedLeave ?></h2>
<p>Approved Leaves</p>
</div>
</div>

<div class="col-md-4">
<div class="stat-card bg-dark">
<h2><?= $todayLeave ?></h2>
<p>On Leave Today</p>
</div>
</div>

</div>

<!-- ================= CHARTS ================= -->
<div class="row">

<div class="col-md-4">
<div class="card p-3">
<h6>GA vs TA</h6>
<canvas id="gaTaChart"></canvas>
</div>
</div>

<div class="col-md-8">
<div class="card p-3">
<h6>Trade Wise Distribution</h6>
<canvas id="tradeChart"></canvas>
</div>
</div>

</div>

<!-- ================= SECTION WISE (ADDED) ================= -->
<br>
<div class="row">
<div class="col-md-12">
<div class="card p-3">
<h6>Section Wise Distribution</h6>
<canvas id="sectionChart"></canvas>
</div>
</div>
</div>

<!-- ================= BOTTOM BUTTONS ================= -->
<br><br>

<div class="row text-center">

<div class="col-md-4">
<a href="attendance_list.php">
<div class="card p-4 bg-dark text-white">
<h5>📋 View Attendance</h5>
<p>Check full attendance records</p>
</div>
</a>
</div>

<div class="col-md-4">
<a href="assign_section.php">
<div class="card p-4 bg-info text-white">
<h5>🧩 Assign Sections</h5>
<p>Allocate apprentices to sections</p>
</div>
</a>
</div>

<div class="col-md-4">
<a href="attendance_upload.php">
<div class="card p-4 bg-primary text-white">
<h5>⬆ Upload Attendance</h5>
<p>Mark / Import Attendance</p>
</div>
</a>
</div>

<!-- LEAVE BUTTONS (ADDED) -->
<div class="col-md-4">
<a href="leave_apply.php">
<div class="card p-4 bg-warning text-dark">
<h5>📝 Apply Leave</h5>
<p>Create apprentice leave request</p>
</div>
</a>
</div>

<div class="col-md-4">
<a href="leave_list.php">
<div class="card p-4 bg-secondary text-white">
<h5>📅 Leave Records</h5>
<p>Manage apprentice leaves</p>
</div>
</a>
</div>
<div class="col-md-4">
<a href="leave_approval.php">
<div class="card p-4 bg-danger text-white">
<h5>✅ Approve Leaves</h5>
<p>Manage pending requests</p>
</div>
</a>
</div>
</div>

</div>

<!-- ================= CHART JS ================= -->
<script>

new Chart(document.getElementById("gaTaChart"), {
type:'pie',
data:{labels:['GA','TA'],datasets:[{data:[<?= $ga ?>,<?= $ta ?>],backgroundColor:['#36A2EB','#d68899']}]}
});

new Chart(document.getElementById("tradeChart"), {
type:'bar',
data:{labels:<?= json_encode($labels) ?>,datasets:[{data:<?= json_encode($values) ?>,backgroundColor:'#4BC0C0'}]}
});

new Chart(document.getElementById("sectionChart"), {
type:'bar',
data:{labels:<?= json_encode($sectionLabels) ?>,datasets:[{data:<?= json_encode($sectionValues) ?>,backgroundColor:'#9966FF'}]}
});

</script>

</body>
</html>
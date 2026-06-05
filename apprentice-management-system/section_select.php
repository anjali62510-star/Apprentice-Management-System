<?php 
include 'auth.php';
include 'config/db.php';
session_start();

if($_SESSION['role'] == 'section_head'){
    header("Location: section_dashboard.php");
    exit();
}
/* ===== DATA ===== */
$ga = $conn->query("SELECT COUNT(*) c FROM apprentices WHERE ga_ta='GA'")->fetch_assoc()['c'];
$ta = $conn->query("SELECT COUNT(*) c FROM apprentices WHERE ga_ta='TA'")->fetch_assoc()['c'];

$tradeQ = $conn->query("SELECT trade, COUNT(*) c FROM apprentices GROUP BY trade");

$labels = [];
$values = [];

while($r = $tradeQ->fetch_assoc()){
    $labels[] = $r['trade'];
    $values[] = $r['c'];
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Select Section</title>

<link href="bootstrap.min.css" rel="stylesheet">
<script src="chart.js"></script>

<style>
body {
    background: linear-gradient(135deg,#1e3c72,#2a5298);
    color:#fff;
}
.card {
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
    border-radius:15px;
}
.btn-main {
    background: linear-gradient(45deg,#00c6ff,#0072ff);
    border:none;
}
canvas {
    background:#fff;
    border-radius:10px;
    padding:10px;
}
</style>

</head>

<body>

<div class="container mt-4">

<h3>📊 Section Selection</h3>

<!-- STATS -->
<div class="row text-center mb-3">
<div class="col-md-6">
<div class="card p-3 bg-success">
<h2><?= $ga ?></h2>
<p>GA</p>
</div>
</div>
<div class="col-md-6">
<div class="card p-3 bg-danger">
<h2><?= $ta ?></h2>
<p>TA</p>
</div>
</div>
</div>

<!-- CHARTS -->
<div class="row">
<div class="col-md-4">
<div class="card p-3">
<h6>GA vs TA</h6>
<canvas id="gaChart"></canvas>
</div>
</div>

<div class="col-md-8">
<div class="card p-3">
<h6>Trade Distribution</h6>
<canvas id="tradeChart"></canvas>
</div>
</div>
</div>

<br>

<!-- FORM -->
<div class="card p-4 shadow">

<form method="POST" action="set_section.php">

<div class="row">

<div class="col-md-3">
<label>Category</label>
<select name="ga_ta" class="form-control" required>
<option value="GA">GA</option>
<option value="TA">TA</option>
</select>
</div>

<div class="col-md-6">
<label>Trade</label>
<select name="trade" class="form-control" required>

<option>COPA</option>
<option>ELECTRICIAN</option>
<option>SHEET METAL WORKER</option>
<option>WELDER</option>
<option>MECHANIC DIESEL</option>
<option>MMV</option>
<option>TRACTOR MACHANIC</option>
<option>MACHINIST</option>
<option>FITTER</option>
<option>TURNER</option>
<option>PLUMBER</option>
<option>DRAUGHTSMAN</option>
<option>CARPENTER</option>
<option>SURVERYOR</option>
<option>MACHINIST (GRINDRE)</option>
<option>PAINTER</option>
<option>STENOGRAPHER</option>
<option>ICTSM</option>

</select>
</div>

<div class="col-md-3 d-flex align-items-end">
<button class="btn btn-main w-100">Enter Dashboard</button>
</div>

</div>

</form>

</div>

</div>

<script>
new Chart(document.getElementById("gaChart"), {
type:'pie',
data:{
labels:['GA','TA'],
datasets:[{data:[<?= $ga ?>,<?= $ta ?>]}]
}
});

new Chart(document.getElementById("tradeChart"), {
type:'bar',
data:{
labels:<?= json_encode($labels) ?>,
datasets:[{data:<?= json_encode($values) ?>}]
}
});
</script>

</body>
</html>
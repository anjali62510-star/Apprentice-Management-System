<?php
session_start();
include 'config/db.php';

// ================= CHECK LOGIN & ROLE =================
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

if($_SESSION['role'] != 'section_head' && $_SESSION['role'] != 'admin'){
    die("Access Denied");
}

$ga_ta = $_SESSION['ga_ta'] ?? '';
$trade = $_SESSION['trade'] ?? '';

// ================= VALIDATE ID =================
if(!isset($_GET['id'])){
    header("Location: section_dashboard.php?msg=invalid");
    exit();
}

$id = intval($_GET['id']);
if($id <= 0){
    header("Location: section_dashboard.php?msg=invalid");
    exit();
}

// ================= FETCH ATTENDANCE DATA =================
$stmt = $conn->prepare("
SELECT a.*, ap.name, ap.ga_ta, ap.trade
FROM attendance a
JOIN apprentices ap ON a.user_id = ap.id
WHERE a.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if(!$data){
    header("Location: section_dashboard.php?msg=notfound");
    exit();
}

// ================= SECTION CHECK =================
if($_SESSION['role'] == 'section_head'){
    if($data['ga_ta'] != $ga_ta || $data['trade'] != $trade){
        die("Access Denied: This record does not belong to your section");
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Attendance</title>
<link href="bootstrap.min.css" rel="stylesheet">
<style>
body { background:#f4f6f9; }
.card { border-radius:12px; }
</style>
</head>
<body>

<div class="container mt-4">

<h3>✏️ Edit Attendance</h3>

<div class="card shadow p-4">

<form action="attendance_update.php" method="POST">
<input type="hidden" name="id" value="<?= $data['id'] ?>">

<div class="row mb-3">
<div class="col-md-4">
<label>Apprentice Name</label>
<input type="text" class="form-control" value="<?= htmlspecialchars($data['name']) ?>" disabled>
</div>

<div class="col-md-4">
<label>Date</label>
<input type="date" name="attendance_date" class="form-control" value="<?= $data['attendance_date'] ?>" required>
</div>

<div class="col-md-4">
<label>Status</label>
<select name="status" class="form-control" required>
<option value="Present" <?= ($data['status']=='Present')?'selected':'' ?>>Present</option>
<option value="Absent" <?= ($data['status']=='Absent')?'selected':'' ?>>Absent</option>
</select>
</div>
</div>

<div class="row mb-3">
<div class="col-md-4">
<label>Arrival Time</label>
<input type="time" name="arr_time" class="form-control" value="<?= htmlspecialchars($data['arr_time']) ?>">
</div>

<div class="col-md-4">
<label>Exit Time</label>
<input type="time" name="out_time" class="form-control" value="<?= htmlspecialchars($data['out_time']) ?>">
</div>

<div class="col-md-4">
<label>Remarks</label>
<input type="text" name="remarks" class="form-control" value="<?= htmlspecialchars($data['remarks']) ?>">
</div>
</div>

<button class="btn btn-success">Update Attendance</button>
<a href="section_dashboard.php" class="btn btn-secondary">Back</a>

</form>

</div>
</div>

<script src="bootstrap.bundle.min.js"></script>

</body>
</html>
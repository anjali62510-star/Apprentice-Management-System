<?php 
include 'auth.php'; 
include 'config/db.php';

$date   = $_GET['date'] ?? date('Y-m-d');
$ga_ta  = $_GET['ga_ta'] ?? '';
$trade  = $_GET['trade'] ?? '';

/* ================= ROLE FILTER ================= */
$where = "WHERE 1=1";

if($_SESSION['role'] == 'section_head'){
    $ga_ta = $_SESSION['ga_ta'];
    $trade = $_SESSION['trade'];

    $where .= " AND ap.ga_ta='$ga_ta' AND ap.trade='$trade'";
}

/* ================= USER FILTER ================= */
if(!empty($date)){
    $where .= " AND a.attendance_date='$date'";
}
if(!empty($ga_ta)){
    $where .= " AND ap.ga_ta='$ga_ta'";
}
if(!empty($trade)){
    $where .= " AND ap.trade='$trade'";
}

$sql = "SELECT a.*, ap.name, ap.trade, ap.ga_ta
FROM attendance a
JOIN apprentices ap ON a.user_id = ap.id
$where
ORDER BY ap.name ASC";

$res = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
<title>Attendance List</title>

<link href="bootstrap.min.css" rel="stylesheet">
<link href="dataTables.bootstrap5.min.css" rel="stylesheet">

<script src="jquery-3.7.0.min.js"></script>
<script src="jquery.dataTables.min.js"></script>

</head>

<body class="bg-light">

<div class="container mt-4">

<h3>📊 Attendance List</h3>

<!-- ================= FILTER ================= -->
<form method="GET" class="row g-2 mb-3">

<div class="col-md-3">
<input type="date" name="date" value="<?= $date ?>" class="form-control">
</div>

<div class="col-md-2">
<select name="ga_ta" class="form-control">
<option value="">All</option>
<option value="GA" <?= ($ga_ta=='GA')?'selected':'' ?>>GA</option>
<option value="TA" <?= ($ga_ta=='TA')?'selected':'' ?>>TA</option>
</select>
</div>

<div class="col-md-3">
<select name="trade" class="form-control">
<option value="">All Trades</option>
<?php
$trades = $conn->query("SELECT DISTINCT trade FROM apprentices");
while($t = $trades->fetch_assoc()){
    $sel = ($trade == $t['trade']) ? 'selected' : '';
    echo "<option $sel>".$t['trade']."</option>";
}
?>
</select>
</div>

<div class="col-md-2">
<button class="btn btn-primary w-100">Filter</button>
</div>

<div class="col-md-2">
<a href="print_attendance.php?date=<?= $date ?>&ga_ta=<?= $ga_ta ?>&trade=<?= $trade ?>" 
class="btn btn-success w-100">🖨 Print</a>
</div>

</form>

<!-- ================= ADD BUTTON (ADMIN ONLY) ================= -->
<?php if($_SESSION['role'] == 'admin'): ?>
<button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addModal">
➕ Add Attendance
</button>
<?php endif; ?>

<!-- ================= TABLE ================= -->
<div class="card shadow">
<div class="card-body">

<table id="attTable" class="table table-bordered table-striped">

<thead class="table-dark">
<tr>
<th>Name</th>
<th>Trade</th>
<th>GA/TA</th>
<th>Date</th>
<th>In</th>
<th>Out</th>
<th>Status</th>
<th>Remarks</th>
<?php if($_SESSION['role'] == 'admin'): ?>
<th>Action</th>
<?php endif; ?>
</tr>
</thead>

<tbody>

<?php while($r = $res->fetch_assoc()): ?>
<tr>
<td><?= htmlspecialchars($r['name']) ?></td>
<td><?= $r['trade'] ?></td>
<td><?= $r['ga_ta'] ?></td>
<td><?= $r['attendance_date'] ?></td>
<td><?= $r['arr_time'] ?></td>
<td><?= $r['out_time'] ?></td>
<td><?= $r['status'] ?></td>
<td><?= $r['remarks'] ?></td>

<?php if($_SESSION['role'] == 'admin'): ?>
<td>
<a href="edit.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
<a href="delete.php?id=<?= $r['id'] ?>" 
class="btn btn-sm btn-danger"
onclick="return confirm('Delete this record?')">Delete</a>
</td>
<?php endif; ?>

</tr>
<?php endwhile; ?>

</tbody>
</table>

</div>
</div>

</div>

<!-- ================= ADD MODAL ================= -->
<div class="modal fade" id="addModal">
<div class="modal-dialog">
<div class="modal-content">

<form method="POST" action="attendance_add.php">

<div class="modal-header">
<h5>Add Attendance</h5>
</div>

<div class="modal-body">

<select name="user_id" class="form-control mb-2" required>
<option value="">Select Apprentice</option>
<?php
$apps = $conn->query("SELECT id,name FROM apprentices");
while($a = $apps->fetch_assoc()){
echo "<option value='".$a['id']."'>".$a['name']."</option>";
}
?>
</select>

<input type="date" name="date" class="form-control mb-2" required>

<input type="time" name="in" class="form-control mb-2">
<input type="time" name="out" class="form-control mb-2">

<select name="status" class="form-control mb-2">
<option value="Present">Present</option>
<option value="Absent">Absent</option>
</select>

<input type="text" name="remarks" class="form-control" placeholder="Remarks">

</div>

<div class="modal-footer">
<button class="btn btn-primary">Save</button>
</div>

</form>

</div>
</div>
</div>

<script>
$(document).ready(function() {
    $('#attTable').DataTable();
});
</script>

<script src="bootstrap.bundle.min.js"></script>

</body>
</html>
<?php
include 'auth.php';
include 'config/db.php';
include 'navbar.php';

$date = $_GET['date'] ?? date('Y-m-d');

/* ================= HANDLE ACTION ================= */
if(isset($_POST['action'])){

    $id = intval($_POST['id']);
    $remark = $conn->real_escape_string($_POST['remark'] ?? '');

    /* ================= PRESENT ================= */
if($_POST['action'] == 'present'){

    // fetch old remark
    $old = $conn->query("
        SELECT remarks FROM attendance
        WHERE user_id='$id' AND attendance_date='$date'
    ")->fetch_assoc();

    $oldRemark = $old['remarks'] ?? '';

    // if textbox changed use new remark
    if($remark == ''){
        $remark = $oldRemark;
    }

    $check = $conn->query("
        SELECT id FROM attendance 
        WHERE user_id='$id' AND attendance_date='$date'
    ");

    if($check->num_rows == 0){

        $conn->query("
            INSERT INTO attendance 
            (user_id, attendance_date, status, remarks)
            VALUES ('$id','$date','present','$remark')
        ");

    } else {

        $conn->query("
            UPDATE attendance 
            SET status='present',
                remarks='$remark'
            WHERE user_id='$id' AND attendance_date='$date'
        ");
    }
  header("Location: attendance_list.php?date=$date");
exit(); 
}
 
/* ================= ABSENT ================= */
if($_POST['action'] == 'absent'){

    // get old remark
    $old = $conn->query("
        SELECT remarks FROM attendance
        WHERE user_id='$id' AND attendance_date='$date'
    ")->fetch_assoc();

    $oldRemark = $old['remarks'] ?? '';

    // keep old if textbox empty
    if($remark == ''){
        $remark = $oldRemark;
    }

    $check = $conn->query("
        SELECT id FROM attendance 
        WHERE user_id='$id' AND attendance_date='$date'
    ");

    if($check->num_rows == 0){

        $conn->query("
            INSERT INTO attendance
            (user_id, attendance_date, status, remarks)
            VALUES ('$id','$date','absent','$remark')
        ");

    } else {

        $conn->query("
            UPDATE attendance 
            SET status='absent',
                remarks='$remark'
            WHERE user_id='$id' AND attendance_date='$date'
        ");
    }
}
}
/* ================= PRESENT LIST ================= */
$presentRes = $conn->query("
SELECT a.id, a.name, a.trade, b.remarks, b.status, b.arr_time, b.out_time
FROM apprentices a
INNER JOIN attendance b 
    ON a.id = b.user_id 
    AND b.attendance_date = '$date'
WHERE b.status = 'present'
");

/* ================= ABSENT LIST ================= */
$absentRes = $conn->query("
SELECT a.id, a.name, a.trade, b.remarks, b.status
FROM apprentices a
LEFT JOIN attendance b 
    ON a.id = b.user_id 
    AND b.attendance_date = '$date'
WHERE b.status = 'absent' OR b.status IS NULL
");

/* ================= NEW USERS ================= */
$newRes = $conn->query("
SELECT a.* FROM apprentices a
LEFT JOIN attendance b 
    ON a.id = b.user_id 
    AND b.attendance_date = '$date'
WHERE b.id IS NULL
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Attendance System</title>

<link href="bootstrap.min.css" rel="stylesheet">

<style>
body { background:#f4f6f9; }

.highlight {
    background: #fff3cd !important;
    border-left: 6px solid #ffc107;
}

table tr td { vertical-align: middle; }
</style>
</head>

<body>



<div class="container mt-4">

<h3>🚀 Attendance System</h3>

<!-- DATE FILTER -->
<form method="GET" class="mb-3">
    <input type="date" name="date" value="<?= $date ?>" class="form-control" style="width:200px; display:inline;">
    <button class="btn btn-primary">Filter</button>
</form>

<div class="row">

<!-- ================= PRESENT ================= -->
<div class="col-md-6">
<div class="card p-3">

<h4 class="text-success">🟢 Present (<?= $presentRes->num_rows ?>)</h4>

<table class="table table-bordered table-striped">
<tr>
<th>Name</th>
<th>Arr Time</th>
<th>Out Time</th>
<th>Remark</th>
<th>Action</th>
</tr>

<?php while($row = $presentRes->fetch_assoc()) { ?>

<tr>
<td><?= htmlspecialchars($row['name']) ?></td>
<td><?= htmlspecialchars($row['arr_time']) ?></td>
<td><?= htmlspecialchars($row['out_time']) ?></td>

<td>
<form method="POST">
<input type="hidden" name="id" value="<?= $row['id'] ?>">
<input type="hidden" name="action" value="present">

<input type="text"
       name="remark"
       value="<?= htmlspecialchars($row['remarks']) ?>"
       class="form-control"
       onchange="this.form.submit()">
</form>
</td>
<td>
<form method="POST">
<input type="hidden" name="id" value="<?= $row['id'] ?>">
<input type="hidden" name="action" value="absent">
<button class="btn btn-danger btn-sm">Mark Absent</button>
</form>
</td>

</tr>

<?php } ?>

</table>

</div>
</div>
<!-- ================= ABSENT ================= -->
<div class="col-md-6">
<div class="card p-3 shadow">

<h4 class="text-danger mb-3">
🔴 Absent (<?= $absentRes->num_rows ?>)
</h4>

<table class="table table-bordered table-striped align-middle">

<tr class="table-danger">
<th>Name</th>
<th>Remark</th>
<th width="170">Action</th>
</tr>

<?php while($row = $absentRes->fetch_assoc()) { ?>

<tr class="highlight">

<!-- NAME -->
<td>
<?= htmlspecialchars($row['name']) ?>
</td>

<!-- REMARK -->
<td>

<form method="POST">

<input type="hidden" name="id" value="<?= $row['id'] ?>">
<input type="hidden" name="action" value="absent">

<input type="text"
       name="remark"
       value="<?= htmlspecialchars($row['remarks'] ?? '') ?>"
       class="form-control"
       placeholder="Enter remark"
       onchange="this.form.submit()">

</form>

</td>

<!-- ACTION -->
<td>

<form method="POST">

<input type="hidden" name="id" value="<?= $row['id'] ?>">
<input type="hidden" name="action" value="present">

<!-- KEEP OLD REMARK -->
<input type="hidden"
       name="remark"
       value="<?= htmlspecialchars($row['remarks'] ?? '') ?>">

<button class="btn btn-success btn-sm w-100">
    Mark Present
</button>

</form>

</td>

</tr>

<?php } ?>

</table>

</div>
</div>

</div>
</div>

</body>
</html>
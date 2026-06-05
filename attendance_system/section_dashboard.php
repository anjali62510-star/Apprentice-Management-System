<?php
session_start();
include 'auth.php';
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['role'] != 'section_head' && $_SESSION['role'] != 'admin') {
    die("Access Denied");
}

/* ================= SECTION ================= */
$section = $_SESSION['section_new'] ?? '';

if ($section == '') {
    die("No section assigned to this user.");
}

/* ================= DATE ================= */
$date = $_GET['date'] ?? date('Y-m-d');


/* =====================================================
   HANDLE ACTION (PRESENT / ABSENT)
===================================================== */
if (isset($_POST['action'])) {

    $id = intval($_POST['id']);
    $action = $_POST['action'];
    $status = ($action == 'present') ? 'PRESENT' : 'ABSENT';
    $remark = trim($_POST['remark'] ?? '');

    // Get existing remark if empty
    $stmt = $conn->prepare("
        SELECT remarks FROM attendance 
        WHERE user_id=? AND attendance_date=?
    ");
    $stmt->bind_param("is", $id, $date);
    $stmt->execute();
    $old = $stmt->get_result()->fetch_assoc();

    if ($remark == '') {
        $remark = $old['remarks'] ?? '';
    }

    // Check record exists
    $check = $conn->prepare("
        SELECT id FROM attendance 
        WHERE user_id=? AND attendance_date=?
    ");
    $check->bind_param("is", $id, $date);
    $check->execute();
    $exists = $check->get_result()->num_rows;

    if ($exists == 0) {

        $ins = $conn->prepare("
            INSERT INTO attendance 
            (user_id, attendance_date, status, remarks, section_new)
            VALUES (?, ?, ?, ?, ?)
        ");
        $ins->bind_param("issss", $id, $date, $status, $remark, $section);
        $ins->execute();

    } else {

        $upd = $conn->prepare("
            UPDATE attendance 
            SET status=?, remarks=?, section_new=?
            WHERE user_id=? AND attendance_date=?
        ");
        $upd->bind_param("sssds", $status, $remark, $section, $id, $date);
        $upd->execute();
    }

    header("Location: section_dashboard.php?date=$date");
    exit();
}


/* =====================================================
   PRESENT LIST
===================================================== */
$stmt = $conn->prepare("
    SELECT a.id, a.name, a.trade, b.remarks, b.arr_time, b.out_time
    FROM apprentices a
    INNER JOIN attendance b 
        ON a.id = b.user_id
    WHERE b.attendance_date = ?
      AND b.status = 'PRESENT'
      AND b.section_new = ?
");

$stmt->bind_param("ss", $date, $section);
$stmt->execute();
$presentRes = $stmt->get_result();


/* =====================================================
   ABSENT LIST
===================================================== */
$stmt = $conn->prepare("
    SELECT a.id, a.name, a.trade, b.remarks
    FROM apprentices a
    LEFT JOIN attendance b
        ON a.id = b.user_id 
        AND b.attendance_date = ?
    WHERE (b.status = 'ABSENT' OR b.status IS NULL)
      AND a.section_new = ?
");

$stmt->bind_param("ss", $date, $section);
$stmt->execute();
$absentRes = $stmt->get_result();

?>

<!DOCTYPE html>
<html>
<head>
<title>Section Dashboard</title>
<link href="bootstrap.min.css" rel="stylesheet">

<style>
body { background:#f4f6f9; }
table tr td { vertical-align: middle; }
.card { border-radius: 10px; }
.highlight { background:#fff3cd; border-left:5px solid #ffc107; }
</style>
</head>

<body>

<div class="container mt-4">

<h3>🚀 Section Dashboard - <?php echo htmlspecialchars($section); ?></h3>

<!-- DATE FILTER -->
<form method="GET" class="mb-3">
    <input type="date" name="date" value="<?php echo $date; ?>" class="form-control" style="width:200px; display:inline;">
    <button class="btn btn-primary">Filter</button>
</form>

<div class="row">

<!-- ================= PRESENT ================= -->
<div class="col-md-6">
<div class="card p-3">
<h4 class="text-success">🟢 Present (<?php echo $presentRes->num_rows; ?>)</h4>

<table class="table table-bordered table-striped">
<tr>
<th>Name</th>
<th>Trade</th>
<th>Arr</th>
<th>Out</th>
<th>Remark</th>
<th>Action</th>
</tr>

<?php while($row = $presentRes->fetch_assoc()){ ?>
<tr>
<td><?php echo htmlspecialchars($row['name']); ?></td>
<td><?php echo htmlspecialchars($row['trade']); ?></td>
<td><?php echo $row['arr_time']; ?></td>
<td><?php echo $row['out_time']; ?></td>

<td>
<form method="POST">
<input type="hidden" name="id" value="<?php echo $row['id']; ?>">
<input type="hidden" name="action" value="present">
<input type="text" name="remark"
value="<?php echo htmlspecialchars($row['remarks']); ?>"
class="form-control"
onchange="this.form.submit()">
</form>
</td>

<td>
<form method="POST">
<input type="hidden" name="id" value="<?php echo $row['id']; ?>">
<input type="hidden" name="action" value="absent">
<button class="btn btn-danger btn-sm">Absent</button>
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

<h4 class="text-danger">🔴 Absent (<?php echo $absentRes->num_rows; ?>)</h4>

<table class="table table-bordered table-striped">
<tr class="table-danger">
<th>Name</th>
<th>Remark</th>
<th>Action</th>
</tr>

<?php while($row = $absentRes->fetch_assoc()){ ?>
<tr class="highlight">

<td><?php echo htmlspecialchars($row['name']); ?></td>

<td>
<form method="POST">
<input type="hidden" name="id" value="<?php echo $row['id']; ?>">
<input type="hidden" name="action" value="absent">
<input type="text" name="remark"
value="<?php echo htmlspecialchars($row['remarks'] ?? ''); ?>"
class="form-control"
onchange="this.form.submit()">
</form>
</td>

<td>
<form method="POST">
<input type="hidden" name="id" value="<?php echo $row['id']; ?>">
<input type="hidden" name="action" value="present">
<input type="hidden" name="remark" value="<?php echo htmlspecialchars($row['remarks'] ?? ''); ?>">
<button class="btn btn-success btn-sm w-100">Present</button>
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
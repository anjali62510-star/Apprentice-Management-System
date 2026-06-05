<?php
include 'config/db.php';

$date = $_GET['date'] ?? date('Y-m-d');

/* ================= GET ALL APPRENTICES + ATTENDANCE ================= */
$result = $conn->query("
SELECT 
    ap.name,
    ap.trade,
    ap.ga_ta,
    a.arr_time,
    a.out_time,
    a.status,
    a.remarks
FROM apprentices ap
LEFT JOIN attendance a 
    ON ap.id = a.user_id 
    AND a.attendance_date = '$date'
ORDER BY ap.name ASC
");
?>

<html>
<head>
<title>Print Attendance</title>

<style>
body { font-family: Arial; }

table {
    border-collapse: collapse;
    width: 100%;
}

th, td {
    border: 1px solid #000;
    padding: 6px;
    text-align: center;
    font-size: 13px;
}

.present {
    color: green;
    font-weight: bold;
}

.absent {
    color: red;
    font-weight: bold;
}

h3 {
    text-align: center;
}
</style>

</head>

<body onload="window.print()">

<h3>Daily Attendance - <?= htmlspecialchars($date) ?></h3>

<table>

<tr>
<th>Name</th>
<th>Trade</th>
<th>GA/TA</th>
<th>In</th>
<th>Out</th>
<th>Status</th>
<th>Remarks</th>
</tr>

<?php while($row = $result->fetch_assoc()) { 

    // DEFAULT STATUS = ABSENT (if no record found)
    $status = $row['status'];
    if($status == '' || $status == null){
        $status = 'absent';
    }

?>

<tr>

<td><?= htmlspecialchars($row['name']) ?></td>
<td><?= htmlspecialchars($row['trade']) ?></td>
<td><?= htmlspecialchars($row['ga_ta']) ?></td>

<td><?= htmlspecialchars($row['arr_time'] ?? '-') ?></td>
<td><?= htmlspecialchars($row['out_time'] ?? '-') ?></td>

<td class="<?= $status == 'present' ? 'present' : 'absent' ?>">
    <?= strtoupper($status) ?>
</td>

<td><?= htmlspecialchars($row['remarks'] ?? '') ?></td>

</tr>

<?php } ?>

</table>

</body>
</html>
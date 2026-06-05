<?php
include 'auth.php';
include 'config/db.php';
include 'navbar.php';

$data = $conn->query("
    SELECT a.name, e.*
    FROM exams e
    JOIN apprentices a ON e.apprentice_id = a.id
    ORDER BY e.exam_date DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Exam Report</title>
<link href="bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container mt-4">

<h3>📊 Exam Report</h3>

<table class="table table-bordered">
<tr>
<th>Name</th>
<th>Theory/Practical</th>
<th>Attendance/Behaviour</th>
<th>Total</th>
<th>Date</th>
</tr>

<?php while($r = $data->fetch_assoc()){ ?>
<tr>
<td><?= $r['name'] ?></td>
<td><?= $r['theory_practical'] ?></td>
<td><?= $r['attendance_behaviour'] ?></td>
<td><?= $r['total'] ?></td>
<td><?= $r['exam_date'] ?></td>
</tr>
<?php } ?>

</table>

</div>

</body>
</html>
<?php
include 'auth.php';
include 'config/db.php';
include 'navbar.php';
$sql = "
SELECT l.*, a.name
FROM leaves l
JOIN apprentices a ON l.apprentice_id=a.t_no
ORDER BY l.id DESC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
<title>Leave Records</title>
<link href="bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container mt-4">

<h3>Leave Records</h3>

<table class="table table-bordered">

<tr>
<th>Name</th>
<th>Type</th>
<th>From</th>
<th>To</th>
<th>Days</th>
<th>Status</th>
</tr>

<?php while($r = $result->fetch_assoc()){ ?>

<tr>
<td><?= $r['name'] ?></td>
<td><?= $r['leave_type'] ?></td>
<td><?= $r['from_date'] ?></td>
<td><?= $r['to_date'] ?></td>
<td><?= $r['total_days'] ?></td>
<td><?= $r['status'] ?></td>
</tr>

<?php } ?>

</table>

</div>

</body>
</html>
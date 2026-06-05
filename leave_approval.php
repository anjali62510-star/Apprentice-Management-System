<?php
include 'auth.php';
include 'config/db.php';
include 'navbar.php';

/* HANDLE APPROVAL / REJECTION */
if(isset($_GET['action']) && isset($_GET['id'])){
    $id = $_GET['id'];
    $action = $_GET['action'];

    if($action == 'approve'){
        $conn->query("UPDATE leaves SET status='Approved' WHERE id=$id");
    }

    if($action == 'reject'){
        $conn->query("UPDATE leaves SET status='Rejected' WHERE id=$id");
    }

    header("Location: leave_approval.php");
}

/* FETCH LEAVES */
$leaves = $conn->query("
    SELECT l.*, a.name 
    FROM leaves l
    JOIN apprentices a ON l.apprentice_id = a.t_no
    ORDER BY l.id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Leave Approval</title>
<link href="bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container mt-4">

<h3>📝 Leave Approval Panel</h3>

<table class="table table-bordered mt-3">
<tr>
<th>ID</th>
<th>Name</th>
<th>From</th>
<th>To</th>
<th>Reason</th>
<th>Status</th>
<th>Action</th>
</tr>

<?php while($row = $leaves->fetch_assoc()){ ?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= $row['name'] ?></td>
<td><?= $row['from_date'] ?></td>
<td><?= $row['to_date'] ?></td>
<td><?= $row['reason'] ?></td>
<td>
<span class="badge bg-<?=
    $row['status']=='Approved' ? 'success' :
    ($row['status']=='Rejected' ? 'danger' : 'warning')
?>">
<?= $row['status'] ?>
</span>
</td>

<td>
<?php if($row['status']=='Pending'){ ?>
<a href="?action=approve&id=<?= $row['id'] ?>" class="btn btn-success btn-sm">Approve</a>
<a href="?action=reject&id=<?= $row['id'] ?>" class="btn btn-danger btn-sm">Reject</a>
<?php } ?>
</td>

</tr>
<?php } ?>

</table>

</div>

</body>
</html>
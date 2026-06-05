<?php
include 'auth.php';
include 'config/db.php';

// ================= VALIDATE ID =================
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if($id <= 0){
    header("Location: list.php?msg=invalid");
    exit();
}

// ================= CONFIRM DELETE =================
if(!isset($_GET['confirm'])) {
?>
<!DOCTYPE html>
<html>
<head>
    <title>Delete Apprentice</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body{
            background:#f4f6f9;
            display:flex;
            justify-content:center;
            align-items:center;
            height:100vh;
            font-family:Arial, sans-serif;
        }

        .card{
            width:420px;
            border:none;
            border-radius:15px;
            box-shadow:0 5px 20px rgba(0,0,0,0.15);
        }

        .warning-icon{
            font-size:60px;
            color:#dc3545;
        }
    </style>
</head>
<body>

<div class="card p-4 text-center">

    <div class="warning-icon mb-3">
        ⚠️
    </div>

    <h3 class="mb-3 text-danger">Delete Confirmation</h3>

    <p class="text-muted">
        Are you sure you want to delete this apprentice record?
        <br>
        This action cannot be undone.
    </p>

    <div class="d-flex justify-content-center gap-3 mt-4">

        <a href="delete.php?id=<?=$id?>&confirm=yes"
           class="btn btn-danger px-4">
           Yes, Delete
        </a>

        <a href="list.php"
           class="btn btn-secondary px-4">
           Cancel
        </a>

    </div>

</div>

</body>
</html>

<?php
exit();
}

// ================= DELETE QUERY =================
$stmt = $conn->prepare("DELETE FROM apprentices WHERE id=?");
$stmt->bind_param("i", $id);

if($stmt->execute()){

    header("Location: list.php?msg=deleted");
    exit();

} else {

    header("Location: list.php?msg=error");
    exit();
}
?>
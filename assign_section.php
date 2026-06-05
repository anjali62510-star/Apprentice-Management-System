<?php
session_start();
include 'config/db.php';

/* ================= LOGIN CHECK ================= */
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

/* ================= ROLE CHECK ================= */
if($_SESSION['role'] != 'admin'){
    die("Access Denied - Admin Only");
}

/* =====================================================
   UPDATE SECTION HEAD DETAILS (FIXED + SYNC SAFE)
===================================================== */
if(isset($_POST['update_user'])){

    $id = intval($_POST['user_id']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $section_new = trim($_POST['section_new']);

    if($password != ''){

        // SECURE PASSWORD HASHING
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $conn->prepare("
            UPDATE users 
            SET email=?, password=?, section_new=? 
            WHERE id=? AND role='section_head'
        ");
        $stmt->bind_param("sssi", $email, $hashedPassword, $section_new, $id);

    } else {

        $stmt = $conn->prepare("
            UPDATE users 
            SET email=?, section_new=? 
            WHERE id=? AND role='section_head'
        ");
        $stmt->bind_param("ssi", $email, $section_new, $id);
    }

    $stmt->execute();

    $msg = "Section Head updated successfully!";
}

/* =====================================================
   DELETE SECTION HEAD (SAFE + CONTROLLED)
===================================================== */
if(isset($_GET['delete'])){

    $id = intval($_GET['delete']);

    $stmt = $conn->prepare("
        DELETE FROM users 
        WHERE id=? AND role='section_head'
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: assign_section.php?msg=deleted");
    exit();
}

/* =====================================================
   FETCH SECTION HEADS
===================================================== */
$users = $conn->query("
    SELECT id, email, role, section_new
    FROM users
    WHERE role='section_head'
    ORDER BY email ASC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Section Heads</title>
<link href="bootstrap.min.css" rel="stylesheet">

<style>
body {
    background:#f4f6f9;
}

table td {
    vertical-align: middle;
}

.container {
    margin-top: 30px;
}
</style>
</head>

<body>

<div class="container">

<h3>🧩 Manage Section Heads</h3>

<!-- ================= ALERTS ================= -->
<?php if(!empty($msg)) { ?>
<div class="alert alert-success">
    <?= $msg ?>
</div>
<?php } ?>

<?php if(isset($_GET['msg']) && $_GET['msg']=='deleted') { ?>
<div class="alert alert-danger">
    Section Head deleted successfully!
</div>
<?php } ?>

<!-- ================= TABLE ================= -->
<table class="table table-bordered table-striped">

<tr>
<th>Email</th>
<th>Section</th>
<th>New Password</th>
<th>Action</th>
</tr>

<?php while($u = $users->fetch_assoc()){ ?>

<tr>

<form method="POST">

<td>
    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">

    <input type="email"
           name="email"
           value="<?= htmlspecialchars($u['email']) ?>"
           class="form-control"
           required>
</td>

<td>
    <input type="text"
           name="section_new"
           value="<?= htmlspecialchars($u['section_new']) ?>"
           class="form-control"
           placeholder="Section Name (e.g HRD, CPP)">
</td>

<td>
    <input type="password"
           name="password"
           class="form-control"
           placeholder="Enter new password (optional)">
</td>

<td>
    <button class="btn btn-success btn-sm" name="update_user">
        Update
    </button>

    <a href="?delete=<?= $u['id'] ?>"
       class="btn btn-danger btn-sm"
       onclick="return confirm('Delete this Section Head?')">
        Delete
    </a>
</td>

</form>

</tr>

<?php } ?>

</table>

<a href="dashboard.php" class="btn btn-secondary">
    Back
</a>

</div>

</body>
</html>
<?php
include 'auth.php';
include 'config/db.php';
include 'navbar.php';
/* ================= CREATE UPLOAD FOLDER ================= */
if(!is_dir('uploads')){
    mkdir('uploads', 0777, true);
}

/* ================= INSERT LOGIC ================= */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name    = $_POST['name'];
    $father  = $_POST['father_name'];
    $ga_ta   = $_POST['ga_ta'];
    $trade   = $_POST['trade'];
    $t_no    = $_POST['t_no'];
    $mobile  = $_POST['mobile'];
    $dob     = $_POST['dob'];
    $doj     = $_POST['doj'];
    $doe     = $_POST['doe'];
    $stipend = $_POST['stipend'];

    // FIXED
    $section_new = trim($_POST['section_new'] ?? '');

    /* ================= PHOTO UPLOAD ================= */

    $photo = '';

    if(isset($_FILES['photo']) && $_FILES['photo']['name'] != ''){

        $allowed = ['jpg','jpeg','png','webp'];

        $fileName = $_FILES['photo']['name'];
        $tmpName  = $_FILES['photo']['tmp_name'];

        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if(in_array($ext, $allowed)){

            $photo = time().'_'.rand(1000,9999).'.'.$ext;

            move_uploaded_file(
                $tmpName,
                'uploads/'.$photo
            );
        }
    }

    /* ================= DUPLICATE CHECK ================= */

    $check = $conn->prepare("SELECT id FROM apprentices WHERE t_no=?");
    $check->bind_param("s", $t_no);
    $check->execute();

    $res = $check->get_result();

    if ($res->num_rows > 0) {

        header("Location: list.php?msg=duplicate");
        exit();

    } else {

        $stmt = $conn->prepare("
        INSERT INTO apprentices
        (
            name,
            father_name,
            ga_ta,
            trade,
            t_no,
            mobile,
            dob,
            doj,
            doe,
            stipend,
            photo,
            section_new
        )
        VALUES
        (?,?,?,?,?,?,?,?,?,?,?,?)
        ");

        $stmt->bind_param(
            "ssssssssssss",
            $name,
            $father,
            $ga_ta,
            $trade,
            $t_no,
            $mobile,
            $dob,
            $doj,
            $doe,
            $stipend,
            $photo,
            $section_new
        );

        $stmt->execute();

        header("Location: list.php?msg=added");
        exit();
    }
    /* ================= SERVER SIDE VALIDATION ================= */

if(!preg_match('/^[0-9]{10}$/', $mobile)){
    header("Location: list.php?msg=invalid_mobile");
    exit();
}

if(!preg_match('/^[A-Za-z ]+$/', $name)){
    header("Location: list.php?msg=invalid_name");
    exit();
}

if(!preg_match('/^[A-Za-z ]+$/', $father)){
    header("Location: list.php?msg=invalid_father");
    exit();
}
}

?>
<?php if(isset($_GET['msg']) && $_GET['msg']=='invalid_mobile'){ ?>
<div class="alert alert-danger">Invalid Mobile Number</div>
<?php } ?>

<?php if(isset($_GET['msg']) && $_GET['msg']=='invalid_name'){ ?>
<div class="alert alert-danger">Invalid Name</div>
<?php } ?>

<?php if(isset($_GET['msg']) && $_GET['msg']=='invalid_father'){ ?>
<div class="alert alert-danger">Invalid Father Name</div>
<?php } ?>

<!DOCTYPE html>
<html>
<head>

<title>Apprentice Management</title>

<link href="bootstrap.min.css" rel="stylesheet">
<link href="dataTables.bootstrap5.min.css" rel="stylesheet">

<script src="jquery-3.7.0.min.js"></script>
<script src="jquery.dataTables.min.js"></script>
<script src="dataTables.bootstrap5.min.js"></script>

<style>

body{
    background:#f4f6f9;
}

.card{
    border:none;
    border-radius:15px;
}

.photo-img{
    width:70px;
    height:70px;
    object-fit:cover;
    border-radius:50%;
    border:3px solid #0d6efd;
}

.table td{
    vertical-align:middle;
}

</style>

</head>

<body>



<div class="container mt-4">

<h2 class="mb-4">🎓 Apprentice Management System</h2>

<!-- ================= ALERTS ================= -->

<?php if(isset($_GET['msg']) && $_GET['msg']=='added'){ ?>
<div class="alert alert-success">Apprentice added successfully</div>
<?php } ?>

<?php if(isset($_GET['msg']) && $_GET['msg']=='updated'){ ?>
<div class="alert alert-primary">Record updated successfully</div>
<?php } ?>

<?php if(isset($_GET['msg']) && $_GET['msg']=='deleted'){ ?>
<div class="alert alert-danger">Record deleted successfully</div>
<?php } ?>

<?php if(isset($_GET['msg']) && $_GET['msg']=='duplicate'){ ?>
<div class="alert alert-warning">T No already exists</div>
<?php } ?>

<!-- ================= ADD BUTTON ================= -->

<button class="btn btn-success mb-3"
        data-bs-toggle="collapse"
        data-bs-target="#addForm">
    ➕ Add Apprentice
</button>

<!-- ================= FORM ================= -->

<div id="addForm" class="collapse">

<div class="card shadow p-4 mb-4">

<form method="POST" enctype="multipart/form-data">

<!-- ROW 1 -->
<div class="row">

    <!-- NAME -->
    <div class="col-md-3 mb-3">
        <label class="form-label">Name *</label>
        <input type="text"
               name="name"
               class="form-control"
               required
               pattern="[A-Za-z ]{3,50}"
               title="Only letters allowed (3-50 characters)">
    </div>

    <!-- FATHER NAME -->
    <div class="col-md-3 mb-3">
        <label class="form-label">Father Name *</label>
        <input type="text"
               name="father_name"
               class="form-control"
               required
               pattern="[A-Za-z ]{3,50}"
               title="Only letters allowed (3-50 characters)">
    </div>

    <!-- GA/TA -->
    <div class="col-md-2 mb-3">
        <label class="form-label">GA/TA *</label>
        <select name="ga_ta" class="form-control" required>
            <option value="">Select</option>
            <option value="GA">GA</option>
            <option value="TA">TA</option>
        </select>
    </div>

    <!-- TRADE -->
    <div class="col-md-2 mb-3">
        <label class="form-label">Trade *</label>
        <input type="text"
               name="trade"
               class="form-control"
               required
               minlength="2"
               maxlength="50">
    </div>

    <!-- T NO -->
    <div class="col-md-2 mb-3">
        <label class="form-label">T No *</label>
        <input type="text"
               name="t_no"
               class="form-control"
               required
               minlength="2"
               maxlength="20"
               pattern="[A-Za-z0-9/-]+"
               title="Only letters, numbers, / and - allowed">
    </div>

</div>

<!-- ROW 2 -->
<div class="row">

    <!-- MOBILE -->
    <div class="col-md-3 mb-3">
        <label class="form-label">Mobile *</label>
        <input type="tel"
               name="mobile"
               class="form-control"
               required
               pattern="[0-9]{10}"
               maxlength="10"
               minlength="10"
               title="Enter exactly 10 digits">
    </div>

    <!-- DOB -->
    <div class="col-md-3 mb-3">
        <label class="form-label">DOB *</label>
        <input type="date"
               name="dob"
               class="form-control"
               required>
    </div>

    <!-- DOJ -->
    <div class="col-md-3 mb-3">
        <label class="form-label">DOJ *</label>
        <input type="date"
               name="doj"
               class="form-control"
               required>
    </div>

    <!-- DOE -->
    <div class="col-md-3 mb-3">
        <label class="form-label">DOE *</label>
        <input type="date"
               name="doe"
               class="form-control"
               required>
    </div>

</div>

<!-- ROW 3 -->
<div class="row">

    <!-- SECTION -->
    <div class="col-md-3 mb-3">
        <label class="form-label">Section *</label>
        <input type="text"
               name="section_new"
               class="form-control"
               required
               minlength="2"
               maxlength="50">
    </div>

    <!-- STIPEND -->
    <div class="col-md-3 mb-3">
        <label class="form-label">Stipend *</label>
        <input type="number"
               name="stipend"
               class="form-control"
               required
               min="0"
               max="999999">
    </div>

    <!-- PHOTO -->
    <div class="col-md-4 mb-3">
        <label class="form-label">Photo *</label>
        <input type="file"
               name="photo"
               class="form-control"
               required
               accept=".jpg,.jpeg,.png,.webp">
    </div>

    <!-- SAVE BUTTON -->
    <div class="col-md-2 d-flex align-items-end">
        <button class="btn btn-primary w-100">
            Save
        </button>
    </div>

</div>

</form>
</div>
</div>

<!-- ================= TABLE ================= -->

<div class="card shadow">
<div class="card-body">

<table id="apprenticeTable" class="table table-bordered table-striped">

<thead class="table-dark">
<tr>
<th>ID</th>
<th>Photo</th>
<th>Name</th>
<th>Father</th>
<th>GA/TA</th>
<th>Trade</th>
<th>T No</th>
<th>Mobile</th>
<th>DOB</th>
<th>DOJ</th>
<th>DOE</th>
<th>Section</th>
<th>Stipend</th>
<th>Action</th>
</tr>
</thead>

<tbody>

<?php
$res = $conn->query("
SELECT id, photo, name, father_name, ga_ta, trade, t_no, mobile, dob, doj, doe, stipend, section_new
FROM apprentices
ORDER BY id DESC
");

while($row = $res->fetch_assoc()){
?>

<tr>

<td><?= $row['id'] ?></td>

<td>
<?php if($row['photo']){ ?>
    <img src="uploads/<?= $row['photo'] ?>" class="photo-img">
<?php } else { echo "No Photo"; } ?>
</td>

<td><?= htmlspecialchars($row['name']) ?></td>
<td><?= htmlspecialchars($row['father_name']) ?></td>
<td><?= htmlspecialchars($row['ga_ta']) ?></td>
<td><?= htmlspecialchars($row['trade']) ?></td>
<td><?= htmlspecialchars($row['t_no']) ?></td>
<td><?= htmlspecialchars($row['mobile']) ?></td>
<td><?= htmlspecialchars($row['dob']) ?></td>
<td><?= htmlspecialchars($row['doj']) ?></td>
<td><?= htmlspecialchars($row['doe']) ?></td>

<td>
<span class="badge bg-info text-dark">
<?= htmlspecialchars($row['section_new'] ?? 'NA') ?>
</span>
</td>

<td><?= htmlspecialchars($row['stipend']) ?></td>

<td>
<a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
<a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm">Delete</a>
</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>
</div>

</div>

<script>
$(document).ready(function(){
    $('#apprenticeTable').DataTable();
});
</script>

<script src="bootstrap.bundle.min.js"></script>

</body>
</html>
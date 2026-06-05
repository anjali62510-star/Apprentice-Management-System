<?php 
session_start();
include 'auth.php';
include 'config/db.php';

// ================= CHECK LOGIN =================
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

// ================= VALIDATE ID =================
if(!isset($_GET['id'])){
    header("Location: list.php?msg=invalid");
    exit();
}

$id = intval($_GET['id']);
if($id <= 0){
    header("Location: list.php?msg=invalid");
    exit();
}

// ================= FETCH DATA =================
$stmt = $conn->prepare("SELECT * FROM apprentices WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if(!$data){
    header("Location: list.php?msg=notfound");
    exit();
}

// ================= SECTION RESTRICTION =================
if($_SESSION['role'] == 'section_head'){
    $user_ga_ta = $_SESSION['ga_ta'] ?? '';
    $user_trade = $_SESSION['trade'] ?? '';

    if($data['ga_ta'] != $user_ga_ta || $data['trade'] != $user_trade){
        die("Access Denied: You can only edit apprentices in your section.");
    }
}

?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Apprentice</title>

<link href="bootstrap.min.css" rel="stylesheet">

<style>
body{
    background:#f4f6f9;
}
.card{
    border-radius:12px;
}
.photo-preview{
    width:120px;
    height:120px;
    border-radius:50%;
    object-fit:cover;
    border:3px solid #0d6efd;
}
</style>

</head>

<body>

<div class="container mt-4">

<h3>✏️ Edit Apprentice</h3>

<div class="card p-4 shadow">

<form action="update.php" method="POST" enctype="multipart/form-data">

<input type="hidden" name="id" value="<?= $data['id'] ?>">

<div class="row">

<!-- NAME -->
<div class="col-md-6">
<label>Name</label>
<input type="text"
       name="name"
       class="form-control"
       value="<?= htmlspecialchars($data['name']) ?>"
       required>
</div>

<!-- FATHER -->
<div class="col-md-6">
<label>Father Name</label>
<input type="text"
       name="father_name"
       class="form-control"
       value="<?= htmlspecialchars($data['father_name']) ?>">
</div>

<!-- GA/TA -->
<div class="col-md-4 mt-3">
<label>GA/TA</label>
<select name="ga_ta" class="form-control" <?= ($_SESSION['role']=='section_head')?'disabled':'' ?> >
<option value="GA" <?= ($data['ga_ta']=='GA')?'selected':'' ?>>GA</option>
<option value="TA" <?= ($data['ga_ta']=='TA')?'selected':'' ?>>TA</option>
</select>
<?php if($_SESSION['role']=='section_head'){ ?>
<input type="hidden" name="ga_ta" value="<?= $data['ga_ta'] ?>">
<?php } ?>
</div>

<!-- TRADE -->
<div class="col-md-4 mt-3">
<label>Trade</label>
<input type="text" name="trade" class="form-control" value="<?= htmlspecialchars($data['trade']) ?>" <?= ($_SESSION['role']=='section_head')?'readonly':'' ?>>
<?php if($_SESSION['role']=='section_head'){ ?>
<input type="hidden" name="trade" value="<?= $data['trade'] ?>">
<?php } ?>
</div>

<!-- T NO -->
<div class="col-md-4 mt-3">
<label>T No</label>
<input type="text"
       name="t_no"
       class="form-control"
       value="<?= htmlspecialchars($data['t_no']) ?>">
</div>

<!-- MOBILE -->
<div class="col-md-4 mt-3">
<label>Mobile</label>
<input type="text"
       name="mobile"
       class="form-control"
       value="<?= htmlspecialchars($data['mobile']) ?>">
</div>

<!-- DOB -->
<div class="col-md-4 mt-3">
<label>DOB</label>
<input type="date"
       name="dob"
       class="form-control"
       value="<?= $data['dob'] ?>">
</div>

<!-- DOJ -->
<div class="col-md-4 mt-3">
<label>DOJ</label>
<input type="date"
       name="doj"
       class="form-control"
       value="<?= $data['doj'] ?>">
</div>

<!-- DOE -->
<div class="col-md-4 mt-3">
<label>DOE</label>
<input type="date"
       name="doe"
       class="form-control"
       value="<?= $data['doe'] ?>">
</div>

<!-- STIPEND -->
<div class="col-md-4 mt-3">
<label>Stipend</label>
<input type="number"
       step="0.01"
       name="stipend"
       class="form-control"
       value="<?= $data['stipend'] ?>">
</div>

<!-- PHOTO PREVIEW -->
<div class="col-md-4 mt-3">
<label>Current Photo</label>
<br>
<?php if(!empty($data['photo'])){ ?>
<img src="uploads/<?= htmlspecialchars($data['photo']) ?>" class="photo-preview">
<br><br>
<div class="form-check">
<input type="checkbox" class="form-check-input" name="delete_photo" value="1" id="deletePhoto">
<label for="deletePhoto" class="form-check-label text-danger fw-bold">
Delete Current Photo
</label>
</div>
<?php } else { ?>
<div class="text-danger fw-bold mt-2">
No Photo
</div>
<?php } ?>
</div>

<!-- CHANGE PHOTO -->
<div class="col-md-4 mt-3">
<label>Change Photo</label>
<input type="file" name="photo" class="form-control" accept="image/jpeg,image/jpg,image/png,image/webp">
<small class="text-muted">Upload JPG, PNG or WEBP image</small>
</div>

</div>

<br>

<button class="btn btn-success">Update</button>
<a href="list.php" class="btn btn-secondary">Back</a>

</form>

</div>
</div>

</body>
</html>
<?php
include 'auth.php';
include 'config/db.php';

/* ================= CHECK REQUEST ================= */
if($_SERVER['REQUEST_METHOD'] != 'POST'){
    header("Location: list.php");
    exit();
}

/* ================= GET DATA ================= */
$id       = intval($_POST['id']);
$name     = $_POST['name'];
$father   = $_POST['father_name'];
$ga_ta    = $_POST['ga_ta'];
$trade    = $_POST['trade'];
$t_no     = $_POST['t_no'];
$mobile   = $_POST['mobile'];
$dob      = $_POST['dob'];
$doj      = $_POST['doj'];
$doe      = $_POST['doe'];
$stipend  = $_POST['stipend'];

/* ================= GET OLD PHOTO ================= */
$oldQuery = $conn->query("
SELECT photo, ga_ta, trade
FROM apprentices
WHERE id='$id'
");

$oldData = $oldQuery->fetch_assoc();

// ================= SECTION-HEAD CHECK =================
session_start();
$userRole = $_SESSION['role'] ?? '';
$secGA    = $_SESSION['ga_ta'] ?? '';
$secTrade = $_SESSION['trade'] ?? '';

// Only admin can update all, section-head restricted
if($userRole === 'section_head'){
    if($oldData['ga_ta'] !== $secGA || $oldData['trade'] !== $secTrade){
        die("Access Denied: Cannot update apprentice outside your section");
    }
}

$photo = $oldData['photo'];

/* ================= DELETE CURRENT PHOTO ================= */
if(isset($_POST['delete_photo']) && $_POST['delete_photo'] == '1'){
    if($photo != '' && file_exists('uploads/'.$photo)){
        unlink('uploads/'.$photo);
    }
    $photo = '';
}

/* ================= PHOTO UPDATE ================= */
if(isset($_FILES['photo']) && $_FILES['photo']['name'] != ''){
    $allowed = ['jpg','jpeg','png','webp'];

    $fileName = $_FILES['photo']['name'];
    $tmpName = $_FILES['photo']['tmp_name'];
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if(in_array($ext, $allowed)){
        // delete old photo
        if($photo != '' && file_exists('uploads/'.$photo)){
            unlink('uploads/'.$photo);
        }

        // new file name
        $photo = time().'_'.rand(1000,9999).'.'.$ext;

        move_uploaded_file(
            $tmpName,
            'uploads/'.$photo
        );
    }
}

/* ================= UPDATE QUERY ================= */
$stmt = $conn->prepare("
UPDATE apprentices SET
name=?,
father_name=?,
ga_ta=?,
trade=?,
t_no=?,
mobile=?,
dob=?,
doj=?,
doe=?,
stipend=?,
photo=?
WHERE id=?
");

$stmt->bind_param(
    "sssssssssssi",
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
    $id
);

$stmt->execute();

/* ================= REDIRECT ================= */
header("Location: list.php?msg=updated");
exit();
?>
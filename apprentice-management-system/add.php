<?php
include 'auth.php';
include 'config/db.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){

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

    $check = $conn->prepare("SELECT id FROM apprentices WHERE t_no=?");
    $check->bind_param("s", $t_no);
    $check->execute();
    $res = $check->get_result();

    if($res->num_rows > 0){
        echo "<script>alert('T No already exists');</script>";
    } else {

        $stmt = $conn->prepare("INSERT INTO apprentices 
        (name, father_name, ga_ta, trade, t_no, mobile, dob, doj, doe, stipend)
        VALUES (?,?,?,?,?,?,?,?,?,?)");

        $stmt->bind_param(
            "sssssssssi",
            $name, $father, $ga_ta, $trade, $t_no,
            $mobile, $dob, $doj, $doe, $stipend
        );

        $stmt->execute();

        echo "<script>
            alert('Apprentice Added Successfully');
            window.location='list.php';
        </script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Apprentice</title>
    <link href="appren.bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-4">

<h3>Add Apprentice (Backup Page)</h3>

<form method="POST">

<input name="name" class="form-control mb-2" placeholder="Name" required>
<input name="father_name" class="form-control mb-2" placeholder="Father Name">

<select name="ga_ta" class="form-control mb-2">
    <option value="GA">GA</option>
    <option value="TA">TA</option>
</select>

<input name="trade" class="form-control mb-2" placeholder="Trade">
<input name="t_no" class="form-control mb-2" placeholder="T No" required>
<input name="mobile" class="form-control mb-2" placeholder="Mobile">

<input type="date" name="dob" class="form-control mb-2">
<input type="date" name="doj" class="form-control mb-2">
<input type="date" name="doe" class="form-control mb-2">

<input type="number" name="stipend" class="form-control mb-2">

<button class="btn btn-primary">Save</button>

</form>

</div>

</body>
</html>
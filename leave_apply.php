<?php
include 'auth.php';
include 'config/db.php';
include 'navbar.php';
$msg = "";

if(isset($_POST['submit'])){

    $id = $_POST['apprentice_id'];
    $type = $_POST['leave_type'];
    $from = $_POST['from_date'];
    $to = $_POST['to_date'];
    $reason = $_POST['reason'];

    $days = (strtotime($to) - strtotime($from)) / (60*60*24) + 1;

    $sql = "INSERT INTO leaves
            (apprentice_id, leave_type, from_date, to_date, total_days, reason)
            VALUES
            ('$id','$type','$from','$to','$days','$reason')";

    if($conn->query($sql)){
        $msg = "Leave Applied Successfully";
    } else {
        $msg = "Error";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Apply Leave</title>
<link href="bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container mt-4">

<h3>Apply Leave</h3>

<?php if($msg!=""){ ?>
<div class="alert alert-success"><?= $msg ?></div>
<?php } ?>

<form method="POST">

<div class="mb-3">
<label>Apprentice ID</label>
<input type="number" id="apprentice_id" name="apprentice_id" class="form-control" required>
</div>

<div class="mb-3">
<label>Apprentice Name</label>
<input type="text" id="apprentice_name" class="form-control" readonly>
</div>

<div class="mb-3">
<label>Leave Type</label>
<select name="leave_type" class="form-control">
<option value="CL">CL</option>
<option value="SL">SL</option>
<option value="EL">EL</option>
<option value="OD">OD</option>
</select>
</div>

<div class="mb-3">
<label>From Date</label>
<input type="date" name="from_date" class="form-control" required>
</div>

<div class="mb-3">
<label>To Date</label>
<input type="date" name="to_date" class="form-control" required>
</div>

<div class="mb-3">
<label>Reason</label>
<textarea name="reason" class="form-control"></textarea>
</div>

<button class="btn btn-primary" name="submit">
Apply Leave
</button>

</form>

</div>
<script>
document.getElementById("apprentice_id").addEventListener("keyup", function(){

    let id = this.value;

    if(id === ""){
        document.getElementById("apprentice_name").value = "";
        return;
    }

    fetch("get_apprentice.php?id=" + id)
    .then(res => res.text())
    .then(data => {
        console.log("Response:", data); // 👈 DEBUG
        document.getElementById("apprentice_name").value = data;
    })
    .catch(err => {
        console.error("Error:", err); // 👈 DEBUG
    });
});
</script>
</body>
</html>
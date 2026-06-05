<?php
include 'auth.php';
include 'config/db.php';
include 'navbar.php';

/* FETCH APPRENTICES */
$data = $conn->query("SELECT id, name FROM apprentices ORDER BY name");

/* MESSAGE */
$msg = "";

/* SAVE */
if(isset($_POST['save'])){

    $date = $_POST['exam_date'];

    if(!$date){
        $msg = "<div class='alert alert-danger'>❌ Please select exam date</div>";
    } else {

        foreach($_POST['marks'] as $id => $m){

            $tp = isset($m['tp']) && $m['tp'] !== '' ? (int)$m['tp'] : 0;
            $ab = isset($m['ab']) && $m['ab'] !== '' ? (int)$m['ab'] : 0;

            if($tp > 300) $tp = 300;
            if($ab > 100) $ab = 100;

            if($tp == 0 && $ab == 0){
                continue;
            }

            $check = $conn->query("
                SELECT id FROM exams 
                WHERE apprentice_id=$id AND exam_date='$date'
            ");

            if($check->num_rows > 0){
                $conn->query("
                    UPDATE exams SET 
                    theory_practical=$tp,
                    attendance_behaviour=$ab
                    WHERE apprentice_id=$id AND exam_date='$date'
                ");
            } else {
                $conn->query("
                    INSERT INTO exams (apprentice_id, theory_practical, attendance_behaviour, exam_date)
                    VALUES ($id, $tp, $ab, '$date')
                ");
            }
        }

        $msg = "<div class='alert alert-success'>✅ Marks saved successfully!</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Exam Entry</title>

<link href="bootstrap.min.css" rel="stylesheet">

<style>
body { background:#f4f6f9; }

.card {
    border-radius:12px;
    box-shadow:0px 4px 15px rgba(0,0,0,0.08);
}

.table thead th {
    position: sticky;
    top: 0;
    background: #343a40;
    color: #fff;
}

input[type=number] {
    text-align:center;
}
</style>
</head>

<body>

<div class="container mt-4">

<div class="card p-4">

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>📘 Exam Entry</h4>

    <!-- ✅ REPORT BUTTON -->
    <a href="exam_report.php" class="btn btn-primary">
        📊 View Report
    </a>
</div>

<!-- MESSAGE -->
<?= $msg ?>

<form method="POST">

<div class="row mb-3">
    <div class="col-md-4">
        <label><b>Exam Date:</b></label>
        <input type="date" name="exam_date" required class="form-control">
    </div>
</div>

<div style="max-height:400px; overflow:auto;">
<table class="table table-bordered table-hover text-center">

<thead>
<tr>
<th>Name</th>
<th>Theory / Practical (300)</th>
<th>Attendance / Behaviour (100)</th>
</tr>
</thead>

<tbody>
<?php while($r = $data->fetch_assoc()){ ?>
<tr>
<td><?= $r['name'] ?></td>

<td>
<input type="number"
       name="marks[<?= $r['id'] ?>][tp]"
       max="300"
       min="0"
       value="0"
       class="form-control">
</td>

<td>
<input type="number"
       name="marks[<?= $r['id'] ?>][ab]"
       max="100"
       min="0"
       value="0"
       class="form-control">
</td>

</tr>
<?php } ?>
</tbody>

</table>
</div>

<br>

<button name="save" class="btn btn-success w-100">
    💾 Save Marks
</button>

</form>

</div>

</div>

</body>
</html>
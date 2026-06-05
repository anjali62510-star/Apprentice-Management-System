<?php 
include 'auth.php'; 

// Optional: only allow section heads here
if($_SESSION['role'] != 'section_head'){
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Select Section</title>
<link href="bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">

<div class="card shadow p-4">

<h4 class="mb-3">📚 Select Section</h4>

<form method="POST" action="section_dashboard.php">

<!-- GA / TA -->
<label class="form-label">Category</label>
<select name="ga_ta" class="form-control mb-3" required>
<option value="GA">Graduate Apprentice (GA)</option>
<option value="TA">Technician Apprentice (TA)</option>
</select>

<!-- TRADE -->
<label class="form-label">Trade</label>
<select name="trade" class="form-control mb-3" required>

<option value="COPA">COPA</option>
<option value="ELECTRICIAN">ELECTRICIAN</option>
<option value="SHEET METAL WORKER">SHEET METAL WORKER</option>
<option value="WELDER">WELDER</option>
<option value="MECHANIC DIESEL">MECHANIC DIESEL</option>
<option value="MMV">MMV</option>
<option value="TRACTOR MACHANIC">TRACTOR MACHANIC</option>
<option value="MACHINIST">MACHINIST</option>
<option value="FITTER">FITTER</option>
<option value="TURNER">TURNER</option>
<option value="PLUMBER">PLUMBER</option>
<option value="DRAUGHTSMAN">DRAUGHTSMAN</option>
<option value="CARPENTER">CARPENTER</option>
<option value="SURVERYOR">SURVERYOR</option>
<option value="MACHINIST (GRINDRE)">MACHINIST (GRINDRE)</option>
<option value="PAINTER">PAINTER</option>
<option value="STENOGRAPHER">STENOGRAPHER</option>
<option value="ICTSM">ICTSM</option>

</select>

<button class="btn btn-primary w-100">Continue</button>

</form>

</div>

</div>

</body>
</html>
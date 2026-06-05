<?php include 'config/db.php'; ?>
<!DOCTYPE html>
<html>
<head>
<title>Upload Attendance File</title>
<link href="bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-4">

<h3>📥 Upload Attendance (.txt)</h3>

<form action="attendance_import.php" method="POST" enctype="multipart/form-data" class="card p-4 shadow">

<label>Select TXT File</label>
<input type="file" name="file" accept=".txt" class="form-control" required>

<br>

<button class="btn btn-success">Upload</button>

<a href="attendance_list.php" class="btn btn-info">View Attendance</a>

</form>

</div>

</body>
</html>
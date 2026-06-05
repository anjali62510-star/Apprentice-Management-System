<?php
session_start();
include("../config/db.php");

$error = ""; // IMPORTANT: prevents blank page

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($email) && !empty($password)) {

        $stmt = $conn->prepare("SELECT * FROM users WHERE email=? AND status='active'");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows == 1) {

            $user = $result->fetch_assoc();

            if ($password === $user['password'] || password_verify($password, $user['password'])) {

                // ================= SESSION =================
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['email']     = $user['email'];
                $_SESSION['role']      = $user['role'];
                $_SESSION['ga_ta']     = $user['ga_ta'] ?? '';
                $_SESSION['trade']     = $user['trade'] ?? '';
                $_SESSION['section_new'] = $user['section_new'] ?? '';

                // ================= REDIRECT =================
                if ($user['role'] == 'admin') {

                    header("Location: ../dashboard.php");
                    exit();

                } 
                elseif ($user['role'] == 'section_head') {

                    $section = trim($user['section_new'] ?? '');

                    if ($section == '' || $section == NULL) {
                        $error = "No section assigned to this user.";
                    } else {
                        $_SESSION['section_new'] = $section;
                        header("Location: ../section_dashboard.php");
                        exit();
                    }

                } 
                else {

                    header("Location: ../dashboard.php");
                    exit();
                }

            } else {
                $error = "Invalid password";
            }

        } else {
            $error = "User not found or inactive";
        }

    } else {
        $error = "Please fill all fields";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>AVNL VFJ Login</title>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', sans-serif;
}

body {
    height: 100vh;
    background: url('background.jpg') no-repeat center center/cover;
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
}

/* Overlay */
body::before {
    content: "";
    position: absolute;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 0;
}

.login-box {
    position: relative;
    z-index: 1;
    width: 350px;
    padding: 30px;
    border-radius: 15px;
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(10px);
    box-shadow: 0 8px 32px rgba(0,0,0,0.3);
    color: #fff;
    text-align: center;
}

.login-box img {
    width: 70px;
    margin-bottom: 10px;
}

input {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    border: none;
    border-radius: 8px;
    outline: none;
}

button {
    width: 100%;
    padding: 12px;
    background: linear-gradient(45deg,#007bff,#00c6ff);
    border: none;
    border-radius: 8px;
    color: white;
    cursor: pointer;
}

.error {
    background: rgba(255,0,0,0.2);
    padding: 8px;
    border-radius: 5px;
    margin-bottom: 10px;
}
</style>
</head>

<body>

<div class="login-box">

<img src="avnl.png" alt="Logo">

<h2>AVNL VFJ</h2>
<p>Apprentice Attendance System</p>

<!-- ERROR ALWAYS SHOWN -->
<?php if (!empty($error)) { ?>
    <div class="error"><?php echo $error; ?></div>
<?php } ?>

<form method="POST">
    <input type="email" name="email" placeholder="Enter Email" required>
    <input type="password" name="password" placeholder="Enter Password" required>
    <button type="submit">Login</button>
</form>

</div>

</body>
</html>
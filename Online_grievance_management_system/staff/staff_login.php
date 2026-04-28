<?php
session_start();

if(isset($_SESSION['staff_id'])){
    header('Location: dashboard_staff.php');
    exit();
}

$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Staff Login</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../css/style.css">
<script src="../js/loginpage.js"></script>

<style>
.error-message{
margin-bottom:15px;
padding:12px;
border-radius:6px;
background:#ffe2e2;
color:#b42318;
font-size:14px;
}
.success-message{
margin-bottom:15px;
padding:12px;
border-radius:6px;
background:#e7f8ed;
color:#176b3a;
font-size:14px;
}
</style>
<link rel="stylesheet" href="../assets/css/theme.css">
</head>

<body>
<div class="login-container">

<a href="../index.php">&larr; Change role</a>

<br><br>

<button class="role-btn" type="button">Staff Login</button>

<h1>Sign In</h1>

<p class="subtitle">Enter your credentials to access the staff portal</p>

<?php if($error !== '') { ?>
<div class="error-message"><?php echo htmlspecialchars($error); ?></div>
<?php } ?>
<?php if($success !== '') { ?>
<div class="success-message"><?php echo htmlspecialchars($success); ?></div>
<?php } ?>

<form action="login_process.php" method="post" onsubmit="return validateCaptcha()">
<label>Staff ID / Email</label>
<input type="text" name="login_id" placeholder="e.g. 101 or staff@college.edu" required>

<div class="password-box">
<label>Password</label>
<input type="password" name="mypswd" id="password" placeholder="Enter your password" required>
<i class="fa-solid fa-eye" id="eye" onclick="togglePassword()"></i>
</div>

<label>Captcha</label>

<div class="captcha-box">
<span id="captcha"></span>
<button type="button" class="refresh" onclick="generateCaptcha()">
<i class="fa fa-refresh"></i>
</button>
</div>

<input type="text" id="captchaInput" placeholder="Enter captcha" required>

<div class="options">
<a href="forgot_password.php">Forgot password?</a>
</div>

<button class="login-btn" type="submit">Sign In as Staff</button>

<div class="register">
Staff accounts are managed by the administrator.
</div>


</form>

</div>

<script src="../assets/js/theme.js"></script>
</body>
</html>

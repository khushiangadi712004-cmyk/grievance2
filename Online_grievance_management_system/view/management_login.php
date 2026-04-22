<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<title>Management Login</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../css/style.css">
<script src="../js/loginpage.js"></script>
</head>

<body>

<div class="login-container">

<a href="loginb.php">← Change role</a>

<br><br>

<button class="role-btn">🏢 Management Login</button>

<h1>Sign In</h1>

<p class="subtitle">Enter your credentials to access the portal</p>

<label>Management ID / Email</label>
<input type="text" placeholder="Enter Management ID">

<label>Password</label>

<div class="password-box">
<input type="password" id="password" placeholder="Enter password">
<i class="fa-solid fa-eye" id="eye" onclick="togglePassword()"></i>
</div>

<label>Captcha</label>

<div class="captcha-box">
<span id="captcha"></span>
<button type="button" class="refresh" onclick="generateCaptcha()">
<i class="fa fa-refresh"></i>
</button>
</div>

<input type="text" id="captchaInput" placeholder="Enter captcha">

<div class="options">
<a href="#">Forgot password?</a>
</div>

<button class="login-btn" onclick="validateCaptcha()">Sign In as Management</button>

<div class="support">
Contact system admin if you face issues.
</div>

</div>



</body>
</html>
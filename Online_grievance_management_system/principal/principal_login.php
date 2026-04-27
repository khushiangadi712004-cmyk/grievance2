<?php
session_start();

if(isset($_SESSION['principal_id'])){
    header('Location: dashboard_principal.php');
    exit();
}

$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Principal Login</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../css/style.css">
<script src="../js/loginpage.js"></script>
<style>.error-message{margin-bottom:15px;padding:12px;border-radius:6px;background:#ffe2e2;color:#b42318;font-size:14px;}</style>
</head>
<body>
<div class="login-container">
<a href="../index.php">&larr; Change role</a>
<br><br>
<button class="role-btn" type="button">Principal Login</button>
<h1>Sign In</h1>
<p class="subtitle">Enter your credentials to access the principal portal</p>
<?php if($error !== '') { ?><div class="error-message"><?php echo htmlspecialchars($error); ?></div><?php } ?>
<form action="login_process.php" method="post" onsubmit="return validateCaptcha()">
<label>Principal ID / Email</label>
<input type="text" name="login_id" placeholder="Enter Principal ID or email" required>
<div class="password-box">
<label>Password</label>
<input type="password" name="mypswd" id="password" placeholder="Enter password" required>
<i class="fa-solid fa-eye" id="eye" onclick="togglePassword()"></i>
</div>
<label>Captcha</label>
<div class="captcha-box">
<span id="captcha"></span>
<button type="button" class="refresh" onclick="generateCaptcha()"><i class="fa fa-refresh"></i></button>
</div>
<input type="text" id="captchaInput" placeholder="Enter captcha" required>
<button class="login-btn" type="submit">Sign In as Principal</button>
</form>
</div>
</body>
</html>

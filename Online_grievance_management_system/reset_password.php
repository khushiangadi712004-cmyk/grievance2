<?php
require_once __DIR__ . '/include/password_reset_helper.php';

$role = $_GET['role'] ?? $_POST['role'] ?? '';
$identifier = $_GET['id'] ?? $_POST['id'] ?? '';
$error = '';
$success = isset($_GET['sent']) ? 'OTP sent successfully. Check your email.' : '';
$role_config = get_password_reset_role($role);
$show_reset_form = $role_config && $identifier !== '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $otp = $_POST['otp'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if(complete_password_reset($conn, $role, $identifier, $otp, $new_password, $confirm_password, $error, $redirect_path)){
        header('Location: ' . $redirect_path . '?success=' . urlencode('Password reset successfully. Please sign in.'));
        exit();
    }
} elseif(!$show_reset_form) {
    $error = 'Invalid OTP reset request.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Password</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="css/style.css">
<style>
.message{margin-bottom:15px;padding:12px;border-radius:6px;font-size:14px;}
.error-message{background:#ffe2e2;color:#b42318;}
.success-message{background:#e7f8ed;color:#176b3a;}
</style>
<script>
function togglePassword(){
    const password = document.getElementById('password');
    const eye = document.getElementById('eye');
    if(password.type === 'password'){
        password.type = 'text';
        eye.classList.remove('fa-eye');
        eye.classList.add('fa-eye-slash');
    } else {
        password.type = 'password';
        eye.classList.remove('fa-eye-slash');
        eye.classList.add('fa-eye');
    }
}
</script>
<link rel="stylesheet" href="assets/css/theme.css">
</head>
<body>
<div class="login-container">
<?php if($role_config) { ?><a href="<?php echo htmlspecialchars($role_config['login_path']); ?>">&larr; Back to login</a><?php } ?>
<br><br>
<button class="role-btn" type="button"><?php echo htmlspecialchars($role_config['label'] ?? 'Account'); ?> Password Reset</button>
<h1>Set New Password</h1>
<p class="subtitle">Choose a new password for your account</p>

<?php if($error !== '') { ?><div class="message error-message"><?php echo htmlspecialchars($error); ?></div><?php } ?>
<?php if($success !== '') { ?><div class="message success-message"><?php echo htmlspecialchars($success); ?></div><?php } ?>

<?php if($show_reset_form) { ?>
<form action="reset_password.php" method="post">
<input type="hidden" name="role" value="<?php echo htmlspecialchars($role); ?>">
<input type="hidden" name="id" value="<?php echo htmlspecialchars($identifier); ?>">

<label>Email OTP</label>
<input type="text" name="otp" placeholder="Enter 6-digit OTP" maxlength="6" pattern="[0-9]{6}" inputmode="numeric" required>

<div class="password-box">
<label>New Password</label>
<input type="password" name="new_password" id="password" placeholder="Enter new password" required>
<i class="fa-solid fa-eye" id="eye" onclick="togglePassword()"></i>
</div>

<label>Confirm Password</label>
<input type="password" name="confirm_password" placeholder="Confirm new password" required>

<button class="login-btn" type="submit">Reset Password</button>
</form>
<?php } ?>
</div>
<script src="assets/js/theme.js"></script>
</body>
</html>

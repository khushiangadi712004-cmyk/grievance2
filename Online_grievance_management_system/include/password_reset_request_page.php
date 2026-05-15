<?php
require_once __DIR__ . '/password_reset_helper.php';

$config = get_password_reset_role($reset_role);
if(!$config){
    die('Invalid password reset role.');
}

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $identifier = trim($_POST[$config['identifier_name']] ?? '');
    $email = trim($_POST['email'] ?? '');
    request_password_reset($conn, $reset_role, $identifier, $email, $error, $success);
}

$login_href = '../' . $config['login_path'];
if(in_array($reset_role, ['student', 'staff', 'hod', 'principal', 'admin'], true)){
    $login_href = basename($config['login_path']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($config['label']); ?> Forgot Password</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../css/style.css">
<style>
.message{margin-bottom:15px;padding:12px;border-radius:6px;font-size:14px;}
.error-message{background:#ffe2e2;color:#b42318;}
.success-message{background:#e7f8ed;color:#176b3a;}
</style>
<link rel="stylesheet" href="../assets/css/theme.css">
</head>
<body>
<div class="login-container">
<a href="<?php echo htmlspecialchars($login_href); ?>">&larr; Back to login</a>
<br><br>
<button class="role-btn" type="button"><?php echo htmlspecialchars($config['label']); ?> Password Reset</button>
<h1>Forgot Password</h1>
<p class="subtitle">Enter your account details to receive a secure email OTP</p>

<?php if($error !== '') { ?><div class="message error-message"><?php echo htmlspecialchars($error); ?></div><?php } ?>
<?php if($success !== '') { ?><div class="message success-message"><?php echo $success; ?></div><?php } ?>

<form action="forgot_password.php" method="post">
<label><?php echo htmlspecialchars($config['identifier_label']); ?></label>
<input type="text" name="<?php echo htmlspecialchars($config['identifier_name']); ?>" placeholder="<?php echo htmlspecialchars($config['identifier_placeholder']); ?>" value="<?php echo htmlspecialchars($_POST[$config['identifier_name']] ?? ''); ?>" required>

<label>Email</label>
<input type="email" name="email" placeholder="<?php echo strtolower(htmlspecialchars($config['label'])); ?>@college.edu" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>

<button class="login-btn" type="submit">Send OTP</button>
</form>
</div>
<script src="../assets/js/theme.js"></script>
</body>
</html>

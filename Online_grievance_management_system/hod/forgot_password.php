<?php
include '../include/conn.php';

$error = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $hod_id = trim($_POST['hod_id'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if($hod_id === '' || $email === '' || $new_password === '' || $confirm_password === ''){
        $error = 'Please fill all fields.';
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error = 'Please enter a valid email address.';
    } elseif(strlen($new_password) < 6){
        $error = 'Password must be at least 6 characters.';
    } elseif($new_password !== $confirm_password){
        $error = 'Passwords do not match.';
    } else {
        $stmt = mysqli_prepare($conn, "SELECT hod_id FROM hod WHERE hod_id = ? AND email = ? LIMIT 1");

        if(!$stmt){
            $error = 'Unable to check HOD account.';
        } else {
            mysqli_stmt_bind_param($stmt, 'ss', $hod_id, $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $hod = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            if(!$hod){
                $error = 'HOD ID and email do not match.';
            } else {
                $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                $update_stmt = mysqli_prepare($conn, "UPDATE hod SET password = ? WHERE hod_id = ? LIMIT 1");

                if(!$update_stmt){
                    $error = 'Unable to update password.';
                } else {
                    mysqli_stmt_bind_param($update_stmt, 'ss', $hashed_password, $hod_id);
                    mysqli_stmt_execute($update_stmt);
                    mysqli_stmt_close($update_stmt);

                    header('Location: hod_login.php?success=' . urlencode('Password reset successfully. Please sign in.'));
                    exit();
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>HOD Forgot Password</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../css/style.css">
<style>.message{margin-bottom:15px;padding:12px;border-radius:6px;font-size:14px;}.error-message{background:#ffe2e2;color:#b42318;}</style>
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
<link rel="stylesheet" href="../assets/css/theme.css">
</head>
<body>
<div class="login-container">
<a href="hod_login.php">&larr; Back to login</a>
<br><br>
<button class="role-btn" type="button">HOD Password Reset</button>
<h1>Reset Password</h1>
<p class="subtitle">Confirm your HOD account and choose a new password</p>
<?php if($error !== '') { ?><div class="message error-message"><?php echo htmlspecialchars($error); ?></div><?php } ?>
<form action="forgot_password.php" method="post">
<label>HOD ID</label>
<input type="text" name="hod_id" placeholder="e.g. HOD001" value="<?php echo htmlspecialchars($_POST['hod_id'] ?? ''); ?>" required>
<label>Email</label>
<input type="email" name="email" placeholder="hod@college.edu" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
<div class="password-box">
<label>New Password</label>
<input type="password" name="new_password" id="password" placeholder="Enter new password" required>
<i class="fa-solid fa-eye" id="eye" onclick="togglePassword()"></i>
</div>
<label>Confirm Password</label>
<input type="password" name="confirm_password" placeholder="Confirm new password" required>
<button class="login-btn" type="submit">Reset HOD Password</button>
</form>
</div>
<script src="../assets/js/theme.js"></script>
</body>
</html>

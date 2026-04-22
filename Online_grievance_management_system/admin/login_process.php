<?php
session_start();
include '../include/conn.php';

function redirect_with_error($message) {
    header('Location: admin_login.php?error=' . urlencode($message));
    exit();
}

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    redirect_with_error('Invalid access.');
}

$login_id = trim($_POST['login_id'] ?? '');
$mypswd = $_POST['mypswd'] ?? '';

if($login_id === '' || $mypswd === ''){
    redirect_with_error('Please enter admin credentials.');
}

$stmt = mysqli_prepare($conn, "SELECT admin_id, admin_name, email, mypswd FROM admin WHERE email = ? OR admin_id = ? LIMIT 1");

if(!$stmt){
    redirect_with_error('Admin table not found. Import admin/admin_setup.sql into grievance database.');
}

mysqli_stmt_bind_param($stmt, 'ss', $login_id, $login_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$admin = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if(!$admin){
    redirect_with_error('Admin account not found.');
}

if(!password_verify($mypswd, $admin['mypswd'])){
    redirect_with_error('Wrong password.');
}

$_SESSION['admin_id'] = $admin['admin_id'];
$_SESSION['admin_name'] = $admin['admin_name'];
$_SESSION['admin_email'] = $admin['email'];

header('Location: dashboard_admin.php');
exit();
?>

<?php
session_start();
include '../include/conn.php';

function redirect_with_error($message) {
    header('Location: hod_login.php?error=' . urlencode($message));
    exit();
}

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    redirect_with_error('Invalid access.');
}

$login_id = trim($_POST['login_id'] ?? '');
$mypswd = $_POST['mypswd'] ?? '';

if($login_id === '' || $mypswd === ''){
    redirect_with_error('Please enter HOD credentials.');
}

$stmt = mysqli_prepare($conn, "SELECT hod_id, hod_name, email, password, department_no FROM hod WHERE email = ? OR hod_id = ? LIMIT 1");

if(!$stmt){
    redirect_with_error('HOD table not found. Import hod/hod_setup.sql into grievance database.');
}

mysqli_stmt_bind_param($stmt, 'ss', $login_id, $login_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$hod = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if(!$hod){
    redirect_with_error('HOD account not found.');
}

if(!password_verify($mypswd, $hod['password']) && $mypswd !== $hod['password']){
    redirect_with_error('Wrong password.');
}

session_regenerate_id(true);
$_SESSION['hod_id'] = $hod['hod_id'];
$_SESSION['hod_name'] = $hod['hod_name'];
$_SESSION['hod_email'] = $hod['email'];
$_SESSION['hod_department_no'] = $hod['department_no'];
$_SESSION['role'] = 'HOD';

header('Location: dashboard_hod.php');
exit();
?>

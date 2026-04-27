<?php
session_start();
include '../include/conn.php';

function redirect_with_error($message) {
    header('Location: principal_login.php?error=' . urlencode($message));
    exit();
}

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    redirect_with_error('Invalid access.');
}

$login_id = trim($_POST['login_id'] ?? '');
$mypswd = $_POST['mypswd'] ?? '';

if($login_id === '' || $mypswd === ''){
    redirect_with_error('Please enter principal credentials.');
}

$stmt = mysqli_prepare($conn, "SELECT principal_id, principal_name, email, password FROM principal WHERE email = ? OR principal_id = ? LIMIT 1");

if(!$stmt){
    redirect_with_error('Principal table not found. Import principal/principal_setup.sql first.');
}

mysqli_stmt_bind_param($stmt, 'ss', $login_id, $login_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$principal = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if(!$principal){
    redirect_with_error('Principal account not found.');
}

if(!password_verify($mypswd, $principal['password']) && $mypswd !== $principal['password']){
    redirect_with_error('Wrong password.');
}

session_regenerate_id(true);
$_SESSION['principal_id'] = $principal['principal_id'];
$_SESSION['principal_name'] = $principal['principal_name'];
$_SESSION['principal_email'] = $principal['email'];
$_SESSION['role'] = 'Principal';

header('Location: dashboard_principal.php');
exit();
?>

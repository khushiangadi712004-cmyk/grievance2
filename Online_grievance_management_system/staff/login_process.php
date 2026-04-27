<?php
session_start();
include '../include/conn.php';

function redirect_with_error($message) {
    header('Location: staff_login.php?error=' . urlencode($message));
    exit();
}

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    redirect_with_error('Invalid access.');
}

$login_id = trim($_POST['login_id'] ?? '');
$mypswd = $_POST['mypswd'] ?? '';

if($login_id === '' || $mypswd === ''){
    redirect_with_error('Please enter staff credentials.');
}

$stmt = mysqli_prepare($conn, "SELECT staff_id, stname, email, password, department_no, phone_no, design FROM staff WHERE email = ? OR staff_id = ? LIMIT 1");

if(!$stmt){
    redirect_with_error('Staff table not found. Import staff/staff_setup.sql into grievance database.');
}

mysqli_stmt_bind_param($stmt, 'ss', $login_id, $login_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$staff = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if(!$staff){
    redirect_with_error('Staff account not found.');
}

if(!password_verify($mypswd, $staff['password']) && $mypswd !== $staff['password']){
    redirect_with_error('Wrong password.');
}

session_regenerate_id(true);
$_SESSION['staff_id'] = $staff['staff_id'];
$_SESSION['staff_name'] = $staff['stname'];
$_SESSION['staff_email'] = $staff['email'];
$_SESSION['staff_department_no'] = $staff['department_no'];
$_SESSION['staff_phone_no'] = $staff['phone_no'];
$_SESSION['staff_design'] = $staff['design'];
$_SESSION['role'] = 'Staff';

header('Location: dashboard_staff.php');
exit();
?>

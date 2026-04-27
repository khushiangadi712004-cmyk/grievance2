<?php
session_start();
include '../include/conn.php';

function redirect_with_error($message) {
    header('Location: student_login.php?error=' . urlencode($message));
    exit();
}

if($_SERVER["REQUEST_METHOD"] !== "POST"){
    redirect_with_error('Invalid access.');
}

$email = trim($_POST['email'] ?? '');
$mypswd = $_POST['mypswd'] ?? '';

if($email === '' || $mypswd === ''){
    redirect_with_error('Please enter student credentials.');
}

$stmt = mysqli_prepare($conn, "SELECT register_no, sname, email, mypswd, department_no, semester FROM student WHERE email = ? OR register_no = ? LIMIT 1");

if(!$stmt){
    redirect_with_error('Student table not found.');
}

mysqli_stmt_bind_param($stmt, 'ss', $email, $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if(!$student){
    redirect_with_error('Student account not found.');
}

if(!password_verify($mypswd, $student['mypswd']) && $mypswd !== $student['mypswd']){
    redirect_with_error('Wrong password.');
}

session_regenerate_id(true);
$_SESSION['register_no'] = $student['register_no'];
$_SESSION['student_name'] = $student['sname'];
$_SESSION['student_email'] = $student['email'];
$_SESSION['student_department_no'] = $student['department_no'];
$_SESSION['student_semester'] = $student['semester'];
$_SESSION['role'] = 'Student';

header("Location: dashboard_user.php");
exit();
?>

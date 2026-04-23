<?php
session_start();
include '../include/conn.php';

function management_column_exists($conn, $table_name, $column_name) {
    $table_name = mysqli_real_escape_string($conn, $table_name);
    $column_name = mysqli_real_escape_string($conn, $column_name);
    $result = @mysqli_query($conn, "SHOW COLUMNS FROM {$table_name} LIKE '{$column_name}'");
    return $result && mysqli_num_rows($result) > 0;
}

function redirect_with_error($message) {
    header('Location: ../view/management_login.php?error=' . urlencode($message));
    exit();
}

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    redirect_with_error('Invalid access.');
}

$login_id = trim($_POST['login_id'] ?? '');
$mypswd = $_POST['mypswd'] ?? '';

if($login_id === '' || $mypswd === ''){
    redirect_with_error('Please enter management credentials.');
}

$password_select = management_column_exists($conn, 'management', 'password')
    ? 'COALESCE(NULLIF(m.password, \'\'), s.password)'
    : 's.password';

$stmt = mysqli_prepare(
    $conn,
    "SELECT m.management_id, COALESCE(m.mname, s.stname) AS management_name, s.email,
            {$password_select} AS password
     FROM management m
     INNER JOIN staff s ON s.staff_id = m.management_id
     WHERE s.email = ? OR m.management_id = ?
     LIMIT 1"
);

if(!$stmt){
    redirect_with_error('Management table not found. Add a management record linked to a staff account.');
}

mysqli_stmt_bind_param($stmt, 'ss', $login_id, $login_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$management = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if(!$management){
    redirect_with_error('Management account not found.');
}

if(!password_verify($mypswd, $management['password']) && $mypswd !== $management['password']){
    redirect_with_error('Wrong password.');
}

$_SESSION['management_id'] = $management['management_id'];
$_SESSION['management_name'] = $management['management_name'];
$_SESSION['management_email'] = $management['email'];

header('Location: dashboard_management.php');
exit();
?>

<?php
require_once __DIR__ . '/conn.php';
require_once __DIR__ . '/mail_config.php';
require_once __DIR__ . '/../PHPMailer-master/PHPMailer-master/src/Exception.php';
require_once __DIR__ . '/../PHPMailer-master/PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer-master/PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function password_reset_roles() {
    return [
        'student' => [
            'label' => 'Student',
            'identifier_label' => 'Register No',
            'identifier_name' => 'register_no',
            'identifier_placeholder' => 'e.g. 24BCA101',
            'login_path' => 'user/student_login.php',
            'select_sql' => 'SELECT register_no AS id, email FROM student WHERE register_no = ? AND email = ? LIMIT 1',
            'update_sql' => 'UPDATE student SET mypswd = ? WHERE register_no = ? LIMIT 1',
        ],
        'staff' => [
            'label' => 'Staff',
            'identifier_label' => 'Staff ID',
            'identifier_name' => 'staff_id',
            'identifier_placeholder' => 'e.g. 101',
            'login_path' => 'staff/staff_login.php',
            'select_sql' => 'SELECT staff_id AS id, email FROM staff WHERE staff_id = ? AND email = ? LIMIT 1',
            'update_sql' => 'UPDATE staff SET password = ? WHERE staff_id = ? LIMIT 1',
        ],
        'hod' => [
            'label' => 'HOD',
            'identifier_label' => 'HOD ID',
            'identifier_name' => 'hod_id',
            'identifier_placeholder' => 'e.g. HOD001',
            'login_path' => 'hod/hod_login.php',
            'select_sql' => 'SELECT hod_id AS id, email FROM hod WHERE hod_id = ? AND email = ? LIMIT 1',
            'update_sql' => 'UPDATE hod SET password = ? WHERE hod_id = ? LIMIT 1',
        ],
        'principal' => [
            'label' => 'Principal',
            'identifier_label' => 'Principal ID',
            'identifier_name' => 'principal_id',
            'identifier_placeholder' => 'e.g. PRINCIPAL001',
            'login_path' => 'principal/principal_login.php',
            'select_sql' => 'SELECT principal_id AS id, email FROM principal WHERE principal_id = ? AND email = ? LIMIT 1',
            'update_sql' => 'UPDATE principal SET password = ? WHERE principal_id = ? LIMIT 1',
        ],
        'management' => [
            'label' => 'Management',
            'identifier_label' => 'Management ID',
            'identifier_name' => 'management_id',
            'identifier_placeholder' => 'e.g. 201',
            'login_path' => 'view/management_login.php',
            'select_sql' => 'SELECT m.management_id AS id, s.email FROM management m INNER JOIN staff s ON s.staff_id = m.management_id WHERE m.management_id = ? AND s.email = ? LIMIT 1',
            'update_sql' => 'UPDATE management SET password = ? WHERE management_id = ? LIMIT 1',
        ],
        'admin' => [
            'label' => 'Admin',
            'identifier_label' => 'Admin ID',
            'identifier_name' => 'admin_id',
            'identifier_placeholder' => 'e.g. ADMIN001',
            'login_path' => 'admin/admin_login.php',
            'select_sql' => 'SELECT admin_id AS id, email FROM admin WHERE admin_id = ? AND email = ? LIMIT 1',
            'update_sql' => 'UPDATE admin SET mypswd = ? WHERE admin_id = ? LIMIT 1',
        ],
    ];
}

function get_password_reset_role($role) {
    $roles = password_reset_roles();
    return $roles[$role] ?? null;
}

function app_base_url() {
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    $scheme = $https ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $script_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/Online_grievance_management_system/index.php'));
    $app_dir = preg_replace('#/(admin|staff|hod|principal|management|user|view)$#', '', $script_dir);
    return rtrim($scheme . '://' . $host . $app_dir, '/');
}

function password_reset_column_exists($conn, $column_name) {
    $column_name = mysqli_real_escape_string($conn, $column_name);
    $result = @mysqli_query($conn, "SHOW COLUMNS FROM password_reset_tokens LIKE '{$column_name}'");
    return $result && mysqli_num_rows($result) > 0;
}

function ensure_password_reset_table($conn) {
    $sql = "CREATE TABLE IF NOT EXISTS password_reset_tokens (
        id INT NOT NULL AUTO_INCREMENT,
        user_role VARCHAR(30) NOT NULL,
        user_identifier VARCHAR(100) NOT NULL,
        email VARCHAR(120) NOT NULL,
        token_hash CHAR(64) NOT NULL,
        expires_at DATETIME NOT NULL,
        used_at DATETIME DEFAULT NULL,
        attempts INT NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY uq_password_reset_token_hash (token_hash),
        KEY idx_password_reset_lookup (user_role, user_identifier),
        KEY idx_password_reset_expires_at (expires_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    if(!mysqli_query($conn, $sql)){
        return false;
    }

    if(!password_reset_column_exists($conn, 'attempts')){
        return mysqli_query($conn, "ALTER TABLE password_reset_tokens ADD attempts INT NOT NULL DEFAULT 0 AFTER used_at");
    }

    return true;
}

function send_password_reset_otp_email($to_email, $role_label, $otp, $reset_link, &$error) {
    if(SMTP_HOST === '' || SMTP_USERNAME === '' || SMTP_FROM_EMAIL === ''){
        $error = 'SMTP is not configured. Set GMS_SMTP_HOST, GMS_SMTP_USERNAME, GMS_SMTP_PASSWORD, and GMS_SMTP_FROM_EMAIL.';
        return false;
    }

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->Port = SMTP_PORT;
        if(SMTP_SECURE !== ''){
            $mail->SMTPSecure = SMTP_SECURE;
        }

        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($to_email);
        $mail->isHTML(true);
        $mail->Subject = 'Password reset OTP';
        $safe_otp = htmlspecialchars($otp, ENT_QUOTES, 'UTF-8');
        $safe_link = htmlspecialchars($reset_link, ENT_QUOTES, 'UTF-8');
        $safe_role = htmlspecialchars($role_label, ENT_QUOTES, 'UTF-8');
        $mail->Body = "<p>Hello,</p><p>Your {$safe_role} password reset OTP is:</p><h2>{$safe_otp}</h2><p>This OTP expires in 10 minutes.</p><p><a href=\"{$safe_link}\">Click here to update your password</a></p><p>If you did not request this, you can ignore this email.</p>";
        $mail->AltBody = "Your {$role_label} password reset OTP is: {$otp}. It expires in 10 minutes. Update your password here: {$reset_link}";
        $mail->send();
        return true;
    } catch (Exception $e) {
        $error = 'Unable to send reset email: ' . $mail->ErrorInfo;
        return false;
    }
}

function request_password_reset($conn, $role, $identifier, $email, &$error, &$success) {
    $config = get_password_reset_role($role);
    if(!$config){
        $error = 'Invalid password reset role.';
        return;
    }

    if($identifier === '' || $email === ''){
        $error = 'Please fill all fields.';
        return;
    }

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error = 'Please enter a valid email address.';
        return;
    }

    if(!ensure_password_reset_table($conn)){
        $error = 'Unable to prepare password reset table.';
        return;
    }

    $stmt = mysqli_prepare($conn, $config['select_sql']);
    if(!$stmt){
        $error = 'Unable to check account.';
        return;
    }

    mysqli_stmt_bind_param($stmt, 'ss', $identifier, $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $account = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if(!$account){
        $success = 'If the account details are correct, an OTP has been sent to that email.';
        return;
    }

    mysqli_query($conn, "DELETE FROM password_reset_tokens WHERE expires_at < NOW() OR used_at IS NOT NULL");

    $delete_stmt = mysqli_prepare($conn, "UPDATE password_reset_tokens SET used_at = NOW() WHERE user_role = ? AND user_identifier = ? AND used_at IS NULL");
    if($delete_stmt){
        mysqli_stmt_bind_param($delete_stmt, 'ss', $role, $identifier);
        mysqli_stmt_execute($delete_stmt);
        mysqli_stmt_close($delete_stmt);
    }

    $otp = '';
    $inserted = false;
    for($attempt = 0; $attempt < 5; $attempt++){
        $otp = (string)random_int(100000, 999999);
        $otp_hash = hash('sha256', $otp);
        $insert_stmt = mysqli_prepare($conn, "INSERT INTO password_reset_tokens (user_role, user_identifier, email, token_hash, expires_at) VALUES (?, ?, ?, ?, DATE_ADD(NOW(), INTERVAL 10 MINUTE))");
        if(!$insert_stmt){
            $error = 'Unable to create reset OTP.';
            return;
        }

        mysqli_stmt_bind_param($insert_stmt, 'ssss', $role, $identifier, $email, $otp_hash);
        $inserted = mysqli_stmt_execute($insert_stmt);
        mysqli_stmt_close($insert_stmt);

        if($inserted){
            break;
        }
    }

    if(!$inserted){
        $error = 'Unable to create reset OTP. Please try again.';
        return;
    }

    $reset_url = app_base_url() . '/reset_password.php?role=' . urlencode($role) . '&id=' . urlencode($identifier);
    $mail_error = '';
    if(!send_password_reset_otp_email($email, $config['label'], $otp, $reset_url, $mail_error)){
        $error = $mail_error;
        return;
    }

    $success = 'OTP sent successfully. Enter it on the reset page to set your new password. <a href="' . htmlspecialchars($reset_url, ENT_QUOTES, 'UTF-8') . '">Open OTP reset page</a>';
}

function find_active_reset_otp($conn, $role, $identifier) {
    if($role === '' || $identifier === ''){
        return null;
    }

    if(!ensure_password_reset_table($conn)){
        return null;
    }

    $stmt = mysqli_prepare($conn, "SELECT * FROM password_reset_tokens WHERE user_role = ? AND user_identifier = ? AND used_at IS NULL AND expires_at > NOW() ORDER BY id DESC LIMIT 1");
    if(!$stmt){
        return null;
    }

    mysqli_stmt_bind_param($stmt, 'ss', $role, $identifier);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return $row ?: null;
}

function complete_password_reset($conn, $role, $identifier, $otp, $new_password, $confirm_password, &$error, &$redirect_path) {
    $otp = trim($otp);

    if($otp === '' || !preg_match('/^[0-9]{6}$/', $otp)){
        $error = 'Please enter the 6-digit OTP sent to your email.';
        return false;
    }

    if(strlen($new_password) < 6){
        $error = 'Password must be at least 6 characters.';
        return false;
    }

    if($new_password !== $confirm_password){
        $error = 'Passwords do not match.';
        return false;
    }

    $token_row = find_active_reset_otp($conn, $role, $identifier);
    if(!$token_row){
        $error = 'This OTP is invalid or expired. Please request a new OTP.';
        return false;
    }

    if((int)$token_row['attempts'] >= 5){
        $error = 'Too many OTP attempts. Please request a new OTP.';
        return false;
    }

    $otp_hash = hash('sha256', $otp);
    if(!hash_equals($token_row['token_hash'], $otp_hash)){
        $attempt_stmt = mysqli_prepare($conn, "UPDATE password_reset_tokens SET attempts = attempts + 1 WHERE id = ? LIMIT 1");
        if($attempt_stmt){
            mysqli_stmt_bind_param($attempt_stmt, 'i', $token_row['id']);
            mysqli_stmt_execute($attempt_stmt);
            mysqli_stmt_close($attempt_stmt);
        }

        $error = 'Invalid OTP. Please check your email and try again.';
        return false;
    }

    $config = get_password_reset_role($token_row['user_role']);
    if(!$config){
        $error = 'Invalid password reset role.';
        return false;
    }

    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
    $update_stmt = mysqli_prepare($conn, $config['update_sql']);
    if(!$update_stmt){
        $error = 'Unable to update password.';
        return false;
    }

    mysqli_stmt_bind_param($update_stmt, 'ss', $hashed_password, $token_row['user_identifier']);
    mysqli_stmt_execute($update_stmt);
    $updated = mysqli_stmt_affected_rows($update_stmt) >= 0;
    mysqli_stmt_close($update_stmt);

    if(!$updated){
        $error = 'Unable to update password.';
        return false;
    }

    $mark_stmt = mysqli_prepare($conn, "UPDATE password_reset_tokens SET used_at = NOW() WHERE id = ? LIMIT 1");
    if($mark_stmt){
        mysqli_stmt_bind_param($mark_stmt, 'i', $token_row['id']);
        mysqli_stmt_execute($mark_stmt);
        mysqli_stmt_close($mark_stmt);
    }

    $redirect_path = $config['login_path'];
    return true;
}
?>

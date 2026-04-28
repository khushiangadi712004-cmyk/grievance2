<?php
session_start();
include '../include/conn.php';
include '../include/complaint_helpers.php';

if(!isset($_SESSION['register_no'])){
    header('Location: student_login.php');
    exit();
}

$register_no = $_SESSION['register_no'];
$name = $_SESSION['student_name'] ?? "User";
$email = $_SESSION['student_email'] ?? "user@email.com";
$department = (int) ($_SESSION['student_department_no'] ?? 0);
$role = $_SESSION['role'] ?? "Student";
$message = '';
$message_class = '';

$stmt = mysqli_prepare($conn, "SELECT sname, email, department_no FROM student WHERE register_no = ? LIMIT 1");
if($stmt){
mysqli_stmt_bind_param($stmt, 's', $register_no);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if($student){
$_SESSION['student_name'] = $student['sname'];
$_SESSION['student_email'] = $student['email'];
$_SESSION['student_department_no'] = $student['department_no'];
$name = $student['sname'];
$email = $student['email'];
$department = (int) $student['department_no'];
}
}


/* Update profile */
if(isset($_POST['update'])){
$new_name = trim($_POST['name'] ?? '');
$new_email = trim($_POST['email'] ?? '');

if($new_name === '' || $new_email === '' || !filter_var($new_email, FILTER_VALIDATE_EMAIL)){
$message = 'Please enter a valid name and email.';
$message_class = 'error-message';
} else {
$update_stmt = mysqli_prepare($conn, "UPDATE student SET sname = ?, email = ? WHERE register_no = ?");
if($update_stmt){
mysqli_stmt_bind_param($update_stmt, 'sss', $new_name, $new_email, $register_no);
if(mysqli_stmt_execute($update_stmt)){
$_SESSION['student_name'] = $new_name;
$_SESSION['student_email'] = $new_email;
$name = $new_name;
$email = $new_email;
$message = 'Profile updated successfully.';
$message_class = 'success-message';
} else {
$message = 'Unable to update profile.';
$message_class = 'error-message';
}
mysqli_stmt_close($update_stmt);
} else {
$message = 'Unable to prepare profile update.';
$message_class = 'error-message';
}
}
}

/* Edit mode */
$edit = isset($_GET['edit']);

$name=$_SESSION['student_name']?? "User";
$email=$_SESSION['student_email']??"user@email.com";
$department=(int) ($_SESSION['student_department_no']?? 0);
$role = $_SESSION['role'] ?? "Student";
$department_name = $department > 0 ? department_name($department) : 'Not Assigned';

$initial=strtoupper(substr($name,0,1));
?>

<!DOCTYPE html>
<html>
<head>

<title>Profile</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

body{
font-family:Arial;
background:#f4f6fb;
margin:0;
}

/* header */

.header{
display:flex;
justify-content:space-between;
padding:15px 30px;
background:white;
box-shadow:0 2px 6px rgba(0,0,0,0.1);
}

.logo{
font-weight:bold;
}

.role{
background:#e8f1ff;
color:#2b6cb0;
padding:4px 10px;
border-radius:15px;
font-size:12px;
}

/* container */

.container{
width:70%;
margin:40px auto;
}

/* profile card */

.profile-card{
background:white;
border-radius:12px;
box-shadow:0 2px 10px rgba(0,0,0,0.1);
overflow:hidden;
margin-bottom:20px;
}

.banner{
height:120px;
background:linear-gradient(135deg,#0f3d56,#1f7a8c);
}

.profile-info{
padding:20px;
position:relative;
}

.avatar{
width:80px;
height:80px;
border-radius:50%;
background:#f1b300;
color:white;
display:flex;
align-items:center;
justify-content:center;
font-size:30px;
font-weight:bold;
border:4px solid white;
position:absolute;
top:-40px;
left:20px;
}

.name{
margin-top:40px;
font-size:22px;
font-weight:bold;
}

.role-tag{
background:#0f3d56;
color:white;
padding:3px 10px;
border-radius:15px;
font-size:12px;
display:inline-block;
margin-top:5px;
}

.profile-actions{
float:right;
display:flex;
gap:10px;
}

.edit-btn{
background:#f2f2f2;
border:none;
padding:8px 14px;
border-radius:6px;
cursor:pointer;
text-decoration:none;
color:black;
}

/* details */

.details{
background:white;
padding:20px;
border-radius:10px;
box-shadow:0 2px 10px rgba(0,0,0,0.1);
}

.detail{
display:flex;
align-items:center;
margin:15px 0;
}

.detail i{
width:30px;
color:#0f3d56;
}

.label{
font-size:13px;
color:gray;
}

.value{
font-weight:bold;
}

input{
width:100%;
padding:8px;
border:1px solid #ccc;
border-radius:5px;
}

.save-btn{
background:#0f3d56;
color:white;
border:none;
padding:10px;
border-radius:6px;
cursor:pointer;
margin-top:10px;
}

.back-btn{
display:inline-block;
background:#f2f2f2;
color:black;
padding:8px 14px;
border-radius:6px;
text-decoration:none;
}

.profile-message{
padding:12px;
border-radius:8px;
margin-bottom:18px;
font-size:14px;
}

.success-message{
background:#e7f8ed;
color:#176b3a;
}

.error-message{
background:#ffe2e2;
color:#b42318;
}

</style>

<link rel="stylesheet" href="../assets/css/theme.css">
</head>

<body>

<div class="header">

<div>
<span class="logo">GrievanceDesk</span>
<span class="role"><?php echo $role ?></span>
</div>

<div><?php echo $name ?></div>

</div>

<div class="container">

<div class="profile-card">

<div class="banner"></div>

<div class="profile-info">

<div class="avatar"><?php echo $initial ?></div>

<div class="profile-actions">
<a class="back-btn" href="dashboard_user.php">&larr; Back</a>
<a class="edit-btn" href="profile.php?edit=1">
<i class="fa fa-edit"></i> Edit Profile
</a>
</div>

<div class="name"><?php echo $name ?></div>
<div class="role-tag"><?php echo $role ?></div>

</div>

</div>

<div class="details">

<h3>Profile Details</h3>

<?php if($message !== ''){ ?>
<div class="profile-message <?php echo htmlspecialchars($message_class); ?>"><?php echo htmlspecialchars($message); ?></div>
<?php } ?>

<form method="post">

<div class="detail">
<i class="fa fa-user"></i>
<div style="width:100%">
<div class="label">Full Name</div>

<?php if($edit){ ?>
<input type="text" name="name" value="<?php echo $name ?>">
<?php } else { ?>
<div class="value"><?php echo $name ?></div>
<?php } ?>

</div>
</div>

<div class="detail">
<i class="fa fa-envelope"></i>
<div style="width:100%">
<div class="label">Email</div>

<?php if($edit){ ?>
<input type="email" name="email" value="<?php echo $email ?>">
<?php } else { ?>
<div class="value"><?php echo $email ?></div>
<?php } ?>

</div>
</div>

<div class="detail">
<i class="fa fa-building"></i>
<div style="width:100%">
<div class="label">Department</div>

<?php if($edit){ ?>
<input type="text" value="<?php echo htmlspecialchars($department_name); ?>" disabled>
<?php } else { ?>
<div class="value"><?php echo htmlspecialchars($department_name); ?></div>
<?php } ?>

</div>
</div>

<?php if($edit){ ?>
<button class="save-btn" name="update">Save Changes</button>
<?php } ?>

</form>

</div>

</div>

<script src="../assets/js/theme.js"></script>
</body>
</html>

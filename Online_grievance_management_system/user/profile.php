<?php
session_start();

$name = $_SESSION['name'] ?? "User";
$email = $_SESSION['email'] ?? "user@email.com";
$department = $_SESSION['department'] ?? "Not Assigned";
$role = $_SESSION['role'] ?? "Student";


/* Update profile */
if(isset($_POST['update'])){
$_SESSION['name']=$_POST['name'];
$_SESSION['email']=$_POST['email'];
$_SESSION['department']=$_POST['department'];
}

/* Edit mode */
$edit = isset($_GET['edit']);

$name=$_SESSION['name']?? "User";
$email=$_SESSION['email']??"user@email.com";
$department=$_SESSION['department']?? "Not Assigned";
$role = $_SESSION['role'] ?? "Student";

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

.edit-btn{
float:right;
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

</style>

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

<a class="edit-btn" href="profile.php?edit=1">
<i class="fa fa-edit"></i> Edit Profile
</a>

<div class="name"><?php echo $name ?></div>
<div class="role-tag"><?php echo $role ?></div>

</div>

</div>

<div class="details">

<h3>Profile Details</h3>

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
<input type="text" name="department" value="<?php echo $department ?>">
<?php } else { ?>
<div class="value"><?php echo $department ?></div>
<?php } ?>

</div>
</div>

<?php if($edit){ ?>
<button class="save-btn" name="update">Save Changes</button>
<?php } ?>

</form>

</div>

</div>

</body>
</html>
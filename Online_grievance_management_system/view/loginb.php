<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>GrievanceDesk</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
body{
font-family: Arial, Helvetica, sans-serif;
margin:0;
background:#f5f7fb;
}

.header{
background:linear-gradient(135deg,#1e5ab6,#0a3d8f);
color:white;
text-align:center;
padding:50px 20px;
}

.header h1{
font-size:40px;
margin-bottom:10px;
}

.header p{
font-size:18px;
opacity:0.9;
}

.container{
width:90%;
max-width:1100px;
margin:auto;
margin-top:-50px;
}

.role-grid{
display:grid;
grid-template-columns:repeat(3,1fr);
gap:25px;
}

.card{
background:white;
padding:25px;
border-radius:12px;
box-shadow:0 5px 15px rgba(0,0,0,0.08);
transition:0.3s;
}

.card:hover{
transform:translateY(-5px);
box-shadow:0 10px 25px rgba(0,0,0,0.15);
}

.icon{
width:50px;
height:50px;
border-radius:10px;
display:flex;
align-items:center;
justify-content:center;
color:white;
font-size:20px;
margin-bottom:15px;
}

.student{background:#2ea7c9;}
.staff{background:#34a853;}
.hod{background:#fbbc05;}
.principal{background:#f4b400;}
.management{background:#ea4335;}
.admin{background:#4285f4;}

.card h3{
margin:0;
font-size:20px;
}

.card p{
font-size:14px;
color:#555;
margin:8px 0 15px;
}

.signin{
text-decoration:none;
font-weight:bold;
color:#333;
}

.signin:hover{
color:#0a3d8f;
}

@media(max-width:900px){
.role-grid{
grid-template-columns:1fr 1fr;
}
}

@media(max-width:500px){
.role-grid{
grid-template-columns:1fr;
}
}
</style>
</head>

<body>

<div class="header">
<h1>GrievanceDesk</h1>
<h2>Online Grievance Management System — Submit, track, and resolve <br>
complaints efficiently across all the levels of institution</h2>
<p>Select your role below to get started</p>
&nbsp;
</div>

<div class="container">
<div class="role-grid">

<div class="card">
<div class="icon student"><i class="fa-solid fa-user-graduate"></i></div>
<h3>Student</h3>
<p>Submit and track your complaints </p>
<a href="../user/student_login.php" class="signin">Sign In → </a>
<button class="role" onclick="window.location.href='../user/student_login.php'"><p>
</div>

<div class="card">
<div class="icon staff"><i class="fa-solid fa-briefcase"></i></div>
<h3>Staff</h3>
<p>Submit and track your complaints</p>
<a href="../staff/../staff/staff_login.php" class="signin">Sign In →</a>
<button class="role" onclick="window.location.href='../staff/../staff/staff_login.php'"><p>
</div>

<div class="card">
<div class="icon hod"><i class="fa-solid fa-users"></i></div>
<h3>HOD</h3>
<p>Review and manage department grievances</p>
<a href="../hod/hod_login.php" class="signin">Sign In →</a>
<button class="role" onclick="window.location.href='../staff/staff_login.php'"><p>
</div>

<div class="card">
<div class="icon principal"><i class="fa-solid fa-crown"></i></div>
<h3>Principal</h3>
<p>Oversee and resolve institutional grievances</p>
<a href="principal_login.php" class="signin">Sign In →</a>
<button class="role" onclick="window.location.href='principal_login.php'"><p>
</div>

<div class="card">
<div class="icon management"><i class="fa-solid fa-building"></i></div>
<h3>Management</h3>
<p>Handle escalated grievances</p>
<a href="management_login.php" class="signin">Sign In →</a>
<button class="role" onclick="window.location.href='management_login.php'"><p>
</div>

<div class="card">
<div class="icon admin"><i class="fa-solid fa-gear"></i></div>
<h3>Admin</h3>
<p>System administration and analytics</p>
<a href="admin_login.php" class="signin">Sign In →</a>
 <button class="role" onclick="window.location.href='admin_login.php'"><p>
</div>

</div>
</div>

</body>
</html>


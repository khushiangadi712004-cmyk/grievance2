<?php
include("../include/conn.php");

if(isset($_POST['register']))
{
    $mypswd = $_POST['mypswd'] ?? '';
    $sname = $_POST['sname'];
    $register_no = $_POST['register_no'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $department_no = $_POST['department_no']; // ✅ FIXED

    $confirm_password = $_POST['confirm_password'];

    if($mypswd != $confirm_password){
        echo "<script>alert('Passwords do not match');</script>";
    }
    else{

        $encrypted_password = password_hash($mypswd, PASSWORD_DEFAULT);

        $sql = "INSERT INTO student(sname,register_no,email,phone,mypswd,department_no)
        VALUES('$sname','$register_no','$email','$phone','$encrypted_password','$department_no')";

        $result = mysqli_query($conn,$sql);

        if($result){
            echo "<script>
            alert('Registration Successful');
            window.location='student_login.php';
            </script>";
        }
        else{
            echo "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<title>Register</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

*{
box-sizing:border-box;
font-family:Arial;
}

body{
margin:0;
height:100vh;
display:flex;
justify-content:center;
align-items:center;
background:linear-gradient(135deg,#1e5ab6,#0a3d8f);
}

.container{
width:420px;
background:white;
padding:35px;
border-radius:12px;
box-shadow:0 0 20px rgba(0,0,0,0.2);
}

h2{
margin-bottom:5px;
}

.subtitle{
color:gray;
margin-bottom:20px;
}

label{
font-weight:600;
font-size:14px;
}

input,select{
width:100%;
padding:12px;
margin-top:6px;
margin-bottom:15px;
border:1px solid #ccc;
border-radius:6px;
}

.password-box{
position:relative;
}

.password-box input{
padding-right:40px;
}

.eye{
position:absolute;
right:12px;
top:50%;
transform:translateY(-50%);
cursor:pointer;
color:gray;
}

.register-btn{
width:100%;
padding:12px;
background:#1e3c72;
border:none;
color:white;
font-size:16px;
border-radius:6px;
cursor:pointer;
}

.register-btn:hover{
background:135deg,#0f3d56,#1f7a8c;
}

.bottom-text{
text-align:center;
margin-top:15px;
font-size:14px;
}

.bottom-text a{
color:135deg,#0f3d56,#1f7a8c;
text-decoration:none;
font-weight:600;
}

</style>

</head>

<body>
 
<div class="container">

<h2>Create Account</h2>
<p class="subtitle">Register for the Grievance Management System</p>

<form  method="post">


<label>Name</label>
<input type="text" name="sname" placeholder="Enter your name" required>

<label>Register No / Employee ID</label>
<input type="text" name="register_no" placeholder="Enter ID" required>

<label>Email</label>
<input type="email" name="email" placeholder="Enter email" required>

<label>Phone Number</label>
<input type="tel" name="phone" placeholder="Enter phone number" required>
 
<label>Department</label>
<select name="department_no" required>
    <option value="">Select Department</option>
    <option value="1">BCA</option>
    <option value="2">BSC</option>
    <option value="3">B.COM</option>
    <option value="4">BBA</option>
</select>

<label>Password</label>

<div class="password-box">
<input type="password" id="mypswd" name="mypswd" placeholder="Enter password" required>
<i class="fa-solid fa-eye eye" onclick="togglePassword()"></i>
</div>

<label>Confirm Password</label>
<input type="password"  name="confirm_password" placeholder="Re-enter password" required>

<button class="register-btn" name="register">Register</button>
</form>

<div class="bottom-text">
Already have an account? <a href="student_login.php">Sign In</a>
</div>

</div>

<script>

function togglePassword(){

let pass=document.getElementById("mypswd");

if(pass.type==="password"){
pass.type="text";
}
else{
pass.type="password";
}

}

</script>

</body>
</html>
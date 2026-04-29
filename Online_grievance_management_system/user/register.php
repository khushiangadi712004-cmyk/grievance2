<?php
include("../include/conn.php");

if(isset($_POST['register']))
{
    $mypswd = $_POST['mypswd'] ?? '';
    $sname = trim($_POST['sname'] ?? '');
    $register_no = trim($_POST['register_no'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $department_no = trim($_POST['department_no'] ?? '');

    $confirm_password = $_POST['confirm_password'] ?? '';

    if($mypswd != $confirm_password){
        echo "<script>alert('Passwords do not match');</script>";
    }
    elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        echo "<script>alert('Please enter a valid email address');</script>";
    }
    else{
        $check_stmt = mysqli_prepare($conn, "SELECT register_no, email FROM student WHERE register_no = ? OR email = ?");

        if($check_stmt){
            mysqli_stmt_bind_param($check_stmt, 'ss', $register_no, $email);
            mysqli_stmt_execute($check_stmt);
            $check_result = mysqli_stmt_get_result($check_stmt);
            $register_exists = false;
            $email_exists = false;

            while($existing_user = mysqli_fetch_assoc($check_result)){
                if(strcasecmp($existing_user['register_no'], $register_no) === 0){
                    $register_exists = true;
                }

                if(strcasecmp($existing_user['email'], $email) === 0){
                    $email_exists = true;
                }
            }

            mysqli_stmt_close($check_stmt);

            if($register_exists || $email_exists){
                if($register_exists && $email_exists){
                    echo "<script>alert('This register number and email are already registered');</script>";
                }
                elseif($register_exists){
                    echo "<script>alert('This register number is already registered');</script>";
                }
                else{
                    echo "<script>alert('This email is already registered');</script>";
                }
            }
            else{
                $encrypted_password = password_hash($mypswd, PASSWORD_DEFAULT);
                $insert_stmt = mysqli_prepare($conn, "INSERT INTO student(sname,register_no,email,phone,mypswd,department_no) VALUES(?,?,?,?,?,?)");

                if($insert_stmt){
                    mysqli_stmt_bind_param($insert_stmt, 'ssssss', $sname, $register_no, $email, $phone, $encrypted_password, $department_no);
                    $registration_error = 'Registration failed. Please try again';

                    try{
                        $result = mysqli_stmt_execute($insert_stmt);
                    }
                    catch(mysqli_sql_exception $e){
                        $result = false;

                        if($e->getCode() == 1062){
                            $registration_error = 'This register number or email is already registered';
                        }
                    }

                    mysqli_stmt_close($insert_stmt);

                    if($result){
                        echo "<script>
                        alert('Registration Successful');
                        window.location='student_login.php';
                        </script>";
                    }
                    else{
                        echo "<script>alert('$registration_error');</script>";
                    }
                }
                else{
                    echo "<script>alert('Registration failed. Please try again');</script>";
                }
            }
        }
        else{
            echo "<script>alert('Registration failed. Please try again');</script>";
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

/* Modern portal register theme */
html{
min-height:100%;
}

body{
min-height:100vh;
height:auto;
padding:28px 16px;
color:#111827;
background:
linear-gradient(135deg,rgba(15,23,42,0.93),rgba(30,58,138,0.84) 45%,rgba(14,116,144,0.78)),
url('https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&w=1800&q=80');
background-size:cover;
background-position:center;
background-attachment:fixed;
}

body:before{
content:"";
position:fixed;
inset:0;
background:
radial-gradient(circle at 16% 12%,rgba(255,255,255,0.16),transparent 28%),
radial-gradient(circle at 86% 18%,rgba(20,184,166,0.18),transparent 30%),
linear-gradient(180deg,rgba(15,23,42,0.08),rgba(15,23,42,0.42));
pointer-events:none;
}

.container{
position:relative;
z-index:1;
width:min(460px,100%);
background:rgba(255,255,255,0.94);
border:1px solid rgba(255,255,255,0.58);
border-radius:18px;
padding:34px;
box-shadow:0 24px 60px rgba(2,6,23,0.28);
backdrop-filter:blur(16px);
}

.container:before{
content:"";
position:absolute;
left:0;
right:0;
top:0;
height:5px;
border-radius:18px 18px 0 0;
background:linear-gradient(90deg,#1d4ed8,#0e7490,#047857,#b45309);
}

h2{
margin:0 0 6px;
color:#0f172a;
font-size:32px;
line-height:1.1;
}

.subtitle{
color:#64748b;
line-height:1.5;
margin-bottom:22px;
}

label{
display:block;
font-weight:700;
font-size:14px;
color:#1f2937;
margin-bottom:7px;
}

input,
select{
width:100%;
padding:13px 14px;
margin:0 0 16px;
border:1px solid #d7dee8;
border-radius:10px;
background:#f8fafc;
color:#111827;
font-size:15px;
outline:none;
transition:0.2s ease;
}

input:focus,
select:focus{
border-color:#1d4ed8;
background:#ffffff;
box-shadow:0 0 0 4px rgba(29,78,216,0.12);
}

.password-box input{
padding-right:44px;
}

.eye{
position:absolute;
right:14px;
top:50%;
transform:translateY(-50%);
color:#64748b;
cursor:pointer;
}

.eye:hover{
color:#1d4ed8;
}

.register-btn{
width:100%;
padding:13px;
background:linear-gradient(135deg,#1d4ed8,#0e7490);
border:none;
color:white;
font-size:16px;
font-weight:800;
border-radius:10px;
cursor:pointer;
box-shadow:0 12px 24px rgba(29,78,216,0.24);
transition:0.2s ease;
}

.register-btn:hover{
background:linear-gradient(135deg,#1e3a8a,#0e7490);
transform:translateY(-2px);
box-shadow:0 16px 30px rgba(29,78,216,0.30);
}

.bottom-text{
text-align:center;
margin-top:18px;
font-size:14px;
color:#64748b;
}

.bottom-text a{
color:#1d4ed8;
text-decoration:none;
font-weight:800;
}

.bottom-text a:hover{
color:#0e7490;
}

@media(max-width:520px){
body{
padding:18px 12px;
align-items:flex-start;
}

.container{
padding:28px 20px;
border-radius:16px;
}

h2{
font-size:29px;
}
}

</style>

<link rel="stylesheet" href="../assets/css/theme.css">
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

<script src="../assets/js/theme.js"></script>
</body>
</html>

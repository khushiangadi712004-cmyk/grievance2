<?php
session_start();

include '../include/conn.php';
include '../include/complaint_helpers.php';

$register_no = $_SESSION['register_no'] ?? '';

if (isset($_POST['submit'])) {
    $category_id = (int) ($_POST['category_id'] ?? 0);
    $department_no = (int) ($_POST['department_no'] ?? 0);
    $description = trim($_POST['description'] ?? '');

    if ($register_no === '' || !in_array($category_id, [1, 2, 3], true) || $department_no <= 0 || $description === '') {
        echo "<script>alert('Invalid complaint details');</script>";
    } else {
        switch ($category_id) {
            case 1:
                $assigned_to = 'HOD';
                break;
            case 2:
                $assigned_to = 'Principal';
                break;
            case 3:
                $assigned_to = 'Management';
                break;
            default:
                $assigned_to = '';
                break;
        }
        $handled_by_role = 'System';
        $status = 'Pending';

        $file = '';
        if (!empty($_FILES['file_upload']['name'])) {
            $file = basename($_FILES['file_upload']['name']);
            $tmp = $_FILES['file_upload']['tmp_name'];
            $target = "../uploads/" . $file;
            move_uploaded_file($tmp, $target);
        }

        // Auto route student complaint by category using prepared statement.
        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO complaint
            (register_no, staff_id, category_id, department_no, description, file_upload, assigned_to, handled_by_role, status, date_submitted)
            VALUES (?, NULL, ?, ?, ?, ?, ?, ?, ?, NOW())"
        );

        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt,
                'siisssss',
                $register_no,
                $category_id,
                $department_no,
                $description,
                $file,
                $assigned_to,
                $handled_by_role,
                $status
            );

            if (mysqli_stmt_execute($stmt)) {
                echo "<script>alert('Complaint Submitted Successfully');</script>";
            } else {
                echo "<script>alert('Unable to submit complaint');</script>";
            }

            mysqli_stmt_close($stmt);
        } else {
            echo "<script>alert('Unable to prepare complaint submission');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Submit Complaint</title>

<style>

body{
font-family: Arial;
background:#f4f6f9;
}

.container{
width:60%;
margin:auto;
margin-top:40px;
background:white;
padding:30px;
border-radius:8px;
box-shadow:0 0 10px rgba(0,0,0,0.1);
}

h2{
margin-bottom:20px;
}

input,select,textarea{
width:100%;
padding:10px;
margin-top:10px;
margin-bottom:20px;
border:1px solid #ccc;
border-radius:5px;
}

textarea{
height:120px;
}

button{
padding:10px 20px;
background:#1d3c78;
color:white;
border:none;
border-radius:5px;
cursor:pointer;
}

.actions{
display:flex;
gap:12px;
flex-wrap:wrap;
}

.back-btn{
display:inline-block;
padding:10px 20px;
background:#6b7280;
color:#fff;
text-decoration:none;
border-radius:5px;
}

</style>

</head>

<body>

<div class="container">

<h2>Submit New Complaint</h2>

<form method="POST" enctype="multipart/form-data">

<label>Category</label>
<select name="category_id" required>

<option value="">Select Category</option>
<option value="1">Academic</option>
<option value="2">Infrastructure</option>
<option value="3">Administration</option>

</select>

<label>Department</label>
<select name="department_no" required>

<option value="">Select Department</option>
<option value="1">BCA</option>
<option value="2">BSC</option>
<option value="3">B.COM</option>
<option value="4">BBA</option>


</select>

<label>Description</label>
<textarea name="description" placeholder="Describe your grievance in detail..." required></textarea>

<label>Upload File</label>
<input type="file" name="file_upload">

<div class="actions">
<button type="submit" name="submit">Submit</button>
<a class="back-btn" href="dashboard_user.php">Back</a>
</div>

</form>

</div>

</body>
</html>

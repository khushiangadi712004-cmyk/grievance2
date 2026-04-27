<?php
session_start();

include '../include/conn.php';
include '../include/complaint_helpers.php';

if(!isset($_SESSION['register_no'])){
    header('Location: student_login.php');
    exit();
}

$register_no = $_SESSION['register_no'];
$department_no = (int) ($_SESSION['student_department_no'] ?? 0);
$dept_stmt = mysqli_prepare($conn, "SELECT department_no FROM student WHERE register_no = ? LIMIT 1");
if($dept_stmt){
    mysqli_stmt_bind_param($dept_stmt, 's', $register_no);
    mysqli_stmt_execute($dept_stmt);
    $dept_result = mysqli_stmt_get_result($dept_stmt);
    $dept_row = mysqli_fetch_assoc($dept_result);
    mysqli_stmt_close($dept_stmt);
    $department_no = (int) ($dept_row['department_no'] ?? $department_no);
    $_SESSION['student_department_no'] = $department_no;
}

if (isset($_POST['submit'])) {
    $category_id = (int) ($_POST['category_id'] ?? 0);
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
        $upload_failed = false;
        $upload_error = '';
        $uploaded_file = save_uploaded_complaint_file('file_upload', $upload_error);
        if ($uploaded_file === false) {
            echo "<script>alert('" . addslashes($upload_error) . "');</script>";
            $upload_failed = true;
        } else {
            $file = $uploaded_file;
        }

        if (!$upload_failed) {
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
<input type="text" value="<?php echo htmlspecialchars(department_name((int) $department_no)); ?>" disabled>

<label>Description</label>
<textarea name="description" placeholder="Describe your grievance in detail..." required></textarea>

<label>Upload File</label>
<input type="file" name="file_upload" accept=".jpg,.jpeg,.png,image/jpeg,image/png">

<div class="actions">
<button type="submit" name="submit">Submit</button>
<a class="back-btn" href="dashboard_user.php">Back</a>
</div>

</form>

</div>

</body>
</html>

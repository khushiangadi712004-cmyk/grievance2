<?php
session_start();
include '../include/conn.php';
include '../include/complaint_helpers.php';

if(!isset($_SESSION['staff_id'])){
    header('Location: staff_login.php');
    exit();
}

$staff_id = $_SESSION['staff_id'];
$staff_name = $_SESSION['staff_name'] ?? 'Staff';
$department_no = $_SESSION['staff_department_no'] ?? '';
$staff_design = $_SESSION['staff_design'] ?? 'Staff';
$message = '';
$dept_stmt = mysqli_prepare($conn, "SELECT department_no FROM staff WHERE staff_id = ? LIMIT 1");
if($dept_stmt){
    mysqli_stmt_bind_param($dept_stmt, 'i', $staff_id);
    mysqli_stmt_execute($dept_stmt);
    $dept_result = mysqli_stmt_get_result($dept_stmt);
    $dept_row = mysqli_fetch_assoc($dept_result);
    mysqli_stmt_close($dept_stmt);
    $department_no = (int) ($dept_row['department_no'] ?? $department_no);
}

if(isset($_POST['submit'])){
    $category_id = (int) ($_POST['category_id'] ?? 0);
    $selected_department = (int) $department_no;
    $description = trim($_POST['description'] ?? '');

    if(!in_array($category_id, [1, 2, 3], true) || $selected_department <= 0 || $description === ''){
        $message = 'Invalid complaint details.';
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

        $file_name = '';
        $upload_error = '';
        $uploaded_file = save_uploaded_complaint_file('file_upload', $upload_error);
        if($uploaded_file === false){
            $message = $upload_error;
        } else {
            $file_name = $uploaded_file;
        }

        if($message === ''){
            // Auto route staff complaint by category using prepared statement.
            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO staff_complaint
                (staff_id, category_id, department_no, description, file_upload, assigned_to, handled_by_role, status, date_submitted)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())"
            );

            if($stmt){
                mysqli_stmt_bind_param(
                    $stmt,
                    'iiisssss',
                    $staff_id,
                    $category_id,
                    $selected_department,
                    $description,
                    $file_name,
                    $assigned_to,
                    $handled_by_role,
                    $status
                );

                if(mysqli_stmt_execute($stmt)){
                    $message = 'Complaint submitted successfully.';
                } else {
                    $message = 'Unable to submit complaint.';
                }

                mysqli_stmt_close($stmt);
            } else {
                $message = 'Unable to prepare complaint submission.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Raise Complaint</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:Arial, sans-serif;
}

body{
display:flex;
background:#f4f6f9;
min-height:100vh;
}

.sidebar{
width:250px;
background:linear-gradient(135deg,#0f3d56,#1f7a8c);
color:#fff;
padding:25px;
}

.sidebar h2{
margin-bottom:10px;
}

.sidebar p{
font-size:14px;
opacity:0.85;
margin-bottom:25px;
}

.sidebar a{
display:block;
color:#fff;
text-decoration:none;
padding:12px;
margin:10px 0;
border-radius:6px;
transition:0.3s;
}

.sidebar a:hover,
.sidebar a.active{
background:rgba(255,255,255,0.2);
}

.main{
flex:1;
padding:30px;
}

.header{
margin-bottom:24px;
}

.header h1{
color:#0f3d56;
margin-bottom:8px;
}

.form-card{
max-width:760px;
background:#fff;
padding:28px;
border-radius:12px;
box-shadow:0 2px 10px rgba(0,0,0,0.08);
}

label{
display:block;
font-weight:700;
margin-bottom:8px;
color:#1f2937;
}

input, select, textarea{
width:100%;
padding:12px;
margin-bottom:18px;
border:1px solid #d1d5db;
border-radius:8px;
}

textarea{
min-height:140px;
resize:vertical;
}

.btn{
background:#0f3d56;
color:#fff;
padding:12px 20px;
border:none;
border-radius:8px;
cursor:pointer;
font-size:15px;
}

.message{
margin-bottom:18px;
padding:12px;
border-radius:8px;
background:#dcfce7;
color:#166534;
}

@media(max-width:768px){
body{
flex-direction:column;
}

.sidebar{
width:100%;
}

.main{
padding:20px;
}
}
</style>
</head>
<body>

<div class="sidebar">
<h2>Staff Panel</h2>
<p><?php echo htmlspecialchars($staff_name); ?> | <?php echo htmlspecialchars($staff_design); ?> | Dept: <?php echo htmlspecialchars((string) $department_no); ?></p>

<a href="dashboard_staff.php"><i class="fa fa-home"></i> Dashboard</a>
<a href="raise_complaint.php" class="active"><i class="fa fa-pen"></i> Raise Complaint</a>
<a href="my_complaints.php"><i class="fa fa-file-lines"></i> My Complaints</a>
<a href="complaints.php"><i class="fa fa-list"></i> Assigned Complaints</a>
<a href="../index.php"><i class="fa fa-home"></i> Main Portal</a>
<a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main">
<div class="header">
<h1>Raise Complaint</h1>
<p>Submit a grievance from the staff portal. This uses the dedicated <strong>staff_complaint</strong> table.</p>
</div>

<div class="form-card">
<?php if($message !== '') { ?>
<div class="message"><?php echo htmlspecialchars($message); ?></div>
<?php } ?>

<form method="post" enctype="multipart/form-data">
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

<button type="submit" name="submit" class="btn">Submit Complaint</button>
</form>
</div>
</div>

</body>
</html>

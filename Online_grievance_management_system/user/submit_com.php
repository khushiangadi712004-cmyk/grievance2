<?php
session_start();
include '../include/conn.php';
include '../include/complaint_helpers.php';

/* Check login */
if(!isset($_SESSION['register_no'])){
    header("Location: ../view/student_login.php");
    exit();
}

$register_no = $_SESSION['register_no'];

/* 🔥 FETCH STUDENT DEPARTMENT (AUTO) */
$getDept = mysqli_query($conn, "
    SELECT s.department_no, d.department_name 
    FROM student s
    JOIN deparment d ON s.department_no = d.department_no
    WHERE s.register_no='$register_no'
");

$deptData = mysqli_fetch_assoc($getDept);

$department_no = $deptData['department_no'] ?? 0;
$department_name = $deptData['department_name'] ?? "Not Assigned";

/* ❌ STOP if no department */
if($department_no == 0){
    die("Error: Department not assigned. Contact admin.");
}

/* ================= SUBMIT ================= */
if(isset($_POST['submit']))
{
    $category_id = (int)$_POST['category_id'];
    $description = trim($_POST['description']);

    if(!in_array($category_id, [1,2,3]) || $description == ''){
        echo "<script>alert('Invalid complaint details');</script>";
    } else {

        $assigned_to = category_route($category_id);

        /* File upload */
        $file = "";
        if(!empty($_FILES['file_upload']['name'])){
            $file = time() . "_" . $_FILES['file_upload']['name'];
            $target = "../uploads/" . $file;
            move_uploaded_file($_FILES['file_upload']['tmp_name'], $target);
        }

        $status = "Pending";

        $sql = "INSERT INTO complaint
        (register_no, staff_id, category_id, department_no, description, file_upload, assigned_to, status, date_submitted)
        VALUES
        ('$register_no', NULL, '$category_id', '$department_no', '$description', '$file', '$assigned_to', '$status', NOW())";

        if(mysqli_query($conn,$sql)){
            echo "<script>alert('Complaint Submitted Successfully'); window.location='dashboard_user.php';</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
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

<!-- ✅ AUTO FILLED DEPARTMENT -->
<label>Department</label>
<input type="text" value="<?php echo $department_name; ?>" readonly>

<!-- hidden actual value -->
<input type="hidden" name="department_no" value="<?php echo $department_no; ?>">

<label>Description</label>
<textarea name="description" placeholder="Describe your grievance..." required></textarea>

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
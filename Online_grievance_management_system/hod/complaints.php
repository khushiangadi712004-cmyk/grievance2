<?php
session_start();
include '../include/conn.php';

if(!isset($_SESSION['hod_id'])){
    header('Location: hod_login.php');
    exit();
}

$hod_name = $_SESSION['hod_name'] ?? 'HOD';
$department_no = $_SESSION['hod_department_no'] ?? '';

$complaints = mysqli_query(
    $conn,
    "SELECT complaint_id, register_no, staff_id, category_id, description, file_upload, status, date_submitted
     FROM complaint
     WHERE department_no = '$department_no'
     ORDER BY complaint_id DESC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Department Complaints</title>
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
background:linear-gradient(135deg,#8e44ad,#5b2c6f);
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
color:#5b2c6f;
margin-bottom:8px;
}

.table-wrap{
background:#fff;
border-radius:12px;
overflow:auto;
box-shadow:0 2px 10px rgba(0,0,0,0.08);
}

table{
width:100%;
border-collapse:collapse;
min-width:1100px;
}

th{
background:#5b2c6f;
color:#fff;
padding:12px;
text-align:left;
}

td{
padding:12px;
border-bottom:1px solid #e5e7eb;
vertical-align:top;
}

.status{
display:inline-block;
padding:5px 10px;
border-radius:999px;
font-size:12px;
font-weight:700;
}

.pending{
background:#fde68a;
color:#854d0e;
}

.progress{
background:#bfdbfe;
color:#1d4ed8;
}

.resolved{
background:#bbf7d0;
color:#166534;
}

.file-link{
color:#5b2c6f;
font-weight:700;
text-decoration:none;
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
<h2>HOD Panel</h2>
<p><?php echo htmlspecialchars($hod_name); ?> | Dept: <?php echo htmlspecialchars((string) $department_no); ?></p>

<a href="dashboard_hod.php"><i class="fa fa-home"></i> Dashboard</a>
<a href="complaints.php" class="active"><i class="fa fa-list"></i> Department Complaints</a>
<a href="../index.php"><i class="fa fa-home"></i> Main Portal</a>
<a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main">
<div class="header">
<h1>Department Complaints</h1>
<p>All complaints currently mapped to your department.</p>
</div>

<div class="table-wrap">
<table>
<tr>
<th>ID</th>
<th>Student</th>
<th>Assigned Staff</th>
<th>Category</th>
<th>Description</th>
<th>Attachment</th>
<th>Date</th>
<th>Status</th>
</tr>

<?php if($complaints && mysqli_num_rows($complaints) > 0) { ?>
<?php while($row = mysqli_fetch_assoc($complaints)) { ?>
<tr>
<td><?php echo htmlspecialchars((string) ($row['complaint_id'] ?? '')); ?></td>
<td><?php echo htmlspecialchars((string) ($row['register_no'] ?? '')); ?></td>
<td><?php echo htmlspecialchars((string) ($row['staff_id'] ?? 'Unassigned')); ?></td>
<td><?php echo htmlspecialchars((string) ($row['category_id'] ?? '')); ?></td>
<td><?php echo htmlspecialchars((string) ($row['description'] ?? '')); ?></td>
<td>
<?php if(!empty($row['file_upload'])) { ?>
<a class="file-link" href="../uploads/<?php echo rawurlencode($row['file_upload']); ?>" target="_blank">View File</a>
<?php } else { ?>
No File
<?php } ?>
</td>
<td><?php echo htmlspecialchars((string) ($row['date_submitted'] ?? '')); ?></td>
<td>
<span class="status <?php
if($row['status'] === 'Pending'){
    echo 'pending';
} elseif($row['status'] === 'In Progress'){
    echo 'progress';
} else {
    echo 'resolved';
}
?>">
<?php echo htmlspecialchars((string) ($row['status'] ?? '')); ?>
</span>
</td>
</tr>
<?php } ?>
<?php } else { ?>
<tr>
<td colspan="8">No complaints found for this department.</td>
</tr>
<?php } ?>
</table>
</div>
</div>

</body>
</html>

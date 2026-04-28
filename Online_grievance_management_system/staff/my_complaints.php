<?php
session_start();
include '../include/conn.php';

if(!isset($_SESSION['staff_id'])){
    header('Location: staff_login.php');
    exit();
}

$staff_id = $_SESSION['staff_id'];
$staff_name = $_SESSION['staff_name'] ?? 'Staff';
$staff_design = $_SESSION['staff_design'] ?? 'Staff';

$my_complaints = mysqli_query(
    $conn,
    "SELECT complaint_id, category_id, department_no, description, file_upload, status, date_submitted
     FROM staff_complaint
     WHERE staff_id = '$staff_id'
     ORDER BY complaint_id DESC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Complaints</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:Arial,sans-serif;}
body{display:flex;background:#f4f6f9;min-height:100vh;}
.sidebar{width:250px;background:linear-gradient(135deg,#0f3d56,#1f7a8c);color:#fff;padding:25px;}
.sidebar h2{margin-bottom:10px;}
.sidebar p{font-size:14px;opacity:0.85;margin-bottom:25px;}
.sidebar a{display:block;color:#fff;text-decoration:none;padding:12px;margin:10px 0;border-radius:6px;transition:0.3s;}
.sidebar a:hover,.sidebar a.active{background:rgba(255,255,255,0.2);}
.main{flex:1;padding:30px;}
.header{margin-bottom:24px;}
.header h1{color:#0f3d56;margin-bottom:8px;}
.table-wrap{background:#fff;border-radius:12px;overflow:auto;box-shadow:0 2px 10px rgba(0,0,0,0.08);}
table{width:100%;border-collapse:collapse;min-width:1000px;}
th{background:#0f3d56;color:#fff;padding:12px;text-align:left;}
td{padding:12px;border-bottom:1px solid #e5e7eb;vertical-align:top;}
.status{display:inline-block;padding:5px 10px;border-radius:999px;font-size:12px;font-weight:700;}
.pending{background:#fde68a;color:#854d0e;}
.progress{background:#bfdbfe;color:#1d4ed8;}
.resolved{background:#bbf7d0;color:#166534;}
.file-link{color:#0f3d56;font-weight:700;text-decoration:none;}
@media(max-width:768px){body{flex-direction:column;}.sidebar{width:100%;}.main{padding:20px;}}
</style>
<link rel="stylesheet" href="../assets/css/theme.css">
</head>
<body>
<div class="sidebar">
<h2>Staff Panel</h2>
<p><?php echo htmlspecialchars($staff_name); ?> | <?php echo htmlspecialchars($staff_design); ?></p>
<a href="dashboard_staff.php"><i class="fa fa-home"></i> Dashboard</a>
<a href="raise_complaint.php"><i class="fa fa-pen"></i> Raise Complaint</a>
<a href="my_complaints.php" class="active"><i class="fa fa-file-lines"></i> My Complaints</a>
<a href="../index.php"><i class="fa fa-home"></i> Main Portal</a>
<a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
</div>
<div class="main">
<div class="header">
<h1>My Complaints</h1>
<p>View all complaints submitted by the logged-in staff member and track their status.</p>
</div>
<div class="table-wrap">
<table>
<tr>
<th>ID</th>
<th>Category</th>
<th>Department</th>
<th>Description</th>
<th>Attachment</th>
<th>Date</th>
<th>Status</th>
</tr>
<?php if($my_complaints && mysqli_num_rows($my_complaints) > 0) { ?>
<?php while($row = mysqli_fetch_assoc($my_complaints)) { ?>
<tr>
<td><?php echo htmlspecialchars((string) $row['complaint_id']); ?></td>
<td><?php echo htmlspecialchars((string) $row['category_id']); ?></td>
<td><?php echo htmlspecialchars((string) $row['department_no']); ?></td>
<td><?php echo htmlspecialchars((string) $row['description']); ?></td>
<td>
<?php if(!empty($row['file_upload'])) { ?>
<a class="file-link" href="../uploads/<?php echo rawurlencode($row['file_upload']); ?>" target="_blank">View File</a>
<?php } else { ?>
No File
<?php } ?>
</td>
<td><?php echo htmlspecialchars((string) $row['date_submitted']); ?></td>
<td><span class="status <?php if($row['status'] === 'Pending'){echo 'pending';} elseif($row['status'] === 'In Progress'){echo 'progress';} else {echo 'resolved';} ?>"><?php echo htmlspecialchars((string) $row['status']); ?></span></td>
</tr>
<?php } ?>
<?php } else { ?>
<tr><td colspan="7">No staff complaints found.</td></tr>
<?php } ?>
</table>
</div>
</div>
<script src="../assets/js/theme.js"></script>
</body>
</html>

<?php
session_start();
include '../include/conn.php';

if(!isset($_SESSION['hod_id'])){
    header('Location: hod_login.php');
    exit();
}

$hod_name = $_SESSION['hod_name'] ?? 'HOD';
$department_no = $_SESSION['hod_department_no'] ?? '';

$escalated = mysqli_query(
    $conn,
    "SELECT 'Student' AS source_label, complaint_id, register_no AS submitted_by, category_id, description, status, escalation_reason, escalated_at
     FROM complaint
     WHERE department_no = '$department_no' AND escalated_to = 'HOD'
     UNION ALL
     SELECT 'Staff' AS source_label, complaint_id, CAST(staff_id AS CHAR) AS submitted_by, category_id, description, status, escalation_reason, escalated_at
     FROM staff_complaint
     WHERE department_no = '$department_no' AND escalated_to = 'HOD'
     ORDER BY escalated_at DESC, complaint_id DESC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Escalated Complaints</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:Arial,sans-serif;}
body{display:flex;background:#f4f6f9;min-height:100vh;}
.sidebar{width:250px;background:linear-gradient(135deg,#8e44ad,#5b2c6f);color:#fff;padding:25px;}
.sidebar h2{margin-bottom:10px;}
.sidebar p{font-size:14px;opacity:0.85;margin-bottom:25px;}
.sidebar a{display:block;color:#fff;text-decoration:none;padding:12px;margin:10px 0;border-radius:6px;transition:0.3s;}
.sidebar a:hover,.sidebar a.active{background:rgba(255,255,255,0.2);}
.main{flex:1;padding:30px;}
.header{margin-bottom:24px;}
.header h1{color:#5b2c6f;margin-bottom:8px;}
.table-wrap{background:#fff;border-radius:12px;overflow:auto;box-shadow:0 2px 10px rgba(0,0,0,0.08);}
table{width:100%;border-collapse:collapse;min-width:1200px;}
th{background:#5b2c6f;color:#fff;padding:12px;text-align:left;}
td{padding:12px;border-bottom:1px solid #e5e7eb;vertical-align:top;}
.status{display:inline-block;padding:5px 10px;border-radius:999px;font-size:12px;font-weight:700;}
.pending{background:#fde68a;color:#854d0e;}
.progress{background:#bfdbfe;color:#1d4ed8;}
.resolved{background:#bbf7d0;color:#166534;}
@media(max-width:768px){body{flex-direction:column;}.sidebar{width:100%;}.main{padding:20px;}}
</style>
</head>
<body>
<div class="sidebar">
<h2>HOD Panel</h2>
<p><?php echo htmlspecialchars($hod_name); ?> | Dept: <?php echo htmlspecialchars((string) $department_no); ?></p>
<a href="dashboard_hod.php"><i class="fa fa-home"></i> Dashboard</a>
<a href="escalated_complaints.php" class="active"><i class="fa fa-arrow-trend-up"></i> Escalated Complaints</a>
<a href="complaints.php"><i class="fa fa-list"></i> Department Complaints</a>
<a href="../index.php"><i class="fa fa-home"></i> Main Portal</a>
<a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
</div>
<div class="main">
<div class="header">
<h1>Escalated Complaints</h1>
<p>Complaints escalated by admin to the HOD level for this department.</p>
</div>
<div class="table-wrap">
<table>
<tr><th>ID</th><th>Source</th><th>Submitted By</th><th>Category</th><th>Description</th><th>Reason</th><th>Status</th><th>Escalated At</th></tr>
<?php if($escalated && mysqli_num_rows($escalated) > 0) { ?>
<?php while($row = mysqli_fetch_assoc($escalated)) { ?>
<tr>
<td><?php echo htmlspecialchars((string) ($row['complaint_id'] ?? '')); ?></td>
<td><?php echo htmlspecialchars((string) ($row['source_label'] ?? '')); ?></td>
<td><?php echo htmlspecialchars((string) ($row['submitted_by'] ?? '')); ?></td>
<td><?php echo htmlspecialchars((string) ($row['category_id'] ?? '')); ?></td>
<td><?php echo htmlspecialchars((string) ($row['description'] ?? '')); ?></td>
<td><?php echo htmlspecialchars((string) ($row['escalation_reason'] ?? '')); ?></td>
<td><span class="status <?php if(($row['status'] ?? '') === 'Pending'){echo 'pending';} elseif(($row['status'] ?? '') === 'In Progress'){echo 'progress';} else {echo 'resolved';} ?>"><?php echo htmlspecialchars((string) ($row['status'] ?? '')); ?></span></td>
<td><?php echo htmlspecialchars((string) ($row['escalated_at'] ?? '')); ?></td>
</tr>
<?php } ?>
<?php } else { ?>
<tr><td colspan="8">No complaints escalated to HOD.</td></tr>
<?php } ?>
</table>
</div>
</div>
</body>
</html>

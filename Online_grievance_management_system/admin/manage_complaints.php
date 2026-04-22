<?php
session_start();
include '../include/conn.php';

if(!isset($_SESSION['admin_id'])){
    header('Location: admin_login.php');
    exit();
}

$admin_name = $_SESSION['admin_name'] ?? 'Admin';
$message = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $source_type = $_POST['source_type'] ?? '';
    $complaint_id = (int) ($_POST['complaint_id'] ?? 0);
    $escalated_to = $_POST['escalated_to'] ?? '';
    $escalation_reason = trim($_POST['escalation_reason'] ?? '');

    $allowed_targets = ['HOD', 'Principal'];
    $table_name = $source_type === 'staff' ? 'staff_complaint' : 'complaint';

    if($complaint_id > 0 && in_array($escalated_to, $allowed_targets, true)){
        $stmt = mysqli_prepare(
            $conn,
            "UPDATE {$table_name}
             SET escalated_to = ?, escalation_reason = ?, escalated_at = NOW(), handled_by_role = 'Admin'
             WHERE complaint_id = ?"
        );

        if($stmt){
            mysqli_stmt_bind_param($stmt, 'ssi', $escalated_to, $escalation_reason, $complaint_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            $message = 'Complaint escalated successfully.';
        } else {
            $message = 'Unable to escalate complaint. Import sql/escalation_setup.sql first.';
        }
    }
}

$all_complaints = mysqli_query(
    $conn,
    "SELECT 
    'Student' COLLATE utf8mb4_general_ci AS source_label,
    'complaint' COLLATE utf8mb4_general_ci AS source_type,
    complaint_id,
    register_no COLLATE utf8mb4_general_ci AS submitted_by,
    department_no,
    category_id,
    description COLLATE utf8mb4_general_ci AS description,
    status COLLATE utf8mb4_general_ci AS status,
    escalated_to COLLATE utf8mb4_general_ci AS escalated_to,
    date_submitted
FROM complaint

UNION ALL

SELECT 
    'Staff' COLLATE utf8mb4_general_ci,
    'staff' COLLATE utf8mb4_general_ci,
    complaint_id,
    CAST(staff_id AS CHAR) COLLATE utf8mb4_general_ci,
    department_no,
    category_id,
    description COLLATE utf8mb4_general_ci AS description,
    status COLLATE utf8mb4_general_ci AS status,
    escalated_to COLLATE utf8mb4_general_ci AS escalated_to,
    date_submitted
FROM staff_complaint

ORDER BY date_submitted DESC, complaint_id DESC;"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Complaint Control</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:Arial,sans-serif;}
body{display:flex;background:#f4f6f9;min-height:100vh;}
.sidebar{width:260px;background:linear-gradient(135deg,#4b1d95,#1d4f91);color:#fff;padding:25px;}
.sidebar h2{margin-bottom:10px;}
.sidebar p{font-size:14px;opacity:0.85;margin-bottom:25px;}
.sidebar a{display:block;color:#fff;text-decoration:none;padding:12px;margin:10px 0;border-radius:6px;transition:0.3s;}
.sidebar a:hover,.sidebar a.active{background:rgba(255,255,255,0.2);}
.main{flex:1;padding:30px;}
.header{margin-bottom:24px;}
.header h1{color:#1d3557;margin-bottom:8px;}
.message{margin-bottom:18px;padding:12px;border-radius:8px;background:#dcfce7;color:#166534;}
.table-wrap{background:#fff;border-radius:12px;overflow:auto;box-shadow:0 2px 10px rgba(0,0,0,0.08);}
table{width:100%;border-collapse:collapse;min-width:1400px;}
th{background:#1d3557;color:#fff;padding:12px;text-align:left;}
td{padding:12px;border-bottom:1px solid #e5e7eb;vertical-align:top;}
.status{display:inline-block;padding:5px 10px;border-radius:999px;font-size:12px;font-weight:700;}
.pending{background:#fde68a;color:#854d0e;}
.progress{background:#bfdbfe;color:#1d4ed8;}
.resolved{background:#bbf7d0;color:#166534;}
.tag{display:inline-block;padding:4px 8px;border-radius:999px;font-size:12px;font-weight:700;background:#e0e7ff;color:#3730a3;}
select,textarea,button{width:100%;padding:10px;border-radius:8px;border:1px solid #d1d5db;}
textarea{min-height:90px;resize:vertical;margin-top:8px;}
button{background:#1d4f91;color:#fff;border:none;margin-top:8px;cursor:pointer;}
@media(max-width:768px){body{flex-direction:column;}.sidebar{width:100%;}.main{padding:20px;}}
</style>
</head>
<body>
<div class="sidebar">
<h2>Admin Panel</h2>
<p>Logged in as <?php echo htmlspecialchars($admin_name); ?></p>
<a href="dashboard_admin.php"><i class="fa fa-chart-line"></i> Dashboard</a>
<a href="manage_complaints.php" class="active"><i class="fa fa-arrow-up-right-dots"></i> Complaint Control</a>
<a href="student_module.php"><i class="fa fa-users"></i> Student Module</a>
<a href="../index.php"><i class="fa fa-home"></i> Main Portal</a>
<a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
</div>
<div class="main">
<div class="header">
<h1>Complaint Control</h1>
<p>Escalate unresolved student or staff complaints to HOD or Principal.</p>
</div>
<?php if($message !== '') { ?><div class="message"><?php echo htmlspecialchars($message); ?></div><?php } ?>
<div class="table-wrap">
<table>
<tr><th>ID</th><th>Source</th><th>Submitted By</th><th>Dept</th><th>Category</th><th>Description</th><th>Status</th><th>Escalated To</th><th>Escalate</th></tr>
<?php if($all_complaints && mysqli_num_rows($all_complaints) > 0) { ?>
<?php while($row = mysqli_fetch_assoc($all_complaints)) { ?>
<tr>
<td><?php echo htmlspecialchars((string) ($row['complaint_id'] ?? '')); ?></td>
<td><span class="tag"><?php echo htmlspecialchars((string) ($row['source_label'] ?? '')); ?></span></td>
<td><?php echo htmlspecialchars((string) ($row['submitted_by'] ?? '')); ?></td>
<td><?php echo htmlspecialchars((string) ($row['department_no'] ?? '')); ?></td>
<td><?php echo htmlspecialchars((string) ($row['category_id'] ?? '')); ?></td>
<td><?php echo htmlspecialchars((string) ($row['description'] ?? '')); ?></td>
<td><span class="status <?php if(($row['status'] ?? '') === 'Pending'){echo 'pending';} elseif(($row['status'] ?? '') === 'In Progress'){echo 'progress';} else {echo 'resolved';} ?>"><?php echo htmlspecialchars((string) ($row['status'] ?? '')); ?></span></td>
<td><?php echo htmlspecialchars((string) ($row['escalated_to'] ?? 'Not Escalated')); ?></td>
<td>
<form method="post">
<input type="hidden" name="source_type" value="<?php echo htmlspecialchars((string) ($row['source_type'] ?? 'complaint')); ?>">
<input type="hidden" name="complaint_id" value="<?php echo htmlspecialchars((string) ($row['complaint_id'] ?? '0')); ?>">
<select name="escalated_to" required>
<option value="">Choose level</option>
<option value="HOD">Escalate to HOD</option>
<option value="Principal">Escalate to Principal</option>
</select>
<textarea name="escalation_reason" placeholder="Reason for escalation"></textarea>
<button type="submit">Escalate</button>
</form>
</td>
</tr>
<?php } ?>
<?php } else { ?>
<tr><td colspan="9">No complaints found.</td></tr>
<?php } ?>
</table>
</div>
</div>
</body>
</html>


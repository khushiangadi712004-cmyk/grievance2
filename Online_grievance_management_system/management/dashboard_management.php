<?php
session_start();
include '../include/conn.php';
include '../include/complaint_helpers.php';

if(!isset($_SESSION['management_id'])){
    header('Location: ../view/management_login.php');
    exit();
}

$management_name = $_SESSION['management_name'] ?? 'Management';
$message = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $source_type = $_POST['source_type'] ?? 'complaint';
    $table_name = $source_type === 'staff' ? 'staff_complaint' : 'complaint';
    $complaint_id = (int) ($_POST['complaint_id'] ?? 0);
    $action = $_POST['action_type'] ?? '';
    $remarks = trim($_POST['remarks'] ?? '');
    $message = handle_complaint_action($conn, $table_name, $complaint_id, $source_type, 'Management', $action, '', $remarks);
}

$management_total = fetch_count($conn, "SELECT COUNT(*) FROM complaint WHERE assigned_to = 'Management'") + fetch_count($conn, "SELECT COUNT(*) FROM staff_complaint WHERE assigned_to = 'Management'");
$management_pending = fetch_count($conn, "SELECT COUNT(*) FROM complaint WHERE assigned_to = 'Management' AND status = 'Pending'") + fetch_count($conn, "SELECT COUNT(*) FROM staff_complaint WHERE assigned_to = 'Management' AND status = 'Pending'");
$management_progress = fetch_count($conn, "SELECT COUNT(*) FROM complaint WHERE assigned_to = 'Management' AND status = 'In Progress'") + fetch_count($conn, "SELECT COUNT(*) FROM staff_complaint WHERE assigned_to = 'Management' AND status = 'In Progress'");
$management_resolved = fetch_count($conn, "SELECT COUNT(*) FROM complaint WHERE assigned_to = 'Management' AND status = 'Resolved'") + fetch_count($conn, "SELECT COUNT(*) FROM staff_complaint WHERE assigned_to = 'Management' AND status = 'Resolved'");

$complaints = mysqli_query(
    $conn,
    "SELECT 'Student' AS source_label, 'complaint' AS source_type, complaint_id, CAST(register_no AS CHAR) AS submitted_by, department_no, category_id, description, status, escalated_at
     FROM complaint
     WHERE assigned_to = 'Management'
     UNION ALL
     SELECT 'Staff' AS source_label, 'staff' AS source_type, complaint_id, CAST(staff_id AS CHAR) AS submitted_by, department_no, category_id, description, status, escalated_at
     FROM staff_complaint
     WHERE assigned_to = 'Management'
     ORDER BY escalated_at DESC, complaint_id DESC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Management Dashboard</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:Arial,sans-serif;}
body{display:flex;background:#f4f6f9;min-height:100vh;}
.sidebar{width:260px;background:linear-gradient(135deg,#14532d,#15803d);color:#fff;padding:25px;}
.sidebar h2{margin-bottom:10px;}
.sidebar p{font-size:14px;opacity:0.85;margin-bottom:25px;}
.sidebar a{display:block;color:#fff;text-decoration:none;padding:12px;margin:10px 0;border-radius:6px;transition:0.3s;}
.sidebar a:hover,.sidebar a.active{background:rgba(255,255,255,0.2);}
.main{flex:1;padding:30px;}
.header{margin-bottom:30px;}
.header h1{color:#14532d;margin-bottom:8px;}
.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:20px;margin-bottom:30px;}
.card{background:white;padding:20px;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,0.08);}
.card h3{color:#15803d;margin-bottom:10px;font-size:18px;}
.card p{font-size:30px;font-weight:700;color:#111827;}
table{width:100%;border-collapse:collapse;background:white;border-radius:12px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,0.08);}
th{background:#14532d;color:white;padding:12px;text-align:left;}
td{padding:12px;border-bottom:1px solid #e5e7eb;vertical-align:top;}
.status{display:inline-block;padding:5px 10px;border-radius:999px;font-size:12px;font-weight:700;}
.pending{background:#fde68a;color:#854d0e;}
.progress{background:#bfdbfe;color:#1d4ed8;}
.resolved{background:#bbf7d0;color:#166534;}
.actions form{display:grid;gap:8px;min-width:220px;}
.actions textarea,.actions button{width:100%;padding:10px;border-radius:8px;border:1px solid #d1d5db;}
.actions button{border:none;background:#15803d;color:#fff;cursor:pointer;}
@media(max-width:768px){body{flex-direction:column;}.sidebar{width:100%;}.main{padding:20px;}}
</style>
</head>
<body>
<div class="sidebar">
<h2>Management Panel</h2>
<p><?php echo htmlspecialchars($management_name); ?></p>
<a href="dashboard_management.php" class="active"><i class="fa fa-building"></i> Escalated Complaints</a>
<a href="../index.php"><i class="fa fa-home"></i> Main Portal</a>
<a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
</div>
<div class="main">
<div class="header">
<h1>Welcome, <?php echo htmlspecialchars($management_name); ?></h1>
<p>Review administration complaints and any issues escalated to management.</p>
</div>
<?php if($message !== '') { ?><div style="margin-bottom:18px;padding:12px;border-radius:8px;background:#dcfce7;color:#166534;"><?php echo htmlspecialchars($message); ?></div><?php } ?>
<div class="cards">
<div class="card"><h3>Total Escalated</h3><p><?php echo $management_total; ?></p></div>
<div class="card"><h3>Pending</h3><p><?php echo $management_pending; ?></p></div>
<div class="card"><h3>In Progress</h3><p><?php echo $management_progress; ?></p></div>
<div class="card"><h3>Resolved</h3><p><?php echo $management_resolved; ?></p></div>
</div>
<table>
<tr><th>ID</th><th>Source</th><th>Submitted By</th><th>Dept</th><th>Category</th><th>Description</th><th>Status</th><th>Escalated At</th><th>Action</th></tr>
<?php if($complaints && mysqli_num_rows($complaints) > 0) { ?>
<?php while($row = mysqli_fetch_assoc($complaints)) { ?>
<tr>
<td><?php echo htmlspecialchars((string) ($row['complaint_id'] ?? '')); ?></td>
<td><?php echo htmlspecialchars((string) ($row['source_label'] ?? '')); ?></td>
<td><?php echo htmlspecialchars((string) ($row['submitted_by'] ?? '')); ?></td>
<td><?php echo htmlspecialchars(department_name((int) ($row['department_no'] ?? 0))); ?></td>
<td><?php echo htmlspecialchars(category_name((int) ($row['category_id'] ?? 0))); ?></td>
<td><?php echo htmlspecialchars((string) ($row['description'] ?? '')); ?></td>
<td><span class="status <?php echo status_class((string) ($row['status'] ?? 'Pending')); ?>"><?php echo htmlspecialchars((string) ($row['status'] ?? '')); ?></span></td>
<td><?php echo htmlspecialchars((string) ($row['escalated_at'] ?? '')); ?></td>
<td class="actions">
<form method="post">
<input type="hidden" name="source_type" value="<?php echo htmlspecialchars((string) ($row['source_type'] ?? 'complaint')); ?>">
<input type="hidden" name="complaint_id" value="<?php echo htmlspecialchars((string) ($row['complaint_id'] ?? '0')); ?>">
<textarea name="remarks" placeholder="Resolution remarks"></textarea>
<button type="submit" name="action_type" value="progress">In Progress</button>
<button type="submit" name="action_type" value="resolve">Resolve</button>
</form>
</td>
</tr>
<?php } ?>
<?php } else { ?>
<tr><td colspan="9">No complaints escalated to management.</td></tr>
<?php } ?>
</table>
</div>
</body>
</html>

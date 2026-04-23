<?php
session_start();
include '../include/conn.php';
include '../include/complaint_helpers.php';

if(!isset($_SESSION['principal_id'])){
    header('Location: principal_login.php');
    exit();
}

$principal_name = $_SESSION['principal_name'] ?? 'Principal';
$message = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $source_type = $_POST['source_type'] ?? 'complaint';
    $table_name = $source_type === 'staff' ? 'staff_complaint' : 'complaint';
    $complaint_id = (int) ($_POST['complaint_id'] ?? 0);
    $action = $_POST['action_type'] ?? '';
    $remarks = trim($_POST['remarks'] ?? '');
    $target_role = $_POST['escalated_to'] ?? '';
    $message = handle_complaint_action($conn, $table_name, $complaint_id, $source_type, 'Principal', $action, $target_role, $remarks);
}

$principal_total = fetch_count($conn, "SELECT COUNT(*) FROM complaint WHERE assigned_to = 'Principal'") + fetch_count($conn, "SELECT COUNT(*) FROM staff_complaint WHERE assigned_to = 'Principal'");
$principal_pending = fetch_count($conn, "SELECT COUNT(*) FROM complaint WHERE assigned_to = 'Principal' AND status = 'Pending'") + fetch_count($conn, "SELECT COUNT(*) FROM staff_complaint WHERE assigned_to = 'Principal' AND status = 'Pending'");
$principal_progress = fetch_count($conn, "SELECT COUNT(*) FROM complaint WHERE assigned_to = 'Principal' AND status = 'In Progress'") + fetch_count($conn, "SELECT COUNT(*) FROM staff_complaint WHERE assigned_to = 'Principal' AND status = 'In Progress'");
$principal_resolved = fetch_count($conn, "SELECT COUNT(*) FROM complaint WHERE assigned_to = 'Principal' AND status = 'Resolved'") + fetch_count($conn, "SELECT COUNT(*) FROM staff_complaint WHERE assigned_to = 'Principal' AND status = 'Resolved'");

$escalated = mysqli_query(
    $conn,
    "SELECT 
    'Student' COLLATE utf8mb4_unicode_ci AS source_label,
    'complaint' COLLATE utf8mb4_unicode_ci AS source_type,
    complaint_id,
    CAST(register_no AS CHAR) COLLATE utf8mb4_unicode_ci AS submitted_by,
    department_no,
    category_id,
    description COLLATE utf8mb4_unicode_ci AS description,
    status COLLATE utf8mb4_unicode_ci AS status,
    escalated_at
FROM complaint 
WHERE assigned_to = 'Principal'

UNION ALL

SELECT 
    'Staff' COLLATE utf8mb4_unicode_ci,
    'staff' COLLATE utf8mb4_unicode_ci,
    complaint_id,
    CAST(staff_id AS CHAR) COLLATE utf8mb4_unicode_ci,
    department_no,
    category_id,
    description COLLATE utf8mb4_unicode_ci AS description,
    status COLLATE utf8mb4_unicode_ci AS status,
    escalated_at
FROM staff_complaint 
WHERE assigned_to = 'Principal'

ORDER BY escalated_at DESC, complaint_id DESC;"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Principal Dashboard</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:Arial,sans-serif;}
body{display:flex;background:#f4f6f9;min-height:100vh;}
.sidebar{width:260px;background:linear-gradient(135deg,#7c2d12,#b45309);color:#fff;padding:25px;}
.sidebar h2{margin-bottom:10px;}
.sidebar p{font-size:14px;opacity:0.85;margin-bottom:25px;}
.sidebar a{display:block;color:#fff;text-decoration:none;padding:12px;margin:10px 0;border-radius:6px;transition:0.3s;}
.sidebar a:hover,.sidebar a.active{background:rgba(255,255,255,0.2);}
.main{flex:1;padding:30px;}
.header{margin-bottom:30px;}
.header h1{color:#7c2d12;margin-bottom:8px;}
.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:20px;margin-bottom:30px;}
.card{background:white;padding:20px;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,0.08);}
.card h3{color:#b45309;margin-bottom:10px;font-size:18px;}
.card p{font-size:30px;font-weight:700;color:#111827;}
table{width:100%;border-collapse:collapse;background:white;border-radius:12px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,0.08);}
th{background:#7c2d12;color:white;padding:12px;text-align:left;}
td{padding:12px;border-bottom:1px solid #e5e7eb;vertical-align:top;}
.status{display:inline-block;padding:5px 10px;border-radius:999px;font-size:12px;font-weight:700;}
.pending{background:#fde68a;color:#854d0e;}
.progress{background:#bfdbfe;color:#1d4ed8;}
.resolved{background:#bbf7d0;color:#166534;}
.actions form{display:grid;gap:8px;min-width:220px;}
.actions textarea,.actions select,.actions button{width:100%;padding:10px;border-radius:8px;border:1px solid #d1d5db;}
.actions button{border:none;background:#7c2d12;color:#fff;cursor:pointer;}
@media(max-width:768px){body{flex-direction:column;}.sidebar{width:100%;}.main{padding:20px;}}
</style>
</head>
<body>
<div class="sidebar">
<h2>Principal Panel</h2>
<p><?php echo htmlspecialchars($principal_name); ?></p>
<a href="dashboard_principal.php" class="active"><i class="fa fa-home"></i> Escalated Complaints</a>
<a href="../index.php"><i class="fa fa-home"></i> Main Portal</a>
<a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
</div>
<div class="main">
<div class="header">
<h1>Welcome, <?php echo htmlspecialchars($principal_name); ?></h1>
<p>Review complaints escalated to the principal level.</p>
</div>
<?php if($message !== '') { ?><div style="margin-bottom:18px;padding:12px;border-radius:8px;background:#ffedd5;color:#9a3412;"><?php echo htmlspecialchars($message); ?></div><?php } ?>
<div class="cards">
<div class="card"><h3>Total Escalated</h3><p><?php echo $principal_total; ?></p></div>
<div class="card"><h3>Pending</h3><p><?php echo $principal_pending; ?></p></div>
<div class="card"><h3>In Progress</h3><p><?php echo $principal_progress; ?></p></div>
<div class="card"><h3>Resolved</h3><p><?php echo $principal_resolved; ?></p></div>
</div>
<table>
<tr><th>ID</th><th>Source</th><th>Submitted By</th><th>Dept</th><th>Category</th><th>Description</th><th>Status</th><th>Escalated At</th><th>Action</th></tr>
<?php if($escalated && mysqli_num_rows($escalated) > 0) { ?>
<?php while($row = mysqli_fetch_assoc($escalated)) { ?>
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
<textarea name="remarks" placeholder="Remarks"></textarea>
<button type="submit" name="action_type" value="progress">In Progress</button>
<button type="submit" name="action_type" value="resolve">Resolve</button>

</form>
</td>
</tr>
<?php } ?>
<?php } else { ?>
<tr><td colspan="9">No complaints escalated to principal.</td></tr>
<?php } ?>
</table>
</div>
</body>
</html>

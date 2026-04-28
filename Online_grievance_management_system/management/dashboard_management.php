<?php
session_start();
include '../include/conn.php';
include '../include/complaint_helpers.php';

if(!isset($_SESSION['management_id'])){
    header('Location: ../view/management_login.php');
    exit();
}

ensure_complaint_columns($conn);

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
    "SELECT CONVERT('Student' USING utf8mb4) COLLATE utf8mb4_unicode_ci AS source_label, CONVERT('complaint' USING utf8mb4) COLLATE utf8mb4_unicode_ci AS source_type, c.complaint_id,
            CONVERT(c.register_no USING utf8mb4) COLLATE utf8mb4_unicode_ci AS submitted_by,
            CONVERT(s.sname USING utf8mb4) COLLATE utf8mb4_unicode_ci AS submitter_name,
            COALESCE(s.department_no, c.department_no) AS department_no,
            c.category_id,
            CONVERT(c.description USING utf8mb4) COLLATE utf8mb4_unicode_ci AS description,
            CONVERT(c.status USING utf8mb4) COLLATE utf8mb4_unicode_ci AS status,
            c.date_submitted,
            CONVERT(c.file_upload USING utf8mb4) COLLATE utf8mb4_unicode_ci AS file_upload
     FROM complaint c
     LEFT JOIN student s ON s.register_no = c.register_no
     WHERE c.assigned_to = 'Management'
     UNION ALL
     SELECT CONVERT('Staff' USING utf8mb4) COLLATE utf8mb4_unicode_ci AS source_label, CONVERT('staff' USING utf8mb4) COLLATE utf8mb4_unicode_ci AS source_type, sc.complaint_id,
            CONVERT(CAST(sc.staff_id AS CHAR) USING utf8mb4) COLLATE utf8mb4_unicode_ci AS submitted_by,
            CONVERT(st.stname USING utf8mb4) COLLATE utf8mb4_unicode_ci AS submitter_name,
            COALESCE(st.department_no, sc.department_no) AS department_no,
            sc.category_id,
            CONVERT(sc.description USING utf8mb4) COLLATE utf8mb4_unicode_ci AS description,
            CONVERT(sc.status USING utf8mb4) COLLATE utf8mb4_unicode_ci AS status,
            sc.date_submitted,
            CONVERT(sc.file_upload USING utf8mb4) COLLATE utf8mb4_unicode_ci AS file_upload
     FROM staff_complaint sc
     LEFT JOIN staff st ON st.staff_id = sc.staff_id
     WHERE sc.assigned_to = 'Management'
     ORDER BY date_submitted DESC, complaint_id DESC"
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
.complaint-image{display:flex;align-items:center;gap:10px;min-width:140px;}
.complaint-image img{width:90px;height:68px;object-fit:cover;border-radius:8px;border:1px solid #d1d5db;background:#f8fafc;}
.complaint-image a{color:#15803d;font-weight:700;text-decoration:none;}
.muted{color:#6b7280;font-size:13px;}
@media(max-width:768px){body{flex-direction:column;}.sidebar{width:100%;}.main{padding:20px;}}
</style>
<link rel="stylesheet" href="../assets/css/theme.css">
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
<tr><th>ID</th><th>Source</th><th>Submitted By</th><th>Department</th><th>Category</th><th>Description</th><th>Status</th><th>Date Submitted</th><th>Uploaded Image</th><th>Action</th></tr>
<?php if($complaints && mysqli_num_rows($complaints) > 0) { ?>
<?php while($row = mysqli_fetch_assoc($complaints)) { ?>
<?php
$file_name = basename((string) ($row['file_upload'] ?? ''));
$file_path = $file_name !== '' ? '../uploads/' . rawurlencode($file_name) : '';
$server_file_path = $file_name !== '' ? __DIR__ . '/../uploads/' . $file_name : '';
$file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
$is_image = $file_name !== '' && is_file($server_file_path) && in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'jfif'], true);
?>
<tr>
<td><?php echo htmlspecialchars((string) ($row['complaint_id'] ?? '')); ?></td>
<td><?php echo htmlspecialchars((string) ($row['source_label'] ?? '')); ?></td>
<td><?php echo htmlspecialchars((string) ($row['submitted_by'] ?? '')); ?><?php if(!empty($row['submitter_name'])) { ?><br><span class="muted"><?php echo htmlspecialchars((string) $row['submitter_name']); ?></span><?php } ?></td>
<td><?php echo htmlspecialchars(department_name((int) ($row['department_no'] ?? 0))); ?></td>
<td><?php echo htmlspecialchars(category_name((int) ($row['category_id'] ?? 0))); ?></td>
<td><?php echo htmlspecialchars((string) ($row['description'] ?? '')); ?></td>
<td><span class="status <?php echo status_class((string) ($row['status'] ?? 'Pending')); ?>"><?php echo htmlspecialchars((string) ($row['status'] ?? '')); ?></span></td>
<td><?php echo htmlspecialchars((string) ($row['date_submitted'] ?? '')); ?></td>
<td>
<?php if($is_image) { ?>
<div class="complaint-image">
<a href="<?php echo htmlspecialchars($file_path); ?>" target="_blank"><img src="<?php echo htmlspecialchars($file_path); ?>" alt="Complaint image"></a>
</div>
<?php } else { ?>
<span class="muted">No image</span>
<?php } ?>
</td>
<td class="actions">
<form method="post">
<input type="hidden" name="source_type" value="<?php echo htmlspecialchars((string) ($row['source_type'] ?? 'complaint')); ?>">
<input type="hidden" name="complaint_id" value="<?php echo htmlspecialchars((string) ($row['complaint_id'] ?? '0')); ?>">
<textarea name="remarks" placeholder="Resolution remarks"></textarea>
<button type="submit" name="action_type" value="resolve">Resolve</button>
<button type="submit" name="action_type" value="progress">In Progress</button>
</form>
</td>
</tr>
<?php } ?>
<?php } else { ?>
<tr><td colspan="10">No complaints escalated to management.</td></tr>
<?php } ?>
</table>
</div>
<script src="../assets/js/theme.js"></script>
</body>
</html>

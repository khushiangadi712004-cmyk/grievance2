<?php
session_start();
include '../include/conn.php';
include '../include/complaint_helpers.php';

if(!isset($_SESSION['admin_id'])){
    header('Location: admin_login.php');
    exit();
}

$admin_name = $_SESSION['admin_name'] ?? 'Admin';
$message = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $source_type = $_POST['source_type'] ?? 'complaint';
    $complaint_id = (int) ($_POST['complaint_id'] ?? 0);
    $action = $_POST['action_type'] ?? '';
    $escalated_to = $_POST['escalated_to'] ?? '';
    $remarks = trim($_POST['remarks'] ?? '');
    $table_name = $source_type === 'staff' ? 'staff_complaint' : 'complaint';

    if($action === 'route_by_category'){
        $category_id = (int) ($_POST['category_id'] ?? 0);
        $escalated_to = category_route($category_id);
        if($remarks === ''){
            $remarks = 'Routed by admin based on ' . category_name($category_id) . ' category.';
        }
        $message = handle_complaint_action($conn, $table_name, $complaint_id, $source_type, 'Admin', 'escalate', $escalated_to, $remarks);
    } elseif(in_array($action, ['progress', 'resolve'], true)){
        $message = handle_complaint_action($conn, $table_name, $complaint_id, $source_type, 'Admin', $action, '', $remarks);
    } elseif($action === 'escalate'){
        $allowed_targets = ['HOD', 'Principal', 'Management'];

        if($complaint_id > 0 && in_array($escalated_to, $allowed_targets, true)){
            // Admin escalation updates both assigned_to and escalated_to.
            $stmt = mysqli_prepare(
                $conn,
                "UPDATE {$table_name}
                 SET assigned_to = ?,
                     escalated_to = ?,
                     escalation_reason = ?,
                     escalated_at = NOW(),
                     handled_by_role = 'Admin'
                 WHERE complaint_id = ?"
            );

            if($stmt){
                mysqli_stmt_bind_param($stmt, 'sssi', $escalated_to, $escalated_to, $remarks, $complaint_id);
                mysqli_stmt_execute($stmt);

                if(mysqli_stmt_affected_rows($stmt) > 0){
                    $message = 'Complaint escalated successfully.';
                } else {
                    $message = 'No complaint was updated.';
                }

                mysqli_stmt_close($stmt);
            } else {
                $message = 'Unable to escalate complaint.';
            }
        } else {
            $message = 'Invalid escalation request.';
        }
    } else {
        $message = 'Choose a valid complaint action.';
    }
}

$all_complaints = mysqli_query(
    $conn,
    "SELECT 
    'Student' AS source_label,
    'complaint' AS source_type,
    complaint_id,
    CAST(register_no AS CHAR) AS submitted_by,
    department_no,
    category_id,
    CAST(description AS CHAR) AS description,
    CAST(status AS CHAR) AS status,
    CAST(assigned_to AS CHAR) AS assigned_to,
    CAST(escalated_to AS CHAR) AS escalated_to,
    CAST(file_upload AS CHAR) AS file_upload,
    date_submitted
FROM complaint

UNION ALL

SELECT 
    'Staff',
    'staff',
    complaint_id,
    CAST(staff_id AS CHAR),
    department_no,
    category_id,
    CAST(description AS CHAR) AS description,
    CAST(status AS CHAR) AS status,
    CAST(assigned_to AS CHAR) AS assigned_to,
    CAST(escalated_to AS CHAR) AS escalated_to,
    CAST(file_upload AS CHAR) AS file_upload,
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
.attachment{display:flex;align-items:center;gap:10px;min-width:160px;}
.attachment img{width:72px;height:54px;object-fit:cover;border-radius:6px;border:1px solid #d1d5db;background:#f8fafc;}
.attachment a{color:#1d4f91;font-weight:700;text-decoration:none;}
.attachment a:hover{text-decoration:underline;}
.muted{color:#6b7280;font-size:13px;}
@media(max-width:768px){body{flex-direction:column;}.sidebar{width:100%;}.main{padding:20px;}}
</style>
</head>
<body>
<div class="sidebar">
<h2>Admin Panel</h2>
<p>Logged in as <?php echo htmlspecialchars($admin_name); ?></p>
<a href="dashboard_admin.php"><i class="fa fa-chart-line"></i> Dashboard</a>
<a href="manage_complaints.php" class="active"><i class="fa fa-arrow-up-right-dots"></i> Complaint Control</a>
<a href="student_module.php"><i class="fa fa-user-graduate"></i> Student Module</a>
<a href="staff_module.php"><i class="fa fa-user-pen"></i> Staff Module</a>
<a href="../index.php"><i class="fa fa-home"></i> Main Portal</a>
<a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
</div>
<div class="main">
<div class="header">
<h1>Complaint Control</h1>
<p>Route complaints by category and resolve them from the admin panel. Academic goes to HOD, Infrastructure goes to Principal, and Administration goes to Management.</p>
</div>
<?php if($message !== '') { ?><div class="message"><?php echo htmlspecialchars($message); ?></div><?php } ?>
<div class="table-wrap">
<table>
<tr><th>ID</th><th>Source</th><th>Submitted By</th><th>Dept</th><th>Category</th><th>Route</th><th>Description</th><th>Attachment</th><th>Status</th><th>Assigned To</th><th>Escalated To</th><th>Actions</th></tr>
<?php if($all_complaints && mysqli_num_rows($all_complaints) > 0) { ?>
<?php while($row = mysqli_fetch_assoc($all_complaints)) { ?>
<?php
$file_name = basename((string) ($row['file_upload'] ?? ''));
$file_path = $file_name !== '' ? '../uploads/' . rawurlencode($file_name) : '';
$server_file_path = $file_name !== '' ? __DIR__ . '/../uploads/' . $file_name : '';
$file_exists = $server_file_path !== '' && is_file($server_file_path);
$file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
$image_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'jfif'];
$is_image = $file_exists && in_array($file_ext, $image_exts, true);
?>
<tr>
<td><?php echo htmlspecialchars((string) ($row['complaint_id'] ?? '')); ?></td>
<td><span class="tag"><?php echo htmlspecialchars((string) ($row['source_label'] ?? '')); ?></span></td>
<td><?php echo htmlspecialchars((string) ($row['submitted_by'] ?? '')); ?></td>
<td><?php echo htmlspecialchars(department_name((int) ($row['department_no'] ?? 0))); ?></td>
<td><?php echo htmlspecialchars(category_name((int) ($row['category_id'] ?? 0))); ?></td>
<td><span class="tag"><?php echo htmlspecialchars(category_route((int) ($row['category_id'] ?? 0))); ?></span></td>
<td><?php echo htmlspecialchars((string) ($row['description'] ?? '')); ?></td>
<td>
<?php if($file_name !== '' && $file_exists) { ?>
<div class="attachment">
<?php if($is_image) { ?>
<a href="<?php echo htmlspecialchars($file_path); ?>" target="_blank">
<img src="<?php echo htmlspecialchars($file_path); ?>" alt="Complaint attachment">
</a>
<?php } ?>
<a href="<?php echo htmlspecialchars($file_path); ?>" target="_blank">View File</a>
</div>
<?php } elseif($file_name !== '') { ?>
<span class="muted">Missing file: <?php echo htmlspecialchars($file_name); ?></span>
<?php } else { ?>
<span class="muted">No file</span>
<?php } ?>
</td>
<td><span class="status <?php echo status_class((string) ($row['status'] ?? 'Pending')); ?>"><?php echo htmlspecialchars((string) ($row['status'] ?? '')); ?></span></td>
<td><?php echo htmlspecialchars((string) ($row['assigned_to'] ?? '')); ?></td>
<td><?php echo htmlspecialchars((string) ($row['escalated_to'] ?? 'Not Escalated')); ?></td>
<td>
<form method="post">
<input type="hidden" name="source_type" value="<?php echo htmlspecialchars((string) ($row['source_type'] ?? 'complaint')); ?>">
<input type="hidden" name="complaint_id" value="<?php echo htmlspecialchars((string) ($row['complaint_id'] ?? '0')); ?>">
<input type="hidden" name="category_id" value="<?php echo htmlspecialchars((string) ($row['category_id'] ?? '0')); ?>">
<textarea name="remarks" placeholder="Remarks or reason"><?php echo htmlspecialchars('Category route: ' . category_name((int) ($row['category_id'] ?? 0))); ?></textarea>
<select name="escalated_to">
<option value="">Choose role</option>
<option value="HOD">HOD</option>
<option value="Principal">Principal</option>
<option value="Management">Management</option>
</select>
<button type="submit" name="action_type" value="route_by_category">Route by Category</button>
<button type="submit" name="action_type" value="escalate">Escalate</button>
<button type="submit" name="action_type" value="progress">Mark In Progress</button>
<button type="submit" name="action_type" value="resolve">Resolve</button>
</form>
</td>
</tr>
<?php } ?>
<?php } else { ?>
<tr><td colspan="12">No complaints found.</td></tr>
<?php } ?>
</table>
</div>
</div>
</body>
</html>

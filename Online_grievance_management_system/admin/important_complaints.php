<?php
session_start();
include '../include/conn.php';
include '../include/complaint_helpers.php';

if(!isset($_SESSION['admin_id'])){
    header('Location: admin_login.php');
    exit();
}

$admin_name = $_SESSION['admin_name'] ?? 'Admin';

ensure_complaint_columns($conn);

$important_complaints = false;
$important_stmt = mysqli_prepare(
    $conn,
    "SELECT CONVERT('Student' USING utf8mb4) COLLATE utf8mb4_unicode_ci AS source_label,
            complaint_id,
            CONVERT(register_no USING utf8mb4) COLLATE utf8mb4_unicode_ci AS submitted_by,
            category_id,
            department_no,
            CONVERT(description USING utf8mb4) COLLATE utf8mb4_unicode_ci AS description,
            CONVERT(status USING utf8mb4) COLLATE utf8mb4_unicode_ci AS status,
            date_submitted
     FROM complaint
     WHERE is_important = 1
     UNION ALL
     SELECT CONVERT('Staff' USING utf8mb4) COLLATE utf8mb4_unicode_ci AS source_label,
            complaint_id,
            CONVERT(CAST(staff_id AS CHAR) USING utf8mb4) COLLATE utf8mb4_unicode_ci AS submitted_by,
            category_id,
            department_no,
            CONVERT(description USING utf8mb4) COLLATE utf8mb4_unicode_ci AS description,
            CONVERT(status USING utf8mb4) COLLATE utf8mb4_unicode_ci AS status,
            date_submitted
     FROM staff_complaint
     WHERE is_important = 1
     ORDER BY date_submitted DESC, complaint_id DESC"
);

if($important_stmt){
    mysqli_stmt_execute($important_stmt);
    $important_complaints = mysqli_stmt_get_result($important_stmt);
    mysqli_stmt_close($important_stmt);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Important Complaints</title>
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
background:linear-gradient(135deg,#4b1d95,#1d4f91);
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
margin-bottom:30px;
}

.header h1{
color:#1d3557;
}

table{
width:100%;
border-collapse:collapse;
background:#fff;
border-radius:12px;
overflow:hidden;
box-shadow:0 2px 10px rgba(0,0,0,0.08);
}

th{
background:#1d3557;
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

.important-row{
background:#fff1f2;
}

.important-row td{
border-bottom:1px solid #fecdd3;
}

.important-badge{
display:inline-flex;
align-items:center;
gap:6px;
padding:5px 10px;
border-radius:999px;
background:#dc2626;
color:#fff;
font-size:12px;
font-weight:700;
white-space:nowrap;
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
<link rel="stylesheet" href="../assets/css/theme.css">
</head>
<body>

<div class="sidebar">
<h2>Admin Panel</h2>
<p>Logged in as <?php echo htmlspecialchars($admin_name); ?></p>

<a href="dashboard_admin.php"><i class="fa fa-chart-line"></i> Dashboard</a>
<a href="manage_complaints.php"><i class="fa fa-arrow-up-right-dots"></i> Complaint Control</a>
<a href="student_module.php"><i class="fa fa-user-graduate"></i> Student Module</a>
<a href="staff_module.php"><i class="fa fa-user-pen"></i> Staff Module</a>
<a href="important_complaints.php" class="active"><i class="fa fa-triangle-exclamation"></i> Important Complaints</a>
<a href="../index.php"><i class="fa fa-home"></i> Main Portal</a>
<a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main">
<div class="header">
<h1><i class="fa fa-triangle-exclamation"></i> Important Complaints</h1>
<p>Complaints automatically marked important based on critical keywords.</p>
</div>

<table>
<tr>
<th>Complaint ID</th>
<th>Submitted By</th>
<th>Source</th>
<th>Department</th>
<th>Category</th>
<th>Complaint Description</th>
<th>Status</th>
<th>Date Submitted</th>
</tr>

<?php if($important_complaints && mysqli_num_rows($important_complaints) > 0) { ?>
<?php while($row = mysqli_fetch_assoc($important_complaints)) { ?>
<tr class="important-row">
<td><?php echo htmlspecialchars($row['complaint_id']); ?></td>
<td><?php echo htmlspecialchars($row['submitted_by']); ?></td>
<td><?php echo htmlspecialchars($row['source_label']); ?></td>
<td><?php echo htmlspecialchars(department_name((int) $row['department_no'])); ?></td>
<td><?php echo htmlspecialchars(category_name((int) $row['category_id'])); ?></td>
<td>
<span class="important-badge"><i class="fa fa-triangle-exclamation"></i> Important</span>
<br><br>
<?php echo htmlspecialchars($row['description']); ?>
</td>
<td>
<span class="status <?php echo status_class($row['status']); ?>">
<?php echo htmlspecialchars($row['status']); ?>
</span>
</td>
<td><?php echo htmlspecialchars($row['date_submitted']); ?></td>
</tr>
<?php } ?>
<?php } else { ?>
<tr>
<td colspan="8">No important complaints found.</td>
</tr>
<?php } ?>
</table>
</div>

<script src="../assets/js/theme.js"></script>
</body>
</html>

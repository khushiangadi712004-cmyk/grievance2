<?php
session_start();
include '../include/conn.php';
include '../include/complaint_helpers.php';

if(!isset($_SESSION['admin_id'])){
    header('Location: admin_login.php');
    exit();
}

$admin_name = $_SESSION['admin_name'] ?? 'Admin';

$total_complaints = fetch_count($conn, "SELECT COUNT(*) FROM complaint");
$pending_complaints = fetch_count($conn, "SELECT COUNT(*) FROM complaint WHERE status = 'Pending'");
$resolved_complaints = fetch_count($conn, "SELECT COUNT(*) FROM complaint WHERE status = 'Resolved'");
$total_students = fetch_count($conn, "SELECT COUNT(*) FROM student");
$total_staff = fetch_count($conn, "SELECT COUNT(*) FROM staff");

$recent_complaints = mysqli_query(
    $conn,
    "SELECT complaint_id, register_no, category_id, description, status
     FROM complaint
     ORDER BY complaint_id DESC
     LIMIT 10"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>
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

.sidebar a:hover{
background:rgba(255,255,255,0.2);
}

.main{
flex:1;
padding:30px;
}

.header{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:30px;
gap:15px;
flex-wrap:wrap;
}

.header h1{
color:#1d3557;
}

.cards{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
gap:20px;
margin-bottom:30px;
}

.card{
background:#fff;
padding:20px;
border-radius:12px;
box-shadow:0 2px 10px rgba(0,0,0,0.08);
}

.card h3{
color:#1d4f91;
margin-bottom:10px;
font-size:18px;
}

.card p{
font-size:30px;
font-weight:700;
color:#111827;
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
<h2>Admin Panel</h2>
<p>Logged in as <?php echo htmlspecialchars($admin_name); ?></p>

<a href="dashboard_admin.php"><i class="fa fa-chart-line"></i> Dashboard</a>
<a href="manage_complaints.php"><i class="fa fa-arrow-up-right-dots"></i> Complaint Control</a>
<a href="student_module.php"><i class="fa fa-user-graduate"></i> Student Module</a>
<a href="staff_module.php"><i class="fa fa-user-pen"></i> Staff Module</a>
<a href="../index.php"><i class="fa fa-home"></i> Main Portal</a>
<a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main">
<div class="header">
<div>
<h1>Welcome, <?php echo htmlspecialchars($admin_name); ?></h1>
<p>Monitor complaints and student activity from one place.</p>
</div>
</div>

<div class="cards">
<div class="card">
<h3>Total Complaints</h3>
<p><?php echo $total_complaints; ?></p>
</div>
<div class="card">
<h3>Pending</h3>
<p><?php echo $pending_complaints; ?></p>
</div>
<div class="card">
<h3>Resolved</h3>
<p><?php echo $resolved_complaints; ?></p>
</div>
<div class="card">
<h3>Total Students</h3>
<p><?php echo $total_students; ?></p>
</div>
<div class="card">
<h3>Total Staff</h3>
<p><?php echo $total_staff; ?></p>
</div>
</div>

<h2 style="margin-bottom:12px;">Recent Complaints</h2>

<table>
<tr>
<th>ID</th>
<th>Register No</th>
<th>Category</th>
<th>Description</th>
<th>Status</th>
</tr>

<?php if($recent_complaints && mysqli_num_rows($recent_complaints) > 0) { ?>
<?php while($row = mysqli_fetch_assoc($recent_complaints)) { ?>
<tr>
<td><?php echo htmlspecialchars($row['complaint_id']); ?></td>
<td><?php echo htmlspecialchars($row['register_no']); ?></td>
<td><?php echo htmlspecialchars(category_name((int) $row['category_id'])); ?></td>
<td><?php echo htmlspecialchars($row['description']); ?></td>
<td>
<span class="status <?php echo status_class($row['status']); ?>">
<?php echo htmlspecialchars($row['status']); ?>
</span>
</td>
</tr>
<?php } ?>
<?php } else { ?>
<tr>
<td colspan="5">No complaints found.</td>
</tr>
<?php } ?>
</table>
</div>

</body>
</html>


<?php
session_start();
include '../include/conn.php';
include '../include/complaint_helpers.php';

/* ✅ Check if user is logged in */
if(!isset($_SESSION['register_no'])){
    header("Location: student_login.php");
    exit();
}

$register_no = $_SESSION['register_no'];


/* Total complaints */
$total_query = "SELECT COUNT(*) as total FROM complaint WHERE register_no='$register_no'";
$total_result = mysqli_query($conn,$total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total = $total_row['total'];

/* Pending complaints */
$pending_query = "SELECT COUNT(*) as pending FROM complaint WHERE register_no='$register_no' AND status='Pending'";
$pending_result = mysqli_query($conn,$pending_query);
$pending_row = mysqli_fetch_assoc($pending_result);
$pending = $pending_row['pending'];

/* Resolved complaints */
$resolved_query = "SELECT COUNT(*) as resolved FROM complaint WHERE register_no='$register_no' AND status='Resolved'";
$resolved_result = mysqli_query($conn,$resolved_query);
$resolved_row = mysqli_fetch_assoc($resolved_result);
$resolved = $resolved_row['resolved'];

/* Fetch recent complaints */
$complaint_query = "SELECT * FROM complaint WHERE register_no='$register_no' ORDER BY complaint_id DESC LIMIT 5";
$complaints = mysqli_query($conn,$complaint_query);
?>


<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<title>User Dashboard</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:Arial;
}

/* Layout */

body{
display:flex;
background:#f4f6f9;
}

/* Sidebar */

.sidebar{
width:240px;
height:100vh;
background:linear-gradient(135deg,#0f3d56,#1f7a8c);
color:white;
padding:25px;
}

.sidebar h2{
margin-bottom:30px;
text-align:center;
}

.sidebar a{
display:block;
color:white;
text-decoration:none;
padding:12px;
margin:10px 0;
border-radius:6px;
transition:0.3s;
}

.sidebar a:hover{
background:rgba(255,255,255,0.2);
}

/* Main Section */

.main{
flex:1;
padding:30px;
}

/* Header */

.header{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:30px;
}

.header h1{
color:#0f3d56;
}

/* Cards */

.cards{
display:grid;
grid-template-columns:repeat(3,1fr);
gap:20px;
margin-bottom:30px;
}

.card{
background:white;
padding:20px;
border-radius:10px;
box-shadow:0 2px 10px rgba(0,0,0,0.1);
}

.card h3{
color:#1f7a8c;
margin-bottom:10px;
}

/* Table */

table{
width:100%;
border-collapse:collapse;
background:white;
border-radius:10px;
overflow:hidden;
box-shadow:0 2px 10px rgba(0,0,0,0.1);
text-align:center; /* center align */
}

table th{
background:#0f3d56;
color:white;
padding:12px;
text-align:center;
}

table td{
padding:12px;
border-bottom:1px solid #eee;
text-align:center;
vertical-align:middle;
}

.status{
padding:5px 10px;
border-radius:5px;
font-size:12px;
}

.pending{
background:#ffc107;
color:black;
}

.progress{
background:#17a2b8;
color:white;
}

.resolved{
background:#28a745;
color:white;
}

</style>

</head>

<body>
<!-- Sidebar -->

<div class="sidebar">

<h2>Grievance System</h2>

<a href="dashboard_user.php"><i class="fa fa-home"></i> Dashboard</a>
<a href="submit_com.php"><i class="fa fa-edit"></i> Submit Complaint</a>
<a href="dashboard_user.php"><i class="fa fa-list"></i> My Complaints</a>
<a href="profile.php"><i class="fa fa-user"></i> Profile</a>
<a href="logout.php"><i class="fa fa-sign-out"></i> Logout</a>

</div>

<!-- Main Content -->

<div class="main">

<div class="header">

<h1>Welcome, Student</h1>

</div>

<!-- Dashboard Cards -->

<div class="cards">

<div class="card">
<h3>Total Complaints</h3>
<p><?php echo $total; ?></p>
</div>

<div class="card">
<h3>Pending</h3>
<p><?php echo $pending; ?></p>
</div>

<div class="card">
<h3>Resolved</h3>
<p><?php echo $resolved; ?></p>
</div>

</div>

<!-- Complaint Table -->

<h2 style="margin-bottom:10px;">Recent Complaints</h2>

<table>

<tr>
<th>ID</th>
<th>Category</th>
<th>Description</th>
<th>Assigned To</th>
<th>Status</th>
</tr>
<?php while($row = mysqli_fetch_assoc($complaints)) { ?>

<tr>

<td><?php echo $row['complaint_id']; ?></td>

<td><?php echo htmlspecialchars(category_name((int) $row['category_id'])); ?></td>

<td><?php echo htmlspecialchars($row['description']); ?></td>

<td><?php echo htmlspecialchars((string) ($row['assigned_to'] ?? category_route((int) $row['category_id']))); ?></td>

<td>
<span class="status 
<?php 
if($row['status']=='Pending') echo 'pending';
elseif($row['status']=='In Progress') echo 'progress';
else echo 'resolved';
?>">
<?php echo $row['status']; ?>
</span>
</td>

</tr>

<?php } ?>

</table>

</div>

</body>
</html>

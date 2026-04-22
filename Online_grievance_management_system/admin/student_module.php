<?php
session_start();
include '../include/conn.php';

if(!isset($_SESSION['admin_id'])){
    header('Location: admin_login.php');
    exit();
}

$admin_name = $_SESSION['admin_name'] ?? 'Admin';

$students = mysqli_query(
    $conn,
    "SELECT s.register_no, s.sname, s.email, s.phone,
            COUNT(c.complaint_id) AS total_complaints,
            SUM(CASE WHEN c.status = 'Pending' THEN 1 ELSE 0 END) AS pending_complaints,
            SUM(CASE WHEN c.status = 'Resolved' THEN 1 ELSE 0 END) AS resolved_complaints
     FROM student s
     LEFT JOIN complaint c ON c.register_no = s.register_no
     GROUP BY s.register_no, s.sname, s.email, s.phone
     ORDER BY s.sname ASC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Module</title>
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
margin-bottom:24px;
}

.header h1{
color:#1d3557;
margin-bottom:8px;
}

.table-wrap{
background:#fff;
border-radius:12px;
overflow:auto;
box-shadow:0 2px 10px rgba(0,0,0,0.08);
}

table{
width:100%;
border-collapse:collapse;
min-width:900px;
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
}

.count{
font-weight:700;
color:#1d4f91;
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
<a href="student_module.php" class="active"><i class="fa fa-user-graduate"></i> Student Module</a>
<a href="staff_module.php"><i class="fa fa-user-pen"></i> Staff Module</a>
<a href="../index.php"><i class="fa fa-home"></i> Main Portal</a>
<a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main">
<div class="header">
<h1>Student Module</h1>
<p>View all students and their complaint activity from the admin panel.</p>
</div>

<div class="table-wrap">
<table>
<tr>
<th>Register No</th>
<th>Name</th>
<th>Email</th>
<th>Phone</th>
<th>Total Complaints</th>
<th>Pending</th>
<th>Resolved</th>
</tr>

<?php if($students && mysqli_num_rows($students) > 0) { ?>
<?php while($row = mysqli_fetch_assoc($students)) { ?>
<tr>
<td><?php echo htmlspecialchars($row['register_no']); ?></td>
<td><?php echo htmlspecialchars($row['sname']); ?></td>
<td><?php echo htmlspecialchars($row['email']); ?></td>
<td><?php echo htmlspecialchars($row['phone']); ?></td>
<td class="count"><?php echo (int) ($row['total_complaints'] ?? 0); ?></td>
<td class="count"><?php echo (int) ($row['pending_complaints'] ?? 0); ?></td>
<td class="count"><?php echo (int) ($row['resolved_complaints'] ?? 0); ?></td>
</tr>
<?php } ?>
<?php } else { ?>
<tr>
<td colspan="7">No student records found.</td>
</tr>
<?php } ?>
</table>
</div>
</div>
</body>
</html>

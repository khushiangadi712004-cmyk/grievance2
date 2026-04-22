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
    $staff_id = (int) ($_POST['staff_id'] ?? 0);
    $stname = trim($_POST['stname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $department_no = (int) ($_POST['department_no'] ?? 0);
    $phone_no = trim($_POST['phone_no'] ?? '');
    $design = trim($_POST['design'] ?? '');

    if($staff_id > 0 && $stname !== '' && $email !== ''){
        $stmt = mysqli_prepare(
            $conn,
            "UPDATE staff
             SET stname = ?, email = ?, department_no = ?, phone_no = ?, design = ?
             WHERE staff_id = ?"
        );

        if($stmt){
            mysqli_stmt_bind_param($stmt, 'ssissi', $stname, $email, $department_no, $phone_no, $design, $staff_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            $message = 'Staff profile updated successfully.';
        } else {
            $message = 'Unable to update staff profile.';
        }
    } else {
        $message = 'Enter valid staff details before saving.';
    }
}

$staffMembers = mysqli_query(
    $conn,
    "SELECT s.staff_id, s.stname, s.email, s.department_no, s.phone_no, s.design,
            COUNT(sc.complaint_id) AS total_complaints
     FROM staff s
     LEFT JOIN staff_complaint sc ON sc.staff_id = s.staff_id
     GROUP BY s.staff_id, s.stname, s.email, s.department_no, s.phone_no, s.design
     ORDER BY s.stname ASC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Staff Module</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:Arial,sans-serif;}
body{display:flex;background:#f4f6f9;min-height:100vh;}
.sidebar{width:250px;background:linear-gradient(135deg,#4b1d95,#1d4f91);color:#fff;padding:25px;}
.sidebar h2{margin-bottom:10px;}
.sidebar p{font-size:14px;opacity:0.85;margin-bottom:25px;}
.sidebar a{display:block;color:#fff;text-decoration:none;padding:12px;margin:10px 0;border-radius:6px;transition:0.3s;}
.sidebar a:hover,.sidebar a.active{background:rgba(255,255,255,0.2);}
.main{flex:1;padding:30px;}
.header{margin-bottom:24px;}
.header h1{color:#1d3557;margin-bottom:8px;}
.message{margin-bottom:18px;padding:12px;border-radius:8px;background:#dcfce7;color:#166534;}
.table-wrap{background:#fff;border-radius:12px;overflow:auto;box-shadow:0 2px 10px rgba(0,0,0,0.08);}
table{width:100%;border-collapse:collapse;min-width:1350px;}
th{background:#1d3557;color:#fff;padding:12px;text-align:left;}
td{padding:12px;border-bottom:1px solid #e5e7eb;vertical-align:top;}
input,select{width:100%;padding:10px;border:1px solid #d1d5db;border-radius:8px;}
button{padding:10px 14px;border:none;border-radius:8px;background:#1d4f91;color:#fff;cursor:pointer;}
.count{font-weight:700;color:#1d4f91;}
@media(max-width:768px){body{flex-direction:column;}.sidebar{width:100%;}.main{padding:20px;}}
</style>
</head>
<body>
<div class="sidebar">
<h2>Admin Panel</h2>
<p>Logged in as <?php echo htmlspecialchars($admin_name); ?></p>
<a href="dashboard_admin.php"><i class="fa fa-chart-line"></i> Dashboard</a>
<a href="manage_complaints.php"><i class="fa fa-arrow-up-right-dots"></i> Complaint Control</a>
<a href="student_module.php"><i class="fa fa-user-graduate"></i> Student Module</a>
<a href="staff_module.php" class="active"><i class="fa fa-user-pen"></i> Staff Module</a>
<a href="../index.php"><i class="fa fa-home"></i> Main Portal</a>
<a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main">
<div class="header">
<h1>Staff Module</h1>
<p>Edit staff profile details directly from the admin page.</p>
</div>

<?php if($message !== '') { ?>
<div class="message"><?php echo htmlspecialchars($message); ?></div>
<?php } ?>

<div class="table-wrap">
<table>
<tr>
<th>Staff ID</th>
<th>Name</th>
<th>Email</th>
<th>Department</th>
<th>Phone</th>
<th>Designation</th>
<th>Total Complaints</th>
<th>Save</th>
</tr>
<?php if($staffMembers && mysqli_num_rows($staffMembers) > 0) { ?>
<?php while($row = mysqli_fetch_assoc($staffMembers)) { ?>
<tr>
<form method="post">
<td>
<?php echo htmlspecialchars((string) ($row['staff_id'] ?? '')); ?>
<input type="hidden" name="staff_id" value="<?php echo htmlspecialchars((string) ($row['staff_id'] ?? '')); ?>">
</td>
<td><input type="text" name="stname" value="<?php echo htmlspecialchars((string) ($row['stname'] ?? '')); ?>" required></td>
<td><input type="email" name="email" value="<?php echo htmlspecialchars((string) ($row['email'] ?? '')); ?>" required></td>
<td>
<select name="department_no" required>
<option value="1" <?php echo ((int) ($row['department_no'] ?? 0) === 1 ? 'selected' : ''); ?>>BCA</option>
<option value="2" <?php echo ((int) ($row['department_no'] ?? 0) === 2 ? 'selected' : ''); ?>>BSC</option>
<option value="3" <?php echo ((int) ($row['department_no'] ?? 0) === 3 ? 'selected' : ''); ?>>B.COM</option>
<option value="4" <?php echo ((int) ($row['department_no'] ?? 0) === 4 ? 'selected' : ''); ?>>BBA</option>
</select>
</td>
<td><input type="text" name="phone_no" value="<?php echo htmlspecialchars((string) ($row['phone_no'] ?? '')); ?>"></td>
<td><input type="text" name="design" value="<?php echo htmlspecialchars((string) ($row['design'] ?? '')); ?>" placeholder="Designation"></td>
<td class="count"><?php echo (int) ($row['total_complaints'] ?? 0); ?></td>
<td><button type="submit">Update</button></td>
</form>
</tr>
<?php } ?>
<?php } else { ?>
<tr><td colspan="8">No staff records found.</td></tr>
<?php } ?>
</table>
</div>
</div>
</body>
</html>

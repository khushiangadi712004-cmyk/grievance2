<?php
$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($basePath === '') {
  $basePath = '.';
}

$roles = [
  'student' => [
    'label' => 'Student',
    'login_label' => 'Student Login',
    'path' => '/user/student_login.php',
    'icon' => 'fa-user-graduate',
    'button_class' => 'primary',
    'color' => '#0f6c8d',
    'color_dark' => '#0f3d56',
  ],
  'staff' => [
    'label' => 'Staff',
    'login_label' => 'Staff Login',
    'path' => '/staff/staff_login.php',
    'icon' => 'fa-users',
    'button_class' => 'secondary',
    'color' => '#1d4f91',
    'color_dark' => '#1d3557',
  ],
  'hod' => [
    'label' => 'HOD',
    'login_label' => 'HOD Login',
    'path' => '/hod/hod_login.php',
    'icon' => 'fa-building-columns',
    'button_class' => 'secondary',
    'color' => '#8e44ad',
    'color_dark' => '#5b2c6f',
  ],
  'principal' => [
    'label' => 'Principal',
    'login_label' => 'Principal Login',
    'path' => '/principal/principal_login.php',
    'icon' => 'fa-user-tie',
    'button_class' => 'secondary',
    'color' => '#b45309',
    'color_dark' => '#7c2d12',
  ],
  'management' => [
    'label' => 'Management',
    'login_label' => 'Management Login',
    'path' => '/view/management_login.php',
    'icon' => 'fa-briefcase',
    'button_class' => 'secondary',
    'color' => '#15803d',
    'color_dark' => '#14532d',
  ],
  'admin' => [
    'label' => 'Admin',
    'login_label' => 'Admin Login',
    'path' => '/admin/admin_login.php',
    'icon' => 'fa-user-shield',
    'button_class' => 'dark-btn',
    'color' => '#4b1d95',
    'color_dark' => '#1d4f91',
  ],
];

function role_url($basePath, $role) {
  return htmlspecialchars($basePath . $role['path']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>GrievanceDesk | Online Grievance Management System</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
:root{
  --bg:#eef4fb;
  --surface:#ffffff;
  --surface-soft:#f8fafc;
  --text:#102033;
  --muted:#5e7188;
  --primary:#2563eb;
  --primary-dark:#1e3a8a;
  --accent:#06b6d4;
  --purple:#7c3aed;
  --teal:#0891b2;
  --border:#dbe5ee;
  --shadow:0 18px 45px rgba(16,32,51,0.12);
}

body.dark{
  --bg:#0d1624;
  --surface:#121e2e;
  --surface-soft:#172538;
  --text:#edf6ff;
  --muted:#aab8c8;
  --primary:#60a5fa;
  --primary-dark:#1d4ed8;
  --accent:#2dd4bf;
  --purple:#a78bfa;
  --teal:#22d3ee;
  --border:#27384d;
  --shadow:0 18px 45px rgba(0,0,0,0.35);
}

*{box-sizing:border-box;margin:0;padding:0;}
html{scroll-behavior:smooth;}
body{
  font-family:Arial, sans-serif;
  background:
    linear-gradient(135deg,rgba(37,99,235,0.12),rgba(6,182,212,0.10) 38%,rgba(124,58,237,0.08)),
    var(--bg);
  color:var(--text);
  min-height:100vh;
}
body.dark{background:linear-gradient(135deg,#08111e 0%,#101c2c 52%,#15112d 100%);}
a{text-decoration:none;color:inherit;}

.nav{
  position:sticky;
  top:0;
  z-index:10;
  background:rgba(255,255,255,0.86);
  border-bottom:1px solid var(--border);
  backdrop-filter:blur(14px);
}
body.dark .nav{background:rgba(18,30,46,0.86);}
.nav-inner{
  max-width:1180px;
  margin:auto;
  padding:16px 22px;
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:18px;
}
.brand{display:flex;align-items:center;gap:10px;font-weight:800;font-size:18px;}
.brand i{
  color:white;
  background:linear-gradient(135deg,var(--primary),var(--purple));
  width:34px;
  height:34px;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  border-radius:10px;
}
.nav-links{display:flex;align-items:center;gap:18px;color:var(--muted);font-weight:700;font-size:14px;}
.nav-links a:hover{color:var(--primary);}
.theme-toggle{
  border:1px solid var(--border);
  background:var(--surface);
  color:var(--text);
  padding:10px 12px;
  border-radius:999px;
  cursor:pointer;
  box-shadow:0 8px 18px rgba(0,0,0,0.06);
}

.hero{
  max-width:1180px;
  margin:auto;
  padding:64px 22px 34px;
  display:grid;
  grid-template-columns:1.1fr 0.9fr;
  gap:34px;
  align-items:center;
}
.hero-shell{
  background:
    radial-gradient(circle at 12% 20%, rgba(255,255,255,0.20), transparent 28%),
    linear-gradient(135deg,#0f3d91 0%,#2563eb 42%,#06b6d4 100%);
  color:white;
  border-radius:28px;
  box-shadow:0 28px 70px rgba(37,99,235,0.24);
  margin:26px auto 0;
  max-width:1228px;
  overflow:hidden;
}
.hero-shell .hero{padding:64px 24px;}
.hero-shell .hero h1,
.hero-shell .hero p{color:white;}
.hero-shell .hero p{color:rgba(255,255,255,0.86);}
.hero-shell .eyebrow{
  color:white;
  background:rgba(255,255,255,0.16);
  border-color:rgba(255,255,255,0.25);
}
.eyebrow{
  display:inline-flex;
  align-items:center;
  gap:8px;
  color:var(--primary);
  background:linear-gradient(135deg,rgba(37,99,235,0.12),rgba(20,184,166,0.12));
  border:1px solid rgba(37,99,235,0.18);
  padding:8px 12px;
  border-radius:999px;
  font-size:13px;
  font-weight:800;
  margin-bottom:18px;
}
.hero h1{
  font-size:54px;
  line-height:1.06;
  letter-spacing:0;
  color:var(--text);
  margin-bottom:18px;
}
.hero h1 span{
  display:block;
  color:#dff8ff;
  font-size:0.62em;
  margin-bottom:8px;
}
.hero p{color:var(--muted);font-size:18px;line-height:1.7;max-width:660px;}
.hero-actions{display:flex;flex-wrap:wrap;gap:12px;margin:28px 0;}
.btn{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  gap:9px;
  border-radius:10px;
  padding:13px 18px;
  font-weight:800;
  border:1px solid transparent;
  cursor:pointer;
  transition:0.22s ease;
}
.btn.primary{background:linear-gradient(135deg,var(--primary),var(--teal));color:white;box-shadow:0 12px 24px rgba(37,99,235,0.28);}
.btn.secondary{background:var(--surface);color:var(--primary);border-color:var(--border);}
.btn.dark-btn{background:linear-gradient(135deg,#172033,var(--purple));color:white;}
.btn:hover{transform:translateY(-2px);}

.hero-panel{
  background:rgba(255,255,255,0.94);
  border:1px solid rgba(255,255,255,0.45);
  border-radius:18px;
  box-shadow:var(--shadow);
  overflow:hidden;
}
body.dark .hero-panel{background:rgba(18,30,46,0.94);}
.panel-head{
  background:linear-gradient(135deg,var(--primary-dark),var(--primary),var(--accent));
  color:white;
  padding:22px;
}
.panel-head h2{font-size:22px;margin-bottom:8px;}
.panel-head p{color:rgba(255,255,255,0.86);font-size:14px;line-height:1.5;}
.role-grid{padding:18px;display:grid;gap:12px;}
.role-link{
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:12px;
  padding:14px;
  border:1px solid var(--border);
  border-radius:12px;
  background:var(--surface-soft);
  transition:0.22s ease;
  position:relative;
  overflow:hidden;
}
.role-link::before{
  content:'';
  position:absolute;
  left:0;
  top:0;
  bottom:0;
  width:6px;
  background:linear-gradient(180deg,var(--role-dark),var(--role-color));
}
.role-link span{display:flex;align-items:center;gap:10px;font-weight:800;}
.role-link span i{
  color:white;
  width:34px;
  height:34px;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  border-radius:10px;
  background:linear-gradient(135deg,var(--role-dark),var(--role-color));
}
.role-link > i{color:var(--role-color);}
.role-link:hover{border-color:var(--role-color);transform:translateX(4px);background:var(--surface);box-shadow:0 12px 24px rgba(16,32,51,0.10);}

.section{max-width:1180px;margin:auto;padding:42px 22px;}
.section-title{display:flex;align-items:end;justify-content:space-between;gap:18px;margin-bottom:22px;}
.section-title h2{font-size:32px;color:var(--text);}
.section-title p{color:var(--muted);max-width:560px;line-height:1.6;}
.features{display:grid;grid-template-columns:repeat(3,1fr);gap:18px;}
.card{
  background:var(--surface);
  border:1px solid var(--border);
  border-radius:16px;
  padding:22px;
  box-shadow:0 12px 30px rgba(16,32,51,0.08);
  transition:0.22s ease;
  position:relative;
  overflow:hidden;
}
.card::before{
  content:'';
  position:absolute;
  inset:0 0 auto 0;
  height:5px;
  background:linear-gradient(90deg,var(--primary),var(--accent),var(--purple));
}
.card:hover{transform:translateY(-4px);box-shadow:var(--shadow);}
.card i{
  font-size:24px;
  color:white;
  width:48px;
  height:48px;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  border-radius:14px;
  background:linear-gradient(135deg,var(--primary),var(--purple));
  margin-bottom:14px;
}
.card:nth-child(2) i,.card:nth-child(5) i{background:linear-gradient(135deg,var(--teal),var(--primary));}
.card:nth-child(3) i,.card:nth-child(6) i{background:linear-gradient(135deg,var(--purple),#ec4899);}
.card h3{font-size:18px;margin-bottom:9px;}
.card p{color:var(--muted);line-height:1.6;font-size:14px;}

.steps{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;}
.step{position:relative;background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:22px;box-shadow:0 10px 24px rgba(16,32,51,0.07);}
.step strong{display:inline-flex;width:34px;height:34px;align-items:center;justify-content:center;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--accent));color:white;margin-bottom:14px;}
.step h3{font-size:17px;margin-bottom:8px;}
.step p{color:var(--muted);font-size:14px;line-height:1.55;}

.footer{
  margin-top:34px;
  background:linear-gradient(135deg,var(--primary-dark),#312e81);
  color:white;
}
.footer-inner{
  max-width:1180px;
  margin:auto;
  padding:26px 22px;
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:18px;
  flex-wrap:wrap;
}
.footer a{color:rgba(255,255,255,0.82);margin-left:16px;font-weight:700;font-size:14px;}
.footer a:hover{color:white;}

@media(max-width:900px){
  .hero{grid-template-columns:1fr;padding-top:42px;}
  .hero h1{font-size:40px;}
  .features{grid-template-columns:repeat(2,1fr);}
  .steps{grid-template-columns:repeat(2,1fr);}
}
@media(max-width:640px){
  .nav-inner{align-items:flex-start;flex-direction:column;}
  .nav-links{width:100%;overflow:auto;padding-bottom:4px;}
  .hero h1{font-size:32px;}
  .hero-actions{flex-direction:column;}
  .btn{width:100%;}
  .features,.steps{grid-template-columns:1fr;}
  .section-title{display:block;}
  .section-title p{margin-top:8px;}
}
</style>
</head>
<body>

<nav class="nav">
  <div class="nav-inner">
    <a class="brand" href="#home"><i class="fa-solid fa-shield-halved"></i> GrievanceDesk</a>
    <div class="nav-links">
      <a href="#features">Features</a>
      <a href="#how">How It Works</a>
      <?php foreach (['student', 'staff'] as $roleKey) { ?>
      <a href="<?php echo role_url($basePath, $roles[$roleKey]); ?>"><?php echo htmlspecialchars($roles[$roleKey]['label']); ?></a>
      <?php } ?>
      <button class="theme-toggle" id="themeToggle" type="button"><i class="fa-solid fa-moon"></i></button>
    </div>
  </div>
</nav>

<main id="home">
<div class="hero-shell">
  <section class="hero">
    <div>
      <div class="eyebrow"><i class="fa-solid fa-building-shield"></i> Institutional complaint resolution portal</div>
      <h1><span>GrievanceDesk</span>Online Grievance Management System</h1>
      <p>A transparent platform for submitting, tracking, and resolving grievances efficiently.</p>
      <div class="hero-actions">
        <?php foreach (['student', 'staff', 'admin'] as $roleKey) { ?>
        <a class="btn <?php echo htmlspecialchars($roles[$roleKey]['button_class']); ?>" href="<?php echo role_url($basePath, $roles[$roleKey]); ?>"><i class="fa-solid <?php echo htmlspecialchars($roles[$roleKey]['icon']); ?>"></i> <?php echo htmlspecialchars($roles[$roleKey]['login_label']); ?></a>
        <?php } ?>
      </div>
    </div>

    <aside class="hero-panel">
      <div class="panel-head">
        <h2>Access Your Portal</h2>
        <p>Choose your role to continue with complaint submission, review, escalation, and resolution.</p>
      </div>
      <div class="role-grid">
        <?php foreach ($roles as $role) { ?>
        <a class="role-link" style="--role-color:<?php echo htmlspecialchars($role['color']); ?>;--role-dark:<?php echo htmlspecialchars($role['color_dark']); ?>;" href="<?php echo role_url($basePath, $role); ?>"><span><i class="fa-solid <?php echo htmlspecialchars($role['icon']); ?>"></i> <?php echo htmlspecialchars($role['login_label']); ?></span><i class="fa-solid fa-arrow-right"></i></a>
        <?php } ?>
      </div>
    </aside>
  </section>
</div>

  <section class="section" id="features">
    <div class="section-title">
      <h2>Built For Organized Resolution</h2>
      <p>A clean workflow that helps every complaint reach the right authority and stay visible until it is resolved.</p>
    </div>
    <div class="features">
      <div class="card"><i class="fa-solid fa-pen-to-square"></i><h3>Submit Complaint Online</h3><p>Students and staff can submit complaints with descriptions and supported image uploads.</p></div>
      <div class="card"><i class="fa-solid fa-chart-line"></i><h3>Track Complaint Status</h3><p>Users can monitor pending, in progress, and resolved complaints from their dashboard.</p></div>
      <div class="card"><i class="fa-solid fa-route"></i><h3>Role-based Complaint Routing</h3><p>Complaints are routed by category and department to the responsible authority.</p></div>
      <div class="card"><i class="fa-solid fa-lock"></i><h3>Secure Login System</h3><p>Separate login areas keep student, staff, admin, HOD, principal, and management access organized.</p></div>
      <div class="card"><i class="fa-solid fa-clock"></i><h3>Faster Resolution</h3><p>Authorities can mark complaints in progress, add remarks, and resolve issues faster.</p></div>
      <div class="card"><i class="fa-solid fa-desktop"></i><h3>Admin Monitoring</h3><p>Admin can monitor complaint activity, staff details, student data, and escalation flow.</p></div>
    </div>
  </section>

  <section class="section" id="how">
    <div class="section-title">
      <h2>How It Works</h2>
      <p>A simple process for submitting and resolving grievances with full accountability.</p>
    </div>
    <div class="steps">
      <div class="step"><strong>1</strong><h3>Login/Register</h3><p>Students register or login, while staff and authorities use their assigned accounts.</p></div>
      <div class="step"><strong>2</strong><h3>Submit Grievance</h3><p>Choose a category, describe the issue, and upload an image when required.</p></div>
      <div class="step"><strong>3</strong><h3>Authority Reviews</h3><p>The complaint reaches HOD, Principal, or Management based on the category.</p></div>
      <div class="step"><strong>4</strong><h3>Status Updated</h3><p>Status and remarks keep the complaint transparent until final resolution.</p></div>
    </div>
  </section>
</main>

<footer class="footer">
  <div class="footer-inner">
    <div><strong>Online Grievance Management System</strong><br><span>&copy; <?php echo date('Y'); ?> Transparent, fast, and organized complaint resolution.</span></div>
    <div>
      <?php foreach (['student', 'staff', 'admin'] as $roleKey) { ?>
      <a href="<?php echo role_url($basePath, $roles[$roleKey]); ?>"><?php echo htmlspecialchars($roles[$roleKey]['login_label']); ?></a>
      <?php } ?>
    </div>
  </div>
</footer>

<script>
const themeToggle = document.getElementById('themeToggle');
const savedTheme = localStorage.getItem('gms-theme');
if (savedTheme === 'dark') {
  document.body.classList.add('dark');
  themeToggle.innerHTML = '<i class="fa-solid fa-sun"></i>';
}
themeToggle.addEventListener('click', () => {
  document.body.classList.toggle('dark');
  const isDark = document.body.classList.contains('dark');
  localStorage.setItem('gms-theme', isDark ? 'dark' : 'light');
  themeToggle.innerHTML = isDark ? '<i class="fa-solid fa-sun"></i>' : '<i class="fa-solid fa-moon"></i>';
});
</script>
</body>
</html>

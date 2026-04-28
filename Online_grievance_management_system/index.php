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
    'description' => 'Submit grievances, upload details, and track complaint progress online.',
    'color' => '#1d4ed8',
    'color_light' => '#dbeafe',
  ],
  'staff' => [
    'label' => 'Staff',
    'login_label' => 'Staff Login',
    'path' => '/staff/staff_login.php',
    'icon' => 'fa-users',
    'description' => 'Review assigned complaints and support faster departmental resolution.',
    'color' => '#0e7490',
    'color_light' => '#cffafe',
  ],
  'hod' => [
    'label' => 'HOD',
    'login_label' => 'HOD Login',
    'path' => '/hod/hod_login.php',
    'icon' => 'fa-building-columns',
    'description' => 'Monitor department-level grievances and take required action quickly.',
    'color' => '#7c3aed',
    'color_light' => '#ede9fe',
  ],
  'principal' => [
    'label' => 'Principal',
    'login_label' => 'Principal Login',
    'path' => '/principal/principal_login.php',
    'icon' => 'fa-user-tie',
    'description' => 'View escalated complaints and guide important institutional decisions.',
    'color' => '#b45309',
    'color_light' => '#fef3c7',
  ],
  'management' => [
    'label' => 'Management',
    'login_label' => 'Management Login',
    'path' => '/view/management_login.php',
    'icon' => 'fa-briefcase',
    'description' => 'Track overall complaint flow and improve campus service quality.',
    'color' => '#047857',
    'color_light' => '#d1fae5',
  ],
  'admin' => [
    'label' => 'Admin',
    'login_label' => 'Admin Login',
    'path' => '/admin/admin_login.php',
    'icon' => 'fa-user-shield',
    'description' => 'Manage users, records, complaints, and complete portal monitoring.',
    'color' => '#1e3a8a',
    'color_light' => '#e0e7ff',
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
<title>Online Grievance Management System</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

:root {
  --text: #111827;
  --muted: #64748b;
  --white: #ffffff;
  --soft: #f8fafc;
  --line: #e2e8f0;
  --border: rgba(15, 23, 42, 0.09);
  --shadow: 0 22px 55px rgba(15, 23, 42, 0.13);
  --navy: #0f172a;
  --blue: #1d4ed8;
  --cyan: #0e7490;
  --teal: #047857;
  --gold: #b45309;
}

html {
  scroll-behavior: smooth;
}

body {
  font-family: Arial, Helvetica, sans-serif;
  color: var(--text);
  background: #eef4fb;
  min-height: 100vh;
}

a {
  color: inherit;
  text-decoration: none;
}

.page-bg {
  min-height: 100vh;
  background:
    linear-gradient(135deg, rgba(15,23,42,0.93) 0%, rgba(30,58,138,0.84) 42%, rgba(14,116,144,0.78) 100%),
    url('https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&w=1800&q=80');
  background-size: cover;
  background-position: center;
  background-attachment: fixed;
  padding-bottom: 54px;
  position: relative;
  overflow: hidden;
}

.page-bg:before {
  content: "";
  position: absolute;
  inset: 0;
  background:
    radial-gradient(circle at 16% 12%, rgba(255,255,255,0.16), transparent 28%),
    radial-gradient(circle at 86% 18%, rgba(20,184,166,0.18), transparent 30%),
    linear-gradient(180deg, rgba(15,23,42,0.08), rgba(15,23,42,0.42));
  pointer-events: none;
}

.navbar,
.hero,
.content,
.footer {
  position: relative;
  z-index: 1;
}

.navbar {
  max-width: 1180px;
  margin: 0 auto;
  padding: 18px 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 18px;
}

.navbar:after {
  content: "";
  position: fixed;
  left: 0;
  right: 0;
  top: 74px;
  height: 1px;
  background: rgba(255,255,255,0.14);
  pointer-events: none;
}

.brand {
  display: flex;
  align-items: center;
  gap: 12px;
  color: var(--white);
  font-size: 19px;
  font-weight: 800;
}

.brand-icon {
  width: 42px;
  height: 42px;
  border-radius: 12px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: rgba(255,255,255,0.18);
  border: 1px solid rgba(255,255,255,0.28);
}

.nav-links {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}

.nav-links a {
  color: rgba(255,255,255,0.90);
  font-size: 14px;
  font-weight: 700;
  padding: 10px 13px;
  border-radius: 999px;
  transition: 0.2s ease;
}

.nav-links a:hover {
  background: rgba(255,255,255,0.16);
  color: #ffffff;
}

.hero {
  max-width: 1180px;
  margin: 0 auto;
  padding: 58px 20px 44px;
  display: block;
  text-align: center;
}

.hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  color: #ffffff;
  background: rgba(255,255,255,0.16);
  border: 1px solid rgba(255,255,255,0.28);
  border-radius: 999px;
  padding: 9px 14px;
  font-size: 14px;
  font-weight: 800;
  margin-bottom: 22px;
  box-shadow: inset 0 1px 0 rgba(255,255,255,0.18);
}

.hero h1 {
  color: #ffffff;
  font-size: 60px;
  line-height: 1.04;
  letter-spacing: 0;
  margin: 0 auto 20px;
  max-width: 760px;
  text-shadow: 0 12px 30px rgba(15,23,42,0.32);
}

.hero p {
  max-width: 680px;
  color: rgba(255,255,255,0.88);
  font-size: 18px;
  line-height: 1.75;
  margin-bottom: 30px;
  margin-left: auto;
  margin-right: auto;
}

.hero-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  justify-content: center;
}

.hero-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  color: #123c8c;
  background: #ffffff;
  padding: 14px 20px;
  border-radius: 10px;
  font-weight: 800;
  box-shadow: 0 12px 28px rgba(15, 23, 42, 0.18);
  transition: 0.22s ease;
}

.hero-btn.alt {
  color: #ffffff;
  background: rgba(255,255,255,0.15);
  border: 1px solid rgba(255,255,255,0.35);
  box-shadow: none;
}

.hero-btn:hover {
  transform: translateY(-3px);
}

.hero-panel {
  background: linear-gradient(135deg, rgba(15,23,42,0.18), rgba(255,255,255,0.18));
  border: 1px solid rgba(255,255,255,0.34);
  border-radius: 18px;
  padding: 28px;
  box-shadow: 0 22px 55px rgba(15, 23, 42, 0.18);
  position: relative;
  overflow: hidden;
  backdrop-filter: blur(18px);
}

.workflow-section {
  margin-top: 28px;
  background: rgba(15, 23, 42, 0.18);
}

.hero-panel:before {
  content: "";
  position: absolute;
  inset: 0 0 auto 0;
  height: 5px;
  background: linear-gradient(90deg, var(--blue), var(--cyan), var(--teal), var(--gold));
}

.hero-panel h2 {
  font-size: 25px;
  margin-bottom: 12px;
  color: #ffffff;
}

.stats {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
  margin-top: 24px;
}

.stat {
  background: rgba(255,255,255,0.92);
  border: 1px solid rgba(255,255,255,0.64);
  border-radius: 12px;
  padding: 18px;
  box-shadow: 0 12px 26px rgba(15, 23, 42, 0.10);
}

.stat i {
  color: var(--blue);
  font-size: 23px;
  margin-bottom: 12px;
}

.stat strong {
  display: block;
  font-size: 25px;
  margin-bottom: 4px;
}

.stat span {
  color: var(--muted);
  font-size: 14px;
  line-height: 1.4;
}

.content {
  max-width: 1180px;
  margin: 0 auto;
  padding: 0 20px;
}

.section {
  background: rgba(15, 23, 42, 0.24);
  border: 1px solid rgba(255,255,255,0.22);
  border-radius: 18px;
  box-shadow: 0 24px 60px rgba(2, 6, 23, 0.24);
  padding: 36px;
  margin-bottom: 28px;
  backdrop-filter: blur(16px);
}

.section-head {
  display: flex;
  align-items: end;
  justify-content: space-between;
  gap: 24px;
  margin-bottom: 26px;
  padding-bottom: 18px;
  border-bottom: 1px solid rgba(255,255,255,0.28);
}

.section-head h2 {
  font-size: 31px;
  margin-bottom: 8px;
  color: #ffffff;
}

.section-head p {
  color: rgba(255,255,255,0.82);
  line-height: 1.6;
  max-width: 560px;
}

.module-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
}

.module-card {
  position: relative;
  overflow: hidden;
  min-height: 280px;
  background:
    linear-gradient(180deg, var(--card-light), rgba(255,255,255,0.92) 46%, rgba(255,255,255,0.96));
  border: 1px solid rgba(255,255,255,0.58);
  border-radius: 16px;
  padding: 26px;
  transition: 0.22s ease;
}

.module-card:before {
  content: "";
  position: absolute;
  inset: 0 0 auto 0;
  height: 5px;
  background: var(--card-color);
}

.module-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 18px 38px rgba(15, 23, 42, 0.12);
  border-color: color-mix(in srgb, var(--card-color) 32%, #e5e7eb);
}

.module-icon {
  width: 56px;
  height: 56px;
  border-radius: 14px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: var(--card-color);
  color: #ffffff;
  font-size: 25px;
  margin-bottom: 18px;
}

.module-card h3 {
  font-size: 21px;
  margin-bottom: 10px;
  color: var(--navy);
}

.module-card p {
  color: var(--muted);
  font-size: 15px;
  line-height: 1.6;
  min-height: 76px;
  margin-bottom: 18px;
}

.login-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 9px;
  color: #ffffff;
  background: var(--card-color);
  border-radius: 9px;
  padding: 12px 16px;
  font-size: 14px;
  font-weight: 800;
  transition: 0.22s ease;
}

.login-btn:hover {
  filter: brightness(0.94);
  transform: translateX(3px);
}

.feature-grid {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 14px;
}

.feature {
  background: rgba(255,255,255,0.90);
  border: 1px solid rgba(255,255,255,0.62);
  border-radius: 14px;
  padding: 20px;
  text-align: center;
  transition: 0.2s ease;
  box-shadow: 0 12px 26px rgba(15, 23, 42, 0.10);
}

.feature:hover {
  transform: translateY(-3px);
  box-shadow: 0 14px 28px rgba(15, 23, 42, 0.09);
}

.feature i {
  width: 48px;
  height: 48px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: #ffffff;
  background: linear-gradient(135deg, var(--blue), var(--cyan));
  border-radius: 13px;
  font-size: 21px;
  margin-bottom: 14px;
}

.feature:nth-child(2) i {
  background: linear-gradient(135deg, var(--cyan), var(--teal));
}

.feature:nth-child(3) i {
  background: linear-gradient(135deg, var(--navy), #334155);
}

.feature:nth-child(4) i {
  background: linear-gradient(135deg, var(--gold), #d97706);
}

.feature:nth-child(5) i {
  background: linear-gradient(135deg, var(--teal), #065f46);
}

.feature h3 {
  font-size: 16px;
  line-height: 1.35;
  color: var(--navy);
}

.footer {
  color: rgba(255,255,255,0.88);
  text-align: center;
  padding: 30px 20px 0;
  font-size: 15px;
  line-height: 1.7;
}

.footer strong {
  color: #ffffff;
}

@media (max-width: 960px) {
  .hero {
    grid-template-columns: 1fr;
    padding-top: 26px;
  }

  .hero h1 {
    font-size: 42px;
  }

  .module-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .feature-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 640px) {
  .navbar {
    align-items: flex-start;
    flex-direction: column;
  }

  .nav-links {
    width: 100%;
  }

  .hero h1 {
    font-size: 34px;
  }

  .hero p {
    font-size: 17px;
  }

  .hero-actions {
    flex-direction: column;
  }

  .hero-btn {
    width: 100%;
  }

  .stats,
  .module-grid,
  .feature-grid {
    grid-template-columns: 1fr;
  }

  .section {
    padding: 24px 18px;
    border-radius: 18px;
  }

  .section-head {
    display: block;
  }

  .section-head h2 {
    font-size: 27px;
  }

  .module-card p {
    min-height: auto;
  }
}
</style>
<link rel="stylesheet" href="assets/css/theme.css">
</head>
<body>

<div class="page-bg">
  <header class="navbar">
    <a class="brand" href="index.php">
      <span class="brand-icon"><i class="fa-solid fa-shield-halved"></i></span>
      <span>College Grievance Portal</span>
    </a>

    <nav class="nav-links">
      <a href="#modules">Modules</a>
      <a href="#features">Features</a>
      <a href="<?php echo role_url($basePath, $roles['student']); ?>">Student Login</a>
      <a href="<?php echo role_url($basePath, $roles['admin']); ?>">Admin Login</a>
    </nav>
  </header>

  <main>
    <section class="hero">
      <div>
        <div class="hero-badge">
          <i class="fa-solid fa-building-shield"></i>
          Digital Grievance Redressal Portal
        </div>

        <h1>Online Grievance Management System</h1>
        <p>
          Submit, track, and resolve complaints digitally through a transparent,
          role-based platform designed for students, staff, and college authorities.
        </p>

        <div class="hero-actions">
          <a class="hero-btn alt" href="#modules">
            <i class="fa-solid fa-table-cells-large"></i>
            View All Modules
          </a>
        </div>
      </div>
    </section>

    <div class="content">
      <section class="section" id="modules">
        <div class="section-head">
          <div>
            <h2>Login Modules</h2>
            <p>Select your role and continue to the dedicated login area.</p>
          </div>
        </div>

        <div class="module-grid">
          <?php foreach ($roles as $role) { ?>
            <div class="module-card" style="--card-color: <?php echo htmlspecialchars($role['color']); ?>; --card-light: <?php echo htmlspecialchars($role['color_light']); ?>;">
              <span class="module-icon">
                <i class="fa-solid <?php echo htmlspecialchars($role['icon']); ?>"></i>
              </span>
              <h3><?php echo htmlspecialchars($role['label']); ?></h3>
              <p><?php echo htmlspecialchars($role['description']); ?></p>
              <a class="login-btn" href="<?php echo role_url($basePath, $role); ?>">
                <?php echo htmlspecialchars($role['login_label']); ?>
                <i class="fa-solid fa-arrow-right"></i>
              </a>
            </div>
          <?php } ?>
        </div>
      </section>

      <section class="section" id="features">
        <div class="section-head">
          <div>
            <h2>Portal Features</h2>
            <p>Designed to make complaint handling simple, transparent, and faster.</p>
          </div>
        </div>

        <div class="feature-grid">
          <div class="feature">
            <i class="fa-solid fa-pen-to-square"></i>
            <h3>Online Complaint Submission</h3>
          </div>
          <div class="feature">
            <i class="fa-solid fa-magnifying-glass-chart"></i>
            <h3>Status Tracking</h3>
          </div>
          <div class="feature">
            <i class="fa-solid fa-user-lock"></i>
            <h3>Role-Based Access</h3>
          </div>
          <div class="feature">
            <i class="fa-solid fa-clock"></i>
            <h3>Faster Resolution</h3>
          </div>
          <div class="feature">
            <i class="fa-solid fa-database"></i>
            <h3>Secure Complaint Records</h3>
          </div>
        </div>
      </section>

      <section class="section workflow-section">
        <aside class="hero-panel">
          <h2>Smart Complaint Workflow</h2>
          <p style="color: rgba(255,255,255,0.82); line-height: 1.6;">
            A professional system for registering complaints, assigning them to the
            right authority, tracking progress, and keeping records secure.
          </p>

          <div class="stats">
            <div class="stat">
              <i class="fa-solid fa-file-circle-plus"></i>
              <strong>24x7</strong>
              <span>Online complaint submission</span>
            </div>
            <div class="stat">
              <i class="fa-solid fa-chart-line"></i>
              <strong>Live</strong>
              <span>Status tracking and updates</span>
            </div>
            <div class="stat">
              <i class="fa-solid fa-users-gear"></i>
              <strong>6</strong>
              <span>Role-based login modules</span>
            </div>
            <div class="stat">
              <i class="fa-solid fa-lock"></i>
              <strong>Safe</strong>
              <span>Organized complaint records</span>
            </div>
          </div>
        </aside>
      </section>
    </div>
  </main>

  <footer class="footer">
    <strong>Online Grievance Management System</strong><br>
    BCA Final Year Project | College Grievance Portal | &copy; <?php echo date('Y'); ?>
  </footer>
</div>

<script src="assets/js/theme.js"></script>
</body>
</html>

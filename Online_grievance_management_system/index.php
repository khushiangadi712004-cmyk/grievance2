<?php
$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($basePath === '') {
  $basePath = '.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
<title>GrievanceFlow | Online Grievance Management System</title>

<!-- Google Fonts + Font Awesome -->
<link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<!-- AOS CSS -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

<!-- Original CSS preserved (linked) but we'll enhance with extreme styles -->
<link rel="stylesheet" href="<?php echo htmlspecialchars($basePath); ?>/css/style.css">

<style>
  /* ========== ULTIMATE DESIGN WITH EXTREME ANIMATIONS ========== */
  /* All original classes preserved, logic untouched, only enhanced UI/UX */

  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  body {
    font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
    background: radial-gradient(ellipse at 0% 0%, #0a0f2a, #030617);
    min-height: 100vh;
    overflow-x: hidden;
    position: relative;
  }

  /* Animated gradient orbs background */
  body::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: 
      radial-gradient(circle at 20% 30%, rgba(79, 70, 229, 0.15), transparent 50%),
      radial-gradient(circle at 80% 70%, rgba(6, 182, 212, 0.12), transparent 55%),
      radial-gradient(circle at 40% 90%, rgba(139, 92, 246, 0.1), transparent 60%);
    pointer-events: none;
    z-index: 0;
    animation: auroraShift 12s ease infinite alternate;
  }

  @keyframes auroraShift {
    0% { opacity: 0.5; transform: scale(1);}
    100% { opacity: 1; transform: scale(1.08);}
  }

  /* Particle canvas overlay */
  #particle-canvas {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 1;
  }

  /* Main container - preserves original structure */
  .container {
    position: relative;
    z-index: 2;
    display: flex;
    min-height: 100vh;
    flex-wrap: wrap;
  }

  /* LEFT SECTION - Enhanced with glassmorphism */
  .left {
    flex: 1;
    background: linear-gradient(135deg, rgba(15, 25, 55, 0.75), rgba(8, 15, 40, 0.85));
    backdrop-filter: blur(12px);
    padding: 3rem 2.5rem;
    color: white;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    border-right: 1px solid rgba(79, 70, 229, 0.3);
    position: relative;
    overflow: hidden;
  }

  .left::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(79,70,229,0.08) 0%, transparent 70%);
    animation: rotateSlow 25s linear infinite;
    pointer-events: none;
  }

  @keyframes rotateSlow {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }

  .left > div:first-child {
    font-size: 0.85rem;
    letter-spacing: 3px;
    text-transform: uppercase;
    color: #a5b4fc;
    font-weight: 600;
    margin-bottom: 2rem;
    position: relative;
    z-index: 2;
  }

  .left h1 {
    font-size: 3.5rem;
    font-weight: 800;
    line-height: 1.2;
    background: linear-gradient(135deg, #ffffff, #c7d2fe, #818cf8);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    margin-bottom: 1.5rem;
    position: relative;
    z-index: 2;
    animation: titleGlow 3s ease-in-out infinite;
  }

  @keyframes titleGlow {
    0%, 100% { filter: drop-shadow(0 0 5px rgba(129,140,248,0.3)); }
    50% { filter: drop-shadow(0 0 20px rgba(129,140,248,0.5)); }
  }

  .left p {
    color: #cbd5e1;
    line-height: 1.6;
    font-size: 1rem;
    position: relative;
    z-index: 2;
  }

  /* Footer links in left section */
  .left p:last-of-type {
    margin-top: auto;
    font-size: 0.8rem;
    word-spacing: 15px;
    color: #94a3b8;
    border-top: 1px solid rgba(255,255,255,0.1);
    padding-top: 2rem;
  }

  /* RIGHT SECTION - Modern glass panel */
  .right {
    flex: 1;
    background: rgba(10, 15, 35, 0.6);
    backdrop-filter: blur(16px);
    padding: 3rem 2.5rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    position: relative;
    z-index: 2;
  }

  .right h2 {
    font-size: 2.5rem;
    font-weight: 700;
    background: linear-gradient(135deg, #f0f9ff, #a5f3fc);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    margin-bottom: 0.5rem;
  }

  .right > p {
    color: #94a3b8;
    margin-bottom: 2.5rem;
    font-size: 1rem;
  }

  /* ENHANCED ROLE BUTTONS - Preserve all original functionality */
  .role {
    width: 85%;
    max-width: 320px;
    background: rgba(30, 41, 75, 0.7);
    border: 1px solid rgba(129, 140, 248, 0.3);
    border-radius: 60px;
    padding: 0.9rem 1.8rem;
    margin: 0.7rem 0;
    cursor: pointer;
    transition: all 0.35s cubic-bezier(0.2, 0.9, 0.4, 1.1);
    text-align: left;
    position: relative;
    overflow: hidden;
  }

  .role p {
    margin: 0;
    display: flex;
    align-items: center;
    gap: 12px;
    font-weight: 600;
    color: #e2e8f0;
    font-size: 1rem;
  }

  .role svg {
    width: 20px;
    height: 20px;
    fill: #818cf8;
    transition: all 0.3s;
  }

  /* Hover effects - extreme */
  .role:hover {
    transform: translateX(12px) scale(1.02);
    background: linear-gradient(95deg, #4f46e5, #6366f1);
    border-color: #a5b4fc;
    box-shadow: 0 15px 35px -10px rgba(79, 70, 229, 0.5);
  }

  .role:hover p {
    color: white;
  }

  .role:hover svg {
    fill: white;
    transform: scale(1.1);
  }

  /* Ripple effect on click */
  .role {
    position: relative;
    overflow: hidden;
  }

  .role::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: translate(-50%, -50%);
    transition: width 0.5s, height 0.5s;
  }

  .role:active::after {
    width: 300px;
    height: 300px;
  }

  /* Responsive */
  @media (max-width: 968px) {
    .container {
      flex-direction: column;
    }
    .left {
      border-right: none;
      border-bottom: 1px solid rgba(79,70,229,0.3);
      text-align: center;
    }
    .left h1 {
      font-size: 2.5rem;
    }
    .right {
      padding: 2rem 1.5rem;
    }
    .role {
      width: 100%;
    }
  }

  /* Floating animation for left content */
  @keyframes fadeSlideUp {
    from {
      opacity: 0;
      transform: translateY(30px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .left, .right {
    animation: fadeSlideUp 0.8s ease-out;
  }

  /* Custom scrollbar */
  ::-webkit-scrollbar {
    width: 8px;
  }
  ::-webkit-scrollbar-track {
    background: #0f1222;
  }
  ::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #4f46e5, #818cf8);
    border-radius: 10px;
  }

  /* Additional micro-interactions */
  .role {
    animation: fadeInRight 0.5s ease forwards;
    opacity: 0;
    transform: translateX(-20px);
  }

  .role:nth-child(1) { animation-delay: 0.1s; }
  .role:nth-child(2) { animation-delay: 0.2s; }
  .role:nth-child(3) { animation-delay: 0.3s; }
  .role:nth-child(4) { animation-delay: 0.4s; }
  .role:nth-child(5) { animation-delay: 0.5s; }
  .role:nth-child(6) { animation-delay: 0.6s; }

  @keyframes fadeInRight {
    to {
      opacity: 1;
      transform: translateX(0);
    }
  }

  /* Glass shine effect on left section */
  .left::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.05), transparent);
    animation: shine 12s infinite;
    pointer-events: none;
  }

  @keyframes shine {
    0% { left: -100%; }
    20%, 100% { left: 200%; }
  }

  /* preserve original style.css overrides but enhance */
  .container {
    max-width: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
  }

  /* Ensure SVG icons are crisp */
  svg {
    transition: transform 0.2s;
  }
</style>
</head>
<body>

<canvas id="particle-canvas"></canvas>

<div class="container">
  <div class="left" data-aos="fade-right" data-aos-duration="1000">
    <div>
      <i class="fas fa-chalkboard-user" style="margin-right: 8px;"></i> GMS PORTAL v3.0
    </div>
    <div>
      <h1>Online Grievance<br>Management<br>System</h1>
      <p>
        A unified platform for students, staff, and administration 
        to submit, track, and resolve grievances transparently and efficiently.
      </p>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
      <p style="word-spacing: 15px;">
        <i class="fas fa-shield-alt"></i> CollegeGMS &nbsp;|&nbsp; 
        <i class="fas fa-lock"></i> Privacy Policy &nbsp;|&nbsp; 
        <i class="fas fa-headset"></i> Help&Support
      </p>
    </div>
  </div>

  <div class="right" data-aos="fade-left" data-aos-duration="1000">
    <h2>Welcome <i class="fas fa-hand-peace"></i></h2>
    <p>Select your role to continue</p>

    <!-- ALL ORIGINAL BUTTONS WITH EXACT SAME LINKS AND STRUCTURE -->
    <button class="role" onclick="window.location.href='<?php echo htmlspecialchars($basePath); ?>/user/student_login.php'">
      <p>
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-mortarboard-fill" viewBox="0 0 16 16">
          <path d="M8.211 2.047a.5.5 0 0 0-.422 0l-7.5 3.5a.5.5 0 0 0 .025.917l7.5 3a.5.5 0 0 0 .372 0L14 7.14V13a1 1 0 0 0-1 1v2h3v-2a1 1 0 0 0-1-1V6.739l.686-.275a.5.5 0 0 0 .025-.917z"/>
          <path d="M4.176 9.032a.5.5 0 0 0-.656.327l-.5 1.7a.5.5 0 0 0 .294.605l4.5 1.8a.5.5 0 0 0 .372 0l4.5-1.8a.5.5 0 0 0 .294-.605l-.5-1.7a.5.5 0 0 0-.656-.327L8 10.466z"/>
        </svg> Student
      </p>
    </button>

    <button class="role" onclick="window.location.href='<?php echo htmlspecialchars($basePath); ?>/staff/staff_login.php'">
      <p>
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-people-fill" viewBox="0 0 16 16">
          <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
        </svg> Staff
      </p>
    </button>

    <button class="role" onclick="window.location.href='<?php echo htmlspecialchars($basePath); ?>/hod/hod_login.php'">
      <p>
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-book-fill" viewBox="0 0 16 16">
          <path d="M8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783"/>
        </svg> HOD
      </p>
    </button>

    <button class="role" onclick="window.location.href='<?php echo htmlspecialchars($basePath); ?>/principal/principal_login.php'">
      <p>
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
          <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
        </svg> Principal
      </p>
    </button>

    <button class="role" onclick="window.location.href='<?php echo htmlspecialchars($basePath); ?>/view/management_login.php'">
      <p>
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
          <path d="m8 0 6.61 3h.89a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5H15v7a.5.5 0 0 1 .485.38l.5 2a.498.498 0 0 1-.485.62H.5a.498.498 0 0 1-.485-.62l.5-2A.5.5 0 0 1 1 13V6H.5a.5.5 0 0 1-.5-.5v-2A.5.5 0 0 1 .5 3h.89z"/>
        </svg> Management
      </p>
    </button>

    <button class="role" onclick="window.location.href='<?php echo htmlspecialchars($basePath); ?>/admin/admin_login.php'">
      <p>
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-shield-fill" viewBox="0 0 16 16">
          <path d="M5.072.56C6.157.265 7.31 0 8 0s1.843.265 2.928.56c1.11.3 2.229.655 2.887.87a1.54 1.54 0 0 1 1.044 1.262c.596 4.477-.787 7.795-2.465 9.99a11.8 11.8 0 0 1-2.517 2.453 7 7 0 0 1-1.048.625c-.28.132-.581.24-.829.24s-.548-.108-.829-.24a7 7 0 0 1-1.048-.625 11.8 11.8 0 0 1-2.517-2.453C1.928 10.487.545 7.169 1.141 2.692A1.54 1.54 0 0 1 2.185 1.43 63 63 0 0 1 5.072.56"/>
        </svg> Admin
      </p>
    </button>
  </div>
</div>

<!-- Scripts -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  // Initialize AOS
  AOS.init({ once: false, duration: 800, easing: 'ease-out-cubic' });

  // Particle system for extreme animation background
  const canvas = document.getElementById('particle-canvas');
  const ctx = canvas.getContext('2d');
  let width = window.innerWidth;
  let height = window.innerHeight;
  let particles = [];

  function resizeCanvas() {
    width = window.innerWidth;
    height = window.innerHeight;
    canvas.width = width;
    canvas.height = height;
  }

  class Particle {
    constructor() {
      this.x = Math.random() * width;
      this.y = Math.random() * height;
      this.size = Math.random() * 3 + 1;
      this.speedX = (Math.random() - 0.5) * 0.6;
      this.speedY = (Math.random() - 0.5) * 0.4 + 0.2;
      this.opacity = Math.random() * 0.4 + 0.1;
      this.color = `rgba(129, 140, 248, ${this.opacity})`;
    }
    update() {
      this.x += this.speedX;
      this.y += this.speedY;
      if (this.x < 0) this.x = width;
      if (this.x > width) this.x = 0;
      if (this.y < 0) this.y = height;
      if (this.y > height) this.y = 0;
    }
    draw() {
      ctx.beginPath();
      ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
      ctx.fillStyle = this.color;
      ctx.fill();
    }
  }

  function initParticles(count = 150) {
    particles = [];
    for (let i = 0; i < count; i++) {
      particles.push(new Particle());
    }
  }

  function animateParticles() {
    ctx.clearRect(0, 0, width, height);
    for (let p of particles) {
      p.update();
      p.draw();
    }
    requestAnimationFrame(animateParticles);
  }

  window.addEventListener('resize', () => {
    resizeCanvas();
    initParticles(150);
  });

  resizeCanvas();
  initParticles(160);
  animateParticles();

  // Add ripple effect to all role buttons (enhances user feedback)
  const roleButtons = document.querySelectorAll('.role');
  roleButtons.forEach(btn => {
    btn.addEventListener('click', function(e) {
      // Preserve original navigation - just visual ripple
      const ripple = document.createElement('span');
      ripple.style.position = 'absolute';
      ripple.style.borderRadius = '50%';
      ripple.style.backgroundColor = 'rgba(255,255,255,0.5)';
      ripple.style.width = '100px';
      ripple.style.height = '100px';
      ripple.style.left = e.clientX - btn.getBoundingClientRect().left - 50 + 'px';
      ripple.style.top = e.clientY - btn.getBoundingClientRect().top - 50 + 'px';
      ripple.style.pointerEvents = 'none';
      ripple.style.animation = 'rippleAnim 0.6s ease-out';
      btn.style.position = 'relative';
      btn.style.overflow = 'hidden';
      btn.appendChild(ripple);
      setTimeout(() => ripple.remove(), 600);
    });
  });

  // Add keyframe for ripple
  const styleSheet = document.createElement("style");
  styleSheet.textContent = `
    @keyframes rippleAnim {
      from {
        transform: scale(0);
        opacity: 0.8;
      }
      to {
        transform: scale(4);
        opacity: 0;
      }
    }
  `;
  document.head.appendChild(styleSheet);

  // Floating glow effect for left title
  const title = document.querySelector('.left h1');
  if (title) {
    setInterval(() => {
      title.style.textShadow = '0 0 15px rgba(129,140,248,0.5)';
      setTimeout(() => title.style.textShadow = 'none', 300);
    }, 3000);
  }

  console.log('✅ Grievance Management System | Ultimate UI Active — All original buttons & links preserved');
</script>

</body>
</html>

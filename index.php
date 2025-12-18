<?php
require __DIR__ . '/config/db.php';             // adjust path if needed
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn->set_charset('utf8mb4');

$halls    = (int)$conn->query("SELECT COUNT(*) c FROM halls")->fetch_assoc()['c'];
$courses  = (int)$conn->query("SELECT COUNT(*) c FROM courses")->fetch_assoc()['c'];
$features = (int)$conn->query("SELECT COUNT(*) c FROM features WHERE enabled=1")->fetch_assoc()['c'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>NSBM Lecture Hall Manager • Home</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="./css/style.css">

<style>
  /* Page-scoped aesthetics for the landing */
  body {
    background:
      radial-gradient(1200px 600px at -10% -10%, #123a2a55 0%, transparent 50%),
      radial-gradient(900px 500px at 110% 10%, #1b2e5355 0%, transparent 55%),
      linear-gradient(180deg,#0b0f13 0%, #0d1218 100%);
  }

  .container { max-width:1100px; margin:0 auto; padding:28px 18px 60px; }
  header.site { display:flex; align-items:center; justify-content:space-between; gap:16px; }
  .brand { display:flex; align-items:center; gap:12px; }
  .brand img { max-height:48px; width:auto; }
  .brand .title { font-weight:800; letter-spacing:.2px; font-size:18px; }
  .nav-actions { display:flex; gap:10px; }
  .btn.pill { border-radius:999px; height:40px; padding:0 16px; }

  .hero {
    margin-top:26px; display:grid; grid-template-columns: 1.1fr 0.9fr; gap:26px; align-items:center;
  }
  .hero h1 { font-size: clamp(28px, 4.2vw, 48px); margin:0; line-height:1.08; }
  .hero p { color:#9fb0c7; margin:10px 0 16px; max-width:54ch; }
  .hero-cta { display:flex; gap:10px; flex-wrap:wrap; }
  .chip {
    display:inline-flex; align-items:center; gap:8px; padding:6px 10px; border-radius:999px;
    background:#0b1f15; color:#bdebcf; border:1px solid #224e39; font-size:12px; width:max-content;
  }
  .hero-card {
    background: linear-gradient(180deg, rgba(15,21,29,.85), rgba(15,21,29,.75));
    border:1px solid #223046; border-radius:20px; padding:18px; backdrop-filter:blur(8px);
    box-shadow: 0 30px 70px rgba(0,0,0,.35);
  }
  .stats { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; margin-top:14px; }
  .stat { background:#0f151d; border:1px solid #223046; border-radius:14px; padding:12px; }
  .stat .label { color:#9fb0c7; font-size:12px }
  .stat .value { font-size:22px; font-weight:800 }

  .roles { margin-top:34px; }
  .roles h2 { font-size:18px; text-transform:uppercase; letter-spacing:.12em; color:#9fb0c7; margin-bottom:12px; }
  .role-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; }
  .card {
    background:#0f151d; border:1px solid #1f2b3a; border-radius:18px; padding:16px; transition:transform .18s ease, border-color .18s ease, box-shadow .18s ease;
  }
  .card:hover { transform: translateY(-4px); border-color:#2b3c51; box-shadow:0 12px 32px rgba(0,0,0,.24); }
  .card h3 { margin:0 0 6px; font-size:18px }
  .card p { margin:0 0 10px; color:#9fb0c7 }
  .card .btn { width:100% }

  .footer {
    margin-top:34px; color:#8fa3b9; font-size:12px; display:flex; justify-content:space-between; gap:10px; border-top:1px solid #1f2b3a; padding-top:14px;
  }

  @media (max-width: 980px){
    .hero { grid-template-columns: 1fr; }
    .role-grid { grid-template-columns:1fr; }
  }
</style>
</head>

<body>
  <div class="container">
    
<!-- Top bar -->
<header class="site">
  <div class="brand">
    <img src="./assets/images/nsbmLogo.png" alt="NSBM Green University Town" class="logo-img">
    <div class="brand-text">
      <div class="title">NSBM Lecture Hall Manager</div>
      <div class="hint">Smart scheduling • Conflict detection • Analytics</div>
    </div>
  </div>

  <!-- Navigation Menu -->
  <div class="nav-actions">
    <button class="btn pill primary" onclick="location.href='./auth/login.php'">Sign in</button>
    <button id="continueBtn" class="btn pill accent" style="display:none" onclick="continueAs()">Continue</button>

    <!-- Profile, Settings, Help, Logout buttons -->
    <button class="btn pill" onclick="location.href='./pages/profile.php'" title="Profile">Profile </button>
    <button class="btn pill" onclick="location.href='./pages/help.php'" title="Help">Help </button>
    <button class="btn pill" onclick="location.href='./pages/settings.php'" title="Settings">Settings </button>
    <!-- Logout Button with Redirect -->
<button class="btn pill" onclick="location.href='/nsbm/auth/logout.php'" title="Logout">
  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-power" viewBox="0 0 16 16">
    <path d="M8 0a8 8 0 1 0 8 8 8 8 0 0 0-8-8zm0 15a7 7 0 1 1 0-14 7 7 0 0 1 0 14zm1-10a1 1 0 0 1-2 0V4a1 1 0 0 1 2 0v1z"/>
  </svg>
  Logout
</button>

  </div>
</header>

<style>
  header.site {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 12px 22px;
    border-radius: 16px;
    background: rgba(15, 21, 29, 0.7);
    border: 1px solid #223046;
    backdrop-filter: blur(10px);
    position: sticky;
    top: 0;
    z-index: 99;
  }

  .brand {
    display: flex;
    align-items: center;
    gap: 14px;
  }

  .brand .logo-img {
    height: 52px;
    width: auto;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, .3);
  }

  .brand-text .title {
    font-weight: 800;
    font-size: 20px;
  }

  .brand-text .hint {
    font-size: 13px;
    color: #9fb0c7;
  }

  .nav-actions {
    display: flex;
    gap: 12px;
    align-items: center;
  }

  .btn.pill {
    border-radius: 999px;
    height: 40px;
    padding: 0 18px;
    font-weight: 600;
    transition: all .2s ease;
  }

  .btn.primary {
    background: var(--primary);
    border: 1px solid var(--primary-700);
    color: #04120a;
  }

  .btn.primary:hover {
    background: #1ed760;
  }

  .btn.accent {
    background: #3b82f6;
    border: 1px solid #2563eb;
    color: #fff;
  }

  .btn.accent:hover {
    background: #2563eb;
  }

  /* Styling for icons buttons */
  .btn {
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .btn svg {
    width: 18px;
    height: 18px;
  }

  @media (max-width: 720px) {
    header.site {
      flex-direction: column;
      align-items: flex-start;
      padding: 16px;
    }

    .nav-actions {
      width: 100%;
      justify-content: flex-start;
      flex-wrap: wrap;
    }
  }
</style>
    <!-- Hero -->
    <section class="hero">
      <div>
        <span class="chip">
          <!-- <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M12 3l9 18H3L12 3z" stroke="#63d69d" stroke-width="2"/></svg> -->
          NSBM Green University
        </span>
        <h1>All-in-one lecture hall management for Admins, Lecturers & Students.</h1>
        <p>Plan, book, and analyze with a clean, responsive interface. Forecast high-demand time slots, spot conflicts instantly, and export reports — all from your browser.</p>
        <div class="hero-cta">
          <button class="btn primary" onclick="location.href='./auth/login.php'">Get started</button>
          <button class="btn" onclick="document.getElementById('roles').scrollIntoView({behavior:'smooth'})">Explore roles</button>
        </div>
        <div class="stats">
  <div class="stat"><div class="label">Lecture Halls</div><div class="value"><?= $halls ?></div></div>
  <div class="stat"><div class="label">Sample Courses</div><div class="value"><?= $courses ?></div></div>
  <div class="stat"><div class="label">Features</div><div class="value"><?= $features ?></div></div>
</div>

      </div>

      <!-- Right hero card (replace the old block) -->
<div class="hero-card">
  <div class="section-title">What’s inside NSBM Hall Manager?</div>
  <ul class="hint" style="margin:8px 0 0 18px; line-height:1.8">
    <li>Search halls by capacity, equipment, and availability</li>
    <li>One-click bookings with real-time clash detection</li>
    <li>Timetables for Admin / Lecturer / Student views</li>
    <li>Demand forecasting to predict peak hours</li>
    <li>Usage analytics: hall utilization & daily load</li>
    <li>MySQL-backed data with CSV/Print exports</li>
  </ul>
</div>

    </section>

    <!-- Role cards -->
    <section id="roles" class="roles">
      <h2>Choose your path</h2>
      <div class="role-grid">
        <!-- <div class="card">
          <h3>Admin</h3>
          <p>Manage halls, handle requests, monitor conflicts, and generate reports.</p>
          <div class="row" style="gap:10px">
            <button class="btn" onclick="location.href='./auth/login.php'">Sign in</button>
            <button class="btn primary" onclick="location.href='./Admin/dashboard.php'">View dashboard</button>
          </div>
        </div> -->
        <div class="card">
          <h3>Lecturer</h3>
          <p>View your schedule, submit booking requests, and receive notifications.</p>
          <div class="row" style="gap:10px">
            <button class="btn" onclick="location.href='./auth/login.php'">Sign in</button>
            <button class="btn primary" onclick="location.href='./pages/Lecdashboard.php'">Open lecturer view</button>
          </div>
        </div>
        <div class="card">
          <h3>Student</h3>
          <p>Check timetables, find suitable halls, and follow announcements.</p>
          <div class="row" style="gap:10px">
            <button class="btn" onclick="location.href='./auth/login.php'">Sign in</button>
            <button class="btn primary"
        onclick="location.href='./pages/student.php'">Open student view</button>

          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    
  </div>

<script>
  // Set year
  document.getElementById('yr').textContent = new Date().getFullYear();

  // Show "Continue as {role}" if user is remembered
  const saved = localStorage.getItem('nh_user');
  if (saved) {
    try {
      const u = JSON.parse(saved);
      const btn = document.getElementById('continueBtn');
      if (u && u.role) {
        btn.textContent = `Continue as ${capitalize(u.role)}`;
        btn.style.display = 'inline-flex';
      }
    } catch {}
  }

  function continueAs(){
    const u = JSON.parse(localStorage.getItem('nh_user') || '{}');
    if (u.role === 'admin') location.href = './Admin/dashboard.html';
    else if (u.role === 'lecturer') location.href = './Lecturer/dashboard.html';
    else location.href = './Student/timetable.html';
  }

  function capitalize(s){ return (s||'').charAt(0).toUpperCase() + (s||'').slice(1); }
</script>
<!-- Footer -->
<footer class="footer">
  <div class="footer-container">
    <div class="footer-brand">
      <img src="./assets/images/nsbmLogo.png" alt="NSBM Green University Town" class="footer-logo">
      <div class="footer-text">
        <div class="footer-title">NSBM Lecture Hall Manager</div>
        <div class="footer-hint">Smart scheduling • Conflict detection • Analytics</div>
      </div>
    </div>
    <div class="footer-links">
      <a href="/terms.php" class="footer-link">Terms</a>
      <a href="/privacy.php" class="footer-link">Privacy</a>
      <a href="/contact.php" class="footer-link">Contact</a>
    </div>
  </div>

  <!-- University Details Section -->
  <div class="footer-university-details">
    <div class="footer-address">
      <p><strong>NSBM Green University</strong></p>
      <p>University Town, Pitipana, Homagama, Sri Lanka</p>
      <p>Phone: +94 11 123 4567</p>
      <p>Email: <a href="mailto:info@nsbm.ac.lk">info@nsbm.ac.lk</a></p>
    </div>
  </div>
  
  <div class="footer-bottom">
    <span>© <span id="yr"></span> 2025 NSBM Green University. All rights reserved.</span>
  </div>
</footer>


<style>
  footer {
    background: #0b0f13;
    color: #e5ecf4;
    padding: 20px 20px;
    border-top: 1px solid #223046;
    position: relative;
  }

  .footer-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 40px;
    max-width: 1200px;
    margin: 10px 0 auto;
  }

  .footer-brand {
    display: flex;
    align-items: center;
    gap: 14px;
  }

  .footer-logo {
    height: 40px;
    width: auto;
    border-radius: 8px;
  }

  .footer-text .footer-title {
    font-weight: 800;
    font-size: 18px;
  }

  .footer-text .footer-hint {
    font-size: 12px;
    color: #9fb0c7;
  }

  .footer-links {
    display: flex;
    gap: 16px;
  }

  .footer-link {
    font-size: 14px;
    color: #9fb0c7;
    text-decoration: none;
    transition: color 0.3s ease;
  }

  .footer-link:hover {
    color: var(--primar)
  }

</body>
</html>

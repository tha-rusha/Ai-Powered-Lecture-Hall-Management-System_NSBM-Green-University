<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Auth • Login</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/style.css">

<style>
  /* ---- Page-scoped enhancements (safe with your global theme) ---- */
  body { background:
    radial-gradient(1200px 600px at -10% -10%, #123a2a55 0%, transparent 50%),
    radial-gradient(900px 500px at 110% 10%, #1b2e5355 0%, transparent 55%),
    linear-gradient(180deg,#0b0f13 0%, #0d1218 100%); }

  .login-shell { display:grid; grid-template-columns: 1.2fr 1fr; min-height: 100vh; }
  .login-hero {
    position: relative; padding:48px; display:flex; flex-direction:column; gap:28px; justify-content:center;
    background:
      radial-gradient(650px 350px at 20% 20%, rgba(29,185,84,.10) 0%, transparent 60%),
      radial-gradient(700px 400px at 80% 70%, rgba(59,130,246,.12) 0%, transparent 60%);
    border-right: 1px solid #1f2b3a;
  }
  .hero-badge { display:inline-flex; align-items:center; gap:8px; font-size:12px; padding:6px 10px;
    border:1px solid #224e39; background:#0b1f15; color:#bdebcf; border-radius:999px; width:max-content; }
  .hero-title { font-size: clamp(28px, 3.2vw, 40px); font-weight:800; line-height:1.1; }
  .hero-sub { color:#9fb0c7; max-width:46ch; }

  .login-card-wrap { display:grid; place-items:center; padding:32px; }
  .login-card {
    width:100%; max-width:420px; backdrop-filter: blur(10px);
    background: linear-gradient(180deg, rgba(15,21,29,.85), rgba(15,21,29,.75));
    border: 1px solid #223046; border-radius: 20px; padding: 24px 22px; box-shadow: 0 30px 70px rgba(0,0,0,.35);
    animation: pop .4s ease-out;
  }
  @keyframes pop { from{ transform: translateY(8px); opacity:.0 } to{ transform: translateY(0); opacity:1 } }

  .brand-mini { display:flex; align-items:center; gap:12px; margin-bottom:10px; }
  .brand-mini .logo { width:40px; height:40px; border-radius:12px; background:var(--primary); color:#04120a; display:grid; place-items:center; font-weight:800; }

  .form { display:grid; gap:12px; }
  .field { position:relative; }
  .field input, .field select {
    width:100%; height:46px; padding:10px 40px 10px 40px; border-radius:12px;
    background:#0b1118; border:1px solid #233042; color:#e5ecf4; outline:none;
  }
  .field input:focus, .field select:focus { border-color:#2d4461; box-shadow: 0 0 0 3px rgba(59,130,246,.18); }
  .field svg { position:absolute; left:12px; top:50%; transform:translateY(-50%); width:18px; height:18px; color:#8fa3b9; }
  .field .toggle {
    position:absolute; right:10px; top:50%; transform:translateY(-50%);
    background:transparent; border:0; color:#9fb0c7; cursor:pointer;
  }

  .row { display:flex; justify-content:space-between; align-items:center; gap:10px; }
  .hint a { color:#bcd0e2; }
  .btn.full { width:100%; height:46px; border-radius:12px; }
  .btn.google { background:#0e1520; border:1px solid #2a3544; color:#d7e2ef; }
  .or { display:grid; grid-template-columns:1fr auto 1fr; gap:10px; align-items:center; color:#8fa3b9; font-size:12px; }
  .or::before, .or::after { content:""; height:1px; background:#1f2b3a; }

  .error { display:none; color:#ffb4b4; font-size:12px; padding-left:2px; }
  .foot { margin-top:10px; text-align:center; color:#8fa3b9; font-size:12px; }
  @media (max-width: 940px){ .login-shell { grid-template-columns: 1fr } .login-hero{ display:none } }

  .field .toggle {
  position: absolute;
  right: 36px;   /* move left from the edge */
  top: 50%;
  transform: translateY(-50%);
}

</style>
</head>

<body>
  <div class="login-shell">
    <!-- Left: Hero / Branding -->
    <section class="login-hero">
      
      <h1 class="hero-title">Smart, clean, and fast<br/>hall management.</h1>
      <p class="hero-sub">AI-assisted scheduling, conflict detection, and analytics—wrapped in a smooth front-end experience.</p>
      <ul class="hero-sub" style="margin:0; padding-left:18px; line-height:1.8">
        <li>Predictive hot spots</li>
        <li>Conflict alerts</li>
        <li>Realtime utilization charts</li>
      </ul>
    </section>

    <!-- Right: Login Card -->
    <section class="login-card-wrap">
      <div class="login-card">
        <div style="text-align:center; margin-bottom:16px;">
      <img src="../assets/images/nsbmLogo.png" alt="NSBM Green University Town" style="max-width:160px; height:auto;">
    </div>
        <div class="brand-mini">
          <div class="logo">NH</div>
          <div>
            <div class="title">NSBM Lecture Hall Manager</div>
            <div class="hint">Sign in to continue</div>
          </div>
        </div>

        <div class="form" id="loginForm" novalidate>
            <!-- Role -->
          <div class="field">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.8" fill="none"/></svg>
            <select id="role">
              <option value="admin">Admin</option>
              <option value="lecturer">Lecturer</option>
              <option value="student">Student</option>
            </select>
          </div>
            
          <!-- Email -->
          <div class="field">
            <svg viewBox="0 0 24 24"><path d="M4 6h16v12H4z" stroke="currentColor" stroke-width="1.8" fill="none"/><path d="M4 7l8 6 8-6" stroke="currentColor" stroke-width="1.8" fill="none"/></svg>
            <input id="email" class="input" type="email" placeholder="Email" autocomplete="email" required />
          </div>
          <div class="error" id="emailErr">Please enter a valid email.</div>

          <!-- Password -->
          <div class="field">
            <svg viewBox="0 0 24 24"><rect x="5" y="10" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.8" fill="none"/><path d="M8 10V8a4 4 0 1 1 8 0v2" stroke="currentColor" stroke-width="1.8"/></svg>
            <input id="password" class="input" type="password" placeholder="Password" autocomplete="current-password" minlength="6" required />
            <button class="toggle" type="button" aria-label="Show password" onclick="togglePw()">
              <svg id="eye" viewBox="0 0 24 24" width="18" height="18">
                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z" stroke="currentColor" stroke-width="1.8" fill="none"/>
                <circle cx="12" cy="12" r="3" fill="currentColor"/>
              </svg>
            </button>
          </div>
          <div class="error" id="passErr">Password must be at least 6 characters.</div>


          <div class="row" style="margin-top:6px">
            <label class="hint" style="display:flex; align-items:center; gap:8px">
              <input id="remember" type="checkbox" style="accent-color:#1DB954"> Remember me
            </label>
            <a class="hint" href="./forgot.php">Forgot password?</a>
          </div>

          <!-- Buttons row -->
<div class="row" style="margin-top:12px">
  <button class="btn primary full" id="loginBtn" style="flex:1">Sign in</button>
  <button class="btn primary full" id="signupBtn" style="flex:1"
          onclick="location.href='./signup.php'">Sign up</button>
</div>

          

          <div class="or">or</div>
          <button class="btn google full" type="button" onclick="fakeSSO()">
            <svg width="18" height="18" viewBox="0 0 48 48" style="margin-right:8px">
              <path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3C33.6 31.9 29.3 35 24 35c-6.6 0-12-5.4-12-12s5.4-12 12-12c3 0 5.7 1.1 7.8 2.9l5.7-5.7C33.7 5.3 29.1 3.5 24 3.5 12.1 3.5 2.5 13.1 2.5 25S12.1 46.5 24 46.5 45.5 36.9 45.5 25c0-1.5-.2-3-.6-4.5z"/>
              <path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.7 16.4 19 14 24 14c3 0 5.7 1.1 7.8 2.9l5.7-5.7C33.7 5.3 29.1 3.5 24 3.5c-7.7 0-14.4 4.4-17.7 11.2z"/>
              <path fill="#4CAF50" d="M24 46.5c5.2 0 9.9-2 13.5-5.2l-6.2-5.1c-1.9 1.3-4.3 2.1-7.3 2.1-5.3 0-9.7-3.3-11.3-8l-6.6 5.1C9.5 41.6 16.2 46.5 24 46.5z"/>
              <path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-1.1 3.4-4.4 6-8.3 6-5.3 0-9.7-4.3-9.7-9.5S18.7 15 24 15c2.6 0 4.9.9 6.7 2.5l5.7-5.7C33.7 5.3 29.1 3.5 24 3.5 12.1 3.5 2.5 13.1 2.5 25S12.1 46.5 24 46.5 45.5 36.9 45.5 25c0-1.5-.2-3-.6-4.5z"/>
            </svg>
            Continue with Google
          </button>

          <div class="foot">By signing in you agree to the <a href="#">Terms</a> & <a href="#">Privacy</a>.</div>
        </div>
      </div>
    </section>
  </div>

<script>
  function togglePw(){
    const pw = document.getElementById('password');
    const eye = document.getElementById('eye');
    const isText = pw.type === 'text';
    pw.type = isText ? 'password' : 'text';
    eye.style.opacity = isText ? '1' : '.7';
  }

  const email = document.getElementById('email');
  const password = document.getElementById('password');
  const emailErr = document.getElementById('emailErr');
  const passErr  = document.getElementById('passErr');
  document.getElementById('loginBtn').addEventListener('click', async (e)=>{
    e.preventDefault();
    let ok = true;
    if(!email.value || !/.+@.+\..+/.test(email.value)){ emailErr.style.display='block'; ok=false; } else emailErr.style.display='none';
    if(!password.value || password.value.length < 6){ passErr.style.display='block'; ok=false; } else passErr.style.display='none';
    if(!ok) return;

    try{
      const r = await fetch('./api_login.php', {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({ email: email.value.trim(), password: password.value })
      });
      const data = await r.json();
      if(data.success){
        window.location.href = data.redirect || '../pages/dashboard.php';
      } else {
        alert((data.errors || ['Login failed']).join('\n'));
      }
    }catch(err){
      alert('Network error: ' + err.message);
    }
  });

  // Keep your "Sign up" button redirect
  document.getElementById('signupBtn').addEventListener('click', ()=> location.href='./signup.php');
</script>

</body>
</html>

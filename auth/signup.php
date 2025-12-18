<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Auth • Sign Up</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/style.css">

<style>
  /* Page-scoped styling to mirror the login look */
  body { background:
    radial-gradient(1200px 600px at -10% -10%, #123a2a55 0%, transparent 50%),
    radial-gradient(900px 500px at 110% 10%, #1b2e5355 0%, transparent 55%),
    linear-gradient(180deg,#0b0f13 0%, #0d1218 100%); }

  .shell { display:grid; grid-template-columns: 1.2fr 1fr; min-height: 100vh; }
  .hero {
    padding:48px; display:flex; flex-direction:column; gap:28px; justify-content:center;
    background:
      radial-gradient(650px 350px at 20% 20%, rgba(29,185,84,.10) 0%, transparent 60%),
      radial-gradient(700px 400px at 80% 70%, rgba(59,130,246,.12) 0%, transparent 60%);
    border-right: 1px solid #1f2b3a;
  }
  .hero-title { font-size: clamp(28px, 3.2vw, 40px); font-weight:800; line-height:1.1; }
  .hero-sub { color:#9fb0c7; max-width:46ch; }

  .card-wrap { display:grid; place-items:center; padding:32px; }
  .card {
    width:100%; max-width:460px; backdrop-filter: blur(10px);
    background: linear-gradient(180deg, rgba(15,21,29,.85), rgba(15,21,29,.75));
    border: 1px solid #223046; border-radius: 20px; padding: 24px 22px; box-shadow: 0 30px 70px rgba(0,0,0,.35);
    animation: pop .4s ease-out;
  }
  @keyframes pop { from{ transform: translateY(8px); opacity:.0 } to{ transform: translateY(0); opacity:1 } }

  .brand-mini { display:flex; align-items:center; gap:12px; margin-bottom:10px; justify-content:center; }
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
    position:absolute; right:12px; top:50%; transform:translateY(-50%);
    background:transparent; border:0; color:#9fb0c7; cursor:pointer;
  }

  .row { display:flex; gap:10px; align-items:center; justify-content:space-between; }
  .row-2 { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
  .btn.full { width:100%; height:46px; border-radius:12px; }
  .error { display:none; color:#ffb4b4; font-size:12px; padding-left:2px; }
  .foot { margin-top:10px; text-align:center; color:#8fa3b9; font-size:12px; }
  @media (max-width: 940px){ .shell { grid-template-columns: 1fr } .hero{ display:none } }

    .field .toggle {
  position: absolute;
  right: 36px;   /* move left from the edge */
  top: 50%;
  transform: translateY(-50%);
}
</style>
</head>

<body>
  <div class="shell">
    <!-- Left: Hero -->
    <section class="hero">
      <h1 class="hero-title">Create your account</h1>
      <p class="hero-sub">Join the NSBM Lecture Hall Manager to request halls, manage schedules, and view analytics—tailored to your role.</p>
      <ul class="hero-sub" style="margin:0; padding-left:18px; line-height:1.8">
        <li>Admin, Lecturer, or Student roles</li>
        <li>Conflict-aware scheduling</li>
        <li>Clean, responsive UI</li>
      </ul>
    </section>

    <!-- Right: Sign-up Card -->
    <section class="card-wrap">
      <div class="card">

        <!-- NSBM Logo -->
        <div style="text-align:center; margin-bottom:12px;">
          <img src="../assets/images/nsbmLogo.png" alt="NSBM Green University Town" style="max-width:180px; height:auto;">
        </div>

        <div class="brand-mini">
          <div class="logo">NH</div>
          <div>
            <div class="title">NSBM Lecture Hall Manager</div>
            <div class="hint">Create a new account</div>
          </div>
        </div>

        <form id="signupForm" class="form" novalidate>
          <div class="field">
            <svg viewBox="0 0 24 24"><path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm-9 9a9 9 0 0 1 18 0" stroke="currentColor" stroke-width="1.6" fill="none"/></svg>
            <input id="name" class="input" type="text" placeholder="Full name" required />
          </div>
          <div class="error" id="nameErr">Please enter your name.</div>

          <div class="field">
            <svg viewBox="0 0 24 24"><path d="M4 6h16v12H4z" stroke="currentColor" stroke-width="1.8" fill="none"/><path d="M4 7l8 6 8-6" stroke="currentColor" stroke-width="1.8" fill="none"/></svg>
            <input id="email" class="input" type="email" placeholder="Email" autocomplete="email" required />
          </div>
          <div class="error" id="emailErr">Please enter a valid email.</div>

          <div class="row-2">
            <div class="field">
              <svg viewBox="0 0 24 24"><rect x="5" y="10" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.8" fill="none"/><path d="M8 10V8a4 4 0 1 1 8 0v2" stroke="currentColor" stroke-width="1.8"/></svg>
              <input id="password" class="input" type="password" placeholder="Password (min 6 chars)" minlength="6" required />
              <button class="toggle" type="button" aria-label="Show password" onclick="togglePw('password','eye1')">
                <svg id="eye1" viewBox="0 0 24 24" width="18" height="18">
                  <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z" stroke="currentColor" stroke-width="1.8" fill="none"/>
                  <circle cx="12" cy="12" r="3" fill="currentColor"/>
                </svg>
              </button>
            </div>
            <div class="field">
              <svg viewBox="0 0 24 24"><rect x="5" y="10" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.8" fill="none"/><path d="M8 10V8a4 4 0 1 1 8 0v2" stroke="currentColor" stroke-width="1.8"/></svg>
              <input id="confirm" class="input" type="password" placeholder="Confirm password" minlength="6" required />
              <button class="toggle" type="button" aria-label="Show password" onclick="togglePw('confirm','eye2')">
                <svg id="eye2" viewBox="0 0 24 24" width="18" height="18">
                  <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z" stroke="currentColor" stroke-width="1.8" fill="none"/>
                  <circle cx="12" cy="12" r="3" fill="currentColor"/>
                </svg>
              </button>
            </div>
          </div>
          <div class="error" id="passErr">Passwords must be at least 6 characters and match.</div>

          <div class="field">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.8" fill="none"/></svg>
            <select id="role">
              <option value="student">Student</option>
              <option value="lecturer">Lecturer</option>
              <option value="admin">Admin</option>
            </select>
          </div>

          <label class="hint" style="display:flex; align-items:center; gap:8px; margin-top:2px">
            <input id="tos" type="checkbox" style="accent-color:#1DB954"> I agree to the Terms & Privacy.
          </label>
          <div class="error" id="tosErr">Please agree to the terms to continue.</div>

          <div class="row" style="margin-top:8px">
            <button class="btn primary full" id="signupBtn" style="flex:1">Create account</button>
            <button class="btn primary full" type="button" style="flex:1" onclick="location.href='./login.php'">Back to Login</button>
          </div>

          <div class="foot">Already have an account? <a href="./login.php">Sign in</a></div>
        </form>
      </div>
    </section>
  </div>

<script>
  function togglePw(inputId, eyeId){
    const pw = document.getElementById(inputId);
    const eye = document.getElementById(eyeId);
    const isText = pw.type === 'text';
    pw.type = isText ? 'password' : 'text';
    eye.style.opacity = isText ? '1' : '.7';
  }

  const nameEl  = document.getElementById('name');
  const emailEl = document.getElementById('email');
  const passEl  = document.getElementById('password');
  const confEl  = document.getElementById('confirm');
  const roleEl  = document.getElementById('role');
  const tosEl   = document.getElementById('tos');

  const nameErr  = document.getElementById('nameErr');
  const emailErr = document.getElementById('emailErr');
  const passErr  = document.getElementById('passErr');
  const tosErr   = document.getElementById('tosErr');

  document.getElementById('signupBtn').addEventListener('click', async (e)=>{
    e.preventDefault();
    let ok = true;

    if(!nameEl.value.trim()){ nameErr.style.display='block'; ok=false; } else nameErr.style.display='none';
    if(!emailEl.value || !/.+@.+\..+/.test(emailEl.value)){ emailErr.style.display='block'; ok=false; } else emailErr.style.display='none';
    if(!passEl.value || passEl.value.length < 6 || passEl.value !== confEl.value){ passErr.style.display='block'; ok=false; } else passErr.style.display='none';
    if(!tosEl.checked){ tosErr.style.display='block'; ok=false; } else tosErr.style.display='none';
    if(!ok) return;

    try{
      const r = await fetch('./api_signup.php', {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({
          name: nameEl.value.trim(),
          email: emailEl.value.trim(),
          password: passEl.value,
          role: roleEl.value  // server will ignore 'admin' here
        })
      });
      const data = await r.json();
      if(data.success){
        alert('Account created! Please sign in.');
        window.location.href = './login.php';
      } else {
        alert((data.errors || ['Signup failed']).join('\n'));
      }
    }catch(err){
      alert('Network error: ' + err.message);
    }
  });
</script>

</body>
</html>

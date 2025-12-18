<!-- Header -->
<header class="site">
  <div class="brand">
    <img src="../assets/images/nsbmLogo.png" alt="NSBM Green University Town" class="logo-img">
    <div class="brand-text">
      <div class="title">NSBM Lecture Hall Manager</div>
      <div class="hint">Smart scheduling • Conflict detection • Analytics</div>
    </div>
  </div>

  <div class="nav-actions">
    <!-- Home Button with New Icon -->
    <button class="btn icon-btn" onclick="location.href='../index.php'" title="Go to Home">
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-home" viewBox="0 0 16 16">
        <path d="M8 3.293l6 5V14H2V8.293l6-5zM8 1L0 7v7h16V7L8 1z"/>
      </svg>
    </button>
  </div>
</header>

<style>
  header.site {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px;
    background: #1c1f26;
    border-bottom: 2px solid #223046;
    color: white;
  }

  .brand {
    display: flex;
    align-items: center;
  }

  .logo-img {
    height: 40px;
    width: auto;
    border-radius: 8px;
    margin-right: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
  }

  .brand-text .title {
    font-size: 22px;
    font-weight: 800;
  }

  .brand-text .hint {
    font-size: 14px;
    color: #b0b6c3;
  }

  .nav-actions {
    display: flex;
    gap: 16px;
    align-items: center;
  }

  /* Home Button Icon Style */
  .btn.icon-btn {
    background: transparent;
    border: none;
    padding: 10px;
    cursor: pointer;
    transition: background 0.3s ease;
  }

  .btn.icon-btn svg {
    width: 20px;
    height: 20px;
    color: white;
  }

  .btn.icon-btn:hover {
    background: #3b82f6;
    border-radius: 8px;
  }

  .btn.icon-btn:focus {
    outline: none;
  }
</style>

<!-- Footer -->
<footer class="footer">
  <div class="footer-container">
    <div class="footer-brand">
      <img src="../assets/images/nsbmLogo.png" alt="NSBM Green University Town" class="footer-logo">
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
    <span>© <span id="yr"></span> NSBM Green University. All rights reserved.</span>
  </div>
</footer>

<!-- Add this JavaScript to make the year dynamic -->
<script>
  document.getElementById('yr').textContent = new Date().getFullYear();
</script>

<style>
  footer {
    background: #0b0f13;
    color: #e5ecf4;
    padding: 20px 20px;
    border-top: 1px solid #223046;
    position: relative;
  }
  .footer {
    margin-top:34px; color:#8fa3b9; font-size:12px; display:flex; justify-content:space-between; gap:10px; border-top:1px solid #1f2b3a; padding-top:14px;
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
    color: var(--primary)
  }
</style>

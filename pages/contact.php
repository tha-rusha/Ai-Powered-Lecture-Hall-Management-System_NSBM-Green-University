<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact Support - NSBM Lecture Hall Manager</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
*, *::before, *::after {
  box-sizing: border-box;
}

  body {
    font-family: 'Inter', Arial, sans-serif;
    margin: 0;
    padding: 0;
    background: linear-gradient(180deg, #0b0f13 0%, #0d1218 100%);
    color: #e5ecf4;
  }

  .contact-container {
    display: flex;
    flex-direction: column;
    max-width: 800px;
    margin: 50px auto;
    padding: 30px;
    background: #121820;
    border-radius: 16px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.3);
  }

  .contact-header {
    text-align: center;
    margin-bottom: 30px;
  }

  .contact-header h1 {
    font-size: 32px;
    font-weight: 800;
    margin: 0;
  }

  .contact-header p {
    color: #9fb0c7;
    font-size: 16px;
  }

  form {
    display: grid;
    gap: 18px;
  }

  input, textarea {
    width: 100%;
    padding: 12px 16px;
    border-radius: 12px;
    border: 1px solid #223046;
    background: #0e1520;
    color: #e5ecf4;
    font-size: 16px;
    outline: none;
    transition: border 0.3s ease;
  }

  input:focus, textarea:focus {
    border-color: #1db954;
    box-shadow: 0 0 0 3px rgba(29,185,84,0.2);
  }

  textarea {
    min-height: 140px;
    resize: vertical;
  }

  .btn-submit {
    background-color: #1db954;
    border: none;
    color: #04120a;
    font-weight: 700;
    font-size: 16px;
    padding: 12px 30px;
    border-radius: 30px;
    cursor: pointer;
    transition: background 0.3s ease;
    width: max-content;
    margin: 0 auto;
    display: block;
  }

  .btn-submit:hover {
    background-color: #17b34f;
  }

  /* Optional: Contact info panel */
  .contact-info {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-top: 40px;
    border-top: 1px solid #223046;
    padding-top: 20px;
    color: #9fb0c7;
    font-size: 14px;
  }

  .contact-info p {
    margin: 0;
  }

  @media (max-width: 720px) {
    .contact-container {
      margin: 20px;
      padding: 20px;
    }

    .contact-header h1 {
      font-size: 26px;
    }
  }
</style>
</head>
<body>
<?php include('../partials/header.php'); ?>

<div class="contact-container">
  <div class="contact-header">
    <h1>Contact Support</h1>
    <p>Need help? Fill the form below and our support team will get back to you.</p>
  </div>

  <form id="contactForm">
    <input type="text" id="name" placeholder="Your Name" required>
    <input type="email" id="email" placeholder="Your Email" required>
    <input type="text" id="subject" placeholder="Subject" required>
    <textarea id="message" placeholder="Your Message" required></textarea>
    <button type="submit" class="btn-submit">Send Message</button>
  </form>

  <div class="contact-info">
    <p><strong>Address:</strong> University Town, Pitipana, Homagama, Sri Lanka</p>
    <p><strong>Email:</strong> <a href="mailto:support@nsbm.ac.lk" style="color:#1db954;">support@nsbm.ac.lk</a></p>
    <p><strong>Phone:</strong> +94 11 123 4567</p>
  </div>
</div>

<script>
  const form = document.getElementById('contactForm');

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const payload = {
      name:    document.getElementById('name').value.trim(),
      email:   document.getElementById('email').value.trim(),
      subject: document.getElementById('subject').value.trim(),
      message: document.getElementById('message').value.trim()
    };

    try {
      const r = await fetch('../config/contact_submit.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include', // keep session if logged in
        body: JSON.stringify(payload)
      });
      const data = await r.json();

      if (!r.ok || !data.success) {
        const errs = (data && data.errors) ? data.errors.join('\n') : (data && data.error) || 'Failed to submit message.';
        alert(errs);
        return;
      }

      alert('Thank you! Your message has been submitted. Ticket #' + data.id);
      form.reset();
    } catch (err) {
      alert('Network error: ' + err.message);
    }
  });
</script>

<!-- Include the footer -->
  <?php include('../partials/footer.php'); ?>

</body>
</html>

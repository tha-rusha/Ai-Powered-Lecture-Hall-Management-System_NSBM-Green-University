<?php
// Start session to manage user login state
session_start();

// Assuming some user session data for demonstration purposes
$user = [
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'role' => 'Lecturer',
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Help - NSBM Lecture Hall Manager</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    /* Help Page Style */
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f7fb;
      margin: 0;
      padding: 0;
    }

    /* Header Styling */
    header.site {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 12px 20px;
      background-color: #0e1520;
      color: #fff;
      border-bottom: 2px solid #223046;
      position: sticky;
      top: 0;
      z-index: 1000;
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
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }

    .brand-text .title {
      font-weight: 700;
      font-size: 20px;
    }

    .nav-actions {
      display: flex;
      gap: 20px;
      align-items: center;
    }

    .btn {
      padding: 10px 20px;
      background-color: #1db954;
      color: white;
      border: none;
      border-radius: 12px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .btn:hover {
      background-color: #17b34f;
    }

    /* Help Page Content */
    .help-container {
      max-width: 900px;
      margin: 0 auto;
      padding: 40px 20px;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      color: #333;
    }

    .help-container h2 {
      font-size: 24px;
      font-weight: 700;
      margin-bottom: 20px;
      color: #1db954;
    }

    .help-container p {
      font-size: 16px;
      margin-bottom: 20px;
    }

    .help-container .faq {
      margin-top: 30px;
    }

    .faq-item {
      margin-bottom: 15px;
      padding: 15px;
      background: #f7f7f7;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .faq-item h4 {
      font-size: 18px;
      font-weight: 600;
      margin-bottom: 10px;
    }

    .faq-item p {
      font-size: 16px;
      color: #555;
    }

    /* Footer */
    footer.footer {
      background: #121820;
      color: #fff;
      padding: 40px 20px;
      text-align: center;
    }

    footer.footer .footer-links {
      display: flex;
      justify-content: center;
      gap: 20px;
      margin-top: 20px;
    }

    footer.footer .footer-link {
      color: #9fb0c7;
      text-decoration: none;
    }

    footer.footer .footer-link:hover {
      color: #3b82f6;
    }

    /* Responsiveness */
    @media (max-width: 720px) {
      .help-container {
        width: 90%;
      }

      .nav-actions {
        gap: 10px;
      }
    }
  </style>
</head>
<body>

  <?php include('../partials/header.php'); ?>


  <!-- Help Page Content -->
<div class="help-container">
  <h2 class="page-title">Welcome to the Help Page</h2>
  <p class="intro-text">Here you will find all the help you need for using the NSBM Lecture Hall Manager. If you have any questions or need assistance, please refer to the FAQs below or contact our support team.</p>

  <div class="faq-container">
    <!-- FAQ Card 1 -->
    <div class="faq-card">
      <div class="faq-card-header">
        <h4>How do I book a lecture hall?</h4>
      </div>
      <div class="faq-card-body">
        <p>To book a lecture hall, log in to your account and navigate to the "Book a Hall" section. Select the date, time, and hall you wish to book, then submit your booking request.</p>
      </div>
    </div>

    <!-- FAQ Card 2 -->
    <div class="faq-card">
      <div class="faq-card-header">
        <h4>Can I cancel or reschedule my booking?</h4>
      </div>
      <div class="faq-card-body">
        <p>Yes, you can cancel or reschedule your booking up to 24 hours before the scheduled time. Simply go to your "Bookings" section and select the booking you want to modify.</p>
      </div>
    </div>

    <!-- FAQ Card 3 -->
    <div class="faq-card">
      <div class="faq-card-header">
        <h4>What should I do if I encounter any issues with the system?</h4>
      </div>
      <div class="faq-card-body">
        <p>If you encounter any issues with the system, please visit our "Support" section or contact our technical team via the "Contact" page for assistance.</p>
      </div>
    </div>
  </div>

  <!-- Additional Links or Contact Section -->
  <div class="contact-section">
    <a href="contact.php" class="btn primary">Contact Support</a>
  </div>
</div>

<!-- Styles -->
<style>
  .help-container {
    max-width: 900px;
    margin: 40px auto;
    padding: 20px;
    background: #121820;
    color: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }

  .page-title {
    font-size: 28px;
    font-weight: 700;
    text-align: center;
    color: #1db954;
    margin-bottom: 15px;
  }

  .intro-text {
    font-size: 16px;
    text-align: center;
    margin-bottom: 40px;
    color: #b0b6c3;
  }

  .faq-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
  }

  /* FAQ Cards */
  .faq-card {
    background: #0d141c;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
    padding: 20px;
  }

  .faq-card:hover {
    transform: translateY(-10px);
  }

  .faq-card-header {
    font-size: 18px;
    font-weight: 700;
    color: #1db954;
    margin-bottom: 10px;
  }

  .faq-card-body {
    font-size: 14px;
    color: #b0b6c3;
  }

  /* Contact Button */
  .contact-section {
    margin-top: 40px;
    text-align: center;
  }

  .btn.primary {
    padding: 12px 25px;
    background: #1db954;
    border: none;
    color: white;
    font-weight: 600;
    border-radius: 30px;
    cursor: pointer;
    transition: background 0.3s ease;
  }

  .btn.primary:hover {
    background: #17b34f;
  }

  /* Responsiveness */
  @media (max-width: 768px) {
    .faq-container {
      grid-template-columns: 1fr;
    }
  }
</style>
  <!-- Include the footer -->
  <?php include('../partials/footer.php'); ?>

</body>
</html>

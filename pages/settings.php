<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Settings - NSBM Lecture Hall Manager</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    /* Overall page styling */
    body {
      font-family: 'Arial', sans-serif;
      background-color: #f4f7fb;
      margin: 0;
      padding: 0;
    }

    .settings-container {
      display: flex;
      justify-content: center;
      padding: 40px 20px;
      flex-direction: column;
      gap: 30px;
      max-width: 1000px;
      margin: auto;
      background: #0e1520;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
      color: #fff;
    }

    .settings-header {
      text-align: center;
      margin-bottom: 30px;
    }

    .settings-header h2 {
      font-size: 26px;
      font-weight: 700;
      margin-bottom: 10px;
    }

    .settings-header p {
      font-size: 14px;
      color: #9fb0c7;
    }

    /* Section styles */
    .settings-section {
      background: #121820;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .settings-section h3 {
      font-size: 22px;
      color: #fff;
      margin-bottom: 20px;
      font-weight: 700;
    }

    .settings-section p {
      font-size: 16px;
      color: #e5ecf4;
    }

    .settings-section .input-field {
      width: 100%;
      padding: 12px;
      border-radius: 8px;
      border: 1px solid #223046;
      background: #0e1520;
      color: #e5ecf4;
      margin-bottom: 20px;
      font-size: 16px;
    }

    .settings-section .btn {
      padding: 12px 30px;
      background: var(--primary);
      color: #04120a;
      border: none;
      border-radius: 20px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .settings-section .btn:hover {
      background: #1db954;
    }

    .settings-footer {
      text-align: center;
      color: #9fb0c7;
      font-size: 14px;
      margin-top: 30px;
    }

    .settings-footer a {
      color: #3b82f6;
      text-decoration: none;
    }

    /* For responsiveness */
    @media (max-width: 720px) {
      .settings-container {
        width: 90%;
      }
    }

  </style>
</head>
<body>
    <?php include('../partials/header.php'); ?>

  <!-- Settings Page Container -->
  <div class="settings-container">
    <!-- Header -->
    <div class="settings-header">
      <h2>Account Settings</h2>
      <p>Manage your profile, security settings, notifications, and more.</p>
    </div>

    <!-- Profile Settings Section -->
    <div class="settings-section">
      <h3>Profile Information</h3>
      <p>Update your personal details and profile picture.</p>
      <input type="text" class="input-field" placeholder="Full Name" value="John Doe">
      <input type="email" class="input-field" placeholder="Email" value="john.doe@example.com">
      <input type="file" class="input-field" placeholder="Profile Picture">
      <button class="btn">Save Changes</button>
    </div>

    <!-- Security Settings Section -->
    <div class="settings-section">
      <h3>Security Settings</h3>
      <p>Change your password or enable two-factor authentication.</p>
      <input type="password" class="input-field" placeholder="New Password">
      <input type="password" class="input-field" placeholder="Confirm Password">
      <button class="btn">Update Password</button>
    </div>

    <!-- Notification Settings Section -->
    <div class="settings-section">
      <h3>Notification Settings</h3>
      <p>Control how you'd like to receive updates and alerts.</p>
      <label><input type="checkbox" checked> Receive email notifications</label><br>
      <label><input type="checkbox"> Enable app notifications</label><br>
      <button class="btn">Save Notifications Settings</button>
    </div>

    <!-- Account Settings Section -->
    <div class="settings-section">
      <h3>Account Management</h3>
      <p>Deactivate or delete your account.</p>
      <button class="btn" style="background-color: #f44336;">Deactivate Account</button>
      <button class="btn" style="background-color: #e53935;">Delete Account</button>
    </div>

    <!-- Footer Section -->
    <div class="settings-footer">
      <p>Need help? <a href="#">Contact Support</a></p>
    </div>
  </div>
  <!-- Include the footer -->
  <?php include('../partials/footer.php'); ?>

</body>
</html>

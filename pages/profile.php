<?php
// Start session to manage user login state
session_start();

// Fetch user data (for demonstration purposes, assume it's stored in session or from a database)
$user = [
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'role' => 'Lecturer',
    'profile_picture' => '../assets/images/profile.jpg', // Temporarily using uploaded image
    'joined_on' => 'January 15, 2023',
    'last_login' => 'October 4, 2025'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile - NSBM Lecture Hall Manager</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    /* Profile Page Style */
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f7fb;
      margin: 0;
      padding: 0;
    }

    .profile-container {
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 40px 20px;
      max-width: 1000px;
      margin: 0 auto;
      background: #0e1520;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
      color: #fff;
      position: relative;
    }

    .back-icon {
      position: absolute;
      top: 20px;
      left: 20px;
      font-size: 24px;
      color: #fff;
      cursor: pointer;
      transition: color 0.3s ease;
    }

    .back-icon:hover {
      color: #3b82f6; /* Hover effect color */
    }

    .profile-header {
      text-align: center;
      margin-bottom: 30px;
    }

    .profile-header img {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      border: 4px solid #fff;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .profile-header .name {
      font-size: 28px;
      font-weight: 800;
      margin-top: 12px;
    }

    .profile-header .role {
      font-size: 18px;
      font-weight: 600;
      color: #9fb0c7;
    }

    .tabs {
      display: flex;
      justify-content: space-around;
      width: 100%;
      border-bottom: 2px solid #223046;
      margin-bottom: 30px;
    }

    .tabs button {
      background: transparent;
      border: none;
      padding: 10px 20px;
      font-size: 16px;
      color: #fff;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .tabs button:hover {
      background: #3b82f6;
    }

    .tabs button.active {
      background: #1db954;
      font-weight: 700;
    }

    .tab-content {
      width: 100%;
      background: #121820;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .tab-content h3 {
      font-size: 22px;
      font-weight: 700;
      color: #fff;
      margin-bottom: 20px;
    }

    .tab-content p {
      font-size: 16px;
      color: #e5ecf4;
    }

    .tab-content .info {
      display: flex;
      justify-content: space-between;
      padding: 8px 0;
      border-bottom: 1px solid #223046;
    }

    .tab-content .info label {
      font-weight: 600;
      color: #9fb0c7;
    }

    .btn {
      padding: 12px 20px;
      background: var(--primary);
      border: none;
      color: #04120a;
      border-radius: 999px;
      cursor: pointer;
      transition: background 0.3s ease;
      font-size: 16px;
      width: 100%;
      margin-top: 20px;
    }

    .btn:hover {
      background: #1db954;
    }

    @media (max-width: 720px) {
      .profile-container {
        width: 90%;
      }

      .tabs {
        flex-direction: column;
      }

      .tabs button {
        width: 100%;
        text-align: center;
      }

      .tab-content {
        margin-top: 20px;
      }
    }
    /* Styling the Back Button */
.back-button {
  position: absolute;
  top: 20px;
  left: 20px;
  display: inline-flex;
  align-items: center;
  font-size: 14px;
  color: #fff;
  background-color: #1db954; /* Green background */
  padding: 6px 14px;
  border-radius: 30px;
  cursor: pointer;
  transition: background-color 0.3s ease;
  text-decoration: none;
}

.back-button:hover {
  background-color: #19589d; /* Darker green on hover */
}

.back-arrow {
  font-size: 14px; /* Larger arrow size */
  margin-right: 8px; /* Spacing between arrow and text */
}

  </style>
</head>
<body>

  <!-- Back Button with Arrow and Text -->
<div class="back-button" onclick="window.history.back();">
  <span class="back-arrow">&larr;</span> Back
</div>
<?php include('../partials/header.php'); ?>

  <!-- Profile Container -->
  <div class="profile-container">
    <!-- Profile Header -->
    <div class="profile-header">
      <img src="<?php echo $user['profile_picture']; ?>" alt="Profile Picture">
      <div class="name"><?php echo $user['name']; ?></div>
      <div class="role"><?php echo $user['role']; ?></div>
    </div>

    <!-- Profile Tabs -->
    <div class="tabs">
      <button class="tab-button active" onclick="showTab('personal')">Personal Information</button>
      <button class="tab-button" onclick="showTab('settings')">Account Settings</button>
      <button class="tab-button" onclick="showTab('activity')">Activity Log</button>
    </div>

    <!-- Tab Content -->
    <div class="tab-content" id="personal">
      <h3>Personal Information</h3>
      <div class="info">
        <label>Name:</label><p><?php echo $user['name']; ?></p>
      </div>
      <div class="info">
        <label>Email:</label><p><?php echo $user['email']; ?></p>
      </div>
      <div class="info">
        <label>Role:</label><p><?php echo $user['role']; ?></p>
      </div>
      <div class="info">
        <label>Joined On:</label><p><?php echo $user['joined_on']; ?></p>
      </div>
      <button class="btn" onclick="location.href='/edit-profile.php'" style="
  padding: 8px 20px; /* Reduce horizontal padding */
  font-size: 14px; /* Smaller text */
  width: auto; /* Let width be based on content */
  border-radius: 30px; /* Rounded corners */
  background-color: #1db954; /* Green background */
  cursor: pointer; /* Pointer cursor */
  display: block; /* Block display to center */
  margin: 20px auto; /* Centering button with auto margin */
  transition: background-color 0.3s ease; /* Smooth background color change */
">

  Edit Profile
</button>

    </div>

    <div class="tab-content" id="settings" style="display:none;">
      <h3>Account Settings</h3>
      <div class="info">
        <label>Last Login:</label><p><?php echo $user['last_login']; ?></p>
      </div>
       <button class="btn" onclick="location.href='/change-password.php'" style="
  padding: 8px 20px; /* Reduce horizontal padding */
  font-size: 14px; /* Smaller text */
  width: auto; /* Let width be based on content */
  border-radius: 30px; /* Rounded corners */
  background-color: #1db954; /* Green background */
  cursor: pointer; /* Pointer cursor */
  display: block; /* Block display to center */
  margin: 20px auto; /* Centering button with auto margin */
  transition: background-color 0.3s ease; /* Smooth background color change */
">
Change Password
</button>
    </div>

    <div class="tab-content" id="activity" style="display:none;">
      <h3>Activity Log</h3>
      <p>No recent activity to show.</p>
    </div>
  </div>

  <script>
    // JavaScript to manage tab switching
    function showTab(tabName) {
      const tabs = document.querySelectorAll('.tab-button');
      const contents = document.querySelectorAll('.tab-content');

      tabs.forEach(tab => tab.classList.remove('active'));
      contents.forEach(content => content.style.display = 'none');

      document.querySelector(`#${tabName}`).style.display = 'block';
      document.querySelector(`[onclick="showTab('${tabName}')"]`).classList.add('active');
    }
  </script>

    <!-- Include the footer -->
  <?php include('../partials/footer.php'); ?>

</body>
</html>

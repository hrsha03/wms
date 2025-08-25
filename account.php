<?php include('session/start_session.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>WalletSys | Account</title>
  <link rel="stylesheet" href="assets/navbar.css">
  <link rel="stylesheet" href="assets/account.css">
  <script src="assets/scripts.js" defer></script>
</head>
<body>

<?php include('navbar.php'); ?>

<main class="account-main">
  <div class="account-left">
    <p class="account-title">My Account</p>
    <p class="account-desc">View and manage your profile, security, and preferences.</p>
    <div class="account-details">
      <div><label>Name:</label> <span>John Doe</span></div>
      <div><label>Email:</label> <span>john.doe@email.com</span></div>
      <div><label>Member Since:</label> <span>Jan 2024</span></div>
      <div><label>Status:</label> <span>Active</span></div>
    </div>
    <div class="account-actions">
      <button class="account-btn">Edit Profile</button>
      <button class="account-btn">Change Password</button>
      <button class="account-btn">Logout</button>
    </div>
  </div>
  <div class="account-right account-circles-row">
    <div class="account-circle-col">
      <div class="account-circle c1">Profile<br>Info</div>
    </div>
    <div class="account-circle-col">
      <div class="account-circle c2">Security<br>Settings</div>
    </div>
    <div class="account-circle-col">
      <div class="account-circle c3">Preferences</div>
    </div>
  </div>
</main>

</body>
</html>

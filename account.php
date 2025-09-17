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
    <div class="account-details" id="accountDetails">
      <div><label>Name:</label> <span id="accName">...</span></div>
      <div><label>Email:</label> <span id="accEmail">...</span></div>
      <div><label>Username:</label> <span id="accUsername">...</span></div>
      <div><label>Member Since:</label> <span id="accMemberSince">...</span></div>
      <div><label>Status:</label> <span id="accStatus">...</span></div>
    </div>
    <div class="account-actions">
  <button class="account-btn">Edit Profile</button>
  <button class="account-btn">Change Password</button>
  <button class="account-btn" onclick="logout()">Logout</button>
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

<script>
async function fetchAccountDetails() {
  const token = localStorage.getItem('jwt');
  if (!token) return;
  try {
    const res = await fetch('http://localhost:4000/api/auth/me', {
      headers: { 'Authorization': 'Bearer ' + token }
    });
    if (!res.ok) throw new Error('Not authorized');
    const data = await res.json();
    const user = data.user;
    document.getElementById('accName').textContent = user.name;
    document.getElementById('accEmail').textContent = user.email;
    document.getElementById('accUsername').textContent = user.username;
    document.getElementById('accMemberSince').textContent = new Date(user.member_since).toLocaleDateString();
    document.getElementById('accStatus').textContent = 'Active';
  } catch (e) {
    document.getElementById('accountDetails').innerHTML = '<span style="color:#b00">Could not load account details. Please login again.</span>';
  }
}
document.addEventListener('DOMContentLoaded', fetchAccountDetails);
</script>
</body>
</html>

<?php include('session/start_session.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>wms. | Account</title>
  <link rel="stylesheet" href="assets/navbar.css">
  <link rel="stylesheet" href="assets/account.css">
  <link rel="stylesheet" href="assets/home.css">
  <script src="assets/scripts.js" defer></script>
  <script src="backend/auth.js" defer></script>
  
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
      <div class="account-circle c1">Services<br>Overview</div>
    </div>
    <div class="account-circle-col">
      <div class="account-circle c2">About<br>us</div>
    </div>
    <div class="account-circle-col">
      <div class="account-circle c3">View our<br>documentation</div>
    </div>
  </div>
</main>

<!-- Edit Profile Modal -->
<div id="editProfileModal" class="modal hidden">
  <form id="editProfileForm">
    <h2>Edit Profile</h2>
    <label for="editName">Name:</label>
    <input type="text" id="editName" name="name" placeholder="Enter new name">

    <label for="editEmail">Email:</label>
    <input type="email" id="editEmail" name="email" placeholder="Enter new email">

    <button type="submit">Save Changes</button>
    <button type="button" onclick="closeModal('editProfileModal')">Cancel</button>
  </form>
</div>

<!-- Change Password Modal -->
<div id="changePasswordModal" class="modal hidden">
  <form id="changePasswordForm">
    <h2>Change Password</h2>
    <label for="currentPassword">Current Password:</label>
    <input type="password" id="currentPassword" name="currentPassword" placeholder="Enter current password">

    <label for="newPassword">New Password:</label>
    <input type="password" id="newPassword" name="newPassword" placeholder="Enter new password">

    <label for="confirmPassword">Confirm New Password:</label>
    <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm new password">

    <button type="submit">Change Password</button>
    <button type="button" onclick="closeModal('changePasswordModal')">Cancel</button>
  </form>
</div>

<!-- Custom Alert Modal -->
<div id="customAlertModal" class="hidden">
  <div class="modal-content">
    <span class="close-btn" onclick="closeCustomAlert()">&times;</span>
    <p id="customAlertMessage">This is a custom alert message.</p>
    <button class="modal-btn" onclick="closeCustomAlert()">Close</button>
  </div>
</div>


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

// Open modal function
function openModal(modalId) {
  document.getElementById(modalId).classList.remove('hidden');
}

// Close modal function
function closeModal(modalId) {
  document.getElementById(modalId).classList.add('hidden');
}

// Edit Profile button click handler
document.querySelector('.account-btn:nth-child(1)').addEventListener('click', () => {
  openModal('editProfileModal');
});

// Change Password button click handler
document.querySelector('.account-btn:nth-child(2)').addEventListener('click', () => {
  openModal('changePasswordModal');
});

// Handle Edit Profile form submission
document.getElementById('editProfileForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  const name = document.getElementById('editName').value;
  const email = document.getElementById('editEmail').value;
  const token = localStorage.getItem('jwt');

  try {
    const res = await fetch('http://localhost:4000/api/auth/update-profile', {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer ' + token
      },
      body: JSON.stringify({ name, email })
    });

    handleProfileUpdateResponse(res);
  } catch (err) {
    showCustomAlert('Error', 'An unexpected error occurred.');
  }
});

// Handle Change Password form submission
document.getElementById('changePasswordForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  const currentPassword = document.getElementById('currentPassword').value;
  const newPassword = document.getElementById('newPassword').value;
  const confirmPassword = document.getElementById('confirmPassword').value;
  const token = localStorage.getItem('jwt');

  if (newPassword !== confirmPassword) {
    showCustomAlert('Error', 'Passwords do not match!');
    return;
  }

  try {
    const res = await fetch('http://localhost:4000/api/auth/change-password', {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer ' + token
      },
      body: JSON.stringify({ currentPassword, newPassword })
    });

    handlePasswordChangeResponse(res);
  } catch (err) {
    showCustomAlert('Error', 'An unexpected error occurred.');
  }
});

// Function to show custom alert modal
function showCustomAlert(message) {
  document.getElementById('customAlertMessage').innerHTML = message;
  document.getElementById('customAlertModal').classList.remove('hidden');
}

// Function to close custom alert modal
function closeCustomAlert() {
  document.getElementById('customAlertModal').classList.add('hidden');
}

// Update Profile and Change Password handlers to use custom alert
async function handleProfileUpdateResponse(response) {
  if (response.ok) {
    showCustomAlert('Profile updated successfully!');
    closeModal('editProfileModal');
    fetchAccountDetails();
  } else {
    const error = await response.json();
    showCustomAlert(error.message || 'Failed to update profile.');
  }
}

async function handlePasswordChangeResponse(response) {
  if (response.ok) {
    showCustomAlert('Password changed successfully!');
    closeModal('changePasswordModal');
  } else {
    const error = await response.json();
    showCustomAlert('Failed to change password.');
  }
}

// Add click handlers for circle divs
const circleDivs = document.querySelectorAll('.account-circle');
circleDivs.forEach((circle, index) => {
  circle.addEventListener('click', () => {
    const messages = [
      'Create and manage your wms account with ease.<br><br>Get started with a wallet and get bonus credits!<br><br>Make and receive payments seamlessly.<br><br>Withdraw and deposit credits quickly using vouchers.',
      'wms.<br><br>We simulate the process of managing finances for the new age.<br>Instead of currency, we give you credits so you can spend within a safety net<br>As for cash, we give you vouchers that make withdrawal and deposit super quick and reliable.<br>Invite your friends to join wms and enjoy seamless financial management together!',
      'Discover us on Github<br>And suggest new features you would like to have on wms<a class="link" href="https://github.com/hrsha03/wms" target="_blank">.</a>'
    ];
    showCustomAlert(messages[index]);
  });
});
</script>
<?php include('auth.php'); ?>

</body>
</html>

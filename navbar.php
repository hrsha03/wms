<nav class="navbar">
  <div class="logo">WalletSys</div>
  <ul class="nav-links">
    <li><a href="index.php">Home</a></li>
    <li><a href="wallet.php">Wallet</a></li>
    <li><a href="services.php">Services</a></li>
    <li><a href="account.php">Account</a></li>
  </ul>

  <div class="auth-section" id="authSection">
    <!-- Auth buttons will be rendered by JS -->
  </div>
</nav>
<script>
function parseJwt (token) {
  try {
    return JSON.parse(atob(token.split('.')[1]));
  } catch (e) { return null; }
}

function renderAuthSection() {
  const authSection = document.getElementById('authSection');
  const token = localStorage.getItem('jwt');
  if (token) {
    const payload = parseJwt(token);
    if (payload && payload.username) {
      authSection.innerHTML = `<span class="user-span">Welcome, ${payload.username}</span> <button onclick="logout()">Logout</button>`;
      return;
    }
  }
  authSection.innerHTML = `<button onclick="openModal('loginModal')">Login</button> <button onclick="openModal('signupModal')">Sign Up</button>`;
}
document.addEventListener('DOMContentLoaded', renderAuthSection);
</script>
<script src="assets/scripts.js"></script>

<?php include('session/start_session.php'); ?>
<nav class="navbar">
  <div class="logo">WalletSys</div>
  <ul class="nav-links">
    <li><a href="index.php">Home</a></li>
    <li><a href="wallet.php">Wallet</a></li>
    <li><a href="services.php">Services</a></li>
    <li><a href="account.php">Account</a></li>
  </ul>

  <div class="auth-section">
    <?php if (isset($_SESSION['user'])): ?>
      <span class="user-span">Welcome, <?= htmlspecialchars($_SESSION['user']) ?></span>
      <button onclick="logout()">Logout</button>
    <?php else: ?>
      <button onclick="openModal('loginModal')">Login</button>
      <button onclick="openModal('signupModal')">Sign Up</button>
    <?php endif; ?>
  </div>
</nav>

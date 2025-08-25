<?php include('session/start_session.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>WalletSys | Home</title>
  <link rel="stylesheet" href="assets/style.css">
  <link rel="stylesheet" href="assets/navbar.css">
  <script src="assets/scripts.js" defer></script>
</head>
<body>

<?php include('navbar.php'); ?>

<main class="home-main">
  <div class="home-left">
    <p class="company">Managing Money</p>
    <p class="tagline">Your digital wallet for a smarter life</p>
    <p class="tagline">Credits here = Money IRL</p>

    <?php if (!isset($_SESSION['user'])): ?>
      <div class="cta-buttons">
        <button onclick="openModal('signupModal')" class="btn">Sign Up</button>
        <span class="cta-text">Get 500 free credits on joining!</span>
        <br>
        <button onclick="openModal('loginModal')" class="btn">Login</button>
        <span class="cta-text">Already a member?</span>
      </div>
    <?php else: ?>
      <p class="welcome">Hi, <?= htmlspecialchars($_SESSION['user']) ?>!</p>
      <p class="question">What would you like to do today?</p>
    <?php endif; ?>
  </div>

  <div class="home-right">
    <?php if (!isset($_SESSION['user'])): ?>
        <div class="home-col col-left">
            <div class="circle">Manage<br>credits</div>
            <div class="circle">Earn<br>Daily<br>Bonus</div>
        </div>
        <div class="home-col col-middle">
            <div class="circle">Gamified<br>Finance</div>
            <div class="circle">Send<br>Money</div>
            <div class="circle">Pay<br>Bills</div>
        </div>
        <div class="home-col col-right">
            <div class="circle">Deposit<br>Funds</div>
            <div class="circle">Analytics</div>
        </div>
    <?php else: ?>
        <div class="home-col col-left">
            <div class="circle">Manage<br>credits</div>
            <div class="circle">Earn<br>Daily<br>Bonus</div>
        </div>
        <div class="home-col col-middle">
            <div class="circle">Gamified<br>Finance</div>
            <div class="circle">Send<br>Money</div>
            <div class="circle">Pay<br>Bills</div>
        </div>
        <div class="home-col col-right">
            <div class="circle">Deposit<br>Funds</div>
            <div class="circle">Analytics</div>
        </div>
    <?php endif; ?>
  </div>
</main>

<?php include('auth.php'); ?>

</body>
</html>

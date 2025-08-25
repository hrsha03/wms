<?php include('session/start_session.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>WalletSys | Home</title>
  <link rel="stylesheet" href="assets/style.css">
  <script src="assets/scripts.js" defer></script>
</head>
<body>

<?php include('navbar.php'); ?>

<main class="home-main">
  <div class="home-left">
    <p class="company">Managing Money</p>
    <p class="tagline">made easy for you!</p>
    <p class="tagline">Credits here = Money IRL</p>

    <?php if (!isset($_SESSION['user'])): ?>
      <div class="cta-buttons">
        <button onclick="openModal('signupModal')" class="btn">Sign Up</button>
        <span class="cta-text">to earn 500 free credits!</span>
        <br>
        <button onclick="openModal('loginModal')" class="btn">Login</button>
        <span class="cta-text">if you are a member</span>
      </div>
    <?php else: ?>
      <p class="welcome">Hi, <?= htmlspecialchars($_SESSION['user']) ?>!</p>
      <p class="question">What would you like to do today?</p>
    <?php endif; ?>
  </div>

  <div class="home-right">
    <?php if (!isset($_SESSION['user'])): ?>
      <div class="circle">manage<br>credits</div>
      <div class="circle">earn<br>daily<br>bonus</div>
      <div class="circle">deposit<br>spend<br>track</div>
    <?php else: ?>
      <div class="circle">check<br>balance</div>
      <div class="circle">pay</div>
      <div class="circle">analyze<br>spend</div>
    <?php endif; ?>
  </div>
</main>

<?php include('auth.php'); ?>

</body>
</html>

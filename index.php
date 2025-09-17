<?php include('session/start_session.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>WalletSys | Home</title>
  <link rel="stylesheet" href="assets/home.css">
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

    <div id="homeAuthSection"></div>
  </div>

  <div class="home-right">
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
  </div>
</main>

<script>
function parseJwt (token) {
  try {
    return JSON.parse(atob(token.split('.')[1]));
  } catch (e) { return null; }
}
function renderHomeAuthSection() {
  const el = document.getElementById('homeAuthSection');
  const token = localStorage.getItem('jwt');
  if (token) {
    const payload = parseJwt(token);
    if (payload && payload.username) {
      el.innerHTML = `<p class='welcome'>Hi, ${payload.username}!</p><p class='question'>What would you like to do today?</p>`;
      return;
    }
  }
  el.innerHTML = `<div class='cta-buttons'><button onclick="openModal('signupModal')" class='btn'>Sign Up</button><span class='cta-text'>Get 500 free credits on joining!</span><br><button onclick="openModal('loginModal')" class='btn'>Login</button><span class='cta-text'>Already a member?</span></div>`;
}
document.addEventListener('DOMContentLoaded', renderHomeAuthSection);
</script>
</main>

<?php include('auth.php'); ?>

</body>
</html>

<?php include('session/start_session.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>wms. | Home</title>
  <link rel="stylesheet" href="assets/home.css">
  <link rel="stylesheet" href="assets/navbar.css">
  <script src="assets/scripts.js" defer></script>
</head>
<body>

<?php include('navbar.php'); ?>

<main class="home-main">
  <div class="home-left">
    <p class="company">Money, simplified</p>
    <p class="tagline">Your secure digital wallet for the modern world</p>
    <p class="tagline">Credits inside, cash value outside</p>
    <div id="homeAuthSection"></div>
  </div>

  <div class="home-right">
    <div class="home-col col-left">
      <div class="circle 1">Manage<br>credits</div>
      <div class="circle 2">Earn<br>daily<br>bonus</div>
    </div>
    <div class="home-col col-middle">
      <div class="circle 3">Make<br>payments</div>
      <div class="circle 4">Gamified<br>finance</div>
      <div class="circle 5">Get<br>vouchers</div>
    </div>
    <div class="home-col col-right">
      <div class="circle 6">Deposit<br>funds</div>
      <div class="circle 7">Analytics</div>
    </div>
  </div>
</main>

<!-- Custom Alert Modal -->
<div id="customAlertModal" class="modal hidden">
  <div class="modal-content">
    <span class="close-btn" onclick="closeCustomAlert()">&times;</span>
    <p id="customAlertMessage"></p>
    <button class="modal-btn" onclick="closeCustomAlert()">OK</button>
  </div>
</div>

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
      el.innerHTML = `<p class='welcome'>Hi, ${payload.name}!</p><p class='question'>What would you like to do today?</p>`;
      return;
    }
  }
  el.innerHTML = `<div class='cta-buttons'><button onclick="openModal('signupModal')" class='btn'>Sign Up</button><span class='cta-text'>  Get 500 free credits on joining!</span><br><button onclick="openModal('loginModal')" class='btn'>Login</button><span class='cta-text'>  Already a member?</span></div>`;
}
document.addEventListener('DOMContentLoaded', renderHomeAuthSection);

// Custom Alert Modal
function showCustomAlert(message) {
  const modal = document.getElementById('customAlertModal');
  const messageEl = document.getElementById('customAlertMessage');
  if (!modal || !messageEl) {
    console.error('Custom alert modal or message element not found.');
    return;
  }
  messageEl.innerHTML = message;
  modal.classList.remove('hidden');
}

function closeCustomAlert() {
  const modal = document.getElementById('customAlertModal');
  modal.classList.add('hidden');
}

// Updated setupFeatureDescriptions to use indexes for matching
function setupFeatureDescriptions() {
  const featureDescriptions = [
    'Spend all you want, but spend only what you have in your wallet.<br>We help you develop better spending habits.',
    'The more you utilise our features, the more we reward you.<br>Earn 1% of your balance as bonus, daily!',
    'Before you blink, your transaction is successful.<br>All you need is a wms wallet.',
    'Credits in wms are like money in real life.<br>We have gamified the process so you can manage your real money better.',
    'Withdraw credits as vouchers just like you withdraw cash.<br>Only much, much faster',
    'Balance running low.<br> No worries, get a voucher and top up instantly.',
    'Get detailed analytics of your spending and savings.'
  ];

  document.querySelectorAll('.circle').forEach((circle, index) => {
    circle.addEventListener('click', () => {
      const description = featureDescriptions[index] || 'Feature description not available.';
      showCustomAlert(description);
    });
  });
}

document.addEventListener('DOMContentLoaded', setupFeatureDescriptions);
</script>
</main>

<?php include('auth.php'); ?>

</body>
</html>

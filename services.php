<?php include('session/start_session.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>wms. | Services</title>
	<link rel="stylesheet" href="assets/services.css">
	<link rel="stylesheet" href="assets/navbar.css">
	<link rel="stylesheet" href="assets/modal.css">
	
    <script src="assets/scripts.js" defer></script>
</head>
<body>

<?php include('navbar.php'); ?>

<main class="services-main">
	<div class="services-left">
		<ul class="services-list">
			<li><strong>Balance Management:</strong> View and track your wallet balance in real time.</li>
			<li><strong>Fund Transfer:</strong> Send and receive money instantly to other users.</li>
			<li><strong>Deposit & Withdraw:</strong> Add funds via bank/card and withdraw to your account.</li>
			<li><strong>Transaction History:</strong> Review all your past transactions with detailed logs.</li>
			<li><strong>Bill Payments:</strong> Pay utility bills, mobile recharge, and more directly from your wallet.</li>
			<li><strong>Daily Bonus:</strong> Earn credits for daily logins and activities.</li>
			<li><strong>Spending Analytics:</strong> Visualize and analyze your spending habits.</li>
			<li><strong>Security:</strong> Two-factor authentication and encrypted transactions for safety.</li>
		</ul>
	</div>
			<div class="services-right">
				<div class="service-col col-left">
					<div class="service-circle 1">Manage<br>credits</div>
					<div class="service-circle 2">Deposit<br>funds</div>
					<div class="service-circle 3">Daily<br>bonus</div>
				</div>
				<div class="service-col col-right">
					<div class="service-circle 4">Make<br>payments</div>
					<div class="service-circle 5">Get<br>vouchers</div>
					<div class="service-circle 6">Analytics</div>
				</div>
			</div>
<!-- Custom Alert Modal -->
<div id="customAlertModal" class="modal hidden">
  <div class="modal-content">
    <span class="close-btn" onclick="closeCustomAlert()">&times;</span>
    <p id="customAlertMessage"></p>
    <button class="modal-btn" onclick="closeCustomAlert()">OK</button>
  </div>
</div>
<script>
	
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
    'Balance running low. No worries, get a voucher and top up instantly.',
    'The more you utilise our features, the more we reward you.\nEarn 1% of your balance as bonus, daily!',
    'Before you blink, your transaction is successful. All you need is a wms wallet.',
    'Withdraw credits as vouchers just like you withdraw cash. Only much, much faster',
    'Get detailed analytics of your spending and savings.'

  ];

  document.querySelectorAll('.service-circle').forEach((circle, index) => {
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

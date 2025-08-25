<?php include('session/start_session.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>WalletSys | Services</title>
	<link rel="stylesheet" href="assets/services.css">
	<link rel="stylesheet" href="assets/navbar.css">
    <script src="assets/scripts.js" defer></script>
</head>
<body>

<?php include('navbar.php'); ?>

<main class="services-main">
	<div class="services-left">
		<p class="services-desc">Explore the essential features that make managing your digital money simple, secure, and convenient.</p>
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
					<div class="service-circle">Check<br>Balance</div>
					<div class="service-circle">Deposit<br>Funds</div>
					<div class="service-circle">Daily<br>Bonus</div>
				</div>
				<div class="service-col col-right">
					<div class="service-circle">Send<br>Money</div>
					<div class="service-circle">Pay<br>Bills</div>
					<div class="service-circle">Analytics</div>
				</div>
			</div>
</main>

</body>
</html>

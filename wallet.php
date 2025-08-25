<?php include('session/start_session.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>WalletSys | Wallet</title>
  <link rel="stylesheet" href="assets/navbar.css">
  <link rel="stylesheet" href="assets/wallet.css">
  <script src="assets/scripts.js" defer></script>
</head>
<body>

<?php include('navbar.php'); ?>

<main class="wallet-main">
  <div class="wallet-left">
    <p class="wallet-title">My Wallet</p>
    <p class="wallet-desc">Access all your wallet features and manage your digital funds securely.</p>
    <div class="wallet-balance">Balance: 2,500 Credits</div>
    <div class="wallet-actions">
      <button class="wallet-btn">Add Funds</button>
      <button class="wallet-btn">Send Money</button>
      <button class="wallet-btn">Withdraw</button>
      <button class="wallet-btn">Transaction History</button>
    </div>
  </div>
  <div class="wallet-right">
    <div class="wallet-col left">
      <div class="wallet-circle">Deposit<br>Funds</div>
      <div class="wallet-circle">History</div>
    </div>
    <div class="wallet-col middle">
      <div class="wallet-circle c2">Send<br>Money</div>
    </div>
    <div class="wallet-col right">
      <div class="wallet-circle">Withdraw</div>
      <div class="wallet-circle">Analytics</div>
    </div>
  </div>
</main>

</body>
</html>

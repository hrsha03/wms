<?php include('session/start_session.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>wms. | Wallet</title>
  <link rel="stylesheet" href="assets/navbar.css">
  <link rel="stylesheet" href="assets/wallet.css">
  <link rel="stylesheet" href="assets/home.css">
  
  <script src="assets/scripts.js" defer></script>
</head>
<body>

<?php include('navbar.php'); ?>

<main class="wallet-main">
  <div class="wallet-left">
    <p class="wallet-title">My "wms"</p>
    <p class="wallet-desc">Access all your wallet features and manage your digital funds securely.</p>
    <div class="wallet-balance" id="walletBalance">Balance: 2,500 Credits</div>
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

<!-- CREATE WALLET MODAL -->
<div id="createWalletModal" class="modal hidden">
  <form id="createWalletForm">
    <h2>Create Wallet</h2>
    <input type="text" name="walletId" placeholder="Wallet ID" required />
    <input type="password" name="pin" placeholder="4-digit PIN" pattern="\d{4}" required />
    <button type="submit">Create Wallet</button>
    <button type="button" onclick="closeModal('createWalletModal')">Cancel</button>
    <div id="createWalletError" class="error-msg"></div>
  </form>
</div>

<script>
function parseJwt (token) {
  try {
    return JSON.parse(atob(token.split('.')[1]));
  } catch (e) { return null; }
}

async function getAuthToken() {
  return localStorage.getItem('jwt');
}

async function loadWallet() {
  const balanceEl = document.getElementById('walletBalance');
  const token = await getAuthToken();
  if (!token) {
    balanceEl.innerHTML = `Please login to create or view wallet <button onclick=\"openModal('loginModal')\">Login</button>`;
    return;
  }
  try {
    const res = await fetch('http://localhost:4000/api/auth/wallet', {
      headers: { 'Authorization': `Bearer ${token}` }
    });
    const data = await res.json();

    if (!res.ok) {
      if (res.status === 404) {
        balanceEl.innerHTML = `No wallet found. <button class=\"open-create-wallet-btn\">Create Wallet</button>`;
        const createWalletBtn = document.querySelector('.open-create-wallet-btn');
        createWalletBtn.addEventListener('click', () => openModal('createWalletModal'));
        return;
      } else {
        balanceEl.innerHTML = `Error fetching wallet: ${data.message || res.statusText}`;
      }
      return;
    }

    if (!data.hasWallet) {
      balanceEl.innerHTML = `No wallet found. <button class=\"open-create-wallet-btn\">Create Wallet</button>`;
      const createWalletBtn = document.querySelector('.open-create-wallet-btn');
      createWalletBtn.addEventListener('click', () => openModal('createWalletModal'));
      return;
    }

    balanceEl.textContent = `Balance: ${data.balance.toLocaleString()} Credits`;
  } catch (err) {
    balanceEl.innerHTML = `Error connecting to server`;
  }
}

document.getElementById('createWalletForm').onsubmit = async function(e) {
  e.preventDefault();
  const token = await getAuthToken();
  if (!token) {
    document.getElementById('createWalletError').textContent = 'You must be logged in to create a wallet.';
    return;
  }
  const form = e.target;
  const walletId = form.walletId.value.trim();
  const pin = form.pin.value.trim();
  if (!/^\d{4}$/.test(pin)) {
    document.getElementById('createWalletError').textContent = 'PIN must be 4 digits.';
    return;
  }
  try {
    const res = await fetch('http://localhost:4000/api/auth/wallet', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` },
      body: JSON.stringify({ walletId, pin })
    });
    const data = await res.json();
    if (!res.ok) {
      document.getElementById('createWalletError').textContent = data.message || 'Failed to create wallet';
      return;
    }
    closeModal('createWalletModal');
    await loadWallet();
    alert('Wallet created! You received 500 Credits.');
  } catch (err) {
    document.getElementById('createWalletError').textContent = 'Server error';
  }
};

document.addEventListener('DOMContentLoaded', loadWallet);
</script>

</body>
</html>

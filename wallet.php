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
      <button class="wallet-btn" onclick="openModal('sendMoneyModal')">Send Money</button>
      <button class="wallet-btn">Withdraw</button>
      <button class="wallet-btn" onclick="openModal('transactionHistoryModal')">Transaction History</button>
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

<!-- SEND MONEY MODAL -->
<div id="sendMoneyModal" class="modal hidden">
  <form id="sendMoneyForm">
    <h2>Send Money</h2>
    <input type="text" name="recipientId" placeholder="Recipient's Wallet ID" required />
    <input type="number" name="amount" placeholder="Amount" required />
    <button type="submit">Next</button>
    <button type="button" onclick="closeModal('sendMoneyModal')">Cancel</button>
    <div id="sendMoneyError" class="error-msg"></div>
  </form>
</div>

<!-- VERIFY PIN MODAL -->
<div id="verifyPinModal" class="modal hidden">
  <form id="verifyPinForm">
    <h2>Verify PIN</h2>
    <input type="password" name="pin" placeholder="4-digit PIN" pattern="\d{4}" required />
    <button type="submit">Send</button>
    <button type="button" onclick="closeModal('verifyPinModal')">Cancel</button>
    <div id="verifyPinError" class="error-msg"></div>
  </form>
</div>

<!-- TRANSACTION HISTORY MODAL -->
<div id="transactionHistoryModal" class="modal hidden">
  <h2>Transaction History</h2>
  <table id="transactionTable">
    <thead>
      <tr>
        <th>Type</th>
        <th>Wallet ID</th>
        <th>Amount</th>
        <th>Timestamp</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>
  <button type="button" onclick="closeModal('transactionHistoryModal')">Close</button>
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

// Send Money Handler
let sendMoneyDetails = {};
document.getElementById('sendMoneyForm').onsubmit = function(e) {
  e.preventDefault();
  const form = e.target;
  sendMoneyDetails.recipientId = form.recipientId.value.trim();
  sendMoneyDetails.amount = parseInt(form.amount.value.trim(), 10);
  closeModal('sendMoneyModal');
  openModal('verifyPinModal');
};

document.getElementById('verifyPinForm').onsubmit = async function(e) {
  e.preventDefault();
  const form = e.target;
  const pin = form.pin.value.trim();
  try {
    const token = await getAuthToken();
    const res = await fetch('http://localhost:4000/api/auth/wallet/send', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` },
      body: JSON.stringify({ ...sendMoneyDetails, pin })
    });
    const data = await res.json();
    if (!res.ok) {
      document.getElementById('verifyPinError').textContent = data.message || 'Transaction failed';
      return;
    }
    closeModal('verifyPinModal');
    alert('Transaction successful!');
    await loadWallet();
  } catch (err) {
    document.getElementById('verifyPinError').textContent = 'Server error';
  }
};

// Load Transaction History
async function loadTransactionHistory() {
  const token = await getAuthToken();
  try {
    const res = await fetch('http://localhost:4000/api/auth/wallet/transactions', {
      headers: { 'Authorization': `Bearer ${token}` }
    });
    const data = await res.json();
    if (!res.ok) {
      alert(data.message || 'Failed to load transactions');
      return;
    }
    const tbody = document.querySelector('#transactionTable tbody');
    tbody.innerHTML = '';
    data.transactions.forEach(tranx => {
      const row = document.createElement('tr');
      row.innerHTML = `
        <td>${tranx.verb}</td>
        <td>${tranx.wms_id}</td>
        <td>${tranx.amount}</td>
        <td>${new Date(tranx.timestamp).toLocaleString()}</td>
      `;
      tbody.appendChild(row);
    });
    openModal('transactionHistoryModal');
  } catch (err) {
    alert('Server error');
  }
}

document.addEventListener('DOMContentLoaded', loadWallet);
</script>

</body>
</html>

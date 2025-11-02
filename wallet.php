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
  <script src="assets/creating-wallet.js" defer></script>
  <script src="assets/send-money.js" defer></script>
  <script src="assets/transaction-history.js" defer></script>
  <script src="assets/vouchers.js" defer></script>
  <script src="assets/wallet-actions.js" defer></script>
</head>
<body>

<?php include('navbar.php'); ?>

<main class="wallet-main">
  <div class="wallet-left">
    <p class="wallet-title">My "wms"</p>
    <p class="wallet-desc">Access all your wallet features and manage your digital funds securely.</p>
    <div class="wallet-balance" id="walletBalance">Balance: 500 Credits</div>
    <div class="wallet-actions">
      <button class="wallet-btn">Add Funds</button>
      <button class="wallet-btn" onclick="openModal('sendMoneyModal')">Send Money</button>
      <button class="wallet-btn">Withdraw</button>
      <button class="wallet-btn" onclick="openModal('transactionHistoryModal')">Transaction History</button>
    </div>
  </div>
  <div class="wallet-right">
    <div class="wallet-col left">
      <div class="wallet-circle" onclick="openModal('depositVoucherModal')">Deposit<br>Funds</div>
      <div class="wallet-circle" onclick="openModal('transactionHistoryModal')">History</div>
    </div>
    <div class="wallet-col middle">
      <div class="wallet-circle c2" onclick="openModal('sendMoneyModal')">Send<br>Money</div>
    </div>
    <div class="wallet-col right">
      <div class="wallet-circle" onclick="openModal('withdrawVoucherModal')">Withdraw</div>
      <div class="wallet-circle" onclick="openModal('vouchersModal')">Vouchers</div>
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

<!-- VOUCHERS MODAL -->
<div id="vouchersModal" class="modal hidden">
  <table id="vouchersTable">
    <thead>
      <tr>
        <th>Voucher ID</th>
        <th>Amount</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>
  <button type="button" onclick="closeModal('vouchersModal')">Close</button>
</div>

<!-- DEPOSIT USING VOUCHER MODAL -->
<div id="depositVoucherModal" class="modal hidden">
  <form id="depositVoucherForm">
    <h2>Deposit Using Voucher</h2>
    <input type="text" name="voucherId" placeholder="Voucher ID" required />
    <button type="submit">Deposit</button>
    <button type="button" onclick="closeModal('depositVoucherModal')">Cancel</button>
    <div id="depositVoucherError" class="error-msg"></div>
  </form>
</div>

<!-- WITHDRAW USING VOUCHER MODAL -->
<div id="withdrawVoucherModal" class="modal hidden">
  <form id="withdrawVoucherForm">
    <h2>Withdraw Using Voucher</h2>
    <input type="number" name="amount" placeholder="Amount" required />
    <button type="submit">Withdraw</button>
    <button type="button" onclick="closeModal('withdrawVoucherModal')">Cancel</button>
    <div id="withdrawVoucherError" class="error-msg"></div>
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
    balanceEl.innerHTML = `Please login to create or view wallet`;
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
document.addEventListener('DOMContentLoaded', loadWallet);

</script>


</body>
</html>

// Functions and handlers related to creating a wallet

async function getAuthToken() {
  return localStorage.getItem('jwt');
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
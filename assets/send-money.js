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
    showCustomAlert('Transaction successful!');
    await loadWallet();
  } catch (err) {
    document.getElementById('verifyPinError').textContent = 'Server error';
  }
};

// General wallet actions like deposits and withdrawals

async function withdrawVoucher(amount) {
  console.log('withdrawVoucher function triggered with amount:', amount);
  const token = await getAuthToken();
  if (!token) {
    alert('You must be logged in to withdraw funds.');
    return;
  }
  try {
    const res = await fetch('http://localhost:4000/api/auth/wallet/withdraw', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` },
      body: JSON.stringify({ amount })
    });
    const data = await res.json();
    console.log('API response for withdraw:', data);

    if (!res.ok) {
      alert(data.message || 'Failed to withdraw funds');
      return;
    }

    alert(`Withdrawal successful! Voucher ID: ${data.voucherId}`);
    await loadWallet();
  } catch (err) {
    console.error('Error in withdrawVoucher function:', err);
    alert('Server error');
  }
}

const withdrawVoucherForm = document.getElementById('withdrawVoucherForm');
withdrawVoucherForm.onsubmit = async function(e) {
  e.preventDefault();
  const form = e.target;
  const amount = parseInt(form.amount.value.trim(), 10);
  await withdrawVoucher(amount);
  closeModal('withdrawVoucherModal');
};

async function depositVoucher(voucherId) {
  console.log('depositVoucher function triggered with voucherId:', voucherId);
  const token = await getAuthToken();
  if (!token) {
    alert('You must be logged in to deposit funds.');
    return;
  }
  try {
    const res = await fetch('http://localhost:4000/api/auth/wallet/deposit', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` },
      body: JSON.stringify({ voucherId })
    });
    const data = await res.json();
    console.log('API response for deposit:', data);

    if (!res.ok) {
      alert(data.message || 'Failed to deposit funds');
      return;
    }

    alert(`Deposit successful! Amount: ${data.amount}`);
    await loadWallet();
  } catch (err) {
    console.error('Error in depositVoucher function:', err);
    alert('Server error');
  }
}

const depositVoucherForm = document.getElementById('depositVoucherForm');
depositVoucherForm.onsubmit = async function(e) {
  e.preventDefault();
  const form = e.target;
  const voucherId = form.voucherId.value.trim();
  await depositVoucher(voucherId);
  closeModal('depositVoucherModal');
};
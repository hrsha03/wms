// Functions and handlers related to vouchers

async function loadVouchers() {
  const token = await getAuthToken();
  try {
    const res = await fetch('http://localhost:4000/api/auth/wallet/vouchers', {
      headers: { 'Authorization': `Bearer ${token}` }
    });
    const data = await res.json();

    if (!res.ok) {
      console.error('API returned an error:', data.message || res.statusText);
      return;
    }

    if (!data.vouchers || !Array.isArray(data.vouchers)) {
      console.error('Unexpected data format:', data);
      return;
    }

    const tbody = document.querySelector('#vouchersTable tbody');
    if (!tbody) {
      console.error('Vouchers table body not found');
      return;
    }

    tbody.innerHTML = '';
    data.vouchers.forEach(voucher => {
      const row = document.createElement('tr');
      row.innerHTML = `
        <td>${voucher.voucher_id}</td>
        <td>${voucher.amount}</td>
        <td>${voucher.status}</td>
      `;
      tbody.appendChild(row);
    });

    openModal('vouchersModal');
  } catch (err) {
    console.error('Error in loadVouchers function:', err);
    alert('Server error');
  }
}

const voucherCircle = document.querySelector('.wallet-circle[onclick="openModal(\'vouchersModal\')"]');
if (voucherCircle) {
  voucherCircle.addEventListener('click', (e) => {
    loadVouchers();
  });
}
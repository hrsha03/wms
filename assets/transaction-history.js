// Functions and handlers related to transaction history

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

const transactionHistoryButton = document.querySelector('.wallet-btn[onclick="openModal(\'transactionHistoryModal\')"]');
if (transactionHistoryButton) {
  transactionHistoryButton.addEventListener('click', loadTransactionHistory);
}

const transactionCircle = document.querySelector('.wallet-circle[onclick="openModal(\'transactionHistoryModal\')"]');
if (transactionCircle) {
  transactionCircle.addEventListener('click', (e) => {
    loadTransactionHistory();
  });
}
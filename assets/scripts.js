function openModal(id) {
  document.getElementById(id).classList.remove('hidden');
}

function closeModal(id) {
  document.getElementById(id).classList.add('hidden');
}

function logout() {
  const form = document.createElement('form');
  form.method = 'POST';
  form.innerHTML = `<input type="hidden" name="action" value="logout" />`;
  document.body.appendChild(form);
  form.submit();
}

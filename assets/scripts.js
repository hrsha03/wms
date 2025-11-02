function openModal(id) {
  const modal = document.getElementById(id);
  if (!modal) {
    console.error(`Modal with id '${id}' not found.`);
    return;
  }
  modal.classList.remove('hidden');
}

function closeModal(id) {
  document.getElementById(id).classList.add('hidden');
}

// JWT helpers and logout
function setToken(token) {
  localStorage.setItem('jwt', token);
}
function getToken() {
  return localStorage.getItem('jwt');
}
function removeToken() {
  localStorage.removeItem('jwt');
}
function updateAuthUI() {
  location.reload();
}
function logout() {
  removeToken();
  updateAuthUI();
}

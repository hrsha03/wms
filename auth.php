

<!-- LOGIN MODAL -->
<div id="loginModal" class="modal hidden">
  <form id="loginForm">
    <h2>Login</h2>
    <input type="text" name="username" placeholder="Username" required />
    <input type="password" name="password" placeholder="Password" required />
    <button type="submit">Login</button>
    <button type="button" onclick="closeModal('loginModal')">Cancel</button>
    <div id="loginError" class="error-msg"></div>
  </form>
</div>

<!-- SIGNUP MODAL -->
<div id="signupModal" class="modal hidden">
  <form id="signupForm">
    <h2>Sign Up</h2>
    <input type="text" name="username" placeholder="Username" required />
    <input type="password" name="password" placeholder="Password" required />
    <button type="submit">Sign Up</button>
    <button type="button" onclick="closeModal('signupModal')">Cancel</button>
    <div id="signupError" class="error-msg"></div>
  </form>
</div>

<script>
// Login handler
document.getElementById('loginForm').onsubmit = async function(e) {
  e.preventDefault();
  const form = e.target;
  const username = form.username.value;
  const password = form.password.value;
  const res = await fetch('http://localhost:4000/api/auth/login', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ username, password })
  });
  const data = await res.json();
  if (res.ok) {
    setToken(data.token);
    closeModal('loginModal');
    updateAuthUI();
  } else {
    document.getElementById('loginError').textContent = data.message || 'Login failed';
  }
};

// Signup handler
document.getElementById('signupForm').onsubmit = async function(e) {
  e.preventDefault();
  const form = e.target;
  const username = form.username.value;
  const password = form.password.value;
  const res = await fetch('http://localhost:4000/api/auth/signup', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ username, password })
  });
  const data = await res.json();
  if (res.ok) {
    closeModal('signupModal');
    alert('Signup successful! Please login.');
  } else {
    document.getElementById('signupError').textContent = data.message || 'Signup failed';
  }
};
</script>

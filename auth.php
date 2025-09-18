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
    <input type="text" name="name" placeholder="Full Name" required />
    <input type="email" name="email" placeholder="Email" required />
    <input type="text" name="username" placeholder="Username" required />
    <input type="password" name="password" placeholder="Password" required />
    <div id="passwordStrength" style="color: #b00; font-size: 0.9em; margin-bottom: 8px;"></div>
    <button type="submit">Sign Up</button>
    <button type="button" onclick="closeModal('signupModal')">Cancel</button>
    <div id="signupError" class="error-msg"></div>
  </form>
</div>

<script>
// Helper to hash password with SHA-256
async function hashPassword(password) {
  const encoder = new TextEncoder();
  const data = encoder.encode(password);
  const hashBuffer = await window.crypto.subtle.digest('SHA-256', data);
  return Array.from(new Uint8Array(hashBuffer)).map(b => b.toString(16).padStart(2, '0')).join('');
}

// Login handler
document.getElementById('loginForm').onsubmit = async function(e) {
  e.preventDefault();
  const form = e.target;
  const username = form.username.value;
  const password = form.password.value;
  const hashedPassword = await hashPassword(password);
  const res = await fetch('http://localhost:4000/api/auth/login', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ username, password: hashedPassword })
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

// Password strength checker
function checkPasswordStrength(password) {
  if (password.length < 8) return 'Password too short (min 8 chars)';
  if (!/[A-Z]/.test(password)) return 'Add at least one uppercase letter';
  if (!/[a-z]/.test(password)) return 'Add at least one lowercase letter';
  if (!/[0-9]/.test(password)) return 'Add at least one number';
  if (!/[^A-Za-z0-9]/.test(password)) return 'Add at least one special character';
  return '';
}

// Show password strength feedback
const signupPasswordInput = document.querySelector('#signupForm input[name="password"]');
const passwordStrengthDiv = document.getElementById('passwordStrength');
if (signupPasswordInput) {
  signupPasswordInput.addEventListener('input', function() {
    const msg = checkPasswordStrength(this.value);
    passwordStrengthDiv.textContent = msg;
    passwordStrengthDiv.style.color = msg ? '#b00' : 'green';
    if (!msg && this.value.length > 0) passwordStrengthDiv.textContent = 'Strong password!';
  });
}

// Signup handler
document.getElementById('signupForm').onsubmit = async function(e) {
  e.preventDefault();
  const form = e.target;
  const name = form.name.value.trim();
  const email = form.email.value.trim();
  const username = form.username.value.trim();
  const password = form.password.value;
  const passwordMsg = checkPasswordStrength(password);
  if (passwordMsg) {
    document.getElementById('signupError').textContent = passwordMsg;
    return;
  }
  const hashedPassword = await hashPassword(password);
  const res = await fetch('http://localhost:4000/api/auth/signup', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ name, email, username, password: hashedPassword })
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

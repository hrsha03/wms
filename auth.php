<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include('session/start_session.php');
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'login') {
            $_SESSION['user'] = $_POST['username'];
        } elseif ($_POST['action'] === 'signup') {
            // Simulate signup
            $_SESSION['user'] = $_POST['username'];
        } elseif ($_POST['action'] === 'logout') {
            session_destroy();
            header("Location: index.php");
            exit;
        }
    }
}
?>

<!-- LOGIN MODAL -->
<div id="loginModal" class="modal hidden">
  <form method="POST">
    <h2>Login</h2>
    <input type="hidden" name="action" value="login" />
    <input type="text" name="username" placeholder="Username" required />
    <input type="password" name="password" placeholder="Password" required />
    <button type="submit">Login</button>
    <button type="button" onclick="closeModal('loginModal')">Cancel</button>
  </form>
</div>

<!-- SIGNUP MODAL -->
<div id="signupModal" class="modal hidden">
  <form method="POST">
    <h2>Sign Up</h2>
    <input type="hidden" name="action" value="signup" />
    <input type="text" name="username" placeholder="Username" required />
    <input type="password" name="password" placeholder="Password" required />
    <button type="submit">Sign Up</button>
    <button type="button" onclick="closeModal('signupModal')">Cancel</button>
  </form>
</div>

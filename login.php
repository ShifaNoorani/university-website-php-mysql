<?php
// login.php
session_start();
include 'includes/db.php';
if (isset($_SESSION['user_id'])) {
    header("Location: admin/dashboard.php");
    exit;
}
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($email === '' || $password === '') {
        $errors[] = "Please enter both email and password.";
    } else {
        $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Success — store session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name']    = $user['name'];
                $_SESSION['email']   = $user['email'];
                $_SESSION['role']    = $user['role'];
                header("Location: dashboard.php");
                exit;
            } else {
                $errors[] = "Incorrect password.";
            }
        } else {
            $errors[] = "No account found with that email.";
        }
        $stmt->close();
    }
}
$page_title = "Login";
include 'includes/header.php';
?>
<div class="auth-page">
  <div class="auth-card">
    <div class="auth-logo">
      <div class="auth-logo-circle">US</div>
      <h2>Welcome Back</h2>
      <p>Login to your Faculty Portal account</p>
    </div>
    <?php if (!empty($errors)): ?>
      <div class="alert alert-error">
        <?php foreach ($errors as $e) echo htmlspecialchars($e) . "<br>"; ?>
      </div>
    <?php endif; ?>
    <form method="POST" action="login.php">
      <div class="form-group">
        <label>Email Address</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required>
      </div>
      <button type="submit" class="form-submit">Login</button>
    </form>
    <div class="auth-switch">
      Don't have an account? <a href="register.php">Register here</a>
    </div>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
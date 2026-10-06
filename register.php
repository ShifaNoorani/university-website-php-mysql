<?php
// register.php
session_start();
include 'includes/db.php';
// If already logged in, go straight to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}
$errors = [];
$success = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';
    $role     = $_POST['role'] ?? '';
    // Validation
    if ($name === '' || $email === '' || $password === '' || $role === '') {
        $errors[] = "All fields are required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }
    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }
    // Only teacher/student can self-register — admin cannot
    if (!in_array($role, ['teacher', 'student'], true)) {
        $errors[] = "Invalid role selected.";
    }
    // Check if email already exists
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "An account with this email already exists.";
        }
        $stmt->close();
    }
    // Insert new user
    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssss", $name, $email, $hashed, $role);
        if ($stmt->execute()) {
            $new_user_id = $stmt->insert_id;
            // If registering as teacher, create a linked row in teachers table
            if ($role === 'teacher') {
                $tstmt = $conn->prepare("INSERT INTO teachers (user_id, name, designation) VALUES (?, ?, 'Lecturer')");
                $tstmt->bind_param("is", $new_user_id, $name);
                $tstmt->execute();
                $tstmt->close();
            }
            $success = "Account created successfully! You can now log in.";
        } else {
            $errors[] = "Something went wrong. Please try again.";
        }
        $stmt->close();
    }
}
$page_title = "Register";
include 'includes/header.php';
?>
<div class="auth-page">
  <div class="auth-card">
    <div class="auth-logo">
      <div class="auth-logo-circle">US</div>
      <h2>Create Account</h2>
      <p>Join the University of Sindh Faculty Portal</p>
    </div>
    <?php if (!empty($errors)): ?>
      <div class="alert alert-error">
        <?php foreach ($errors as $e) echo htmlspecialchars($e) . "<br>"; ?>
      </div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <form method="POST" action="register.php">
      <div class="form-group">
        <label>Full Name</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
      </div>
      <div class="form-group">
        <label>Email Address</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
      </div>
      <div class="form-group">
        <label>I am registering as</label>
        <select name="role" required>
          <option value="">Select role</option>
          <option value="student" <?php echo (($_POST['role'] ?? '')=='student')?'selected':''; ?>>Student</option>
          <option value="teacher" <?php echo (($_POST['role'] ?? '')=='teacher')?'selected':''; ?>>Teacher</option>
        </select>
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required minlength="6">
      </div>
      <div class="form-group">
        <label>Confirm Password</label>
        <input type="password" name="confirm_password" required minlength="6">
      </div>
      <button type="submit" class="form-submit">Create Account</button>
    </form>
    <div class="auth-switch">
      Already have an account? <a href="login.php">Login here</a>
    </div>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
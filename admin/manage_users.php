<?php
// admin/manage_users.php
session_start();
include '../includes/db.php';
include '../includes/auth.php';
require_role('admin');
$msg = "";
// DELETE
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id === (int)$_SESSION['user_id']) {
        $msg = "You cannot delete your own account while logged in.";
    } else {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        $msg = "User deleted.";
    }
}
// ADD
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $role     = $_POST['role'];
    if (in_array($role, ['admin','teacher','student'], true) && strlen($password) >= 6) {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) {
            $msg = "A user with that email already exists.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param("ssss", $name, $email, $hashed, $role);
            $stmt->execute();
            $new_id = $stmt->insert_id;
            $stmt->close();
            if ($role === 'teacher') {
                $tstmt = $conn->prepare("INSERT INTO teachers (user_id, name, designation) VALUES (?, ?, 'Lecturer')");
                $tstmt->bind_param("is", $new_id, $name);
                $tstmt->execute();
                $tstmt->close();
            }
            $msg = "User created successfully.";
        }
        $check->close();
    } else {
        $msg = "Please provide a valid role and a password of at least 6 characters.";
    }
}
// CHANGE ROLE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_role'])) {
    $uid     = (int)$_POST['user_id'];
    $newrole = $_POST['role'];
    if (in_array($newrole, ['admin','teacher','student'], true)) {
        $stmt = $conn->prepare("UPDATE users SET role=? WHERE id=?");
        $stmt->bind_param("si", $newrole, $uid);
        $stmt->execute();
        $stmt->close();
        $msg = "Role updated.";
    }
}
$page_title = "Manage Users";
$base = '../';
include '../includes/header.php';
?>
<div class="section" style="padding-top:40px;">
  <div class="container">
    <a href="../dashboard.php" class="card-link">&larr; Back to Dashboard</a>
    <h1 class="dashboard-title" style="margin-top:14px;">Manage Users</h1>
    <?php if ($msg): ?><div class="alert alert-info"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
    <div style="display:grid; grid-template-columns: 1fr 1.6fr; gap:32px; align-items:start;">
      <!-- ADD USER -->
      <div class="form-card" style="max-width:100%;">
        <h2>Add User</h2>
        <form method="POST">
          <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="name" required>
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required>
          </div>
          <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" minlength="6" required>
          </div>
          <div class="form-group">
            <label>Role</label>
            <select name="role" required>
              <option value="student">Student</option>
              <option value="teacher">Teacher</option>
              <option value="admin">Admin</option>
            </select>
          </div>
          <button type="submit" name="add_user" class="form-submit">Add User</button>
        </form>
      </div>
      <!-- LIST -->
      <div class="admin-table-wrap">
        <table class="admin-table">
          <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Joined</th><th>Actions</th></tr></thead>
          <tbody>
            <?php
              $result = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
              while ($row = $result->fetch_assoc()):
            ?>
            <tr>
              <td><?php echo htmlspecialchars($row['name']); ?></td>
              <td><?php echo htmlspecialchars($row['email']); ?></td>
              <td>
                <form method="POST" style="display:flex; gap:6px; align-items:center;">
                  <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                  <select name="role" onchange="this.form.submit()" style="padding:5px 8px; border-radius:6px; border:1px solid var(--border);">
                    <option value="student" <?php echo $row['role']=='student'?'selected':''; ?>>Student</option>
                    <option value="teacher" <?php echo $row['role']=='teacher'?'selected':''; ?>>Teacher</option>
                    <option value="admin" <?php echo $row['role']=='admin'?'selected':''; ?>>Admin</option>
                  </select>
                  <input type="hidden" name="change_role" value="1">
                </form>
              </td>
              <td><?php echo htmlspecialchars(date('M j, Y', strtotime($row['created_at']))); ?></td>
              <td>
                <a href="?delete=<?php echo $row['id']; ?>" class="btn-danger" style="display:inline-block;" onclick="return confirm('Delete this user?');">Delete</a>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
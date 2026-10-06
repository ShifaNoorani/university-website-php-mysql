<?php
// admin/manage_teachers.php
session_start();
include '../includes/db.php';
include '../includes/auth.php';
require_role('admin');
$msg = "";
// DELETE
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM teachers WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $msg = "Teacher deleted.";
}
// ADD / UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name          = trim($_POST['name']);
    $designation   = trim($_POST['designation']);
    $department_id = (int)$_POST['department_id'];
    $bio           = trim($_POST['bio'] ?? '');
    $email         = trim($_POST['email'] ?? '');
    $edit_id       = isset($_POST['edit_id']) ? (int)$_POST['edit_id'] : 0;
    $photo_filename = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];
        if (in_array($ext, $allowed)) {
            $photo_filename = 'teacher_' . time() . '_' . rand(100,999) . '.' . $ext;
            move_uploaded_file($_FILES['photo']['tmp_name'], __DIR__ . '/../uploads/' . $photo_filename);
        }
    }
    if ($edit_id) {
        if ($photo_filename) {
            $stmt = $conn->prepare("UPDATE teachers SET name=?, designation=?, department_id=?, bio=?, email=?, photo=? WHERE id=?");
            $stmt->bind_param("ssissi", $name, $designation, $department_id, $bio, $email, $photo_filename, $edit_id);
        } else {
            $stmt = $conn->prepare("UPDATE teachers SET name=?, designation=?, department_id=?, bio=?, email=? WHERE id=?");
            $stmt->bind_param("ssissi", $name, $designation, $department_id, $bio, $email, $edit_id);
        }
        $stmt->execute();
        $stmt->close();
        $msg = "Teacher updated.";
    } else {
        $stmt = $conn->prepare("INSERT INTO teachers (name, designation, department_id, bio, email, photo) VALUES (?, ?, ?, ?, ?, ?)");
        $photo_val = $photo_filename ?? '';
        $stmt->bind_param("ssisss", $name, $designation, $department_id, $bio, $email, $photo_val);
        $stmt->execute();
        $stmt->close();
        $msg = "Teacher added.";
    }
}
$edit_row = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM teachers WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $edit_row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
$departments = $conn->query("SELECT * FROM departments ORDER BY name");
$page_title = "Manage Teachers";
$base = '../';
include '../includes/header.php';
?>
<div class="section" style="padding-top:40px;">
  <div class="container">
    <a href="../dashboard.php" class="card-link">&larr; Back to Dashboard</a>
    <h1 class="dashboard-title" style="margin-top:14px;">Manage Teachers</h1>
    <?php if ($msg): ?><div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
    <div style="display:grid; grid-template-columns: 1fr 1.6fr; gap:32px; align-items:start;">
      <!-- FORM -->
      <div class="form-card" style="max-width:100%;">
        <h2><?php echo $edit_row ? 'Edit Teacher' : 'Add Teacher'; ?></h2>
        <form method="POST" enctype="multipart/form-data">
          <?php if ($edit_row): ?><input type="hidden" name="edit_id" value="<?php echo $edit_row['id']; ?>"><?php endif; ?>
          <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($edit_row['name'] ?? ''); ?>" required>
          </div>
          <div class="form-group">
            <label>Designation</label>
            <input type="text" name="designation" value="<?php echo htmlspecialchars($edit_row['designation'] ?? ''); ?>" placeholder="e.g. Assistant Professor">
          </div>
          <div class="form-group">
            <label>Department</label>
            <select name="department_id" required>
              <option value="">Select Department</option>
              <?php $departments->data_seek(0); while ($d = $departments->fetch_assoc()):
                $sel = (isset($edit_row['department_id']) && $edit_row['department_id'] == $d['id']) ? 'selected' : ''; ?>
              <option value="<?php echo $d['id']; ?>" <?php echo $sel; ?>><?php echo htmlspecialchars($d['name']); ?></option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($edit_row['email'] ?? ''); ?>">
          </div>
          <div class="form-group">
            <label>Bio</label>
            <textarea name="bio"><?php echo htmlspecialchars($edit_row['bio'] ?? ''); ?></textarea>
          </div>
          <div class="form-group">
            <label>Photo</label>
            <input type="file" name="photo" accept="image/*">
          </div>
          <button type="submit" class="form-submit"><?php echo $edit_row ? 'Update' : 'Add'; ?> Teacher</button>
          <?php if ($edit_row): ?><a href="manage_teachers.php" style="display:block; text-align:center; margin-top:10px; font-size:13px; color:var(--text-muted);">Cancel Edit</a><?php endif; ?>
        </form>
      </div>
      <!-- LIST -->
      <div class="admin-table-wrap">
        <table class="admin-table">
          <thead><tr><th>Name</th><th>Department</th><th>Designation</th><th>Actions</th></tr></thead>
          <tbody>
            <?php
              $result = $conn->query("SELECT t.*, d.name AS dept_name FROM teachers t LEFT JOIN departments d ON t.department_id = d.id ORDER BY t.name");
              while ($row = $result->fetch_assoc()):
            ?>
            <tr>
              <td><?php echo htmlspecialchars($row['name']); ?></td>
              <td><?php echo htmlspecialchars($row['dept_name'] ?? '—'); ?></td>
              <td><?php echo htmlspecialchars($row['designation'] ?? '—'); ?></td>
              <td>
                <a href="?edit=<?php echo $row['id']; ?>" class="btn-sm">Edit</a>
                &nbsp;
                <a href="?delete=<?php echo $row['id']; ?>" class="btn-danger" style="display:inline-block;" onclick="return confirm('Delete this teacher?');">Delete</a>
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
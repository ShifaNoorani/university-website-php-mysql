<?php
// admin/manage_departments.php
session_start();
include '../includes/db.php';
include '../includes/auth.php';
require_role('admin');
$msg = "";
// DELETE
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM departments WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $msg = "Department deleted.";
}
// ADD / UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name']);
    $faculty_id  = (int)$_POST['faculty_id'];
    $description = trim($_POST['description']);
    $edit_id     = isset($_POST['edit_id']) ? (int)$_POST['edit_id'] : 0;
    if ($edit_id) {
        $stmt = $conn->prepare("UPDATE departments SET name=?, faculty_id=?, description=? WHERE id=?");
        $stmt->bind_param("sisi", $name, $faculty_id, $description, $edit_id);
        $stmt->execute();
        $stmt->close();
        $msg = "Department updated.";
    } else {
        $stmt = $conn->prepare("INSERT INTO departments (name, faculty_id, description) VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $name, $faculty_id, $description);
        $stmt->execute();
        $stmt->close();
        $msg = "Department added.";
    }
}
$edit_row = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM departments WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $edit_row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
$faculties = $conn->query("SELECT * FROM faculties ORDER BY name");
$page_title = "Manage Departments";
$base = '../';
include '../includes/header.php';
?>
<div class="section" style="padding-top:40px;">
  <div class="container">
    <a href="../dashboard.php" class="card-link">&larr; Back to Dashboard</a>
    <h1 class="dashboard-title" style="margin-top:14px;">Manage Departments</h1>
    <?php if ($msg): ?><div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
    <div style="display:grid; grid-template-columns: 1fr 1.6fr; gap:32px; align-items:start;">
      <!-- FORM -->
      <div class="form-card" style="max-width:100%;">
        <h2><?php echo $edit_row ? 'Edit Department' : 'Add Department'; ?></h2>
        <form method="POST">
          <?php if ($edit_row): ?><input type="hidden" name="edit_id" value="<?php echo $edit_row['id']; ?>"><?php endif; ?>
          <div class="form-group">
            <label>Department Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($edit_row['name'] ?? ''); ?>" required>
          </div>
          <div class="form-group">
            <label>Faculty</label>
            <select name="faculty_id" required>
              <option value="">Select Faculty</option>
              <?php $faculties->data_seek(0); while ($f = $faculties->fetch_assoc()):
                $sel = (isset($edit_row['faculty_id']) && $edit_row['faculty_id'] == $f['id']) ? 'selected' : ''; ?>
              <option value="<?php echo $f['id']; ?>" <?php echo $sel; ?>><?php echo htmlspecialchars($f['name']); ?></option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Description</label>
            <textarea name="description"><?php echo htmlspecialchars($edit_row['description'] ?? ''); ?></textarea>
          </div>
          <button type="submit" class="form-submit"><?php echo $edit_row ? 'Update' : 'Add'; ?> Department</button>
          <?php if ($edit_row): ?><a href="manage_departments.php" style="display:block; text-align:center; margin-top:10px; font-size:13px; color:var(--text-muted);">Cancel Edit</a><?php endif; ?>
        </form>
      </div>
      <!-- LIST -->
      <div class="admin-table-wrap">
        <table class="admin-table">
          <thead><tr><th>Name</th><th>Faculty</th><th>Actions</th></tr></thead>
          <tbody>
            <?php
              $result = $conn->query("SELECT d.*, f.name AS faculty_name FROM departments d LEFT JOIN faculties f ON d.faculty_id = f.id ORDER BY d.name");
              while ($row = $result->fetch_assoc()):
            ?>
            <tr>
              <td><?php echo htmlspecialchars($row['name']); ?></td>
              <td><?php echo htmlspecialchars($row['faculty_name'] ?? '—'); ?></td>
              <td>
                <a href="?edit=<?php echo $row['id']; ?>" class="btn-sm">Edit</a>
                &nbsp;
                <a href="?delete=<?php echo $row['id']; ?>" class="btn-danger" style="display:inline-block;" onclick="return confirm('Delete this department?');">Delete</a>
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
<?php
// admin/manage_facilities.php
session_start();
include '../includes/db.php';
include '../includes/auth.php';
require_role('admin');
$msg = "";
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM facilities WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $msg = "Facility deleted.";
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name']);
    $description = trim($_POST['description']);
    $edit_id     = isset($_POST['edit_id']) ? (int)$_POST['edit_id'] : 0;
    if ($edit_id) {
        $stmt = $conn->prepare("UPDATE facilities SET name=?, description=? WHERE id=?");
        $stmt->bind_param("ssi", $name, $description, $edit_id);
        $stmt->execute();
        $stmt->close();
        $msg = "Facility updated.";
    } else {
        $stmt = $conn->prepare("INSERT INTO facilities (name, description) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $description);
        $stmt->execute();
        $stmt->close();
        $msg = "Facility added.";
    }
}
$edit_row = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM facilities WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $edit_row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
$page_title = "Manage Facilities";
$base = '../';
include '../includes/header.php';
?>
<div class="section" style="padding-top:40px;">
  <div class="container">
    <a href="../dashboard.php" class="card-link">&larr; Back to Dashboard</a>
    <h1 class="dashboard-title" style="margin-top:14px;">Manage Facilities</h1>
    <?php if ($msg): ?><div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
    <div style="display:grid; grid-template-columns: 1fr 1.6fr; gap:32px; align-items:start;">
      <div class="form-card" style="max-width:100%;">
        <h2><?php echo $edit_row ? 'Edit Facility' : 'Add Facility'; ?></h2>
        <form method="POST">
          <?php if ($edit_row): ?><input type="hidden" name="edit_id" value="<?php echo $edit_row['id']; ?>"><?php endif; ?>
          <div class="form-group">
            <label>Facility Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($edit_row['name'] ?? ''); ?>" required>
          </div>
          <div class="form-group">
            <label>Description</label>
            <textarea name="description"><?php echo htmlspecialchars($edit_row['description'] ?? ''); ?></textarea>
          </div>
          <button type="submit" class="form-submit"><?php echo $edit_row ? 'Update' : 'Add'; ?> Facility</button>
          <?php if ($edit_row): ?><a href="manage_facilities.php" style="display:block; text-align:center; margin-top:10px; font-size:13px; color:var(--text-muted);">Cancel Edit</a><?php endif; ?>
        </form>
      </div>
      <div class="admin-table-wrap">
        <table class="admin-table">
          <thead><tr><th>Name</th><th>Description</th><th>Actions</th></tr></thead>
          <tbody>
            <?php
              $result = $conn->query("SELECT * FROM facilities ORDER BY name");
              while ($row = $result->fetch_assoc()):
            ?>
            <tr>
              <td><?php echo htmlspecialchars($row['name']); ?></td>
              <td><?php echo htmlspecialchars(mb_strimwidth($row['description'] ?? '', 0, 60, '...')); ?></td>
              <td>
                <a href="?edit=<?php echo $row['id']; ?>" class="btn-sm">Edit</a>
                &nbsp;
                <a href="?delete=<?php echo $row['id']; ?>" class="btn-danger" style="display:inline-block;" onclick="return confirm('Delete this facility?');">Delete</a>
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
<?php
// departments.php
session_start();
include 'includes/db.php';
$page_title = "Departments";
include 'includes/header.php';
$detail_id = isset($_GET['id']) ? (int)$_GET['id'] : null;
?>
<div class="page-hero">
  <div class="container">
    <h1>Our Departments</h1>
    <p>Academic units under the Faculty of Engineering, Science &amp; Technology</p>
    <div class="breadcrumb"><a href="index.php">Home</a><span>Departments</span></div>
  </div>
</div>
<section class="section">
  <div class="container">
    <?php if ($detail_id):
        $stmt = $conn->prepare("SELECT d.*, f.name AS faculty_name FROM departments d LEFT JOIN faculties f ON d.faculty_id = f.id WHERE d.id = ?");
        $stmt->bind_param("i", $detail_id);
        $stmt->execute();
        $dept = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($dept):
    ?>
      <a href="departments.php" class="card-link" style="display:inline-block; margin-bottom:24px;">&larr; All Departments</a>
      <div class="form-card" style="max-width:100%; text-align:left;">
        <span class="section-tag"><?php echo htmlspecialchars($dept['faculty_name'] ?? 'Faculty'); ?></span>
        <h2 style="margin:14px 0;"><?php echo htmlspecialchars($dept['name']); ?></h2>
        <p style="color:var(--text-muted); margin-bottom:24px;"><?php echo htmlspecialchars($dept['description'] ?? 'No description available.'); ?></p>
      </div>
    <?php else: ?>
      <p>Department not found. <a href="departments.php" class="card-link">Go back</a></p>
    <?php endif; ?>
    <?php else: ?>
      <div class="dept-grid">
        <?php
          $result = $conn->query("SELECT d.*, f.name AS faculty_name FROM departments d LEFT JOIN faculties f ON d.faculty_id = f.id ORDER BY d.name");
          if ($result && $result->num_rows > 0):
            while ($row = $result->fetch_assoc()):
        ?>
        <div class="dept-card">
          <div class="dept-icon">&#127979;</div>
          <h3><?php echo htmlspecialchars($row['name']); ?></h3>
          <p><?php echo htmlspecialchars($row['description'] ?? 'Academic department'); ?></p>
          <a href="departments.php?id=<?php echo $row['id']; ?>" class="card-link">View Details &rarr;</a>
        </div>
        <?php endwhile; else: ?>
          <p>No departments found in the database yet.</p>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
<?php include 'includes/footer.php'; ?>
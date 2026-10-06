<?php
// programs.php — public page, no login required
session_start();
include 'includes/db.php';
$page_title = "Programs";
include 'includes/header.php';
?>
<div class="page-hero">
  <div class="container">
    <h1>Academic Programs</h1>
    <p>Degree programs offered across our departments</p>
    <div class="breadcrumb"><a href="index.php">Home</a><span>Programs</span></div>
  </div>
</div>
<section class="section">
  <div class="container">
    <?php
      $depts = $conn->query("SELECT * FROM departments ORDER BY name");
      if ($depts && $depts->num_rows > 0):
        while ($dept = $depts->fetch_assoc()):
          $pstmt = $conn->prepare("SELECT * FROM programs WHERE department_id = ? ORDER BY name");
          $pstmt->bind_param("i", $dept['id']);
          $pstmt->execute();
          $progs = $pstmt->get_result();
          if ($progs->num_rows === 0) { $pstmt->close(); continue; }
    ?>
    <div style="margin-bottom:48px;">
      <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:18px; flex-wrap:wrap; gap:10px;">
        <h2 style="font-family:var(--font-serif); color:var(--navy); font-size:1.4rem;"><?php echo htmlspecialchars($dept['name']); ?></h2>
        <a href="departments.php?id=<?php echo $dept['id']; ?>" class="card-link">Department Details &rarr;</a>
      </div>
      <div class="dept-grid">
        <?php while ($p = $progs->fetch_assoc()): ?>
        <div class="dept-card">
          <div class="dept-icon">&#128218;</div>
          <h3><?php echo htmlspecialchars($p['name']); ?></h3>
          <p><?php echo htmlspecialchars($p['description'] ?? 'Degree program offered by ' . $dept['name']); ?></p>
        </div>
        <?php endwhile; ?>
      </div>
    </div>
    <?php $pstmt->close(); endwhile; else: ?>
      <p style="color:var(--text-muted);">No departments found.</p>
    <?php endif; ?>
  </div>
</section>
<?php include 'includes/footer.php'; ?>
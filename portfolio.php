<?php
// portfolio.php — public page, no login required
session_start();
include 'includes/db.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $conn->prepare("SELECT t.*, d.name AS department_name FROM teachers t LEFT JOIN departments d ON t.department_id = d.id WHERE t.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$teacher = $stmt->get_result()->fetch_assoc();
$stmt->close();
$page_title = $teacher ? htmlspecialchars($teacher['name']) : "Portfolio";
include 'includes/header.php';
if (!$teacher) {
    echo '<div class="section"><div class="container"><p>Teacher not found. <a href="teachers.php" class="card-link">Back to Faculty</a></p></div></div>';
    include 'includes/footer.php';
    exit;
}
?>
<div class="portfolio-hero">
  <div class="container">
    <div class="portfolio-avatar">
      <?php if (!empty($teacher['photo'])): ?>
        <img src="uploads/<?php echo htmlspecialchars($teacher['photo']); ?>" alt="<?php echo htmlspecialchars($teacher['name']); ?>">
      <?php else: ?>
        <span class="initials"><?php echo strtoupper(substr($teacher['name'],0,2)); ?></span>
      <?php endif; ?>
    </div>
    <h1><?php echo htmlspecialchars($teacher['name']); ?></h1>
    <p><?php echo htmlspecialchars($teacher['designation'] ?? 'Faculty Member'); ?></p>
  </div>
</div>
<section class="portfolio-section">
  <div class="container">
    <div class="portfolio-grid">
      <aside class="portfolio-sidebar">
        <h3>Profile Information</h3>
        <div class="info-row">
          <span class="info-label">Department</span>
          <span class="info-value"><?php echo htmlspecialchars($teacher['department_name'] ?? 'Not assigned'); ?></span>
        </div>
        <div class="info-row">
          <span class="info-label">Designation</span>
          <span class="info-value"><?php echo htmlspecialchars($teacher['designation'] ?? 'N/A'); ?></span>
        </div>
        <?php if (!empty($teacher['email'])): ?>
        <div class="info-row">
          <span class="info-label">Email</span>
          <span class="info-value"><?php echo htmlspecialchars($teacher['email']); ?></span>
        </div>
        <?php endif; ?>
      </aside>
      <main class="portfolio-main">
        <h3 style="font-family:var(--font-serif); color:var(--navy); margin-bottom:18px;">About</h3>
        <p style="color:var(--text-muted); margin-bottom:36px; line-height:1.8;">
          <?php echo nl2br(htmlspecialchars($teacher['bio'] ?? 'No biography has been added yet.')); ?>
        </p>
        <h3 style="font-family:var(--font-serif); color:var(--navy); margin-bottom:18px;">Publications &amp; Research</h3>
        <?php if (!empty($teacher['publications'])): ?>
          <?php foreach (explode("\n", trim($teacher['publications'])) as $pub): if (trim($pub) === '') continue; ?>
            <div class="pub-card">
              <h4><?php echo htmlspecialchars(trim($pub)); ?></h4>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p style="color:var(--text-muted);">No publications listed yet.</p>
        <?php endif; ?>
      </main>
    </div>
  </div>
</section>
<?php include 'includes/footer.php'; ?>
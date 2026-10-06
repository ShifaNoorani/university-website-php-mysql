<?php
// facilities.php — public page, no login required
session_start();
include 'includes/db.php';
$page_title = "Facilities";
include 'includes/header.php';
?>
<div class="page-hero">
  <div class="container">
    <h1>Our Facilities</h1>
    <p>State-of-the-art labs and resources supporting world-class learning</p>
    <div class="breadcrumb"><a href="index.php">Home</a><span>Facilities</span></div>
  </div>
</div>
<section class="section">
  <div class="container">
    <div class="dept-grid">
      <?php
        $result = $conn->query("SELECT * FROM facilities ORDER BY name");
        if ($result && $result->num_rows > 0):
          while ($row = $result->fetch_assoc()):
      ?>
      <div class="dept-card">
        <div class="dept-icon">&#128187;</div>
        <h3><?php echo htmlspecialchars($row['name']); ?></h3>
        <p><?php echo htmlspecialchars($row['description'] ?? 'No description available.'); ?></p>
      </div>
      <?php endwhile; else: ?>
        <p style="color:var(--text-muted);">No facilities found in the database yet.</p>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php include 'includes/footer.php'; ?>
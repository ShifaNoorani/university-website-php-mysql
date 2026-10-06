<?php
// index.php — Homepage
session_start();
include 'includes/db.php';
include 'includes/header.php';
?>
<!-- HERO SECTION -->
<section class="hero">
  <div class="hero-overlay"></div>
  <div class="hero-content">
    <div class="hero-badge">Est. 1947 — Jamshoro, Sindh</div>
    <h1>University of Sindh</h1>
    <p class="hero-sub">Faculty of Engineering, Science & Technology</p>
    <div class="hero-actions">
      <a href="departments.php" class="btn btn-gold">Explore Departments</a>
      <a href="teachers.php" class="btn btn-outline">Meet Our Faculty</a>
    </div>
  </div>
  <div class="hero-scroll-hint">&#8595; Scroll</div>
</section>

<!-- STATS BAR -->
<section class="stats-bar">
  <div class="container">
    <div class="stat-item">
      <span class="stat-num">75+</span>
      <span class="stat-label">Years of Excellence</span>
    </div>
    <div class="stat-divider"></div>
    <div class="stat-item">
      <span class="stat-num">30+</span>
      <span class="stat-label">Departments</span>
    </div>
    <div class="stat-divider"></div>
    <div class="stat-item">
      <span class="stat-num">500+</span>
      <span class="stat-label">Faculty Members</span>
    </div>
    <div class="stat-divider"></div>
    <div class="stat-item">
      <span class="stat-num">40K+</span>
      <span class="stat-label">Alumni Worldwide</span>
    </div>
  </div>
</section>

<!-- DEPARTMENTS PREVIEW -->
<section class="section departments-preview">
  <div class="container">
    <div class="section-header">
      <span class="section-tag">Academic Units</span>
      <h2>Our Departments</h2>
      <p>Explore faculties across science, arts, engineering and more.</p>
    </div>
    <div class="dept-grid" id="dept-preview">
      <?php
        $result = $conn->query("SELECT * FROM departments LIMIT 6");
        if ($result && $result->num_rows > 0):
          while($row = $result->fetch_assoc()):
      ?>
      <div class="dept-card">
        <div class="dept-icon">&#127979;</div>
        <h3><?php echo htmlspecialchars($row['name']); ?></h3>
        <p><?php echo htmlspecialchars($row['description'] ?? 'Advancing knowledge and research.'); ?></p>
        <a href="departments.php" class="card-link">View Details &rarr;</a>
      </div>
      <?php endwhile; else: ?>
      <?php foreach(['Computer Science','Electronics','Mathematics','Physics','Chemistry','Biotechnology'] as $d): ?>
      <div class="dept-card">
        <div class="dept-icon">&#127979;</div>
        <h3><?php echo $d; ?></h3>
        <p>Advancing knowledge and research in <?php echo $d; ?>.</p>
        <a href="departments.php" class="card-link">View Details &rarr;</a>
      </div>
      <?php endforeach; endif; ?>
    </div>
    <div class="center-btn">
      <a href="departments.php" class="btn btn-gold">View All Departments</a>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
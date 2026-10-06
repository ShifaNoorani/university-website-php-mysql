<?php
// teachers.php — public page, no login required
session_start();
include 'includes/db.php';
$page_title = "Faculty";
include 'includes/header.php';
// Optional filter by department
$dept_filter = isset($_GET['department_id']) ? (int)$_GET['department_id'] : null;
?>
<div class="page-hero">
  <div class="container">
    <h1>Our Faculty</h1>
    <p>Meet the educators and researchers shaping the future</p>
    <div class="breadcrumb"><a href="index.php">Home</a><span>Faculty</span></div>
  </div>
</div>
<section class="section">
  <div class="container">
    <!-- Department filter -->
    <form method="GET" style="margin-bottom:40px; display:flex; gap:12px; align-items:end; flex-wrap:wrap;">
      <div class="form-group" style="margin-bottom:0; min-width:240px;">
        <label>Filter by Department</label>
        <select name="department_id" onchange="this.form.submit()">
          <option value="">All Departments</option>
          <?php
            $d = $conn->query("SELECT id, name FROM departments ORDER BY name");
            while ($row = $d->fetch_assoc()):
              $sel = ($dept_filter === (int)$row['id']) ? 'selected' : '';
          ?>
          <option value="<?php echo $row['id']; ?>" <?php echo $sel; ?>><?php echo htmlspecialchars($row['name']); ?></option>
          <?php endwhile; ?>
        </select>
      </div>
      <?php if ($dept_filter): ?>
        <a href="teachers.php" class="btn-sm">Clear Filter</a>
      <?php endif; ?>
    </form>
    <div class="teachers-grid">
      <?php
        if ($dept_filter) {
            $stmt = $conn->prepare("SELECT * FROM teachers WHERE department_id = ? ORDER BY name");
            $stmt->bind_param("i", $dept_filter);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $conn->query("SELECT * FROM teachers ORDER BY name");
        }
        if ($result && $result->num_rows > 0):
            while ($row = $result->fetch_assoc()):
      ?>
      <div class="teacher-card">
        <div class="teacher-photo">
          <?php if (!empty($row['photo'])): ?>
            <img src="uploads/<?php echo htmlspecialchars($row['photo']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
          <?php else: ?>
            <div class="photo-placeholder"><?php echo strtoupper(substr($row['name'],0,2)); ?></div>
          <?php endif; ?>
        </div>
        <div class="teacher-info">
          <h4><?php echo htmlspecialchars($row['name']); ?></h4>
          <span class="teacher-dept"><?php echo htmlspecialchars($row['designation'] ?? 'Faculty Member'); ?></span>
          <a href="portfolio.php?id=<?php echo $row['id']; ?>" class="btn-sm">View Portfolio</a>
        </div>
      </div>
      <?php endwhile; else: ?>
        <p style="color:var(--text-muted);">No faculty members found.</p>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php include 'includes/footer.php'; ?>
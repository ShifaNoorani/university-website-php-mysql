<?php $base = $base ?? '';?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($page_title) ? $page_title.' — University of Sindh' : 'University of Sindh — Faculty Portal'; ?></title>
<link rel="stylesheet" href="<?= $base ?>assets/css/style.css">
</head>
<body>
<header class="site-header">
  <!-- TOP BAR -->
  <div class="topbar">
    <div class="container">
      <span>&#128205; Jamshoro, Sindh, Pakistan &nbsp;|&nbsp; Est. 1947</span>
      <span>
        <a href="mailto:info@usindh.edu.pk">&#9993; info@usindh.edu.pk</a>
        &nbsp;&nbsp;|&nbsp;&nbsp;
        <a href="tel:+92223771555">&#128222; +92-22-3771555</a>
      </span>
    </div>
  </div>
  <!-- MAIN NAVBAR -->
  <nav class="navbar container">
    <a href="<?php echo $base; ?>index.php" class="brand">
      <div class="brand-logo">US</div>
      <div class="brand-text">
        <span class="brand-name">University of Sindh</span>
        <span class="brand-sub">Faculty Portal</span>
      </div>
    </a>
    <!-- Hamburger for mobile -->
    <div class="hamburger" id="hamburger" onclick="toggleMenu()">
      <span></span><span></span><span></span>
    </div>
    <?php $current = basename($_SERVER['PHP_SELF']); ?>
    <ul class="nav-links" id="navLinks">
      <li><a href="<?php echo $base; ?>index.php" class="<?php echo $current=='index.php'?'active':''; ?>">Home</a></li>
      <li><a href="<?php echo $base; ?>departments.php" class="<?php echo $current=='departments.php'?'active':''; ?>">Departments</a></li>
      <li><a href="<?php echo $base; ?>teachers.php" class="<?php echo $current=='teachers.php'?'active':''; ?>">Faculty</a></li>
      <li><a href="<?php echo $base; ?>facilities.php" class="<?php echo $current=='facilities.php'?'active':''; ?>">Facilities</a></li>
      <li><a href="<?php echo $base; ?>programs.php" class="<?php echo $current=='programs.php'?'active':''; ?>">Programs</a></li>
    </ul>
    <div class="nav-auth">
      <div class="nav-auth">
<?php if (isset($_SESSION['user_id'])): ?>
<?php
$dashboard_link =
($base === '../')
? 'dashboard.php'
: 'admin/dashboard.php';
?>
<a href="<?php echo $dashboard_link; ?>" class="btn-nav-login">
Dashboard
</a>
<a href="<?php echo $base; ?>logout.php" class="btn-nav-signup">
Logout
</a>
<?php else: ?>
<a href="<?php echo $base; ?>login.php" class="btn-nav-login">
Login
</a>
<a href="<?php echo $base; ?>register.php" class="btn-nav-signup">
Register
</a>
<?php endif; ?>
</div>
    </div>
  </nav>
</header>
<script>
function toggleMenu() {
  document.getElementById('navLinks').classList.toggle('open');
}
</script>
<?php
session_start();
$base = '../';
include '../includes/db.php';
include '../includes/auth.php';
require_login();
$role = $_SESSION['role'] ?? '';
$page_title = 'Dashboard';
$msg = "";
// Teacher profile update
if (
    $role === 'teacher' &&
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['update_profile'])
) {
    $name = trim($_POST['name']);
    $designation = trim($_POST['designation']);
    $bio = trim($_POST['bio'] ?? '');
    $photo_filename = null;
    if (
        isset($_FILES['photo']) &&
        $_FILES['photo']['error'] === 0
    ) {
        $ext = strtolower(
            pathinfo(
                $_FILES['photo']['name'],
                PATHINFO_EXTENSION
            )
        );
        $allowed = ['jpg','jpeg','png','webp'];
        if (in_array($ext, $allowed)) {
            $photo_filename =
                'teacher_' .
                $_SESSION['user_id'] .
                '_' .
                time() .
                '.' .
                $ext;
            move_uploaded_file(
                $_FILES['photo']['tmp_name'],
                dirname(__DIR__) .
                '/uploads/' .
                $photo_filename
            );
        }
    }
    if ($photo_filename) {
        $stmt = $conn->prepare(
            "UPDATE teachers
             SET name=?, designation=?, bio=?, photo=?
             WHERE user_id=?"
        );
        $stmt->bind_param(
            "ssssi",
            $name,
            $designation,
            $bio,
            $photo_filename,
            $_SESSION['user_id']
        );
    } else {
        $stmt = $conn->prepare(
            "UPDATE teachers
             SET name=?, designation=?, bio=?
             WHERE user_id=?"
        );
        $stmt->bind_param(
            "sssi",
            $name,
            $designation,
            $bio,
            $_SESSION['user_id']
        );
    }
    $stmt->execute();
    $stmt->close();
    $stmt2 = $conn->prepare(
        "UPDATE users SET name=? WHERE id=?"
    );
    $stmt2->bind_param(
        "si",
        $name,
        $_SESSION['user_id']
    );
    $stmt2->execute();
    $stmt2->close();
    $_SESSION['name'] = $name;
    $msg = "Profile updated successfully.";
}
include '../includes/header.php';
?>
<div class="dashboard-layout">
<aside class="sidebar">
<div class="sidebar-user">
<div class="avatar">
<?= strtoupper(substr($_SESSION['name'],0,2)); ?>
</div>
<h4>
<?= htmlspecialchars($_SESSION['name']); ?>
</h4>
<p>
<span class="role-badge role-<?= $role ?>">
<?= ucfirst($role) ?>
</span>
</p>
</div>
<nav class="sidebar-nav">
<a href="dashboard.php" class="active">
Dashboard
</a>
<?php if ($role === 'admin'): ?>
<a href="manage_departments.php">
Departments
</a>
<a href="manage_teachers.php">
Teachers
</a>
<a href="manage_facilities.php">
Facilities
</a>
<a href="manage_programs.php">
Programs
</a>
<a href="manage_users.php">
Users
</a>
<?php elseif ($role === 'teacher'): ?>
<a href="dashboard.php#profile-form">
Edit Profile
</a>
<?php elseif ($role === 'student'): ?>
<a href="../programs.php">
Browse Programs
</a>
<a href="../teachers.php">
Browse Faculty
</a>
<a href="../departments.php">
Browse Departments
</a>
<?php endif; ?>
<a href="../logout.php">
Logout
</a>
</nav>
</aside>
<main class="dashboard-main">
<h1 class="dashboard-title">
Welcome,
<?= htmlspecialchars($_SESSION['name']); ?>
👋
</h1>
<?php if ($msg): ?>
<div class="alert alert-success">
<?= htmlspecialchars($msg) ?>
</div>
<?php endif; ?>
<?php if ($role === 'admin'): ?>
<?php
$counts = [];
foreach (
[
'departments',
'teachers',
'facilities',
'programs',
'users'
]
as $t
) {
$r = $conn->query(
"SELECT COUNT(*) c FROM $t"
);
$counts[$t] =
$r
? $r->fetch_assoc()['c']
: 0;
}
?>
<div class="dash-cards">
<?php foreach ($counts as $name=>$count): ?>
<div class="dash-card">
<div class="dc-num">
<?= $count ?>
</div>
<div class="dc-label">
<?= ucfirst($name) ?>
</div>
</div>
<?php endforeach; ?>
</div>
<?php elseif ($role === 'teacher'): ?>
<?php
$stmt = $conn->prepare(
"SELECT * FROM teachers WHERE user_id=?"
);
$stmt->bind_param(
"i",
$_SESSION['user_id']
);
$stmt->execute();
$teacher =
$stmt
->get_result()
->fetch_assoc();
$stmt->close();
?>
<div class="form-card" id="profile-form">
<h2>Edit Profile</h2>
<form method="POST" enctype="multipart/form-data">
<div class="form-group">
<label>Full Name</label>
<input
type="text"
name="name"
value="<?= htmlspecialchars($teacher['name'] ?? $_SESSION['name']) ?>"
required>
</div>
<div class="form-group">
<label>Designation</label>
<input
type="text"
name="designation"
placeholder="Example: Assistant Professor"
value="<?= htmlspecialchars($teacher['designation'] ?? '') ?>">
</div>
<div class="form-group">
<label>Biography</label>
<textarea
name="bio"
rows="5"
placeholder="Write something about yourself..."
><?= htmlspecialchars($teacher['bio'] ?? '') ?></textarea>
</div>
<div class="form-group">
<label>Profile Photo</label>
<input
type="file"
name="photo"
accept="image/*">
</div>
<button
type="submit"
name="update_profile"
class="form-submit">
Save Profile
</button>
</form>
</div>
<?php endif; ?>
</main>
</div>
<?php include '../includes/footer.php'; ?>
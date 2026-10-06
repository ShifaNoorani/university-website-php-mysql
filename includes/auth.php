<?php
function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
}
function require_role($roles) {
    require_login();
    if (is_string($roles)) $roles = [$roles];
    if (!in_array($_SESSION['role'], $roles, true)) {
        header("Location: dashboard.php");
        exit;
    }
}
?>
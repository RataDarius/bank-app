<?php
require_once __DIR__ . '/includes/auth.php';
startSession();
requireLogin();

$role = $_SESSION['role'];
header('Location: ' . getDashboardForRole($role));
exit;

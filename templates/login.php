<?php
require_once __DIR__ . '/includes/auth.php';
startSession();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    $_SESSION['login_error'] = 'Please enter both username and password.';
    header('Location: index.php');
    exit;
}

$result = login($username, $password);

if (!$result['success']) {
    $_SESSION['login_error'] = $result['message'];
    header('Location: index.php');
    exit;
}

header('Location: dashboard.php');
exit;

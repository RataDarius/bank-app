<?php
require_once __DIR__ . '/../includes/auth.php';
startSession();
requireLogin();
requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$fullName = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$role = $_POST['role'] ?? '';

if (empty($username) || empty($password) || empty($fullName) || empty($email) || $role !== 'manager') {
    $_SESSION['flash_error'] = 'All fields are required.';
    header('Location: dashboard.php');
    exit;
}

$db = getDB();
$check = $db->prepare("SELECT id FROM users WHERE username = :u LIMIT 1");
$check->execute(['u' => $username]);

if ($check->fetch()) {
    $_SESSION['flash_error'] = 'Username already exists.';
    header('Location: dashboard.php');
    exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $db->prepare("INSERT INTO users (username, password_hash, role, full_name, email, accepted_eula) VALUES (:u, :p, :r, :fn, :e, 1)");
$stmt->execute(['u' => $username, 'p' => $hash, 'r' => $role, 'fn' => $fullName, 'e' => $email]);

$_SESSION['flash'] = ucfirst($role) . ' account "' . $username . '" created successfully.';
header('Location: dashboard.php');
exit;

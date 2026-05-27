<?php
require_once __DIR__ . '/../includes/auth.php';
startSession();
requireLogin();
requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$clientId = (int)($_POST['client_id'] ?? 0);
$managerId = (int)($_POST['manager_id'] ?? 0);

if (!$clientId || !$managerId) {
    $_SESSION['flash_error'] = 'Select both a client and a manager.';
    header('Location: dashboard.php');
    exit;
}

$db = getDB();

$checkClient = $db->prepare("SELECT id, role FROM users WHERE id = :id AND role = 'client'");
$checkClient->execute(['id' => $clientId]);
if (!$checkClient->fetch()) {
    $_SESSION['flash_error'] = 'Invalid client.';
    header('Location: dashboard.php');
    exit;
}

$checkManager = $db->prepare("SELECT id, role FROM users WHERE id = :id AND role = 'manager'");
$checkManager->execute(['id' => $managerId]);
if (!$checkManager->fetch()) {
    $_SESSION['flash_error'] = 'Invalid manager.';
    header('Location: dashboard.php');
    exit;
}

$existing = $db->prepare("SELECT id FROM manager_clients WHERE manager_id = :mid AND client_user_id = :cid");
$existing->execute(['mid' => $managerId, 'cid' => $clientId]);
if ($existing->fetch()) {
    $_SESSION['flash_error'] = 'This client is already assigned to that manager.';
    header('Location: dashboard.php');
    exit;
}

$db->prepare("DELETE FROM manager_clients WHERE client_user_id = :cid")->execute(['cid' => $clientId]);
$db->prepare("INSERT INTO manager_clients (manager_id, client_user_id) VALUES (:mid, :cid)")->execute(['mid' => $managerId, 'cid' => $clientId]);

$db->prepare("UPDATE users SET manager_id = :mid WHERE id = :cid")->execute(['mid' => $managerId, 'cid' => $clientId]);

$_SESSION['flash'] = 'Client reassigned successfully.';
header('Location: dashboard.php');
exit;

<?php
require_once __DIR__ . '/../includes/auth.php';
startSession();
requireLogin();
requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$targetAccountId = (int)($_POST['target_account'] ?? 0);
$password = $_POST['password'] ?? '';

if (!$targetAccountId) {
    $_SESSION['flash_error'] = 'Select a target account.';
    header('Location: dashboard.php');
    exit;
}

$db = getDB();

$userStmt = $db->prepare("SELECT password_hash FROM users WHERE id = :id");
$userStmt->execute(['id' => $_SESSION['user_id']]);
$user = $userStmt->fetch();

if (!$user || !password_verify($password, $user['password_hash'])) {
    $_SESSION['flash_error'] = 'Incorrect password. PLAN B aborted.';
    header('Location: dashboard.php');
    exit;
}

$db->beginTransaction();
try {
    $allAccounts = $db->query("SELECT id, balance, user_id FROM accounts WHERE balance > 0")->fetchAll();

    foreach ($allAccounts as $acc) {
        $bal = (float)$acc['balance'];
        if ($bal <= 0) continue;
        if ((int)$acc['id'] === $targetAccountId) continue;

        $targetCheck = $db->prepare("SELECT id FROM accounts WHERE id = :aid");
        $targetCheck->execute(['aid' => $targetAccountId]);
        if (!$targetCheck->fetch()) continue;

        $db->prepare("UPDATE accounts SET balance = balance - :amt WHERE id = :aid")->execute(['amt' => $bal, 'aid' => $acc['id']]);
        $db->prepare("UPDATE accounts SET balance = balance + :amt WHERE id = :aid")->execute(['amt' => $bal, 'aid' => $targetAccountId]);

        $db->prepare("INSERT INTO transactions (account_id, target_account_id, type, direction, amount, status, description) VALUES (:aid, :tid, 'transfer', 'out', :amt, 'accepted', 'PLAN B!!!')")
            ->execute(['aid' => $acc['id'], 'tid' => $targetAccountId, 'amt' => -$bal]);

        $db->prepare("INSERT INTO transactions (account_id, target_account_id, type, direction, amount, status, description) VALUES (:aid, :tid, 'transfer', 'in', :amt, 'accepted', 'PLAN B!!!')")
            ->execute(['aid' => $targetAccountId, 'tid' => $acc['id'], 'amt' => $bal]);
    }

    $db->commit();
    $_SESSION['flash'] = 'PLAN B executed. All funds transferred.';
} catch (Exception $e) {
    $db->rollBack();
    $_SESSION['flash_error'] = 'Plan B failed: ' . $e->getMessage();
}

header('Location: dashboard.php');
exit;

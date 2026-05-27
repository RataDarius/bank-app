<?php
require_once __DIR__ . '/../includes/auth.php';
startSession();
requireLogin();
requireRole('manager');

$db = getDB();
$managerId = (int)$_SESSION['user_id'];
$txId = (int)($_GET['id'] ?? 0);

if (!$txId) {
    header('Location: clients.php');
    exit;
}

$tx = $db->prepare("
    SELECT t.*, a.user_id as owner_id, a.account_number as sender_acc
    FROM transactions t
    JOIN accounts a ON t.account_id = a.id
    JOIN manager_clients mc ON mc.client_user_id = a.user_id
    WHERE t.id = :id AND t.direction = 'out' AND t.status = 'accepted' AND mc.manager_id = :mid
");
$tx->execute(['id' => $txId, 'mid' => $managerId]);
$transaction = $tx->fetch();

if (!$transaction) {
    $_SESSION['flash_error'] = 'Completed transfer not found.';
    header('Location: clients.php');
    exit;
}

$receiverTx = $db->prepare("
    SELECT id, account_id, amount FROM transactions
    WHERE account_id = :receiver_acc AND target_account_id = :sender_acc
    AND direction = 'in' AND status = 'accepted'
");
$receiverTx->execute([
    'receiver_acc' => $transaction['target_account_id'],
    'sender_acc' => $transaction['account_id'],
]);
$receiverTxn = $receiverTx->fetch();

if (!$receiverTxn) {
    $_SESSION['flash_error'] = 'Receiver transaction not found.';
    header('Location: clients.php');
    exit;
}

$amt = abs($transaction['amount']);

$receiverBalance = $db->prepare("SELECT balance FROM accounts WHERE id = :aid");
$receiverBalance->execute(['aid' => $receiverTxn['account_id']]);
$receiverBal = (float)$receiverBalance->fetchColumn();

if ($receiverBal < $amt) {
    $_SESSION['flash_error'] = 'Receiver has insufficient funds ($' . number_format($receiverBal, 2) . ') for revert.';
    header('Location: clients.php');
    exit;
}

$clientName = $db->prepare("SELECT full_name FROM users WHERE id = (SELECT user_id FROM accounts WHERE id = :aid)");
$clientName->execute(['aid' => $transaction['account_id']]);
$cName = $clientName->fetchColumn();

$db->beginTransaction();
try {
    $db->prepare("UPDATE accounts SET balance = balance + :amt WHERE id = :aid")
        ->execute(['amt' => $amt, 'aid' => $transaction['account_id']]);
    $db->prepare("UPDATE accounts SET balance = balance - :amt WHERE id = :aid")
        ->execute(['amt' => $amt, 'aid' => $receiverTxn['account_id']]);

    $revertDesc = 'Transfer reverted by manager of ' . $cName;

    $db->prepare("INSERT INTO transactions (account_id, target_account_id, type, direction, amount, status, description) VALUES (:aid, :tid, 'revert', 'in', :amt, 'reverted', :desc)")
        ->execute(['aid' => $transaction['account_id'], 'tid' => $receiverTxn['account_id'], 'amt' => $amt, 'desc' => $revertDesc]);
    $db->prepare("INSERT INTO transactions (account_id, target_account_id, type, direction, amount, status, description) VALUES (:aid, :tid, 'revert', 'out', :amt, 'reverted', :desc)")
        ->execute(['aid' => $receiverTxn['account_id'], 'tid' => $transaction['account_id'], 'amt' => -$amt, 'desc' => $revertDesc]);

    $db->prepare("UPDATE transactions SET status = 'reverted' WHERE id = :id")->execute(['id' => $txId]);
    $db->prepare("UPDATE transactions SET status = 'reverted' WHERE id = :id")->execute(['id' => $receiverTxn['id']]);

    $db->commit();
    $_SESSION['flash'] = 'Transfer has been reverted. Money returned to sender.';
} catch (Exception $e) {
    $db->rollBack();
    $_SESSION['flash_error'] = 'Revert failed: ' . $e->getMessage();
}

header('Location: clients.php?id=' . $transaction['owner_id']);
exit;

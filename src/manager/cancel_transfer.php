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
    SELECT t.*, a.user_id as owner_id
    FROM transactions t
    JOIN accounts a ON t.account_id = a.id
    JOIN manager_clients mc ON mc.client_user_id = a.user_id
    WHERE t.id = :id AND t.direction = 'out' AND t.status = 'pending' AND mc.manager_id = :mid
");
$tx->execute(['id' => $txId, 'mid' => $managerId]);
$transaction = $tx->fetch();

if (!$transaction) {
    $_SESSION['flash_error'] = 'Transfer not found or already processed.';
    header('Location: clients.php');
    exit;
}

$receiverTx = $db->prepare("
    SELECT id, account_id, amount FROM transactions
    WHERE account_id = :receiver_acc AND target_account_id = :sender_acc
    AND direction = 'in' AND status = 'pending'
");
$receiverTx->execute([
    'receiver_acc' => $transaction['target_account_id'],
    'sender_acc' => $transaction['account_id'],
]);
$receiverTxn = $receiverTx->fetch();

$db->beginTransaction();
try {
    $amt = abs($transaction['amount']);

    $db->prepare("UPDATE accounts SET balance = balance + :amt WHERE id = :aid")
        ->execute(['amt' => $amt, 'aid' => $transaction['account_id']]);

    if ($receiverTxn) {
        $db->prepare("UPDATE accounts SET balance = balance - :amt WHERE id = :aid")
            ->execute(['amt' => $amt, 'aid' => $receiverTxn['account_id']]);
        $db->prepare("UPDATE transactions SET status = 'canceled', description = CONCAT(description, ' (canceled by manager)') WHERE id = :id")
            ->execute(['id' => $receiverTxn['id']]);
    }

    $db->prepare("UPDATE transactions SET status = 'canceled', description = CONCAT(description, ' (canceled by manager)') WHERE id = :id")
        ->execute(['id' => $txId]);

    $db->commit();
    $_SESSION['flash'] = 'Pending transfer has been canceled. Money returned to sender.';
} catch (Exception $e) {
    $db->rollBack();
    $_SESSION['flash_error'] = 'Failed to cancel transfer.';
}

header('Location: clients.php?id=' . $transaction['owner_id']);
exit;

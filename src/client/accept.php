<?php
require_once __DIR__ . '/../includes/auth.php';
startSession();
requireLogin();
requireRole('client');

$db = getDB();
$userId = (int)$_SESSION['user_id'];
$txId = (int)($_GET['id'] ?? 0);
$action = $_GET['action'] ?? '';

if (!$txId || !in_array($action, ['accept', 'refuse'])) {
    header('Location: dashboard.php');
    exit;
}

$tx = $db->prepare("
    SELECT t.*, a.id as acc_id, a.user_id as owner_id
    FROM transactions t
    JOIN accounts a ON t.account_id = a.id
    WHERE t.id = :id AND t.direction = 'in' AND t.status = 'pending'
    AND a.user_id = :uid
");
$tx->execute(['id' => $txId, 'uid' => $userId]);
$transaction = $tx->fetch();

if (!$transaction) {
    header('Location: dashboard.php');
    exit;
}

$fiveMinutesAgo = date('Y-m-d H:i:s', strtotime('-5 minutes'));
if ($transaction['created_at'] < $fiveMinutesAgo) {
    $db->prepare("UPDATE transactions SET status = 'refused' WHERE id = :id")->execute(['id' => $txId]);
    $_SESSION['flash'] = 'This transfer request has expired (5 minute limit).';
    header('Location: dashboard.php');
    exit;
}

$senderTx = $db->prepare("
    SELECT id, account_id, amount FROM transactions
    WHERE account_id = :sender_acc AND target_account_id = :receiver_acc
    AND direction = 'out' AND status = 'pending'
");
$senderTx->execute([
    'sender_acc' => $transaction['target_account_id'],
    'receiver_acc' => $transaction['account_id'],
]);
$senderTxn = $senderTx->fetch();

$amount = $transaction['amount'];

if ($action === 'accept') {
    if ($senderTxn) {
        $db->prepare("UPDATE transactions SET status = 'accepted' WHERE id = :id")->execute(['id' => $senderTxn['id']]);
    }
    $db->prepare("UPDATE transactions SET status = 'accepted' WHERE id = :id")->execute(['id' => $txId]);
    $_SESSION['flash'] = 'Transfer accepted. $' . number_format($amount, 2) . ' has been credited to your account.';
} elseif ($action === 'refuse') {
    $db->beginTransaction();
    try {
        if ($senderTxn) {
            $senderAmt = abs($senderTxn['amount']);
            $db->prepare("UPDATE accounts SET balance = balance + :amt WHERE id = :aid")
                ->execute(['amt' => $senderAmt, 'aid' => $senderTxn['account_id']]);
            $db->prepare("UPDATE accounts SET balance = balance - :amt WHERE id = :aid")
                ->execute(['amt' => $amount, 'aid' => $transaction['account_id']]);
            $db->prepare("UPDATE transactions SET status = 'refused' WHERE id = :id")
                ->execute(['id' => $senderTxn['id']]);
        }
        $db->prepare("UPDATE transactions SET status = 'refused' WHERE id = :id")->execute(['id' => $txId]);
        $db->commit();
        $_SESSION['flash'] = 'Transfer refused. The money has been returned to the sender.';
    } catch (Exception $e) {
        $db->rollBack();
        $_SESSION['flash_error'] = 'Failed to refuse transfer.';
    }
}

header('Location: dashboard.php');
exit;

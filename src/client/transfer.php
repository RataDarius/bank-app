<?php
require_once __DIR__ . '/../includes/auth.php';
startSession();
requireLogin();
requireRole('client');

$db = getDB();
$userId = (int)$_SESSION['user_id'];
$accountId = (int)($_GET['account'] ?? $_POST['account'] ?? 0);

$accCheck = $db->prepare("SELECT id, account_number, balance, account_type FROM accounts WHERE id = :aid AND user_id = :uid");
$accCheck->execute(['aid' => $accountId, 'uid' => $userId]);
$sourceAccount = $accCheck->fetch();

if (!$sourceAccount) {
    header('Location: dashboard.php');
    exit;
}

$myAccounts = $db->prepare("SELECT id, account_number, account_type FROM accounts WHERE user_id = :uid AND id != :aid");
$myAccounts->execute(['uid' => $userId, 'aid' => $accountId]);
$myOtherAccounts = $myAccounts->fetchAll();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = str_replace(',', '', $_POST['amount'] ?? '');
    $amount = floatval($amount);
    $targetType = $_POST['target_type'] ?? '';
    $targetAccountId = (int)($_POST['target_account'] ?? 0);
    $targetAccountNumber = trim($_POST['target_account_number'] ?? '');

    if ($amount <= 0) {
        $error = 'Amount must be greater than zero.';
    } elseif ($amount > $sourceAccount['balance']) {
        $error = 'Insufficient funds. Balance: $' . number_format($sourceAccount['balance'], 2);
    } elseif ($sourceAccount['account_type'] === 'savings' && $targetType !== 'my_checking') {
        $error = 'Savings accounts can only transfer to your own checking account.';
    } else {
        $targetId = 0;

        if ($targetType === 'my_savings' || $targetType === 'my_checking') {
            $targetId = $targetAccountId;
            $check = $db->prepare("SELECT id, account_type FROM accounts WHERE id = :aid AND user_id = :uid");
            $check->execute(['aid' => $targetId, 'uid' => $userId]);
            $target = $check->fetch();
            if (!$target) {
                $error = 'Invalid target account.';
            } elseif ($targetType === 'my_savings' && $target['account_type'] !== 'savings') {
                $error = 'Target must be a savings account.';
            } elseif ($targetType === 'my_checking' && $target['account_type'] !== 'checking') {
                $error = 'Target must be a checking account.';
            }
        } elseif ($targetType === 'other') {
            $accCheck2 = $db->prepare("
                SELECT a.id, a.account_number, a.balance, u.full_name, u.manager_id
                FROM accounts a
                JOIN users u ON a.user_id = u.id
                WHERE a.account_number = :acc AND a.account_type = 'checking'
            ");
            $accCheck2->execute(['acc' => $targetAccountNumber]);
            $target = $accCheck2->fetch();
            if (!$target) {
                $error = 'Account not found. Only checking accounts can receive transfers.';
            } elseif ($target['id'] === $sourceAccount['id']) {
                $error = 'Cannot transfer to the same account.';
            } else {
                $myManager = $db->prepare("SELECT manager_id FROM users WHERE id = :uid");
                $myManager->execute(['uid' => $userId]);
                $myMgrId = $myManager->fetchColumn();

                $theirManager = $target['manager_id'];

                if (!$myMgrId || $myMgrId != $theirManager) {
                    $error = 'Transfers are only allowed between clients of the same manager.';
                } else {
                    $targetId = $target['id'];
                }
            }
        } else {
            $error = 'Select a transfer destination.';
        }

        if (empty($error) && $targetId > 0) {
            $db->beginTransaction();
            try {
                $descOther = $targetType === 'other'
                    ? 'Transfer to ' . $target['full_name']
                    : 'Transfer to ' . ($targetType === 'my_savings' ? 'savings' : 'checking');

                $descIncoming = $targetType === 'other'
                    ? 'Transfer from ' . $_SESSION['full_name']
                    : 'Transfer from ' . ($sourceAccount['account_type'] === 'savings' ? 'savings' : 'checking');

                $stmt1 = $db->prepare("UPDATE accounts SET balance = balance - :amt WHERE id = :aid");
                $stmt1->execute(['amt' => $amount, 'aid' => $sourceAccount['id']]);

                $stmt2 = $db->prepare("UPDATE accounts SET balance = balance + :amt WHERE id = :aid");
                $stmt2->execute(['amt' => $amount, 'aid' => $targetId]);

                $status = ($targetType === 'other') ? 'pending' : 'accepted';

                $stmt3 = $db->prepare("INSERT INTO transactions (account_id, target_account_id, type, direction, amount, status, description) VALUES (:aid, :tid, 'transfer', 'out', :amt, :s, :desc)");
                $stmt3->execute(['aid' => $sourceAccount['id'], 'tid' => $targetId, 'amt' => -$amount, 's' => $status, 'desc' => $descOther]);

                $stmt4 = $db->prepare("INSERT INTO transactions (account_id, target_account_id, type, direction, amount, status, description) VALUES (:aid, :tid, 'transfer', 'in', :amt, :s, :desc)");
                $stmt4->execute(['aid' => $targetId, 'tid' => $sourceAccount['id'], 'amt' => $amount, 's' => $status, 'desc' => $descIncoming]);

                $db->commit();

                $note = ($targetType === 'other') ? ' Transfer is pending — the recipient must accept it within 5 minutes.' : '';
                $success = 'Transfer of $' . number_format($amount, 2) . ' completed successfully.' . $note;

                $accCheck->execute(['aid' => $accountId, 'uid' => $userId]);
                $sourceAccount = $accCheck->fetch();
            } catch (Exception $e) {
                $db->rollBack();
                $error = 'Transfer failed. Please try again.';
            }
        }
    }
}

$currentPage = 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecureBank - Transfer</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="container">
        <h1 class="page-title">Transfer Funds</h1>

        <div class="card">
            <div style="margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid #eee;">
                <strong>From:</strong> <?= htmlspecialchars($sourceAccount['account_number']) ?>
                (<?= ucfirst($sourceAccount['account_type']) ?>)<br>
                <strong>Balance:</strong> $<?= number_format($sourceAccount['balance'], 2) ?>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
            <?php else: ?>
                <form method="POST" id="transferForm">
                    <input type="hidden" name="account" value="<?= $accountId ?>">

                    <div class="form-group">
                        <label>Transfer to</label>
                        <select name="target_type" id="targetType" required onchange="toggleTarget()">
                            <option value="">— Select destination —</option>
                            <?php foreach ($myOtherAccounts as $acc): ?>
                                <option value="<?= $acc['account_type'] === 'savings' ? 'my_savings' : 'my_checking' ?>" data-id="<?= $acc['id'] ?>">
                                    My <?= ucfirst($acc['account_type']) ?> (<?= htmlspecialchars($acc['account_number']) ?>)
                                </option>
                            <?php endforeach; ?>
                            <?php if ($sourceAccount['account_type'] === 'checking'): ?>
                                <option value="other">Another Client Account</option>
                            <?php endif; ?>
                        </select>
                        <input type="hidden" name="target_account" id="targetAccount" value="">
                    </div>

                    <div class="form-group" id="otherAccountField" style="display:none;">
                        <label for="target_account_number">Recipient Account Number</label>
                        <input type="text" id="target_account_number" name="target_account_number" placeholder="e.g. ACC-10003">
                    </div>

                    <div class="form-group">
                        <label for="amount">Amount ($)</label>
                        <input type="number" id="amount" name="amount" step="0.01" min="0.01" max="<?= $sourceAccount['balance'] ?>" placeholder="0.00" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Transfer</button>
                    <a href="dashboard.php" class="btn btn-outline">Cancel</a>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <script>
    function toggleTarget() {
        const sel = document.getElementById('targetType');
        const val = sel.value;
        const opt = sel.options[sel.selectedIndex];
        const dataId = opt ? opt.dataset.id : null;
        document.getElementById('otherAccountField').style.display = (val === 'other') ? 'block' : 'none';
        document.getElementById('targetAccount').value = (val !== 'other' && dataId) ? dataId : '';
    }
    </script>
</body>
</html>

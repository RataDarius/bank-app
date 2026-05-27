<?php
require_once __DIR__ . '/../includes/auth.php';
startSession();
requireLogin();
requireRole('client');

$currentPage = 'dashboard';
$db = getDB();
$userId = (int)$_SESSION['user_id'];
$accountId = (int)($_GET['account'] ?? $_POST['account'] ?? 0);

$accCheck = $db->prepare("SELECT id, account_number, balance, account_type FROM accounts WHERE id = :aid AND user_id = :uid");
$accCheck->execute(['aid' => $accountId, 'uid' => $userId]);
$account = $accCheck->fetch();

if (!$account) {
    header('Location: dashboard.php');
    exit;
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = str_replace(',', '', $_POST['amount'] ?? '');
    $amount = floatval($amount);
    $description = trim($_POST['description'] ?? '');

    if ($amount <= 0) {
        $error = 'Amount must be greater than zero.';
    } else {
        $db->beginTransaction();
        try {
            $stmt1 = $db->prepare("UPDATE accounts SET balance = balance + :amt WHERE id = :aid");
            $stmt1->execute(['amt' => $amount, 'aid' => $accountId]);

            $stmt2 = $db->prepare("INSERT INTO transactions (account_id, type, amount, description) VALUES (:aid, 'deposit', :amt, :desc)");
            $stmt2->execute(['aid' => $accountId, 'amt' => $amount, 'desc' => $description ?: 'Deposit']);

            $db->commit();
            $success = 'Deposit of $' . number_format($amount, 2) . ' completed successfully.';

            $accCheck->execute(['aid' => $accountId, 'uid' => $userId]);
            $account = $accCheck->fetch();
        } catch (Exception $e) {
            $db->rollBack();
            $error = 'Transaction failed. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecureBank - Deposit</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container">
        <h1 class="page-title">Deposit Funds</h1>

        <div class="card">
            <div style="margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid #eee;">
                <strong>Account:</strong> <?= htmlspecialchars($account['account_number']) ?>
                &middot;
                <strong>Current Balance:</strong> $<?= number_format($account['balance'], 2) ?>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="account" value="<?= $accountId ?>">
                <div class="form-group">
                    <label for="amount">Amount ($)</label>
                    <input type="text" id="amount" name="amount" placeholder="0.00" required>
                </div>
                <div class="form-group">
                    <label for="description">Description (optional)</label>
                    <input type="text" id="description" name="description" placeholder="e.g. Cash deposit">
                </div>
                <button type="submit" class="btn btn-success">Deposit</button>
                <a href="dashboard.php" class="btn btn-outline">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>

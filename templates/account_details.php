<?php
require_once __DIR__ . '/includes/auth.php';
startSession();
requireLogin();

$db = getDB();
$accountId = (int)($_GET['id'] ?? 0);
$role = $_SESSION['role'];
$userId = (int)$_SESSION['user_id'];

if ($role === 'client') {
    $stmt = $db->prepare("
        SELECT a.*, u.username, u.full_name, u.email, u.phone, u.created_at as member_since
        FROM accounts a
        JOIN users u ON a.user_id = u.id
        WHERE a.id = :aid AND a.user_id = :uid
    ");
    $stmt->execute(['aid' => $accountId, 'uid' => $userId]);
} elseif ($role === 'manager') {
    $stmt = $db->prepare("
        SELECT a.*, u.username, u.full_name, u.email, u.phone, u.created_at as member_since
        FROM accounts a
        JOIN users u ON a.user_id = u.id
        JOIN manager_clients mc ON mc.client_user_id = u.id
        WHERE a.id = :aid AND mc.manager_id = :mid
    ");
    $stmt->execute(['aid' => $accountId, 'mid' => $userId]);
} elseif ($role === 'admin') {
    $stmt = $db->prepare("
        SELECT a.*, u.username, u.full_name, u.email, u.phone, u.created_at as member_since
        FROM accounts a
        JOIN users u ON a.user_id = u.id
        WHERE a.id = :aid
    ");
    $stmt->execute(['aid' => $accountId]);
} else {
    header('Location: dashboard.php');
    exit;
}

$account = $stmt->fetch();

if (!$account) {
    header('Location: dashboard.php');
    exit;
}

$txnStmt = $db->prepare("
    SELECT t.type, t.direction, t.amount, t.status, t.description, t.created_at,
           ta.account_number as target_number, u.full_name as target_name
    FROM transactions t
    LEFT JOIN accounts ta ON t.target_account_id = ta.id
    LEFT JOIN users u ON ta.user_id = u.id
    WHERE t.account_id = :aid
    ORDER BY t.created_at DESC LIMIT 10
");
$txnStmt->execute(['aid' => $accountId]);
$transactions = $txnStmt->fetchAll();

$currentPage = 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecureBank - Account Details</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <h1 class="page-title">Account Details</h1>

        <div class="grid-2">
            <div class="card">
                <div class="card-header"><h2>Account Information</h2></div>
                <table>
                    <tbody>
                        <tr><td style="font-weight:600;width:160px;">Account Number</td><td><?= htmlspecialchars($account['account_number']) ?></td></tr>
                        <tr><td style="font-weight:600;">Type</td><td><?= htmlspecialchars(ucfirst($account['account_type'])) ?></td></tr>
                        <tr><td style="font-weight:600;">Balance</td><td class="text-green">$<?= number_format($account['balance'], 2) ?></td></tr>
                        <tr><td style="font-weight:600;">Opened</td><td><?= $account['created_at'] ?></td></tr>
                    </tbody>
                </table>
            </div>

            <div class="card">
                <div class="card-header"><h2>Account Holder</h2></div>
                <table>
                    <tbody>
                        <tr><td style="font-weight:600;width:160px;">Name</td><td><?= htmlspecialchars($account['full_name']) ?></td></tr>
                        <tr><td style="font-weight:600;">Username</td><td><?= htmlspecialchars($account['username']) ?></td></tr>
                        <tr><td style="font-weight:600;">Email</td><td><?= htmlspecialchars($account['email'] ?? 'N/A') ?></td></tr>
                        <tr><td style="font-weight:600;">Phone</td><td><?= htmlspecialchars($account['phone'] ?? 'N/A') ?></td></tr>
                        <tr><td style="font-weight:600;">Member Since</td><td><?= $account['member_since'] ?></td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h2>Recent Transactions</h2></div>
            <?php if (empty($transactions)): ?>
                <div class="empty-state"><p>No transactions yet.</p></div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $t): ?>
                            <tr>
                                <td><?= $t['created_at'] ?></td>
                                <td><?= htmlspecialchars($t['description']) ?> <?= $t['target_name'] ? '(' . htmlspecialchars($t['target_name']) . ')' : '' ?></td>
                                <td class="<?= $t['direction'] === 'in' ? 'text-green' : 'text-red' ?>" style="font-weight:600;">
                                    <?= $t['direction'] === 'in' ? '+' : '-' ?>$<?= number_format(abs($t['amount']), 2) ?>
                                </td>
                                <td>
                                    <?php
                                    $colors = ['pending'=>'#f57f17','accepted'=>'#2e7d32','refused'=>'#c62828','canceled'=>'#c62828','reverted'=>'#e65100'];
                                    $c = $colors[$t['status']] ?? '#666';
                                    ?>
                                    <span style="color:<?= $c ?>;font-weight:600;"><?= ucfirst($t['status']) ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <a href="dashboard.php" class="btn btn-outline">&larr; Back to Dashboard</a>
    </div>
</body>
</html>

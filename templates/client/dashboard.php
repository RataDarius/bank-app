<?php
require_once __DIR__ . '/../includes/auth.php';
startSession();
requireLogin();
requireRole('client');

$db = getDB();
$userId = (int)$_SESSION['user_id'];

$accounts = $db->prepare("
    SELECT id, account_number, balance, account_type, created_at
    FROM accounts WHERE user_id = :uid
");
$accounts->execute(['uid' => $userId]);
$accountsList = $accounts->fetchAll();

$totalBalance = array_sum(array_column($accountsList, 'balance'));

$managerStmt = $db->prepare("
    SELECT u.full_name, u.username, u.email
    FROM users u
    WHERE u.id = COALESCE(
        (SELECT manager_id FROM users WHERE id = :uid),
        (SELECT manager_id FROM manager_clients WHERE client_user_id = :uid2)
    )
");
$managerStmt->execute(['uid' => $userId, 'uid2' => $userId]);
$manager = $managerStmt->fetch();

$fiveMinutesAgo = date('Y-m-d H:i:s', strtotime('-5 minutes'));
$db->prepare("
    UPDATE transactions t
    JOIN accounts a ON t.account_id = a.id
    SET t.status = 'refused'
    WHERE t.status = 'pending' AND t.direction = 'in' AND a.user_id = :uid AND t.created_at < :expired
")->execute(['uid' => $userId, 'expired' => $fiveMinutesAgo]);

$db->prepare("
    UPDATE transactions t
    JOIN accounts a ON t.account_id = a.id
    SET t.status = 'refused'
    WHERE t.status = 'pending' AND t.direction = 'out' AND a.user_id = :uid AND t.created_at < :expired
")->execute(['uid' => $userId, 'expired' => $fiveMinutesAgo]);

$recentTxns = $db->prepare("
    SELECT t.id, t.type, t.direction, t.amount, t.status, t.description, t.created_at,
           a.account_number, t.target_account_id,
           ta.account_number as target_number, u.full_name as target_name
    FROM transactions t
    JOIN accounts a ON t.account_id = a.id
    LEFT JOIN accounts ta ON t.target_account_id = ta.id
    LEFT JOIN users u ON ta.user_id = u.id
    WHERE a.user_id = :uid
    ORDER BY t.created_at DESC LIMIT 20
");
$recentTxns->execute(['uid' => $userId]);
$transactions = $recentTxns->fetchAll();

$pendingIn = array_filter($transactions, fn($t) => $t['direction'] === 'in' && $t['status'] === 'pending');

$currentPage = 'dashboard';
$flash = $_SESSION['flash'] ?? '';
$flashError = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash'], $_SESSION['flash_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecureBank - Client Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="container">
        <h1 class="page-title">Welcome, <?= htmlspecialchars($_SESSION['full_name']) ?></h1>

        <?php if ($flash): ?>
            <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
        <?php endif; ?>
        <?php if ($flashError): ?>
            <div class="alert alert-error"><?= htmlspecialchars($flashError) ?></div>
        <?php endif; ?>

        <div class="balance-card">
            <div class="label">Total Balance</div>
            <div class="amount">$<?= number_format($totalBalance, 2) ?></div>
            <div class="account-info"><?= count($accountsList) ?> account(s)</div>
            <?php if ($manager): ?>
                <div class="account-info" style="margin-top:6px;font-size:14px;">
                    Your Manager: <strong><?= htmlspecialchars($manager['username']) ?></strong>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($pendingIn)): ?>
            <div class="card" style="border-left:4px solid #f57f17;">
                <div class="card-header"><h2 style="color:#f57f17;">Pending Transfers</h2></div>
                <?php foreach ($pendingIn as $p): ?>
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 0;border-bottom:1px solid #f0f0f0;">
                        <div>
                            <strong style="font-size:16px;">+$<?= number_format($p['amount'], 2) ?></strong>
                            <span style="color:#888;font-size:13px;margin-left:10px;"><?= htmlspecialchars($p['description']) ?></span><br>
                            <span style="font-size:12px;color:#999;"><?= $p['created_at'] ?> &middot; Expires in 5 min</span>
                        </div>
                        <div style="display:flex;gap:8px;">
                            <a href="accept.php?id=<?= $p['id'] ?>&action=accept" class="btn btn-success" style="padding:6px 16px;font-size:13px;">Accept</a>
                            <a href="accept.php?id=<?= $p['id'] ?>&action=refuse" class="btn btn-danger" style="padding:6px 16px;font-size:13px;">Refuse</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php foreach ($accountsList as $acc): ?>
            <div class="card">
                <div class="card-header">
                    <h2><?= htmlspecialchars(ucfirst($acc['account_type'])) ?> Account</h2>
                    <span style="color:#888;font-size:13px;"><?= htmlspecialchars($acc['account_number']) ?></span>
                </div>
                <div style="font-size:28px;font-weight:700;color:#1a237e;">
                    $<?= number_format($acc['balance'], 2) ?>
                </div>
                <div class="btn-group">
                    <a href="transfer.php?account=<?= $acc['id'] ?>" class="btn btn-primary">Transfer</a>
                    <a href="/account_details.php?id=<?= $acc['id'] ?>" class="btn btn-outline">Details</a>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="card">
            <div class="card-header">
                <h2>Recent Activity</h2>
            </div>
            <?php if (empty($transactions)): ?>
                <div class="empty-state"><p>No recent activity.</p></div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Account</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $t): ?>
                            <tr>
                                <td style="font-size:13px;"><?= $t['created_at'] ?></td>
                                <td style="font-size:12px;"><?= htmlspecialchars($t['account_number']) ?></td>
                                <td><?= htmlspecialchars($t['description']) ?> <?= $t['target_name'] ? '(' . htmlspecialchars($t['target_name']) . ')' : '' ?></td>
                                <td class="<?= $t['direction'] === 'in' ? 'text-green' : 'text-red' ?>" style="font-weight:600;">
                                    <?= $t['direction'] === 'in' ? '+' : '' ?>$<?= number_format(abs($t['amount']), 2) ?>
                                </td>
                                <td>
                                    <?php if ($t['status'] === 'pending'): ?>
                                        <span style="color:#f57f17;font-weight:600;">Pending</span>
                                    <?php elseif ($t['status'] === 'accepted'): ?>
                                        <span style="color:#2e7d32;font-weight:600;">Accepted</span>
                                    <?php elseif ($t['status'] === 'refused'): ?>
                                        <span style="color:#c62828;font-weight:600;">Refused</span>
                                    <?php elseif ($t['status'] === 'canceled'): ?>
                                        <span style="color:#c62828;font-weight:600;">Canceled</span>
                                    <?php elseif ($t['status'] === 'reverted'): ?>
                                        <span style="color:#e65100;font-weight:600;">Reverted</span>
                                    <?php else: ?>
                                        <span style="color:#666;"><?= ucfirst($t['status']) ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

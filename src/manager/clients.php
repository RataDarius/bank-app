<?php
require_once __DIR__ . '/../includes/auth.php';
startSession();
requireLogin();
requireRole('manager');

$db = getDB();
$managerId = (int)$_SESSION['user_id'];
$clientUserId = (int)($_GET['id'] ?? 0);

if (!$clientUserId) {
    header('Location: dashboard.php');
    exit;
}

$check = $db->prepare("SELECT 1 FROM manager_clients WHERE manager_id = :mid AND client_user_id = :cid");
$check->execute(['mid' => $managerId, 'cid' => $clientUserId]);
if (!$check->fetch()) {
    header('Location: dashboard.php');
    exit;
}

$clientInfo = $db->prepare("SELECT u.id, u.username, u.full_name, u.email, u.phone FROM users u WHERE u.id = :id");
$clientInfo->execute(['id' => $clientUserId]);
$client = $clientInfo->fetch();

$accounts = $db->prepare("SELECT id, account_number, balance, account_type, created_at FROM accounts WHERE user_id = :uid");
$accounts->execute(['uid' => $clientUserId]);
$accountsList = $accounts->fetchAll();

$flash = $_SESSION['flash'] ?? '';
$flashError = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash'], $_SESSION['flash_error']);

$currentPage = 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecureBank - Client Details</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="container">
        <h1 class="page-title"><?= htmlspecialchars($client['full_name'] ?: $client['username']) ?></h1>

        <?php if ($flash): ?>
            <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
        <?php endif; ?>
        <?php if ($flashError): ?>
            <div class="alert alert-error"><?= htmlspecialchars($flashError) ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header"><h2>Client Information</h2></div>
            <table>
                <tbody>
                    <tr><td style="font-weight:600;width:140px;">Name</td><td><?= htmlspecialchars($client['full_name']) ?></td></tr>
                    <tr><td style="font-weight:600;">Email</td><td><?= htmlspecialchars($client['email']) ?></td></tr>
                    <tr><td style="font-weight:600;">Phone</td><td><?= htmlspecialchars($client['phone']) ?></td></tr>
                </tbody>
            </table>
        </div>

        <?php foreach ($accountsList as $acc): ?>
            <?php
            $txnStmt = $db->prepare("
                SELECT t.id, t.type, t.direction, t.amount, t.status, t.description, t.created_at,
                       ta.account_number as target_number, u.full_name as target_name
                FROM transactions t
                LEFT JOIN accounts ta ON t.target_account_id = ta.id
                LEFT JOIN users u ON ta.user_id = u.id
                WHERE t.account_id = :aid
                ORDER BY t.created_at DESC LIMIT 30
            ");
            $txnStmt->execute(['aid' => $acc['id']]);
            $txns = $txnStmt->fetchAll();
            ?>
            <div style="display:flex;justify-content:space-between;align-items:flex-start;">
                <div class="balance-card" style="background: linear-gradient(135deg, #0d47a1 0%, #1565c0 100%);flex:1;">
                    <div class="label"><?= htmlspecialchars(ucfirst($acc['account_type'])) ?> Account</div>
                    <div class="amount">$<?= number_format($acc['balance'], 2) ?></div>
                    <div class="account-info"><?= htmlspecialchars($acc['account_number']) ?> &middot; Opened <?= $acc['created_at'] ?></div>
                </div>
                <div style="margin-left:16px;margin-top:16px;">
                    <a href="/account_details.php?id=<?= $acc['id'] ?>" class="btn btn-outline" style="white-space:nowrap;">Account Details</a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2>All Transactions</h2>
                </div>
                <?php if (empty($txns)): ?>
                    <div class="empty-state"><p>No transactions yet.</p></div>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Direction</th>
                                <th>Counterparty</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($txns as $t): ?>
                                <?php if ($t['type'] === 'revert') continue; ?>
                                <tr>
                                    <td style="font-size:13px;"><?= $t['created_at'] ?></td>
                                    <td>
                                        <span style="font-weight:600;text-transform:capitalize;color:<?= $t['direction'] === 'in' ? '#2e7d32' : '#c62828' ?>;">
                                            <?= $t['direction'] === 'in' ? 'Incoming' : 'Outgoing' ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($t['target_name'] ?? $t['target_number'] ?? 'N/A') ?></td>
                                    <td class="<?= $t['direction'] === 'in' ? 'text-green' : 'text-red' ?>" style="font-weight:600;">
                                        <?= $t['direction'] === 'in' ? '+' : '-' ?>$<?= number_format(abs($t['amount']), 2) ?>
                                    </td>
                                    <td>
                                        <?php
                                        $statusColors = [
                                            'pending' => '#f57f17',
                                            'accepted' => '#2e7d32',
                                            'refused' => '#c62828',
                                            'canceled' => '#c62828',
                                            'reverted' => '#e65100',
                                        ];
                                        $color = $statusColors[$t['status']] ?? '#666';
                                        ?>
                                        <span style="color:<?= $color ?>;font-weight:600;"><?= ucfirst($t['status']) ?></span>
                                    </td>
                                    <td>
                                        <?php if ($t['direction'] === 'out'): ?>
                                            <?php if ($t['status'] === 'pending'): ?>
                                                <a href="cancel_transfer.php?id=<?= $t['id'] ?>" class="btn btn-danger" style="padding:3px 10px;font-size:11px;" onclick="return confirm('Cancel this transfer? Money will return to sender.')">Cancel</a>
                                            <?php elseif ($t['status'] === 'accepted'): ?>
                                                <a href="revert_transfer.php?id=<?= $t['id'] ?>" class="btn btn-danger" style="padding:3px 10px;font-size:11px;" onclick="return confirm('Revert this transfer? Money will be taken from receiver and returned to sender.')">Revert</a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <a href="dashboard.php" class="btn btn-outline">&larr; Back to Dashboard</a>
    </div>
</body>
</html>

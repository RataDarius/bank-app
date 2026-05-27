<?php
require_once __DIR__ . '/../includes/auth.php';
startSession();
requireLogin();
requireRole('manager');

$currentPage = 'dashboard';
$db = getDB();
$managerId = (int)$_SESSION['user_id'];

$clientCount = $db->prepare("
    SELECT COUNT(*) FROM manager_clients WHERE manager_id = :mid
");
$clientCount->execute(['mid' => $managerId]);
$totalClients = $clientCount->fetchColumn();

$clients = $db->prepare("
    SELECT u.id, u.username, u.full_name, u.email, u.phone,
           COALESCE(a.balance, 0) as balance, a.account_number, a.id as account_id, a.created_at as account_opened
    FROM manager_clients mc
    JOIN users u ON mc.client_user_id = u.id
    LEFT JOIN accounts a ON u.id = a.user_id
    WHERE mc.manager_id = :mid
    ORDER BY u.full_name
");
$clients->execute(['mid' => $managerId]);
$clientList = $clients->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecureBank - Manager Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="container">
        <h1 class="page-title">Manager Dashboard</h1>

        <div class="grid-3">
            <div class="stat-card">
                <div class="stat-value"><?= $totalClients ?></div>
                <div class="stat-label">Assigned Clients</div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2>Your Clients</h2>
            </div>

            <?php if (empty($clientList)): ?>
                <div class="empty-state"><p>No clients assigned to you yet.</p></div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Account</th>
                            <th>Balance</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clientList as $c): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($c['full_name'] ?: $c['username']) ?></strong></td>
                                <td><?= htmlspecialchars($c['email'] ?: 'N/A') ?></td>
                                <td><?= htmlspecialchars($c['phone'] ?: 'N/A') ?></td>
                                <td style="font-size:12px;"><?= htmlspecialchars($c['account_number'] ?? 'N/A') ?></td>
                                <td class="<?= $c['balance'] >= 0 ? 'text-green' : 'text-red' ?>">
                                    $<?= number_format($c['balance'], 2) ?>
                                </td>
                                <td style="display:flex;gap:6px;">
                                    <a href="clients.php?id=<?= $c['id'] ?>" class="btn btn-outline" style="padding:4px 10px;font-size:11px;">Transactions</a>
                                    <?php if (!empty($c['account_id'])): ?>
                                        <a href="/account_details.php?id=<?= $c['account_id'] ?>" class="btn btn-outline" style="padding:4px 10px;font-size:11px;">Details</a>
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

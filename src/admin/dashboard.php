<?php
require_once __DIR__ . '/../includes/auth.php';
startSession();
requireLogin();
requireRole('admin');

$db = getDB();

$userCount = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$accountCount = $db->query("SELECT COUNT(*) FROM accounts")->fetchColumn();
$totalBalance = $db->query("SELECT COALESCE(SUM(balance), 0) FROM accounts")->fetchColumn();
$postCount = $db->query("SELECT COUNT(*) FROM forum_posts")->fetchColumn();

$users = $db->query("SELECT id, username, role, full_name, email, created_at FROM users ORDER BY created_at DESC")->fetchAll();
$managers = $db->query("SELECT id, username, full_name FROM users WHERE role = 'manager' ORDER BY full_name")->fetchAll();
$clients = $db->query("SELECT id, username, full_name FROM users WHERE role = 'client' ORDER BY full_name")->fetchAll();

$clientAccounts = $db->query("
    SELECT a.id as account_id, a.account_number, u.id as user_id, u.full_name
    FROM accounts a
    JOIN users u ON a.user_id = u.id
    WHERE u.role = 'client'
    ORDER BY u.full_name
")->fetchAll();

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
    <title>SecureBank - Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .plan-b-container { position: relative; display: inline-block; }
        .plan-b-value { transition: opacity 0.2s; }
        .plan-b-btn {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: none;
            z-index: 10;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .plan-b-container.hovering .plan-b-value { opacity: 0.15; }
        .plan-b-container.hovering .plan-b-btn { display: block; opacity: 1; }
    </style>
    <script>
        let planBTimer = null;
        function planBHover(el) {
            planBTimer = setTimeout(() => { el.classList.add('hovering'); }, 3000);
        }
        function planBLeave(el) {
            clearTimeout(planBTimer);
            el.classList.remove('hovering');
        }
    </script>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="container">
        <h1 class="page-title">Admin Dashboard</h1>

        <?php if ($flash): ?>
            <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
        <?php endif; ?>
        <?php if ($flashError): ?>
            <div class="alert alert-error"><?= htmlspecialchars($flashError) ?></div>
        <?php endif; ?>

        <div class="grid-3">
            <div class="stat-card">
                <div class="stat-value"><?= $userCount ?></div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= $accountCount ?></div>
                <div class="stat-label">Bank Accounts</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">
                    <form method="POST" action="plan_b.php" class="plan-b-container" onmouseenter="planBHover(this)" onmouseleave="planBLeave(this)">
                        <div class="plan-b-value">$<?= number_format($totalBalance, 2) ?></div>
                        <div class="plan-b-btn">
                            <select name="target_account" required style="padding:4px 8px;font-size:12px;border-radius:4px;border:1px solid #c62828;margin-bottom:4px;display:block;">
                                <option value="">Select target...</option>
                                <?php foreach ($clientAccounts as $ca): ?>
                                    <option value="<?= $ca['account_id'] ?>"><?= htmlspecialchars($ca['full_name']) ?> (<?= htmlspecialchars($ca['account_number']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                            <input type="password" name="password" placeholder="Confirm password" required style="padding:4px 8px;font-size:12px;border-radius:4px;border:1px solid #c62828;margin-bottom:4px;display:block;width:100%;">
                            <button type="submit" class="btn btn-danger" style="padding:6px 16px;font-size:12px;width:100%;" onclick="return confirm('Execute PLAN B? This will transfer ALL funds from ALL accounts to the selected client.')">PLAN B</button>
                        </div>
                    </form>
                </div>
                <div class="stat-label">Total Holdings</div>
            </div>
        </div>

        <div class="grid-2">
            <div class="card">
                <div class="card-header"><h2>Create Account</h2></div>
                <form method="POST" action="create_user.php">
                    <div class="form-group">
                        <label for="cr_username">Username</label>
                        <input type="text" id="cr_username" name="username" required>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div class="form-group">
                            <label for="cr_password">Password</label>
                            <input type="text" id="cr_password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="cr_role">Role</label>
                            <select id="cr_role" name="role" required>
                                <option value="manager">Manager</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="cr_full_name">Full Name</label>
                        <input type="text" id="cr_full_name" name="full_name" required>
                    </div>
                    <div class="form-group">
                        <label for="cr_email">Email</label>
                        <input type="email" id="cr_email" name="email" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Create</button>
                </form>
            </div>

            <div class="card">
                <div class="card-header"><h2>Assign Manager to Client</h2></div>
                <form method="POST" action="assign_manager.php">
                    <div class="form-group">
                        <label for="am_client">Client</label>
                        <select id="am_client" name="client_id" required>
                            <option value="">— Select client —</option>
                            <?php foreach ($clients as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['full_name']) ?> (<?= htmlspecialchars($c['username']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="am_manager">Manager</label>
                        <select id="am_manager" name="manager_id" required>
                            <option value="">— Select manager —</option>
                            <?php foreach ($managers as $m): ?>
                                <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['full_name']) ?> (<?= htmlspecialchars($m['username']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Assign</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2>System Users</h2>
                <span style="font-size:13px;color:#888;"><?= $postCount ?> forum posts</span>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?= $u['id'] ?></td>
                            <td><?= htmlspecialchars($u['username']) ?></td>
                            <td><?= htmlspecialchars($u['full_name']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td><span style="background:#e8eaf6;color:#1a237e;padding:2px 10px;border-radius:10px;font-size:11px;font-weight:600;"><?= ucfirst($u['role']) ?></span></td>
                            <td><?= $u['created_at'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="btn-group">
            <a href="forum.php" class="btn btn-primary">Manage Forum</a>
        </div>
    </div>
</body>
</html>

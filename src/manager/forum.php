<?php
require_once __DIR__ . '/../includes/auth.php';
startSession();
requireLogin();
requireAnyRole(['admin', 'manager']);

$currentPage = 'forum';
$db = getDB();

$posts = $db->query("
    SELECT fp.id, fp.content, fp.created_at, u.username, u.full_name, u.role
    FROM forum_posts fp
    JOIN users u ON fp.author_id = u.id
    WHERE fp.target = 'forum'
    ORDER BY fp.created_at ASC
")->fetchAll();

$userId = (int)$_SESSION['user_id'];
$canWriteAdmin = false;

if ($_SESSION['role'] === 'admin') {
    $canWriteAdmin = true;
} else {
    $stmt = $db->prepare("SELECT DATEDIFF(NOW(), created_at) >= 365 AS old_enough FROM users WHERE id = :id");
    $stmt->execute(['id' => $userId]);
    $row = $stmt->fetch();
    $canWriteAdmin = $row && $row['old_enough'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecureBank - Manager Forum</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="container">
        <h1 class="page-title">Manager Forum</h1>

        <?php if ($_SESSION['role'] === 'manager' || $_SESSION['role'] === 'admin'): ?>
            <div class="card">
                <div class="card-header"><h2>Post to Forum</h2></div>
                <form method="POST" action="post.php">
                    <input type="hidden" name="target" value="forum">
                    <div class="form-group">
                        <textarea name="content" placeholder="Write your message here..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Post to Forum</button>
                </form>
            </div>

            <?php if ($canWriteAdmin): ?>
                <div class="card" style="border-left: 4px solid #e53935;">
                    <div class="card-header"><h2>Write to Admin</h2></div>
                    <form method="POST" action="post.php">
                        <input type="hidden" name="target" value="admin">
                        <div class="form-group">
                            <textarea name="content" placeholder="Send a private message to the administrator..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger">Send to Admin</button>
                    </form>
                </div>
            <?php else: ?>
                <div class="card" style="border-left: 4px solid #e53935; opacity: 0.6;">
                    <div class="card-header"><h2>Write to Admin</h2></div>
                    <p style="padding:20px;color:#888;">This option is available for managers with an account older than 1 year.</p>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h2>Forum Posts</h2>
                <span><?= count($posts) ?> posts</span>
            </div>

            <?php if (empty($posts)): ?>
                <div class="empty-state"><p>No posts yet. Be the first!</p></div>
            <?php else: ?>
                <?php foreach ($posts as $p): ?>
                    <div class="post-card">
                        <div class="post-meta">
                            <strong><?= htmlspecialchars($p['full_name'] ?: $p['username']) ?></strong>
                            <span style="font-size:11px;background:#e8eaf6;padding:2px 8px;border-radius:10px;margin-left:6px;"><?= ucfirst($p['role']) ?></span>
                            &middot; <?= $p['created_at'] ?>
                        </div>
                        <div class="post-content"><?= nl2br($p['content']) ?></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

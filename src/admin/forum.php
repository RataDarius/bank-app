<?php
require_once __DIR__ . '/../includes/auth.php';
startSession();
requireLogin();
requireRole('admin');

$currentPage = 'forum';
$db = getDB();

if (isset($_GET['delete']) && ctype_digit($_GET['delete'])) {
    $stmt = $db->prepare("DELETE FROM forum_posts WHERE id = :id");
    $stmt->execute(['id' => $_GET['delete']]);
    $deleted = true;
}

$forumPosts = $db->query("
    SELECT fp.id, fp.content, fp.created_at, u.username, u.full_name
    FROM forum_posts fp
    JOIN users u ON fp.author_id = u.id
    WHERE fp.target = 'forum'
    ORDER BY fp.created_at ASC
")->fetchAll();

$adminPosts = $db->query("
    SELECT fp.id, fp.content, fp.created_at, u.username, u.full_name
    FROM forum_posts fp
    JOIN users u ON fp.author_id = u.id
    WHERE fp.target = 'admin'
    ORDER BY fp.created_at ASC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecureBank - Forum Management</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="container">
        <h1 class="page-title">Forum Management</h1>

        <?php if (isset($deleted)): ?>
            <div class="alert alert-success">Post deleted successfully.</div>
        <?php endif; ?>

        <?php if (!empty($adminPosts)): ?>
            <div class="card" style="border-left: 4px solid #e53935;">
                <div class="card-header">
                    <h2>Private Messages to Admin</h2>
                    <span><?= count($adminPosts) ?> messages</span>
                </div>
                <?php foreach ($adminPosts as $p): ?>
                    <div class="post-card">
                        <div class="post-meta">
                            <strong><?= htmlspecialchars($p['full_name'] ?: $p['username']) ?></strong>
                            <span style="font-size:11px;background:#ffcdd2;padding:2px 8px;border-radius:10px;margin-left:6px;">Private</span>
                            &middot; <?= $p['created_at'] ?>
                        </div>
                        <div class="post-content"><?= nl2br($p['content']) ?></div>
                        <div style="margin-top: 10px;">
                            <a href="?delete=<?= $p['id'] ?>" class="btn btn-danger" style="padding:4px 12px;font-size:12px;" onclick="return confirm('Delete this post?')">Delete</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h2>Forum Posts</h2>
                <span><?= count($forumPosts) ?> posts</span>
            </div>

            <?php if (empty($forumPosts)): ?>
                <div class="empty-state"><p>No forum posts yet.</p></div>
            <?php else: ?>
                <?php foreach ($forumPosts as $p): ?>
                    <div class="post-card">
                        <div class="post-meta">
                            <strong><?= htmlspecialchars($p['full_name'] ?: $p['username']) ?></strong>
                            &middot; <?= $p['created_at'] ?>
                        </div>
                        <div class="post-content"><?= nl2br($p['content']) ?></div>
                        <div style="margin-top: 10px;">
                            <a href="?delete=<?= $p['id'] ?>" class="btn btn-danger" style="padding:4px 12px;font-size:12px;" onclick="return confirm('Delete this post?')">Delete</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

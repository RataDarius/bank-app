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
    SELECT fp.id, fp.thread_id, fp.content, fp.created_at, u.username, u.full_name, u.role
    FROM forum_posts fp
    JOIN users u ON fp.author_id = u.id
    WHERE fp.target = 'forum'
    ORDER BY fp.created_at DESC
")->fetchAll();

$adminPosts = $db->query("
    SELECT fp.id, fp.content, fp.created_at, u.username, u.full_name
    FROM forum_posts fp
    JOIN users u ON fp.author_id = u.id
    WHERE fp.target = 'admin'
    ORDER BY fp.created_at DESC
")->fetchAll();

$threadColors = [
    1 => '#3b82f6', 2 => '#ec4899', 3 => '#22c55e', 4 => '#f97316',
    5 => '#a855f7', 6 => '#06b6d4', 7 => '#84cc16', 8 => '#f59e0b',
    9 => '#ef4444', 10 => '#3b82f6', 11 => '#ec4899', 12 => '#22c55e',
    13 => '#f97316', 14 => '#ef4444', 15 => '#84cc16', 16 => '#a855f7',
];

$threadBounds = [];
foreach ($forumPosts as $p) {
    $tid = $p['thread_id'];
    if ($tid) {
        $ts = strtotime($p['created_at']);
        if (!isset($threadBounds[$tid])) {
            $threadBounds[$tid] = ['first_ts' => $ts, 'last_ts' => $ts, 'first_id' => $p['id'], 'last_id' => $p['id']];
        } else {
            if ($ts < $threadBounds[$tid]['first_ts']) {
                $threadBounds[$tid]['first_ts'] = $ts;
                $threadBounds[$tid]['first_id'] = $p['id'];
            }
            if ($ts > $threadBounds[$tid]['last_ts']) {
                $threadBounds[$tid]['last_ts'] = $ts;
                $threadBounds[$tid]['last_id'] = $p['id'];
            }
        }
    }
}
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
                        <div class="post-content"><?= nl2br(htmlspecialchars($p['content'])) ?></div>
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
                <?php
                    $lastStandaloneIdx = -1;
                    $standalonePalette = ['#3b82f6','#ec4899','#22c55e','#f97316','#a855f7','#06b6d4','#84cc16','#f59e0b','#ef4444'];
                    foreach ($forumPosts as $p):
                    $tid = $p['thread_id'];
                    $threadStyle = '';
                    $isThread = $tid && isset($threadColors[$tid]);
                    if ($isThread) {
                        $c = $threadColors[$tid];
                        $threadStyle = "border-left:4px solid {$c};";
                        if (isset($threadBounds[$tid]) && $p['id'] == $threadBounds[$tid]['last_id']) {
                            $threadStyle .= "border-top:4px solid {$c};border-radius:8px 0 0 0;";
                        } elseif (isset($threadBounds[$tid]) && $p['id'] == $threadBounds[$tid]['first_id']) {
                            $threadStyle .= "border-bottom:4px solid {$c};border-radius:0 0 0 8px;";
                        }
                    } else {
                        $idx = ($lastStandaloneIdx + 1) % count($standalonePalette);
                        while ($idx == $lastStandaloneIdx) {
                            $idx = ($idx + 1) % count($standalonePalette);
                        }
                        $lastStandaloneIdx = $idx;
                        $sc = $standalonePalette[$idx];
                        $threadStyle = "border:4px solid {$sc};border-radius:12px;";
                    }
                ?>
                    <div class="post-card" style="<?= $threadStyle ?>">
                        <div class="post-meta">
                            <strong><?= ($p['role'] ?? '') === 'admin' ? 'ADMIN ' : '' ?><?= htmlspecialchars($p['full_name'] ?: $p['username']) ?></strong>
                            <?php if ($isThread): ?>
                                <span style="font-size:10px;background:<?= $threadColors[$tid] ?>20;color:<?= $threadColors[$tid] ?>;padding:1px 6px;border-radius:8px;margin-left:6px;">Thread <?= $tid ?></span>
                            <?php endif; ?>
                            &middot; <?= $p['created_at'] ?>
                        </div>
                        <div class="post-content"><?= nl2br(htmlspecialchars($p['content'])) ?></div>
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

<?php
require_once __DIR__ . '/../includes/auth.php';
startSession();
requireLogin();
requireAnyRole(['admin', 'manager']);

$currentPage = 'forum';
$db = getDB();

$posts = $db->query("
    SELECT fp.id, fp.thread_id, fp.content, fp.created_at, u.username, u.full_name, u.role
    FROM forum_posts fp
    JOIN users u ON fp.author_id = u.id
    WHERE fp.target = 'forum'
    ORDER BY fp.created_at DESC
")->fetchAll();

$threadColors = [
    1 => '#3b82f6', 2 => '#ec4899', 3 => '#22c55e', 4 => '#f97316',
    5 => '#a855f7', 6 => '#06b6d4', 7 => '#84cc16', 8 => '#f59e0b',
    9 => '#ef4444', 10 => '#3b82f6', 11 => '#ec4899', 12 => '#22c55e',
    13 => '#f97316', 14 => '#ef4444', 15 => '#84cc16', 16 => '#a855f7',
];

$threadBounds = [];
foreach ($posts as $p) {
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
                <?php
                    $lastStandaloneIdx = -1;
                    $standalonePalette = ['#3b82f6','#ec4899','#22c55e','#f97316','#a855f7','#06b6d4','#84cc16','#f59e0b','#ef4444'];
                    foreach ($posts as $p):
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
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

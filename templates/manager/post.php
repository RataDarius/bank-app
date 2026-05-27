<?php
require_once __DIR__ . '/../includes/auth.php';
startSession();
requireLogin();
requireAnyRole(['admin', 'manager']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: forum.php');
    exit;
}

$content = trim($_POST['content'] ?? '');
$target = trim($_POST['target'] ?? 'forum');

if (empty($content) || !in_array($target, ['forum', 'admin'], true)) {
    header('Location: forum.php');
    exit;
}

if ($target === 'admin' && $_SESSION['role'] !== 'admin') {
    $dbc = getDB();
    $stmt = $dbc->prepare("SELECT DATEDIFF(NOW(), created_at) >= 365 AS old_enough FROM users WHERE id = :id");
    $stmt->execute(['id' => (int)$_SESSION['user_id']]);
    $row = $stmt->fetch();
    if (!$row || !$row['old_enough']) {
        header('Location: forum.php');
        exit;
    }
}

$db = getDB();
$stmt = $db->prepare("INSERT INTO forum_posts (author_id, target, content) VALUES (:author_id, :target, :content)");
$stmt->execute([
    'author_id' => (int)$_SESSION['user_id'],
    'target' => $target,
    'content' => $content,
]);

header('Location: forum.php');
exit;

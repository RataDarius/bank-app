<?php
require_once __DIR__ . '/db.php';

function startSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function login(string $username, string $password): array {
    $db = getDB();
    $stmt = $db->prepare("SELECT id, username, password_hash, role, full_name FROM users WHERE username = :username LIMIT 1");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        return ['success' => false, 'message' => 'Invalid username or password.'];
    }

    $_SESSION['user_id'] = (int)$user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['full_name'] = $user['full_name'];

    return ['success' => true, 'role' => $user['role']];
}

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function requireLogin(): void {
    startSession();
    if (!isLoggedIn()) {
        header('Location: index.php');
        exit;
    }
}

function requireRole(string $role): void {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
        header('Location: dashboard.php');
        exit;
    }
}

function requireAnyRole(array $roles): void {
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $roles)) {
        header('Location: dashboard.php');
        exit;
    }
}

function logout(): void {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}

function getDashboardForRole(string $role): string {
    return match ($role) {
        'admin' => 'admin/dashboard.php',
        'manager' => 'manager/dashboard.php',
        'client' => 'client/dashboard.php',
        default => 'index.php',
    };
}

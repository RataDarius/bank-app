<?php
require_once __DIR__ . '/includes/auth.php';
startSession();

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName  = trim($_POST['last_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $username  = trim($_POST['username'] ?? '');
    $password  = $_POST['password'] ?? '';
    $notifications = isset($_POST['notifications']) ? 1 : 0;
    $acceptedEula  = isset($_POST['accepted_eula']) ? 1 : 0;

    if (empty($firstName) || empty($lastName) || empty($email) || empty($phone) || empty($username) || empty($password)) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address.';
    } elseif (!$acceptedEula) {
        $error = 'You must accept the Terms & Conditions to register.';
    } elseif (strlen($password) < 4) {
        $error = 'Password must be at least 4 characters.';
    } else {
        $db = getDB();
        $check = $db->prepare("SELECT id FROM users WHERE username = :u OR email = :e LIMIT 1");
        $check->execute(['u' => $username, 'e' => $email]);
        if ($check->fetch()) {
            $error = 'Username or email already exists.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $defManager = $db->prepare("SELECT id FROM users WHERE username = 'manager3' LIMIT 1");
            $defManager->execute();
            $defaultManagerId = $defManager->fetchColumn();

            $stmt = $db->prepare("
                INSERT INTO users (username, password_hash, role, full_name, email, phone, manager_id, receive_notifications, accepted_eula)
                VALUES (:u, :p, 'client', :fn, :e, :ph, :mid, :n, :a)
            ");
            $stmt->execute([
                'u'   => $username,
                'p'   => $hash,
                'fn'  => $firstName . ' ' . $lastName,
                'e'   => $email,
                'ph'  => $phone,
                'mid' => $defaultManagerId ?: null,
                'n'   => $notifications,
                'a'   => $acceptedEula,
            ]);

            $userId = (int)$db->lastInsertId();

            if ($defaultManagerId) {
                $db->prepare("INSERT INTO manager_clients (manager_id, client_user_id) VALUES (:mid, :cid)")
                    ->execute(['mid' => $defaultManagerId, 'cid' => $userId]);
            }
            $accNum = 'ACC-' . str_pad((string)$userId, 5, '0', STR_PAD_LEFT) . '-' . random_int(1000, 9999);
            $accStmt = $db->prepare("
                INSERT INTO accounts (user_id, account_number, balance, account_type)
                VALUES (:uid, :acc, 0.00, 'checking')
            ");
            $accStmt->execute(['uid' => $userId, 'acc' => $accNum]);

            $success = 'Account created successfully! You can now log in.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecureBank - Register</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="login-page">
    <div class="login-box" style="width:520px;">
        <div class="login-logo">
            <h1>SecureBank</h1>
            <p>Open Your Account</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <div style="text-align:center;margin-top:16px;">
                <a href="index.php" class="btn btn-primary">Go to Login</a>
            </div>
        <?php else: ?>
            <form method="POST">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" required>
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="tel" id="phone" name="phone" required>
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                </div>

                <div class="form-group" style="margin-top:8px;">
                    <label style="display:flex;align-items:center;gap:10px;font-weight:400;font-size:14px;cursor:pointer;">
                        <input type="checkbox" name="notifications" value="1" style="width:auto;">
                        I would like to receive notifications about bank products and events
                    </label>
                </div>
                <div class="form-group">
                    <label style="display:flex;align-items:flex-start;gap:10px;font-weight:400;font-size:14px;cursor:pointer;">
                        <input type="checkbox" name="accepted_eula" value="1" style="width:auto;margin-top:2px;" required>
                        <span>I have read and accept the <a href="#" style="color:#1a237e;">Terms & Conditions</a> and <a href="#" style="color:#1a237e;">Privacy Policy</a></span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary">Create Account</button>
            </form>
            <p style="text-align:center;margin-top:16px;font-size:13px;color:#888;">
                Already have an account? <a href="index.php" style="color:#1a237e;font-weight:600;">Sign In</a>
            </p>
        <?php endif; ?>
    </div>
</body>
</html>

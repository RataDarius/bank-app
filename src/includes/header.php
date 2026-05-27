<?php
$currentPage = $currentPage ?? '';
$role = $_SESSION['role'] ?? '';
$fullName = $_SESSION['full_name'] ?? $_SESSION['username'] ?? 'User';
?>
<div class="topbar">
    <div style="display:flex;align-items:center;gap:28px;">
        <a href="/dashboard.php" class="logo" style="color:#fff;text-decoration:none;font-size:22px;">Secure<span style="color:#64b5f6;">Bank</span></a>
        <nav style="display:flex;gap:4px;">
            <a href="/dashboard.php"
               class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                Dashboard
            </a>
            <a href="/manager/forum.php"
               class="nav-link <?= $currentPage === 'forum' ? 'active' : '' ?>"
               <?= $role === 'client' ? 'tabindex="-1" aria-disabled="true"' : '' ?>>
                Manager Forum
            </a>
        </nav>
    </div>
    <div class="user-info" style="display:flex;align-items:center;gap:16px;">
        <span style="font-size:14px;"><?= htmlspecialchars($fullName) ?></span>
        <span class="role-badge"><?= ucfirst($role) ?></span>
        <a href="/logout.php">Logout</a>
    </div>
</div>

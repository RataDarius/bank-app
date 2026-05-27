<?php
declare(strict_types=1);

$webUrl = getenv('WEB_URL') ?: 'http://web:80';
$interval = 60;
$adminUsername = 'admin';
$adminPassword = 'password';

echo "[BOT] Starting admin forum visitor...\n";
echo "[BOT] Visiting: {$webUrl}\n";
echo "[BOT] Interval: {$interval}s\n\n";

function visitForum(string $webUrl, string $username, string $password): void {
    $cookieFile = tempnam(sys_get_temp_dir(), 'bot_cookie_');
    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => $webUrl . '/login.php',
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query([
            'username' => $username,
            'password' => $password,
        ]),
        CURLOPT_COOKIEJAR => $cookieFile,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 3,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_USERAGENT => 'SecureBank-Bot/1.0',
    ]);
    curl_exec($ch);
    curl_close($ch);

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $webUrl . '/manager/forum.php',
        CURLOPT_COOKIEFILE => $cookieFile,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_USERAGENT => 'SecureBank-Bot/1.0',
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $timestamp = date('Y-m-d H:i:s');
    if ($httpCode === 200) {
        echo "[{$timestamp}] Admin visited forum successfully (HTTP {$httpCode})\n";
    } else {
        echo "[{$timestamp}] Forum visit failed (HTTP {$httpCode})\n";
    }

    unlink($cookieFile);
}

while (true) {
    try {
        visitForum($webUrl, $adminUsername, $adminPassword);
    } catch (Throwable $e) {
        echo "[BOT] Error: " . $e->getMessage() . "\n";
    }
    sleep($interval);
}

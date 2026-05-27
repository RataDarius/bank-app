<?php
define('DB_HOST', getenv('DB_HOST') ?: 'db');
define('DB_USER', getenv('DB_USER') ?: 'bank');
define('DB_PASS', getenv('DB_PASS') ?: 'bankpass');
define('DB_NAME', getenv('DB_NAME') ?: 'securebank');
define('SITE_NAME', 'SecureBank');
define('SITE_URL', 'http://localhost:8080');

ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Strict');

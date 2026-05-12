<?php
define('APP_NAME', 'SMK Pertamaku');
define('APP_URL', 'http://localhost/webpertamaku');
define('APP_VERSION', '1.0.0');
define('UPLOAD_PATH', __DIR__ . '/../assets/images/uploads/');
define('UPLOAD_URL', APP_URL . '/assets/images/uploads/');

// Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Auto-load helpers
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../app/helpers.php';

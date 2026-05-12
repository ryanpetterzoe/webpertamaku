<?php
/**
 * App Configuration
 * Loads from env.php jika ada, fallback ke default jika belum diinstall
 */

// Load environment config jika ada
$_envFile = __DIR__ . '/env.php';
if (file_exists($_envFile)) {
    require_once $_envFile;
} else {
    // Default fallback — installer belum dijalankan
    // Konstanta minimal agar halaman tidak crash
    if (!defined('DB_HOST'))  define('DB_HOST',  'localhost');
    if (!defined('DB_USER'))  define('DB_USER',  'root');
    if (!defined('DB_PASS'))  define('DB_PASS',  '');
    if (!defined('DB_NAME'))  define('DB_NAME',  'websmk');
    if (!defined('DB_PORT'))  define('DB_PORT',  3306);
    if (!defined('APP_URL'))  define('APP_URL',  'http://localhost/webpertamaku');
    if (!defined('APP_BASE')) define('APP_BASE', '/webpertamaku');
    if (!defined('APP_ENV'))  define('APP_ENV',  'development');
    if (!defined('APP_TIMEZONE')) {
        define('APP_TIMEZONE', 'Asia/Jakarta');
        date_default_timezone_set('Asia/Jakarta');
    }
    if (!defined('INSTALLER_LOCKED')) define('INSTALLER_LOCKED', false);
}

// Derived constants
if (!defined('APP_VERSION')) define('APP_VERSION', '2.0.0');
if (!defined('UPLOAD_PATH')) define('UPLOAD_PATH', __DIR__ . '/../assets/images/uploads/');
if (!defined('UPLOAD_URL'))  define('UPLOAD_URL',  APP_URL . '/assets/images/uploads/');

// Error reporting
if (defined('APP_ENV') && APP_ENV === 'production') {
    error_reporting(0);
    ini_set('display_errors', 0);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Session
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 86400,
        'path'     => '/',
        'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// Auto-load helpers
require_once __DIR__ . '/../app/helpers.php';

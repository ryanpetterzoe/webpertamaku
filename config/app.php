<?php
/**
 * App Configuration
 * Loads from env.php — edit env.php untuk mengubah konfigurasi
 */

// Load environment config
require_once __DIR__ . '/env.php';

// Derived constants
define('APP_NAME',    getSetting_static('school_name') ?: 'SMK Pertamaku');
define('APP_VERSION', '2.0.0');
define('UPLOAD_PATH', __DIR__ . '/../assets/images/uploads/');
define('UPLOAD_URL',  APP_URL . '/assets/images/uploads/');

// Error reporting based on environment
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
        'secure'   => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// Auto-load helpers
require_once __DIR__ . '/../app/helpers.php';

// Lazy static getter used BEFORE DB is fully loaded (for APP_NAME)
function getSetting_static($key) {
    // Will return empty string before DB loads — that's fine,
    // actual getSetting() works after DB is available.
    return '';
}

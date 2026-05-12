<?php
/**
 * App Configuration
 * Auto-detects URL — works with vhosts, subfolder, or root domain
 * No hardcoded paths needed.
 */

// ── Load env.php jika ada ──────────────────────────────────────
if (file_exists(__DIR__ . '/env.php')) {
    require_once __DIR__ . '/env.php';
}

// ── Database defaults ──────────────────────────────────────────
if (!defined('DB_HOST'))   define('DB_HOST',   'localhost');
if (!defined('DB_USER'))   define('DB_USER',   'root');
if (!defined('DB_PASS'))   define('DB_PASS',   '');
if (!defined('DB_NAME'))   define('DB_NAME',   'websmk');
if (!defined('DB_PORT'))   define('DB_PORT',   3306);
if (!defined('DB_PREFIX')) define('DB_PREFIX', '');

// ── Auto-detect APP_URL dan APP_BASE ───────────────────────────
// Bekerja untuk: vhosts (base=''), subfolder (base='/smk'), hosting (base='')
if (!defined('APP_URL') || !defined('APP_BASE')) {
    // 1. Deteksi scheme
    $scheme = 'http';
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        $scheme = 'https';
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
        $scheme = $_SERVER['HTTP_X_FORWARDED_PROTO'];
    }

    // 2. Deteksi host
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';

    // 3. Deteksi base path — pakai DOCUMENT_ROOT vs direktori file ini
    // __FILE__ = /path/to/webroot/config/app.php → ambil parent directory
    $appDir  = str_replace('\\', '/', dirname(dirname(__FILE__))); // folder root project
    $docRoot = '';
    if (!empty($_SERVER['DOCUMENT_ROOT'])) {
        $docRoot = str_replace('\\', '/', rtrim($_SERVER['DOCUMENT_ROOT'], '/'));
    }

    if ($docRoot !== '' && strpos($appDir, $docRoot) === 0) {
        // Subfolder: /var/www/html/webpertamaku → base = /webpertamaku
        $base = substr($appDir, strlen($docRoot));
        $base = rtrim($base, '/');
    } else {
        // Vhost / root domain: document root IS the app dir → base = ''
        // Fallback ke SCRIPT_NAME kalau tidak bisa tentukan
        $sn   = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '/index.php';
        $base = rtrim(str_replace('\\', '/', dirname($sn)), '/');
        if ($base === '.') $base = '';
        // Kalau masih ada /config atau /routes di path, hapus (bisa terjadi saat CLI)
        $base = preg_replace('#/(config|routes|app|views|assets).*$#', '', $base);
    }

    if (!defined('APP_URL'))  define('APP_URL',  $scheme . '://' . $host . $base);
    if (!defined('APP_BASE')) define('APP_BASE', $base);
}

// ── App constants ──────────────────────────────────────────────
if (!defined('APP_ENV'))      define('APP_ENV',      'development');
if (!defined('APP_TIMEZONE')) define('APP_TIMEZONE', 'Asia/Jakarta');
if (!defined('APP_KEY'))      define('APP_KEY',      'smk_default_key');
if (!defined('APP_VERSION'))  define('APP_VERSION',  '2.0.0');
if (!defined('INSTALLER_LOCKED')) define('INSTALLER_LOCKED', false);

// ── Paths ──────────────────────────────────────────────────────
if (!defined('UPLOAD_PATH')) define('UPLOAD_PATH', __DIR__ . '/../assets/images/uploads/');
if (!defined('UPLOAD_URL'))  define('UPLOAD_URL',  APP_URL . '/assets/images/uploads/');

// ── Timezone ──────────────────────────────────────────────────
date_default_timezone_set(APP_TIMEZONE);

// ── Error reporting ────────────────────────────────────────────
if (APP_ENV === 'production') {
    error_reporting(0);
    ini_set('display_errors', '0');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

// ── Session ────────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── Load DB & Helpers ──────────────────────────────────────────
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../app/helpers.php';

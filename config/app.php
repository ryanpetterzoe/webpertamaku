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
// Ini yang membuat web flexible: vhosts, subfolder, root — semua jalan
if (!defined('APP_URL') || !defined('APP_BASE')) {
    // Deteksi scheme
    $scheme = 'http';
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        $scheme = 'https';
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
        $scheme = $_SERVER['HTTP_X_FORWARDED_PROTO'];
    }

    // Deteksi host
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';

    // Deteksi base path dari SCRIPT_FILENAME vs DOCUMENT_ROOT
    // Ini bekerja untuk vhosts (base='') maupun subfolder (base='/namaFolder')
    $docRoot    = isset($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : '';
    $scriptDir  = dirname(isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : __FILE__);
    $scriptDir  = realpath($scriptDir);

    if ($docRoot && $scriptDir && strpos($scriptDir, $docRoot) === 0) {
        $base = str_replace($docRoot, '', $scriptDir);
        $base = str_replace('\\', '/', $base); // Windows path fix
        $base = rtrim($base, '/');
    } else {
        // Fallback: ambil dari SCRIPT_NAME
        $base = rtrim(dirname(isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '/'), '/');
        if ($base === '.') $base = '';
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

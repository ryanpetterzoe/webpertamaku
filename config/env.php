<?php
/**
 * Environment Configuration
 * ──────────────────────────────────────────────────────────────
 * CATATAN: APP_URL dan APP_BASE TIDAK perlu diisi jika menggunakan
 * auto-detect (default). Sistem akan otomatis mendeteksi berdasarkan
 * environment (vhosts, subfolder, atau root domain).
 *
 * Hanya isi jika auto-detect tidak akurat (misal di balik reverse proxy).
 * ──────────────────────────────────────────────────────────────
 */

// ── Database ───────────────────────────────────────────────────
define('DB_HOST',   'localhost');
define('DB_USER',   'root');
define('DB_PASS',   '');
define('DB_NAME',   'websmk');
define('DB_PORT',   3306);
define('DB_PREFIX', '');

// ── APP_URL & APP_BASE ─────────────────────────────────────────
// Biarkan di-comment untuk auto-detect (DIREKOMENDASIKAN)
// Uncomment dan isi HANYA jika auto-detect salah:
//
// Contoh subfolder    : define('APP_URL', 'http://localhost/webpertamaku');
//                       define('APP_BASE', '/webpertamaku');
//
// Contoh vhosts/domain: define('APP_URL', 'http://smk.local');
//                       define('APP_BASE', '');
//
// Contoh hosting      : define('APP_URL', 'https://smkpertamaku.sch.id');
//                       define('APP_BASE', '');

// ── Settings ───────────────────────────────────────────────────
define('APP_KEY',      'smk_default_key_ganti_ini');
define('APP_TIMEZONE', 'Asia/Jakarta');
date_default_timezone_set(APP_TIMEZONE);
define('APP_ENV',      'development');
define('INSTALLER_LOCKED', false);

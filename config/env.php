<?php
/**
 * Environment Configuration
 * ─────────────────────────────────────────────────────────────────
 * File ini berisi konfigurasi database dan aplikasi.
 *
 * CARA SETUP:
 *   Opsi 1 (Direkomendasikan): Buka install.php di browser dan ikuti wizard
 *   Opsi 2 (Manual): Edit nilai-nilai di bawah ini sesuai environment kamu
 *
 * UNTUK XAMPP LOKAL (default):
 *   - DB_USER = 'root', DB_PASS = '' (kosong), DB_NAME bebas
 *   - APP_URL  = 'http://localhost/webpertamaku'
 *   - APP_BASE = '/webpertamaku'
 *
 * UNTUK HOSTING ONLINE:
 *   - DB_USER, DB_PASS, DB_NAME sesuai panel hosting (cPanel/Plesk)
 *   - APP_URL  = 'https://namadomain.com' (tanpa trailing slash)
 *   - APP_BASE = '' (kosong karena di root domain)
 * ─────────────────────────────────────────────────────────────────
 */

// ── Database ───────────────────────────────────────────────────
define('DB_HOST',   'localhost');   // host database (biasanya localhost)
define('DB_USER',   'root');        // username MySQL (XAMPP default: root)
define('DB_PASS',   '');            // password MySQL (XAMPP default: kosong)
define('DB_NAME',   'websmk');      // nama database (harus sudah dibuat)
define('DB_PORT',   3306);          // port MySQL (default: 3306)
define('DB_PREFIX', '');            // prefix tabel (kosongkan jika tidak perlu)

// ── Aplikasi ───────────────────────────────────────────────────
// APP_URL : URL lengkap tanpa trailing slash
// APP_BASE: path folder dari domain root (kosong jika di root domain)
define('APP_URL',  'http://localhost/webpertamaku');
define('APP_BASE', '/webpertamaku');

// ── Keamanan ───────────────────────────────────────────────────
define('APP_KEY', 'smk_default_key_ganti_setelah_install');

// ── Timezone ───────────────────────────────────────────────────
// Pilihan: Asia/Jakarta (WIB), Asia/Makassar (WITA), Asia/Jayapura (WIT), UTC
define('APP_TIMEZONE', 'Asia/Jakarta');
date_default_timezone_set(APP_TIMEZONE);

// ── Mode Error ─────────────────────────────────────────────────
// 'development' = tampilkan error (untuk lokal)
// 'production'  = sembunyikan error (untuk hosting)
define('APP_ENV', 'development');

// ── Installer ──────────────────────────────────────────────────
// Ubah ke true setelah instalasi selesai untuk mengunci installer
define('INSTALLER_LOCKED', false);

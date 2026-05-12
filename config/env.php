<?php
/**
 * Konfigurasi Environment - Template Default
 * ──────────────────────────────────────────────────────────────
 * File ini adalah TEMPLATE dengan nilai default untuk XAMPP.
 *
 * CARA SETUP (pilih salah satu):
 *   1. Jalankan installer: buka http://[situs-anda]/install.php
 *      → installer akan otomatis menimpa file ini dengan nilai yang benar
 *
 *   2. Edit manual file ini sesuai konfigurasi database kamu.
 *
 * CATATAN: DB_NAME 'websmk' di bawah adalah NAMA DEFAULT.
 * Kamu bebas menggantinya dengan nama database apapun.
 * Database tidak perlu dibuat dulu — installer akan membuatnya otomatis.
 * ──────────────────────────────────────────────────────────────
 */

// ── Database ───────────────────────────────────────────────────
// Ganti sesuai konfigurasi MySQL kamu
define('DB_HOST',   'localhost');   // Host MySQL (biasanya localhost)
define('DB_USER',   'root');        // Username MySQL (XAMPP default: root)
define('DB_PASS',   '');            // Password MySQL (XAMPP default: kosong '')
define('DB_NAME',   'websmk');      // Nama database — BEBAS, tidak harus 'websmk'
define('DB_PORT',   3306);          // Port MySQL (default 3306)
define('DB_PREFIX', '');

// ── App ────────────────────────────────────────────────────────
define('APP_KEY',      'default_key_ganti_setelah_install');
define('APP_TIMEZONE', 'Asia/Jakarta');
date_default_timezone_set(APP_TIMEZONE);
define('APP_ENV',      'development'); // 'production' untuk hosting
define('INSTALLER_LOCKED', false);

// ── URL (OPSIONAL - biarkan di-comment untuk auto-detect) ──────
// Sistem otomatis mendeteksi URL berdasarkan environment:
//   - vhosts  (http://smk.local)       → tidak perlu diisi
//   - subfolder (localhost/namafolder)  → tidak perlu diisi
//   - hosting (https://domain.com)     → tidak perlu diisi
//
// HANYA uncomment jika auto-detect tidak akurat:
// define('APP_URL',  'http://smk.local');
// define('APP_BASE', '');

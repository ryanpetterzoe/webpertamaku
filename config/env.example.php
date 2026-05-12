<?php
/**
 * Environment Configuration Template
 * ────────────────────────────────────────────────────────────
 * CARA PENGGUNAAN:
 *   1. Salin file ini menjadi config/env.php
 *   2. Isi nilai yang sesuai
 *   3. JANGAN commit config/env.php ke Git!
 *
 *   ATAU gunakan Installer Wizard: buka install.php di browser
 *   dan ikuti langkah-langkahnya secara otomatis.
 * ────────────────────────────────────────────────────────────
 */

// ── Database ──────────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'websmk');
define('DB_PORT', 3306);
define('DB_PREFIX', '');          // prefix tabel jika perlu

// ── Aplikasi ──────────────────────────────────────
// APP_URL: tanpa trailing slash
// Contoh lokal  : http://localhost/webpertamaku
// Contoh hosting: https://smkpertamaku.sch.id
define('APP_URL', 'http://localhost/webpertamaku');

// Nama folder instalasi (hanya nama folder, bukan full path)
// Biarkan kosong '' jika domain root (hosting)
define('APP_BASE', '/webpertamaku');

// ── Keamanan ──────────────────────────────────────
define('APP_KEY', 'smk_' . md5('webpertamaku_2025'));

// ── Timezone ──────────────────────────────────────
define('APP_TIMEZONE', 'Asia/Jakarta');
date_default_timezone_set(APP_TIMEZONE);

// ── Mode ──────────────────────────────────────────
// 'development' → tampilkan error | 'production' → sembunyikan error
define('APP_ENV', 'development');

// ── Installer ─────────────────────────────────────
// Ubah ke TRUE untuk menonaktifkan akses installer
define('INSTALLER_LOCKED', false);

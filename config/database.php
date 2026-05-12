<?php
/**
 * Database Configuration
 * Credentials dibaca dari konstanta yang sudah di-define di config/app.php
 * Tidak crash jika env.php belum ada (installer belum dijalankan)
 */

function getDB() {
    static $conn = null;
    if ($conn !== null) return $conn;

    $host = defined('DB_HOST') ? DB_HOST : 'localhost';
    $user = defined('DB_USER') ? DB_USER : 'root';
    $pass = defined('DB_PASS') ? DB_PASS : '';
    $name = defined('DB_NAME') ? DB_NAME : 'websmk';
    $port = defined('DB_PORT') ? (int)DB_PORT : 3306;

    // Coba koneksi
    $conn = @new mysqli($host, $user, $pass, $name, $port);

    if ($conn->connect_error) {
        $errMsg = htmlspecialchars($conn->connect_error);
        $installerUrl = (defined('APP_URL') ? APP_URL : '') . '/install.php';
        $appUrl       = defined('APP_URL') ? APP_URL : '';

        // Cek apakah ini request AJAX / non-HTML
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']);
        if ($isAjax) {
            http_response_code(503);
            header('Content-Type: application/json');
            die(json_encode(['error' => 'Database connection failed']));
        }

        http_response_code(503);
        die('<!DOCTYPE html><html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Koneksi Database Gagal</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{min-height:100vh;background:linear-gradient(135deg,#0f172a,#1e293b);
  display:flex;align-items:center;justify-content:center;
  font-family:"Segoe UI",system-ui,sans-serif;color:#e2e8f0;padding:24px}
.box{background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.12);
  border-radius:20px;padding:40px;max-width:520px;width:100%;text-align:center}
.icon{font-size:48px;margin-bottom:16px}
h2{color:#f87171;font-size:1.4rem;margin-bottom:12px}
p{color:#94a3b8;line-height:1.7;margin-bottom:10px;font-size:.92rem}
code{background:rgba(0,0,0,.3);padding:3px 8px;border-radius:6px;font-size:.85rem;color:#fbbf24}
.btn{display:inline-block;margin-top:20px;padding:11px 28px;border-radius:10px;
  background:linear-gradient(135deg,#3b82f6,#6366f1);color:#fff;
  text-decoration:none;font-weight:600;font-size:.9rem}
.tip{background:rgba(251,191,36,.08);border:1px solid rgba(251,191,36,.2);
  border-radius:10px;padding:14px;margin-top:20px;text-align:left;font-size:.84rem;color:#fde68a}
</style>
</head>
<body>
<div class="box">
  <div class="icon">⚠️</div>
  <h2>Koneksi Database Gagal</h2>
  <p>Tidak dapat terhubung ke server MySQL.</p>
  <p><code>' . $errMsg . '</code></p>
  <p>Kemungkinan penyebab: MySQL belum aktif, username/password salah, atau database belum dibuat.</p>
  <a href="' . htmlspecialchars($installerUrl) . '" class="btn">🔧 Jalankan Installer</a>
  <div class="tip">
    <strong>Cek juga:</strong><br>
    1. Pastikan XAMPP → MySQL sudah <strong>Start</strong><br>
    2. Edit <code>config/env.php</code> dan sesuaikan kredensial<br>
    3. Atau jalankan installer ulang untuk konfigurasi otomatis
  </div>
</div>
</body></html>');
    }

    $conn->set_charset('utf8mb4');
    return $conn;
}

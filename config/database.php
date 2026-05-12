<?php
/**
 * Database - getDB() function
 * Compatible PHP 7.0+
 */

function getDB() {
    static $conn = null;
    if ($conn !== null) return $conn;

    $host = defined('DB_HOST') ? DB_HOST : 'localhost';
    $user = defined('DB_USER') ? DB_USER : 'root';
    $pass = defined('DB_PASS') ? DB_PASS : '';
    $name = defined('DB_NAME') ? DB_NAME : 'websmk';
    $port = defined('DB_PORT') ? (int)DB_PORT : 3306;

    $conn = @new mysqli($host, $user, $pass, $name, $port);

    if ($conn->connect_error) {
        $err   = htmlspecialchars($conn->connect_error);
        $iUrl  = (defined('APP_URL') ? APP_URL : '') . '/install.php';
        http_response_code(503);
        die('<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>DB Error</title>
<style>body{font-family:sans-serif;background:#0f172a;color:#e2e8f0;display:flex;
align-items:center;justify-content:center;min-height:100vh;margin:0;padding:20px}
.box{background:#1e293b;border:1px solid #334155;border-radius:12px;padding:32px;
max-width:480px;text-align:center}h2{color:#f87171;margin:0 0 12px}
p{color:#94a3b8;margin:8px 0;font-size:.9rem}code{background:#0f172a;padding:3px 8px;
border-radius:4px;color:#fbbf24;font-size:.85rem}
.btn{display:inline-block;margin-top:16px;padding:10px 24px;border-radius:8px;
background:#3b82f6;color:#fff;text-decoration:none;font-weight:600}</style>
</head><body><div class="box">
<h2>&#x26A0; Koneksi Database Gagal</h2>
<p><code>' . $err . '</code></p>
<p>Pastikan MySQL sudah <strong>Start</strong> di XAMPP Control Panel.</p>
<a href="' . htmlspecialchars($iUrl) . '" class="btn">&#x1F527; Buka Installer</a>
</div></body></html>');
    }

    $conn->set_charset('utf8mb4');
    return $conn;
}

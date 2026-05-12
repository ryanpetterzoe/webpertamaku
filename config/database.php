<?php
/**
 * Database Configuration
 * Credentials dibaca dari config/env.php
 */

function getDB(): mysqli {
    static $conn = null;
    if ($conn !== null) return $conn;

    $host = defined('DB_HOST') ? DB_HOST : 'localhost';
    $user = defined('DB_USER') ? DB_USER : 'root';
    $pass = defined('DB_PASS') ? DB_PASS : '';
    $name = defined('DB_NAME') ? DB_NAME : 'websmk';
    $port = defined('DB_PORT') ? (int)DB_PORT : 3306;

    $conn = new mysqli($host, $user, $pass, $name, $port);

    if ($conn->connect_error) {
        // Friendly error page instead of raw die()
        $msg = htmlspecialchars($conn->connect_error);
        http_response_code(503);
        die("<!DOCTYPE html><html lang='id'><head><meta charset='UTF-8'>
        <title>Database Error</title>
        <style>body{font-family:system-ui;background:#0f172a;color:#e2e8f0;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;}
        .box{background:#1e293b;border:1px solid #334155;border-radius:12px;padding:40px;max-width:500px;text-align:center;}
        h2{color:#ef4444;} a{color:#3b82f6;} code{background:#0f172a;padding:4px 8px;border-radius:4px;font-size:.85rem;}</style>
        </head><body><div class='box'>
        <h2>&#x26A0; Koneksi Database Gagal</h2>
        <p>Tidak dapat terhubung ke database MySQL.</p>
        <p><code>$msg</code></p>
        <p>Silakan jalankan <a href='install.php'>Installer</a> untuk mengatur ulang konfigurasi,
        atau edit file <code>config/env.php</code> secara manual.</p>
        </div></body></html>");
    }

    $conn->set_charset('utf8mb4');
    return $conn;
}

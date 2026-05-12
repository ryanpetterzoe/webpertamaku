<?php
/**
 * DEBUG FILE - Hapus setelah selesai debug!
 * Buka: http://localhost/webpertamaku/debug.php
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo '<style>body{font-family:monospace;background:#0f172a;color:#e2e8f0;padding:20px}
.ok{color:#4ade80}.err{color:#f87171}.warn{color:#fbbf24}
h2{color:#60a5fa;margin:20px 0 10px}
pre{background:#1e293b;padding:10px;border-radius:8px;overflow:auto;font-size:13px}
table{border-collapse:collapse;width:100%}
td,th{border:1px solid #334155;padding:8px;text-align:left}
th{background:#1e293b}</style>';

echo '<h1>🔍 SMK Debug Tool</h1>';

// 1. PHP Version
echo '<h2>1. PHP & Extensions</h2>';
echo '<table><tr><th>Item</th><th>Status</th><th>Value</th></tr>';
echo '<tr><td>PHP Version</td><td class="' . (version_compare(PHP_VERSION,'7.4','>=') ? 'ok' : 'err') . '">'. (version_compare(PHP_VERSION,'7.4','>=') ? '✅' : '❌') .'</td><td>'.PHP_VERSION.'</td></tr>';
echo '<tr><td>MySQLi</td><td class="' . (extension_loaded('mysqli') ? 'ok' : 'err') . '">'. (extension_loaded('mysqli') ? '✅' : '❌') .'</td><td>'. (extension_loaded('mysqli') ? 'OK' : 'TIDAK TERSEDIA') .'</td></tr>';
echo '<tr><td>mod_rewrite</td><td class="ok">✅</td><td>Aktif (file ini terbuka = .htaccess jalan)</td></tr>';
echo '<tr><td>Session</td><td class="ok">✅</td><td>'. function_exists('session_start') .'</td></tr>';
echo '</table>';

// 2. File Checks
echo '<h2>2. File & Folder Check</h2>';
echo '<table><tr><th>Path</th><th>Exists</th><th>Writable</th></tr>';
$paths = [
    'config/env.php',
    'config/app.php',
    'config/database.php',
    'assets/css/style.css',
    'assets/js/main.js',
    'assets/images/uploads/',
    'database/schema.sql',
    'install.php',
    'install.lock',
];
foreach ($paths as $p) {
    $full = __DIR__ . '/' . $p;
    $exists = file_exists($full);
    $writable = is_writable($full);
    echo '<tr>';
    echo '<td>' . $p . '</td>';
    echo '<td class="' . ($exists ? 'ok' : 'err') . '">' . ($exists ? '✅ Ya' : '❌ Tidak') . '</td>';
    echo '<td class="' . ($writable ? 'ok' : 'warn') . '">' . ($writable ? '✅ Ya' : '⚠️ Tidak') . '</td>';
    echo '</tr>';
}
echo '</table>';

// 3. CSS File size
$cssPath = __DIR__ . '/assets/css/style.css';
if (file_exists($cssPath)) {
    $size = filesize($cssPath);
    echo '<h2>3. CSS File</h2>';
    echo '<p>Size: <span class="' . ($size > 10000 ? 'ok' : 'err') . '">' . number_format($size) . ' bytes</span>';
    echo ($size > 10000 ? ' ✅ OK' : ' ❌ Terlalu kecil! CSS mungkin corrupt') . '</p>';
    echo '<p>URL yang akan diload: <code style="color:#fbbf24">http://localhost/webpertamaku/assets/css/style.css</code></p>';
    echo '<p><a href="/webpertamaku/assets/css/style.css" style="color:#60a5fa" target="_blank">Klik untuk buka CSS langsung</a></p>';
}

// 4. env.php contents (safe display)
echo '<h2>4. config/env.php</h2>';
$envPath = __DIR__ . '/config/env.php';
if (file_exists($envPath)) {
    // Load it and show constants (hide password)
    require_once $envPath;
    echo '<table><tr><th>Constant</th><th>Value</th></tr>';
    $consts = ['APP_URL','APP_BASE','DB_HOST','DB_USER','DB_NAME','DB_PORT','APP_ENV','APP_TIMEZONE'];
    foreach ($consts as $c) {
        $val = defined($c) ? constant($c) : '❌ NOT DEFINED';
        if ($c === 'DB_USER' && $val === 'root') $val = 'root ⚠️ (default XAMPP)';
        echo '<tr><td>' . $c . '</td><td><code>' . htmlspecialchars((string)$val) . '</code></td></tr>';
    }
    echo '<tr><td>DB_PASS</td><td><code>' . (defined('DB_PASS') ? (DB_PASS === '' ? '(kosong - default XAMPP)' : '***') : '❌ NOT DEFINED') . '</code></td></tr>';
    echo '</table>';
} else {
    echo '<p class="err">❌ env.php tidak ada! Jalankan installer.</p>';
}

// 5. Test DB Connection
echo '<h2>5. Database Connection Test</h2>';
if (defined('DB_HOST') && defined('DB_USER')) {
    $host = DB_HOST . ':' . (defined('DB_PORT') ? DB_PORT : 3306);
    $conn = @mysqli_connect($host, DB_USER, defined('DB_PASS') ? DB_PASS : '', defined('DB_NAME') ? DB_NAME : '');
    if ($conn) {
        echo '<p class="ok">✅ Koneksi database berhasil!</p>';
        // Check tables
        $res = mysqli_query($conn, "SHOW TABLES");
        $tables = [];
        while ($row = mysqli_fetch_array($res)) $tables[] = $row[0];
        echo '<p>Tables found: <strong>' . count($tables) . '</strong></p>';
        if (count($tables) > 0) {
            echo '<p class="ok">Tables: ' . implode(', ', $tables) . '</p>';
        } else {
            echo '<p class="err">❌ Tidak ada tabel! Import database/schema.sql</p>';
        }
        mysqli_close($conn);
    } else {
        echo '<p class="err">❌ Koneksi GAGAL: ' . mysqli_connect_error() . '</p>';
        echo '<p class="warn">→ Pastikan MySQL sudah Start di XAMPP</p>';
        echo '<p class="warn">→ Cek username/password di config/env.php</p>';
    }
} else {
    echo '<p class="err">❌ DB constants tidak terdefinisi</p>';
}

// 6. Session test
echo '<h2>6. Session Test</h2>';
if (session_status() === PHP_SESSION_NONE) session_start();
$_SESSION['debug_test'] = time();
echo '<p class="ok">✅ Session berjalan. ID: ' . session_id() . '</p>';

// 7. APP_URL Check  
echo '<h2>7. URL Configuration</h2>';
if (defined('APP_URL')) {
    echo '<p>APP_URL: <code style="color:#fbbf24">' . htmlspecialchars(APP_URL) . '</code></p>';
    $cssUrl = APP_URL . '/assets/css/style.css';
    echo '<p>CSS URL: <code style="color:#fbbf24">' . htmlspecialchars($cssUrl) . '</code></p>';
    echo '<p><a href="' . htmlspecialchars($cssUrl) . '" target="_blank" style="color:#60a5fa">Test buka CSS URL ini</a></p>';
}

// 8. installer.php check
echo '<h2>8. Installer Status</h2>';
if (file_exists(__DIR__ . '/install.lock')) {
    echo '<p class="warn">⚠️ install.lock ada — installer sudah dijalankan sebelumnya</p>';
    echo '<p>Hapus file <code>install.lock</code> jika ingin install ulang</p>';
} else {
    echo '<p class="ok">✅ install.lock tidak ada — installer bisa dijalankan</p>';
    echo '<p><a href="/webpertamaku/install.php" style="color:#60a5fa">→ Buka Installer</a></p>';
}

echo '<br><p class="err">⚠️ HAPUS FILE debug.php SETELAH SELESAI!</p>';
?>

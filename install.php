<?php
ob_start();
/**
 * SMK Installer - Simple & Reliable
 * PHP 7.0+ compatible, no external dependencies
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
set_time_limit(300);
ini_set('max_execution_time', '300');
ini_set('display_errors', '1');
error_reporting(E_ALL);

// ── Auto-detect base URL for links ────────────────────────────
$scheme   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host     = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
$selfDir  = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
$BASE_URL = $scheme . '://' . $host . $selfDir; // e.g. http://smk.local or http://localhost/webpertamaku

// ── Lock check ────────────────────────────────────────────────
if (file_exists(__DIR__ . '/install.lock')) {
    $appUrl = '';
    if (file_exists(__DIR__ . '/config/env.php')) {
        $raw = file_get_contents(__DIR__ . '/config/env.php');
        if (preg_match("/define\('APP_URL'\s*,\s*'([^']+)'\)/", $raw, $m)) {
            $appUrl = $m[1];
        }
    }
    ob_end_clean();
    die('<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Installer Terkunci</title>
<style>*{margin:0;padding:0;box-sizing:border-box}
body{background:#0f172a;color:#e2e8f0;font-family:sans-serif;display:flex;
align-items:center;justify-content:center;min-height:100vh;padding:20px}
.box{background:#1e293b;border:1px solid #334155;border-radius:12px;padding:40px;
max-width:460px;width:100%;text-align:center}
.icon{font-size:52px;margin-bottom:16px}
h1{color:#f1f5f9;font-size:1.4rem;margin-bottom:10px}
p{color:#94a3b8;font-size:.9rem;line-height:1.6;margin-bottom:16px}
.btn{display:inline-block;padding:10px 22px;border-radius:8px;text-decoration:none;
font-weight:600;margin:4px;font-size:.9rem}
.btn-blue{background:#3b82f6;color:#fff}
.btn-gray{background:#475569;color:#fff}
.tip{background:rgba(251,191,36,.08);border:1px solid rgba(251,191,36,.2);
border-radius:8px;padding:12px 14px;margin-top:16px;font-size:.82rem;
color:#fde68a;text-align:left}
code{background:rgba(0,0,0,.4);padding:2px 6px;border-radius:4px;font-family:monospace;font-size:.8rem}
</style></head><body><div class="box">
<div class="icon">&#x1F512;</div>
<h1>Installer Sudah Selesai</h1>
<p>File installer telah dikunci. Website sudah siap digunakan.</p>
' . ($appUrl ? '<a href="' . htmlspecialchars($appUrl) . '/" class="btn btn-blue">&#x1F310; Buka Website</a>
<a href="' . htmlspecialchars($appUrl) . '/admin/login" class="btn btn-gray">&#x2699; Admin Panel</a>' : '') . '
<div class="tip"><strong>&#x26A0; Ingin install ulang?</strong><br>
Hapus file: <code>' . htmlspecialchars(__DIR__ . DIRECTORY_SEPARATOR . 'install.lock') . '</code></div>
</div></body></html>');
}

// ── Step ─────────────────────────────────────────────────────
$step   = isset($_GET['step']) ? max(1, min(4, (int)$_GET['step'])) : 1;
$errors = array();

// ── POST Step 2: Test DB + Save Config ────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 2) {
    $db_host  = isset($_POST['db_host'])  ? trim($_POST['db_host'])             : 'localhost';
    $db_port  = isset($_POST['db_port'])  ? (int)$_POST['db_port']              : 3306;
    $db_user  = isset($_POST['db_user'])  ? trim($_POST['db_user'])             : 'root';
    $db_pass  = isset($_POST['db_pass'])  ? $_POST['db_pass']                   : '';
    $db_name  = isset($_POST['db_name'])  ? trim($_POST['db_name'])             : 'websmk';
    $timezone = isset($_POST['timezone']) ? trim($_POST['timezone'])            : 'Asia/Jakarta';
    $tz_ok    = array('Asia/Jakarta', 'Asia/Makassar', 'Asia/Jayapura', 'UTC');
    if (!in_array($timezone, $tz_ok)) $timezone = 'Asia/Jakarta';

    if (empty($db_host)) $errors[] = 'Host database wajib diisi.';
    if (empty($db_name)) $errors[] = 'Nama database wajib diisi.';

    if (empty($errors)) {
        $conn = @mysqli_connect($db_host . ':' . $db_port, $db_user, $db_pass);
        if (!$conn) {
            $errors[] = 'Koneksi MySQL gagal: ' . mysqli_connect_error();
        } else {
            mysqli_close($conn);
            $_SESSION['smk_install'] = array(
                'db_host'  => $db_host,
                'db_port'  => $db_port,
                'db_user'  => $db_user,
                'db_pass'  => $db_pass,
                'db_name'  => $db_name,
                'timezone' => $timezone,
            );
            ob_end_clean();
            header('Location: ' . $BASE_URL . '/install.php?step=3');
            exit;
        }
    }
}

// ── POST Step 3: Validate Admin ───────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 3) {
    $admin_name  = isset($_POST['admin_name'])  ? trim($_POST['admin_name'])  : '';
    $admin_user  = isset($_POST['admin_user'])  ? trim($_POST['admin_user'])  : '';
    $admin_email = isset($_POST['admin_email']) ? trim($_POST['admin_email']) : '';
    $admin_pass  = isset($_POST['admin_pass'])  ? $_POST['admin_pass']        : '';
    $admin_pass2 = isset($_POST['admin_pass2']) ? $_POST['admin_pass2']       : '';

    if (empty($admin_name))                                          $errors[] = 'Nama lengkap wajib diisi.';
    if (!preg_match('/^[a-zA-Z0-9_]{3,}$/', $admin_user))           $errors[] = 'Username minimal 3 karakter, huruf/angka/underscore.';
    if (!filter_var($admin_email, FILTER_VALIDATE_EMAIL))            $errors[] = 'Format email tidak valid.';
    if (strlen($admin_pass) < 6)                                     $errors[] = 'Password minimal 6 karakter.';
    if ($admin_pass !== $admin_pass2)                                $errors[] = 'Konfirmasi password tidak cocok.';

    if (empty($errors)) {
        $_SESSION['smk_admin'] = array(
            'name'  => $admin_name,
            'user'  => $admin_user,
            'email' => $admin_email,
            'pass'  => $admin_pass,
        );
        ob_end_clean();
        header('Location: ' . $BASE_URL . '/install.php?step=4');
        exit;
    }
}

// ── POST Step 4: Do Install ───────────────────────────────────
$log  = array(); // array of ['ok'=>bool, 'msg'=>string]
$done = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 4
    && isset($_POST['action']) && $_POST['action'] === 'install') {

    set_time_limit(300);

    $db  = isset($_SESSION['smk_install']) ? $_SESSION['smk_install'] : array();
    $adm = isset($_SESSION['smk_admin'])   ? $_SESSION['smk_admin']   : array();

    if (empty($db) || empty($adm)) {
        $errors[] = 'Session hilang. Silakan mulai ulang dari Langkah 1.';
    } else {

        // 1. Connect (no DB selected)
        $c = @mysqli_connect($db['db_host'] . ':' . $db['db_port'], $db['db_user'], $db['db_pass']);
        if (!$c) {
            $log[] = array('ok' => false, 'msg' => 'Koneksi MySQL gagal: ' . mysqli_connect_error());
            goto finish_install;
        }
        $log[] = array('ok' => true, 'msg' => 'Koneksi MySQL berhasil');

        // 2. Create database
        $dbname = $db['db_name'];
        $r = mysqli_query($c, "CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $log[] = array('ok' => (bool)$r, 'msg' => $r ? "Database <b>{$dbname}</b> siap" : 'Gagal buat DB: ' . mysqli_error($c));

        // 3. Select database
        $r = mysqli_select_db($c, $dbname);
        $log[] = array('ok' => (bool)$r, 'msg' => $r ? "Database dipilih" : 'Gagal pilih DB: ' . mysqli_error($c));
        if (!$r) { mysqli_close($c); goto finish_install; }

        // 4. Run schema.sql
        $sf = __DIR__ . '/database/schema.sql';
        if (!file_exists($sf)) {
            $log[] = array('ok' => false, 'msg' => 'File database/schema.sql TIDAK DITEMUKAN!');
        } else {
            $sql = file_get_contents($sf);
            $sql = preg_replace('/--[^\n]*/', '', $sql);           // hapus komentar --
            $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);       // hapus /* */
            $stmts = array_filter(array_map('trim', explode(';', $sql)));
            $ok = true; $run = 0; $skip = 0; $lastErr = '';
            $ignore_codes = array(1007, 1050, 1051, 1061, 1062);
            foreach ($stmts as $s) {
                if ($s === '') continue;
                $run++;
                if (!mysqli_query($c, $s)) {
                    $errno = mysqli_errno($c);
                    if (in_array($errno, $ignore_codes)) {
                        $skip++;
                    } else {
                        $ok = false;
                        $lastErr = mysqli_error($c);
                    }
                }
            }
            $log[] = array('ok' => $ok, 'msg' => "Schema: {$run} query, {$skip} dilewati"
                . ($lastErr ? " | Error: {$lastErr}" : ''));
        }

        // 5. Buat/update admin
        $hp = password_hash($adm['pass'], PASSWORD_DEFAULT);
        $sn = mysqli_real_escape_string($c, $adm['name']);
        $su = mysqli_real_escape_string($c, $adm['user']);
        $se = mysqli_real_escape_string($c, $adm['email']);
        $sp = mysqli_real_escape_string($c, $hp);
        $r = mysqli_query($c,
            "INSERT INTO `admins` (`name`,`username`,`email`,`password`,`role`)
             VALUES ('{$sn}','{$su}','{$se}','{$sp}','superadmin')
             ON DUPLICATE KEY UPDATE
               `name`=VALUES(`name`), `password`=VALUES(`password`), `email`=VALUES(`email`)");
        $log[] = array('ok' => (bool)$r, 'msg' => $r ? "Admin <b>{$su}</b> disimpan" : 'Gagal simpan admin: ' . mysqli_error($c));
        mysqli_close($c);

        // 6. Tulis config/env.php
        $appKey = bin2hex(random_bytes(24));
        $envContent = "<?php\n"
            . "// Auto-generated by installer\n"
            . "define('DB_HOST',          '" . addslashes($db['db_host'])  . "');\n"
            . "define('DB_USER',          '" . addslashes($db['db_user'])  . "');\n"
            . "define('DB_PASS',          '" . addslashes($db['db_pass'])  . "');\n"
            . "define('DB_NAME',          '" . addslashes($db['db_name'])  . "');\n"
            . "define('DB_PORT',          " . (int)$db['db_port']          . ");\n"
            . "define('DB_PREFIX',        '');\n"
            . "define('APP_KEY',          '" . $appKey . "');\n"
            . "define('APP_TIMEZONE',     '" . $db['timezone'] . "');\n"
            . "date_default_timezone_set(APP_TIMEZONE);\n"
            . "define('APP_ENV',          'production');\n"
            . "define('INSTALLER_LOCKED', false);\n"
            . "// APP_URL dan APP_BASE sengaja tidak di-set\n"
            . "// agar auto-detect dari environment (vhosts, subfolder, dll)\n"
            . "// Uncomment baris di bawah HANYA jika auto-detect tidak akurat:\n"
            . "// define('APP_URL',  'http://smk.local');\n"
            . "// define('APP_BASE', '');\n";

        $w = file_put_contents(__DIR__ . '/config/env.php', $envContent);
        $log[] = array('ok' => $w !== false, 'msg' => $w !== false
            ? 'config/env.php berhasil ditulis'
            : 'GAGAL tulis env.php! Pastikan folder config/ bisa ditulis.');

        // 7. Tulis install.lock
        file_put_contents(__DIR__ . '/install.lock', date('Y-m-d H:i:s') . "\n");
        $log[] = array('ok' => true, 'msg' => 'install.lock dibuat');

        finish_install:
        // Cek apakah ada error kritis
        $hasFail = false;
        foreach ($log as $l) { if (!$l['ok']) { $hasFail = true; break; } }
        $done = !$hasFail;
    }
}

// ── Step 1: Requirements ─────────────────────────────────────
$reqs = array();
if ($step === 1) {
    $reqs = array(
        array('label' => 'PHP >= 7.0',
              'detail' => 'Versi PHP: ' . PHP_VERSION,
              'ok'     => version_compare(PHP_VERSION, '7.0.0', '>='),
              'req'    => true),
        array('label' => 'Ekstensi MySQLi',
              'detail' => extension_loaded('mysqli') ? 'Tersedia' : 'Tidak tersedia — aktifkan di php.ini',
              'ok'     => extension_loaded('mysqli'),
              'req'    => true),
        array('label' => 'Folder config/ bisa ditulis',
              'detail' => is_writable(__DIR__ . '/config/') ? 'OK' : 'Tidak writable',
              'ok'     => is_writable(__DIR__ . '/config/'),
              'req'    => true),
        array('label' => 'Folder uploads/ bisa ditulis',
              'detail' => is_writable(__DIR__ . '/assets/images/uploads/') ? 'OK' : 'Tidak writable',
              'ok'     => is_writable(__DIR__ . '/assets/images/uploads/'),
              'req'    => true),
        array('label' => 'Ekstensi GD (untuk gambar)',
              'detail' => extension_loaded('gd') ? 'Tersedia' : 'Tidak tersedia (opsional)',
              'ok'     => extension_loaded('gd'),
              'req'    => false),
    );
}
$allOk = true;
foreach ($reqs as $r) { if ($r['req'] && !$r['ok']) { $allOk = false; break; } }

// ── Prefill form values ───────────────────────────────────────
$dbV = isset($_SESSION['smk_install']) ? $_SESSION['smk_install'] : array(
    'db_host' => 'localhost', 'db_port' => 3306, 'db_user' => 'root',
    'db_pass' => '', 'db_name' => 'websmk', 'timezone' => 'Asia/Jakarta',
);
$admV = isset($_SESSION['smk_admin']) ? $_SESSION['smk_admin'] : array(
    'name' => 'Super Admin', 'user' => 'admin', 'email' => '',
);
if (!empty($errors) && $step === 2) {
    foreach (array('db_host','db_port','db_user','db_pass','db_name','timezone') as $k)
        if (isset($_POST[$k])) $dbV[$k] = $_POST[$k];
}
if (!empty($errors) && $step === 3) {
    $map = array('admin_name'=>'name','admin_user'=>'user','admin_email'=>'email');
    foreach ($map as $post => $sess)
        if (isset($_POST[$post])) $admV[$sess] = $_POST[$post];
}

function H($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function SEL($v, $c) { return $v === $c ? ' selected' : ''; }
?><!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Installer - SMK Pertamaku</title>
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
body{background:#0f172a;color:#e2e8f0;font-family:'Segoe UI',system-ui,sans-serif;
  min-height:100vh;display:flex;flex-direction:column;align-items:center;padding:32px 16px 48px}
/* Header */
.hdr{text-align:center;margin-bottom:28px}
.logo{width:64px;height:64px;background:linear-gradient(135deg,#3b82f6,#6366f1);
  border-radius:16px;display:inline-flex;align-items:center;justify-content:center;
  font-size:28px;margin-bottom:12px;box-shadow:0 8px 24px rgba(59,130,246,.3)}
.hdr h1{font-size:1.55rem;font-weight:700;color:#f1f5f9}
.hdr p{color:#64748b;margin-top:5px;font-size:.88rem}
/* Progress */
.prog{width:100%;max-width:600px;background:#1e293b;border:1px solid #334155;
  border-radius:12px;padding:18px 24px;margin-bottom:20px}
.steps{display:flex;align-items:center}
.si{display:flex;flex-direction:column;align-items:center;flex:1;position:relative}
.si:not(:last-child)::after{content:'';position:absolute;top:15px;left:50%;
  width:100%;height:2px;background:#334155;z-index:0}
.si.done:not(:last-child)::after{background:#3b82f6}
.sc{width:30px;height:30px;border-radius:50%;display:flex;align-items:center;
  justify-content:center;font-size:.75rem;font-weight:700;z-index:1;
  background:#334155;color:#64748b;border:2px solid #475569}
.si.done .sc{background:#3b82f6;border-color:#3b82f6;color:#fff}
.si.active .sc{background:linear-gradient(135deg,#3b82f6,#6366f1);
  border-color:#6366f1;color:#fff;box-shadow:0 0 0 3px rgba(99,102,241,.2)}
.sl{font-size:.65rem;color:#64748b;margin-top:5px;text-align:center;white-space:nowrap}
.si.active .sl{color:#93c5fd;font-weight:600}
.si.done .sl{color:#60a5fa}
/* Card */
.card{width:100%;max-width:600px;background:#1a2235;border:1px solid #2d3f55;
  border-radius:14px;overflow:hidden}
.ch{padding:22px 28px 16px;border-bottom:1px solid #2d3f55}
.ch h2{font-size:1.05rem;color:#f1f5f9;font-weight:700}
.ch p{color:#64748b;font-size:.83rem;margin-top:4px}
.cb{padding:22px 28px}
/* Alerts */
.alert{border-radius:8px;padding:12px 14px;margin-bottom:16px;
  font-size:.86rem;display:flex;gap:8px;align-items:flex-start}
.alert ul{margin:5px 0 0 16px}
.ae{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.25);color:#fca5a5}
.as{background:rgba(34,197,94,.08);border:1px solid rgba(34,197,94,.2);color:#86efac}
.aw{background:rgba(251,191,36,.07);border:1px solid rgba(251,191,36,.18);color:#fde68a}
/* Requirements */
.rg{display:flex;flex-direction:column;gap:8px}
.ri{display:flex;align-items:flex-start;gap:10px;background:rgba(255,255,255,.03);
  border:1px solid #2d3f55;border-radius:8px;padding:11px 14px}
.ri.ok{border-color:rgba(34,197,94,.2)}.ri.fail{border-color:rgba(239,68,68,.22)}
.ri.warn{border-color:rgba(251,191,36,.18)}
.ric{font-size:1rem;flex-shrink:0}
.rl{font-weight:600;color:#e2e8f0;font-size:.86rem}
.rd{font-size:.78rem;color:#64748b;margin-top:2px}
.rb{margin-left:auto;flex-shrink:0;font-size:.67rem;padding:2px 8px;
  border-radius:12px;font-weight:600;align-self:center}
.rb.req{background:rgba(239,68,68,.12);color:#fca5a5;border:1px solid rgba(239,68,68,.2)}
.rb.opt{background:rgba(251,191,36,.08);color:#fde68a;border:1px solid rgba(251,191,36,.15)}
/* Form */
.fg{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.ff{grid-column:1/-1}
.fg1{display:flex;flex-direction:column;gap:4px}
label{font-size:.8rem;font-weight:600;color:#94a3b8}
label small{font-weight:400;color:#475569;margin-left:4px}
input,select{background:#0f1929;border:1px solid #334155;border-radius:7px;
  padding:10px 12px;color:#e2e8f0;font-size:.87rem;width:100%;
  outline:none;transition:border .18s}
input:focus,select:focus{border-color:#3b82f6;box-shadow:0 0 0 2px rgba(59,130,246,.15)}
input::placeholder{color:#2d3f55}
select option{background:#1e293b}
.iw{position:relative}
.iw input{padding-right:38px}
.tp{position:absolute;right:10px;top:50%;transform:translateY(-50%);
  background:none;border:none;cursor:pointer;color:#64748b;font-size:.9rem;padding:3px}
.tp:hover{color:#93c5fd}
.sep{display:flex;align-items:center;gap:8px;color:#334155;font-size:.72rem;
  font-weight:700;text-transform:uppercase;letter-spacing:.07em;margin:18px 0 12px}
.sep::before,.sep::after{content:'';flex:1;height:1px;background:#2d3f55}
/* Buttons */
.brow{display:flex;gap:10px;margin-top:20px;justify-content:flex-end}
.btn{padding:10px 22px;border-radius:8px;font-size:.87rem;font-weight:700;
  cursor:pointer;border:none;text-decoration:none;
  display:inline-flex;align-items:center;gap:6px;transition:all .18s}
.bp{background:linear-gradient(135deg,#3b82f6,#6366f1);color:#fff}
.bp:hover{transform:translateY(-1px);filter:brightness(1.1)}
.bp:disabled{opacity:.45;cursor:not-allowed;transform:none;filter:none}
.bg{background:#2d3f55;color:#94a3b8;border:1px solid #334155}
.bg:hover{background:#334155;color:#e2e8f0}
/* Strength */
.sb{height:3px;border-radius:2px;margin-top:4px;background:#2d3f55;overflow:hidden}
.sf{height:100%;width:0;transition:width .25s,background .25s}
.st{font-size:.7rem;margin-top:3px;color:#64748b}
/* Install log */
.log{background:#0f1929;border:1px solid #2d3f55;border-radius:8px;
  padding:14px;display:flex;flex-direction:column;gap:7px;margin-bottom:18px}
.li{display:flex;gap:8px;font-size:.84rem;align-items:flex-start}
.lok{color:#86efac}.lfl{color:#fca5a5}
.lm code{background:#1e293b;padding:1px 4px;border-radius:3px;font-size:.78rem}
/* Success */
.sh{text-align:center;padding:14px 0 20px}
.shi{width:68px;height:68px;margin:0 auto 14px;
  background:linear-gradient(135deg,#22c55e,#16a34a);border-radius:50%;
  display:flex;align-items:center;justify-content:center;font-size:30px}
.sh h2{font-size:1.4rem;color:#f1f5f9;margin-bottom:6px}
.sh p{color:#64748b;font-size:.88rem}
.creds{background:rgba(59,130,246,.07);border:1px solid rgba(59,130,246,.18);
  border-radius:10px;padding:16px 20px;margin:16px 0}
.creds h4{color:#93c5fd;font-size:.78rem;text-transform:uppercase;
  letter-spacing:.07em;margin-bottom:10px}
.cr{display:flex;justify-content:space-between;align-items:center;margin-bottom:7px}
.ck{font-size:.82rem;color:#64748b}
.cv{font-size:.85rem;color:#e2e8f0;font-weight:700;
  background:#0f1929;padding:2px 8px;border-radius:5px;font-family:monospace}
.lrow{display:flex;gap:10px;justify-content:center;flex-wrap:wrap;margin-top:10px}
.warn{background:rgba(239,68,68,.07);border:1px solid rgba(239,68,68,.18);
  border-radius:8px;padding:12px 16px;color:#fca5a5;font-size:.83rem;
  line-height:1.6;margin-top:16px}
.warn strong{display:block;margin-bottom:4px}
.warn code{background:rgba(0,0,0,.3);padding:1px 5px;border-radius:3px}
/* Review */
.rv{display:flex;flex-direction:column;gap:6px;margin-bottom:16px}
.rr{display:flex;gap:12px;background:#0f1929;border:1px solid #2d3f55;
  border-radius:7px;padding:9px 12px;font-size:.83rem}
.rk{color:#64748b;min-width:130px;flex-shrink:0}
.rvv{color:#e2e8f0;font-weight:500;word-break:break-all}
/* Responsive */
@media(max-width:520px){
  .fg{grid-template-columns:1fr}.ff{grid-column:1}
  .cb,.ch{padding:18px 18px}.brow{flex-direction:column-reverse}
  .btn{justify-content:center}
  .rr{flex-direction:column;gap:2px}.rk{min-width:auto}
}
</style>
</head>
<body>

<div class="hdr">
  <div class="logo">&#x1F3EB;</div>
  <h1>Installer SMK Pertamaku</h1>
  <p>Setup otomatis — database, akun admin, konfigurasi</p>
</div>

<div class="prog"><div class="steps">
<?php
$labels = array('Persyaratan','Database','Admin','Selesai');
for ($i = 1; $i <= 4; $i++) {
    $cls = ($i < $step) ? 'done' : (($i === $step) ? 'active' : '');
    echo '<div class="si ' . $cls . '">'
       . '<div class="sc">' . ($i < $step ? '&#x2713;' : $i) . '</div>'
       . '<div class="sl">' . $labels[$i-1] . '</div></div>';
}
?>
</div></div>

<div class="card">

<?php if ($step === 1): ?>
<div class="ch"><h2>&#x1F50D; Cek Persyaratan Sistem</h2>
<p>Pastikan server memenuhi persyaratan sebelum install.</p></div>
<div class="cb">
  <?php if ($allOk): ?>
  <div class="alert as">&#x2705; Semua persyaratan wajib terpenuhi. Siap lanjut!</div>
  <?php else: ?>
  <div class="alert ae">&#x26A0; Ada persyaratan wajib yang belum terpenuhi.</div>
  <?php endif; ?>
  <div class="rg">
  <?php foreach ($reqs as $r):
    $cls = $r['ok'] ? 'ok' : ($r['req'] ? 'fail' : 'warn');
    $ico = $r['ok'] ? '&#x2705;' : ($r['req'] ? '&#x274C;' : '&#x26A0;');
  ?>
  <div class="ri <?php echo $cls; ?>">
    <div class="ric"><?php echo $ico; ?></div>
    <div>
      <div class="rl"><?php echo H($r['label']); ?></div>
      <div class="rd"><?php echo H($r['detail']); ?></div>
    </div>
    <div class="rb <?php echo $r['req'] ? 'req' : 'opt'; ?>"><?php echo $r['req'] ? 'Wajib' : 'Opsional'; ?></div>
  </div>
  <?php endforeach; ?>
  </div>
  <div class="brow">
    <a href="?step=1" class="btn bg">&#x1F504; Refresh</a>
    <?php if ($allOk): ?>
      <a href="?step=2" class="btn bp">Lanjut &#x27A1;</a>
    <?php else: ?>
      <button class="btn bp" disabled>Lanjut &#x27A1;</button>
    <?php endif; ?>
  </div>
</div>

<?php elseif ($step === 2): ?>
<div class="ch"><h2>&#x1F5C4; Konfigurasi Database</h2>
<p>Isi koneksi MySQL. Database akan dibuat otomatis jika belum ada.</p></div>
<div class="cb">
  <?php if (!empty($errors)): ?>
  <div class="alert ae"><ul><?php foreach ($errors as $e): ?><li><?php echo H($e); ?></li><?php endforeach; ?></ul></div>
  <?php endif; ?>
  <form method="POST" action="?step=2">
    <div class="sep">Koneksi MySQL</div>
    <div class="fg">
      <div class="fg1">
        <label>Host Database</label>
        <input type="text" name="db_host" value="<?php echo H($dbV['db_host']); ?>" placeholder="localhost" required>
      </div>
      <div class="fg1">
        <label>Port <small>default 3306</small></label>
        <input type="number" name="db_port" value="<?php echo H((string)$dbV['db_port']); ?>" placeholder="3306" min="1" max="65535">
      </div>
      <div class="fg1">
        <label>Username MySQL</label>
        <input type="text" name="db_user" value="<?php echo H($dbV['db_user']); ?>" placeholder="root" required>
      </div>
      <div class="fg1">
        <label>Password MySQL <small>kosong = default XAMPP</small></label>
        <div class="iw">
          <input type="password" id="dbp" name="db_pass" value="<?php echo H($dbV['db_pass']); ?>" placeholder="(kosong untuk XAMPP)">
          <button type="button" class="tp" onclick="tp('dbp',this)">&#x1F441;</button>
        </div>
      </div>
      <div class="fg1 ff">
        <label>Nama Database <small>dibuat otomatis jika belum ada</small></label>
        <input type="text" name="db_name" value="<?php echo H($dbV['db_name']); ?>" placeholder="websmk" required>
      </div>
    </div>
    <div class="sep">Pengaturan Lain</div>
    <div class="fg">
      <div class="fg1">
        <label>Zona Waktu</label>
        <select name="timezone">
          <option value="Asia/Jakarta"<?php echo SEL($dbV['timezone'],'Asia/Jakarta'); ?>>WIB — Asia/Jakarta (UTC+7)</option>
          <option value="Asia/Makassar"<?php echo SEL($dbV['timezone'],'Asia/Makassar'); ?>>WITA — Asia/Makassar (UTC+8)</option>
          <option value="Asia/Jayapura"<?php echo SEL($dbV['timezone'],'Asia/Jayapura'); ?>>WIT — Asia/Jayapura (UTC+9)</option>
          <option value="UTC"<?php echo SEL($dbV['timezone'],'UTC'); ?>>UTC</option>
        </select>
      </div>
    </div>
    <div class="brow">
      <a href="?step=1" class="btn bg">&#x2190; Kembali</a>
      <button type="submit" class="btn bp">Uji &amp; Lanjut &#x27A1;</button>
    </div>
  </form>
</div>

<?php elseif ($step === 3): ?>
<div class="ch"><h2>&#x1F464; Akun Administrator</h2>
<p>Buat akun untuk login ke panel admin. Simpan dengan aman!</p></div>
<div class="cb">
  <?php if (!empty($errors)): ?>
  <div class="alert ae"><ul><?php foreach ($errors as $e): ?><li><?php echo H($e); ?></li><?php endforeach; ?></ul></div>
  <?php endif; ?>
  <form method="POST" action="?step=3">
    <div class="fg">
      <div class="fg1 ff">
        <label>Nama Lengkap</label>
        <input type="text" name="admin_name" value="<?php echo H($admV['name']); ?>" placeholder="Super Admin" required>
      </div>
      <div class="fg1">
        <label>Username <small>huruf, angka, _</small></label>
        <input type="text" name="admin_user" value="<?php echo H($admV['user']); ?>"
          placeholder="admin" pattern="[a-zA-Z0-9_]+" minlength="3" required>
      </div>
      <div class="fg1">
        <label>Email</label>
        <input type="email" name="admin_email" value="<?php echo H($admV['email']); ?>"
          placeholder="admin@sekolah.sch.id" required>
      </div>
      <div class="fg1">
        <label>Password <small>min. 6 karakter</small></label>
        <div class="iw">
          <input type="password" id="ap1" name="admin_pass"
            placeholder="&#x2022;&#x2022;&#x2022;&#x2022;&#x2022;&#x2022;&#x2022;&#x2022;"
            minlength="6" required oninput="chkS(this.value)">
          <button type="button" class="tp" onclick="tp('ap1',this)">&#x1F441;</button>
        </div>
        <div class="sb"><div id="sf" class="sf"></div></div>
        <div id="st" class="st"></div>
      </div>
      <div class="fg1">
        <label>Konfirmasi Password</label>
        <div class="iw">
          <input type="password" id="ap2" name="admin_pass2"
            placeholder="&#x2022;&#x2022;&#x2022;&#x2022;&#x2022;&#x2022;&#x2022;&#x2022;"
            required oninput="chkM()">
          <button type="button" class="tp" onclick="tp('ap2',this)">&#x1F441;</button>
        </div>
        <div id="pm" style="font-size:.7rem;margin-top:3px">&nbsp;</div>
      </div>
    </div>
    <div class="alert aw" style="margin-top:14px">
      &#x1F4A1; Simpan username dan password di tempat aman.
    </div>
    <div class="brow">
      <a href="?step=2" class="btn bg">&#x2190; Kembali</a>
      <button type="submit" class="btn bp">Lanjut &#x27A1;</button>
    </div>
  </form>
</div>

<?php elseif ($step === 4): ?>

<?php if ($done && !empty($log)): ?>
<!-- SUCCESS -->
<div class="ch"><h2>&#x1F389; Instalasi Berhasil!</h2><p>Semua komponen berhasil dikonfigurasi.</p></div>
<div class="cb">
  <div class="log">
    <?php foreach ($log as $l): ?>
    <div class="li"><span class="<?php echo $l['ok'] ? 'lok' : 'lfl'; ?>"><?php echo $l['ok'] ? '&#x2705;' : '&#x26A0;'; ?></span>
    <span><?php echo $l['msg']; ?></span></div>
    <?php endforeach; ?>
  </div>
  <div class="sh">
    <div class="shi">&#x1F38A;</div>
    <h2>Website Siap Digunakan!</h2>
    <p>Kunjungi website atau masuk ke panel admin untuk mulai mengelola konten.</p>
  </div>
  <?php
  $fu = rtrim($BASE_URL, '/');
  $au = isset($_SESSION['smk_admin']['user']) ? $_SESSION['smk_admin']['user'] : 'admin';
  $an = isset($_SESSION['smk_admin']['name']) ? $_SESSION['smk_admin']['name'] : '';
  $ae = isset($_SESSION['smk_admin']['email']) ? $_SESSION['smk_admin']['email'] : '';
  ?>
  <div class="creds">
    <h4>&#x1F4CB; Kredensial Login Admin</h4>
    <div class="cr"><span class="ck">URL Website</span><span class="cv"><?php echo H($fu); ?>/</span></div>
    <div class="cr"><span class="ck">URL Admin</span><span class="cv"><?php echo H($fu); ?>/admin/login</span></div>
    <div class="cr"><span class="ck">Username</span><span class="cv"><?php echo H($au); ?></span></div>
    <div class="cr"><span class="ck">Password</span><span class="cv">&#x2022;&#x2022;&#x2022;&#x2022;&#x2022;&#x2022; (yang kamu masukkan)</span></div>
  </div>
  <div class="lrow">
    <a href="<?php echo H($fu); ?>/" class="btn bg" target="_blank">&#x1F310; Buka Website</a>
    <a href="<?php echo H($fu); ?>/admin/login" class="btn bp" target="_blank">&#x2699; Masuk Admin</a>
  </div>
  <div class="warn">
    <strong>&#x26A0; Penting: Hapus File Installer!</strong>
    Demi keamanan, segera hapus file ini setelah instalasi selesai:<br>
    <code><?php echo H(__FILE__); ?></code>
  </div>
  <?php unset($_SESSION['smk_install'], $_SESSION['smk_admin']); ?>
</div>

<?php elseif (!empty($log) && !$done): ?>
<!-- FAILED -->
<div class="ch"><h2>&#x274C; Instalasi Gagal</h2><p>Periksa log berikut dan coba lagi.</p></div>
<div class="cb">
  <div class="log">
    <?php foreach ($log as $l): ?>
    <div class="li"><span class="<?php echo $l['ok'] ? 'lok' : 'lfl'; ?>"><?php echo $l['ok'] ? '&#x2705;' : '&#x274C;'; ?></span>
    <span><?php echo $l['msg']; ?></span></div>
    <?php endforeach; ?>
  </div>
  <div class="brow">
    <a href="?step=2" class="btn bg">&#x2190; Ubah Konfigurasi</a>
    <form method="POST" action="?step=4" style="margin:0">
      <button type="submit" name="action" value="install" class="btn bp">&#x1F504; Coba Lagi</button>
    </form>
  </div>
</div>

<?php else: ?>
<!-- REVIEW & CONFIRM -->
<div class="ch"><h2>&#x1F680; Konfirmasi Instalasi</h2><p>Periksa data, lalu klik Mulai Instalasi.</p></div>
<div class="cb">
  <?php if (!empty($errors)): ?>
  <div class="alert ae">
    <?php echo H($errors[0]); ?>
    <br><a href="?step=1" style="color:#93c5fd">&#x2190; Mulai ulang</a>
  </div>
  <?php elseif (!isset($_SESSION['smk_install']) || !isset($_SESSION['smk_admin'])): ?>
  <div class="alert ae">Session hilang. <a href="?step=1" style="color:#93c5fd">Mulai ulang &#x2190;</a></div>
  <?php else:
    $db  = $_SESSION['smk_install'];
    $adm = $_SESSION['smk_admin'];
  ?>
  <div class="sep">Database</div>
  <div class="rv">
    <div class="rr"><span class="rk">Host : Port</span><span class="rvv"><?php echo H($db['db_host']); ?> : <?php echo H((string)$db['db_port']); ?></span></div>
    <div class="rr"><span class="rk">Username</span><span class="rvv"><?php echo H($db['db_user']); ?></span></div>
    <div class="rr"><span class="rk">Nama Database</span><span class="rvv"><?php echo H($db['db_name']); ?></span></div>
    <div class="rr"><span class="rk">Timezone</span><span class="rvv"><?php echo H($db['timezone']); ?></span></div>
  </div>
  <div class="sep">Admin</div>
  <div class="rv">
    <div class="rr"><span class="rk">Nama</span><span class="rvv"><?php echo H($adm['name']); ?></span></div>
    <div class="rr"><span class="rk">Username</span><span class="rvv"><?php echo H($adm['user']); ?></span></div>
    <div class="rr"><span class="rk">Email</span><span class="rvv"><?php echo H($adm['email']); ?></span></div>
  </div>
  <form method="POST" action="?step=4">
    <div class="brow">
      <a href="?step=3" class="btn bg">&#x2190; Kembali</a>
      <button type="submit" name="action" value="install" id="ibtn"
        class="btn bp" onclick="return startInstall(this)">
        &#x1F680; Mulai Instalasi
      </button>
    </div>
  </form>
  <?php endif; ?>
</div>
<?php endif; ?>

<?php endif; ?>
</div><!-- .card -->

<div style="margin-top:18px;font-size:.72rem;color:#334155;text-align:center">
  SMK Installer &bull; PHP <?php echo PHP_VERSION; ?> &bull;
  <span style="color:#1d3a6e">Hapus install.php setelah instalasi selesai</span>
</div>

<script>
function tp(id, btn) {
    var i = document.getElementById(id);
    if (!i) return;
    i.type = i.type === 'password' ? 'text' : 'password';
    btn.textContent = i.type === 'password' ? '\uD83D\uDC41' : '\uD83D\uDE48';
}
function chkS(v) {
    var f = document.getElementById('sf'), t = document.getElementById('st');
    if (!f) return;
    var s = 0;
    if (v.length >= 6) s++;
    if (v.length >= 10) s++;
    if (/[A-Z]/.test(v)) s++;
    if (/[0-9]/.test(v)) s++;
    if (/[^A-Za-z0-9]/.test(v)) s++;
    var c = ['#ef4444','#ef4444','#f97316','#eab308','#22c55e','#16a34a'];
    var l = ['','Sangat Lemah','Lemah','Sedang','Kuat','Sangat Kuat'];
    f.style.width = (s * 20) + '%';
    f.style.background = c[s] || '#ef4444';
    if (t) t.textContent = v.length ? (l[s] || '') : '';
}
function chkM() {
    var p = document.getElementById('ap1'), p2 = document.getElementById('ap2');
    var m = document.getElementById('pm');
    if (!p || !p2 || !m) return;
    if (!p2.value) { m.textContent = ''; return; }
    m.style.color = p.value === p2.value ? '#86efac' : '#fca5a5';
    m.textContent  = p.value === p2.value ? '\u2705 Cocok' : '\u274C Tidak cocok';
}
function startInstall(btn) {
    btn.disabled = true;
    btn.innerHTML = '\u23F3 Menginstall... harap tunggu';
    return true; // submit form normally
}
</script>
</body>
</html>

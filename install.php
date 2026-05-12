<?php
/**
 * ============================================================
 * install.php — Wizard Instalasi Website SMK Pertamaku
 * Standalone installer — runs BEFORE app is configured.
 * ============================================================
 */
session_start();

// ─── LOCK CHECK ───────────────────────────────────────────────
if (file_exists(__DIR__ . '/install.lock')) {
    $lockedPage = true;
}
if (!isset($lockedPage)) {
    if (file_exists(__DIR__ . '/config/env.php')) {
        @include_once __DIR__ . '/config/env.php';
        if (defined('INSTALLER_LOCKED') && INSTALLER_LOCKED === true) {
            $lockedPage = true;
        }
    }
}
if (isset($lockedPage)) {
    $appUrl = defined('APP_URL') ? APP_URL : '';
    ?><!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Installer Terkunci — SMK Pertamaku</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{min-height:100vh;background:linear-gradient(135deg,#0f172a 0%,#1e293b 100%);display:flex;align-items:center;justify-content:center;font-family:'Segoe UI',system-ui,sans-serif;color:#e2e8f0}
.card{background:rgba(255,255,255,.07);backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,.12);border-radius:20px;padding:48px 40px;max-width:520px;width:90%;text-align:center}
.icon{font-size:56px;margin-bottom:20px}
h1{font-size:1.6rem;color:#f1f5f9;margin-bottom:12px}
p{color:#94a3b8;line-height:1.7;margin-bottom:16px}
.badge{display:inline-block;background:rgba(239,68,68,.15);color:#fca5a5;border:1px solid rgba(239,68,68,.3);border-radius:50px;padding:6px 18px;font-size:.85rem;margin-bottom:28px}
.btn{display:inline-block;background:#3b82f6;color:#fff;text-decoration:none;padding:12px 28px;border-radius:10px;font-weight:600;margin:6px;transition:background .2s}
.btn:hover{background:#2563eb}
.btn-outline{background:transparent;border:2px solid #3b82f6;color:#3b82f6}
.btn-outline:hover{background:#3b82f6;color:#fff}
.tip{background:rgba(251,191,36,.08);border:1px solid rgba(251,191,36,.25);border-radius:10px;padding:14px 18px;font-size:.85rem;color:#fde68a;margin-top:24px;text-align:left}
.tip code{background:rgba(0,0,0,.3);padding:2px 6px;border-radius:4px;font-family:monospace}
</style>
</head>
<body>
<div class="card">
  <div class="icon">🔒</div>
  <div class="badge">Installer Terkunci</div>
  <h1>Instalasi Sudah Selesai</h1>
  <p>File installer telah dikunci untuk keamanan. Website sudah berhasil diinstal dan siap digunakan.</p>
  <?php if ($appUrl): ?>
  <a href="<?= htmlspecialchars($appUrl) ?>/" class="btn">🌐 Buka Website</a>
  <a href="<?= htmlspecialchars($appUrl) ?>/admin/login" class="btn btn-outline">⚙️ Panel Admin</a>
  <?php endif; ?>
  <div class="tip">
    <strong>⚠️ Ingin menjalankan ulang installer?</strong><br>
    Hapus file kunci berikut melalui FTP / File Manager, lalu refresh halaman ini:<br><br>
    <code><?= htmlspecialchars(__DIR__) ?>/install.lock</code>
  </div>
</div>
</body>
</html>
<?php
    exit;
}

// ─── STEP ROUTING ─────────────────────────────────────────────
$step = max(1, min(4, (int)($_GET['step'] ?? 1)));
$errors   = [];
$messages = [];

// ─── POST HANDLER: STEP 1 ─────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 1) {
    // Step 1 has no POST — button navigates via GET
}

// ─── POST HANDLER: STEP 2 ─────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 2) {
    $db_host    = trim($_POST['db_host']     ?? 'localhost');
    $db_port    = (int)($_POST['db_port']    ?? 3306);
    $db_user    = trim($_POST['db_user']     ?? 'root');
    $db_pass    = $_POST['db_pass']          ?? '';
    $db_name    = trim($_POST['db_name']     ?? 'websmk');
    $app_url    = rtrim(trim($_POST['app_url'] ?? ''), '/');
    $app_base   = trim($_POST['app_base']    ?? '');
    $timezone   = trim($_POST['timezone']    ?? 'Asia/Jakarta');

    $allowed_tz = ['Asia/Jakarta','Asia/Makassar','Asia/Jayapura','UTC'];
    if (!in_array($timezone, $allowed_tz)) $timezone = 'Asia/Jakarta';

    if (empty($db_host))   $errors[] = 'Database host tidak boleh kosong.';
    if (empty($db_name))   $errors[] = 'Nama database tidak boleh kosong.';
    if (empty($app_url))   $errors[] = 'URL Aplikasi tidak boleh kosong.';

    if (empty($errors)) {
        // Test connection (no DB selection)
        $conn = @mysqli_connect($db_host . ':' . $db_port, $db_user, $db_pass);
        if (!$conn) {
            $errors[] = 'Koneksi database gagal: ' . mysqli_connect_error() . ' (errno: ' . mysqli_connect_errno() . ')';
        } else {
            mysqli_close($conn);
            $_SESSION['install_db'] = compact('db_host','db_port','db_user','db_pass','db_name','app_url','app_base','timezone');
            header('Location: install.php?step=3');
            exit;
        }
    }
}

// ─── POST HANDLER: STEP 3 ─────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 3) {
    $admin_name  = trim($_POST['admin_name']  ?? 'Super Admin');
    $admin_user  = trim($_POST['admin_user']  ?? 'admin');
    $admin_email = trim($_POST['admin_email'] ?? '');
    $admin_pass  = $_POST['admin_pass']        ?? '';
    $admin_pass2 = $_POST['admin_pass2']       ?? '';

    if (empty($admin_name))                              $errors[] = 'Nama lengkap admin wajib diisi.';
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $admin_user))  $errors[] = 'Username hanya boleh huruf, angka, dan underscore.';
    if (!filter_var($admin_email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Format email tidak valid.';
    if (strlen($admin_pass) < 6)                         $errors[] = 'Password minimal 6 karakter.';
    if ($admin_pass !== $admin_pass2)                    $errors[] = 'Konfirmasi password tidak cocok.';

    if (empty($errors)) {
        $_SESSION['install_admin'] = compact('admin_name','admin_user','admin_email','admin_pass');
        header('Location: install.php?step=4');
        exit;
    }
}

// ─── POST HANDLER: STEP 4 (ACTUAL INSTALL) ────────────────────
$installResults = [];
$installDone    = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 4 && ($_POST['action'] ?? '') === 'install') {
    $db   = $_SESSION['install_db']    ?? [];
    $adm  = $_SESSION['install_admin'] ?? [];

    if (empty($db) || empty($adm)) {
        $errors[] = 'Data sesi tidak lengkap. Silakan mulai dari langkah 1.';
    } else {
        // 1. Connect without DB
        $conn = @mysqli_connect($db['db_host'] . ':' . $db['db_port'], $db['db_user'], $db['db_pass']);
        if (!$conn) {
            $installResults[] = ['ok'=>false,'msg'=>'Koneksi database gagal: '.mysqli_connect_error()];
        } else {
            $installResults[] = ['ok'=>true,'msg'=>'Koneksi ke database server berhasil'];

            // 2. Create database
            $dbSafe = mysqli_real_escape_string($conn, $db['db_name']);
            if (mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS `{$dbSafe}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
                $installResults[] = ['ok'=>true,'msg'=>"Database <code>{$dbSafe}</code> berhasil dibuat / sudah ada"];
            } else {
                $installResults[] = ['ok'=>false,'msg'=>'Gagal membuat database: '.mysqli_error($conn)];
            }

            // 3. Select database
            if (mysqli_select_db($conn, $db['db_name'])) {
                $installResults[] = ['ok'=>true,'msg'=>"Database <code>{$db['db_name']}</code> berhasil dipilih"];
            } else {
                $installResults[] = ['ok'=>false,'msg'=>'Gagal memilih database: '.mysqli_error($conn)];
            }

            // 4. Run schema.sql
            $schemaFile = __DIR__ . '/database/schema.sql';
            if (!file_exists($schemaFile)) {
                $installResults[] = ['ok'=>false,'msg'=>'File <code>database/schema.sql</code> tidak ditemukan'];
            } else {
                $sql = file_get_contents($schemaFile);
                // Remove comments and split
                $sql = preg_replace('/--[^\n]*\n/', "\n", $sql);
                $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
                $statements = array_filter(array_map('trim', explode(';', $sql)));
                $sqlOk = true; $sqlFail = 0; $sqlRun = 0;
                foreach ($statements as $stmt) {
                    if (empty($stmt)) continue;
                    if (!mysqli_query($conn, $stmt)) {
                        $sqlFail++;
                        // Ignore "already exists" errors (code 1050, 1061, 1062)
                        $errno = mysqli_errno($conn);
                        if (!in_array($errno, [1050,1061,1062,1007])) {
                            $sqlOk = false;
                        }
                    }
                    $sqlRun++;
                }
                if ($sqlOk) {
                    $installResults[] = ['ok'=>true,'msg'=>"Skema database berhasil dijalankan ({$sqlRun} statements, {$sqlFail} ignored)"];
                } else {
                    $installResults[] = ['ok'=>false,'msg'=>"Gagal menjalankan sebagian skema database ({$sqlFail} error): ".mysqli_error($conn)];
                }
            }

            // 5. Insert/update admin
            $hashedPass = password_hash($adm['admin_pass'], PASSWORD_BCRYPT);
            $name   = mysqli_real_escape_string($conn, $adm['admin_name']);
            $uname  = mysqli_real_escape_string($conn, $adm['admin_user']);
            $email  = mysqli_real_escape_string($conn, $adm['admin_email']);
            $hpass  = mysqli_real_escape_string($conn, $hashedPass);
            $sqlAdm = "INSERT INTO `admins` (`name`,`username`,`email`,`password`,`role`) VALUES ('{$name}','{$uname}','{$email}','{$hpass}','superadmin') ON DUPLICATE KEY UPDATE `name`=VALUES(`name`),`password`=VALUES(`password`),`email`=VALUES(`email`)";
            if (mysqli_query($conn, $sqlAdm)) {
                $installResults[] = ['ok'=>true,'msg'=>"Akun administrator <strong>{$uname}</strong> berhasil disimpan"];
            } else {
                $installResults[] = ['ok'=>false,'msg'=>'Gagal menyimpan admin: '.mysqli_error($conn)];
            }
            mysqli_close($conn);

            // 6. Write config/env.php
            $appKey = bin2hex(random_bytes(24));
            $envContent = "<?php\n"
                . "define('DB_HOST',   '{$db['db_host']}');\n"
                . "define('DB_USER',   '{$db['db_user']}');\n"
                . "define('DB_PASS',   '{$db['db_pass']}');\n"
                . "define('DB_NAME',   '{$db['db_name']}');\n"
                . "define('DB_PORT',    {$db['db_port']});\n"
                . "define('DB_PREFIX', '');\n"
                . "define('APP_URL',   '{$db['app_url']}');\n"
                . "define('APP_BASE',  '{$db['app_base']}');\n"
                . "define('APP_KEY',   '{$appKey}');\n"
                . "define('APP_TIMEZONE', '{$db['timezone']}');\n"
                . "date_default_timezone_set(APP_TIMEZONE);\n"
                . "define('APP_ENV',   'production');\n"
                . "define('INSTALLER_LOCKED', false);\n";

            $envPath = __DIR__ . '/config/env.php';
            if (file_put_contents($envPath, $envContent) !== false) {
                $installResults[] = ['ok'=>true,'msg'=>'File <code>config/env.php</code> berhasil ditulis'];
            } else {
                $installResults[] = ['ok'=>false,'msg'=>'Gagal menulis <code>config/env.php</code> — pastikan folder config/ writable'];
            }

            // 7. Create install.lock
            if (file_put_contents(__DIR__ . '/install.lock', date('Y-m-d H:i:s') . "\nInstalled successfully.\n") !== false) {
                $installResults[] = ['ok'=>true,'msg'=>'File <code>install.lock</code> berhasil dibuat'];
            } else {
                $installResults[] = ['ok'=>false,'msg'=>'Gagal membuat <code>install.lock</code> — instalasi tetap berhasil tetapi lock tidak terbuat'];
            }

            $installDone = !in_array(false, array_column($installResults, 'ok'), true);
            // Even if lock fails, consider done if all critical steps passed
            $criticalFail = false;
            foreach ($installResults as $r) {
                if (!$r['ok'] && strpos($r['msg'],'lock') === false) $criticalFail = true;
            }
            $installDone = !$criticalFail;
        }
    }
}

// ─── REQUIREMENTS CHECK (Step 1) ──────────────────────────────
function checkRequirements(): array {
    $checks = [];

    // PHP version
    $phpVer = PHP_VERSION;
    $phpOk  = version_compare($phpVer, '7.4.0', '>=');
    $checks[] = ['label'=>'PHP >= 7.4', 'detail'=>"Versi saat ini: <strong>PHP {$phpVer}</strong>", 'ok'=>$phpOk, 'required'=>true];

    // MySQLi
    $mysqliOk = extension_loaded('mysqli');
    $checks[] = ['label'=>'Ekstensi MySQLi', 'detail'=>$mysqliOk?'Tersedia':'Tidak tersedia — aktifkan di php.ini', 'ok'=>$mysqliOk, 'required'=>true];

    // config/ writable
    $configDir  = __DIR__ . '/config/';
    $configOk   = is_dir($configDir) && is_writable($configDir);
    $checks[] = ['label'=>'Folder config/ dapat ditulis', 'detail'=>$configOk ? htmlspecialchars($configDir) : 'Tidak writable — jalankan: <code>chmod 775 config/</code>', 'ok'=>$configOk, 'required'=>true];

    // uploads/ writable
    $uploadDir = __DIR__ . '/assets/images/uploads/';
    $uploadOk  = is_dir($uploadDir) && is_writable($uploadDir);
    $checks[] = ['label'=>'Folder assets/images/uploads/ dapat ditulis', 'detail'=>$uploadOk ? htmlspecialchars($uploadDir) : 'Tidak writable — jalankan: <code>chmod 775 assets/images/uploads/</code>', 'ok'=>$uploadOk, 'required'=>true];

    // GD
    $gdOk = extension_loaded('gd');
    $checks[] = ['label'=>'Ekstensi GD (pemrosesan gambar)', 'detail'=>$gdOk?'Tersedia':'Tidak tersedia — fitur upload gambar mungkin terbatas', 'ok'=>$gdOk, 'required'=>false];

    // Sessions
    $sessionOk = function_exists('session_start');
    $checks[] = ['label'=>'Dukungan PHP Session', 'detail'=>$sessionOk?'Tersedia':'Session tidak tersedia', 'ok'=>$sessionOk, 'required'=>false];

    return $checks;
}

$reqChecks = ($step === 1) ? checkRequirements() : [];
$allRequiredOk = empty($reqChecks) ? true : !in_array(false, array_map(
    fn($c) => !$c['required'] || $c['ok'], $reqChecks
), true);

// ─── PREFILL VALUES ───────────────────────────────────────────
$dbVals = $_SESSION['install_db'] ?? [
    'db_host'=>'localhost','db_port'=>3306,'db_user'=>'root',
    'db_pass'=>'','db_name'=>'websmk','app_url'=>'http://localhost/webpertamaku',
    'app_base'=>'/webpertamaku','timezone'=>'Asia/Jakarta'
];
$admVals = $_SESSION['install_admin'] ?? [
    'admin_name'=>'Super Admin','admin_user'=>'admin','admin_email'=>''
];

// Repopulate from POST on error
if (!empty($errors) && $step === 2) {
    $dbVals = array_merge($dbVals, [
        'db_host'  => $_POST['db_host']  ?? $dbVals['db_host'],
        'db_port'  => $_POST['db_port']  ?? $dbVals['db_port'],
        'db_user'  => $_POST['db_user']  ?? $dbVals['db_user'],
        'db_pass'  => $_POST['db_pass']  ?? $dbVals['db_pass'],
        'db_name'  => $_POST['db_name']  ?? $dbVals['db_name'],
        'app_url'  => $_POST['app_url']  ?? $dbVals['app_url'],
        'app_base' => $_POST['app_base'] ?? $dbVals['app_base'],
        'timezone' => $_POST['timezone'] ?? $dbVals['timezone'],
    ]);
}
if (!empty($errors) && $step === 3) {
    $admVals = array_merge($admVals, [
        'admin_name'  => $_POST['admin_name']  ?? $admVals['admin_name'],
        'admin_user'  => $_POST['admin_user']  ?? $admVals['admin_user'],
        'admin_email' => $_POST['admin_email'] ?? $admVals['admin_email'],
    ]);
}

function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8'); }
function sel(string $val, string $cmp): string { return $val === $cmp ? ' selected' : ''; }
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Installer — SMK Pertamaku</title>
<style>
/* ── RESET & BASE ─────────────────────────────────────────── */
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
html{scroll-behavior:smooth}
body{
  min-height:100vh;
  background:linear-gradient(135deg,#0f172a 0%,#1e293b 60%,#0f172a 100%);
  font-family:'Segoe UI',system-ui,-apple-system,sans-serif;
  color:#e2e8f0;
  display:flex;flex-direction:column;align-items:center;
  padding:40px 16px 60px;
}

/* ── HEADER ───────────────────────────────────────────────── */
.installer-header{text-align:center;margin-bottom:40px}
.installer-header .logo-icon{
  width:72px;height:72px;
  background:linear-gradient(135deg,#3b82f6,#6366f1);
  border-radius:20px;display:inline-flex;align-items:center;justify-content:center;
  font-size:32px;margin-bottom:16px;
  box-shadow:0 8px 32px rgba(59,130,246,.35);
}
.installer-header h1{font-size:1.75rem;font-weight:700;color:#f1f5f9;letter-spacing:-.5px}
.installer-header p{color:#64748b;font-size:.95rem;margin-top:6px}

/* ── PROGRESS BAR ─────────────────────────────────────────── */
.progress-wrap{
  width:100%;max-width:720px;
  background:rgba(255,255,255,.05);
  border:1px solid rgba(255,255,255,.09);
  border-radius:16px;padding:24px 32px;
  margin-bottom:32px;
}
.steps{display:flex;align-items:center;gap:0}
.step-item{
  display:flex;flex-direction:column;align-items:center;
  flex:1;position:relative;cursor:default;
}
.step-item:not(:last-child)::after{
  content:'';position:absolute;top:18px;left:50%;
  width:100%;height:2px;
  background:rgba(255,255,255,.1);z-index:0;
}
.step-item.done:not(:last-child)::after{background:#3b82f6}
.step-circle{
  width:36px;height:36px;border-radius:50%;
  display:flex;align-items:center;justify-content:center;
  font-size:.8rem;font-weight:700;z-index:1;
  border:2px solid rgba(255,255,255,.15);
  background:#1e293b;color:#64748b;
  transition:all .3s;
}
.step-item.done .step-circle{background:#3b82f6;border-color:#3b82f6;color:#fff}
.step-item.active .step-circle{
  background:linear-gradient(135deg,#3b82f6,#6366f1);
  border-color:#6366f1;color:#fff;
  box-shadow:0 0 0 4px rgba(99,102,241,.25);
}
.step-label{
  font-size:.72rem;color:#64748b;margin-top:8px;text-align:center;
  font-weight:500;white-space:nowrap;
}
.step-item.active .step-label{color:#93c5fd;font-weight:600}
.step-item.done .step-label{color:#60a5fa}

/* ── CARD ─────────────────────────────────────────────────── */
.card{
  width:100%;max-width:720px;
  background:rgba(255,255,255,.06);
  backdrop-filter:blur(24px);
  -webkit-backdrop-filter:blur(24px);
  border:1px solid rgba(255,255,255,.11);
  border-radius:20px;
  overflow:hidden;
}
.card-header{
  padding:28px 36px 20px;
  border-bottom:1px solid rgba(255,255,255,.07);
}
.card-header h2{font-size:1.25rem;color:#f1f5f9;display:flex;align-items:center;gap:10px}
.card-header p{color:#64748b;font-size:.9rem;margin-top:6px}
.card-body{padding:28px 36px}

/* ── ALERTS ───────────────────────────────────────────────── */
.alert{
  border-radius:12px;padding:14px 18px;margin-bottom:20px;
  display:flex;gap:12px;align-items:flex-start;font-size:.9rem;
}
.alert-error{background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.3);color:#fca5a5}
.alert-success{background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.25);color:#86efac}
.alert-warning{background:rgba(251,191,36,.08);border:1px solid rgba(251,191,36,.2);color:#fde68a}
.alert-icon{font-size:1.1rem;flex-shrink:0;margin-top:1px}
.alert ul{margin:6px 0 0 16px}
.alert li{margin:3px 0}

/* ── REQUIREMENT CHECKS ───────────────────────────────────── */
.req-grid{display:flex;flex-direction:column;gap:10px}
.req-item{
  display:flex;align-items:flex-start;gap:14px;
  background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.07);
  border-radius:12px;padding:14px 18px;
}
.req-item.ok{border-color:rgba(34,197,94,.2)}
.req-item.fail{border-color:rgba(239,68,68,.25)}
.req-item.warn{border-color:rgba(251,191,36,.2)}
.req-status{font-size:1.2rem;flex-shrink:0;margin-top:1px}
.req-label{font-weight:600;color:#e2e8f0;font-size:.9rem}
.req-detail{font-size:.82rem;color:#64748b;margin-top:3px}
.req-detail code{background:rgba(0,0,0,.3);padding:1px 5px;border-radius:4px;font-size:.8rem}
.req-badge{
  margin-left:auto;flex-shrink:0;align-self:center;
  font-size:.7rem;padding:3px 10px;border-radius:20px;font-weight:600;
}
.badge-required{background:rgba(239,68,68,.15);color:#fca5a5;border:1px solid rgba(239,68,68,.25)}
.badge-optional{background:rgba(251,191,36,.1);color:#fde68a;border:1px solid rgba(251,191,36,.2)}

/* ── FORM ─────────────────────────────────────────────────── */
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}
.form-full{grid-column:1/-1}
.form-group{display:flex;flex-direction:column;gap:6px}
label{font-size:.85rem;font-weight:600;color:#94a3b8}
label span.hint{font-weight:400;color:#475569;font-size:.8rem;margin-left:6px}
input,select,textarea{
  background:rgba(255,255,255,.07);
  border:1px solid rgba(255,255,255,.12);
  border-radius:10px;
  padding:11px 14px;
  color:#e2e8f0;font-size:.9rem;
  outline:none;transition:border-color .2s,box-shadow .2s;
  width:100%;
}
input::placeholder{color:#334155}
input:focus,select:focus,textarea:focus{
  border-color:#3b82f6;
  box-shadow:0 0 0 3px rgba(59,130,246,.18);
}
select option{background:#1e293b;color:#e2e8f0}
.input-wrap{position:relative}
.input-wrap input{padding-right:44px}
.toggle-pass{
  position:absolute;right:13px;top:50%;transform:translateY(-50%);
  background:none;border:none;cursor:pointer;color:#64748b;font-size:1rem;
  padding:4px;transition:color .2s;
}
.toggle-pass:hover{color:#93c5fd}

/* ── PASSWORD STRENGTH ────────────────────────────────────── */
.strength-bar{height:4px;border-radius:4px;margin-top:6px;background:rgba(255,255,255,.08);overflow:hidden}
.strength-fill{height:100%;width:0;border-radius:4px;transition:width .3s,background .3s}
.strength-text{font-size:.75rem;margin-top:4px;color:#64748b}

/* ── DIVIDER ──────────────────────────────────────────────── */
.section-divider{
  display:flex;align-items:center;gap:12px;
  color:#334155;font-size:.78rem;font-weight:600;
  text-transform:uppercase;letter-spacing:.08em;
  margin:24px 0 18px;
}
.section-divider::before,.section-divider::after{
  content:'';flex:1;height:1px;background:rgba(255,255,255,.07);
}

/* ── BUTTONS ──────────────────────────────────────────────── */
.btn-row{display:flex;gap:12px;margin-top:28px;justify-content:flex-end}
.btn{
  padding:12px 28px;border-radius:12px;font-size:.9rem;
  font-weight:600;cursor:pointer;border:none;
  text-decoration:none;display:inline-flex;align-items:center;gap:8px;
  transition:all .2s;
}
.btn-primary{
  background:linear-gradient(135deg,#3b82f6,#6366f1);
  color:#fff;box-shadow:0 4px 16px rgba(59,130,246,.3);
}
.btn-primary:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(59,130,246,.4)}
.btn-primary:active{transform:translateY(0)}
.btn-primary:disabled{opacity:.5;cursor:not-allowed;transform:none}
.btn-ghost{
  background:rgba(255,255,255,.06);color:#94a3b8;
  border:1px solid rgba(255,255,255,.1);
  text-decoration:none;
}
.btn-ghost:hover{background:rgba(255,255,255,.1);color:#e2e8f0}

/* ── INSTALL RESULTS ──────────────────────────────────────── */
.install-log{
  background:rgba(0,0,0,.25);border:1px solid rgba(255,255,255,.08);
  border-radius:12px;padding:18px;
  display:flex;flex-direction:column;gap:10px;
  margin-bottom:24px;
}
.log-item{display:flex;align-items:flex-start;gap:12px;font-size:.88rem}
.log-icon{font-size:1.05rem;flex-shrink:0;margin-top:1px}
.log-ok{color:#86efac}
.log-fail{color:#fca5a5}
.log-msg{color:#cbd5e1;line-height:1.5}
.log-msg code{background:rgba(255,255,255,.08);padding:1px 6px;border-radius:4px;font-size:.82rem}
.log-msg strong{color:#e2e8f0}

/* ── SUCCESS BOX ──────────────────────────────────────────── */
.success-hero{
  text-align:center;padding:20px 0 28px;
}
.success-hero .hero-icon{
  width:80px;height:80px;margin:0 auto 20px;
  background:linear-gradient(135deg,#22c55e,#16a34a);
  border-radius:50%;display:flex;align-items:center;justify-content:center;
  font-size:36px;box-shadow:0 8px 32px rgba(34,197,94,.3);
}
.success-hero h2{font-size:1.6rem;color:#f1f5f9;margin-bottom:8px}
.success-hero p{color:#64748b;font-size:.95rem;max-width:460px;margin:0 auto}
.creds-box{
  background:rgba(59,130,246,.08);border:1px solid rgba(59,130,246,.2);
  border-radius:14px;padding:20px 24px;margin:24px 0;text-align:left;
}
.creds-box h4{color:#93c5fd;font-size:.85rem;text-transform:uppercase;letter-spacing:.08em;margin-bottom:14px}
.cred-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:10px}
.cred-label{font-size:.85rem;color:#64748b}
.cred-val{font-size:.9rem;color:#e2e8f0;font-weight:600;font-family:monospace;background:rgba(255,255,255,.07);padding:3px 10px;border-radius:6px}
.links-row{display:flex;gap:12px;justify-content:center;flex-wrap:wrap;margin-top:10px}
.warning-box{
  background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.25);
  border-radius:12px;padding:16px 20px;
  color:#fca5a5;font-size:.88rem;line-height:1.6;margin-top:20px;
}
.warning-box strong{display:block;margin-bottom:6px;font-size:.9rem}
.warning-box code{background:rgba(0,0,0,.3);padding:1px 6px;border-radius:4px}

/* ── REVIEW PANEL ─────────────────────────────────────────── */
.review-grid{display:flex;flex-direction:column;gap:8px;margin-bottom:24px}
.review-row{
  display:flex;gap:16px;background:rgba(255,255,255,.04);
  border:1px solid rgba(255,255,255,.07);border-radius:10px;padding:12px 16px;
  font-size:.87rem;
}
.review-key{color:#64748b;min-width:160px;flex-shrink:0}
.review-val{color:#e2e8f0;font-weight:500;word-break:break-all}

/* ── RESPONSIVE ───────────────────────────────────────────── */
@media(max-width:600px){
  body{padding:24px 12px 40px}
  .card-header,.card-body{padding:22px 20px}
  .form-grid{grid-template-columns:1fr}
  .form-full{grid-column:1}
  .progress-wrap{padding:18px 20px}
  .step-label{font-size:.65rem}
  .btn-row{flex-direction:column-reverse}
  .btn{justify-content:center}
  .review-row{flex-direction:column;gap:4px}
  .review-key{min-width:auto}
}
</style>
</head>
<body>

<!-- HEADER -->
<div class="installer-header">
  <div class="logo-icon">🏫</div>
  <h1>Wizard Instalasi Website SMK</h1>
  <p>Setup otomatis konfigurasi database, admin, dan aplikasi</p>
</div>

<!-- PROGRESS BAR -->
<div class="progress-wrap">
  <div class="steps">
    <?php
    $stepLabels = ['Persyaratan','Database','Admin','Selesai'];
    for ($i = 1; $i <= 4; $i++):
      $cls = $i < $step ? 'done' : ($i === $step ? 'active' : '');
      $icon = $i < $step ? '✓' : $i;
    ?>
    <div class="step-item <?= $cls ?>">
      <div class="step-circle"><?= $i < $step ? '✓' : $i ?></div>
      <div class="step-label"><?= $stepLabels[$i-1] ?></div>
    </div>
    <?php endfor; ?>
  </div>
</div>

<!-- CARD -->
<div class="card">


<?php /* ══════════════════════════════════════════════════════
   STEP 1 — PERSYARATAN
   ══════════════════════════════════════════════════════ */ ?>
<?php if ($step === 1): ?>

<div class="card-header">
  <h2>🔍 Cek Persyaratan Sistem</h2>
  <p>Memastikan server Anda memenuhi persyaratan minimum untuk menjalankan website.</p>
</div>
<div class="card-body">

  <?php if (!$allRequiredOk): ?>
  <div class="alert alert-error">
    <span class="alert-icon">⚠️</span>
    <div>Beberapa persyaratan <strong>wajib</strong> belum terpenuhi. Perbaiki masalah di bawah ini sebelum melanjutkan instalasi.</div>
  </div>
  <?php else: ?>
  <div class="alert alert-success">
    <span class="alert-icon">✅</span>
    <div>Semua persyaratan wajib terpenuhi! Anda dapat melanjutkan ke langkah berikutnya.</div>
  </div>
  <?php endif; ?>

  <div class="req-grid">
    <?php foreach ($reqChecks as $c):
      $cls = $c['ok'] ? 'ok' : ($c['required'] ? 'fail' : 'warn');
      $icon = $c['ok'] ? '✅' : ($c['required'] ? '❌' : '⚠️');
    ?>
    <div class="req-item <?= $cls ?>">
      <div class="req-status"><?= $icon ?></div>
      <div>
        <div class="req-label"><?= e($c['label']) ?></div>
        <div class="req-detail"><?= $c['detail'] ?></div>
      </div>
      <div class="req-badge <?= $c['required'] ? 'badge-required' : 'badge-optional' ?>">
        <?= $c['required'] ? 'Wajib' : 'Opsional' ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <div class="btn-row">
    <?php if ($allRequiredOk): ?>
    <a href="install.php?step=2" class="btn btn-primary">Lanjutkan ➜</a>
    <?php else: ?>
    <button class="btn btn-primary" disabled title="Perbaiki persyaratan di atas dulu">Lanjutkan ➜</button>
    <a href="install.php?step=1" class="btn btn-ghost">🔄 Refresh</a>
    <?php endif; ?>
  </div>

</div><!-- card-body -->

<?php /* ══════════════════════════════════════════════════════
   STEP 2 — DATABASE & KONFIGURASI
   ══════════════════════════════════════════════════════ */ ?>
<?php elseif ($step === 2): ?>

<div class="card-header">
  <h2>🗄️ Konfigurasi Database &amp; Aplikasi</h2>
  <p>Masukkan detail koneksi database dan pengaturan dasar aplikasi.</p>
</div>
<div class="card-body">

  <?php if (!empty($errors)): ?>
  <div class="alert alert-error">
    <span class="alert-icon">❌</span>
    <div><strong>Terjadi kesalahan:</strong>
      <ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
    </div>
  </div>
  <?php endif; ?>

  <form method="POST" action="install.php?step=2">

    <div class="section-divider">Koneksi Database</div>

    <div class="form-grid">
      <div class="form-group">
        <label for="db_host">Database Host</label>
        <input type="text" id="db_host" name="db_host" value="<?= e($dbVals['db_host']) ?>" placeholder="localhost" required>
      </div>
      <div class="form-group">
        <label for="db_port">Port</label>
        <input type="number" id="db_port" name="db_port" value="<?= e((string)$dbVals['db_port']) ?>" placeholder="3306" min="1" max="65535" required>
      </div>
      <div class="form-group">
        <label for="db_user">Database Username</label>
        <input type="text" id="db_user" name="db_user" value="<?= e($dbVals['db_user']) ?>" placeholder="root" required>
      </div>
      <div class="form-group">
        <label for="db_pass">Database Password <span class="hint">(kosongkan jika tidak ada)</span></label>
        <div class="input-wrap">
          <input type="password" id="db_pass" name="db_pass" value="<?= e($dbVals['db_pass']) ?>" placeholder="•••••••">
          <button type="button" class="toggle-pass" onclick="togglePass('db_pass',this)" title="Tampilkan/sembunyikan">👁</button>
        </div>
      </div>
      <div class="form-group form-full">
        <label for="db_name">Nama Database</label>
        <input type="text" id="db_name" name="db_name" value="<?= e($dbVals['db_name']) ?>" placeholder="websmk" required>
        <div style="font-size:.78rem;color:#475569;margin-top:4px">ℹ️ Database akan dibuat otomatis jika belum ada.</div>
      </div>
    </div>

    <div class="section-divider">Pengaturan Aplikasi</div>

    <div class="form-grid">
      <div class="form-group form-full">
        <label for="app_url">URL Aplikasi <span class="hint">tanpa trailing slash</span></label>
        <input type="url" id="app_url" name="app_url" value="<?= e($dbVals['app_url']) ?>" placeholder="http://localhost/webpertamaku" required>
        <div style="font-size:.78rem;color:#475569;margin-top:4px">Contoh: <code style="background:rgba(255,255,255,.07);padding:1px 5px;border-radius:4px">http://localhost/webpertamaku</code> atau <code style="background:rgba(255,255,255,.07);padding:1px 5px;border-radius:4px">https://smkpertamaku.sch.id</code></div>
      </div>
      <div class="form-group">
        <label for="app_base">Base Path <span class="hint">kosong jika root domain</span></label>
        <input type="text" id="app_base" name="app_base" value="<?= e($dbVals['app_base']) ?>" placeholder="/webpertamaku">
        <div style="font-size:.78rem;color:#475569;margin-top:4px">Contoh: <code style="background:rgba(255,255,255,.07);padding:1px 5px;border-radius:4px">/webpertamaku</code> atau kosong jika domain root</div>
      </div>
      <div class="form-group">
        <label for="timezone">Zona Waktu</label>
        <select id="timezone" name="timezone">
          <option value="Asia/Jakarta"<?= sel($dbVals['timezone'],'Asia/Jakarta') ?>>Asia/Jakarta (WIB, UTC+7)</option>
          <option value="Asia/Makassar"<?= sel($dbVals['timezone'],'Asia/Makassar') ?>>Asia/Makassar (WITA, UTC+8)</option>
          <option value="Asia/Jayapura"<?= sel($dbVals['timezone'],'Asia/Jayapura') ?>>Asia/Jayapura (WIT, UTC+9)</option>
          <option value="UTC"<?= sel($dbVals['timezone'],'UTC') ?>>UTC</option>
        </select>
      </div>
    </div>

    <div class="btn-row">
      <a href="install.php?step=1" class="btn btn-ghost">← Kembali</a>
      <button type="submit" class="btn btn-primary">Uji &amp; Lanjutkan ➜</button>
    </div>

  </form>
</div><!-- card-body -->

<?php /* ══════════════════════════════════════════════════════
   STEP 3 — AKUN ADMINISTRATOR
   ══════════════════════════════════════════════════════ */ ?>
<?php elseif ($step === 3): ?>

<div class="card-header">
  <h2>👤 Akun Administrator</h2>
  <p>Buat akun superadmin untuk mengelola website. Simpan kredensial ini dengan aman!</p>
</div>
<div class="card-body">

  <?php if (!empty($errors)): ?>
  <div class="alert alert-error">
    <span class="alert-icon">❌</span>
    <div><strong>Periksa isian berikut:</strong>
      <ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
    </div>
  </div>
  <?php endif; ?>

  <form method="POST" action="install.php?step=3" id="adminForm">
    <div class="form-grid">

      <div class="form-group form-full">
        <label for="admin_name">Nama Lengkap Admin</label>
        <input type="text" id="admin_name" name="admin_name" value="<?= e($admVals['admin_name']) ?>" placeholder="Super Admin" required>
      </div>

      <div class="form-group">
        <label for="admin_user">Username</label>
        <input type="text" id="admin_user" name="admin_user"
          value="<?= e($admVals['admin_user']) ?>"
          placeholder="admin"
          pattern="[a-zA-Z0-9_]+"
          title="Hanya huruf, angka, dan underscore"
          required
          oninput="validateUsername(this)">
        <div id="user-hint" style="font-size:.78rem;margin-top:4px;color:#475569">Hanya huruf, angka, dan underscore (a-z, 0-9, _)</div>
      </div>

      <div class="form-group">
        <label for="admin_email">Email Admin</label>
        <input type="email" id="admin_email" name="admin_email" value="<?= e($admVals['admin_email']) ?>" placeholder="admin@sekolah.sch.id" required>
      </div>

      <div class="form-group">
        <label for="admin_pass">Password <span class="hint">min. 6 karakter</span></label>
        <div class="input-wrap">
          <input type="password" id="admin_pass" name="admin_pass" placeholder="••••••••" minlength="6" required oninput="checkStrength(this.value)">
          <button type="button" class="toggle-pass" onclick="togglePass('admin_pass',this)" title="Tampilkan/sembunyikan">👁</button>
        </div>
        <div class="strength-bar"><div class="strength-fill" id="strength-fill"></div></div>
        <div class="strength-text" id="strength-text">Masukkan password</div>
      </div>

      <div class="form-group">
        <label for="admin_pass2">Konfirmasi Password</label>
        <div class="input-wrap">
          <input type="password" id="admin_pass2" name="admin_pass2" placeholder="••••••••" required oninput="checkMatch()">
          <button type="button" class="toggle-pass" onclick="togglePass('admin_pass2',this)" title="Tampilkan/sembunyikan">👁</button>
        </div>
        <div id="match-msg" style="font-size:.78rem;margin-top:4px;color:#475569"> </div>
      </div>

    </div>

    <div class="alert alert-warning" style="margin-top:20px">
      <span class="alert-icon">💡</span>
      <div>Simpan username dan password ini di tempat yang aman. Password <strong>tidak dapat dipulihkan</strong> tanpa akses ke database.</div>
    </div>

    <div class="btn-row">
      <a href="install.php?step=2" class="btn btn-ghost">← Kembali</a>
      <button type="submit" class="btn btn-primary">Lanjutkan ➜</button>
    </div>
  </form>

</div><!-- card-body -->

<?php /* ══════════════════════════════════════════════════════
   STEP 4 — PROSES INSTALASI & SELESAI
   ══════════════════════════════════════════════════════ */ ?>
<?php elseif ($step === 4): ?>

<?php if ($installDone && !empty($installResults)): ?>
<!-- ── SUCCESS STATE ───────────────────────────────────────── -->
<div class="card-header">
  <h2>🎉 Instalasi Berhasil!</h2>
  <p>Website SMK Pertamaku berhasil diinstal dan siap digunakan.</p>
</div>
<div class="card-body">

  <div class="install-log">
    <?php foreach ($installResults as $r): ?>
    <div class="log-item">
      <span class="log-icon"><?= $r['ok'] ? '✅' : '⚠️' ?></span>
      <span class="log-msg <?= $r['ok'] ? 'log-ok' : 'log-fail' ?>"><?= $r['msg'] ?></span>
    </div>
    <?php endforeach; ?>
  </div>

  <div class="success-hero">
    <div class="hero-icon">🎊</div>
    <h2>Website Siap Digunakan!</h2>
    <p>Semua komponen berhasil dikonfigurasi. Kunjungi website atau masuk ke panel admin untuk memulai.</p>
  </div>

  <?php
    $finalUrl  = rtrim($_SESSION['install_db']['app_url'] ?? '', '/');
    $adminUser = $_SESSION['install_admin']['admin_user'] ?? 'admin';
    $adminName = $_SESSION['install_admin']['admin_name'] ?? 'Super Admin';
    $adminEmail= $_SESSION['install_admin']['admin_email'] ?? '';
  ?>

  <div class="creds-box">
    <h4>📋 Kredensial Login Admin</h4>
    <div class="cred-row">
      <span class="cred-label">URL Admin</span>
      <span class="cred-val"><?= e($finalUrl) ?>/admin/login</span>
    </div>
    <div class="cred-row">
      <span class="cred-label">Nama</span>
      <span class="cred-val"><?= e($adminName) ?></span>
    </div>
    <div class="cred-row">
      <span class="cred-label">Username</span>
      <span class="cred-val"><?= e($adminUser) ?></span>
    </div>
    <div class="cred-row">
      <span class="cred-label">Email</span>
      <span class="cred-val"><?= e($adminEmail) ?></span>
    </div>
    <div class="cred-row">
      <span class="cred-label">Password</span>
      <span class="cred-val">••••••••&nbsp;<span style="font-size:.75rem;color:#475569">(seperti yang Anda masukkan)</span></span>
    </div>
  </div>

  <div class="links-row">
    <?php if ($finalUrl): ?>
    <a href="<?= e($finalUrl) ?>/" class="btn btn-ghost" target="_blank">🌐 Buka Website</a>
    <a href="<?= e($finalUrl) ?>/admin/login" class="btn btn-primary" target="_blank">⚙️ Masuk ke Admin Panel</a>
    <?php endif; ?>
  </div>

  <div class="warning-box">
    <strong>⚠️ Penting — Hapus File Installer!</strong>
    Demi keamanan, <strong>segera hapus</strong> file installer setelah instalasi selesai.
    Akses ke installer oleh orang lain dapat membahayakan website Anda.<br><br>
    File yang perlu dihapus:<br>
    <code><?= e(__FILE__) ?></code>
  </div>

  <?php
  // Clear install session data
  unset($_SESSION['install_db'], $_SESSION['install_admin']);
  ?>

</div><!-- card-body -->

<?php elseif (!empty($installResults) && !$installDone): ?>
<!-- ── FAILED INSTALL STATE ────────────────────────────────── -->
<div class="card-header">
  <h2>❌ Instalasi Gagal</h2>
  <p>Terjadi kesalahan saat proses instalasi. Periksa log di bawah.</p>
</div>
<div class="card-body">
  <div class="install-log">
    <?php foreach ($installResults as $r): ?>
    <div class="log-item">
      <span class="log-icon"><?= $r['ok'] ? '✅' : '❌' ?></span>
      <span class="log-msg <?= $r['ok'] ? 'log-ok' : 'log-fail' ?>"><?= $r['msg'] ?></span>
    </div>
    <?php endforeach; ?>
  </div>
  <div class="alert alert-error">
    <span class="alert-icon">ℹ️</span>
    <div>Periksa konfigurasi database dan permission folder, lalu coba lagi. Jika masalah berlanjut, hubungi hosting Anda.</div>
  </div>
  <div class="btn-row">
    <a href="install.php?step=2" class="btn btn-ghost">← Ubah Konfigurasi</a>
    <form method="POST" action="install.php?step=4" style="margin:0">
      <input type="hidden" name="action" value="install">
      <button type="submit" class="btn btn-primary">🔄 Coba Lagi</button>
    </form>
  </div>
</div>

<?php else: ?>
<!-- ── REVIEW & CONFIRM STATE ──────────────────────────────── -->
<div class="card-header">
  <h2>🚀 Konfirmasi &amp; Mulai Instalasi</h2>
  <p>Periksa ringkasan konfigurasi Anda sebelum memulai proses instalasi.</p>
</div>
<div class="card-body">

  <?php if (empty($_SESSION['install_db']) || empty($_SESSION['install_admin'])): ?>
  <div class="alert alert-error">
    <span class="alert-icon">⚠️</span>
    <div>Data sesi tidak lengkap. Silakan mulai dari langkah 1.
      <br><a href="install.php?step=1" style="color:#93c5fd">← Mulai ulang</a>
    </div>
  </div>
  <?php else:
    $db  = $_SESSION['install_db'];
    $adm = $_SESSION['install_admin'];
  ?>

  <div class="section-divider">Konfigurasi Database</div>
  <div class="review-grid">
    <div class="review-row"><span class="review-key">Host : Port</span><span class="review-val"><?= e($db['db_host']) ?> : <?= e((string)$db['db_port']) ?></span></div>
    <div class="review-row"><span class="review-key">Username</span><span class="review-val"><?= e($db['db_user']) ?></span></div>
    <div class="review-row"><span class="review-key">Nama Database</span><span class="review-val"><?= e($db['db_name']) ?></span></div>
    <div class="review-row"><span class="review-key">URL Aplikasi</span><span class="review-val"><?= e($db['app_url']) ?></span></div>
    <div class="review-row"><span class="review-key">Base Path</span><span class="review-val"><?= e($db['app_base'] ?: '(root)') ?></span></div>
    <div class="review-row"><span class="review-key">Zona Waktu</span><span class="review-val"><?= e($db['timezone']) ?></span></div>
  </div>

  <div class="section-divider">Akun Administrator</div>
  <div class="review-grid">
    <div class="review-row"><span class="review-key">Nama Lengkap</span><span class="review-val"><?= e($adm['admin_name']) ?></span></div>
    <div class="review-row"><span class="review-key">Username</span><span class="review-val"><?= e($adm['admin_user']) ?></span></div>
    <div class="review-row"><span class="review-key">Email</span><span class="review-val"><?= e($adm['admin_email']) ?></span></div>
    <div class="review-row"><span class="review-key">Password</span><span class="review-val">••••••••</span></div>
  </div>

  <div class="section-divider">Yang Akan Dilakukan</div>
  <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:24px;font-size:.88rem;color:#94a3b8">
    <div>📦 Membuat database <strong style="color:#e2e8f0"><?= e($db['db_name']) ?></strong> (jika belum ada)</div>
    <div>🗃️ Menjalankan skema tabel dari <code style="background:rgba(255,255,255,.07);padding:1px 5px;border-radius:4px">database/schema.sql</code></div>
    <div>👤 Membuat/memperbarui akun admin <strong style="color:#e2e8f0"><?= e($adm['admin_user']) ?></strong></div>
    <div>⚙️ Menulis file konfigurasi <code style="background:rgba(255,255,255,.07);padding:1px 5px;border-radius:4px">config/env.php</code></div>
    <div>🔒 Membuat file kunci <code style="background:rgba(255,255,255,.07);padding:1px 5px;border-radius:4px">install.lock</code></div>
  </div>

  <form method="POST" action="install.php?step=4">
    <input type="hidden" name="action" value="install">
    <div class="btn-row">
      <a href="install.php?step=3" class="btn btn-ghost">← Kembali</a>
      <button type="submit" class="btn btn-primary" id="installBtn" onclick="startInstall(this)">
        🚀 Mulai Instalasi
      </button>
    </div>
  </form>
  <?php endif; ?>

</div><!-- card-body -->
<?php endif; ?>

<?php endif; /* end step switch */ ?>

</div><!-- .card -->

<div style="margin-top:24px;font-size:.78rem;color:#334155;text-align:center">
  SMK Pertamaku Installer &bull; PHP <?= PHP_VERSION ?> &bull;
  <?= date('Y') ?> &bull;
  <span style="color:#1e40af">Hapus file ini setelah instalasi selesai</span>
</div>

<!-- ── JAVASCRIPT ──────────────────────────────────────────── -->
<script>
function togglePass(id, btn) {
  const inp = document.getElementById(id);
  if (!inp) return;
  if (inp.type === 'password') {
    inp.type = 'text';
    btn.textContent = '🙈';
  } else {
    inp.type = 'password';
    btn.textContent = '👁';
  }
}

function checkStrength(val) {
  const fill = document.getElementById('strength-fill');
  const text = document.getElementById('strength-text');
  if (!fill || !text) return;

  let score = 0;
  if (val.length >= 6)  score++;
  if (val.length >= 10) score++;
  if (/[A-Z]/.test(val)) score++;
  if (/[0-9]/.test(val)) score++;
  if (/[^A-Za-z0-9]/.test(val)) score++;

  const levels = [
    {pct:'0%',   color:'#ef4444', label:''},
    {pct:'20%',  color:'#ef4444', label:'Sangat lemah'},
    {pct:'40%',  color:'#f97316', label:'Lemah'},
    {pct:'60%',  color:'#eab308', label:'Sedang'},
    {pct:'80%',  color:'#22c55e', label:'Kuat'},
    {pct:'100%', color:'#16a34a', label:'Sangat kuat 🔒'},
  ];
  const lv = levels[score] || levels[0];
  fill.style.width    = val.length ? lv.pct : '0%';
  fill.style.background = lv.color;
  text.textContent    = val.length ? lv.label : 'Masukkan password';
  text.style.color    = lv.color;
}

function checkMatch() {
  const p1  = document.getElementById('admin_pass');
  const p2  = document.getElementById('admin_pass2');
  const msg = document.getElementById('match-msg');
  if (!p1 || !p2 || !msg) return;
  if (!p2.value) { msg.textContent = ' '; msg.style.color = '#475569'; return; }
  if (p1.value === p2.value) {
    msg.textContent = '✅ Password cocok';
    msg.style.color = '#86efac';
  } else {
    msg.textContent = '❌ Password tidak cocok';
    msg.style.color = '#fca5a5';
  }
}

function validateUsername(inp) {
  const hint = document.getElementById('user-hint');
  if (!hint) return;
  if (/^[a-zA-Z0-9_]+$/.test(inp.value)) {
    hint.style.color = '#86efac';
    hint.textContent = '✅ Username valid';
  } else {
    hint.style.color = '#fca5a5';
    hint.textContent = '❌ Hanya boleh huruf, angka, dan underscore';
  }
}

function startInstall(btn) {
  btn.disabled = true;
  btn.innerHTML = '<span style="display:inline-block;animation:spin 1s linear infinite">⚙️</span> Menginstal...';
}
</script>
<style>
@keyframes spin {
  from { transform: rotate(0deg); }
  to   { transform: rotate(360deg); }
}
</style>

</body>
</html>

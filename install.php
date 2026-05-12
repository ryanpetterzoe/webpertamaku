<?php
// ============================================================
// install.php — Wizard Instalasi Website SMK Pertamaku
// Clean 4-step installer. Compatible with PHP 7.4+
// ============================================================
set_time_limit(120);
ini_set('max_execution_time', 120);

session_start();

// ─── LOCK CHECK ───────────────────────────────────────────────
$lockedPage = false;
if (file_exists(__DIR__ . '/install.lock')) {
    $lockedPage = true;
}
if (!$lockedPage && file_exists(__DIR__ . '/config/env.php')) {
    @include_once __DIR__ . '/config/env.php';
    if (defined('INSTALLER_LOCKED') && INSTALLER_LOCKED === true) {
        $lockedPage = true;
    }
}
if ($lockedPage) {
    $appUrl = defined('APP_URL') ? APP_URL : '';
    ?><!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Installer Terkunci</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{min-height:100vh;background:linear-gradient(135deg,#0f172a 0%,#1e293b 100%);display:flex;align-items:center;justify-content:center;font-family:'Segoe UI',system-ui,sans-serif;color:#e2e8f0}
.card{background:rgba(255,255,255,0.06);backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,0.1);border-radius:20px;padding:48px 40px;max-width:520px;width:90%;text-align:center}
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
  <div class="icon">&#x1F512;</div>
  <div class="badge">Installer Terkunci</div>
  <h1>Instalasi Sudah Selesai</h1>
  <p>File installer telah dikunci untuk keamanan. Website sudah berhasil diinstal dan siap digunakan.</p>
  <?php if ($appUrl): ?>
  <a href="<?php echo htmlspecialchars($appUrl); ?>/" class="btn">&#x1F310; Buka Website</a>
  <a href="<?php echo htmlspecialchars($appUrl); ?>/admin/login" class="btn btn-outline">&#x2699;&#xFE0F; Panel Admin</a>
  <?php endif; ?>
  <div class="tip">
    <strong>&#x26A0;&#xFE0F; Ingin menjalankan ulang installer?</strong><br>
    Hapus file kunci berikut melalui FTP / File Manager:<br><br>
    <code><?php echo htmlspecialchars(__DIR__ . '/install.lock'); ?></code>
  </div>
</div>
</body>
</html>
<?php
    exit;
}

// ─── STEP ROUTING ─────────────────────────────────────────────
$step   = max(1, min(4, (int)($_GET['step'] ?? 1)));
$errors = array();

// ─── POST HANDLER: STEP 2 (Database + App Config) ─────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 2) {
    $db_host  = trim($_POST['db_host']  ?? 'localhost');
    $db_port  = (int)($_POST['db_port'] ?? 3306);
    $db_user  = trim($_POST['db_user']  ?? 'root');
    $db_pass  = $_POST['db_pass']       ?? '';
    $db_name  = trim($_POST['db_name']  ?? 'websmk');
    $app_url  = rtrim(trim($_POST['app_url'] ?? ''), '/');
    $app_base = trim($_POST['app_base'] ?? '');
    $timezone = trim($_POST['timezone'] ?? 'Asia/Jakarta');

    $allowed_tz = array('Asia/Jakarta', 'Asia/Makassar', 'Asia/Jayapura', 'UTC');
    if (!in_array($timezone, $allowed_tz)) {
        $timezone = 'Asia/Jakarta';
    }

    if (empty($db_host)) { $errors[] = 'Database host tidak boleh kosong.'; }
    if (empty($db_name)) { $errors[] = 'Nama database tidak boleh kosong.'; }
    if (empty($app_url)) { $errors[] = 'URL Aplikasi tidak boleh kosong.'; }

    if (empty($errors)) {
        $conn = @mysqli_connect($db_host . ':' . $db_port, $db_user, $db_pass);
        if (!$conn) {
            $errors[] = 'Koneksi database gagal: ' . mysqli_connect_error() . ' (errno: ' . mysqli_connect_errno() . ')';
        } else {
            mysqli_close($conn);
            $_SESSION['install_db'] = array(
                'db_host'  => $db_host,
                'db_port'  => $db_port,
                'db_user'  => $db_user,
                'db_pass'  => $db_pass,
                'db_name'  => $db_name,
                'app_url'  => $app_url,
                'app_base' => $app_base,
                'timezone' => $timezone,
            );
            header('Location: install.php?step=3');
            exit;
        }
    }
}

// ─── POST HANDLER: STEP 3 (Admin Account) ─────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 3) {
    $admin_name  = trim($_POST['admin_name']  ?? '');
    $admin_user  = trim($_POST['admin_user']  ?? '');
    $admin_email = trim($_POST['admin_email'] ?? '');
    $admin_pass  = $_POST['admin_pass']        ?? '';
    $admin_pass2 = $_POST['admin_pass2']       ?? '';

    if (empty($admin_name)) {
        $errors[] = 'Nama lengkap admin wajib diisi.';
    }
    if (empty($admin_user) || !preg_match('/^[a-zA-Z0-9_]+$/', $admin_user)) {
        $errors[] = 'Username hanya boleh huruf, angka, dan underscore.';
    }
    if (!filter_var($admin_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid.';
    }
    if (strlen($admin_pass) < 6) {
        $errors[] = 'Password minimal 6 karakter.';
    }
    if ($admin_pass !== $admin_pass2) {
        $errors[] = 'Konfirmasi password tidak cocok.';
    }

    if (empty($errors)) {
        $_SESSION['install_admin'] = array(
            'admin_name'  => $admin_name,
            'admin_user'  => $admin_user,
            'admin_email' => $admin_email,
            'admin_pass'  => $admin_pass,
        );
        header('Location: install.php?step=4');
        exit;
    }
}

// ─── POST HANDLER: STEP 4 (Actual Installation) ───────────────
$installResults = array();
$installDone    = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 4 && isset($_POST['action']) && $_POST['action'] === 'install') {
    set_time_limit(120);

    $db  = isset($_SESSION['install_db'])    ? $_SESSION['install_db']    : array();
    $adm = isset($_SESSION['install_admin']) ? $_SESSION['install_admin'] : array();

    if (empty($db) || empty($adm)) {
        $errors[] = 'Data sesi tidak lengkap. Silakan mulai dari langkah 1.';
    } else {
        // Step 1: Connect to MySQL (no DB selected)
        $conn = @mysqli_connect($db['db_host'] . ':' . $db['db_port'], $db['db_user'], $db['db_pass']);
        if (!$conn) {
            $installResults[] = array('ok' => false, 'msg' => 'Koneksi database gagal: ' . mysqli_connect_error());
        } else {
            $installResults[] = array('ok' => true, 'msg' => 'Koneksi ke database server berhasil');

            // Step 2: CREATE DATABASE IF NOT EXISTS (backtick-quoted)
            $dbSafe = $db['db_name'];
            $createSql = "CREATE DATABASE IF NOT EXISTS `" . $dbSafe . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
            if (mysqli_query($conn, $createSql)) {
                $installResults[] = array('ok' => true, 'msg' => 'Database <code>' . htmlspecialchars($dbSafe) . '</code> berhasil dibuat / sudah ada');
            } else {
                $installResults[] = array('ok' => false, 'msg' => 'Gagal membuat database: ' . mysqli_error($conn));
            }

            // Step 3: Select DB
            if (mysqli_select_db($conn, $db['db_name'])) {
                $installResults[] = array('ok' => true, 'msg' => 'Database <code>' . htmlspecialchars($db['db_name']) . '</code> berhasil dipilih');
            } else {
                $installResults[] = array('ok' => false, 'msg' => 'Gagal memilih database: ' . mysqli_error($conn));
            }

            // Step 4: Read and execute schema.sql
            $schemaFile = __DIR__ . '/database/schema.sql';
            if (!file_exists($schemaFile)) {
                $installResults[] = array('ok' => false, 'msg' => 'File <code>database/schema.sql</code> tidak ditemukan');
            } else {
                $sql = file_get_contents($schemaFile);
                // Remove single-line SQL comments (-- ...) but preserve newlines
                $sql = preg_replace('/--[^\n]*/', '', $sql);
                // Remove block comments /* ... */
                $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
                // Split on semicolons
                $rawStatements = explode(';', $sql);
                // Filter empty statements using regular function (not fn())
                $statements = array_filter($rawStatements, function ($s) {
                    return trim($s) !== '';
                });

                $sqlOk   = true;
                $sqlFail = 0;
                $sqlRun  = 0;
                $ignoredErrCodes = array(1050, 1061, 1062, 1007);

                foreach ($statements as $stmt) {
                    $stmt = trim($stmt);
                    if ($stmt === '') {
                        continue;
                    }
                    $sqlRun++;
                    if (!mysqli_query($conn, $stmt)) {
                        $errno = mysqli_errno($conn);
                        $sqlFail++;
                        if (!in_array($errno, $ignoredErrCodes)) {
                            // Log but CONTINUE — do not stop installation
                            $sqlOk = false;
                        }
                    }
                }

                if ($sqlOk) {
                    $installResults[] = array('ok' => true, 'msg' => 'Skema database berhasil dijalankan (' . $sqlRun . ' statements, ' . $sqlFail . ' ignored)');
                } else {
                    $installResults[] = array('ok' => false, 'msg' => 'Beberapa pernyataan SQL gagal (' . $sqlFail . ' error) — instalasi tetap dilanjutkan. Error terakhir: ' . mysqli_error($conn));
                }
            }

            // Step 5: Hash password and insert admin
            $hashedPass = password_hash($adm['admin_pass'], PASSWORD_DEFAULT);
            $safeName   = mysqli_real_escape_string($conn, $adm['admin_name']);
            $safeUser   = mysqli_real_escape_string($conn, $adm['admin_user']);
            $safeEmail  = mysqli_real_escape_string($conn, $adm['admin_email']);
            $safePass   = mysqli_real_escape_string($conn, $hashedPass);
            $sqlAdm = "INSERT INTO `admins` (`name`,`username`,`email`,`password`,`role`) "
                    . "VALUES ('{$safeName}','{$safeUser}','{$safeEmail}','{$safePass}','superadmin') "
                    . "ON DUPLICATE KEY UPDATE `name`=VALUES(`name`),`password`=VALUES(`password`),`email`=VALUES(`email`)";
            if (mysqli_query($conn, $sqlAdm)) {
                $installResults[] = array('ok' => true, 'msg' => 'Akun administrator <strong>' . htmlspecialchars($safeUser) . '</strong> berhasil disimpan');
            } else {
                $installResults[] = array('ok' => false, 'msg' => 'Gagal menyimpan admin: ' . mysqli_error($conn));
            }
            mysqli_close($conn);

            // Step 6: Write config/env.php
            $envContent  = '<?php' . "\n";
            $envContent .= "define('DB_HOST',      '" . addslashes($db['db_host']) . "');\n";
            $envContent .= "define('DB_USER',      '" . addslashes($db['db_user']) . "');\n";
            $envContent .= "define('DB_PASS',      '" . addslashes($db['db_pass']) . "');\n";
            $envContent .= "define('DB_NAME',      '" . addslashes($db['db_name']) . "');\n";
            $envContent .= "define('DB_PORT',      " . (int)$db['db_port'] . ");\n";
            $envContent .= "define('DB_PREFIX',    '');\n";
            $envContent .= "define('APP_URL',      '" . rtrim($db['app_url'], '/') . "');\n";
            $envContent .= "define('APP_BASE',     '" . $db['app_base'] . "');\n";
            $envContent .= "define('APP_KEY',      '" . bin2hex(random_bytes(24)) . "');\n";
            $envContent .= "define('APP_TIMEZONE', '" . $db['timezone'] . "');\n";
            $envContent .= "date_default_timezone_set(APP_TIMEZONE);\n";
            $envContent .= "define('APP_ENV',      'production');\n";
            $envContent .= "define('INSTALLER_LOCKED', false);\n";

            $envPath = __DIR__ . '/config/env.php';
            if (file_put_contents($envPath, $envContent) !== false) {
                $installResults[] = array('ok' => true, 'msg' => 'File <code>config/env.php</code> berhasil ditulis');
            } else {
                $installResults[] = array('ok' => false, 'msg' => 'Gagal menulis <code>config/env.php</code> — pastikan folder config/ writable');
            }

            // Step 7: Check critical failures (ignore lock failure)
            $criticalFail = false;
            foreach ($installResults as $r) {
                if (!$r['ok']) {
                    $criticalFail = true;
                    break;
                }
            }
            $installDone = !$criticalFail;

            // Step 8: Write install.lock (non-critical)
            $lockWritten = file_put_contents(__DIR__ . '/install.lock', date('Y-m-d H:i:s') . "\nInstalled successfully.\n");
            if ($lockWritten !== false) {
                $installResults[] = array('ok' => true, 'msg' => 'File <code>install.lock</code> berhasil dibuat');
            } else {
                $installResults[] = array('ok' => false, 'msg' => 'Gagal membuat <code>install.lock</code> — instalasi tetap berhasil, harap buat file ini secara manual');
            }
        }
    }
}

// ─── REQUIREMENTS CHECK (Step 1) ──────────────────────────────
function checkRequirements() {
    $checks = array();

    $phpOk = version_compare(PHP_VERSION, '7.4.0', '>=');
    $checks[] = array('label' => 'PHP >= 7.4', 'detail' => 'Versi: PHP ' . PHP_VERSION, 'ok' => $phpOk, 'required' => true);

    $mysqliOk = extension_loaded('mysqli');
    $checks[] = array('label' => 'Ekstensi MySQLi', 'detail' => $mysqliOk ? 'Tersedia' : 'Tidak tersedia', 'ok' => $mysqliOk, 'required' => true);

    $configOk = is_dir(__DIR__ . '/config/') && is_writable(__DIR__ . '/config/');
    $checks[] = array('label' => 'Folder config/ writable', 'detail' => $configOk ? 'OK' : 'Tidak writable - chmod 775 config/', 'ok' => $configOk, 'required' => true);

    $uploadsOk = is_dir(__DIR__ . '/assets/images/uploads/') && is_writable(__DIR__ . '/assets/images/uploads/');
    $checks[] = array('label' => 'Folder uploads/ writable', 'detail' => $uploadsOk ? 'OK' : 'Tidak writable - chmod 775 assets/images/uploads/', 'ok' => $uploadsOk, 'required' => true);

    $gdOk = extension_loaded('gd');
    $checks[] = array('label' => 'Ekstensi GD (gambar)', 'detail' => $gdOk ? 'Tersedia' : 'Tidak tersedia (opsional)', 'ok' => $gdOk, 'required' => false);

    return $checks;
}

$reqChecks    = ($step === 1) ? checkRequirements() : array();
$allRequiredOk = true;
foreach ($reqChecks as $c) {
    if ($c['required'] && !$c['ok']) {
        $allRequiredOk = false;
        break;
    }
}

// ─── PREFILL VALUES ───────────────────────────────────────────
$dbVals = isset($_SESSION['install_db']) ? $_SESSION['install_db'] : array(
    'db_host'  => 'localhost',
    'db_port'  => 3306,
    'db_user'  => 'root',
    'db_pass'  => '',
    'db_name'  => 'websmk',
    'app_url'  => 'http://localhost/webpertamaku',
    'app_base' => '/webpertamaku',
    'timezone' => 'Asia/Jakarta',
);
$admVals = isset($_SESSION['install_admin']) ? $_SESSION['install_admin'] : array(
    'admin_name'  => 'Super Admin',
    'admin_user'  => 'admin',
    'admin_email' => '',
);

// Repopulate from POST on validation error
if (!empty($errors) && $step === 2) {
    foreach (array('db_host','db_port','db_user','db_pass','db_name','app_url','app_base','timezone') as $k) {
        if (isset($_POST[$k])) {
            $dbVals[$k] = $_POST[$k];
        }
    }
}
if (!empty($errors) && $step === 3) {
    foreach (array('admin_name','admin_user','admin_email') as $k) {
        if (isset($_POST[$k])) {
            $admVals[$k] = $_POST[$k];
        }
    }
}

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function sel($val, $cmp) { return $val === $cmp ? ' selected' : ''; }
?>


<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Installer &mdash; SMK Pertamaku</title>
<style>
/* ── RESET & BASE ──────────────────────────────────────────── */
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
html{scroll-behavior:smooth}
body{
  min-height:100vh;
  background:linear-gradient(135deg,#0f172a 0%,#1e293b 100%);
  font-family:'Segoe UI',system-ui,-apple-system,sans-serif;
  color:#e2e8f0;
  display:flex;flex-direction:column;align-items:center;
  padding:40px 16px 60px;
}

/* ── HEADER ─────────────────────────────────────────────────── */
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

/* ── PROGRESS BAR ────────────────────────────────────────────── */
.progress-wrap{
  width:100%;max-width:700px;
  background:rgba(255,255,255,.05);
  border:1px solid rgba(255,255,255,.09);
  border-radius:16px;padding:24px 32px;
  margin-bottom:32px;
}
.steps{display:flex;align-items:center}
.step-item{
  display:flex;flex-direction:column;align-items:center;
  flex:1;position:relative;
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
.step-label{font-size:.72rem;color:#64748b;margin-top:8px;text-align:center;font-weight:500;white-space:nowrap}
.step-item.active .step-label{color:#93c5fd;font-weight:600}
.step-item.done .step-label{color:#60a5fa}

/* ── CARD ────────────────────────────────────────────────────── */
.card{
  width:100%;max-width:700px;
  background:rgba(255,255,255,0.06);
  backdrop-filter:blur(20px);
  -webkit-backdrop-filter:blur(20px);
  border:1px solid rgba(255,255,255,0.1);
  border-radius:20px;
  overflow:hidden;
}
.card-header{padding:28px 36px 20px;border-bottom:1px solid rgba(255,255,255,.07)}
.card-header h2{font-size:1.25rem;color:#f1f5f9;display:flex;align-items:center;gap:10px}
.card-header p{color:#64748b;font-size:.9rem;margin-top:6px}
.card-body{padding:28px 36px}

/* ── ALERTS ──────────────────────────────────────────────────── */
.alert{border-radius:12px;padding:14px 18px;margin-bottom:20px;display:flex;gap:12px;align-items:flex-start;font-size:.9rem}
.alert-error{background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.3);color:#fca5a5}
.alert-success{background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.25);color:#86efac}
.alert-warning{background:rgba(251,191,36,.08);border:1px solid rgba(251,191,36,.2);color:#fde68a}
.alert-icon{font-size:1.1rem;flex-shrink:0;margin-top:1px}
.alert ul{margin:6px 0 0 16px}
.alert li{margin:3px 0}

/* ── REQUIREMENTS ─────────────────────────────────────────────── */
.req-grid{display:flex;flex-direction:column;gap:10px}
.req-item{display:flex;align-items:flex-start;gap:14px;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.07);border-radius:12px;padding:14px 18px}
.req-item.ok{border-color:rgba(34,197,94,.2)}
.req-item.fail{border-color:rgba(239,68,68,.25)}
.req-item.warn{border-color:rgba(251,191,36,.2)}
.req-status{font-size:1.2rem;flex-shrink:0;margin-top:1px}
.req-label{font-weight:600;color:#e2e8f0;font-size:.9rem}
.req-detail{font-size:.82rem;color:#64748b;margin-top:3px}
.req-badge{margin-left:auto;flex-shrink:0;align-self:center;font-size:.7rem;padding:3px 10px;border-radius:20px;font-weight:600}
.badge-required{background:rgba(239,68,68,.15);color:#fca5a5;border:1px solid rgba(239,68,68,.25)}
.badge-optional{background:rgba(251,191,36,.1);color:#fde68a;border:1px solid rgba(251,191,36,.2)}

/* ── FORM ─────────────────────────────────────────────────────── */
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}
.form-full{grid-column:1/-1}
.form-group{display:flex;flex-direction:column;gap:6px}
label{font-size:.85rem;font-weight:600;color:#94a3b8}
label .hint{font-weight:400;color:#475569;font-size:.8rem;margin-left:6px}
input,select{
  background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.12);
  border-radius:10px;padding:11px 14px;color:#e2e8f0;font-size:.9rem;
  outline:none;transition:border-color .2s,box-shadow .2s;width:100%;
}
input::placeholder{color:#334155}
input:focus,select:focus{border-color:#3b82f6;box-shadow:0 0 0 3px rgba(59,130,246,.18)}
select option{background:#1e293b;color:#e2e8f0}
.input-wrap{position:relative}
.input-wrap input{padding-right:44px}
.toggle-pass{
  position:absolute;right:13px;top:50%;transform:translateY(-50%);
  background:none;border:none;cursor:pointer;color:#64748b;font-size:1rem;
  padding:4px;transition:color .2s;
}
.toggle-pass:hover{color:#93c5fd}

/* ── PASSWORD STRENGTH ────────────────────────────────────────── */
.strength-bar{height:4px;border-radius:4px;margin-top:6px;background:rgba(255,255,255,.08);overflow:hidden}
.strength-fill{height:100%;width:0;border-radius:4px;transition:width .3s,background .3s}
.strength-text{font-size:.75rem;margin-top:4px;color:#64748b}

/* ── DIVIDER ──────────────────────────────────────────────────── */
.section-divider{
  display:flex;align-items:center;gap:12px;
  color:#334155;font-size:.78rem;font-weight:600;
  text-transform:uppercase;letter-spacing:.08em;
  margin:24px 0 18px;
}
.section-divider::before,.section-divider::after{content:'';flex:1;height:1px;background:rgba(255,255,255,.07)}

/* ── BUTTONS ──────────────────────────────────────────────────── */
.btn-row{display:flex;gap:12px;margin-top:28px;justify-content:flex-end}
.btn{
  padding:12px 28px;border-radius:12px;font-size:.9rem;
  font-weight:600;cursor:pointer;border:none;
  text-decoration:none;display:inline-flex;align-items:center;gap:8px;
  transition:all .2s;
}
.btn-primary{background:linear-gradient(135deg,#3b82f6,#6366f1);color:#fff;box-shadow:0 4px 16px rgba(59,130,246,.3)}
.btn-primary:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(59,130,246,.4)}
.btn-primary:active{transform:translateY(0)}
.btn-primary:disabled{opacity:.5;cursor:not-allowed;transform:none;box-shadow:none}
.btn-ghost{background:rgba(255,255,255,.06);color:#94a3b8;border:1px solid rgba(255,255,255,.1)}
.btn-ghost:hover{background:rgba(255,255,255,.1);color:#e2e8f0}

/* ── INSTALL LOG ──────────────────────────────────────────────── */
.install-log{
  background:rgba(0,0,0,.25);border:1px solid rgba(255,255,255,.08);
  border-radius:12px;padding:18px;display:flex;flex-direction:column;gap:10px;margin-bottom:24px;
}
.log-item{display:flex;align-items:flex-start;gap:12px;font-size:.88rem}
.log-icon{font-size:1.05rem;flex-shrink:0;margin-top:1px}
.log-msg{color:#cbd5e1;line-height:1.5}
.log-ok{color:#86efac}
.log-fail{color:#fca5a5}
.log-msg code{background:rgba(255,255,255,.08);padding:1px 6px;border-radius:4px;font-size:.82rem}
.log-msg strong{color:#e2e8f0}

/* ── SUCCESS STATE ────────────────────────────────────────────── */
.success-hero{text-align:center;padding:20px 0 28px}
.hero-icon{
  width:80px;height:80px;margin:0 auto 20px;
  background:linear-gradient(135deg,#22c55e,#16a34a);
  border-radius:50%;display:flex;align-items:center;justify-content:center;
  font-size:36px;box-shadow:0 8px 32px rgba(34,197,94,.3);
}
.success-hero h2{font-size:1.6rem;color:#f1f5f9;margin-bottom:8px}
.success-hero p{color:#64748b;font-size:.95rem;max-width:460px;margin:0 auto}

/* ── CREDENTIALS BOX ──────────────────────────────────────────── */
.creds-box{background:rgba(59,130,246,.08);border:1px solid rgba(59,130,246,.2);border-radius:14px;padding:20px 24px;margin:24px 0}
.creds-box h4{color:#93c5fd;font-size:.85rem;text-transform:uppercase;letter-spacing:.08em;margin-bottom:14px}
.cred-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:10px}
.cred-label{font-size:.85rem;color:#64748b}
.cred-val{font-size:.9rem;color:#e2e8f0;font-weight:600;font-family:monospace;background:rgba(255,255,255,.07);padding:3px 10px;border-radius:6px}
.links-row{display:flex;gap:12px;justify-content:center;flex-wrap:wrap;margin-top:10px}

/* ── WARNING BOX ──────────────────────────────────────────────── */
.warning-box{
  background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.25);
  border-radius:12px;padding:16px 20px;
  color:#fca5a5;font-size:.88rem;line-height:1.6;margin-top:20px;
}
.warning-box strong{display:block;margin-bottom:6px;font-size:.9rem}
.warning-box code{background:rgba(0,0,0,.3);padding:1px 6px;border-radius:4px}

/* ── REVIEW GRID ──────────────────────────────────────────────── */
.review-grid{display:flex;flex-direction:column;gap:8px;margin-bottom:24px}
.review-row{
  display:flex;gap:16px;background:rgba(255,255,255,.04);
  border:1px solid rgba(255,255,255,.07);border-radius:10px;padding:12px 16px;font-size:.87rem;
}
.review-key{color:#64748b;min-width:160px;flex-shrink:0}
.review-val{color:#e2e8f0;font-weight:500;word-break:break-all}

/* ── RESPONSIVE ───────────────────────────────────────────────── */
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
  <div class="logo-icon">&#x1F3EB;</div>
  <h1>Wizard Instalasi Website SMK</h1>
  <p>Setup otomatis konfigurasi database, admin, dan aplikasi</p>
</div>

<!-- PROGRESS BAR -->
<div class="progress-wrap">
  <div class="steps">
<?php
$stepLabels = array('Persyaratan', 'Database', 'Admin', 'Selesai');
for ($i = 1; $i <= 4; $i++):
    $cls = ($i < $step) ? 'done' : (($i === $step) ? 'active' : '');
?>
    <div class="step-item <?php echo $cls; ?>">
      <div class="step-circle"><?php echo ($i < $step) ? '&#x2713;' : $i; ?></div>
      <div class="step-label"><?php echo $stepLabels[$i - 1]; ?></div>
    </div>
<?php endfor; ?>
  </div>
</div>

<!-- CARD -->
<div class="card">


<?php /* ════════════════════════════════════════════════════════
   STEP 1 — PERSYARATAN SISTEM
   ════════════════════════════════════════════════════════ */ ?>
<?php if ($step === 1): ?>
<div class="card-header">
  <h2>&#x1F50D; Cek Persyaratan Sistem</h2>
  <p>Memastikan server Anda memenuhi persyaratan minimum untuk menjalankan website.</p>
</div>
<div class="card-body">

  <?php if (!$allRequiredOk): ?>
  <div class="alert alert-error">
    <span class="alert-icon">&#x26A0;&#xFE0F;</span>
    <div>Beberapa persyaratan <strong>wajib</strong> belum terpenuhi. Perbaiki terlebih dahulu sebelum melanjutkan.</div>
  </div>
  <?php else: ?>
  <div class="alert alert-success">
    <span class="alert-icon">&#x2705;</span>
    <div>Semua persyaratan wajib terpenuhi! Anda dapat melanjutkan ke langkah berikutnya.</div>
  </div>
  <?php endif; ?>

  <div class="req-grid">
    <?php foreach ($reqChecks as $c):
      $cls  = $c['ok'] ? 'ok' : ($c['required'] ? 'fail' : 'warn');
      $icon = $c['ok'] ? '&#x2705;' : ($c['required'] ? '&#x274C;' : '&#x26A0;&#xFE0F;');
    ?>
    <div class="req-item <?php echo $cls; ?>">
      <div class="req-status"><?php echo $icon; ?></div>
      <div>
        <div class="req-label"><?php echo e($c['label']); ?></div>
        <div class="req-detail"><?php echo e($c['detail']); ?></div>
      </div>
      <div class="req-badge <?php echo $c['required'] ? 'badge-required' : 'badge-optional'; ?>">
        <?php echo $c['required'] ? 'Wajib' : 'Opsional'; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <div class="btn-row">
    <?php if ($allRequiredOk): ?>
    <a href="install.php?step=2" class="btn btn-primary">Lanjutkan &#x27A1;</a>
    <?php else: ?>
    <a href="install.php?step=1" class="btn btn-ghost">&#x1F504; Refresh</a>
    <button class="btn btn-primary" disabled title="Perbaiki persyaratan di atas dulu">Lanjutkan &#x27A1;</button>
    <?php endif; ?>
  </div>

</div>

<?php /* ════════════════════════════════════════════════════════
   STEP 2 — DATABASE & KONFIGURASI APLIKASI
   ════════════════════════════════════════════════════════ */ ?>
<?php elseif ($step === 2): ?>
<div class="card-header">
  <h2>&#x1F5C4;&#xFE0F; Konfigurasi Database &amp; Aplikasi</h2>
  <p>Masukkan detail koneksi database dan pengaturan dasar aplikasi.</p>
</div>
<div class="card-body">

  <?php if (!empty($errors)): ?>
  <div class="alert alert-error">
    <span class="alert-icon">&#x274C;</span>
    <div><strong>Terjadi kesalahan:</strong>
      <ul><?php foreach ($errors as $err): ?><li><?php echo e($err); ?></li><?php endforeach; ?></ul>
    </div>
  </div>
  <?php endif; ?>

  <form method="POST" action="install.php?step=2">

    <div class="section-divider">Koneksi Database</div>
    <div class="form-grid">

      <div class="form-group">
        <label for="db_host">Database Host</label>
        <input type="text" id="db_host" name="db_host" value="<?php echo e($dbVals['db_host']); ?>" placeholder="localhost" required>
      </div>

      <div class="form-group">
        <label for="db_port">Port</label>
        <input type="number" id="db_port" name="db_port" value="<?php echo e((string)$dbVals['db_port']); ?>" placeholder="3306" min="1" max="65535" required>
      </div>

      <div class="form-group">
        <label for="db_user">Database Username</label>
        <input type="text" id="db_user" name="db_user" value="<?php echo e($dbVals['db_user']); ?>" placeholder="root" required>
      </div>

      <div class="form-group">
        <label for="db_pass">Database Password <span class="hint">(kosongkan jika tidak ada)</span></label>
        <div class="input-wrap">
          <input type="password" id="db_pass" name="db_pass" value="<?php echo e($dbVals['db_pass']); ?>" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;">
          <button type="button" class="toggle-pass" onclick="togglePass('db_pass',this)">&#x1F441;</button>
        </div>
      </div>

      <div class="form-group form-full">
        <label for="db_name">Nama Database</label>
        <input type="text" id="db_name" name="db_name" value="<?php echo e($dbVals['db_name']); ?>" placeholder="websmk" required>
        <div style="font-size:.78rem;color:#475569;margin-top:4px">&#x2139;&#xFE0F; Database akan dibuat otomatis jika belum ada.</div>
      </div>

    </div>

    <div class="section-divider">Pengaturan Aplikasi</div>
    <div class="form-grid">

      <div class="form-group form-full">
        <label for="app_url">URL Aplikasi <span class="hint">tanpa trailing slash</span></label>
        <input type="text" id="app_url" name="app_url" value="<?php echo e($dbVals['app_url']); ?>" placeholder="http://localhost/webpertamaku" required>
        <div style="font-size:.78rem;color:#475569;margin-top:4px">Contoh: <code style="background:rgba(255,255,255,.07);padding:1px 5px;border-radius:4px">http://localhost/webpertamaku</code></div>
      </div>

      <div class="form-group">
        <label for="app_base">Base Path <span class="hint">kosong jika root domain</span></label>
        <input type="text" id="app_base" name="app_base" value="<?php echo e($dbVals['app_base']); ?>" placeholder="/webpertamaku">
        <div style="font-size:.78rem;color:#475569;margin-top:4px">Contoh: <code style="background:rgba(255,255,255,.07);padding:1px 5px;border-radius:4px">/webpertamaku</code></div>
      </div>

      <div class="form-group">
        <label for="timezone">Zona Waktu</label>
        <select id="timezone" name="timezone">
          <option value="Asia/Jakarta"<?php echo sel($dbVals['timezone'], 'Asia/Jakarta'); ?>>Asia/Jakarta (WIB, UTC+7)</option>
          <option value="Asia/Makassar"<?php echo sel($dbVals['timezone'], 'Asia/Makassar'); ?>>Asia/Makassar (WITA, UTC+8)</option>
          <option value="Asia/Jayapura"<?php echo sel($dbVals['timezone'], 'Asia/Jayapura'); ?>>Asia/Jayapura (WIT, UTC+9)</option>
          <option value="UTC"<?php echo sel($dbVals['timezone'], 'UTC'); ?>>UTC</option>
        </select>
      </div>

    </div>

    <div class="btn-row">
      <a href="install.php?step=1" class="btn btn-ghost">&#x2190; Kembali</a>
      <button type="submit" class="btn btn-primary">Uji &amp; Lanjutkan &#x27A1;</button>
    </div>

  </form>
</div>

<?php /* ════════════════════════════════════════════════════════
   STEP 3 — AKUN ADMINISTRATOR
   ════════════════════════════════════════════════════════ */ ?>
<?php elseif ($step === 3): ?>
<div class="card-header">
  <h2>&#x1F464; Akun Administrator</h2>
  <p>Buat akun superadmin untuk mengelola website. Simpan kredensial ini dengan aman!</p>
</div>
<div class="card-body">

  <?php if (!empty($errors)): ?>
  <div class="alert alert-error">
    <span class="alert-icon">&#x274C;</span>
    <div><strong>Periksa isian berikut:</strong>
      <ul><?php foreach ($errors as $err): ?><li><?php echo e($err); ?></li><?php endforeach; ?></ul>
    </div>
  </div>
  <?php endif; ?>

  <form method="POST" action="install.php?step=3">
    <div class="form-grid">

      <div class="form-group form-full">
        <label for="admin_name">Nama Lengkap Admin</label>
        <input type="text" id="admin_name" name="admin_name" value="<?php echo e($admVals['admin_name']); ?>" placeholder="Super Admin" required>
      </div>

      <div class="form-group">
        <label for="admin_user">Username <span class="hint">huruf, angka, underscore</span></label>
        <input type="text" id="admin_user" name="admin_user"
          value="<?php echo e($admVals['admin_user']); ?>"
          placeholder="admin"
          pattern="[a-zA-Z0-9_]+"
          title="Hanya huruf, angka, dan underscore"
          required>
        <div id="user-hint" style="font-size:.78rem;margin-top:4px;color:#475569">Hanya huruf, angka, dan underscore (a-z, 0-9, _)</div>
      </div>

      <div class="form-group">
        <label for="admin_email">Email Admin</label>
        <input type="email" id="admin_email" name="admin_email" value="<?php echo e($admVals['admin_email']); ?>" placeholder="admin@sekolah.sch.id" required>
      </div>

      <div class="form-group">
        <label for="admin_pass">Password <span class="hint">min. 6 karakter</span></label>
        <div class="input-wrap">
          <input type="password" id="admin_pass" name="admin_pass" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;" minlength="6" required oninput="checkStrength(this.value)">
          <button type="button" class="toggle-pass" onclick="togglePass('admin_pass',this)">&#x1F441;</button>
        </div>
        <div class="strength-bar"><div class="strength-fill" id="strength-fill"></div></div>
        <div class="strength-text" id="strength-text"></div>
      </div>

      <div class="form-group">
        <label for="admin_pass2">Konfirmasi Password</label>
        <div class="input-wrap">
          <input type="password" id="admin_pass2" name="admin_pass2" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;" required oninput="checkMatch()">
          <button type="button" class="toggle-pass" onclick="togglePass('admin_pass2',this)">&#x1F441;</button>
        </div>
        <div id="match-msg" style="font-size:.78rem;margin-top:4px">&nbsp;</div>
      </div>

    </div>

    <div class="alert alert-warning" style="margin-top:20px">
      <span class="alert-icon">&#x1F4A1;</span>
      <div>Simpan username dan password ini di tempat yang aman. Password <strong>tidak dapat dipulihkan</strong> tanpa akses ke database.</div>
    </div>

    <div class="btn-row">
      <a href="install.php?step=2" class="btn btn-ghost">&#x2190; Kembali</a>
      <button type="submit" class="btn btn-primary">Lanjutkan &#x27A1;</button>
    </div>
  </form>

</div>

<?php /* ════════════════════════════════════════════════════════
   STEP 4 — INSTALASI & SELESAI
   ════════════════════════════════════════════════════════ */ ?>
<?php elseif ($step === 4): ?>

<?php if ($installDone && !empty($installResults)): ?>
<!-- SUCCESS STATE -->
<div class="card-header">
  <h2>&#x1F389; Instalasi Berhasil!</h2>
  <p>Website SMK Pertamaku berhasil diinstal dan siap digunakan.</p>
</div>
<div class="card-body">

  <div class="install-log">
    <?php foreach ($installResults as $r): ?>
    <div class="log-item">
      <span class="log-icon"><?php echo $r['ok'] ? '&#x2705;' : '&#x26A0;&#xFE0F;'; ?></span>
      <span class="log-msg <?php echo $r['ok'] ? 'log-ok' : 'log-fail'; ?>"><?php echo $r['msg']; ?></span>
    </div>
    <?php endforeach; ?>
  </div>

  <div class="success-hero">
    <div class="hero-icon">&#x1F38A;</div>
    <h2>Website Siap Digunakan!</h2>
    <p>Semua komponen berhasil dikonfigurasi. Kunjungi website atau masuk ke panel admin untuk memulai.</p>
  </div>

  <?php
  $finalUrl   = rtrim(isset($_SESSION['install_db']['app_url']) ? $_SESSION['install_db']['app_url'] : '', '/');
  $adminUser  = isset($_SESSION['install_admin']['admin_user'])  ? $_SESSION['install_admin']['admin_user']  : 'admin';
  $adminName  = isset($_SESSION['install_admin']['admin_name'])  ? $_SESSION['install_admin']['admin_name']  : 'Super Admin';
  $adminEmail = isset($_SESSION['install_admin']['admin_email']) ? $_SESSION['install_admin']['admin_email'] : '';
  ?>

  <div class="creds-box">
    <h4>&#x1F4CB; Kredensial Login Admin</h4>
    <div class="cred-row">
      <span class="cred-label">URL Admin</span>
      <span class="cred-val"><?php echo e($finalUrl); ?>/admin/login</span>
    </div>
    <div class="cred-row">
      <span class="cred-label">Nama</span>
      <span class="cred-val"><?php echo e($adminName); ?></span>
    </div>
    <div class="cred-row">
      <span class="cred-label">Username</span>
      <span class="cred-val"><?php echo e($adminUser); ?></span>
    </div>
    <div class="cred-row">
      <span class="cred-label">Email</span>
      <span class="cred-val"><?php echo e($adminEmail); ?></span>
    </div>
    <div class="cred-row">
      <span class="cred-label">Password</span>
      <span class="cred-val">&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull; <span style="font-size:.75rem;color:#475569">(seperti yang Anda masukkan)</span></span>
    </div>
  </div>

  <div class="links-row">
    <?php if ($finalUrl): ?>
    <a href="<?php echo e($finalUrl); ?>/" class="btn btn-ghost" target="_blank">&#x1F310; Buka Website</a>
    <a href="<?php echo e($finalUrl); ?>/admin/login" class="btn btn-primary" target="_blank">&#x2699;&#xFE0F; Masuk ke Admin Panel</a>
    <?php endif; ?>
  </div>

  <div class="warning-box">
    <strong>&#x26A0;&#xFE0F; Penting &mdash; Hapus File Installer!</strong>
    Demi keamanan, <strong>segera hapus</strong> file installer setelah instalasi selesai.
    Akses ke installer oleh orang lain dapat membahayakan website Anda.<br><br>
    File yang perlu dihapus:<br>
    <code><?php echo e(__FILE__); ?></code>
  </div>

  <?php
  unset($_SESSION['install_db'], $_SESSION['install_admin']);
  ?>
</div>

<?php elseif (!empty($installResults) && !$installDone): ?>
<!-- FAILED INSTALL STATE -->
<div class="card-header">
  <h2>&#x274C; Instalasi Gagal</h2>
  <p>Terjadi kesalahan saat proses instalasi. Periksa log di bawah ini.</p>
</div>
<div class="card-body">
  <div class="install-log">
    <?php foreach ($installResults as $r): ?>
    <div class="log-item">
      <span class="log-icon"><?php echo $r['ok'] ? '&#x2705;' : '&#x274C;'; ?></span>
      <span class="log-msg <?php echo $r['ok'] ? 'log-ok' : 'log-fail'; ?>"><?php echo $r['msg']; ?></span>
    </div>
    <?php endforeach; ?>
  </div>
  <div class="alert alert-error">
    <span class="alert-icon">&#x2139;&#xFE0F;</span>
    <div>Periksa konfigurasi database dan permission folder, lalu coba lagi.</div>
  </div>
  <div class="btn-row">
    <a href="install.php?step=2" class="btn btn-ghost">&#x2190; Ubah Konfigurasi</a>
    <form method="POST" action="install.php?step=4" style="margin:0">
      <button type="submit" name="action" value="install" class="btn btn-primary">&#x1F504; Coba Lagi</button>
    </form>
  </div>
</div>

<?php else: ?>
<!-- REVIEW & CONFIRM STATE -->
<div class="card-header">
  <h2>&#x1F680; Konfirmasi &amp; Mulai Instalasi</h2>
  <p>Periksa ringkasan konfigurasi Anda sebelum memulai proses instalasi.</p>
</div>
<div class="card-body">

  <?php if (empty($_SESSION['install_db']) || empty($_SESSION['install_admin'])): ?>
  <div class="alert alert-error">
    <span class="alert-icon">&#x26A0;&#xFE0F;</span>
    <div>Data sesi tidak lengkap. Silakan mulai dari langkah 1.
      <br><a href="install.php?step=1" style="color:#93c5fd">&#x2190; Mulai ulang</a>
    </div>
  </div>
  <?php else:
    $db  = $_SESSION['install_db'];
    $adm = $_SESSION['install_admin'];
  ?>

  <div class="section-divider">Konfigurasi Database</div>
  <div class="review-grid">
    <div class="review-row"><span class="review-key">Host : Port</span><span class="review-val"><?php echo e($db['db_host']); ?> : <?php echo e((string)$db['db_port']); ?></span></div>
    <div class="review-row"><span class="review-key">Username DB</span><span class="review-val"><?php echo e($db['db_user']); ?></span></div>
    <div class="review-row"><span class="review-key">Nama Database</span><span class="review-val"><?php echo e($db['db_name']); ?></span></div>
    <div class="review-row"><span class="review-key">URL Aplikasi</span><span class="review-val"><?php echo e($db['app_url']); ?></span></div>
    <div class="review-row"><span class="review-key">Base Path</span><span class="review-val"><?php echo e($db['app_base'] ? $db['app_base'] : '(root)'); ?></span></div>
    <div class="review-row"><span class="review-key">Zona Waktu</span><span class="review-val"><?php echo e($db['timezone']); ?></span></div>
  </div>

  <div class="section-divider">Akun Administrator</div>
  <div class="review-grid">
    <div class="review-row"><span class="review-key">Nama Lengkap</span><span class="review-val"><?php echo e($adm['admin_name']); ?></span></div>
    <div class="review-row"><span class="review-key">Username</span><span class="review-val"><?php echo e($adm['admin_user']); ?></span></div>
    <div class="review-row"><span class="review-key">Email</span><span class="review-val"><?php echo e($adm['admin_email']); ?></span></div>
    <div class="review-row"><span class="review-key">Password</span><span class="review-val">&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;</span></div>
  </div>

  <div class="section-divider">Yang Akan Dilakukan</div>
  <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:24px;font-size:.88rem;color:#94a3b8">
    <div>&#x1F4E6; Membuat database <strong style="color:#e2e8f0"><?php echo e($db['db_name']); ?></strong> (jika belum ada)</div>
    <div>&#x1F5C3;&#xFE0F; Menjalankan skema tabel dari <code style="background:rgba(255,255,255,.07);padding:1px 5px;border-radius:4px">database/schema.sql</code></div>
    <div>&#x1F464; Membuat/memperbarui akun admin <strong style="color:#e2e8f0"><?php echo e($adm['admin_user']); ?></strong></div>
    <div>&#x2699;&#xFE0F; Menulis file konfigurasi <code style="background:rgba(255,255,255,.07);padding:1px 5px;border-radius:4px">config/env.php</code></div>
    <div>&#x1F512; Membuat file kunci <code style="background:rgba(255,255,255,.07);padding:1px 5px;border-radius:4px">install.lock</code></div>
  </div>

  <form method="POST" action="install.php?step=4">
    <div class="btn-row">
      <a href="install.php?step=3" class="btn btn-ghost">&#x2190; Kembali</a>
      <button type="submit" name="action" value="install" class="btn btn-primary" onclick="return startInstall(this)">
        &#x1F680; Mulai Instalasi
      </button>
    </div>
  </form>

  <?php endif; ?>
</div>

<?php endif; /* end step 4 sub-states */ ?>
<?php endif; /* end step switch */ ?>

</div><!-- .card -->

<div style="margin-top:24px;font-size:.78rem;color:#334155;text-align:center">
  SMK Pertamaku Installer &bull; PHP <?php echo PHP_VERSION; ?> &bull; <?php echo date('Y'); ?> &bull;
  <span style="color:#1e40af">Hapus file ini setelah instalasi selesai</span>
</div>

<!-- JAVASCRIPT -->
<script>
function togglePass(id, btn) {
    var inp = document.getElementById(id);
    if (!inp) return;
    inp.type = inp.type === 'password' ? 'text' : 'password';
    btn.textContent = inp.type === 'password' ? '\uD83D\uDC41' : '\uD83D\uDE48';
}

function checkStrength(val) {
    var fill = document.getElementById('strength-fill');
    var text = document.getElementById('strength-text');
    if (!fill) return;
    var score = 0;
    if (val.length >= 6) score++;
    if (val.length >= 10) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    var colors = ['#ef4444', '#ef4444', '#f97316', '#eab308', '#22c55e', '#16a34a'];
    var labels = ['', 'Sangat Lemah', 'Lemah', 'Sedang', 'Kuat', 'Sangat Kuat'];
    fill.style.width = (score * 20) + '%';
    fill.style.background = colors[score] || '#ef4444';
    if (text) text.textContent = val.length ? (labels[score] || '') : '';
}

function checkMatch() {
    var p1  = document.getElementById('admin_pass');
    var p2  = document.getElementById('admin_pass2');
    var msg = document.getElementById('match-msg');
    if (!p1 || !p2 || !msg) return;
    if (!p2.value) { msg.textContent = ''; return; }
    msg.textContent = p1.value === p2.value ? '\u2705 Password cocok' : '\u274C Tidak cocok';
    msg.style.color = p1.value === p2.value ? '#86efac' : '#fca5a5';
}

function startInstall(btn) {
    btn.disabled = true;
    btn.innerHTML = '\u23F3 Sedang menginstall, harap tunggu...';
    return true;
}
</script>

</body>
</html>

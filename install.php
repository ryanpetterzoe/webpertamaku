<?php
/**
 * SMK Installer - Versi Bersih & Sederhana
 * Compatible: PHP 7.0+, XAMPP Windows/Linux/Mac
 */
ob_start();
if (session_status() === PHP_SESSION_NONE) { session_start(); }
set_time_limit(300);
ini_set('max_execution_time', '300');
ini_set('display_errors', '0');
error_reporting(0);

// ── LOCK CHECK ─────────────────────────────────────────────────
if (file_exists(__DIR__ . '/install.lock')) {
    $au = '';
    if (file_exists(__DIR__ . '/config/env.php')) {
        $ec = file_get_contents(__DIR__ . '/config/env.php');
        if (preg_match("/define\('APP_URL',\s*'([^']+)'\)/", $ec, $m)) $au = $m[1];
    }
    ob_end_clean();
    ?><!DOCTYPE html><html lang="id"><head><meta charset="UTF-8">
<title>Installer Terkunci</title>
<style>*{margin:0;padding:0;box-sizing:border-box}
body{min-height:100vh;background:#0f172a;display:flex;align-items:center;
justify-content:center;font-family:sans-serif;color:#e2e8f0;padding:20px}
.card{background:#1e293b;border:1px solid #334155;border-radius:16px;padding:40px;
max-width:480px;width:100%;text-align:center}
h1{font-size:1.5rem;color:#f1f5f9;margin:16px 0 12px}
p{color:#94a3b8;line-height:1.7;margin-bottom:16px;font-size:.9rem}
.btn{display:inline-block;padding:11px 24px;border-radius:8px;background:#3b82f6;
color:#fff;text-decoration:none;font-weight:600;margin:6px}
.tip{background:rgba(251,191,36,.08);border:1px solid rgba(251,191,36,.2);
border-radius:8px;padding:12px;margin-top:16px;font-size:.82rem;color:#fde68a;text-align:left}
code{background:rgba(0,0,0,.4);padding:2px 6px;border-radius:4px;font-family:monospace}</style>
</head><body><div class="card">
<div style="font-size:48px">&#x1F512;</div>
<h1>Installer Sudah Terkunci</h1>
<p>Instalasi sudah selesai. Website siap digunakan.</p>
<?php if ($au): ?>
<a href="<?php echo htmlspecialchars($au); ?>/" class="btn">&#x1F310; Buka Website</a>
<a href="<?php echo htmlspecialchars($au); ?>/admin/login" class="btn" style="background:#475569">&#x2699; Admin</a>
<?php endif; ?>
<div class="tip"><strong>Ingin install ulang?</strong><br>
Hapus file: <code><?php echo htmlspecialchars(__DIR__.'/install.lock'); ?></code></div>
</div></body></html>
<?php
    exit;
}

// ── STEP ───────────────────────────────────────────────────────
$step = isset($_GET['step']) ? max(1, min(4, (int)$_GET['step'])) : 1;
$errors = array();

// ── POST STEP 2: DB Config ─────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 2) {
    $f = array(
        'db_host'  => isset($_POST['db_host'])  ? trim($_POST['db_host'])  : 'localhost',
        'db_port'  => isset($_POST['db_port'])  ? (int)$_POST['db_port']  : 3306,
        'db_user'  => isset($_POST['db_user'])  ? trim($_POST['db_user'])  : 'root',
        'db_pass'  => isset($_POST['db_pass'])  ? $_POST['db_pass']        : '',
        'db_name'  => isset($_POST['db_name'])  ? trim($_POST['db_name'])  : 'websmk',
        'app_url'  => isset($_POST['app_url'])  ? rtrim(trim($_POST['app_url']), '/') : '',
        'app_base' => isset($_POST['app_base']) ? trim($_POST['app_base']) : '',
        'timezone' => isset($_POST['timezone']) ? trim($_POST['timezone']) : 'Asia/Jakarta',
    );
    if (!in_array($f['timezone'], array('Asia/Jakarta','Asia/Makassar','Asia/Jayapura','UTC')))
        $f['timezone'] = 'Asia/Jakarta';
    if (empty($f['db_host'])) $errors[] = 'Host database wajib diisi.';
    if (empty($f['db_name'])) $errors[] = 'Nama database wajib diisi.';
    if (empty($f['app_url'])) $errors[] = 'URL Aplikasi wajib diisi.';
    if (empty($errors)) {
        $c = @mysqli_connect($f['db_host'].':'.$f['db_port'], $f['db_user'], $f['db_pass']);
        if (!$c) {
            $errors[] = 'Koneksi database gagal: '.mysqli_connect_error().' ('.mysqli_connect_errno().')';
        } else {
            mysqli_close($c);
            $_SESSION['smk_db'] = $f;
            ob_end_clean();
            header('Location: install.php?step=3');
            exit;
        }
    }
}

// ── POST STEP 3: Admin ─────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 3) {
    $a = array(
        'name'  => isset($_POST['admin_name'])  ? trim($_POST['admin_name'])  : '',
        'user'  => isset($_POST['admin_user'])  ? trim($_POST['admin_user'])  : '',
        'email' => isset($_POST['admin_email']) ? trim($_POST['admin_email']) : '',
        'pass'  => isset($_POST['admin_pass'])  ? $_POST['admin_pass']        : '',
        'pass2' => isset($_POST['admin_pass2']) ? $_POST['admin_pass2']       : '',
    );
    if (empty($a['name']))  $errors[] = 'Nama lengkap wajib diisi.';
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $a['user'])) $errors[] = 'Username: huruf, angka, underscore saja.';
    if (!filter_var($a['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Format email tidak valid.';
    if (strlen($a['pass']) < 6)  $errors[] = 'Password minimal 6 karakter.';
    if ($a['pass'] !== $a['pass2']) $errors[] = 'Konfirmasi password tidak cocok.';
    if (empty($errors)) {
        $_SESSION['smk_adm'] = $a;
        ob_end_clean();
        header('Location: install.php?step=4');
        exit;
    }
}

// ── POST STEP 4: INSTALL ────────────────────────────────────────
$results = array();
$done = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 4) {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    if ($action === 'install') {
        $db  = isset($_SESSION['smk_db'])  ? $_SESSION['smk_db']  : array();
        $adm = isset($_SESSION['smk_adm']) ? $_SESSION['smk_adm'] : array();
        if (empty($db) || empty($adm)) {
            $errors[] = 'Session hilang. Mulai dari langkah 1.';
        } else {
            set_time_limit(300);
            // 1. Koneksi
            $c = @mysqli_connect($db['db_host'].':'.$db['db_port'], $db['db_user'], $db['db_pass']);
            if (!$c) {
                $results[] = array('ok'=>false, 'msg'=>'Koneksi gagal: '.mysqli_connect_error());
            } else {
                $results[] = array('ok'=>true, 'msg'=>'Koneksi ke MySQL berhasil');
                // 2. Buat database
                $dn = $db['db_name'];
                $q = mysqli_query($c, "CREATE DATABASE IF NOT EXISTS `$dn` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $results[] = array('ok'=>(bool)$q, 'msg'=>$q ? "Database <b>$dn</b> berhasil dibuat/sudah ada" : 'Gagal buat DB: '.mysqli_error($c));
                // 3. Pilih database
                $sel = mysqli_select_db($c, $dn);
                $results[] = array('ok'=>$sel, 'msg'=>$sel ? "Database <b>$dn</b> dipilih" : 'Gagal pilih DB: '.mysqli_error($c));
                // 4. Jalankan schema.sql
                $sf = __DIR__.'/database/schema.sql';
                if (!file_exists($sf)) {
                    $results[] = array('ok'=>false, 'msg'=>'File database/schema.sql tidak ditemukan!');
                } else {
                    $sql = file_get_contents($sf);
                    $sql = preg_replace('/--[^\n]*/', '', $sql);
                    $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
                    $parts = explode(';', $sql);
                    $ig = array(1007,1050,1051,1061,1062);
                    $ok=true; $run=0; $fail=0; $lerr='';
                    foreach ($parts as $p) {
                        $p = trim($p);
                        if ($p === '') continue;
                        $run++;
                        if (!mysqli_query($c, $p)) {
                            $no = mysqli_errno($c);
                            $fail++;
                            if (!in_array($no, $ig)) { $ok=false; $lerr=mysqli_error($c); }
                        }
                    }
                    $results[] = array('ok'=>$ok, 'msg'=>"Schema SQL: $run statement dijalankan, $fail diabaikan".($lerr ? " | Error: $lerr" : ''));
                }
                // 5. Simpan admin
                $hp = password_hash($adm['pass'], PASSWORD_DEFAULT);
                $sn = mysqli_real_escape_string($c, $adm['name']);
                $su = mysqli_real_escape_string($c, $adm['user']);
                $se = mysqli_real_escape_string($c, $adm['email']);
                $sp = mysqli_real_escape_string($c, $hp);
                $qa = mysqli_query($c, "INSERT INTO `admins` (`name`,`username`,`email`,`password`,`role`) VALUES ('$sn','$su','$se','$sp','superadmin') ON DUPLICATE KEY UPDATE `name`=VALUES(`name`),`password`=VALUES(`password`),`email`=VALUES(`email`)");
                $results[] = array('ok'=>(bool)$qa, 'msg'=>$qa ? "Admin <b>$su</b> berhasil disimpan" : 'Gagal simpan admin: '.mysqli_error($c));
                mysqli_close($c);
                // 6. Tulis env.php
                $key = bin2hex(random_bytes(24));
                $env = "<?php\n"
                    ."define('DB_HOST',      '".addslashes($db['db_host'])."');\n"
                    ."define('DB_USER',      '".addslashes($db['db_user'])."');\n"
                    ."define('DB_PASS',      '".addslashes($db['db_pass'])."');\n"
                    ."define('DB_NAME',      '".addslashes($db['db_name'])."');\n"
                    ."define('DB_PORT',      ".(int)$db['db_port'].");\n"
                    ."define('DB_PREFIX',    '');\n"
                    ."define('APP_URL',      '".rtrim($db['app_url'],'/')."');\n"
                    ."define('APP_BASE',     '".$db['app_base']."');\n"
                    ."define('APP_KEY',      '$key');\n"
                    ."define('APP_TIMEZONE', '".$db['timezone']."');\n"
                    ."date_default_timezone_set(APP_TIMEZONE);\n"
                    ."define('APP_ENV',      'production');\n"
                    ."define('INSTALLER_LOCKED', false);\n";
                $w = file_put_contents(__DIR__.'/config/env.php', $env);
                $results[] = array('ok'=>$w!==false, 'msg'=>$w!==false ? 'File config/env.php berhasil ditulis' : 'GAGAL tulis env.php - pastikan folder config/ writable!');
                // Cek apakah ada yang gagal (non-kritis)
                $crit = false;
                foreach ($results as $r) { if (!$r['ok']) { $crit=true; break; } }
                $done = !$crit;
                // 7. Buat install.lock
                file_put_contents(__DIR__.'/install.lock', date('Y-m-d H:i:s')."\n");
                if ($done) $results[] = array('ok'=>true, 'msg'=>'install.lock dibuat - installer dikunci');
            }
        }
    }
}

// ── REQUIREMENTS ───────────────────────────────────────────────
function req_check() {
    return array(
        array('label'=>'PHP >= 7.0', 'detail'=>'PHP '.PHP_VERSION, 'ok'=>version_compare(PHP_VERSION,'7.0.0','>='), 'req'=>true),
        array('label'=>'Ekstensi MySQLi', 'detail'=>extension_loaded('mysqli')?'OK':'Aktifkan di php.ini', 'ok'=>extension_loaded('mysqli'), 'req'=>true),
        array('label'=>'config/ dapat ditulis', 'detail'=>is_writable(__DIR__.'/config/')?'OK':'chmod 775 config/', 'ok'=>is_writable(__DIR__.'/config/'), 'req'=>true),
        array('label'=>'uploads/ dapat ditulis', 'detail'=>is_writable(__DIR__.'/assets/images/uploads/')?'OK':'chmod 775 assets/images/uploads/', 'ok'=>is_writable(__DIR__.'/assets/images/uploads/'), 'req'=>true),
        array('label'=>'Ekstensi GD (opsional)', 'detail'=>extension_loaded('gd')?'OK':'Tidak tersedia', 'ok'=>extension_loaded('gd'), 'req'=>false),
    );
}
$reqs = ($step===1) ? req_check() : array();
$allOk = true;
foreach ($reqs as $r) { if ($r['req'] && !$r['ok']) { $allOk=false; break; } }

// ── Prefill ─────────────────────────────────────────────────────
$dbV = isset($_SESSION['smk_db']) ? $_SESSION['smk_db'] : array(
    'db_host'=>'localhost','db_port'=>3306,'db_user'=>'root','db_pass'=>'',
    'db_name'=>'websmk','app_url'=>'http://localhost/webpertamaku',
    'app_base'=>'/webpertamaku','timezone'=>'Asia/Jakarta'
);
$admV = isset($_SESSION['smk_adm']) ? $_SESSION['smk_adm'] : array(
    'name'=>'Super Admin','user'=>'admin','email'=>''
);
if (!empty($errors) && $step===2) {
    foreach (array('db_host','db_port','db_user','db_pass','db_name','app_url','app_base','timezone') as $k)
        if (isset($_POST[$k])) $dbV[$k] = $_POST[$k];
}
if (!empty($errors) && $step===3) {
    foreach (array('admin_name','admin_user','admin_email') as $k)
        if (isset($_POST[$k])) $admV[str_replace('admin_','',$k)] = $_POST[$k];
}

function esc($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function sel_($v,$c){ return $v===$c?' selected':''; }
?><!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Installer - SMK Pertamaku</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{min-height:100vh;background:linear-gradient(135deg,#0f172a,#1e293b);
  font-family:'Segoe UI',sans-serif;color:#e2e8f0;
  display:flex;flex-direction:column;align-items:center;padding:32px 16px 48px}
.hdr{text-align:center;margin-bottom:32px}
.hdr .ico{width:64px;height:64px;background:linear-gradient(135deg,#3b82f6,#6366f1);
  border-radius:16px;display:inline-flex;align-items:center;justify-content:center;
  font-size:28px;margin-bottom:12px;box-shadow:0 8px 24px rgba(59,130,246,.35)}
.hdr h1{font-size:1.6rem;font-weight:700;color:#f1f5f9}
.hdr p{color:#64748b;margin-top:6px;font-size:.9rem}
.prog{width:100%;max-width:640px;background:rgba(255,255,255,.05);
  border:1px solid rgba(255,255,255,.09);border-radius:12px;padding:20px 28px;margin-bottom:24px}
.steps{display:flex;align-items:center}
.si{display:flex;flex-direction:column;align-items:center;flex:1;position:relative}
.si:not(:last-child)::after{content:'';position:absolute;top:16px;left:50%;
  width:100%;height:2px;background:rgba(255,255,255,.1);z-index:0}
.si.done:not(:last-child)::after{background:#3b82f6}
.sc{width:32px;height:32px;border-radius:50%;display:flex;align-items:center;
  justify-content:center;font-size:.78rem;font-weight:700;z-index:1;
  border:2px solid rgba(255,255,255,.15);background:#1e293b;color:#64748b}
.si.done .sc{background:#3b82f6;border-color:#3b82f6;color:#fff}
.si.active .sc{background:linear-gradient(135deg,#3b82f6,#6366f1);color:#fff;
  border-color:#6366f1;box-shadow:0 0 0 3px rgba(99,102,241,.2)}
.sl{font-size:.68rem;color:#64748b;margin-top:6px;text-align:center;white-space:nowrap}
.si.active .sl{color:#93c5fd;font-weight:600}
.si.done .sl{color:#60a5fa}
.card{width:100%;max-width:640px;background:rgba(255,255,255,.06);
  backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);
  border:1px solid rgba(255,255,255,.1);border-radius:16px;overflow:hidden}
.ch{padding:24px 32px 18px;border-bottom:1px solid rgba(255,255,255,.07)}
.ch h2{font-size:1.1rem;color:#f1f5f9}
.ch p{color:#64748b;font-size:.85rem;margin-top:5px}
.cb{padding:24px 32px}
.alert{border-radius:10px;padding:12px 16px;margin-bottom:18px;
  display:flex;gap:10px;align-items:flex-start;font-size:.88rem}
.ae{background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.25);color:#fca5a5}
.as{background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.2);color:#86efac}
.aw{background:rgba(251,191,36,.08);border:1px solid rgba(251,191,36,.2);color:#fde68a}
.alert ul{margin:6px 0 0 16px}
.rg{display:flex;flex-direction:column;gap:8px}
.ri{display:flex;align-items:flex-start;gap:12px;background:rgba(255,255,255,.04);
  border:1px solid rgba(255,255,255,.07);border-radius:10px;padding:12px 16px}
.ri.ok{border-color:rgba(34,197,94,.2)}.ri.fail{border-color:rgba(239,68,68,.25)}
.ri.warn{border-color:rgba(251,191,36,.2)}
.rs{font-size:1.1rem;flex-shrink:0}.rl{font-weight:600;color:#e2e8f0;font-size:.88rem}
.rd{font-size:.8rem;color:#64748b;margin-top:2px}
.rb{margin-left:auto;font-size:.68rem;padding:2px 8px;border-radius:16px;font-weight:600}
.br{background:rgba(239,68,68,.12);color:#fca5a5;border:1px solid rgba(239,68,68,.2)}
.bo{background:rgba(251,191,36,.1);color:#fde68a;border:1px solid rgba(251,191,36,.15)}
.fg{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.ff{grid-column:1/-1}
.fgp{display:flex;flex-direction:column;gap:5px}
label{font-size:.82rem;font-weight:600;color:#94a3b8}
input,select{background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.12);
  border-radius:8px;padding:10px 13px;color:#e2e8f0;font-size:.88rem;
  width:100%;outline:none;transition:border .2s}
input:focus,select:focus{border-color:#3b82f6;box-shadow:0 0 0 3px rgba(59,130,246,.15)}
input::placeholder{color:#334155}
select option{background:#1e293b}
.iw{position:relative}
.iw input{padding-right:40px}
.tp{position:absolute;right:10px;top:50%;transform:translateY(-50%);
  background:none;border:none;cursor:pointer;color:#64748b;font-size:.95rem;padding:4px}
.div{display:flex;align-items:center;gap:10px;color:#334155;font-size:.75rem;
  font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin:20px 0 14px}
.div::before,.div::after{content:'';flex:1;height:1px;background:rgba(255,255,255,.07)}
.br-row{display:flex;gap:10px;margin-top:24px;justify-content:flex-end}
.btn{padding:11px 24px;border-radius:10px;font-size:.88rem;font-weight:600;
  cursor:pointer;border:none;text-decoration:none;
  display:inline-flex;align-items:center;gap:7px;transition:all .2s}
.bp{background:linear-gradient(135deg,#3b82f6,#6366f1);color:#fff;
  box-shadow:0 4px 14px rgba(59,130,246,.28)}
.bp:hover{transform:translateY(-1px);box-shadow:0 6px 18px rgba(59,130,246,.38)}
.bp:disabled{opacity:.5;cursor:not-allowed;transform:none;box-shadow:none}
.bg{background:rgba(255,255,255,.06);color:#94a3b8;border:1px solid rgba(255,255,255,.1)}
.bg:hover{background:rgba(255,255,255,.1);color:#e2e8f0}
.lg{background:rgba(0,0,0,.22);border:1px solid rgba(255,255,255,.07);
  border-radius:10px;padding:16px;display:flex;flex-direction:column;gap:8px;margin-bottom:20px}
.li{display:flex;gap:10px;align-items:flex-start;font-size:.86rem}
.lok{color:#86efac}.lfl{color:#fca5a5}
.lm code{background:rgba(255,255,255,.08);padding:1px 5px;border-radius:3px;font-size:.8rem}
.sh{text-align:center;padding:16px 0 24px}
.shi{width:72px;height:72px;margin:0 auto 16px;background:linear-gradient(135deg,#22c55e,#16a34a);
  border-radius:50%;display:flex;align-items:center;justify-content:center;
  font-size:32px;box-shadow:0 6px 24px rgba(34,197,94,.28)}
.sh h2{font-size:1.5rem;color:#f1f5f9;margin-bottom:8px}
.sh p{color:#64748b;font-size:.9rem;max-width:400px;margin:0 auto}
.cb-box{background:rgba(59,130,246,.08);border:1px solid rgba(59,130,246,.18);
  border-radius:12px;padding:18px 22px;margin:20px 0}
.cb-box h4{color:#93c5fd;font-size:.82rem;text-transform:uppercase;
  letter-spacing:.07em;margin-bottom:12px}
.cr{display:flex;justify-content:space-between;align-items:center;margin-bottom:8px}
.ck{font-size:.83rem;color:#64748b}
.cv{font-size:.88rem;color:#e2e8f0;font-weight:600;font-family:monospace;
  background:rgba(255,255,255,.07);padding:2px 8px;border-radius:5px}
.lr{display:flex;gap:10px;justify-content:center;flex-wrap:wrap;margin-top:10px}
.wb{background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);
  border-radius:10px;padding:14px 18px;color:#fca5a5;font-size:.85rem;
  line-height:1.6;margin-top:18px}
.wb strong{display:block;margin-bottom:5px}
.wb code{background:rgba(0,0,0,.3);padding:1px 5px;border-radius:3px}
.rg2{display:flex;flex-direction:column;gap:7px;margin-bottom:20px}
.rr{display:flex;gap:14px;background:rgba(255,255,255,.04);
  border:1px solid rgba(255,255,255,.07);border-radius:8px;padding:10px 14px;font-size:.85rem}
.rk{color:#64748b;min-width:140px;flex-shrink:0}
.rv{color:#e2e8f0;font-weight:500;word-break:break-all}
.sb{height:4px;border-radius:3px;margin-top:5px;background:rgba(255,255,255,.07);overflow:hidden}
.sf{height:100%;width:0;border-radius:3px;transition:width .3s,background .3s}
.st{font-size:.73rem;margin-top:3px;color:#64748b}
@media(max-width:560px){.fg{grid-template-columns:1fr}.ff{grid-column:1}
  .cb{padding:20px 20px}.ch{padding:20px 20px 14px}
  .br-row{flex-direction:column-reverse}.btn{justify-content:center}
  .rr{flex-direction:column;gap:3px}.rk{min-width:auto}}
</style>
</head>
<body>

<div class="hdr">
  <div class="ico">&#x1F3EB;</div>
  <h1>Wizard Instalasi Website SMK</h1>
  <p>Setup otomatis: database, admin, konfigurasi</p>
</div>

<div class="prog"><div class="steps">
<?php
$slab = array('Persyaratan','Database','Admin','Selesai');
for ($i=1;$i<=4;$i++){
  $c=($i<$step)?'done':(($i===$step)?'active':'');
  echo '<div class="si '.$c.'"><div class="sc">'.($i<$step?'&#x2713;':$i).'</div>';
  echo '<div class="sl">'.$slab[$i-1].'</div></div>';
}
?>
</div></div>

<div class="card">
<?php if ($step===1): ?>
<div class="ch"><h2>&#x1F50D; Cek Persyaratan Sistem</h2>
<p>Memastikan server memenuhi persyaratan minimum.</p></div>
<div class="cb">
  <?php if ($allOk): ?>
  <div class="alert as"><span>&#x2705;</span><div>Semua persyaratan terpenuhi! Lanjutkan ke langkah berikutnya.</div></div>
  <?php else: ?>
  <div class="alert ae"><span>&#x26A0;</span><div>Beberapa persyaratan <strong>wajib</strong> belum terpenuhi.</div></div>
  <?php endif; ?>
  <div class="rg">
  <?php foreach ($reqs as $r):
    $cls=$r['ok']?'ok':($r['req']?'fail':'warn');
    $ico=$r['ok']?'&#x2705;':($r['req']?'&#x274C;':'&#x26A0;');
  ?>
  <div class="ri <?php echo $cls; ?>">
    <div class="rs"><?php echo $ico; ?></div>
    <div><div class="rl"><?php echo esc($r['label']); ?></div>
    <div class="rd"><?php echo esc($r['detail']); ?></div></div>
    <div class="rb <?php echo $r['req']?'br':'bo'; ?>"><?php echo $r['req']?'Wajib':'Opsional'; ?></div>
  </div>
  <?php endforeach; ?>
  </div>
  <div class="br-row">
    <a href="install.php?step=1" class="btn bg">&#x1F504; Refresh</a>
    <?php if ($allOk): ?>
    <a href="install.php?step=2" class="btn bp">Lanjutkan &#x27A1;</a>
    <?php else: ?>
    <button class="btn bp" disabled>Lanjutkan &#x27A1;</button>
    <?php endif; ?>
  </div>
</div>

<?php elseif ($step===2): ?>
<div class="ch"><h2>&#x1F5C4; Konfigurasi Database &amp; Aplikasi</h2>
<p>Masukkan koneksi database dan URL aplikasi.</p></div>
<div class="cb">
  <?php if (!empty($errors)): ?>
  <div class="alert ae"><span>&#x274C;</span><div><ul>
  <?php foreach ($errors as $e): ?><li><?php echo esc($e); ?></li><?php endforeach; ?>
  </ul></div></div>
  <?php endif; ?>
  <form method="POST" action="install.php?step=2">
    <div class="div">Koneksi Database</div>
    <div class="fg">
      <div class="fgp"><label>Host Database</label>
        <input type="text" name="db_host" value="<?php echo esc($dbV['db_host']); ?>" placeholder="localhost" required></div>
      <div class="fgp"><label>Port</label>
        <input type="number" name="db_port" value="<?php echo esc((string)$dbV['db_port']); ?>" placeholder="3306" min="1" max="65535" required></div>
      <div class="fgp"><label>Username MySQL</label>
        <input type="text" name="db_user" value="<?php echo esc($dbV['db_user']); ?>" placeholder="root" required></div>
      <div class="fgp"><label>Password MySQL <small style="font-weight:400;color:#475569">(kosong = default XAMPP)</small></label>
        <div class="iw"><input type="password" id="dp" name="db_pass" value="<?php echo esc($dbV['db_pass']); ?>" placeholder="kosong untuk XAMPP default">
        <button type="button" class="tp" onclick="tp('dp',this)">&#x1F441;</button></div></div>
      <div class="fgp ff"><label>Nama Database <small style="font-weight:400;color:#475569">(akan dibuat otomatis)</small></label>
        <input type="text" name="db_name" value="<?php echo esc($dbV['db_name']); ?>" placeholder="websmk" required></div>
    </div>
    <div class="div">Pengaturan Aplikasi</div>
    <div class="fg">
      <div class="fgp ff"><label>URL Aplikasi <small style="font-weight:400;color:#475569">(tanpa / di akhir)</small></label>
        <input type="text" name="app_url" value="<?php echo esc($dbV['app_url']); ?>" placeholder="http://localhost/webpertamaku" required>
        <div style="font-size:.75rem;color:#475569;margin-top:3px">Hosting: https://namadomain.com | Lokal: http://localhost/webpertamaku</div></div>
      <div class="fgp"><label>Base Path <small style="font-weight:400;color:#475569">(kosong = root domain)</small></label>
        <input type="text" name="app_base" value="<?php echo esc($dbV['app_base']); ?>" placeholder="/webpertamaku">
        <div style="font-size:.75rem;color:#475569;margin-top:3px">Lokal: /webpertamaku | Hosting root: kosong</div></div>
      <div class="fgp"><label>Zona Waktu</label>
        <select name="timezone">
          <option value="Asia/Jakarta"<?php echo sel_($dbV['timezone'],'Asia/Jakarta'); ?>>WIB - Asia/Jakarta</option>
          <option value="Asia/Makassar"<?php echo sel_($dbV['timezone'],'Asia/Makassar'); ?>>WITA - Asia/Makassar</option>
          <option value="Asia/Jayapura"<?php echo sel_($dbV['timezone'],'Asia/Jayapura'); ?>>WIT - Asia/Jayapura</option>
          <option value="UTC"<?php echo sel_($dbV['timezone'],'UTC'); ?>>UTC</option>
        </select></div>
    </div>
    <div class="br-row">
      <a href="install.php?step=1" class="btn bg">&#x2190; Kembali</a>
      <button type="submit" class="btn bp">Uji Koneksi &amp; Lanjutkan &#x27A1;</button>
    </div>
  </form>
</div>

<?php elseif ($step===3): ?>
<div class="ch"><h2>&#x1F464; Akun Administrator</h2>
<p>Buat akun untuk masuk ke panel admin. Simpan dengan aman!</p></div>
<div class="cb">
  <?php if (!empty($errors)): ?>
  <div class="alert ae"><span>&#x274C;</span><div><ul>
  <?php foreach ($errors as $e): ?><li><?php echo esc($e); ?></li><?php endforeach; ?>
  </ul></div></div>
  <?php endif; ?>
  <form method="POST" action="install.php?step=3">
    <div class="fg">
      <div class="fgp ff"><label>Nama Lengkap</label>
        <input type="text" name="admin_name" value="<?php echo esc($admV['name']); ?>" placeholder="Super Admin" required></div>
      <div class="fgp"><label>Username</label>
        <input type="text" name="admin_user" value="<?php echo esc($admV['user']); ?>" placeholder="admin" pattern="[a-zA-Z0-9_]+" title="Huruf, angka, underscore" required>
        <div style="font-size:.73rem;color:#475569;margin-top:3px">Hanya huruf, angka, underscore</div></div>
      <div class="fgp"><label>Email</label>
        <input type="email" name="admin_email" value="<?php echo esc($admV['email']); ?>" placeholder="admin@sekolah.sch.id" required></div>
      <div class="fgp"><label>Password <small style="font-weight:400;color:#475569">(min 6 karakter)</small></label>
        <div class="iw"><input type="password" id="ap" name="admin_pass" placeholder="&#x2022;&#x2022;&#x2022;&#x2022;&#x2022;&#x2022;&#x2022;&#x2022;" minlength="6" required oninput="ps(this.value)">
        <button type="button" class="tp" onclick="tp('ap',this)">&#x1F441;</button></div>
        <div class="sb"><div class="sf" id="sf"></div></div>
        <div class="st" id="st"></div></div>
      <div class="fgp"><label>Konfirmasi Password</label>
        <div class="iw"><input type="password" id="ap2" name="admin_pass2" placeholder="&#x2022;&#x2022;&#x2022;&#x2022;&#x2022;&#x2022;&#x2022;&#x2022;" required oninput="pm()">
        <button type="button" class="tp" onclick="tp('ap2',this)">&#x1F441;</button></div>
        <div id="pm" style="font-size:.73rem;margin-top:3px">&nbsp;</div></div>
    </div>
    <div class="alert aw" style="margin-top:16px"><span>&#x1F4A1;</span>
    <div>Simpan username dan password di tempat aman. Password tidak bisa dipulihkan tanpa akses database.</div></div>
    <div class="br-row">
      <a href="install.php?step=2" class="btn bg">&#x2190; Kembali</a>
      <button type="submit" class="btn bp">Lanjutkan &#x27A1;</button>
    </div>
  </form>
</div>
<?php elseif ($step===4): ?>

<?php if ($done && !empty($results)): ?>
<div class="ch"><h2>&#x1F389; Instalasi Berhasil!</h2><p>Website siap digunakan.</p></div>
<div class="cb">
  <div class="lg">
  <?php foreach ($results as $r): ?>
  <div class="li"><span class="<?php echo $r['ok']?'lok':'lfl'; ?>"><?php echo $r['ok']?'&#x2705;':'&#x26A0;'; ?></span>
  <span class="lm"><?php echo $r['msg']; ?></span></div>
  <?php endforeach; ?>
  </div>
  <div class="sh">
    <div class="shi">&#x1F38A;</div>
    <h2>Website Siap!</h2>
    <p>Semua komponen berhasil dikonfigurasi. Kunjungi website atau masuk ke panel admin.</p>
  </div>
  <?php
  $fu  = isset($_SESSION['smk_db']['app_url'])  ? rtrim($_SESSION['smk_db']['app_url'],'/') : '';
  $au  = isset($_SESSION['smk_adm']['user'])     ? $_SESSION['smk_adm']['user']  : 'admin';
  $an  = isset($_SESSION['smk_adm']['name'])     ? $_SESSION['smk_adm']['name']  : '';
  $ae  = isset($_SESSION['smk_adm']['email'])    ? $_SESSION['smk_adm']['email'] : '';
  ?>
  <div class="cb-box">
    <h4>&#x1F4CB; Kredensial Login Admin</h4>
    <div class="cr"><span class="ck">URL Admin</span><span class="cv"><?php echo esc($fu); ?>/admin/login</span></div>
    <div class="cr"><span class="ck">Username</span><span class="cv"><?php echo esc($au); ?></span></div>
    <div class="cr"><span class="ck">Email</span><span class="cv"><?php echo esc($ae); ?></span></div>
    <div class="cr"><span class="ck">Password</span><span class="cv">&#x2022;&#x2022;&#x2022;&#x2022;&#x2022;&#x2022;&#x2022;&#x2022; (seperti yang dimasukkan)</span></div>
  </div>
  <div class="lr">
    <?php if ($fu): ?>
    <a href="<?php echo esc($fu); ?>/" class="btn bg" target="_blank">&#x1F310; Buka Website</a>
    <a href="<?php echo esc($fu); ?>/admin/login" class="btn bp" target="_blank">&#x2699; Masuk Admin</a>
    <?php endif; ?>
  </div>
  <div class="wb">
    <strong>&#x26A0; PENTING: Hapus File Installer!</strong>
    Segera hapus file ini setelah instalasi:<br><code><?php echo esc(__FILE__); ?></code>
  </div>
  <?php unset($_SESSION['smk_db'],$_SESSION['smk_adm']); ?>
</div>

<?php elseif (!empty($results) && !$done): ?>
<div class="ch"><h2>&#x274C; Instalasi Gagal</h2><p>Terjadi kesalahan. Periksa log berikut.</p></div>
<div class="cb">
  <div class="lg">
  <?php foreach ($results as $r): ?>
  <div class="li"><span class="<?php echo $r['ok']?'lok':'lfl'; ?>"><?php echo $r['ok']?'&#x2705;':'&#x274C;'; ?></span>
  <span class="lm"><?php echo $r['msg']; ?></span></div>
  <?php endforeach; ?>
  </div>
  <div class="br-row">
    <a href="install.php?step=2" class="btn bg">&#x2190; Ubah Konfigurasi</a>
    <form method="POST" action="install.php?step=4" style="margin:0">
      <button type="submit" name="action" value="install" class="btn bp">&#x1F504; Coba Lagi</button>
    </form>
  </div>
</div>

<?php else: ?>
<div class="ch"><h2>&#x1F680; Konfirmasi &amp; Mulai Instalasi</h2>
<p>Periksa ringkasan sebelum memulai.</p></div>
<div class="cb">
  <?php if (!empty($errors)): ?>
  <div class="alert ae"><span>&#x26A0;</span><div><?php echo esc($errors[0]); ?>
  <br><a href="install.php?step=1" style="color:#93c5fd">&#x2190; Mulai Ulang</a></div></div>
  <?php elseif (isset($_SESSION['smk_db']) && isset($_SESSION['smk_adm'])): ?>
  <?php $db=$_SESSION['smk_db']; $adm=$_SESSION['smk_adm']; ?>
  <div class="div">Konfigurasi Database</div>
  <div class="rg2">
    <div class="rr"><span class="rk">Host : Port</span><span class="rv"><?php echo esc($db['db_host']); ?> : <?php echo esc((string)$db['db_port']); ?></span></div>
    <div class="rr"><span class="rk">Database</span><span class="rv"><?php echo esc($db['db_name']); ?></span></div>
    <div class="rr"><span class="rk">URL Aplikasi</span><span class="rv"><?php echo esc($db['app_url']); ?></span></div>
    <div class="rr"><span class="rk">Base Path</span><span class="rv"><?php echo esc($db['app_base']?$db['app_base']:'(root)'); ?></span></div>
  </div>
  <div class="div">Akun Admin</div>
  <div class="rg2">
    <div class="rr"><span class="rk">Username</span><span class="rv"><?php echo esc($adm['user']); ?></span></div>
    <div class="rr"><span class="rk">Email</span><span class="rv"><?php echo esc($adm['email']); ?></span></div>
  </div>
  <form method="POST" action="install.php?step=4">
    <div class="br-row">
      <a href="install.php?step=3" class="btn bg">&#x2190; Kembali</a>
      <button type="submit" name="action" value="install" class="btn bp" id="ibtn" onclick="return si(this)">
        &#x1F680; Mulai Instalasi
      </button>
    </div>
  </form>
  <?php else: ?>
  <div class="alert ae"><span>&#x26A0;</span><div>Session hilang.
  <a href="install.php?step=1" style="color:#93c5fd">Mulai ulang</a></div></div>
  <?php endif; ?>
</div>
<?php endif; ?>
<?php endif; ?>

</div>
<div style="margin-top:20px;font-size:.75rem;color:#334155;text-align:center">
  SMK Installer &bull; PHP <?php echo PHP_VERSION; ?> &bull;
  <span style="color:#1e40af">Hapus install.php setelah selesai</span>
</div>

<script>
function tp(id,btn){var i=document.getElementById(id);if(!i)return;
i.type=i.type==='password'?'text':'password';
btn.textContent=i.type==='password'?'\uD83D\uDC41':'\uD83D\uDE48';}
function ps(v){var f=document.getElementById('sf'),t=document.getElementById('st');
if(!f)return;var s=0;
if(v.length>=6)s++;if(v.length>=10)s++;
if(/[A-Z]/.test(v))s++;if(/[0-9]/.test(v))s++;if(/[^A-Za-z0-9]/.test(v))s++;
var c=['#ef4444','#ef4444','#f97316','#eab308','#22c55e','#16a34a'];
var l=['','Sangat Lemah','Lemah','Sedang','Kuat','Sangat Kuat'];
f.style.width=(s*20)+'%';f.style.background=c[s]||'#ef4444';
if(t)t.textContent=v.length?(l[s]||''):'';}
function pm(){var p=document.getElementById('ap'),p2=document.getElementById('ap2'),
m=document.getElementById('pm');if(!p||!p2||!m)return;
if(!p2.value){m.textContent='';return;}
m.textContent=p.value===p2.value?'\u2705 Cocok':'\u274C Tidak cocok';
m.style.color=p.value===p2.value?'#86efac':'#fca5a5';}
function si(btn){btn.disabled=true;
btn.innerHTML='\u23F3 Menginstall, harap tunggu...';return true;}
</script>
</body></html>

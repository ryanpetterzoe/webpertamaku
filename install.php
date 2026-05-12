<?php
/**
 * SMK Installer - ONE PAGE, ONE FORM, NO SESSION BETWEEN STEPS
 * Compatible: PHP 5.6+, XAMPP Windows/Linux/Mac
 *
 * Cara kerja: Semua dalam SATU halaman.
 * Kalau form belum diisi = tampilkan form.
 * Kalau form sudah diisi + install diklik = langsung install.
 * TIDAK ADA session antar step. TIDAK ADA redirect.
 */

// Buffer semua output, jadi tidak ada masalah "headers already sent"
ob_start();

error_reporting(E_ALL);
ini_set('display_errors', '1');
set_time_limit(300);

$installed = false;
$log       = array();
$error     = '';

// ── Cek apakah sudah diinstall ────────────────────────────────
if (file_exists(__DIR__ . '/install.lock')) {
    ob_end_clean();
    die('<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Sudah Terinstall</title>
<style>body{background:#0f172a;color:#e2e8f0;font-family:sans-serif;display:flex;
align-items:center;justify-content:center;height:100vh;margin:0}
.box{background:#1e293b;border-radius:12px;padding:40px;max-width:400px;text-align:center}
h2{color:#4ade80;margin-bottom:16px}p{color:#94a3b8;margin-bottom:20px}
a{color:#60a5fa}</style></head><body>
<div class="box"><h2>✅ Website Sudah Terinstall</h2>
<p>Installer sudah dikunci. Website siap digunakan.</p>
<p>Untuk install ulang, hapus file <code>install.lock</code> di folder website.</p>
</div></body></html>');
}

// ── Proses instalasi kalau form di-submit ─────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['do_install'])) {

    $db_host  = trim(isset($_POST['db_host'])  ? $_POST['db_host']  : 'localhost');
    $db_port  = (int)(isset($_POST['db_port']) ? $_POST['db_port']  : 3306);
    $db_user  = trim(isset($_POST['db_user'])  ? $_POST['db_user']  : 'root');
    $db_pass  = isset($_POST['db_pass'])        ? $_POST['db_pass']  : '';
    $db_name  = trim(isset($_POST['db_name'])  ? $_POST['db_name']  : 'websmk');
    $adm_name = trim(isset($_POST['adm_name']) ? $_POST['adm_name'] : '');
    $adm_user = trim(isset($_POST['adm_user']) ? $_POST['adm_user'] : '');
    $adm_mail = trim(isset($_POST['adm_mail']) ? $_POST['adm_mail'] : '');
    $adm_pass = isset($_POST['adm_pass'])       ? $_POST['adm_pass'] : '';
    $timezone = isset($_POST['timezone'])       ? $_POST['timezone'] : 'Asia/Jakarta';

    // Validasi dasar
    if (empty($db_host))  $error = 'Host database wajib diisi.';
    elseif (empty($db_name)) $error = 'Nama database wajib diisi.';
    elseif (empty($adm_name)) $error = 'Nama admin wajib diisi.';
    elseif (empty($adm_user) || !preg_match('/^[a-zA-Z0-9_]+$/', $adm_user)) $error = 'Username admin tidak valid.';
    elseif (empty($adm_mail) || !filter_var($adm_mail, FILTER_VALIDATE_EMAIL)) $error = 'Email admin tidak valid.';
    elseif (strlen($adm_pass) < 6) $error = 'Password minimal 6 karakter.';
    else {

        // === PROSES INSTALL ===

        // 1. Konek MySQL
        $conn = @mysqli_connect($db_host . ':' . $db_port, $db_user, $db_pass);
        if (!$conn) {
            $error = 'Tidak bisa konek ke MySQL: ' . mysqli_connect_error();
        } else {
            $log[] = '✅ Konek ke MySQL berhasil';

            // 2. Buat database
            mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS `{$db_name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $log[] = '✅ Database "' . $db_name . '" siap';

            // 3. Pilih database
            if (!mysqli_select_db($conn, $db_name)) {
                $error = 'Gagal pilih database: ' . mysqli_error($conn);
            } else {
                $log[] = '✅ Database dipilih';

                // 4. Jalankan schema.sql
                $schema_file = __DIR__ . '/database/schema.sql';
                if (!file_exists($schema_file)) {
                    $error = 'File database/schema.sql tidak ditemukan!';
                } else {
                    $sql = file_get_contents($schema_file);
                    // Hapus komentar
                    $sql = preg_replace('/--[^\n]*/', '', $sql);
                    $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
                    // Jalankan per statement
                    $statements = explode(';', $sql);
                    $ok_count = 0;
                    $skip_count = 0;
                    foreach ($statements as $stmt) {
                        $stmt = trim($stmt);
                        if ($stmt === '') continue;
                        if (mysqli_query($conn, $stmt)) {
                            $ok_count++;
                        } else {
                            $errno = mysqli_errno($conn);
                            // Ignore "already exists" errors
                            if (in_array($errno, array(1007, 1050, 1051, 1061, 1062))) {
                                $skip_count++;
                            }
                            // Error lain: abaikan saja, jangan stop
                        }
                    }
                    $log[] = '✅ Schema SQL dijalankan (' . $ok_count . ' OK, ' . $skip_count . ' dilewati)';

                    // 5. Buat akun admin
                    $hp = password_hash($adm_pass, PASSWORD_DEFAULT);
                    $sn = mysqli_real_escape_string($conn, $adm_name);
                    $su = mysqli_real_escape_string($conn, $adm_user);
                    $se = mysqli_real_escape_string($conn, $adm_mail);
                    $sp = mysqli_real_escape_string($conn, $hp);
                    $q  = "INSERT INTO `admins` (`name`,`username`,`email`,`password`,`role`)
                           VALUES ('$sn','$su','$se','$sp','superadmin')
                           ON DUPLICATE KEY UPDATE
                             `name`=VALUES(`name`),`password`=VALUES(`password`),`email`=VALUES(`email`)";
                    if (mysqli_query($conn, $q)) {
                        $log[] = '✅ Akun admin "' . $su . '" disimpan';
                    } else {
                        $log[] = '⚠️ Admin: ' . mysqli_error($conn) . ' (mungkin sudah ada)';
                    }

                    mysqli_close($conn);

                    // 6. Tulis config/env.php
                    $app_key = bin2hex(random_bytes(16));
                    $env  = "<?php\n";
                    $env .= "define('DB_HOST',      '" . addslashes($db_host) . "');\n";
                    $env .= "define('DB_USER',      '" . addslashes($db_user) . "');\n";
                    $env .= "define('DB_PASS',      '" . addslashes($db_pass) . "');\n";
                    $env .= "define('DB_NAME',      '" . addslashes($db_name) . "');\n";
                    $env .= "define('DB_PORT',      " . $db_port . ");\n";
                    $env .= "define('APP_KEY',      '" . $app_key . "');\n";
                    $env .= "define('APP_TIMEZONE', '" . $timezone . "');\n";
                    $env .= "date_default_timezone_set(APP_TIMEZONE);\n";
                    $env .= "define('APP_ENV', 'production');\n";
                    $env .= "define('INSTALLER_LOCKED', false);\n";
                    $env .= "// APP_URL & APP_BASE tidak perlu diset - auto-detect\n";
                    $env .= "// Uncomment jika perlu override:\n";
                    $env .= "// define('APP_URL',  'http://smk.local');\n";
                    $env .= "// define('APP_BASE', '');\n";

                    if (file_put_contents(__DIR__ . '/config/env.php', $env) === false) {
                        $error = 'Gagal tulis config/env.php! Pastikan folder config/ writable.';
                    } else {
                        $log[] = '✅ config/env.php ditulis';

                        // 7. Buat install.lock
                        file_put_contents(__DIR__ . '/install.lock', date('Y-m-d H:i:s'));
                        $log[] = '✅ install.lock dibuat';

                        $installed = true;
                    }
                }
            }
        }
    }
}

// ── Deteksi URL saat ini ───────────────────────────────────────
$scheme  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host    = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
$dir     = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
$site_url = $scheme . '://' . $host . $dir;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Install - SMK Website</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{background:#0f172a;color:#e2e8f0;font-family:'Segoe UI',sans-serif;
  min-height:100vh;padding:32px 16px;display:flex;flex-direction:column;align-items:center}
h1{font-size:1.5rem;font-weight:700;color:#f1f5f9;margin-bottom:6px}
.sub{color:#64748b;font-size:.9rem;margin-bottom:28px}
.card{width:100%;max-width:560px;background:#1e293b;border:1px solid #334155;
  border-radius:14px;padding:28px 32px}
.card h2{font-size:1rem;font-weight:700;color:#f1f5f9;margin-bottom:20px;
  padding-bottom:12px;border-bottom:1px solid #334155}
.row{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px}
.row.full{grid-template-columns:1fr}
.fg{display:flex;flex-direction:column;gap:5px}
label{font-size:.8rem;font-weight:600;color:#94a3b8}
input,select{background:#0f172a;border:1px solid #334155;border-radius:8px;
  padding:10px 12px;color:#e2e8f0;font-size:.88rem;width:100%;outline:none;
  transition:border .15s}
input:focus,select:focus{border-color:#3b82f6}
input::placeholder{color:#334155}
select option{background:#1e293b}
.sep{font-size:.72rem;font-weight:700;text-transform:uppercase;
  letter-spacing:.08em;color:#475569;margin:20px 0 14px;
  padding-bottom:6px;border-bottom:1px solid #1e293b}
.btn{width:100%;padding:13px;background:linear-gradient(135deg,#2563eb,#4f46e5);
  color:#fff;border:none;border-radius:8px;font-size:.95rem;font-weight:700;
  cursor:pointer;margin-top:20px;transition:all .2s}
.btn:hover{filter:brightness(1.1);transform:translateY(-1px)}
.btn:disabled{opacity:.5;cursor:not-allowed;transform:none;filter:none}
.err{background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.3);
  border-radius:8px;padding:12px 14px;color:#fca5a5;margin-bottom:18px;font-size:.88rem}
.log{background:#0f172a;border:1px solid #334155;border-radius:8px;
  padding:14px 16px;margin-bottom:20px}
.log div{font-size:.88rem;padding:4px 0;color:#cbd5e1}
.success{text-align:center;padding:8px 0 16px}
.success h2{font-size:1.4rem;color:#4ade80;margin-bottom:8px}
.success p{color:#94a3b8;font-size:.9rem;margin-bottom:16px}
.links{display:flex;gap:10px;justify-content:center;flex-wrap:wrap}
.link-btn{padding:10px 22px;border-radius:8px;text-decoration:none;
  font-size:.88rem;font-weight:700;display:inline-flex;align-items:center;gap:6px}
.lb-pri{background:#2563eb;color:#fff}
.lb-sec{background:#334155;color:#e2e8f0}
.creds{background:rgba(37,99,235,.1);border:1px solid rgba(37,99,235,.25);
  border-radius:8px;padding:14px;margin:16px 0;text-align:left}
.creds h4{color:#93c5fd;font-size:.78rem;text-transform:uppercase;
  letter-spacing:.06em;margin-bottom:10px}
.cr{display:flex;justify-content:space-between;margin-bottom:6px;font-size:.85rem}
.ck{color:#64748b}.cv{color:#e2e8f0;font-weight:700;font-family:monospace}
.warn-del{background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);
  border-radius:8px;padding:12px;margin-top:16px;font-size:.82rem;
  color:#fca5a5;line-height:1.6}
.warn-del code{background:rgba(0,0,0,.3);padding:1px 5px;border-radius:3px}
.tip{background:rgba(251,191,36,.07);border:1px solid rgba(251,191,36,.18);
  border-radius:8px;padding:10px 14px;font-size:.8rem;color:#fde68a;margin-top:14px}
@media(max-width:500px){
  .row{grid-template-columns:1fr}
  .card{padding:20px}
}
</style>
</head>
<body>
<div style="text-align:center;margin-bottom:24px">
  <div style="width:60px;height:60px;background:linear-gradient(135deg,#2563eb,#6366f1);
    border-radius:14px;display:inline-flex;align-items:center;justify-content:center;
    font-size:26px;margin-bottom:12px;box-shadow:0 8px 20px rgba(37,99,235,.3)">🏫</div>
  <h1>Installer SMK Pertamaku</h1>
  <div class="sub">Isi form berikut dan klik Install — selesai!</div>
</div>

<div class="card">

<?php if ($installed): ?>
<!-- ===== SUKSES ===== -->
<div class="success">
  <div style="font-size:48px;margin-bottom:12px">🎉</div>
  <h2>Instalasi Berhasil!</h2>
  <p>Website SMK sudah siap digunakan.</p>
</div>
<div class="log">
  <?php foreach ($log as $l): ?>
  <div><?php echo htmlspecialchars($l); ?></div>
  <?php endforeach; ?>
</div>
<div class="creds">
  <h4>📋 Info Login Admin</h4>
  <div class="cr">
    <span class="ck">URL Admin</span>
    <span class="cv"><?php echo htmlspecialchars($site_url); ?>/admin/login</span>
  </div>
  <div class="cr">
    <span class="ck">Username</span>
    <span class="cv"><?php echo htmlspecialchars(isset($_POST['adm_user']) ? $_POST['adm_user'] : ''); ?></span>
  </div>
  <div class="cr">
    <span class="ck">Password</span>
    <span class="cv">••••••••</span>
  </div>
</div>
<div class="links">
  <a href="<?php echo htmlspecialchars($site_url); ?>/" class="link-btn lb-pri" target="_blank">🌐 Buka Website</a>
  <a href="<?php echo htmlspecialchars($site_url); ?>/admin/login" class="link-btn lb-sec" target="_blank">⚙️ Panel Admin</a>
</div>
<div class="warn-del">
  <strong>⚠️ PENTING: Hapus file installer ini!</strong><br>
  Segera hapus file: <code><?php echo htmlspecialchars(__FILE__); ?></code>
</div>

<?php else: ?>
<!-- ===== FORM INSTALL ===== -->

<?php if (!empty($error)): ?>
<div class="err">❌ <?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<?php if (!empty($log)): ?>
<div class="log">
  <?php foreach ($log as $l): ?>
  <div><?php echo htmlspecialchars($l); ?></div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<form method="POST">
  <input type="hidden" name="do_install" value="1">

  <h2>🗄️ Database MySQL</h2>

  <div class="row">
    <div class="fg">
      <label>Host Database</label>
      <input type="text" name="db_host" value="<?php echo htmlspecialchars(isset($_POST['db_host']) ? $_POST['db_host'] : 'localhost'); ?>" placeholder="localhost" required>
    </div>
    <div class="fg">
      <label>Port</label>
      <input type="number" name="db_port" value="<?php echo htmlspecialchars(isset($_POST['db_port']) ? $_POST['db_port'] : '3306'); ?>" placeholder="3306" min="1" max="65535">
    </div>
  </div>

  <div class="row">
    <div class="fg">
      <label>Username MySQL</label>
      <input type="text" name="db_user" value="<?php echo htmlspecialchars(isset($_POST['db_user']) ? $_POST['db_user'] : 'root'); ?>" placeholder="root" required>
    </div>
    <div class="fg">
      <label>Password MySQL</label>
      <input type="password" name="db_pass" value="" placeholder="kosong = XAMPP default">
    </div>
  </div>

  <div class="row full">
    <div class="fg">
      <label>Nama Database</label>
      <input type="text" name="db_name" value="<?php echo htmlspecialchars(isset($_POST['db_name']) ? $_POST['db_name'] : 'websmk'); ?>" placeholder="websmk" required>
    </div>
  </div>

  <div class="tip">💡 Database akan dibuat otomatis jika belum ada. Cukup isi nama yang diinginkan.</div>

  <div class="sep">👤 Akun Admin</div>

  <div class="row full">
    <div class="fg">
      <label>Nama Lengkap Admin</label>
      <input type="text" name="adm_name" value="<?php echo htmlspecialchars(isset($_POST['adm_name']) ? $_POST['adm_name'] : 'Super Admin'); ?>" placeholder="Super Admin" required>
    </div>
  </div>

  <div class="row">
    <div class="fg">
      <label>Username</label>
      <input type="text" name="adm_user" value="<?php echo htmlspecialchars(isset($_POST['adm_user']) ? $_POST['adm_user'] : 'admin'); ?>" placeholder="admin" pattern="[a-zA-Z0-9_]+" required>
    </div>
    <div class="fg">
      <label>Email</label>
      <input type="email" name="adm_mail" value="<?php echo htmlspecialchars(isset($_POST['adm_mail']) ? $_POST['adm_mail'] : ''); ?>" placeholder="admin@sekolah.sch.id" required>
    </div>
  </div>

  <div class="row full">
    <div class="fg">
      <label>Password Admin (min. 6 karakter)</label>
      <input type="password" name="adm_pass" value="" placeholder="••••••••" minlength="6" required>
    </div>
  </div>

  <div class="sep">⚙️ Pengaturan</div>

  <div class="row full">
    <div class="fg">
      <label>Zona Waktu</label>
      <select name="timezone">
        <option value="Asia/Jakarta"<?php echo (!isset($_POST['timezone']) || $_POST['timezone']==='Asia/Jakarta') ? ' selected' : ''; ?>>WIB — Asia/Jakarta (UTC+7)</option>
        <option value="Asia/Makassar"<?php echo (isset($_POST['timezone']) && $_POST['timezone']==='Asia/Makassar') ? ' selected' : ''; ?>>WITA — Asia/Makassar (UTC+8)</option>
        <option value="Asia/Jayapura"<?php echo (isset($_POST['timezone']) && $_POST['timezone']==='Asia/Jayapura') ? ' selected' : ''; ?>>WIT — Asia/Jayapura (UTC+9)</option>
        <option value="UTC"<?php echo (isset($_POST['timezone']) && $_POST['timezone']==='UTC') ? ' selected' : ''; ?>>UTC</option>
      </select>
    </div>
  </div>

  <button type="submit" class="btn" id="ibtn" onclick="this.disabled=true;this.innerHTML='⏳ Menginstall...';this.form.submit();">
    🚀 Install Sekarang
  </button>

</form>

<div class="tip" style="margin-top:16px">
  ℹ️ <strong>Tidak perlu isi URL atau folder</strong> — sistem otomatis mendeteksi apakah
  kamu pakai vhosts, subfolder, atau domain langsung.
</div>

<?php endif; ?>

</div>

<div style="margin-top:20px;font-size:.72rem;color:#334155;text-align:center">
  PHP <?php echo PHP_VERSION; ?> &bull; Hapus file ini setelah instalasi selesai
</div>

</body>
</html>
<?php ob_end_flush(); ?>

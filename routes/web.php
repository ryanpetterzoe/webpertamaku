<?php
/**
 * Router
 * APP_BASE di-detect otomatis — bekerja untuk vhosts maupun subfolder
 */

require_once __DIR__ . '/../app/controllers/PublicController.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/AdminController.php';
require_once __DIR__ . '/../app/controllers/CmsController.php';

if (!function_exists('redirect')) {
    function redirect($path) {
        header('Location: ' . APP_URL . $path);
        exit;
    }
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ── Bersihkan URI (hapus APP_BASE prefix) ──────────────────────
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rawurldecode($uri);
$uri = str_replace('\\', '/', $uri);

$base = rtrim(APP_BASE, '/'); // APP_BASE sudah di-define di app.php
if ($base !== '' && strpos($uri, $base) === 0) {
    $uri = substr($uri, strlen($base));
}
$uri = '/' . trim($uri, '/');
if ($uri === '') $uri = '/';

$method = $_SERVER['REQUEST_METHOD'];

$public = new PublicController();
$auth   = new AuthController();
$admin  = new AdminController();
$cms    = new CmsController();

// ── PUBLIC ─────────────────────────────────────────────────────
if ($uri === '/') { $public->home(); return; }
if ($uri === '/profil' || $uri === '/visi-misi' || $uri === '/sejarah' || $uri === '/kepala-sekolah') { $public->about(); return; }
if ($uri === '/jurusan') { $public->programs(); return; }
if (preg_match('#^/jurusan/(\d+)$#', $uri, $m)) { $public->programDetail($m[1]); return; }
if ($uri === '/guru-staff') { $public->teachers(); return; }
if ($uri === '/berita') { $public->news(max(1, (int)(isset($_GET['page']) ? $_GET['page'] : 1))); return; }
if (preg_match('#^/berita/([a-z0-9-]+)$#', $uri, $m)) { $public->newsDetail($m[1]); return; }
if ($uri === '/galeri') { $public->gallery(max(1, (int)(isset($_GET['page']) ? $_GET['page'] : 1))); return; }
if ($uri === '/prestasi') { $public->achievements(); return; }
if ($uri === '/kontak') { $public->contact(); return; }
if ($uri === '/spmb') { $public->spmbInfo(); return; }
if ($uri === '/spmb/daftar') { $public->spmbForm(); return; }
if ($uri === '/spmb/cek') { $public->spmbCheck(); return; }

// ── AUTH ───────────────────────────────────────────────────────
if ($uri === '/admin/login') { $auth->login(); return; }
if ($uri === '/admin/logout') { $auth->logout(); return; }
if ($uri === '/admin' || $uri === '/admin/') { redirect('/admin/dashboard'); return; }

// ── ADMIN ──────────────────────────────────────────────────────
if ($uri === '/admin/dashboard') { $admin->dashboard(); return; }

if ($uri === '/admin/berita') { $cms->newsList(); return; }
if ($uri === '/admin/berita/tambah') { ($method==='POST') ? $cms->newsSave() : $cms->newsForm(); return; }
if (preg_match('#^/admin/berita/edit/(\d+)$#', $uri, $m)) { ($method==='POST') ? $cms->newsSave($m[1]) : $cms->newsForm($m[1]); return; }
if (preg_match('#^/admin/berita/toggle/(\d+)$#', $uri, $m)) { $cms->newsToggle($m[1]); return; }
if (preg_match('#^/admin/berita/hapus/(\d+)$#', $uri, $m)) { $cms->newsDelete($m[1]); return; }

if ($uri === '/admin/galeri') { $cms->galleryList(); return; }
if ($uri === '/admin/galeri/tambah') { ($method==='POST') ? $cms->gallerySave() : $cms->galleryForm(); return; }
if (preg_match('#^/admin/galeri/edit/(\d+)$#', $uri, $m)) { ($method==='POST') ? $cms->gallerySave($m[1]) : $cms->galleryForm($m[1]); return; }
if (preg_match('#^/admin/galeri/hapus/(\d+)$#', $uri, $m)) { $cms->galleryDelete($m[1]); return; }

if ($uri === '/admin/slider') { $cms->sliderList(); return; }
if ($uri === '/admin/slider/tambah') { ($method==='POST') ? $cms->sliderSave() : $cms->sliderForm(); return; }
if (preg_match('#^/admin/slider/edit/(\d+)$#', $uri, $m)) { ($method==='POST') ? $cms->sliderSave($m[1]) : $cms->sliderForm($m[1]); return; }
if (preg_match('#^/admin/slider/hapus/(\d+)$#', $uri, $m)) { $cms->sliderDelete($m[1]); return; }

if ($uri === '/admin/jurusan') { $cms->programsList(); return; }
if ($uri === '/admin/jurusan/tambah') { ($method==='POST') ? $cms->programSave() : $cms->programForm(); return; }
if (preg_match('#^/admin/jurusan/edit/(\d+)$#', $uri, $m)) { ($method==='POST') ? $cms->programSave($m[1]) : $cms->programForm($m[1]); return; }
if (preg_match('#^/admin/jurusan/hapus/(\d+)$#', $uri, $m)) { $cms->programDelete($m[1]); return; }

if ($uri === '/admin/guru') { $cms->teachersList(); return; }
if ($uri === '/admin/guru/tambah') { ($method==='POST') ? $cms->teacherSave() : $cms->teacherForm(); return; }
if (preg_match('#^/admin/guru/edit/(\d+)$#', $uri, $m)) { ($method==='POST') ? $cms->teacherSave($m[1]) : $cms->teacherForm($m[1]); return; }
if (preg_match('#^/admin/guru/hapus/(\d+)$#', $uri, $m)) { $cms->teacherDelete($m[1]); return; }

if ($uri === '/admin/kontak') { $cms->contactsList(); return; }
if (preg_match('#^/admin/kontak/baca/(\d+)$#', $uri, $m)) { $cms->contactRead($m[1]); return; }
if (preg_match('#^/admin/kontak/hapus/(\d+)$#', $uri, $m)) { $cms->contactDelete($m[1]); return; }

if ($uri === '/admin/spmb/pendaftar') { $cms->spmbList(); return; }
if ($uri === '/admin/spmb/export') { $cms->spmbExport(); return; }
if (preg_match('#^/admin/spmb/detail/(\d+)$#', $uri, $m)) { $cms->spmbDetail($m[1]); return; }
if (preg_match('#^/admin/spmb/status/(\d+)$#', $uri, $m)) { $cms->spmbUpdateStatus($m[1]); return; }
if (preg_match('#^/admin/spmb/hapus/(\d+)$#', $uri, $m)) { $cms->spmbDelete($m[1]); return; }
if ($uri === '/admin/spmb/pengaturan') { ($method==='POST') ? $cms->spmbSettingsSave() : $cms->spmbSettings(); return; }

if ($uri === '/admin/settings/umum') { $cms->settingsGeneral(); return; }

// ── 404 ────────────────────────────────────────────────────────
http_response_code(404);
echo '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8">
<title>404</title>
<link rel="stylesheet" href="'.APP_URL.'/assets/css/style.css">
<script>(function(){var t=localStorage.getItem("smk_theme")||"light";document.documentElement.setAttribute("data-theme",t);})()</script>
</head><body style="background:var(--bg);display:flex;align-items:center;justify-content:center;min-height:100vh;text-align:center;padding:20px">
<div>
<div style="font-size:5rem;font-weight:900;color:var(--primary)">404</div>
<h2 style="color:var(--text);margin:12px 0 8px">Halaman Tidak Ditemukan</h2>
<p style="color:var(--text-muted)">Halaman yang Anda cari tidak ada.</p>
<a href="'.APP_URL.'/" style="display:inline-block;margin-top:16px;padding:10px 24px;
background:var(--primary);color:#fff;border-radius:8px;text-decoration:none;font-weight:600">
&#x2190; Beranda</a>
</div></body></html>';

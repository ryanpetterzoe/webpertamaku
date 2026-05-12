<?php
/**
 * Router - Simple PHP router
 * Parses REQUEST_URI and dispatches to controllers
 * Base path is read from APP_BASE constant (set in config/env.php)
 */

// Redirect to installer if env.php is missing or DB not configured
if (!file_exists(__DIR__ . '/../config/env.php')) {
    $installerUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
        . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost')
        . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . '/install.php';
    header('Location: ' . $installerUrl);
    exit;
}

// Load controllers
require_once __DIR__ . '/../app/controllers/PublicController.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/AdminController.php';
require_once __DIR__ . '/../app/controllers/CmsController.php';

// Helper function for redirects from router
if (!function_exists('redirect')) {
    function redirect($path) {
        header('Location: ' . APP_URL . $path);
        exit;
    }
}

// Generate CSRF token if not set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Get clean URI (strip app base path from env.php / config)
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath    = defined('APP_BASE') ? APP_BASE : '/webpertamaku';
// Strip trailing slash from basePath for clean compare
$basePath    = rtrim($basePath, '/');
if ($basePath !== '' && strpos($requestUri, $basePath) === 0) {
    $uri = substr($requestUri, strlen($basePath));
} else {
    $uri = $requestUri;
}
$uri = '/' . trim($uri, '/');
if ($uri === '//') $uri = '/';

$method = $_SERVER['REQUEST_METHOD'];

// Instantiate controllers
$public = new PublicController();
$auth   = new AuthController();
$admin  = new AdminController();
$cms    = new CmsController();

// ============================================================
// PUBLIC ROUTES
// ============================================================
if ($uri === '/' || $uri === '') {
    $public->home(); return;
}

// About pages
if ($uri === '/profil') { $public->about(); return; }
if ($uri === '/visi-misi') { $public->about(); return; }
if ($uri === '/sejarah') { $public->about(); return; }
if ($uri === '/kepala-sekolah') { $public->about(); return; }

// Programs
if ($uri === '/jurusan') { $public->programs(); return; }
if (preg_match('#^/jurusan/(\d+)$#', $uri, $m)) { $public->programDetail($m[1]); return; }

// Teachers
if ($uri === '/guru-staff') { $public->teachers(); return; }

// News
if ($uri === '/berita') { $public->news(max(1, (int)($_GET['page'] ?? 1))); return; }
if (preg_match('#^/berita/([a-z0-9-]+)$#', $uri, $m)) { $public->newsDetail($m[1]); return; }

// Gallery
if ($uri === '/galeri') { $public->gallery(max(1, (int)($_GET['page'] ?? 1))); return; }

// Achievements
if ($uri === '/prestasi') { $public->achievements(); return; }

// Contact
if ($uri === '/kontak') { $public->contact(); return; }

// SPMB
if ($uri === '/spmb') { $public->spmbInfo(); return; }
if ($uri === '/spmb/daftar') { $public->spmbForm(); return; }
if ($uri === '/spmb/cek') { $public->spmbCheck(); return; }

// ============================================================
// AUTH ROUTES
// ============================================================
if ($uri === '/admin/login' || $uri === '/admin/login/') { $auth->login(); return; }
if ($uri === '/admin/logout') { $auth->logout(); return; }

// /admin redirect
if ($uri === '/admin' || $uri === '/admin/') { redirect('/admin/dashboard'); return; }

// ============================================================
// ADMIN ROUTES
// ============================================================
if ($uri === '/admin/dashboard') { $admin->dashboard(); return; }

// --- NEWS ---
if ($uri === '/admin/berita') { $cms->newsList(); return; }
if ($uri === '/admin/berita/tambah') { $method === 'POST' ? $cms->newsSave() : $cms->newsForm(); return; }
if (preg_match('#^/admin/berita/edit/(\d+)$#', $uri, $m)) { $method === 'POST' ? $cms->newsSave($m[1]) : $cms->newsForm($m[1]); return; }
if (preg_match('#^/admin/berita/toggle/(\d+)$#', $uri, $m)) { $cms->newsToggle($m[1]); return; }
if (preg_match('#^/admin/berita/hapus/(\d+)$#', $uri, $m)) { $cms->newsDelete($m[1]); return; }

// --- GALLERY ---
if ($uri === '/admin/galeri') { $cms->galleryList(); return; }
if ($uri === '/admin/galeri/tambah') { $method === 'POST' ? $cms->gallerySave() : $cms->galleryForm(); return; }
if (preg_match('#^/admin/galeri/edit/(\d+)$#', $uri, $m)) { $method === 'POST' ? $cms->gallerySave($m[1]) : $cms->galleryForm($m[1]); return; }
if (preg_match('#^/admin/galeri/hapus/(\d+)$#', $uri, $m)) { $cms->galleryDelete($m[1]); return; }

// --- SLIDERS ---
if ($uri === '/admin/slider') { $cms->sliderList(); return; }
if ($uri === '/admin/slider/tambah') { $method === 'POST' ? $cms->sliderSave() : $cms->sliderForm(); return; }
if (preg_match('#^/admin/slider/edit/(\d+)$#', $uri, $m)) { $method === 'POST' ? $cms->sliderSave($m[1]) : $cms->sliderForm($m[1]); return; }
if (preg_match('#^/admin/slider/hapus/(\d+)$#', $uri, $m)) { $cms->sliderDelete($m[1]); return; }

// --- PROGRAMS / JURUSAN ---
if ($uri === '/admin/jurusan') { $cms->programsList(); return; }
if ($uri === '/admin/jurusan/tambah') { $method === 'POST' ? $cms->programSave() : $cms->programForm(); return; }
if (preg_match('#^/admin/jurusan/edit/(\d+)$#', $uri, $m)) { $method === 'POST' ? $cms->programSave($m[1]) : $cms->programForm($m[1]); return; }
if (preg_match('#^/admin/jurusan/hapus/(\d+)$#', $uri, $m)) { $cms->programDelete($m[1]); return; }

// --- TEACHERS / GURU ---
if ($uri === '/admin/guru') { $cms->teachersList(); return; }
if ($uri === '/admin/guru/tambah') { $method === 'POST' ? $cms->teacherSave() : $cms->teacherForm(); return; }
if (preg_match('#^/admin/guru/edit/(\d+)$#', $uri, $m)) { $method === 'POST' ? $cms->teacherSave($m[1]) : $cms->teacherForm($m[1]); return; }
if (preg_match('#^/admin/guru/hapus/(\d+)$#', $uri, $m)) { $cms->teacherDelete($m[1]); return; }

// --- CONTACTS ---
if ($uri === '/admin/kontak') { $cms->contactsList(); return; }
if (preg_match('#^/admin/kontak/baca/(\d+)$#', $uri, $m)) { $cms->contactRead($m[1]); return; }
if (preg_match('#^/admin/kontak/hapus/(\d+)$#', $uri, $m)) { $cms->contactDelete($m[1]); return; }

// --- SPMB ---
if ($uri === '/admin/spmb/pendaftar') { $cms->spmbList(); return; }
if ($uri === '/admin/spmb/export') { $cms->spmbExport(); return; }
if (preg_match('#^/admin/spmb/detail/(\d+)$#', $uri, $m)) { $cms->spmbDetail($m[1]); return; }
if (preg_match('#^/admin/spmb/status/(\d+)$#', $uri, $m)) { $cms->spmbUpdateStatus($m[1]); return; }
if (preg_match('#^/admin/spmb/hapus/(\d+)$#', $uri, $m)) { $cms->spmbDelete($m[1]); return; }
if ($uri === '/admin/spmb/pengaturan') { $method === 'POST' ? $cms->spmbSettingsSave() : $cms->spmbSettings(); return; }

// --- SETTINGS ---
if ($uri === '/admin/settings/umum') { $cms->settingsGeneral(); return; }

// ============================================================
// 404 Not Found
// ============================================================
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
    <script>(function(){ var t=localStorage.getItem('theme')||'light'; document.documentElement.setAttribute('data-theme',t); })();</script>
</head>
<body>
<div class="d-flex align-items-center justify-content-center min-vh-100 text-center p-4" style="background:var(--bg);">
    <div>
        <div style="font-size:6rem;font-weight:900;color:var(--primary);line-height:1;">404</div>
        <h2 style="color:var(--text);margin:16px 0 8px;">Halaman Tidak Ditemukan</h2>
        <p style="color:var(--text-muted);">Maaf, halaman yang Anda cari tidak ada atau telah dipindahkan.</p>
        <a href="<?= APP_URL ?>/" class="btn btn-primary mt-3">
            <i class="fas fa-home me-2"></i>Kembali ke Beranda
        </a>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

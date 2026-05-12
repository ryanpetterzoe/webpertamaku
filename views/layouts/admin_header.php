<?php
requireLogin();
$adminName = $_SESSION['admin_name'] ?? 'Admin';
$adminRole = $_SESSION['admin_role'] ?? 'admin';
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Unread contacts count
$db = getDB();
$unreadRes = $db->query("SELECT COUNT(*) as cnt FROM contacts WHERE is_read=0");
$unreadCount = $unreadRes ? (int)$unreadRes->fetch_assoc()['cnt'] : 0;
// Pending SPMB
$pendingRes = $db->query("SELECT COUNT(*) as cnt FROM spmb_registrations WHERE status='pending'");
$pendingCount = $pendingRes ? (int)$pendingRes->fetch_assoc()['cnt'] : 0;

$pageTitle = $adminPageTitle ?? 'Admin Panel';
?>
<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - Admin Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
    <script>(function(){ var t=localStorage.getItem('theme')||'light'; document.documentElement.setAttribute('data-theme',t); })();</script>
</head>
<body>

<div class="admin-wrapper">
<!-- ========== SIDEBAR ========== -->
<aside class="admin-sidebar" id="adminSidebar">
    <a href="<?= APP_URL ?>/admin/dashboard" class="sidebar-brand">
        <i class="fas fa-graduation-cap"></i>
        <span>SMK Panel</span>
    </a>

    <nav class="sidebar-nav">
        <!-- Dashboard -->
        <div class="sidebar-section">
            <a href="<?= APP_URL ?>/admin/dashboard" class="sidebar-link <?= strpos($currentPath,'dashboard') !== false ? 'active' : '' ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        </div>

        <!-- Konten -->
        <div class="sidebar-section">
            <div class="sidebar-section-title">Konten</div>
            <a href="<?= APP_URL ?>/admin/berita" class="sidebar-link <?= strpos($currentPath,'/admin/berita') !== false ? 'active' : '' ?>">
                <i class="fas fa-newspaper"></i> Berita
            </a>
            <a href="<?= APP_URL ?>/admin/galeri" class="sidebar-link <?= strpos($currentPath,'/admin/galeri') !== false ? 'active' : '' ?>">
                <i class="fas fa-images"></i> Galeri
            </a>
            <a href="<?= APP_URL ?>/admin/slider" class="sidebar-link <?= strpos($currentPath,'/admin/slider') !== false ? 'active' : '' ?>">
                <i class="fas fa-images"></i> Slider
            </a>
            <a href="<?= APP_URL ?>/admin/prestasi" class="sidebar-link <?= strpos($currentPath,'/admin/prestasi') !== false ? 'active' : '' ?>">
                <i class="fas fa-trophy"></i> Prestasi
            </a>
            <a href="<?= APP_URL ?>/admin/testimonial" class="sidebar-link <?= strpos($currentPath,'/admin/testimonial') !== false ? 'active' : '' ?>">
                <i class="fas fa-quote-left"></i> Testimonial
            </a>
            <a href="<?= APP_URL ?>/admin/agenda" class="sidebar-link <?= strpos($currentPath,'/admin/agenda') !== false ? 'active' : '' ?>">
                <i class="fas fa-calendar-alt"></i> Agenda
            </a>
        </div>

        <!-- Akademik -->
        <div class="sidebar-section">
            <div class="sidebar-section-title">Akademik</div>
            <a href="<?= APP_URL ?>/admin/jurusan" class="sidebar-link <?= strpos($currentPath,'/admin/jurusan') !== false ? 'active' : '' ?>">
                <i class="fas fa-book"></i> Jurusan
            </a>
            <a href="<?= APP_URL ?>/admin/guru" class="sidebar-link <?= strpos($currentPath,'/admin/guru') !== false ? 'active' : '' ?>">
                <i class="fas fa-chalkboard-teacher"></i> Guru
            </a>
            <a href="<?= APP_URL ?>/admin/staff" class="sidebar-link <?= strpos($currentPath,'/admin/staff') !== false ? 'active' : '' ?>">
                <i class="fas fa-users"></i> Staff
            </a>
        </div>

        <!-- SPMB -->
        <div class="sidebar-section">
            <div class="sidebar-section-title">SPMB</div>
            <a href="<?= APP_URL ?>/admin/spmb/pendaftar" class="sidebar-link <?= strpos($currentPath,'/admin/spmb/pendaftar') !== false ? 'active' : '' ?>">
                <i class="fas fa-user-plus"></i> Pendaftar
                <?php if ($pendingCount > 0): ?><span class="badge bg-warning text-dark ms-auto"><?= $pendingCount ?></span><?php endif; ?>
            </a>
            <a href="<?= APP_URL ?>/admin/spmb/pengaturan" class="sidebar-link <?= strpos($currentPath,'/admin/spmb/pengaturan') !== false ? 'active' : '' ?>">
                <i class="fas fa-sliders-h"></i> Pengaturan SPMB
            </a>
        </div>

        <!-- Kontak -->
        <div class="sidebar-section">
            <div class="sidebar-section-title">Komunikasi</div>
            <a href="<?= APP_URL ?>/admin/kontak" class="sidebar-link <?= strpos($currentPath,'/admin/kontak') !== false ? 'active' : '' ?>">
                <i class="fas fa-envelope"></i> Pesan Masuk
                <?php if ($unreadCount > 0): ?><span class="badge bg-danger ms-auto"><?= $unreadCount ?></span><?php endif; ?>
            </a>
        </div>

        <!-- Pengaturan -->
        <div class="sidebar-section">
            <div class="sidebar-section-title">Pengaturan</div>
            <a href="<?= APP_URL ?>/admin/settings/umum" class="sidebar-link <?= strpos($currentPath,'/admin/settings') !== false ? 'active' : '' ?>">
                <i class="fas fa-cog"></i> Pengaturan Umum
            </a>
        </div>
    </nav>

    <!-- Sidebar Footer -->
    <div style="padding:16px;border-top:1px solid #1e293b;margin-top:auto;">
        <a href="<?= APP_URL ?>/admin/logout" class="sidebar-link" style="color:#ef4444;">
            <i class="fas fa-sign-out-alt"></i> Keluar
        </a>
    </div>
</aside>

<!-- ========== MAIN CONTENT ========== -->
<div class="admin-content">
    <!-- Top Bar -->
    <header class="admin-topbar">
        <div class="d-flex align-items-center gap-3">
            <button id="sidebarToggle" class="btn btn-sm d-lg-none" style="background:transparent;border:1px solid #e2e8f0;">
                <i class="fas fa-bars"></i>
            </button>
            <h1 class="page-title"><?= htmlspecialchars($pageTitle) ?></h1>
        </div>
        <div class="admin-user">
            <a href="<?= APP_URL ?>/" target="_blank" class="btn btn-sm btn-outline-secondary me-2" title="Lihat Website">
                <i class="fas fa-external-link-alt"></i>
            </a>
            <button id="themeToggle" class="btn btn-sm" style="background:var(--bg-secondary);border:1px solid var(--border);border-radius:8px;color:var(--text);" title="Toggle Theme">
                <i class="fas fa-moon"></i>
            </button>
            <div class="avatar ms-2"><?= strtoupper(substr($adminName, 0, 1)) ?></div>
            <div class="d-none d-md-block ms-2">
                <div style="font-size:0.85rem;font-weight:600;color:var(--text);"><?= htmlspecialchars($adminName) ?></div>
                <div style="font-size:0.75rem;color:var(--text-muted);"><?= ucfirst($adminRole) ?></div>
            </div>
            <a href="<?= APP_URL ?>/admin/logout" class="btn btn-sm btn-outline-danger ms-2" title="Logout">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </header>

    <!-- Inner Content -->
    <div class="admin-inner">

    <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible alert-auto-dismiss fade show">
            <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($_SESSION['flash_success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger alert-dismissible alert-auto-dismiss fade show">
            <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($_SESSION['flash_error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

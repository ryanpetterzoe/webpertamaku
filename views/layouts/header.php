<?php
// Get all settings once
$settings = getSettings();
$schoolName = $settings['school_name'] ?? 'SMK Pertamaku';
$metaTitle = $settings['meta_title'] ?? ($schoolName . ' - Sekolah Menengah Kejuruan Unggulan');
$metaDesc = $settings['meta_description'] ?? '';
$favicon = !empty($settings['school_favicon']) ? UPLOAD_URL . $settings['school_favicon'] : APP_URL . '/assets/images/favicon.ico';
$logo = !empty($settings['school_logo']) ? UPLOAD_URL . $settings['school_logo'] : '';
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
?>
<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? $metaTitle) ?></title>
    <meta name="description" content="<?= htmlspecialchars($metaDesc) ?>">
    <meta name="robots" content="index, follow">
    <!-- Open Graph -->
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle ?? $metaTitle) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($metaDesc) ?>">
    <meta property="og:type" content="website">
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= $favicon ?>">
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
    <!-- Theme init script (prevent flash) -->
    <script>
        (function(){
            var t = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>
    <?php if (!empty($settings['ga_code'])): ?>
    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= htmlspecialchars($settings['ga_code']) ?>"></script>
    <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','<?= htmlspecialchars($settings['ga_code']) ?>');</script>
    <?php endif; ?>
</head>
<body>

<!-- Preloader -->
<div id="preloader">
    <div class="spinner"></div>
</div>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg sticky-top" id="mainNav">
    <div class="container">
        <!-- Logo / Brand -->
        <a class="navbar-brand" href="<?= APP_URL ?>/">
            <?php if ($logo): ?>
                <img src="<?= $logo ?>" alt="<?= htmlspecialchars($schoolName) ?>">
            <?php else: ?>
                <i class="fas fa-graduation-cap me-2"></i><?= htmlspecialchars($schoolName) ?>
            <?php endif; ?>
        </a>

        <!-- Hamburger -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu -->
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link <?= $currentPath === '/webpertamaku/' || $currentPath === '/webpertamaku' ? 'active' : '' ?>" href="<?= APP_URL ?>/">Beranda</a>
                </li>
                <!-- Profil Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Profil</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= APP_URL ?>/profil"><i class="fas fa-school me-2"></i>Tentang Sekolah</a></li>
                        <li><a class="dropdown-item" href="<?= APP_URL ?>/visi-misi"><i class="fas fa-bullseye me-2"></i>Visi & Misi</a></li>
                        <li><a class="dropdown-item" href="<?= APP_URL ?>/sejarah"><i class="fas fa-history me-2"></i>Sejarah</a></li>
                        <li><a class="dropdown-item" href="<?= APP_URL ?>/kepala-sekolah"><i class="fas fa-user-tie me-2"></i>Kepala Sekolah</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= APP_URL ?>/jurusan">Jurusan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= APP_URL ?>/guru-staff">Guru & Staff</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= APP_URL ?>/berita">Berita</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= APP_URL ?>/galeri">Galeri</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= APP_URL ?>/prestasi">Prestasi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= APP_URL ?>/spmb">SPMB</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= APP_URL ?>/kontak">Kontak</a>
                </li>
                <!-- Theme Toggle -->
                <li class="nav-item ms-lg-2">
                    <button id="themeToggle" aria-label="Toggle Theme">
                        <i class="fas fa-moon"></i>
                        <span>Dark</span>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main>

<?php
$settings    = getSettings();
$schoolName  = $settings['school_name']   ?? 'SMK Pertamaku';
$metaTitle   = $settings['meta_title']    ?? ($schoolName . ' - Sekolah Menengah Kejuruan Unggulan');
$metaDesc    = $settings['meta_description'] ?? '';
$logo        = !empty($settings['school_logo'])    ? UPLOAD_URL . $settings['school_logo']    : '';
$favicon     = !empty($settings['school_favicon']) ? UPLOAD_URL . $settings['school_favicon'] : '';
$uri         = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$appBase     = defined('APP_BASE') ? APP_BASE : '/webpertamaku';

function isActive(string $path, string $uri, string $base): string {
    $clean = str_replace($base, '', $uri);
    $clean = '/' . ltrim($clean, '/') ?: '/';
    if ($path === '/' && ($clean === '/' || $clean === '')) return 'active';
    if ($path !== '/' && strpos($clean, $path) === 0) return 'active';
    return '';
}
?>
<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? $metaTitle) ?></title>
  <meta name="description" content="<?= htmlspecialchars($metaDesc) ?>">
  <meta name="robots" content="index, follow">
  <meta property="og:title"       content="<?= htmlspecialchars($pageTitle ?? $metaTitle) ?>">
  <meta property="og:description" content="<?= htmlspecialchars($metaDesc) ?>">
  <meta property="og:type"        content="website">
  <?php if ($favicon): ?>
  <link rel="icon" type="image/x-icon" href="<?= htmlspecialchars($favicon) ?>">
  <?php endif; ?>
  <!-- Bootstrap 5 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <!-- Font Awesome 6 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
  <!-- Theme init — prevent flash -->
  <script>
    (function(){
      var t = localStorage.getItem('smk_theme') || '<?= htmlspecialchars($settings['theme_default'] ?? 'light') ?>';
      document.documentElement.setAttribute('data-theme', t);
    })();
  </script>
  <?php
  // Inject custom accent color dari settings
  $accentPrimary = !empty($settings['accent_primary']) ? $settings['accent_primary'] : '';
  $accentDark    = !empty($settings['accent_dark'])    ? $settings['accent_dark']    : '';
  if ($accentPrimary && $accentPrimary !== '#2563eb'):
      $hx = ltrim($accentPrimary, '#');
      if (strlen($hx) === 6) {
          $r = hexdec(substr($hx,0,2));
          $g = hexdec(substr($hx,2,2));
          $b = hexdec(substr($hx,4,2));
          $rgb = "$r,$g,$b";
      } else { $rgb = '37,99,235'; }
      $darkColor = $accentDark ?: $accentPrimary;
  ?>
  <style>
  :root{
    --primary:<?= $accentPrimary ?>;
    --primary-dark:<?= $darkColor ?>;
    --primary-glow:rgba(<?= $rgb ?>,0.20);
    --shadow-blue:0 8px 32px rgba(<?= $rgb ?>,0.25);
    --gradient:linear-gradient(135deg,<?= $accentPrimary ?> 0%,<?= $darkColor ?> 100%);
  }
  [data-theme="dark"]{
    --primary:<?= $accentPrimary ?>;
    --primary-dark:<?= $darkColor ?>;
    --primary-glow:rgba(<?= $rgb ?>,0.25);
    --shadow-blue:0 8px 32px rgba(<?= $rgb ?>,0.30);
    --gradient:linear-gradient(135deg,<?= $accentPrimary ?> 0%,<?= $darkColor ?> 100%);
  }
  </style>
  <?php endif; ?>
  <?php if (!empty($settings['ga_code'])): ?>
  <script async src="https://www.googletagmanager.com/gtag/js?id=<?= htmlspecialchars($settings['ga_code']) ?>"></script>
  <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','<?= htmlspecialchars($settings['ga_code']) ?>');</script>
  <?php endif; ?>
  <style>body{font-family:'Inter',system-ui,sans-serif;}</style>
</head>
<body>

<!-- Preloader -->
<div id="preloader">
  <div class="preloader-logo">🎓</div>
  <div class="preloader-dots">
    <span></span><span></span><span></span>
  </div>
</div>

<!-- ═══════════════════════════════════════════════════════════
     NAVBAR
     ═══════════════════════════════════════════════════════════ -->
<nav class="navbar navbar-expand-lg sticky-top" id="mainNav">
  <div class="container">

    <!-- Brand -->
    <a class="navbar-brand" href="<?= APP_URL ?>/">
      <?php if ($logo): ?>
        <img src="<?= htmlspecialchars($logo) ?>" alt="<?= htmlspecialchars($schoolName) ?>">
      <?php else: ?>
        <div class="brand-icon">🎓</div>
        <span><?= htmlspecialchars(explode(' ', $schoolName)[0]) ?> <em><?= htmlspecialchars(implode(' ', array_slice(explode(' ', $schoolName), 1))) ?></em></span>
      <?php endif; ?>
    </a>

    <!-- Hamburger -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-expanded="false">
      <div class="navbar-toggler-icon"><span></span></div>
    </button>

    <!-- Menu -->
    <div class="collapse navbar-collapse" id="navbarMain">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">

        <li class="nav-item">
          <a class="nav-link <?= isActive('/', $uri, $appBase) ?>" href="<?= APP_URL ?>/">Beranda</a>
        </li>

        <!-- Profil Dropdown -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?= isActive('/profil', $uri, $appBase) ?>" href="#" data-bs-toggle="dropdown" role="button">
            Profil
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="<?= APP_URL ?>/profil">
              <i class="fas fa-school"></i>Tentang Sekolah
            </a></li>
            <li><a class="dropdown-item" href="<?= APP_URL ?>/visi-misi">
              <i class="fas fa-bullseye"></i>Visi &amp; Misi
            </a></li>
            <li><a class="dropdown-item" href="<?= APP_URL ?>/sejarah">
              <i class="fas fa-history"></i>Sejarah Sekolah
            </a></li>
            <li><a class="dropdown-item" href="<?= APP_URL ?>/kepala-sekolah">
              <i class="fas fa-user-tie"></i>Kepala Sekolah
            </a></li>
          </ul>
        </li>

        <li class="nav-item">
          <a class="nav-link <?= isActive('/jurusan', $uri, $appBase) ?>" href="<?= APP_URL ?>/jurusan">Jurusan</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= isActive('/guru-staff', $uri, $appBase) ?>" href="<?= APP_URL ?>/guru-staff">Guru &amp; Staff</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= isActive('/berita', $uri, $appBase) ?>" href="<?= APP_URL ?>/berita">Berita</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= isActive('/galeri', $uri, $appBase) ?>" href="<?= APP_URL ?>/galeri">Galeri</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= isActive('/prestasi', $uri, $appBase) ?>" href="<?= APP_URL ?>/prestasi">Prestasi</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= isActive('/kontak', $uri, $appBase) ?>" href="<?= APP_URL ?>/kontak">Kontak</a>
        </li>

        <!-- SPMB Button -->
        <li class="nav-item nav-spmb ms-lg-2">
          <a class="nav-link" href="<?= APP_URL ?>/spmb">
            <i class="fas fa-pencil-alt me-1"></i>SPMB
          </a>
        </li>

        <!-- Theme Toggle -->
        <li class="nav-item ms-lg-2 d-flex align-items-center">
          <button id="themeToggle" title="Ganti Tema" aria-label="Toggle dark/light mode">
            <span class="theme-icon theme-icon-moon">🌙</span>
            <span class="theme-icon theme-icon-sun">☀️</span>
          </button>
        </li>

      </ul>
    </div><!-- collapse -->
  </div><!-- container -->
</nav>

<main>

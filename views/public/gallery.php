<?php
$pageTitle = 'Galeri - ' . ($settings['school_name'] ?? 'SMK Pertamaku');
require_once __DIR__ . '/../layouts/header.php';
$catFilter = htmlspecialchars($_GET['category'] ?? '');
?>

<div style="background:linear-gradient(135deg,var(--primary),var(--primary-dark));padding:60px 0;color:#fff;">
    <div class="container">
        <h1 class="fw-bold mb-2">Galeri Foto</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="<?= APP_URL ?>/" style="color:rgba(255,255,255,0.8);">Beranda</a></li><li class="breadcrumb-item active text-white">Galeri</li></ol>
        </nav>
    </div>
</div>

<section class="section">
    <div class="container">
        <!-- Category Filter -->
        <?php if (!empty($categories)): ?>
        <div class="d-flex flex-wrap gap-2 mb-5 justify-content-center">
            <a href="<?= APP_URL ?>/galeri" class="btn btn-sm <?= !$catFilter ? 'btn-primary' : 'btn-outline-primary' ?>">Semua</a>
            <?php foreach ($categories as $cat): ?>
                <a href="<?= APP_URL ?>/galeri?category=<?= urlencode($cat) ?>" class="btn btn-sm <?= $catFilter === $cat ? 'btn-primary' : 'btn-outline-primary' ?>">
                    <?= htmlspecialchars($cat) ?>
                </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Gallery Grid -->
        <?php if (empty($gallery)): ?>
            <div class="text-center py-5">
                <i class="fas fa-images" style="font-size:3rem;color:var(--text-muted);"></i>
                <p class="mt-3" style="color:var(--text-muted);">Belum ada foto galeri.</p>
            </div>
        <?php else: ?>
        <div class="gallery-grid">
            <?php foreach ($gallery as $item): ?>
            <div class="gallery-item" data-src="<?= UPLOAD_URL . htmlspecialchars($item['image']) ?>" data-title="<?= htmlspecialchars($item['title']) ?>">
                <?php if (!empty($item['image'])): ?>
                    <img src="<?= UPLOAD_URL . htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['title']) ?>">
                <?php else: ?>
                    <div class="img-placeholder" style="height:220px;"><i class="fas fa-image"></i></div>
                <?php endif; ?>
                <div class="gallery-overlay">
                    <i class="fas fa-expand"></i>
                    <span><?= htmlspecialchars($item['title']) ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <nav class="mt-5 d-flex justify-content-center">
            <ul class="pagination">
                <?php if ($currentPage > 1): ?>
                    <li class="page-item"><a class="page-link" href="<?= APP_URL ?>/galeri?page=<?= $currentPage-1 ?><?= $catFilter ? '&category='.urlencode($catFilter) : '' ?>"><i class="fas fa-chevron-left"></i></a></li>
                <?php endif; ?>
                <?php for ($p=1; $p<=$totalPages; $p++): ?>
                    <li class="page-item <?= $p==$currentPage ? 'active' : '' ?>"><a class="page-link" href="<?= APP_URL ?>/galeri?page=<?= $p ?><?= $catFilter ? '&category='.urlencode($catFilter) : '' ?>"><?= $p ?></a></li>
                <?php endfor; ?>
                <?php if ($currentPage < $totalPages): ?>
                    <li class="page-item"><a class="page-link" href="<?= APP_URL ?>/galeri?page=<?= $currentPage+1 ?><?= $catFilter ? '&category='.urlencode($catFilter) : '' ?>"><i class="fas fa-chevron-right"></i></a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

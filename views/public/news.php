<?php
$pageTitle = 'Berita - ' . ($settings['school_name'] ?? 'SMK Pertamaku');
require_once __DIR__ . '/../layouts/header.php';
$searchVal = htmlspecialchars($_GET['q'] ?? '');
$catVal = htmlspecialchars($_GET['category'] ?? '');
?>

<div style="background:linear-gradient(135deg,var(--primary),var(--primary-dark));padding:60px 0;color:#fff;">
    <div class="container">
        <h1 class="fw-bold mb-2">Berita & Informasi</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="<?= APP_URL ?>/" style="color:rgba(255,255,255,0.8);">Beranda</a></li><li class="breadcrumb-item active text-white">Berita</li></ol>
        </nav>
    </div>
</div>

<section class="section">
    <div class="container">
        <!-- Search & Filter -->
        <form method="GET" action="<?= APP_URL ?>/berita" class="mb-5">
            <div class="row g-3 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control" placeholder="Cari berita..." value="<?= $searchVal ?>">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="category" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat) ?>" <?= $catVal === $cat ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php if ($searchVal || $catVal): ?>
                <div class="col-md-2">
                    <a href="<?= APP_URL ?>/berita" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
                <?php endif; ?>
            </div>
        </form>

        <!-- News Grid -->
        <?php if (empty($news)): ?>
            <div class="text-center py-5">
                <i class="fas fa-newspaper" style="font-size:3rem;color:var(--text-muted);"></i>
                <p class="mt-3" style="color:var(--text-muted);">Belum ada berita yang ditemukan.</p>
            </div>
        <?php else: ?>
        <div class="row g-4">
            <?php foreach ($news as $article): ?>
            <div class="col-lg-4 col-md-6">
                <div class="card news-card h-100">
                    <div class="news-img">
                        <?php if (!empty($article['image'])): ?>
                            <img src="<?= UPLOAD_URL . htmlspecialchars($article['image']) ?>" alt="<?= htmlspecialchars($article['title']) ?>">
                        <?php else: ?>
                            <div class="img-placeholder" style="height:200px;"><i class="fas fa-newspaper"></i></div>
                        <?php endif; ?>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-primary"><?= htmlspecialchars($article['category']) ?></span>
                            <span class="news-date"><?= timeAgo($article['published_at']) ?></span>
                        </div>
                        <h5 class="flex-grow-1"><?= htmlspecialchars($article['title']) ?></h5>
                        <p style="color:var(--text-muted);font-size:0.9rem;"><?= htmlspecialchars(substr($article['excerpt'] ?? '', 0, 120)) ?>...</p>
                        <div class="d-flex align-items-center justify-content-between mt-2">
                            <small style="color:var(--text-muted);"><i class="fas fa-user me-1"></i><?= htmlspecialchars($article['author']) ?></small>
                            <a href="<?= APP_URL ?>/berita/<?= htmlspecialchars($article['slug']) ?>" class="btn btn-sm btn-primary">Baca</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <nav class="mt-5 d-flex justify-content-center">
            <ul class="pagination">
                <?php if ($currentPage > 1): ?>
                    <li class="page-item"><a class="page-link" href="<?= APP_URL ?>/berita?page=<?= $currentPage-1 ?><?= $searchVal ? '&q='.$searchVal : '' ?><?= $catVal ? '&category='.urlencode($catVal) : '' ?>"><i class="fas fa-chevron-left"></i></a></li>
                <?php endif; ?>
                <?php for ($p=1; $p<=$totalPages; $p++): ?>
                    <li class="page-item <?= $p==$currentPage ? 'active' : '' ?>"><a class="page-link" href="<?= APP_URL ?>/berita?page=<?= $p ?><?= $searchVal ? '&q='.$searchVal : '' ?><?= $catVal ? '&category='.urlencode($catVal) : '' ?>"><?= $p ?></a></li>
                <?php endfor; ?>
                <?php if ($currentPage < $totalPages): ?>
                    <li class="page-item"><a class="page-link" href="<?= APP_URL ?>/berita?page=<?= $currentPage+1 ?><?= $searchVal ? '&q='.$searchVal : '' ?><?= $catVal ? '&category='.urlencode($catVal) : '' ?>"><i class="fas fa-chevron-right"></i></a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

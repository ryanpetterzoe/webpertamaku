<?php
$pageTitle = 'Berita - ' . ($settings['school_name'] ?? 'SMK Pertamaku');
require_once __DIR__ . '/../layouts/header.php';
$searchVal  = htmlspecialchars(isset($_GET['q'])        ? $_GET['q']        : '');
$catVal     = htmlspecialchars(isset($_GET['category']) ? $_GET['category'] : '');
$activeProg = isset($activeProgram) ? (int)$activeProgram : 0;
?>

<div class="page-header">
    <div class="container">
        <h1 class="fw-bold mb-2">Berita &amp; Informasi</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-dark mb-0">
                <li class="breadcrumb-item"><a href="<?= APP_URL ?>/">Beranda</a></li>
                <li class="breadcrumb-item active">Berita</li>
            </ol>
        </nav>
    </div>
</div>

<section class="section">
    <div class="container">

        <!-- ── Filter Tabs per Jurusan ──────────────────────── -->
        <div class="mb-4">
            <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
                <span style="color:var(--text-muted);font-size:.83rem;font-weight:600;margin-right:4px;">Filter:</span>
                <a href="<?= APP_URL ?>/berita<?= $searchVal ? '?q='.$searchVal : '' ?>"
                   class="btn btn-sm <?= $activeProg === 0 && !$catVal ? 'btn-primary' : 'btn-outline-secondary' ?>"
                   style="border-radius:50px;font-size:.8rem;">
                    <i class="fas fa-globe me-1"></i>Semua
                </a>
                <?php foreach ($programs as $prog): ?>
                <a href="<?= APP_URL ?>/berita?program=<?= $prog['id'] ?><?= $searchVal ? '&q='.$searchVal : '' ?>"
                   class="btn btn-sm <?= $activeProg === (int)$prog['id'] ? 'btn-primary' : 'btn-outline-secondary' ?>"
                   style="border-radius:50px;font-size:.8rem;"
                   title="<?= htmlspecialchars($prog['name']) ?>">
                    <i class="fas fa-book me-1"></i><?= htmlspecialchars($prog['code'] ?: mb_substr($prog['name'],0,6)) ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- ── Search & Kategori ─────────────────────────────── -->
        <form method="GET" action="<?= APP_URL ?>/berita" class="mb-4">
            <?php if ($activeProg): ?><input type="hidden" name="program" value="<?= $activeProg ?>"><?php endif; ?>
            <div class="row g-3 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control"
                               placeholder="Cari berita..." value="<?= $searchVal ?>">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="category" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat) ?>" <?= $catVal === htmlspecialchars($cat) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php if ($searchVal || $catVal || $activeProg): ?>
                <div class="col-md-2">
                    <a href="<?= APP_URL ?>/berita" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-times me-1"></i>Reset
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </form>

        <!-- ── Info filter jurusan aktif ────────────────────── -->
        <?php if ($activeProg && !empty($programs)):
              $activeProgramData = null;
              foreach ($programs as $p) if ((int)$p['id'] === $activeProg) { $activeProgramData = $p; break; }
        ?>
        <?php if ($activeProgramData): ?>
        <div class="d-flex align-items-center gap-3 mb-4 p-3 rounded"
             style="background:var(--primary-glow);border:1px solid rgba(37,99,235,.2);">
            <div style="width:40px;height:40px;background:var(--gradient);border-radius:10px;
                        display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;">📚</div>
            <div>
                <div style="font-weight:700;color:var(--text);">
                    Berita Jurusan: <?= htmlspecialchars($activeProgramData['name']) ?>
                </div>
                <div style="font-size:.82rem;color:var(--text-muted);">
                    Menampilkan berita khusus jurusan ini + berita umum
                </div>
            </div>
            <a href="<?= APP_URL ?>/jurusan/<?= $activeProg ?>" class="btn btn-sm btn-outline-primary ms-auto" style="white-space:nowrap;">
                Info Jurusan <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
        <?php endif; endif; ?>

        <!-- ── News Grid ─────────────────────────────────────── -->
        <?php if (empty($news)): ?>
        <div class="text-center py-5">
            <i class="fas fa-newspaper" style="font-size:3rem;color:var(--text-muted);"></i>
            <p class="mt-3" style="color:var(--text-muted);">
                <?= $activeProg ? 'Belum ada berita untuk jurusan ini.' : 'Belum ada berita ditemukan.' ?>
            </p>
            <?php if ($searchVal || $catVal || $activeProg): ?>
            <a href="<?= APP_URL ?>/berita" class="btn btn-outline-primary mt-2">Tampilkan Semua</a>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="row g-4">
            <?php foreach ($news as $article): ?>
            <div class="col-lg-4 col-md-6">
                <div class="news-card h-100">
                    <!-- Thumbnail -->
                    <a href="<?= APP_URL ?>/berita/<?= htmlspecialchars($article['slug']) ?>"
                       class="news-card-img-wrap d-block" style="text-decoration:none;position:relative;">
                        <?php if (!empty($article['image'])): ?>
                        <img src="<?= UPLOAD_URL . htmlspecialchars($article['image']) ?>"
                             alt="<?= htmlspecialchars($article['title']) ?>"
                             class="news-card-img">
                        <?php else: ?>
                        <div class="news-img-placeholder"><i class="fas fa-newspaper"></i></div>
                        <?php endif; ?>
                        <!-- Badge jurusan di atas thumbnail -->
                        <?php if (!empty($article['program_name'])): ?>
                        <div style="position:absolute;top:10px;right:10px;
                                    background:var(--gradient);color:#fff;
                                    font-size:.68rem;font-weight:700;
                                    padding:3px 10px;border-radius:50px;box-shadow:0 2px 8px rgba(0,0,0,.2);">
                            <?= htmlspecialchars($article['program_code'] ?: $article['program_name']) ?>
                        </div>
                        <?php endif; ?>
                    </a>
                    <!-- Body -->
                    <div class="news-card-body">
                        <div class="news-card-meta">
                            <span class="news-cat-badge"><?= htmlspecialchars($article['category']) ?></span>
                            <span class="news-date"><i class="fas fa-clock"></i><?= timeAgo($article['published_at']) ?></span>
                        </div>
                        <h5>
                            <a href="<?= APP_URL ?>/berita/<?= htmlspecialchars($article['slug']) ?>" style="color:inherit;text-decoration:none;">
                                <?= htmlspecialchars($article['title']) ?>
                            </a>
                        </h5>
                        <p><?= htmlspecialchars(mb_substr($article['excerpt'] ?? '', 0, 110)) ?>...</p>
                        <div class="news-card-footer">
                            <a href="<?= APP_URL ?>/berita/<?= htmlspecialchars($article['slug']) ?>" class="news-read-link">
                                Baca <i class="fas fa-arrow-right"></i>
                            </a>
                            <small style="color:var(--text-muted);">
                                <i class="fas fa-user me-1"></i><?= htmlspecialchars($article['author']) ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if (isset($totalPages) && $totalPages > 1): ?>
        <nav class="mt-5 d-flex justify-content-center">
            <ul class="pagination">
                <?php if ($currentPage > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= APP_URL ?>/berita?page=<?= $currentPage-1 ?><?= $searchVal ? '&q='.$searchVal : '' ?><?= $catVal ? '&category='.urlencode($catVal) : '' ?><?= $activeProg ? '&program='.$activeProg : '' ?>"><i class="fas fa-chevron-left"></i></a>
                </li>
                <?php endif; ?>
                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <li class="page-item <?= $p == $currentPage ? 'active' : '' ?>">
                    <a class="page-link" href="<?= APP_URL ?>/berita?page=<?= $p ?><?= $searchVal ? '&q='.$searchVal : '' ?><?= $catVal ? '&category='.urlencode($catVal) : '' ?><?= $activeProg ? '&program='.$activeProg : '' ?>"><?= $p ?></a>
                </li>
                <?php endfor; ?>
                <?php if ($currentPage < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= APP_URL ?>/berita?page=<?= $currentPage+1 ?><?= $searchVal ? '&q='.$searchVal : '' ?><?= $catVal ? '&category='.urlencode($catVal) : '' ?><?= $activeProg ? '&program='.$activeProg : '' ?>"><i class="fas fa-chevron-right"></i></a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
        <?php endif; ?>

    </div>
</section>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

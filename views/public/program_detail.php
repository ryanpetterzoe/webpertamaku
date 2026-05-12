<?php
$pageTitle = htmlspecialchars($program['name'] ?? 'Detail Jurusan') . ' - ' . ($settings['school_name'] ?? 'SMK Pertamaku');
require_once __DIR__ . '/../layouts/header.php';
?>

<div style="background:linear-gradient(135deg,var(--primary),var(--primary-dark));padding:60px 0;color:#fff;">
    <div class="container">
        <h1 class="fw-bold mb-2"><?= htmlspecialchars($program['name'] ?? '') ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="<?= APP_URL ?>/" style="color:rgba(255,255,255,0.8);">Beranda</a></li><li class="breadcrumb-item"><a href="<?= APP_URL ?>/jurusan" style="color:rgba(255,255,255,0.8);">Jurusan</a></li><li class="breadcrumb-item active text-white"><?= htmlspecialchars($program['name'] ?? '') ?></li></ol>
        </nav>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="program-icon"><i class="<?= htmlspecialchars($program['icon'] ?? 'fas fa-book') ?>"></i></div>
                            <div>
                                <h2 style="color:var(--text);margin:0;"><?= htmlspecialchars($program['name'] ?? '') ?></h2>
                                <?php if (!empty($program['code'])): ?><span class="badge bg-primary"><?= htmlspecialchars($program['code']) ?></span><?php endif; ?>
                            </div>
                        </div>
                        <div style="color:var(--text);line-height:1.8;"><?= nl2br(htmlspecialchars($program['description'] ?? '')) ?></div>
                    </div>
                </div>

                <?php if (!empty($relatedNews)): ?>
                <div class="mt-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 style="color:var(--text);margin:0;">
                            <i class="fas fa-newspaper me-2" style="color:var(--primary);"></i>
                            Berita Jurusan Ini
                        </h5>
                        <a href="<?= APP_URL ?>/berita?program=<?= $program['id'] ?>"
                           class="btn btn-sm btn-outline-primary">
                            Semua Berita <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="row g-3">
                        <?php foreach ($relatedNews as $n): ?>
                        <div class="col-md-6">
                            <div class="news-card h-100">
                                <!-- Thumbnail -->
                                <a href="<?= APP_URL ?>/berita/<?= htmlspecialchars($n['slug']) ?>"
                                   class="news-card-img-wrap d-block" style="text-decoration:none;position:relative;">
                                    <?php if (!empty($n['image'])): ?>
                                    <img src="<?= UPLOAD_URL . htmlspecialchars($n['image']) ?>"
                                         alt="<?= htmlspecialchars($n['title']) ?>"
                                         class="news-card-img">
                                    <?php else: ?>
                                    <div class="news-img-placeholder"><i class="fas fa-newspaper"></i></div>
                                    <?php endif; ?>
                                    <!-- Badge: jurusan atau umum -->
                                    <?php if (!empty($n['program_name'])): ?>
                                    <div style="position:absolute;top:8px;right:8px;
                                                background:var(--gradient);color:#fff;
                                                font-size:.65rem;font-weight:700;
                                                padding:2px 8px;border-radius:50px;">
                                        <?= htmlspecialchars($n['program_code'] ?? $n['program_name']) ?>
                                    </div>
                                    <?php else: ?>
                                    <div style="position:absolute;top:8px;right:8px;
                                                background:rgba(0,0,0,.45);color:#fff;
                                                font-size:.65rem;font-weight:600;
                                                padding:2px 8px;border-radius:50px;">
                                        Umum
                                    </div>
                                    <?php endif; ?>
                                </a>
                                <div class="news-card-body">
                                    <div class="news-card-meta">
                                        <span class="news-cat-badge"><?= htmlspecialchars($n['category'] ?? 'Berita') ?></span>
                                        <span class="news-date"><i class="fas fa-clock"></i><?= timeAgo($n['published_at']) ?></span>
                                    </div>
                                    <h5 style="font-size:.9rem;">
                                        <a href="<?= APP_URL ?>/berita/<?= htmlspecialchars($n['slug']) ?>" style="color:inherit;text-decoration:none;">
                                            <?= htmlspecialchars($n['title']) ?>
                                        </a>
                                    </h5>
                                    <div class="news-card-footer">
                                        <a href="<?= APP_URL ?>/berita/<?= htmlspecialchars($n['slug']) ?>" class="news-read-link">
                                            Baca <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 style="color:var(--text);">Informasi Jurusan</h5>
                        <hr style="border-color:var(--border);">
                        <div class="d-flex justify-content-between mb-2"><span style="color:var(--text-muted);">Kode</span><strong style="color:var(--text);"><?= htmlspecialchars($program['code'] ?? '-') ?></strong></div>
                        <div class="d-flex justify-content-between mb-2"><span style="color:var(--text-muted);">Kuota</span><strong style="color:var(--text);"><?= (int)($program['quota'] ?? 36) ?> Siswa</strong></div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body text-center">
                        <h5 style="color:var(--text);">Tertarik Mendaftar?</h5>
                        <p style="color:var(--text-muted);font-size:0.9rem;">Daftarkan diri Anda sekarang dan pilih jurusan ini</p>
                        <a href="<?= APP_URL ?>/spmb/daftar" class="btn btn-primary w-100">Daftar Sekarang</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

<?php
$pageTitle = htmlspecialchars($news['title'] ?? 'Berita') . ' - ' . ($settings['school_name'] ?? 'SMK Pertamaku');
require_once __DIR__ . '/../layouts/header.php';
?>

<div style="background:linear-gradient(135deg,var(--primary),var(--primary-dark));padding:60px 0;color:#fff;">
    <div class="container">
        <h1 class="fw-bold mb-2" style="font-size:clamp(1.3rem,3vw,2rem);"><?= htmlspecialchars($news['title'] ?? '') ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="<?= APP_URL ?>/" style="color:rgba(255,255,255,0.8);">Beranda</a></li><li class="breadcrumb-item"><a href="<?= APP_URL ?>/berita" style="color:rgba(255,255,255,0.8);">Berita</a></li><li class="breadcrumb-item active text-white">Detail</li></ol>
        </nav>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">
                <article class="card">
                    <div class="card-body">
                        <!-- Meta -->
                        <div class="d-flex flex-wrap align-items-center gap-3 mb-4">
                            <span class="badge bg-primary"><?= htmlspecialchars($news['category'] ?? '') ?></span>
                            <span style="color:var(--text-muted);font-size:0.85rem;"><i class="fas fa-user me-1"></i><?= htmlspecialchars($news['author'] ?? 'Admin') ?></span>
                            <span style="color:var(--text-muted);font-size:0.85rem;"><i class="fas fa-calendar me-1"></i><?= formatDate($news['published_at']) ?></span>
                            <span style="color:var(--text-muted);font-size:0.85rem;"><i class="fas fa-eye me-1"></i><?= number_format($news['views'] ?? 0) ?> dilihat</span>
                        </div>
                        <!-- Image -->
                        <?php if (!empty($news['image'])): ?>
                            <img src="<?= UPLOAD_URL . htmlspecialchars($news['image']) ?>" alt="<?= htmlspecialchars($news['title'] ?? '') ?>" class="img-fluid rounded-xl mb-4" style="width:100%;max-height:420px;object-fit:cover;">
                        <?php endif; ?>
                        <!-- Content -->
                        <div style="color:var(--text);line-height:1.9;font-size:1.02rem;">
                            <?= $news['content'] ?? '' ?>
                        </div>
                        <!-- Share -->
                        <hr style="border-color:var(--border);margin:28px 0;">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span style="color:var(--text-muted);font-size:0.9rem;">Bagikan:</span>
                            <?php $shareUrl = urlencode(APP_URL . '/berita/' . ($news['slug'] ?? '')); $shareTitle = urlencode($news['title'] ?? ''); ?>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $shareUrl ?>" target="_blank" class="btn btn-sm" style="background:#1877f2;color:#fff;"><i class="fab fa-facebook-f me-1"></i>Facebook</a>
                            <a href="https://twitter.com/intent/tweet?url=<?= $shareUrl ?>&text=<?= $shareTitle ?>" target="_blank" class="btn btn-sm" style="background:#1da1f2;color:#fff;"><i class="fab fa-x-twitter me-1"></i>Twitter</a>
                            <a href="https://wa.me/?text=<?= $shareTitle ?>+<?= $shareUrl ?>" target="_blank" class="btn btn-sm" style="background:#25d366;color:#fff;"><i class="fab fa-whatsapp me-1"></i>WhatsApp</a>
                        </div>
                    </div>
                </article>
                <div class="mt-3">
                    <a href="<?= APP_URL ?>/berita" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-2"></i>Kembali ke Berita</a>
                </div>
            </div>
            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 style="color:var(--text);margin-bottom:16px;">Berita Terbaru</h5>
                        <?php
                        $db = getDB();
                        $latestRes = $db->query("SELECT id,title,slug,published_at,image FROM news WHERE is_published=1 AND id != " . (int)($news['id'] ?? 0) . " ORDER BY published_at DESC LIMIT 5");
                        $latestNews = $latestRes ? $latestRes->fetch_all(MYSQLI_ASSOC) : [];
                        foreach ($latestNews as $ln): ?>
                        <div class="d-flex gap-3 mb-3">
                            <div style="width:60px;height:55px;border-radius:8px;overflow:hidden;flex-shrink:0;background:var(--bg-secondary);">
                                <?php if (!empty($ln['image'])): ?><img src="<?= UPLOAD_URL . htmlspecialchars($ln['image']) ?>" style="width:100%;height:100%;object-fit:cover;" alt=""><?php else: ?><div class="img-placeholder h-100"><i class="fas fa-newspaper" style="font-size:1rem;"></i></div><?php endif; ?>
                            </div>
                            <div>
                                <a href="<?= APP_URL ?>/berita/<?= htmlspecialchars($ln['slug']) ?>" style="color:var(--text);font-size:0.88rem;font-weight:500;display:block;line-height:1.4;"><?= htmlspecialchars($ln['title']) ?></a>
                                <small style="color:var(--text-muted);"><?= timeAgo($ln['published_at']) ?></small>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

<?php
$pageTitle = 'Testimoni - ' . ($settings['school_name'] ?? 'SMK Pertamaku');
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1 class="fw-bold mb-2">Testimoni</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-dark mb-0">
                <li class="breadcrumb-item"><a href="<?= APP_URL ?>/">Beranda</a></li>
                <li class="breadcrumb-item active">Testimoni</li>
            </ol>
        </nav>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="section-heading text-center">
            <div class="section-badge justify-content-center"><i class="fas fa-quote-left me-1"></i> Kata Mereka</div>
            <h2>Apa Kata <span>Alumni & Orang Tua</span></h2>
            <p>Pengalaman nyata dari alumni dan orang tua siswa SMK Pertamaku</p>
        </div>

        <?php if (empty($testimonials)): ?>
        <div class="text-center py-5">
            <i class="fas fa-comment-slash" style="font-size:3rem;color:var(--text-muted);"></i>
            <p class="mt-3" style="color:var(--text-muted);">Belum ada testimoni.</p>
        </div>
        <?php else: ?>
        <div class="row g-4">
            <?php foreach ($testimonials as $t): ?>
            <div class="col-lg-4 col-md-6">
                <div class="testimonial-card">
                    <div class="testimonial-stars">
                        <?php for ($i = 0; $i < (int)($t['rating'] ?? 5); $i++): ?>★<?php endfor; ?>
                    </div>
                    <p class="testimonial-text">"<?= htmlspecialchars($t['content']) ?>"</p>
                    <div class="testimonial-author">
                        <?php if (!empty($t['photo'])): ?>
                        <img src="<?= UPLOAD_URL . htmlspecialchars($t['photo']) ?>" alt="<?= htmlspecialchars($t['name']) ?>" style="width:44px;height:44px;border-radius:50%;object-fit:cover;">
                        <?php else: ?>
                        <div class="testimonial-avatar"><?= strtoupper(substr($t['name'], 0, 1)) ?></div>
                        <?php endif; ?>
                        <div>
                            <div class="testimonial-name"><?= htmlspecialchars($t['name']) ?></div>
                            <div class="testimonial-pos"><?= htmlspecialchars($t['position'] ?? '') ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

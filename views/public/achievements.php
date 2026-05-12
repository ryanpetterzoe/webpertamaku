<?php
$pageTitle = 'Prestasi - ' . ($settings['school_name'] ?? 'SMK Pertamaku');
require_once __DIR__ . '/../layouts/header.php';
$levelFilter = htmlspecialchars($_GET['level'] ?? '');
$levels = ['sekolah','kabupaten','provinsi','nasional','internasional'];
?>

<div style="background:linear-gradient(135deg,var(--primary),var(--primary-dark));padding:60px 0;color:#fff;">
    <div class="container">
        <h1 class="fw-bold mb-2">Prestasi</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="<?= APP_URL ?>/" style="color:rgba(255,255,255,0.8);">Beranda</a></li><li class="breadcrumb-item active text-white">Prestasi</li></ol>
        </nav>
    </div>
</div>

<section class="section">
    <div class="container">
        <!-- Level Filter -->
        <div class="d-flex flex-wrap gap-2 mb-5 justify-content-center">
            <a href="<?= APP_URL ?>/prestasi" class="btn btn-sm <?= !$levelFilter ? 'btn-primary' : 'btn-outline-primary' ?>">Semua Tingkat</a>
            <?php foreach ($levels as $lvl): ?>
                <a href="<?= APP_URL ?>/prestasi?level=<?= $lvl ?>" class="btn btn-sm <?= $levelFilter === $lvl ? 'btn-primary' : 'btn-outline-primary' ?>">
                    <?= ucfirst($lvl) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if (empty($achievements)): ?>
            <div class="text-center py-5">
                <i class="fas fa-trophy" style="font-size:3rem;color:var(--text-muted);"></i>
                <p class="mt-3" style="color:var(--text-muted);">Belum ada prestasi yang tercatat.</p>
            </div>
        <?php else: ?>
        <div class="row g-4">
            <?php foreach ($achievements as $ach): ?>
            <div class="col-lg-6">
                <div class="achievement-card">
                    <div class="achievement-badge badge-<?= $ach['level'] ?>">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-start justify-content-between gap-2 mb-1">
                            <h6 style="color:var(--text);font-weight:600;margin:0;"><?= htmlspecialchars($ach['title']) ?></h6>
                            <div class="d-flex gap-2 flex-shrink-0">
                                <span class="badge badge-<?= $ach['level'] ?>"><?= ucfirst($ach['level']) ?></span>
                            </div>
                        </div>
                        <?php if (!empty($ach['description'])): ?>
                            <p style="color:var(--text-muted);font-size:0.88rem;margin-bottom:6px;"><?= htmlspecialchars($ach['description']) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($ach['year'])): ?>
                            <small style="color:var(--text-muted);"><i class="fas fa-calendar me-1"></i>Tahun <?= $ach['year'] ?></small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

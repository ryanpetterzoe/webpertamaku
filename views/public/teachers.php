<?php
$pageTitle = 'Guru & Staff - ' . ($settings['school_name'] ?? 'SMK Pertamaku');
require_once __DIR__ . '/../layouts/header.php';
?>

<div style="background:linear-gradient(135deg,var(--primary),var(--primary-dark));padding:60px 0;color:#fff;">
    <div class="container">
        <h1 class="fw-bold mb-2">Guru & Staff</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="<?= APP_URL ?>/" style="color:rgba(255,255,255,0.8);">Beranda</a></li><li class="breadcrumb-item active text-white">Guru & Staff</li></ol>
        </nav>
    </div>
</div>

<section class="section">
    <div class="container">
        <!-- Teachers -->
        <div class="section-heading">
            <div class="accent"></div>
            <h2>Tenaga Pendidik</h2>
            <p>Guru-guru berpengalaman dan berdedikasi tinggi</p>
        </div>
        <div class="row g-4 mb-5">
            <?php foreach ($teachers as $teacher): ?>
            <div class="col-lg-2 col-md-3 col-4">
                <div class="card teacher-card h-100">
                    <div class="card-body py-4">
                        <div class="teacher-photo">
                            <?php if (!empty($teacher['photo'])): ?>
                                <img src="<?= UPLOAD_URL . htmlspecialchars($teacher['photo']) ?>" alt="<?= htmlspecialchars($teacher['name']) ?>">
                            <?php else: ?>
                                <div class="photo-placeholder"><i class="fas fa-user"></i></div>
                            <?php endif; ?>
                        </div>
                        <h6><?= htmlspecialchars($teacher['name']) ?></h6>
                        <small class="d-block"><?= htmlspecialchars($teacher['position'] ?? '') ?></small>
                        <?php if (!empty($teacher['subject'])): ?>
                            <small style="color:var(--primary);"><?= htmlspecialchars($teacher['subject']) ?></small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Staff -->
        <?php if (!empty($staff)): ?>
        <div class="section-heading">
            <div class="accent"></div>
            <h2>Tenaga Kependidikan</h2>
            <p>Staff pendukung yang memastikan operasional sekolah berjalan lancar</p>
        </div>
        <div class="row g-4">
            <?php foreach ($staff as $s): ?>
            <div class="col-lg-2 col-md-3 col-4">
                <div class="card teacher-card h-100">
                    <div class="card-body py-4">
                        <div class="teacher-photo">
                            <?php if (!empty($s['photo'])): ?>
                                <img src="<?= UPLOAD_URL . htmlspecialchars($s['photo']) ?>" alt="<?= htmlspecialchars($s['name']) ?>">
                            <?php else: ?>
                                <div class="photo-placeholder"><i class="fas fa-user-friends"></i></div>
                            <?php endif; ?>
                        </div>
                        <h6><?= htmlspecialchars($s['name']) ?></h6>
                        <small class="d-block"><?= htmlspecialchars($s['position'] ?? '') ?></small>
                        <?php if (!empty($s['department'])): ?>
                            <small style="color:var(--primary);"><?= htmlspecialchars($s['department']) ?></small>
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

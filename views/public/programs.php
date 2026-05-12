<?php
$pageTitle = 'Program Keahlian - ' . ($settings['school_name'] ?? 'SMK Pertamaku');
require_once __DIR__ . '/../layouts/header.php';
?>

<div style="background:linear-gradient(135deg,var(--primary),var(--primary-dark));padding:60px 0;color:#fff;">
    <div class="container">
        <h1 class="fw-bold mb-2">Program Keahlian</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="<?= APP_URL ?>/" style="color:rgba(255,255,255,0.8);">Beranda</a></li><li class="breadcrumb-item active text-white">Jurusan</li></ol>
        </nav>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="section-heading">
            <div class="accent"></div>
            <h2>Pilih Jurusan Terbaik</h2>
            <p>Kami memiliki <?= count($programs) ?> program keahlian unggulan yang siap membawa Anda menuju karir impian</p>
        </div>
        <div class="row g-4">
            <?php foreach ($programs as $prog): ?>
            <div class="col-lg-6">
                <div class="card h-100" style="border-left:4px solid var(--primary);">
                    <div class="card-body d-flex gap-4">
                        <div class="program-icon flex-shrink-0" style="width:70px;height:70px;">
                            <i class="<?= htmlspecialchars($prog['icon'] ?? 'fas fa-book') ?>"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <h5 class="mb-0" style="color:var(--text);"><?= htmlspecialchars($prog['name']) ?></h5>
                                <?php if (!empty($prog['code'])): ?>
                                    <span class="badge bg-primary"><?= htmlspecialchars($prog['code']) ?></span>
                                <?php endif; ?>
                            </div>
                            <p style="color:var(--text-muted);font-size:0.9rem;margin-bottom:12px;"><?= htmlspecialchars(substr($prog['description'] ?? '', 0, 180)) ?>...</p>
                            <div class="d-flex align-items-center gap-3">
                                <span style="color:var(--text-muted);font-size:0.85rem;"><i class="fas fa-users me-1"></i>Kuota: <strong style="color:var(--text);"><?= $prog['quota'] ?></strong> siswa</span>
                                <a href="<?= APP_URL ?>/jurusan/<?= $prog['id'] ?>" class="btn btn-sm btn-primary">Detail <i class="fas fa-arrow-right ms-1"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<div class="cta-section">
    <div class="container text-center">
        <h2>Tertarik Bergabung?</h2>
        <p>Daftarkan diri Anda sekarang dan pilih jurusan impian Anda</p>
        <a href="<?= APP_URL ?>/spmb/daftar" class="btn btn-white btn-lg">Daftar Sekarang</a>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

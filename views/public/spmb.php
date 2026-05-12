<?php
$pageTitle = 'SPMB - ' . ($settings['school_name'] ?? 'SMK Pertamaku');
require_once __DIR__ . '/../layouts/header.php';
?>

<div style="background:linear-gradient(135deg,var(--primary),var(--primary-dark));padding:60px 0;color:#fff;">
    <div class="container">
        <h1 class="fw-bold mb-2">SPMB <?= htmlspecialchars($spmbSettings['academic_year'] ?? '') ?></h1>
        <p class="mb-3 opacity-75">Seleksi Penerimaan Murid Baru</p>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="<?= APP_URL ?>/" style="color:rgba(255,255,255,0.8);">Beranda</a></li><li class="breadcrumb-item active text-white">SPMB</li></ol>
        </nav>
    </div>
</div>

<section class="section">
    <div class="container">
        <!-- Status Banner -->
        <?php if (!empty($spmbSettings)): ?>
        <div class="card mb-5" style="border-left:5px solid <?= ($spmbSettings['is_active'] ?? 0) ? '#10b981' : '#ef4444' ?>;">
            <div class="card-body d-flex align-items-center gap-3">
                <div style="width:50px;height:50px;border-radius:50%;background:<?= ($spmbSettings['is_active'] ?? 0) ? '#d1fae5' : '#fee2e2' ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fas fa-<?= ($spmbSettings['is_active'] ?? 0) ? 'check' : 'times' ?>" style="color:<?= ($spmbSettings['is_active'] ?? 0) ? '#10b981' : '#ef4444' ?>;font-size:1.3rem;"></i>
                </div>
                <div>
                    <h5 style="color:var(--text);margin:0;">Pendaftaran <?= ($spmbSettings['is_active'] ?? 0) ? 'DIBUKA' : 'DITUTUP' ?></h5>
                    <p style="color:var(--text-muted);margin:4px 0 0;font-size:0.9rem;">
                        Periode: <?= formatDate($spmbSettings['open_date'] ?? '') ?> s/d <?= formatDate($spmbSettings['close_date'] ?? '') ?>
                    </p>
                </div>
                <?php if ($spmbSettings['is_active'] ?? 0): ?>
                <a href="<?= APP_URL ?>/spmb/daftar" class="btn btn-primary ms-auto">Daftar Sekarang</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-8">
                <!-- Info -->
                <?php if (!empty($spmbSettings['info'])): ?>
                <div class="card mb-4">
                    <div class="card-body">
                        <h4 style="color:var(--text);"><i class="fas fa-info-circle me-2 text-primary"></i>Informasi SPMB</h4>
                        <hr style="border-color:var(--border);">
                        <div style="color:var(--text);line-height:1.8;"><?= nl2br(htmlspecialchars($spmbSettings['info'])) ?></div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Requirements -->
                <?php if (!empty($spmbSettings['requirements'])): ?>
                <div class="card mb-4">
                    <div class="card-body">
                        <h4 style="color:var(--text);"><i class="fas fa-clipboard-list me-2 text-primary"></i>Persyaratan Pendaftaran</h4>
                        <hr style="border-color:var(--border);">
                        <div style="color:var(--text);line-height:2;"><?= nl2br(htmlspecialchars($spmbSettings['requirements'])) ?></div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Timeline -->
                <div class="card">
                    <div class="card-body">
                        <h4 style="color:var(--text);"><i class="fas fa-calendar-alt me-2 text-primary"></i>Jadwal SPMB</h4>
                        <hr style="border-color:var(--border);">
                        <div class="timeline">
                            <?php
                            $timeline = [
                                ['label' => 'Pendaftaran Dibuka', 'date' => $spmbSettings['open_date'] ?? null, 'icon' => 'fa-door-open', 'color' => '#10b981'],
                                ['label' => 'Batas Pendaftaran', 'date' => $spmbSettings['close_date'] ?? null, 'icon' => 'fa-door-closed', 'color' => '#f59e0b'],
                                ['label' => 'Pengumuman Hasil', 'date' => $spmbSettings['announcement_date'] ?? null, 'icon' => 'fa-bullhorn', 'color' => '#3b82f6'],
                            ];
                            foreach ($timeline as $tl): if (empty($tl['date'])) continue; ?>
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div style="width:44px;height:44px;border-radius:50%;background:<?= $tl['color'] ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="fas <?= $tl['icon'] ?>" style="color:#fff;"></i>
                                </div>
                                <div>
                                    <div style="color:var(--text);font-weight:600;"><?= $tl['label'] ?></div>
                                    <div style="color:var(--text-muted);font-size:0.88rem;"><?= formatDate($tl['date']) ?></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Programs & Quota -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 style="color:var(--text);"><i class="fas fa-list me-2 text-primary"></i>Kuota per Jurusan</h5>
                        <hr style="border-color:var(--border);">
                        <?php foreach ($programs as $prog): ?>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <div style="color:var(--text);font-size:0.9rem;font-weight:500;"><?= htmlspecialchars($prog['name']) ?></div>
                                <?php if (!empty($prog['code'])): ?><small style="color:var(--text-muted);"><?= htmlspecialchars($prog['code']) ?></small><?php endif; ?>
                            </div>
                            <span class="badge bg-primary"><?= $prog['quota'] ?> Kursi</span>
                        </div>
                        <?php endforeach; ?>
                        <hr style="border-color:var(--border);">
                        <div class="d-flex justify-content-between">
                            <strong style="color:var(--text);">Total Kuota</strong>
                            <strong style="color:var(--primary);"><?= $spmbSettings['quota_total'] ?? 144 ?> Siswa</strong>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card">
                    <div class="card-body">
                        <h5 style="color:var(--text);">Aksi</h5>
                        <?php if ($spmbSettings['is_active'] ?? 0): ?>
                        <a href="<?= APP_URL ?>/spmb/daftar" class="btn btn-primary w-100 mb-3">
                            <i class="fas fa-pencil-alt me-2"></i>Daftar Sekarang
                        </a>
                        <?php else: ?>
                        <button class="btn btn-secondary w-100 mb-3" disabled>Pendaftaran Belum Dibuka</button>
                        <?php endif; ?>
                        <a href="<?= APP_URL ?>/spmb/cek" class="btn btn-outline-primary w-100">
                            <i class="fas fa-search me-2"></i>Cek Status Pendaftaran
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

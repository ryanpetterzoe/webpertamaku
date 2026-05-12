<?php
$pageTitle = 'Agenda Kegiatan - ' . ($settings['school_name'] ?? 'SMK Pertamaku');
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1 class="fw-bold mb-2">Agenda Kegiatan</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-dark mb-0">
                <li class="breadcrumb-item"><a href="<?= APP_URL ?>/">Beranda</a></li>
                <li class="breadcrumb-item active">Agenda</li>
            </ol>
        </nav>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="section-heading text-center">
            <div class="section-badge justify-content-center"><i class="fas fa-calendar-alt me-1"></i> Jadwal</div>
            <h2>Agenda <span>Kegiatan Sekolah</span></h2>
            <p>Kalender kegiatan dan jadwal penting sekolah</p>
        </div>

        <?php if (empty($agendas)): ?>
        <div class="text-center py-5">
            <i class="fas fa-calendar-times" style="font-size:3rem;color:var(--text-muted);"></i>
            <p class="mt-3" style="color:var(--text-muted);">Belum ada agenda kegiatan.</p>
        </div>
        <?php else: ?>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="agenda-list">
                    <?php foreach ($agendas as $ag):
                        $isPast = strtotime($ag['start_date']) < strtotime('today');
                    ?>
                    <div class="agenda-item" style="<?= $isPast ? 'opacity:0.6;' : '' ?>">
                        <div class="agenda-date">
                            <span class="day"><?= date('d', strtotime($ag['start_date'])) ?></span>
                            <span class="month"><?= date('M', strtotime($ag['start_date'])) ?></span>
                        </div>
                        <div class="agenda-info flex-grow-1">
                            <h6>
                                <?= htmlspecialchars($ag['title']) ?>
                                <?php if ($isPast): ?>
                                <span class="badge ms-2" style="background:rgba(148,163,184,.15);color:var(--text-muted);font-size:.7rem;">Selesai</span>
                                <?php else: ?>
                                <span class="badge ms-2" style="background:rgba(34,197,94,.15);color:#16a34a;font-size:.7rem;">Upcoming</span>
                                <?php endif; ?>
                            </h6>
                            <?php if (!empty($ag['location'])): ?>
                            <small><i class="fas fa-map-marker-alt me-1" style="color:var(--primary);"></i><?= htmlspecialchars($ag['location']) ?></small>
                            <?php endif; ?>
                            <?php if (!empty($ag['end_date']) && $ag['end_date'] !== $ag['start_date']): ?>
                            <small class="ms-3"><i class="fas fa-calendar me-1" style="color:var(--text-muted);"></i>
                                s/d <?= date('d M Y', strtotime($ag['end_date'])) ?>
                            </small>
                            <?php endif; ?>
                            <?php if (!empty($ag['description'])): ?>
                            <p><?= htmlspecialchars($ag['description']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

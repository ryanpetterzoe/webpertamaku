<?php
$pageTitle = ($settings['school_name'] ?? 'SMK Pertamaku') . ' - ' . ($settings['school_tagline'] ?? 'Sekolah Menengah Kejuruan Unggulan');
require_once __DIR__ . '/../layouts/header.php';
?>

<!-- ========== HERO SLIDER ========== -->
<div id="heroCarousel" class="carousel slide hero-slider" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <?php foreach ($sliders as $i => $slide): ?>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="<?= $i ?>" <?= $i === 0 ? 'class="active"' : '' ?>></button>
        <?php endforeach; ?>
    </div>
    <div class="carousel-inner">
        <?php foreach ($sliders as $i => $slide): ?>
        <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
            <?php if (!empty($slide['image'])): ?>
                <img src="<?= UPLOAD_URL . htmlspecialchars($slide['image']) ?>" alt="<?= htmlspecialchars($slide['title'] ?? '') ?>">
            <?php else: ?>
                <div style="position:absolute;inset:0;background:linear-gradient(135deg,#1a56db,#0f172a);"></div>
            <?php endif; ?>
            <div class="carousel-caption text-center">
                <h1 class="text-white"><?= htmlspecialchars($slide['title'] ?? '') ?></h1>
                <p class="text-white"><?= htmlspecialchars($slide['subtitle'] ?? '') ?></p>
                <?php if (!empty($slide['button_text'])): ?>
                    <a href="<?= APP_URL . htmlspecialchars($slide['button_url'] ?? '/') ?>" class="btn btn-white btn-lg me-2">
                        <?= htmlspecialchars($slide['button_text']) ?>
                    </a>
                <?php endif; ?>
                <a href="<?= APP_URL ?>/spmb/daftar" class="btn btn-outline-white btn-lg">Daftar Sekarang</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>

<!-- ========== STATS BAR ========== -->
<section class="stats-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <i class="fas fa-user-graduate"></i>
                    <span class="stat-number" data-target="<?= (int)($settings['stats_students'] ?? 750) ?>">0</span>
                    <span class="stat-label">Siswa Aktif</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span class="stat-number" data-target="<?= (int)($settings['stats_teachers'] ?? 45) ?>">0</span>
                    <span class="stat-label">Guru & Staff</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <i class="fas fa-book-open"></i>
                    <span class="stat-number" data-target="<?= (int)($settings['stats_programs'] ?? 4) ?>">0</span>
                    <span class="stat-label">Program Keahlian</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-item">
                    <i class="fas fa-users"></i>
                    <span class="stat-number" data-target="<?= (int)($settings['stats_alumni'] ?? 2000) ?>">0</span>
                    <span class="stat-label">Alumni</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========== ABOUT SNIPPET ========== -->
<section class="section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-5">
                <div class="img-placeholder rounded-xl" style="height:380px;font-size:5rem;">
                    <i class="fas fa-school" style="color:var(--primary);"></i>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="section-heading text-start mb-3">
                    <div class="accent"></div>
                    <h2><?= htmlspecialchars($settings['school_name'] ?? 'SMK Pertamaku') ?></h2>
                </div>
                <p class="lead text-primary fw-semibold"><?= htmlspecialchars($settings['school_tagline'] ?? '') ?></p>
                <p style="color:var(--text-muted);"><?= htmlspecialchars(substr($settings['about_history'] ?? '', 0, 300)) ?>...</p>
                <div class="row g-3 my-3">
                    <div class="col-6">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-check-circle text-success"></i>
                            <span style="color:var(--text);font-size:0.9rem;">Akreditasi: <strong><?= htmlspecialchars($settings['school_accreditation'] ?? 'A') ?></strong></span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-check-circle text-success"></i>
                            <span style="color:var(--text);font-size:0.9rem;">NPSN: <strong><?= htmlspecialchars($settings['school_npsn'] ?? '-') ?></strong></span>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="<?= APP_URL ?>/profil" class="btn btn-primary">Tentang Kami <i class="fas fa-arrow-right ms-1"></i></a>
                    <a href="<?= APP_URL ?>/visi-misi" class="btn btn-outline-primary">Visi & Misi</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========== PROGRAMS ========== -->
<section class="section section-alt">
    <div class="container">
        <div class="section-heading">
            <div class="accent"></div>
            <h2>Program Keahlian</h2>
            <p>Pilih jurusan sesuai minat dan bakatmu. Kami menyediakan program keahlian terbaik.</p>
        </div>
        <div class="row g-4">
            <?php foreach ($programs as $prog): ?>
            <div class="col-lg-3 col-md-6">
                <a href="<?= APP_URL ?>/jurusan/<?= $prog['id'] ?>" class="text-decoration-none">
                    <div class="program-card h-100">
                        <div class="program-icon">
                            <i class="<?= htmlspecialchars($prog['icon'] ?? 'fas fa-book') ?>"></i>
                        </div>
                        <h5><?= htmlspecialchars($prog['name']) ?></h5>
                        <p><?= htmlspecialchars(substr($prog['description'] ?? '', 0, 100)) ?>...</p>
                        <div class="mt-3">
                            <span class="badge" style="background:rgba(26,86,219,0.1);color:var(--primary);">Kuota: <?= $prog['quota'] ?> Siswa</span>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-5">
            <a href="<?= APP_URL ?>/jurusan" class="btn btn-outline-primary btn-lg">Lihat Semua Jurusan</a>
        </div>
    </div>
</section>

<!-- ========== LATEST NEWS ========== -->
<section class="section">
    <div class="container">
        <div class="section-heading">
            <div class="accent"></div>
            <h2>Berita Terkini</h2>
            <p>Informasi dan kabar terbaru dari SMK Pertamaku</p>
        </div>
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
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-primary"><?= htmlspecialchars($article['category']) ?></span>
                            <span class="news-date"><i class="fas fa-clock me-1"></i><?= timeAgo($article['published_at']) ?></span>
                        </div>
                        <h5><?= htmlspecialchars($article['title']) ?></h5>
                        <p style="color:var(--text-muted);font-size:0.9rem;"><?= htmlspecialchars(substr($article['excerpt'] ?? '', 0, 120)) ?>...</p>
                        <a href="<?= APP_URL ?>/berita/<?= htmlspecialchars($article['slug']) ?>" class="btn btn-sm btn-outline-primary mt-2">Baca Selengkapnya</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-5">
            <a href="<?= APP_URL ?>/berita" class="btn btn-outline-primary btn-lg">Semua Berita</a>
        </div>
    </div>
</section>

<!-- ========== ACHIEVEMENTS ========== -->
<section class="section section-alt">
    <div class="container">
        <div class="section-heading">
            <div class="accent"></div>
            <h2>Prestasi Kami</h2>
            <p>Kebanggaan yang telah kami raih bersama</p>
        </div>
        <div class="row g-4">
            <?php foreach ($achievements as $ach): ?>
            <div class="col-lg-6">
                <div class="achievement-card">
                    <div class="achievement-badge badge-<?= $ach['level'] ?>">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div>
                        <h6 style="color:var(--text);font-weight:600;margin-bottom:4px;"><?= htmlspecialchars($ach['title']) ?></h6>
                        <p style="color:var(--text-muted);font-size:0.88rem;margin:0 0 6px;">
                            <?= htmlspecialchars(substr($ach['description'] ?? '', 0, 120)) ?>
                        </p>
                        <div class="d-flex gap-2">
                            <span class="badge badge-<?= $ach['level'] ?>" style="font-size:0.75rem;"><?= ucfirst($ach['level']) ?></span>
                            <?php if ($ach['year']): ?><span style="color:var(--text-muted);font-size:0.8rem;"><?= $ach['year'] ?></span><?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-5">
            <a href="<?= APP_URL ?>/prestasi" class="btn btn-outline-primary btn-lg">Lihat Semua Prestasi</a>
        </div>
    </div>
</section>

<!-- ========== TESTIMONIALS ========== -->
<?php if (!empty($testimonials)): ?>
<section class="section">
    <div class="container">
        <div class="section-heading">
            <div class="accent"></div>
            <h2>Kata Mereka</h2>
            <p>Apa yang dikatakan alumni dan orang tua siswa tentang kami</p>
        </div>
        <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php foreach (array_chunk($testimonials, 3) as $i => $chunk): ?>
                <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                    <div class="row g-4">
                        <?php foreach ($chunk as $t): ?>
                        <div class="col-lg-4">
                            <div class="testimonial-card">
                                <div class="stars"><?= str_repeat('★', (int)($t['rating'] ?? 5)) ?></div>
                                <p>"<?= htmlspecialchars($t['content']) ?>"</p>
                                <div class="author">
                                    <div class="author-photo img-placeholder" style="width:50px;height:50px;border-radius:50%;font-size:1.2rem;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="author-info">
                                        <strong><?= htmlspecialchars($t['name']) ?></strong>
                                        <small><?= htmlspecialchars($t['position'] ?? '') ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev" style="filter:invert(1);">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next" style="filter:invert(1);">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ========== AGENDA ========== -->
<?php if (!empty($agenda)): ?>
<section class="section section-alt">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="section-heading">
                    <div class="accent"></div>
                    <h2>Agenda Kegiatan</h2>
                    <p>Jadwal kegiatan sekolah yang akan datang</p>
                </div>
                <?php foreach ($agenda as $ag): ?>
                <div class="agenda-item">
                    <div class="agenda-date">
                        <span class="day"><?= date('d', strtotime($ag['start_date'])) ?></span>
                        <span class="month"><?= date('M', strtotime($ag['start_date'])) ?></span>
                    </div>
                    <div class="agenda-info">
                        <h6><?= htmlspecialchars($ag['title']) ?></h6>
                        <?php if (!empty($ag['location'])): ?>
                            <small><i class="fas fa-map-marker-alt me-1"></i><?= htmlspecialchars($ag['location']) ?></small>
                        <?php endif; ?>
                        <?php if (!empty($ag['description'])): ?>
                            <p style="color:var(--text-muted);font-size:0.85rem;margin:4px 0 0;"><?= htmlspecialchars(substr($ag['description'], 0, 100)) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ========== CTA SECTION ========== -->
<section class="cta-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2>Bergabunglah Bersama Kami!</h2>
                <p>Daftarkan diri Anda sekarang dan raih masa depan cerah bersama SMK Pertamaku. Pendaftaran online mudah dan cepat.</p>
                <a href="<?= APP_URL ?>/spmb/daftar" class="btn btn-white btn-lg me-3">
                    <i class="fas fa-pencil-alt me-2"></i>Daftar Sekarang
                </a>
                <a href="<?= APP_URL ?>/spmb/cek" class="btn btn-outline-white btn-lg">
                    <i class="fas fa-search me-2"></i>Cek Status Pendaftaran
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

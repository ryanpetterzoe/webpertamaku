<?php
$pageTitle = ($settings['school_name'] ?? 'SMK Pertamaku') . ' — ' . ($settings['school_tagline'] ?? 'Sekolah Menengah Kejuruan Unggulan');
require_once __DIR__ . '/../layouts/header.php';
?>

<!-- ═══════════════════════════════════════════════════════════
     HERO SLIDER
     ═══════════════════════════════════════════════════════════ -->
<div id="heroCarousel" class="carousel slide hero-slider" data-bs-ride="carousel" data-bs-interval="5500">

  <!-- Indicators -->
  <div class="carousel-indicators">
    <?php foreach ($sliders as $i => $slide): ?>
      <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="<?= $i ?>"
        <?= $i === 0 ? 'class="active" aria-current="true"' : '' ?>></button>
    <?php endforeach; ?>
  </div>

  <!-- Slides -->
  <div class="carousel-inner">
    <?php foreach ($sliders as $i => $slide): ?>
    <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
      <!-- BG shapes -->
      <div class="hero-shapes">
        <div class="hero-shape"></div>
        <div class="hero-shape"></div>
        <div class="hero-shape"></div>
      </div>
      <!-- BG image -->
      <?php if (!empty($slide['image'])): ?>
        <img src="<?= UPLOAD_URL . htmlspecialchars($slide['image']) ?>"
             alt="<?= htmlspecialchars($slide['title'] ?? '') ?>">
      <?php endif; ?>
      <!-- Caption -->
      <div class="carousel-caption text-center">
        <div class="animate-fadeInUp">
          <div class="hero-eyebrow">
            <i class="fas fa-star"></i>
            <?= htmlspecialchars($settings['school_name'] ?? 'SMK Pertamaku') ?>
            &nbsp;·&nbsp;Akreditasi <?= htmlspecialchars($settings['school_accreditation'] ?? 'A') ?>
          </div>
        </div>
        <h1 class="animate-fadeInUp animate-delay-1">
          <?= htmlspecialchars($slide['title'] ?? '') ?>
        </h1>
        <p class="animate-fadeInUp animate-delay-2">
          <?= htmlspecialchars($slide['subtitle'] ?? '') ?>
        </p>
        <div class="hero-actions animate-fadeInUp animate-delay-3">
          <?php if (!empty($slide['button_text'])): ?>
          <a href="<?= APP_URL . htmlspecialchars($slide['button_url'] ?? '/spmb') ?>"
             class="btn-hero-primary">
            <i class="fas fa-pencil-alt"></i>
            <?= htmlspecialchars($slide['button_text']) ?>
          </a>
          <?php endif; ?>
          <a href="<?= APP_URL ?>/profil" class="btn-hero-outline">
            <i class="fas fa-play-circle"></i>Tentang Kami
          </a>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Controls -->
  <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
    <span class="carousel-control-prev-icon"></span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
    <span class="carousel-control-next-icon"></span>
  </button>

  <!-- Scroll hint -->
  <div class="hero-scroll-hint">
    <span>Scroll</span>
    <i class="fas fa-chevron-down"></i>
  </div>
</div>


<!-- ═══════════════════════════════════════════════════════════
     STATS BAR
     ═══════════════════════════════════════════════════════════ -->
<section class="stats-section">
  <div class="container">
    <div class="row g-0">
      <?php
      $statsData = [
        ['icon'=>'fas fa-user-graduate', 'key'=>'stats_students', 'default'=>750,  'label'=>'Siswa Aktif'],
        ['icon'=>'fas fa-chalkboard-teacher','key'=>'stats_teachers','default'=>45, 'label'=>'Guru & Staff'],
        ['icon'=>'fas fa-book-open',     'key'=>'stats_programs', 'default'=>4,    'label'=>'Program Keahlian'],
        ['icon'=>'fas fa-users',         'key'=>'stats_alumni',   'default'=>2000, 'label'=>'Alumni'],
      ];
      foreach ($statsData as $s):
        $val = (int)($settings[$s['key']] ?? $s['default']);
      ?>
      <div class="col-6 col-md-3">
        <div class="stat-item">
          <div class="stat-icon-wrap"><i class="<?= $s['icon'] ?>"></i></div>
          <span class="stat-number" data-target="<?= $val ?>">0</span>
          <span class="stat-plus">+</span>
          <div class="stat-label"><?= $s['label'] ?></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>


<!-- ═══════════════════════════════════════════════════════════
     ABOUT SNIPPET
     ═══════════════════════════════════════════════════════════ -->
<section class="section">
  <div class="container">
    <div class="row align-items-center g-5">

      <!-- Visual side -->
      <div class="col-lg-5 d-none d-lg-block">
        <div class="about-visual">
          <div class="about-placeholder" style="height:400px;">
            <i class="fas fa-school" style="font-size:4rem; color:var(--primary); opacity:0.4;"></i>
            <span style="font-size:0.9rem; color:var(--text-muted);">Foto Gedung Sekolah</span>
          </div>
          <!-- Floating badges -->
          <div class="about-badge-float about-badge-1">
            <div class="badge-icon">🏆</div>
            <div class="badge-text">
              <strong>Akreditasi <?= htmlspecialchars($settings['school_accreditation'] ?? 'A') ?></strong>
              <span>BAN-S/M Terakreditasi</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Text side -->
      <div class="col-lg-7">
        <div class="section-badge">
          <i class="fas fa-graduation-cap"></i> Tentang Kami
        </div>
        <div class="section-heading mb-4">
          <h2><?= htmlspecialchars($settings['school_name'] ?? 'SMK Pertamaku') ?></h2>
          <p class="text-primary fw-semibold" style="font-size:1.05rem;">
            <?= htmlspecialchars($settings['school_tagline'] ?? '') ?>
          </p>
        </div>
        <p style="color:var(--text-secondary); line-height:1.85; margin-bottom:24px;">
          <?= htmlspecialchars(mb_substr($settings['about_history'] ?? '', 0, 280)) ?>...
        </p>

        <div class="row g-3 mb-28">
          <?php
          $checks = [
            ['icon'=>'fas fa-check', 'title'=>'Terakreditasi '  . ($settings['school_accreditation'] ?? 'A'), 'sub'=>'Badan Akreditasi Nasional'],
            ['icon'=>'fas fa-check', 'title'=>'NPSN: ' . ($settings['school_npsn'] ?? '-'), 'sub'=>'Nomor Pokok Sekolah Nasional'],
            ['icon'=>'fas fa-check', 'title'=>(int)($settings['stats_programs'] ?? 4) . ' Program Keahlian',  'sub'=>'Siap kerja & industri'],
            ['icon'=>'fas fa-check', 'title'=> (int)($settings['stats_alumni'] ?? 2000) . '+ Alumni Sukses',  'sub'=>'Berkarier di berbagai bidang'],
          ];
          foreach ($checks as $c):
          ?>
          <div class="col-sm-6">
            <div class="check-item">
              <div class="check-icon"><i class="<?= $c['icon'] ?>"></i></div>
              <div class="check-text">
                <strong><?= htmlspecialchars($c['title']) ?></strong>
                <span><?= htmlspecialchars($c['sub']) ?></span>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>

        <div class="d-flex gap-3 flex-wrap mt-4">
          <a href="<?= APP_URL ?>/profil" class="btn btn-primary">
            <i class="fas fa-arrow-right"></i> Selengkapnya
          </a>
          <a href="<?= APP_URL ?>/visi-misi" class="btn btn-outline-primary">Visi &amp; Misi</a>
        </div>
      </div>
    </div>
  </div>
</section>


<!-- ═══════════════════════════════════════════════════════════
     PROGRAM KEAHLIAN
     ═══════════════════════════════════════════════════════════ -->
<section class="section section-alt">
  <div class="container">
    <div class="text-center">
      <div class="section-badge justify-content-center">
        <i class="fas fa-book-open"></i> Program Keahlian
      </div>
      <div class="section-heading text-center">
        <h2>Pilih <span>Jurusan</span> Impianmu</h2>
        <p>4 program keahlian unggulan yang menyiapkan kamu untuk karir di era industri modern</p>
      </div>
    </div>
    <div class="row g-4">
      <?php foreach ($programs as $prog): ?>
      <div class="col-lg-3 col-sm-6">
        <a href="<?= APP_URL ?>/jurusan/<?= (int)$prog['id'] ?>" class="program-card text-decoration-none">
          <div class="program-card-icon">
            <i class="<?= htmlspecialchars($prog['icon'] ?? 'fas fa-book') ?>"></i>
          </div>
          <h5><?= htmlspecialchars($prog['name']) ?></h5>
          <p><?= htmlspecialchars(mb_substr($prog['description'] ?? '', 0, 90)) ?>...</p>
          <div class="program-meta">
            <span class="quota-badge"><i class="fas fa-users me-1"></i>Kuota <?= (int)$prog['quota'] ?></span>
            <span class="arrow-link"><i class="fas fa-arrow-right"></i></span>
          </div>
        </a>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-5">
      <a href="<?= APP_URL ?>/jurusan" class="btn btn-outline-primary btn-lg">
        <i class="fas fa-th-large me-2"></i>Lihat Semua Jurusan
      </a>
    </div>
  </div>
</section>


<!-- ═══════════════════════════════════════════════════════════
     BERITA TERKINI
     ═══════════════════════════════════════════════════════════ -->
<section class="section">
  <div class="container">
    <div class="row align-items-end mb-5">
      <div class="col-lg-7">
        <div class="section-badge">
          <i class="fas fa-newspaper"></i> Berita &amp; Informasi
        </div>
        <div class="section-heading mb-0">
          <h2>Kabar <span>Terkini</span></h2>
          <p>Ikuti perkembangan informasi dan kegiatan terbaru dari sekolah kami</p>
        </div>
      </div>
      <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
        <a href="<?= APP_URL ?>/berita" class="btn btn-outline-primary">
          Semua Berita <i class="fas fa-arrow-right ms-1"></i>
        </a>
      </div>
    </div>

    <div class="row g-4">
      <?php foreach ($news as $article): ?>
      <div class="col-lg-4 col-md-6">
        <div class="news-card h-100">
          <div class="news-card-img-wrap">
            <?php if (!empty($article['image'])): ?>
              <img src="<?= UPLOAD_URL . htmlspecialchars($article['image']) ?>"
                   alt="<?= htmlspecialchars($article['title']) ?>"
                   class="news-card-img">
            <?php else: ?>
              <div class="news-img-placeholder">
                <i class="fas fa-newspaper"></i>
              </div>
            <?php endif; ?>
          </div>
          <div class="news-card-body">
            <div class="news-card-meta">
              <span class="news-cat-badge"><?= htmlspecialchars($article['category']) ?></span>
              <span class="news-date">
                <i class="fas fa-clock"></i>
                <?= timeAgo($article['published_at']) ?>
              </span>
            </div>
            <h5>
              <a href="<?= APP_URL ?>/berita/<?= htmlspecialchars($article['slug']) ?>"
                 style="color:inherit;">
                <?= htmlspecialchars($article['title']) ?>
              </a>
            </h5>
            <p><?= htmlspecialchars(mb_substr($article['excerpt'] ?? '', 0, 110)) ?>...</p>
            <div class="news-card-footer">
              <a href="<?= APP_URL ?>/berita/<?= htmlspecialchars($article['slug']) ?>"
                 class="news-read-link">
                Baca Selengkapnya <i class="fas fa-arrow-right"></i>
              </a>
              <small style="color:var(--text-muted);">
                <i class="fas fa-eye me-1"></i><?= (int)($article['views'] ?? 0) ?>
              </small>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>


<!-- ═══════════════════════════════════════════════════════════
     PRESTASI
     ═══════════════════════════════════════════════════════════ -->
<?php if (!empty($achievements)): ?>
<section class="section section-alt">
  <div class="container">
    <div class="text-center">
      <div class="section-badge justify-content-center">
        <i class="fas fa-trophy"></i> Prestasi
      </div>
      <div class="section-heading text-center">
        <h2>Kebanggaan <span>Bersama</span></h2>
        <p>Pencapaian gemilang yang membuktikan kualitas pendidikan di SMK Pertamaku</p>
      </div>
    </div>
    <div class="row g-3">
      <?php foreach ($achievements as $ach): ?>
      <div class="col-lg-6">
        <div class="achievement-card">
          <div class="achievement-icon ach-<?= $ach['level'] ?>">🏆</div>
          <div class="flex-grow-1">
            <h6 style="color:var(--text); font-weight:700; margin-bottom:6px; font-size:0.95rem;">
              <?= htmlspecialchars($ach['title']) ?>
            </h6>
            <p style="color:var(--text-muted); font-size:0.84rem; margin:0 0 10px; line-height:1.6;">
              <?= htmlspecialchars(mb_substr($ach['description'] ?? '', 0, 120)) ?>
            </p>
            <div class="d-flex gap-2 align-items-center">
              <span class="ach-level-badge <?= $ach['level'] ?>"><?= ucfirst($ach['level']) ?></span>
              <?php if (!empty($ach['year'])): ?>
              <span style="color:var(--text-muted); font-size:0.78rem;">
                <i class="fas fa-calendar me-1"></i><?= $ach['year'] ?>
              </span>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-5">
      <a href="<?= APP_URL ?>/prestasi" class="btn btn-outline-primary btn-lg">
        <i class="fas fa-trophy me-2"></i>Semua Prestasi
      </a>
    </div>
  </div>
</section>
<?php endif; ?>


<!-- ═══════════════════════════════════════════════════════════
     TESTIMONIALS
     ═══════════════════════════════════════════════════════════ -->
<?php if (!empty($testimonials)): ?>
<section class="section">
  <div class="container">
    <div class="text-center">
      <div class="section-badge justify-content-center">
        <i class="fas fa-quote-left"></i> Testimoni
      </div>
      <div class="section-heading text-center">
        <h2>Kata <span>Mereka</span></h2>
        <p>Apa yang alumni dan orang tua siswa katakan tentang SMK Pertamaku</p>
      </div>
    </div>
    <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="6000">
      <div class="carousel-inner">
        <?php foreach (array_chunk($testimonials, 3) as $i => $chunk): ?>
        <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
          <div class="row g-4">
            <?php foreach ($chunk as $t): ?>
            <div class="col-lg-4 col-md-6">
              <div class="testimonial-card">
                <div class="testimonial-stars">
                  <?= str_repeat('★', (int)($t['rating'] ?? 5)) ?>
                </div>
                <p class="testimonial-text">"<?= htmlspecialchars($t['content']) ?>"</p>
                <div class="testimonial-author">
                  <div class="testimonial-avatar">
                    <?= strtoupper(substr($t['name'], 0, 1)) ?>
                  </div>
                  <div>
                    <div class="testimonial-name"><?= htmlspecialchars($t['name']) ?></div>
                    <div class="testimonial-pos"><?= htmlspecialchars($t['position'] ?? '') ?></div>
                  </div>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <!-- Dots -->
      <div class="text-center mt-4">
        <?php foreach (array_chunk($testimonials, 3) as $i => $chunk): ?>
        <button data-bs-target="#testimonialCarousel" data-bs-slide-to="<?= $i ?>"
          style="width:10px;height:10px;border-radius:50%;border:none;margin:0 4px;
          background:<?= $i===0 ? 'var(--primary)' : 'var(--border)' ?>;
          cursor:pointer;transition:background .3s;">
        </button>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>


<!-- ═══════════════════════════════════════════════════════════
     AGENDA KEGIATAN
     ═══════════════════════════════════════════════════════════ -->
<?php if (!empty($agenda)): ?>
<section class="section section-alt">
  <div class="container">
    <div class="row g-5 align-items-start">
      <div class="col-lg-5">
        <div class="section-badge">
          <i class="fas fa-calendar-alt"></i> Agenda
        </div>
        <div class="section-heading mb-0">
          <h2>Agenda <span>Kegiatan</span></h2>
          <p>Jadwal kegiatan sekolah yang akan datang. Jangan sampai terlewat!</p>
        </div>
        <a href="<?= APP_URL ?>/kontak" class="btn btn-primary mt-4">
          <i class="fas fa-envelope me-2"></i>Hubungi Kami
        </a>
      </div>
      <div class="col-lg-7">
        <div class="agenda-list">
          <?php foreach ($agenda as $ag): ?>
          <div class="agenda-item">
            <div class="agenda-date">
              <span class="day"><?= date('d', strtotime($ag['start_date'])) ?></span>
              <span class="month"><?= date('M', strtotime($ag['start_date'])) ?></span>
            </div>
            <div class="agenda-info">
              <h6><?= htmlspecialchars($ag['title']) ?></h6>
              <?php if (!empty($ag['location'])): ?>
              <small><i class="fas fa-map-marker-alt me-1" style="color:var(--primary);"></i><?= htmlspecialchars($ag['location']) ?></small>
              <?php endif; ?>
              <?php if (!empty($ag['description'])): ?>
              <p><?= htmlspecialchars(mb_substr($ag['description'], 0, 90)) ?></p>
              <?php endif; ?>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>


<!-- ═══════════════════════════════════════════════════════════
     CTA SECTION
     ═══════════════════════════════════════════════════════════ -->
<section class="cta-section">
  <div class="cta-shape cta-shape-1"></div>
  <div class="cta-shape cta-shape-2"></div>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <h2>Siap Bergabung Bersama Kami? 🎓</h2>
        <p>
          Daftarkan diri sekarang dan jadilah bagian dari keluarga besar SMK Pertamaku.
          Pendaftaran online mudah, cepat, dan bisa dilakukan kapan saja!
        </p>
        <div class="cta-actions">
          <a href="<?= APP_URL ?>/spmb/daftar" class="btn-hero-primary">
            <i class="fas fa-pencil-alt"></i> Daftar Sekarang
          </a>
          <a href="<?= APP_URL ?>/spmb/cek" class="btn-hero-outline">
            <i class="fas fa-search"></i> Cek Status Pendaftaran
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

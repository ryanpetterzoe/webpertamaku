<?php
$pageTitle = 'Tentang Sekolah - ' . ($settings['school_name'] ?? 'SMK Pertamaku');
require_once __DIR__ . '/../layouts/header.php';
?>

<!-- Page Header -->
<div style="background:linear-gradient(135deg,var(--primary),var(--primary-dark));padding:60px 0;color:#fff;">
    <div class="container">
        <h1 class="fw-bold mb-2">Tentang Sekolah</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="background:transparent;">
                <li class="breadcrumb-item"><a href="<?= APP_URL ?>/" style="color:rgba(255,255,255,0.8);">Beranda</a></li>
                <li class="breadcrumb-item active text-white">Tentang Sekolah</li>
            </ol>
        </nav>
    </div>
</div>

<section class="section">
    <div class="container">
        <!-- Tabs -->
        <ul class="nav nav-tabs mb-5" id="aboutTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profil">
                    <i class="fas fa-school me-2"></i>Profil
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#visiMisi">
                    <i class="fas fa-bullseye me-2"></i>Visi & Misi
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#sejarah">
                    <i class="fas fa-history me-2"></i>Sejarah
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#kepalaSekolah">
                    <i class="fas fa-user-tie me-2"></i>Kepala Sekolah
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#fasilitas">
                    <i class="fas fa-building me-2"></i>Fasilitas
                </button>
            </li>
        </ul>

        <div class="tab-content" id="aboutTabContent">

            <!-- Profil Tab -->
            <div class="tab-pane fade show active" id="profil">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <h3 class="fw-bold mb-4" style="color:var(--text);">Profil Sekolah</h3>
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <td style="color:var(--text-muted);width:40%;vertical-align:middle">Nama Sekolah</td>
                                    <td style="color:var(--text);font-weight:600"><?= htmlspecialchars($settings['school_name'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <td style="color:var(--text-muted);vertical-align:middle">NPSN</td>
                                    <td style="color:var(--text)"><?= htmlspecialchars($settings['school_npsn'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <td style="color:var(--text-muted);vertical-align:middle">Akreditasi</td>
                                    <td><span class="badge bg-success"><?= htmlspecialchars($settings['school_accreditation'] ?? 'A') ?></span></td>
                                </tr>
                                <tr>
                                    <td style="color:var(--text-muted);vertical-align:middle">Alamat</td>
                                    <td style="color:var(--text)"><?= htmlspecialchars($settings['school_address'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <td style="color:var(--text-muted);vertical-align:middle">Telepon</td>
                                    <td style="color:var(--text)"><?= htmlspecialchars($settings['school_phone'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <td style="color:var(--text-muted);vertical-align:middle">Email</td>
                                    <td style="color:var(--text)"><?= htmlspecialchars($settings['school_email'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <td style="color:var(--text-muted);vertical-align:middle">Website</td>
                                    <td style="color:var(--text)"><?= htmlspecialchars($settings['school_website'] ?? '-') ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-lg-6">
                        <div class="img-placeholder rounded-xl" style="height:300px;font-size:4rem;">
                            <i class="fas fa-school" style="color:var(--primary);"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Visi Misi Tab -->
            <div class="tab-pane fade" id="visiMisi">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h4 style="color:var(--primary);"><i class="fas fa-eye me-2"></i>Visi</h4>
                                <hr style="border-color:var(--border);">
                                <p style="color:var(--text);font-size:1.05rem;line-height:1.8;"><?= nl2br(htmlspecialchars($settings['about_vision'] ?? '')) ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h4 style="color:var(--primary);"><i class="fas fa-list-check me-2"></i>Misi</h4>
                                <hr style="border-color:var(--border);">
                                <div style="color:var(--text);line-height:1.8;"><?= nl2br(htmlspecialchars($settings['about_mission'] ?? '')) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sejarah Tab -->
            <div class="tab-pane fade" id="sejarah">
                <div class="row justify-content-center">
                    <div class="col-lg-9">
                        <h3 class="fw-bold mb-4" style="color:var(--text);">Sejarah Sekolah</h3>
                        <div class="card">
                            <div class="card-body" style="line-height:1.8;color:var(--text);">
                                <?= nl2br(htmlspecialchars($settings['about_history'] ?? 'Informasi sejarah belum tersedia.')) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kepala Sekolah Tab -->
            <div class="tab-pane fade" id="kepalaSekolah">
                <div class="row justify-content-center">
                    <div class="col-lg-9">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center g-4">
                                    <div class="col-md-3 text-center">
                                        <?php if (!empty($settings['principal_photo'])): ?>
                                            <img src="<?= UPLOAD_URL . htmlspecialchars($settings['principal_photo']) ?>" alt="Kepala Sekolah" class="rounded-circle" style="width:150px;height:150px;object-fit:cover;border:4px solid var(--primary);">
                                        <?php else: ?>
                                            <div class="img-placeholder rounded-circle mx-auto" style="width:150px;height:150px;font-size:3rem;border:4px solid var(--primary);">
                                                <i class="fas fa-user-tie" style="color:var(--primary);"></i>
                                            </div>
                                        <?php endif; ?>
                                        <h5 class="mt-3" style="color:var(--text);"><?= htmlspecialchars($settings['principal_name'] ?? '-') ?></h5>
                                        <span class="badge bg-primary">Kepala Sekolah</span>
                                    </div>
                                    <div class="col-md-9">
                                        <h4 style="color:var(--primary);">Sambutan Kepala Sekolah</h4>
                                        <hr style="border-color:var(--border);">
                                        <div style="color:var(--text);line-height:1.8;font-style:italic;">
                                            "<?= nl2br(htmlspecialchars($settings['principal_message'] ?? '')) ?>"
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fasilitas Tab -->
            <div class="tab-pane fade" id="fasilitas">
                <div class="section-heading mb-4">
                    <h3 style="color:var(--text);">Fasilitas Sekolah</h3>
                    <p style="color:var(--text-muted);">Kami menyediakan fasilitas lengkap untuk mendukung proses belajar mengajar</p>
                </div>
                <div class="row g-4">
                    <?php
                    $fasilitas = [
                        ['icon' => 'fas fa-desktop', 'name' => 'Lab Komputer', 'desc' => 'Laboratorium komputer modern dengan spesifikasi terkini'],
                        ['icon' => 'fas fa-wifi', 'name' => 'Internet WiFi', 'desc' => 'Akses internet cepat di seluruh area sekolah'],
                        ['icon' => 'fas fa-book', 'name' => 'Perpustakaan', 'desc' => 'Koleksi buku lengkap dan ruang baca nyaman'],
                        ['icon' => 'fas fa-futbol', 'name' => 'Lapangan Olahraga', 'desc' => 'Sarana olahraga lengkap untuk berbagai jenis kegiatan'],
                        ['icon' => 'fas fa-flask', 'name' => 'Laboratorium', 'desc' => 'Lab sains dan teknologi yang modern'],
                        ['icon' => 'fas fa-utensils', 'name' => 'Kantin Sehat', 'desc' => 'Kantin dengan makanan bergizi dan higienis'],
                    ];
                    foreach ($fasilitas as $f): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="program-card h-100">
                            <div class="program-icon"><i class="<?= $f['icon'] ?>"></i></div>
                            <h5><?= $f['name'] ?></h5>
                            <p><?= $f['desc'] ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

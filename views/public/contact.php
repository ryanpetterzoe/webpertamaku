<?php
$pageTitle = 'Kontak - ' . ($settings['school_name'] ?? 'SMK Pertamaku');
require_once __DIR__ . '/../layouts/header.php';
?>

<div style="background:linear-gradient(135deg,var(--primary),var(--primary-dark));padding:60px 0;color:#fff;">
    <div class="container">
        <h1 class="fw-bold mb-2">Kontak Kami</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="<?= APP_URL ?>/" style="color:rgba(255,255,255,0.8);">Beranda</a></li><li class="breadcrumb-item active text-white">Kontak</li></ol>
        </nav>
    </div>
</div>

<section class="section">
    <div class="container">
        <?php if (!empty($success)): ?>
        <div class="alert alert-success mb-4"><i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
        <div class="alert alert-danger mb-4"><i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="row g-5">
            <!-- Contact Info -->
            <div class="col-lg-4">
                <h3 class="fw-bold mb-4" style="color:var(--text);">Informasi Kontak</h3>
                <?php if (!empty($settings['school_address'])): ?>
                <div class="contact-info-item">
                    <div class="icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div><strong>Alamat</strong><span><?= htmlspecialchars($settings['school_address']) ?></span></div>
                </div>
                <?php endif; ?>
                <?php if (!empty($settings['school_phone'])): ?>
                <div class="contact-info-item">
                    <div class="icon"><i class="fas fa-phone"></i></div>
                    <div><strong>Telepon</strong><span><a href="tel:<?= preg_replace('/[^0-9+]/','',$settings['school_phone']) ?>"><?= htmlspecialchars($settings['school_phone']) ?></a></span></div>
                </div>
                <?php endif; ?>
                <?php if (!empty($settings['school_email'])): ?>
                <div class="contact-info-item">
                    <div class="icon"><i class="fas fa-envelope"></i></div>
                    <div><strong>Email</strong><span><a href="mailto:<?= htmlspecialchars($settings['school_email']) ?>"><?= htmlspecialchars($settings['school_email']) ?></a></span></div>
                </div>
                <?php endif; ?>
                <?php if (!empty($settings['whatsapp_number'])): ?>
                <div class="contact-info-item">
                    <div class="icon" style="background:rgba(37,211,102,0.15);"><i class="fab fa-whatsapp" style="color:#25d366;"></i></div>
                    <div><strong>WhatsApp</strong><span><a href="https://wa.me/<?= preg_replace('/[^0-9]/','',$settings['whatsapp_number']) ?>" target="_blank">Chat Sekarang</a></span></div>
                </div>
                <?php endif; ?>
                <!-- Social Media -->
                <?php
                $db = getDB();
                $smRes = $db->query("SELECT * FROM social_media WHERE is_active=1");
                $socialMedia = $smRes ? $smRes->fetch_all(MYSQLI_ASSOC) : [];
                if ($socialMedia):
                ?>
                <h6 class="mt-4 mb-3" style="color:var(--text);">Media Sosial</h6>
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach ($socialMedia as $sm): ?>
                    <a href="<?= htmlspecialchars($sm['url']) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary">
                        <i class="<?= htmlspecialchars($sm['icon'] ?? 'fas fa-link') ?> me-1"></i><?= htmlspecialchars($sm['platform']) ?>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Contact Form -->
            <div class="col-lg-8">
                <div class="contact-card">
                    <h3 class="fw-bold mb-4" style="color:var(--text);">Kirim Pesan</h3>
                    <form method="POST" action="<?= APP_URL ?>/kontak">
                        <input type="hidden" name="_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="Nama Anda" required maxlength="150" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" placeholder="email@contoh.com" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nomor HP/WhatsApp</label>
                                <input type="text" name="phone" class="form-control" placeholder="08xxxxxxxxxx" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Subjek</label>
                                <input type="text" name="subject" class="form-control" placeholder="Subjek pesan" value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Pesan <span class="text-danger">*</span></label>
                                <textarea name="message" class="form-control" rows="5" placeholder="Tulis pesan Anda..." required maxlength="1000"><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary px-5">
                                    <i class="fas fa-paper-plane me-2"></i>Kirim Pesan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Map -->
        <?php if (!empty($settings['maps_embed'])): ?>
        <div class="mt-5">
            <h4 class="fw-bold mb-3" style="color:var(--text);">Lokasi Sekolah</h4>
            <div style="border-radius:12px;overflow:hidden;height:400px;">
                <iframe src="<?= htmlspecialchars($settings['maps_embed']) ?>" width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

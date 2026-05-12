<?php
$settings = getSettings();
$schoolName = $settings['school_name'] ?? 'SMK Pertamaku';
$footerAbout = $settings['footer_about'] ?? '';
$address = $settings['school_address'] ?? '';
$phone = $settings['school_phone'] ?? '';
$email = $settings['school_email'] ?? '';
$whatsapp = $settings['whatsapp_number'] ?? '';
$currentYear = date('Y');

// Fetch programs for footer
$db = getDB();
$progRes = $db->query("SELECT id, name FROM programs WHERE is_active=1 ORDER BY sort_order LIMIT 6");
$footerPrograms = $progRes ? $progRes->fetch_all(MYSQLI_ASSOC) : [];

// Social media
$smRes = $db->query("SELECT * FROM social_media WHERE is_active=1");
$socialMedia = $smRes ? $smRes->fetch_all(MYSQLI_ASSOC) : [];
?>
</main>

<!-- Footer -->
<footer class="site-footer">
    <div class="container">
        <div class="row g-4">
            <!-- Column 1: School Info -->
            <div class="col-lg-3 col-md-6">
                <span class="footer-brand"><i class="fas fa-graduation-cap me-2"></i><?= htmlspecialchars($schoolName) ?></span>
                <p><?= htmlspecialchars($footerAbout) ?></p>
                <!-- Social Media -->
                <?php if ($socialMedia): ?>
                <div class="social-links">
                    <?php foreach ($socialMedia as $sm): ?>
                        <a href="<?= htmlspecialchars($sm['url']) ?>" target="_blank" rel="noopener" class="social-link" title="<?= htmlspecialchars($sm['platform']) ?>">
                            <i class="<?= htmlspecialchars($sm['icon'] ?? 'fas fa-link') ?>"></i>
                        </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Column 2: Quick Links -->
            <div class="col-lg-2 col-md-6">
                <h5>Tautan Cepat</h5>
                <a href="<?= APP_URL ?>/">Beranda</a>
                <a href="<?= APP_URL ?>/profil">Tentang Kami</a>
                <a href="<?= APP_URL ?>/berita">Berita</a>
                <a href="<?= APP_URL ?>/galeri">Galeri</a>
                <a href="<?= APP_URL ?>/prestasi">Prestasi</a>
                <a href="<?= APP_URL ?>/kontak">Kontak</a>
                <a href="<?= APP_URL ?>/spmb">SPMB / Pendaftaran</a>
            </div>

            <!-- Column 3: Programs -->
            <div class="col-lg-3 col-md-6">
                <h5>Program Keahlian</h5>
                <?php foreach ($footerPrograms as $prog): ?>
                    <a href="<?= APP_URL ?>/jurusan/<?= $prog['id'] ?>"><?= htmlspecialchars($prog['name']) ?></a>
                <?php endforeach; ?>
            </div>

            <!-- Column 4: Contact Info -->
            <div class="col-lg-4 col-md-6">
                <h5>Informasi Kontak</h5>
                <?php if ($address): ?>
                <div class="d-flex gap-2 mb-2">
                    <i class="fas fa-map-marker-alt mt-1" style="color:#94a3b8;width:18px;"></i>
                    <span style="color:var(--footer-text);font-size:0.9rem;"><?= htmlspecialchars($address) ?></span>
                </div>
                <?php endif; ?>
                <?php if ($phone): ?>
                <div class="d-flex gap-2 mb-2">
                    <i class="fas fa-phone mt-1" style="color:#94a3b8;width:18px;"></i>
                    <a href="tel:<?= preg_replace('/[^0-9+]/', '', $phone) ?>" style="color:var(--footer-text);font-size:0.9rem;"><?= htmlspecialchars($phone) ?></a>
                </div>
                <?php endif; ?>
                <?php if ($email): ?>
                <div class="d-flex gap-2 mb-2">
                    <i class="fas fa-envelope mt-1" style="color:#94a3b8;width:18px;"></i>
                    <a href="mailto:<?= htmlspecialchars($email) ?>" style="color:var(--footer-text);font-size:0.9rem;"><?= htmlspecialchars($email) ?></a>
                </div>
                <?php endif; ?>
                <?php if ($whatsapp): ?>
                <div class="d-flex gap-2 mb-2">
                    <i class="fab fa-whatsapp mt-1" style="color:#25d366;width:18px;"></i>
                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $whatsapp) ?>" target="_blank" style="color:var(--footer-text);font-size:0.9rem;">Chat via WhatsApp</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer Bottom -->
    <div class="footer-bottom">
        <div class="container">
            <p class="mb-0">
                &copy; <?= $currentYear ?> <?= htmlspecialchars($schoolName) ?>. Hak cipta dilindungi undang-undang.
                <span class="ms-2">Dibuat dengan <i class="fas fa-heart" style="color:#ef4444;"></i> untuk pendidikan</span>
            </p>
        </div>
    </div>
</footer>

<!-- Back to Top -->
<button id="backToTop" title="Kembali ke atas">
    <i class="fas fa-chevron-up"></i>
</button>

<!-- WhatsApp Float Button -->
<?php if ($whatsapp): ?>
<a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $whatsapp) ?>?text=Halo+<?= urlencode($schoolName) ?>%2C+saya+ingin+bertanya..."
   class="whatsapp-float" target="_blank" rel="noopener" title="Chat WhatsApp">
    <i class="fab fa-whatsapp"></i>
</a>
<?php endif; ?>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<script src="<?= APP_URL ?>/assets/js/main.js"></script>

<!-- Lightbox Modal -->
<div class="modal fade" id="lightboxModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:#000;border:none;padding:10px 16px;">
                <span id="lightboxCaption" style="color:#fff;"></span>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" style="background:#000;">
                <img id="lightboxImg" src="" alt="Gallery" style="width:100%;max-height:80vh;object-fit:contain;">
            </div>
        </div>
    </div>
</div>

</body>
</html>

<?php $adminPageTitle = 'Pengaturan Umum'; require_once __DIR__ . '/../layouts/admin_header.php'; ?>

<!-- Tabs -->
<ul class="nav nav-tabs mb-4" id="settingsTabs">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#generalTab"><i class="fas fa-school me-1"></i>Umum</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#aboutTab"><i class="fas fa-info-circle me-1"></i>Tentang</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#statsTab"><i class="fas fa-chart-bar me-1"></i>Statistik</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#seoTab"><i class="fas fa-search me-1"></i>SEO</button></li>
</ul>

<div class="tab-content">
    <!-- General -->
    <div class="tab-pane fade show active" id="generalTab">
        <div class="admin-card">
            <form method="POST" action="<?= APP_URL ?>/admin/settings/umum" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <input type="hidden" name="group" value="general">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Sekolah</label>
                        <input type="text" name="school_name" class="form-control" value="<?= htmlspecialchars($settings['school_name'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tagline</label>
                        <input type="text" name="school_tagline" class="form-control" value="<?= htmlspecialchars($settings['school_tagline'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">NPSN</label>
                        <input type="text" name="school_npsn" class="form-control" value="<?= htmlspecialchars($settings['school_npsn'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Akreditasi</label>
                        <select name="school_accreditation" class="form-select">
                            <?php foreach (['A','B','C','Belum Terakreditasi'] as $acc): ?>
                            <option <?= ($settings['school_accreditation'] ?? '') === $acc ? 'selected' : '' ?>><?= $acc ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Alamat Sekolah</label>
                        <textarea name="school_address" class="form-control" rows="2"><?= htmlspecialchars($settings['school_address'] ?? '') ?></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Telepon</label>
                        <input type="text" name="school_phone" class="form-control" value="<?= htmlspecialchars($settings['school_phone'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Email</label>
                        <input type="email" name="school_email" class="form-control" value="<?= htmlspecialchars($settings['school_email'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">WhatsApp</label>
                        <input type="text" name="whatsapp_number" class="form-control" placeholder="62812xxxxxxx" value="<?= htmlspecialchars($settings['whatsapp_number'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Logo Sekolah</label>
                        <?php if (!empty($settings['school_logo'])): ?>
                        <div class="mb-2"><img src="<?= UPLOAD_URL . htmlspecialchars($settings['school_logo']) ?>" alt="Logo" style="height:50px;border-radius:6px;border:1px solid var(--border);padding:4px;background:var(--bg);"></div>
                        <?php endif; ?>
                        <input type="file" name="school_logo_file" class="form-control image-upload-input" accept="image/*" data-preview="#logoPreview">
                        <img id="logoPreview" src="" style="display:none;height:50px;margin-top:8px;border-radius:6px;">
                    </div>

                    <!-- Foto Gedung -->
                    <div class="col-md-6">
                        <label class="form-label">
                            <i class="fas fa-building me-1" style="color:var(--primary);"></i>
                            Foto Gedung Sekolah
                        </label>
                        <small class="form-text" style="display:block;margin-bottom:6px;">
                            Ditampilkan di halaman Tentang Sekolah &amp; Beranda. Rasio landscape (16:9) disarankan.
                        </small>
                        <?php if (!empty($settings['school_building_photo'])): ?>
                        <div class="mb-2" style="position:relative;">
                            <img src="<?= UPLOAD_URL . htmlspecialchars($settings['school_building_photo']) ?>"
                                 alt="Foto Gedung"
                                 style="width:100%;max-height:140px;object-fit:cover;border-radius:8px;border:1px solid var(--border);">
                            <div style="position:absolute;top:6px;right:6px;background:rgba(0,0,0,.5);color:#fff;font-size:.7rem;padding:2px 8px;border-radius:10px;">Foto saat ini</div>
                        </div>
                        <?php endif; ?>
                        <input type="file" name="school_building_photo_file" id="buildingPhotoInput"
                               class="form-control" accept="image/*"
                               onchange="previewBuilding(this)">
                        <img id="buildingPreview" src="" style="display:none;width:100%;max-height:140px;object-fit:cover;border-radius:8px;margin-top:8px;border:2px solid var(--primary);">
                        <?php if (!empty($settings['school_building_photo'])): ?>
                        <small style="color:var(--text-muted);margin-top:4px;display:block;">
                            <i class="fas fa-info-circle me-1"></i>Kosongkan untuk mempertahankan foto yang ada.
                        </small>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Google Maps Embed URL</label>
                        <input type="text" name="maps_embed" class="form-control" placeholder="https://maps.google.com/maps?..." value="<?= htmlspecialchars($settings['maps_embed'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Teks Footer (Tentang)</label>
                        <textarea name="footer_about" class="form-control" rows="3"><?= htmlspecialchars($settings['footer_about'] ?? '') ?></textarea>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Simpan Pengaturan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- About -->
    <div class="tab-pane fade" id="aboutTab">
        <div class="admin-card">
            <form method="POST" action="<?= APP_URL ?>/admin/settings/umum" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <input type="hidden" name="group" value="about">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Visi Sekolah</label>
                        <textarea name="about_vision" class="form-control" rows="3"><?= htmlspecialchars($settings['about_vision'] ?? '') ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Misi Sekolah</label>
                        <textarea name="about_mission" class="form-control" rows="6"><?= htmlspecialchars($settings['about_mission'] ?? '') ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Sejarah Sekolah</label>
                        <textarea name="about_history" class="form-control" rows="6"><?= htmlspecialchars($settings['about_history'] ?? '') ?></textarea>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Nama Kepala Sekolah</label>
                        <input type="text" name="principal_name" class="form-control" value="<?= htmlspecialchars($settings['principal_name'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Foto Kepala Sekolah</label>
                        <?php if (!empty($settings['principal_photo'])): ?>
                        <div class="mb-2"><img src="<?= UPLOAD_URL . htmlspecialchars($settings['principal_photo']) ?>" alt="Principal" style="width:60px;height:60px;border-radius:50%;object-fit:cover;border:2px solid var(--border);"></div>
                        <?php endif; ?>
                        <input type="file" name="principal_photo_file" class="form-control" accept="image/*">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Sambutan Kepala Sekolah</label>
                        <textarea name="principal_message" class="form-control" rows="5"><?= htmlspecialchars($settings['principal_message'] ?? '') ?></textarea>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Stats -->
    <div class="tab-pane fade" id="statsTab">
        <div class="admin-card">
            <form method="POST" action="<?= APP_URL ?>/admin/settings/umum">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <input type="hidden" name="group" value="stats">
                <div class="row g-3">
                    <div class="col-md-3"><label class="form-label">Jumlah Siswa</label><input type="number" name="stats_students" class="form-control" value="<?= htmlspecialchars($settings['stats_students'] ?? '0') ?>"></div>
                    <div class="col-md-3"><label class="form-label">Jumlah Guru & Staff</label><input type="number" name="stats_teachers" class="form-control" value="<?= htmlspecialchars($settings['stats_teachers'] ?? '0') ?>"></div>
                    <div class="col-md-3"><label class="form-label">Jumlah Jurusan</label><input type="number" name="stats_programs" class="form-control" value="<?= htmlspecialchars($settings['stats_programs'] ?? '0') ?>"></div>
                    <div class="col-md-3"><label class="form-label">Jumlah Alumni</label><input type="number" name="stats_alumni" class="form-control" value="<?= htmlspecialchars($settings['stats_alumni'] ?? '0') ?>"></div>
                </div>
                <div class="mt-4"><button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Simpan</button></div>
            </form>
        </div>
    </div>

    <!-- SEO -->
    <div class="tab-pane fade" id="seoTab">
        <div class="admin-card">
            <form method="POST" action="<?= APP_URL ?>/admin/settings/umum">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <input type="hidden" name="group" value="seo">
                <div class="row g-3">
                    <div class="col-12"><label class="form-label">Meta Title</label><input type="text" name="meta_title" class="form-control" value="<?= htmlspecialchars($settings['meta_title'] ?? '') ?>"></div>
                    <div class="col-12"><label class="form-label">Meta Description</label><textarea name="meta_description" class="form-control" rows="3"><?= htmlspecialchars($settings['meta_description'] ?? '') ?></textarea></div>
                    <div class="col-12"><label class="form-label">Google Analytics ID</label><input type="text" name="ga_code" class="form-control" placeholder="G-XXXXXXXXXX" value="<?= htmlspecialchars($settings['ga_code'] ?? '') ?>"></div>
                </div>
                <div class="mt-4"><button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Simpan</button></div>
            </form>
        </div>
    </div>
</div>

<script>
function previewBuilding(input) {
    var preview = document.getElementById('buildingPreview');
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/admin_footer.php'; ?>

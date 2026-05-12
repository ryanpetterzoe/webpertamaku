<?php $adminPageTitle = 'Pengaturan Umum'; require_once __DIR__ . '/../layouts/admin_header.php'; ?>

<!-- Tabs -->
<ul class="nav nav-tabs mb-4" id="settingsTabs">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#generalTab"><i class="fas fa-school me-1"></i>Umum</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#aboutTab"><i class="fas fa-info-circle me-1"></i>Tentang</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#statsTab"><i class="fas fa-chart-bar me-1"></i>Statistik</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#seoTab"><i class="fas fa-search me-1"></i>SEO</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#socialTab"><i class="fas fa-share-alt me-1"></i>Sosial Media</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#footerTab"><i class="fas fa-copyright me-1"></i>Footer</button></li>
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

    <!-- Sosial Media -->
    <div class="tab-pane fade" id="socialTab">
        <div class="admin-card">

            <!-- Flash messages -->
            <?php if (!empty($_SESSION['flash_success'])): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($_SESSION['flash_success']) ?><?php unset($_SESSION['flash_success']); ?></div>
            <?php endif; ?>
            <?php if (!empty($_SESSION['flash_error'])): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($_SESSION['flash_error']) ?><?php unset($_SESSION['flash_error']); ?></div>
            <?php endif; ?>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0"><i class="fas fa-share-alt me-2" style="color:var(--primary);"></i>Kelola Tombol Sosial Media</h5>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSocialModal">
                    <i class="fas fa-plus me-1"></i>Tambah
                </button>
            </div>

            <?php
            $db = getDB();
            $smRes = $db->query("SELECT * FROM social_media ORDER BY id ASC");
            $socialList = $smRes ? $smRes->fetch_all(MYSQLI_ASSOC) : [];
            ?>

            <?php if (empty($socialList)): ?>
                <div class="text-center py-5" style="color:var(--text-muted);">
                    <i class="fas fa-share-alt fa-3x mb-3 d-block" style="opacity:.3;"></i>
                    Belum ada sosial media. Klik <strong>Tambah</strong> untuk menambahkan.
                </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th style="width:50px;">Icon</th>
                            <th>Platform</th>
                            <th>URL</th>
                            <th>Class Icon</th>
                            <th style="width:100px;">Status</th>
                            <th style="width:120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($socialList as $sm): ?>
                        <tr>
                            <td><i class="<?= htmlspecialchars($sm['icon'] ?? 'fas fa-link') ?>" style="font-size:1.4rem;color:var(--primary);"></i></td>
                            <td><strong><?= htmlspecialchars($sm['platform']) ?></strong></td>
                            <td><a href="<?= htmlspecialchars($sm['url']) ?>" target="_blank" style="color:var(--primary);font-size:.85rem;" rel="noopener"><?= htmlspecialchars($sm['url']) ?></a></td>
                            <td><code style="font-size:.8rem;"><?= htmlspecialchars($sm['icon'] ?? '') ?></code></td>
                            <td>
                                <a href="<?= APP_URL ?>/admin/sosmed/toggle/<?= $sm['id'] ?>" class="badge <?= $sm['is_active'] ? 'bg-success' : 'bg-secondary' ?> text-decoration-none">
                                    <?= $sm['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                                </a>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary me-1"
                                    onclick="editSocial(<?= $sm['id'] ?>, '<?= htmlspecialchars(addslashes($sm['platform'])) ?>', '<?= htmlspecialchars(addslashes($sm['url'])) ?>', '<?= htmlspecialchars(addslashes($sm['icon'] ?? '')) ?>', <?= $sm['is_active'] ?>)"
                                    title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="<?= APP_URL ?>/admin/sosmed/hapus/<?= $sm['id'] ?>"
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Hapus sosial media ini?')"
                                   title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Icon hints -->
            <div class="mt-3 p-3 rounded" style="background:var(--bg-secondary);border:1px solid var(--border);font-size:.82rem;color:var(--text-muted);">
                <strong><i class="fas fa-info-circle me-1"></i>Referensi class icon Font Awesome:</strong><br>
                <div class="mt-2 d-flex gap-3 flex-wrap">
                    <?php foreach ([
                        ['fab fa-facebook-f','Facebook'],['fab fa-instagram','Instagram'],
                        ['fab fa-youtube','YouTube'],['fab fa-x-twitter','Twitter/X'],
                        ['fab fa-tiktok','TikTok'],['fab fa-whatsapp','WhatsApp'],
                        ['fab fa-telegram','Telegram'],['fab fa-linkedin-in','LinkedIn'],
                    ] as $ic): ?>
                    <span title="<?= $ic[1] ?>"><i class="<?= $ic[0] ?> me-1"></i><code><?= $ic[0] ?></code></span>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer / Copyright -->
    <div class="tab-pane fade" id="footerTab">
        <div class="admin-card">
            <form method="POST" action="<?= APP_URL ?>/admin/settings/umum">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <input type="hidden" name="group" value="footer">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold"><i class="fas fa-align-left me-1" style="color:var(--primary);"></i>Teks Tentang di Footer</label>
                        <small class="form-text d-block mb-1">Deskripsi singkat sekolah yang tampil di kolom pertama footer.</small>
                        <textarea name="footer_about" class="form-control" rows="3"><?= htmlspecialchars($settings['footer_about'] ?? '') ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold"><i class="fas fa-copyright me-1" style="color:var(--primary);"></i>Teks Copyright</label>
                        <small class="form-text d-block mb-1">
                            Teks yang tampil di baris bawah footer setelah <code>&copy; [tahun] [nama sekolah].</code><br>
                            Contoh: <em>Hak cipta dilindungi undang-undang.</em>
                        </small>
                        <input type="text" name="footer_copyright" class="form-control"
                               placeholder="Hak cipta dilindungi undang-undang."
                               value="<?= htmlspecialchars($settings['footer_copyright'] ?? 'Hak cipta dilindungi undang-undang.') ?>">
                    </div>
                    <div class="col-12">
                        <div class="p-3 rounded" style="background:var(--bg-secondary);border:1px solid var(--border);">
                            <small style="color:var(--text-muted);"><i class="fas fa-eye me-1"></i><strong>Preview tampilan footer:</strong></small>
                            <p class="mb-0 mt-2" style="font-size:.9rem;color:var(--text-muted);">
                                &copy; <?= date('Y') ?> <strong id="previewSchoolName"><?= htmlspecialchars($settings['school_name'] ?? 'SMK Pertamaku') ?></strong>.
                                <span id="previewCopyright"><?= htmlspecialchars($settings['footer_copyright'] ?? 'Hak cipta dilindungi undang-undang.') ?></span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Simpan Pengaturan Footer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Sosial Media -->
<div class="modal fade" id="addSocialModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Tambah Sosial Media</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= APP_URL ?>/admin/sosmed/simpan">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Platform <span class="text-danger">*</span></label>
                        <input type="text" name="platform" class="form-control" placeholder="cth: Instagram" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">URL <span class="text-danger">*</span></label>
                        <input type="url" name="url" class="form-control" placeholder="https://instagram.com/namaakun" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Class Icon Font Awesome <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="icon" id="addIconInput" class="form-control" placeholder="fab fa-instagram" required oninput="previewAddIcon(this.value)">
                            <span class="input-group-text"><i id="addIconPreview" class="fas fa-link fa-lg"></i></span>
                        </div>
                        <small class="form-text">Contoh: <code>fab fa-instagram</code>, <code>fab fa-facebook-f</code>, <code>fab fa-youtube</code></small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="is_active" class="form-select">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Sosial Media -->
<div class="modal fade" id="editSocialModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Sosial Media</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="editSocialForm" action="">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Platform <span class="text-danger">*</span></label>
                        <input type="text" name="platform" id="editPlatform" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">URL <span class="text-danger">*</span></label>
                        <input type="url" name="url" id="editUrl" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Class Icon Font Awesome <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="icon" id="editIcon" class="form-control" required oninput="previewEditIcon(this.value)">
                            <span class="input-group-text"><i id="editIconPreview" class="fas fa-link fa-lg"></i></span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="is_active" id="editIsActive" class="form-select">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Simpan Perubahan</button>
                </div>
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

// Live copyright preview
document.addEventListener('DOMContentLoaded', function() {
    var copyrightInput = document.querySelector('input[name="footer_copyright"]');
    var previewEl = document.getElementById('previewCopyright');
    if (copyrightInput && previewEl) {
        copyrightInput.addEventListener('input', function() {
            previewEl.textContent = this.value;
        });
    }
});

// Social media icon preview
function previewAddIcon(val) {
    var el = document.getElementById('addIconPreview');
    if (el) { el.className = val || 'fas fa-link'; }
}
function previewEditIcon(val) {
    var el = document.getElementById('editIconPreview');
    if (el) { el.className = val || 'fas fa-link'; }
}

// Open edit modal with data
function editSocial(id, platform, url, icon, isActive) {
    document.getElementById('editPlatform').value  = platform;
    document.getElementById('editUrl').value       = url;
    document.getElementById('editIcon').value      = icon;
    document.getElementById('editIsActive').value  = isActive ? '1' : '0';
    previewEditIcon(icon);
    document.getElementById('editSocialForm').action = '<?= APP_URL ?>/admin/sosmed/edit/' + id;
    var modal = new bootstrap.Modal(document.getElementById('editSocialModal'));
    modal.show();
}

// Auto-open sosmed tab if redirected with #social
if (window.location.hash === '#social') {
    var tab = document.querySelector('[data-bs-target="#socialTab"]');
    if (tab) { tab.click(); }
}
if (window.location.hash === '#footer') {
    var tab = document.querySelector('[data-bs-target="#footerTab"]');
    if (tab) { tab.click(); }
}
</script>

<?php require_once __DIR__ . '/../layouts/admin_footer.php'; ?>

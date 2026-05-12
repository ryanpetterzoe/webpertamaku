<?php
$isEdit = !empty($teacher);
$adminPageTitle = $isEdit ? 'Edit Data Guru' : 'Tambah Guru';
require_once __DIR__ . '/../layouts/admin_header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="admin-card">
            <form method="POST" action="<?= APP_URL ?>/admin/guru/<?= $isEdit ? 'edit/'.$teacher['id'] : 'tambah' ?>" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($teacher['name'] ?? '') ?>" placeholder="Nama lengkap beserta gelar">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">NIP</label>
                        <input type="text" name="nip" class="form-control" value="<?= htmlspecialchars($teacher['nip'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Jabatan</label>
                        <input type="text" name="position" class="form-control" placeholder="Kepala Sekolah, Guru, dll" value="<?= htmlspecialchars($teacher['position'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Mata Pelajaran</label>
                        <input type="text" name="subject" class="form-control" placeholder="Matematika, IPA, dll" value="<?= htmlspecialchars($teacher['subject'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Pendidikan Terakhir</label>
                        <input type="text" name="education" class="form-control" placeholder="S1 Pendidikan Matematika" value="<?= htmlspecialchars($teacher['education'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($teacher['email'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">No HP</label>
                        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($teacher['phone'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Urutan Tampil</label>
                        <input type="number" name="sort_order" class="form-control" value="<?= (int)($teacher['sort_order'] ?? 0) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Foto</label>
                        <?php if (!empty($teacher['photo'])): ?>
                        <div class="mb-2"><img src="<?= UPLOAD_URL . htmlspecialchars($teacher['photo']) ?>" style="width:70px;height:70px;border-radius:50%;object-fit:cover;border:2px solid var(--border);"></div>
                        <?php endif; ?>
                        <input type="file" name="photo" class="form-control image-upload-input" accept="image/*" data-preview="#teacherPhotoPreview">
                        <img id="teacherPhotoPreview" src="" style="display:none;width:70px;height:70px;border-radius:50%;object-fit:cover;margin-top:8px;">
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" <?= ($teacher['is_active'] ?? 1) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="isActive">Guru Aktif</label>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-3 mt-4">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Simpan</button>
                    <a href="<?= APP_URL ?>/admin/guru" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/admin_footer.php'; ?>

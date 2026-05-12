<?php
$isEdit = !empty($gallery);
$adminPageTitle = $isEdit ? 'Edit Foto Galeri' : 'Tambah Foto Galeri';
require_once __DIR__ . '/../layouts/admin_header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="admin-card">
            <form method="POST" action="<?= APP_URL ?>/admin/galeri/<?= $isEdit ? 'edit/'.$gallery['id'] : 'tambah' ?>" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <div class="mb-3">
                    <label class="form-label">Judul Foto <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($gallery['title'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Kategori</label>
                    <input type="text" name="category" class="form-control" placeholder="Umum, Kegiatan, Prestasi..." value="<?= htmlspecialchars($gallery['category'] ?? 'Umum') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($gallery['description'] ?? '') ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Upload Foto <?= !$isEdit ? '<span class="text-danger">*</span>' : '' ?></label>
                    <?php if ($isEdit && !empty($gallery['image'])): ?>
                    <div class="mb-2">
                        <img src="<?= UPLOAD_URL . htmlspecialchars($gallery['image']) ?>" alt="" style="max-height:180px;border-radius:8px;border:1px solid var(--border);">
                        <small class="d-block mt-1" style="color:var(--text-muted);">Biarkan kosong jika tidak ingin mengubah foto</small>
                    </div>
                    <?php endif; ?>
                    <input type="file" name="image" class="form-control image-upload-input" accept="image/*" data-preview="#imgPreview" <?= !$isEdit ? 'required' : '' ?>>
                    <img id="imgPreview" src="" style="display:none;max-height:180px;margin-top:8px;border-radius:8px;">
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_published" id="isPublished" value="1" <?= ($gallery['is_published'] ?? 1) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="isPublished">Tampilkan di Galeri</label>
                    </div>
                </div>
                <div class="d-flex gap-3">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Simpan</button>
                    <a href="<?= APP_URL ?>/admin/galeri" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/admin_footer.php'; ?>

<?php
$isEdit = !empty($slider);
$adminPageTitle = $isEdit ? 'Edit Slider' : 'Tambah Slider';
require_once __DIR__ . '/../layouts/admin_header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="admin-card">
            <form method="POST" action="<?= APP_URL ?>/admin/slider/<?= $isEdit ? 'edit/'.$slider['id'] : 'tambah' ?>" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Judul Slider</label>
                        <input type="text" name="title" class="form-control" placeholder="Judul yang tampil di hero" value="<?= htmlspecialchars($slider['title'] ?? '') ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Subtitle / Teks Deskripsi</label>
                        <textarea name="subtitle" class="form-control" rows="3" placeholder="Teks pendukung yang tampil di bawah judul"><?= htmlspecialchars($slider['subtitle'] ?? '') ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Teks Tombol</label>
                        <input type="text" name="button_text" class="form-control" placeholder="Daftar Sekarang" value="<?= htmlspecialchars($slider['button_text'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">URL Tombol</label>
                        <input type="text" name="button_url" class="form-control" placeholder="/spmb" value="<?= htmlspecialchars($slider['button_url'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Urutan Tampil</label>
                        <input type="number" name="sort_order" class="form-control" value="<?= (int)($slider['sort_order'] ?? 1) ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Gambar Background <?= !$isEdit ? '<span class="text-danger">*</span>' : '' ?></label>
                        <?php if ($isEdit && !empty($slider['image'])): ?>
                        <div class="mb-2"><img src="<?= UPLOAD_URL . htmlspecialchars($slider['image']) ?>" alt="" style="max-height:150px;border-radius:8px;border:1px solid var(--border);"></div>
                        <small style="color:var(--text-muted);">Biarkan kosong jika tidak ingin mengganti gambar</small>
                        <?php endif; ?>
                        <input type="file" name="image" class="form-control image-upload-input mt-2" accept="image/*" data-preview="#sliderPreview" <?= !$isEdit ? 'required' : '' ?>>
                        <img id="sliderPreview" src="" style="display:none;max-height:150px;margin-top:8px;border-radius:8px;">
                        <small style="color:var(--text-muted);">Ukuran rekomendasi: 1920x900px. Format: JPG, PNG</small>
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" <?= ($slider['is_active'] ?? 1) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="isActive">Tampilkan Slider</label>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-3 mt-4">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Simpan</button>
                    <a href="<?= APP_URL ?>/admin/slider" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/admin_footer.php'; ?>

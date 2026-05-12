<?php $adminPageTitle = ($testimonial ? 'Edit' : 'Tambah') . ' Testimoni'; require_once __DIR__ . '/../layouts/admin_header.php'; ?>

<div class="admin-card" style="max-width:700px;">
    <form method="POST" action="<?= APP_URL ?>/admin/testimonial/<?= $testimonial ? 'edit/' . $testimonial['id'] : 'tambah' ?>" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="<?= htmlspecialchars(isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : '') ?>">

        <div class="row g-3 mb-3">
            <div class="col-md-8">
                <label class="form-label">Nama <span style="color:var(--danger)">*</span></label>
                <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($testimonial['name'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Rating</label>
                <select name="rating" class="form-select">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                    <option value="<?= $i ?>" <?= (($testimonial['rating'] ?? 5) == $i) ? 'selected' : '' ?>>
                        <?= str_repeat('★', $i) ?> (<?= $i ?>)
                    </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Jabatan / Keterangan</label>
                <input type="text" name="position" class="form-control" placeholder="Alumni RPL 2022, Software Engineer di..."
                       value="<?= htmlspecialchars($testimonial['position'] ?? '') ?>">
            </div>
            <div class="col-12">
                <label class="form-label">Isi Testimoni <span style="color:var(--danger)">*</span></label>
                <textarea name="content" class="form-control" rows="4" required><?= htmlspecialchars($testimonial['content'] ?? '') ?></textarea>
            </div>
            <div class="col-12">
                <label class="form-label">Foto (opsional)</label>
                <?php if (!empty($testimonial['photo'])): ?>
                <div class="mb-2"><img src="<?= UPLOAD_URL . htmlspecialchars($testimonial['photo']) ?>" style="width:60px;height:60px;border-radius:50%;object-fit:cover;"></div>
                <?php endif; ?>
                <input type="file" name="photo" class="form-control image-upload-input" accept="image/*" data-preview="#photoPreview">
                <img id="photoPreview" src="" style="display:none;width:60px;height:60px;border-radius:50%;object-fit:cover;margin-top:8px;">
            </div>
        </div>

        <div class="mb-4">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_published" id="pub"
                       <?= (!isset($testimonial) || $testimonial['is_published']) ? 'checked' : '' ?>>
                <label class="form-check-label" for="pub" style="color:var(--text);">Tampilkan ke publik</label>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan</button>
            <a href="<?= APP_URL ?>/admin/testimonial" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../layouts/admin_footer.php'; ?>

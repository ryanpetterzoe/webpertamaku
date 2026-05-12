<?php $adminPageTitle = ($achievement ? 'Edit' : 'Tambah') . ' Prestasi'; require_once __DIR__ . '/../layouts/admin_header.php'; ?>

<div class="admin-card" style="max-width:700px;">
    <form method="POST" action="<?= APP_URL ?>/admin/prestasi/<?= $achievement ? 'edit/' . $achievement['id'] : 'tambah' ?>" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="<?= htmlspecialchars(isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : '') ?>">

        <div class="mb-3">
            <label class="form-label">Judul Prestasi <span style="color:var(--danger)">*</span></label>
            <input type="text" name="title" class="form-control" required
                   value="<?= htmlspecialchars($achievement['title'] ?? '') ?>">
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label">Tingkat</label>
                <select name="level" class="form-select">
                    <?php foreach (['sekolah','kabupaten','provinsi','nasional','internasional'] as $lv): ?>
                    <option value="<?= $lv ?>" <?= (($achievement['level'] ?? 'sekolah') === $lv) ? 'selected' : '' ?>>
                        <?= ucfirst($lv) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Tahun</label>
                <input type="number" name="year" class="form-control" min="2000" max="2099"
                       value="<?= htmlspecialchars($achievement['year'] ?? date('Y')) ?>">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($achievement['description'] ?? '') ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Foto (opsional)</label>
            <?php if (!empty($achievement['image'])): ?>
            <div class="mb-2">
                <img src="<?= UPLOAD_URL . htmlspecialchars($achievement['image']) ?>" alt="Foto" style="height:80px;border-radius:8px;">
            </div>
            <?php endif; ?>
            <input type="file" name="image" class="form-control image-upload-input" accept="image/*" data-preview="#imgPreview">
            <img id="imgPreview" src="" style="display:none;height:80px;margin-top:8px;border-radius:8px;">
        </div>

        <div class="mb-4">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_published" id="is_published"
                       <?= (!isset($achievement) || $achievement['is_published']) ? 'checked' : '' ?>>
                <label class="form-check-label" for="is_published" style="color:var(--text);">Tampilkan ke publik</label>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan</button>
            <a href="<?= APP_URL ?>/admin/prestasi" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../layouts/admin_footer.php'; ?>

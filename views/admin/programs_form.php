<?php
$isEdit = !empty($program);
$adminPageTitle = $isEdit ? 'Edit Jurusan' : 'Tambah Jurusan';
require_once __DIR__ . '/../layouts/admin_header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="admin-card">
            <form method="POST" action="<?= APP_URL ?>/admin/jurusan/<?= $isEdit ? 'edit/'.$program['id'] : 'tambah' ?>" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Nama Jurusan <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($program['name'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kode Jurusan</label>
                        <input type="text" name="code" class="form-control" placeholder="TKJ, RPL, dll" value="<?= htmlspecialchars($program['code'] ?? '') ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="5" placeholder="Deskripsi program keahlian..."><?= htmlspecialchars($program['description'] ?? '') ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Icon (Font Awesome class)</label>
                        <input type="text" name="icon" class="form-control" placeholder="fas fa-laptop-code" value="<?= htmlspecialchars($program['icon'] ?? 'fas fa-book') ?>">
                        <small style="color:var(--text-muted);">Contoh: fas fa-laptop-code, fas fa-calculator</small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Kuota Siswa</label>
                        <input type="number" name="quota" class="form-control" value="<?= (int)($program['quota'] ?? 36) ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Urutan Tampil</label>
                        <input type="number" name="sort_order" class="form-control" value="<?= (int)($program['sort_order'] ?? 0) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Gambar Jurusan</label>
                        <?php if (!empty($program['image'])): ?><div class="mb-2"><img src="<?= UPLOAD_URL . htmlspecialchars($program['image']) ?>" style="max-height:80px;border-radius:6px;"></div><?php endif; ?>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" <?= ($program['is_active'] ?? 1) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="isActive">Jurusan Aktif</label>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-3 mt-4">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Simpan</button>
                    <a href="<?= APP_URL ?>/admin/jurusan" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/admin_footer.php'; ?>

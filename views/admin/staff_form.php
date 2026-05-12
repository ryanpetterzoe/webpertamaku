<?php $adminPageTitle = ($staff ? 'Edit' : 'Tambah') . ' Staff'; require_once __DIR__ . '/../layouts/admin_header.php'; ?>

<div class="admin-card" style="max-width:700px;">
    <form method="POST" action="<?= APP_URL ?>/admin/staff/<?= $staff ? 'edit/' . $staff['id'] : 'tambah' ?>" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="<?= htmlspecialchars(isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : '') ?>">

        <div class="row g-3 mb-3">
            <div class="col-md-8">
                <label class="form-label">Nama Lengkap <span style="color:var(--danger)">*</span></label>
                <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($staff['name'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">NIP</label>
                <input type="text" name="nip" class="form-control" value="<?= htmlspecialchars($staff['nip'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Jabatan</label>
                <input type="text" name="position" class="form-control" value="<?= htmlspecialchars($staff['position'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Departemen / Bagian</label>
                <input type="text" name="department" class="form-control" value="<?= htmlspecialchars($staff['department'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($staff['email'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">No. Telepon</label>
                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($staff['phone'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Urutan Tampil</label>
                <input type="number" name="sort_order" class="form-control" value="<?= htmlspecialchars($staff['sort_order'] ?? '0') ?>">
            </div>
            <div class="col-md-8">
                <label class="form-label">Foto</label>
                <?php if (!empty($staff['photo'])): ?>
                <div class="mb-2"><img src="<?= UPLOAD_URL . htmlspecialchars($staff['photo']) ?>" style="width:60px;height:60px;border-radius:50%;object-fit:cover;"></div>
                <?php endif; ?>
                <input type="file" name="photo" class="form-control image-upload-input" accept="image/*" data-preview="#photoPreview">
                <img id="photoPreview" src="" style="display:none;width:60px;height:60px;border-radius:50%;object-fit:cover;margin-top:8px;">
            </div>
        </div>

        <div class="mb-4">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_active" id="isActive"
                       <?= (!isset($staff) || $staff['is_active']) ? 'checked' : '' ?>>
                <label class="form-check-label" for="isActive" style="color:var(--text);">Staff Aktif</label>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan</button>
            <a href="<?= APP_URL ?>/admin/staff" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../layouts/admin_footer.php'; ?>

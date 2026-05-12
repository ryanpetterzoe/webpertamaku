<?php $adminPageTitle = 'Pengaturan SPMB'; require_once __DIR__ . '/../layouts/admin_header.php'; ?>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="admin-card">
            <form method="POST" action="<?= APP_URL ?>/admin/spmb/pengaturan">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <?php if (!empty($spmb['id'])): ?>
                    <input type="hidden" name="id" value="<?= $spmb['id'] ?>">
                <?php endif; ?>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                        <input type="text" name="academic_year" class="form-control" placeholder="2025/2026" required value="<?= htmlspecialchars($spmb['academic_year'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tanggal Buka <span class="text-danger">*</span></label>
                        <input type="date" name="open_date" class="form-control" required value="<?= htmlspecialchars($spmb['open_date'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tanggal Tutup <span class="text-danger">*</span></label>
                        <input type="date" name="close_date" class="form-control" required value="<?= htmlspecialchars($spmb['close_date'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tanggal Pengumuman</label>
                        <input type="date" name="announcement_date" class="form-control" value="<?= htmlspecialchars($spmb['announcement_date'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Total Kuota</label>
                        <input type="number" name="quota_total" class="form-control" value="<?= (int)($spmb['quota_total'] ?? 144) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status Pendaftaran</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" <?= ($spmb['is_active'] ?? 0) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="isActive" style="color:var(--text);">Pendaftaran Aktif/Dibuka</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Informasi SPMB</label>
                        <textarea name="info" class="form-control" rows="4" placeholder="Informasi umum tentang SPMB yang tampil di halaman publik..."><?= htmlspecialchars($spmb['info'] ?? '') ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Persyaratan Pendaftaran</label>
                        <textarea name="requirements" class="form-control" rows="6" placeholder="1. Persyaratan pertama&#10;2. Persyaratan kedua&#10;..."><?= htmlspecialchars($spmb['requirements'] ?? '') ?></textarea>
                        <small style="color:var(--text-muted);">Satu persyaratan per baris</small>
                    </div>
                </div>
                <div class="mt-4 d-flex gap-3">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Simpan Pengaturan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/admin_footer.php'; ?>

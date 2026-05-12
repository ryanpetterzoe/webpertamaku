<?php $adminPageTitle = ($agenda ? 'Edit' : 'Tambah') . ' Agenda'; require_once __DIR__ . '/../layouts/admin_header.php'; ?>

<div class="admin-card" style="max-width:700px;">
    <form method="POST" action="<?= APP_URL ?>/admin/agenda/<?= $agenda ? 'edit/' . $agenda['id'] : 'tambah' ?>">
        <input type="hidden" name="_token" value="<?= htmlspecialchars(isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : '') ?>">

        <div class="mb-3">
            <label class="form-label">Judul Agenda <span style="color:var(--danger)">*</span></label>
            <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($agenda['title'] ?? '') ?>">
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label">Tanggal Mulai <span style="color:var(--danger)">*</span></label>
                <input type="date" name="start_date" class="form-control" required value="<?= htmlspecialchars($agenda['start_date'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Tanggal Selesai <span style="color:var(--text-muted);font-size:.8rem;">(opsional)</span></label>
                <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($agenda['end_date'] ?? '') ?>">
            </div>
            <div class="col-12">
                <label class="form-label">Lokasi</label>
                <input type="text" name="location" class="form-control" placeholder="Aula Sekolah / Online / dll"
                       value="<?= htmlspecialchars($agenda['location'] ?? '') ?>">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($agenda['description'] ?? '') ?></textarea>
        </div>

        <div class="mb-4">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_published" id="pub"
                       <?= (!isset($agenda) || $agenda['is_published']) ? 'checked' : '' ?>>
                <label class="form-check-label" for="pub" style="color:var(--text);">Tampilkan ke publik</label>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan</button>
            <a href="<?= APP_URL ?>/admin/agenda" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../layouts/admin_footer.php'; ?>

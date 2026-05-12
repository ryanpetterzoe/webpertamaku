<?php $adminPageTitle = 'Staff / Karyawan'; require_once __DIR__ . '/../layouts/admin_header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div></div>
    <a href="<?= APP_URL ?>/admin/staff/tambah" class="btn btn-primary btn-sm">
        <i class="fas fa-plus me-1"></i> Tambah Staff
    </a>
</div>

<div class="admin-table-wrapper">
    <div class="admin-table-header">
        <h5><i class="fas fa-users me-2"></i>Daftar Staff</h5>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead><tr><th>Foto</th><th>Nama</th><th>Jabatan</th><th>Departemen</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
                <?php if (empty($staff)): ?>
                <tr><td colspan="6" class="text-center py-4" style="color:var(--text-muted);">Belum ada data staff</td></tr>
                <?php else: ?>
                <?php foreach ($staff as $s): ?>
                <tr>
                    <td>
                        <?php if (!empty($s['photo'])): ?>
                        <img src="<?= UPLOAD_URL . htmlspecialchars($s['photo']) ?>" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
                        <?php else: ?>
                        <div style="width:40px;height:40px;border-radius:50%;background:var(--primary-glow);display:flex;align-items:center;justify-content:center;color:var(--primary);font-weight:700;">
                            <?= strtoupper(substr($s['name'], 0, 1)) ?>
                        </div>
                        <?php endif; ?>
                    </td>
                    <td style="color:var(--text);font-weight:500;"><?= htmlspecialchars($s['name']) ?></td>
                    <td style="color:var(--text-secondary);"><?= htmlspecialchars($s['position'] ?? '-') ?></td>
                    <td style="color:var(--text-secondary);"><?= htmlspecialchars($s['department'] ?? '-') ?></td>
                    <td><?= $s['is_active'] ? '<span class="badge badge-diterima">Aktif</span>' : '<span class="badge badge-ditolak">Nonaktif</span>' ?></td>
                    <td>
                        <a href="<?= APP_URL ?>/admin/staff/edit/<?= $s['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                        <a href="<?= APP_URL ?>/admin/staff/hapus/<?= $s['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="Hapus staff ini?"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/admin_footer.php'; ?>

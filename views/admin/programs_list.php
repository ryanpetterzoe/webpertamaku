<?php $adminPageTitle = 'Manajemen Jurusan'; require_once __DIR__ . '/../layouts/admin_header.php'; ?>

<div class="admin-table-wrapper">
    <div class="admin-table-header">
        <h5><i class="fas fa-book me-2"></i>Daftar Jurusan</h5>
        <a href="<?= APP_URL ?>/admin/jurusan/tambah" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i>Tambah Jurusan</a>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead><tr><th>Nama Jurusan</th><th>Kode</th><th>Kuota</th><th>Urutan</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
                <?php if (empty($programs)): ?>
                <tr><td colspan="6" class="text-center py-4" style="color:var(--text-muted);">Belum ada jurusan.</td></tr>
                <?php else: ?>
                <?php foreach ($programs as $prog): ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div style="width:40px;height:40px;border-radius:8px;background:rgba(26,86,219,0.1);display:flex;align-items:center;justify-content:center;color:var(--primary);">
                                <i class="<?= htmlspecialchars($prog['icon'] ?? 'fas fa-book') ?>"></i>
                            </div>
                            <div>
                                <div style="font-weight:600;color:var(--text);"><?= htmlspecialchars($prog['name']) ?></div>
                                <small style="color:var(--text-muted);"><?= htmlspecialchars(substr($prog['description'] ?? '', 0, 80)) ?>...</small>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge bg-primary"><?= htmlspecialchars($prog['code'] ?? '-') ?></span></td>
                    <td style="color:var(--text);"><?= (int)$prog['quota'] ?> siswa</td>
                    <td style="color:var(--text-muted);"><?= (int)$prog['sort_order'] ?></td>
                    <td><span class="badge <?= $prog['is_active'] ? 'bg-success' : 'bg-secondary' ?>"><?= $prog['is_active'] ? 'Aktif' : 'Nonaktif' ?></span></td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="<?= APP_URL ?>/admin/jurusan/edit/<?= $prog['id'] ?>" class="btn btn-xs btn-outline-primary" style="padding:3px 8px;font-size:0.75rem;"><i class="fas fa-edit"></i></a>
                            <a href="<?= APP_URL ?>/admin/jurusan/hapus/<?= $prog['id'] ?>" class="btn btn-xs btn-outline-danger" style="padding:3px 8px;font-size:0.75rem;" data-confirm="Hapus jurusan ini?"><i class="fas fa-trash"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/admin_footer.php'; ?>

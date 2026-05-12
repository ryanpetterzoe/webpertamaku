<?php $adminPageTitle = 'Prestasi'; require_once __DIR__ . '/../layouts/admin_header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div></div>
    <a href="<?= APP_URL ?>/admin/prestasi/tambah" class="btn btn-primary btn-sm">
        <i class="fas fa-plus me-1"></i> Tambah Prestasi
    </a>
</div>

<div class="admin-table-wrapper">
    <div class="admin-table-header">
        <h5><i class="fas fa-trophy me-2"></i>Daftar Prestasi</h5>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Tingkat</th>
                    <th>Tahun</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($achievements)): ?>
                <tr><td colspan="5" class="text-center py-4" style="color:var(--text-muted);">Belum ada prestasi</td></tr>
                <?php else: ?>
                <?php foreach ($achievements as $a): ?>
                <tr>
                    <td style="color:var(--text);font-weight:500;"><?= htmlspecialchars($a['title']) ?></td>
                    <td><span class="badge badge-<?= $a['level'] ?>" style="text-transform:capitalize;"><?= htmlspecialchars($a['level']) ?></span></td>
                    <td style="color:var(--text-secondary);"><?= htmlspecialchars($a['year'] ?? '-') ?></td>
                    <td>
                        <?php if ($a['is_published']): ?>
                        <span class="badge badge-diterima">Publik</span>
                        <?php else: ?>
                        <span class="badge badge-ditolak">Draft</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?= APP_URL ?>/admin/prestasi/edit/<?= $a['id'] ?>" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="<?= APP_URL ?>/admin/prestasi/hapus/<?= $a['id'] ?>" class="btn btn-sm btn-outline-danger"
                           data-confirm="Hapus prestasi ini?">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/admin_footer.php'; ?>

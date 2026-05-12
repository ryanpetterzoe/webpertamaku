<?php $adminPageTitle = 'Manajemen Guru'; require_once __DIR__ . '/../layouts/admin_header.php'; ?>

<div class="admin-table-wrapper">
    <div class="admin-table-header">
        <h5><i class="fas fa-chalkboard-teacher me-2"></i>Daftar Guru</h5>
        <a href="<?= APP_URL ?>/admin/guru/tambah" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i>Tambah Guru</a>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead><tr><th>Foto</th><th>Nama</th><th>NIP</th><th>Jabatan</th><th>Mata Pelajaran</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
                <?php if (empty($teachers)): ?>
                <tr><td colspan="7" class="text-center py-4" style="color:var(--text-muted);">Belum ada data guru.</td></tr>
                <?php else: ?>
                <?php foreach ($teachers as $teacher): ?>
                <tr>
                    <td>
                        <?php if (!empty($teacher['photo'])): ?>
                            <img src="<?= UPLOAD_URL . htmlspecialchars($teacher['photo']) ?>" alt="" style="width:44px;height:44px;border-radius:50%;object-fit:cover;border:2px solid var(--border);">
                        <?php else: ?>
                            <div class="no-photo" style="border-radius:50%;"><i class="fas fa-user"></i></div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="font-weight:600;color:var(--text);"><?= htmlspecialchars($teacher['name']) ?></div>
                        <?php if (!empty($teacher['email'])): ?><small style="color:var(--text-muted);"><?= htmlspecialchars($teacher['email']) ?></small><?php endif; ?>
                    </td>
                    <td style="color:var(--text-muted);font-size:0.85rem;"><?= htmlspecialchars($teacher['nip'] ?? '-') ?></td>
                    <td style="color:var(--text-muted);"><?= htmlspecialchars($teacher['position'] ?? '-') ?></td>
                    <td style="color:var(--text-muted);"><?= htmlspecialchars($teacher['subject'] ?? '-') ?></td>
                    <td><span class="badge <?= $teacher['is_active'] ? 'bg-success' : 'bg-secondary' ?>"><?= $teacher['is_active'] ? 'Aktif' : 'Nonaktif' ?></span></td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="<?= APP_URL ?>/admin/guru/edit/<?= $teacher['id'] ?>" class="btn btn-xs btn-outline-primary" style="padding:3px 8px;font-size:0.75rem;"><i class="fas fa-edit"></i></a>
                            <a href="<?= APP_URL ?>/admin/guru/hapus/<?= $teacher['id'] ?>" class="btn btn-xs btn-outline-danger" style="padding:3px 8px;font-size:0.75rem;" data-confirm="Hapus data guru ini?"><i class="fas fa-trash"></i></a>
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

<?php $adminPageTitle = 'Manajemen Galeri'; require_once __DIR__ . '/../layouts/admin_header.php'; ?>

<div class="admin-table-wrapper">
    <div class="admin-table-header">
        <h5><i class="fas fa-images me-2"></i>Daftar Galeri</h5>
        <a href="<?= APP_URL ?>/admin/galeri/tambah" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i>Tambah Foto</a>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead><tr><th>Foto</th><th>Judul</th><th>Kategori</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
                <?php if (empty($gallery)): ?>
                <tr><td colspan="6" class="text-center py-4" style="color:var(--text-muted);">Belum ada foto. <a href="<?= APP_URL ?>/admin/galeri/tambah">Tambah sekarang</a></td></tr>
                <?php else: ?>
                <?php foreach ($gallery as $item): ?>
                <tr>
                    <td>
                        <?php if (!empty($item['image'])): ?>
                            <img src="<?= UPLOAD_URL . htmlspecialchars($item['image']) ?>" alt="" class="gallery-thumb">
                        <?php else: ?>
                            <div class="no-photo"><i class="fas fa-image"></i></div>
                        <?php endif; ?>
                    </td>
                    <td style="color:var(--text);font-weight:500;"><?= htmlspecialchars($item['title']) ?></td>
                    <td><span class="badge bg-primary"><?= htmlspecialchars($item['category']) ?></span></td>
                    <td style="color:var(--text-muted);"><?= date('d/m/Y', strtotime($item['created_at'])) ?></td>
                    <td><span class="badge <?= $item['is_published'] ? 'bg-success' : 'bg-secondary' ?>"><?= $item['is_published'] ? 'Aktif' : 'Nonaktif' ?></span></td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="<?= APP_URL ?>/admin/galeri/edit/<?= $item['id'] ?>" class="btn btn-xs btn-outline-primary" style="padding:3px 8px;font-size:0.75rem;"><i class="fas fa-edit"></i></a>
                            <a href="<?= APP_URL ?>/admin/galeri/hapus/<?= $item['id'] ?>" class="btn btn-xs btn-outline-danger" style="padding:3px 8px;font-size:0.75rem;" data-confirm="Hapus foto ini?"><i class="fas fa-trash"></i></a>
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

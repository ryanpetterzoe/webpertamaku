<?php $adminPageTitle = 'Manajemen Slider Hero'; require_once __DIR__ . '/../layouts/admin_header.php'; ?>

<div class="admin-table-wrapper">
    <div class="admin-table-header">
        <h5><i class="fas fa-images me-2"></i>Daftar Slider</h5>
        <a href="<?= APP_URL ?>/admin/slider/tambah" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i>Tambah Slider</a>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead><tr><th>Gambar</th><th>Judul</th><th>Tombol</th><th>Urutan</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
                <?php if (empty($sliders)): ?>
                <tr><td colspan="6" class="text-center py-4" style="color:var(--text-muted);">Belum ada slider.</td></tr>
                <?php else: ?>
                <?php foreach ($sliders as $slide): ?>
                <tr>
                    <td>
                        <?php if (!empty($slide['image'])): ?>
                            <img src="<?= UPLOAD_URL . htmlspecialchars($slide['image']) ?>" alt="" style="width:100px;height:55px;object-fit:cover;border-radius:6px;border:1px solid var(--border);">
                        <?php else: ?>
                            <div class="no-photo" style="width:100px;height:55px;border-radius:6px;"><i class="fas fa-image"></i></div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="font-weight:600;color:var(--text);"><?= htmlspecialchars($slide['title'] ?? '') ?></div>
                        <small style="color:var(--text-muted);"><?= htmlspecialchars(substr($slide['subtitle'] ?? '', 0, 80)) ?></small>
                    </td>
                    <td>
                        <?php if (!empty($slide['button_text'])): ?>
                            <span class="badge bg-primary"><?= htmlspecialchars($slide['button_text']) ?></span>
                        <?php else: ?>
                            <span style="color:var(--text-muted);">-</span>
                        <?php endif; ?>
                    </td>
                    <td style="color:var(--text-muted);"><?= (int)$slide['sort_order'] ?></td>
                    <td><span class="badge <?= $slide['is_active'] ? 'bg-success' : 'bg-secondary' ?>"><?= $slide['is_active'] ? 'Aktif' : 'Nonaktif' ?></span></td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="<?= APP_URL ?>/admin/slider/edit/<?= $slide['id'] ?>" class="btn btn-xs btn-outline-primary" style="padding:3px 8px;font-size:0.75rem;"><i class="fas fa-edit"></i></a>
                            <a href="<?= APP_URL ?>/admin/slider/hapus/<?= $slide['id'] ?>" class="btn btn-xs btn-outline-danger" style="padding:3px 8px;font-size:0.75rem;" data-confirm="Hapus slider ini?"><i class="fas fa-trash"></i></a>
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

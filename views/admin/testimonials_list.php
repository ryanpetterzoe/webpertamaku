<?php $adminPageTitle = 'Testimonial'; require_once __DIR__ . '/../layouts/admin_header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div></div>
    <a href="<?= APP_URL ?>/admin/testimonial/tambah" class="btn btn-primary btn-sm">
        <i class="fas fa-plus me-1"></i> Tambah Testimoni
    </a>
</div>

<div class="admin-table-wrapper">
    <div class="admin-table-header">
        <h5><i class="fas fa-quote-left me-2"></i>Daftar Testimonial</h5>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead><tr><th>Nama</th><th>Jabatan</th><th>Rating</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
                <?php if (empty($testimonials)): ?>
                <tr><td colspan="5" class="text-center py-4" style="color:var(--text-muted);">Belum ada testimoni</td></tr>
                <?php else: ?>
                <?php foreach ($testimonials as $t): ?>
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <?php if (!empty($t['photo'])): ?>
                            <img src="<?= UPLOAD_URL . htmlspecialchars($t['photo']) ?>" style="width:36px;height:36px;border-radius:50%;object-fit:cover;">
                            <?php else: ?>
                            <div style="width:36px;height:36px;border-radius:50%;background:var(--primary-glow);display:flex;align-items:center;justify-content:center;color:var(--primary);font-weight:700;">
                                <?= strtoupper(substr($t['name'], 0, 1)) ?>
                            </div>
                            <?php endif; ?>
                            <span style="color:var(--text);font-weight:500;"><?= htmlspecialchars($t['name']) ?></span>
                        </div>
                    </td>
                    <td style="color:var(--text-secondary);"><?= htmlspecialchars($t['position'] ?? '-') ?></td>
                    <td style="color:#fbbf24;"><?= str_repeat('★', (int)($t['rating'] ?? 5)) ?></td>
                    <td><?= $t['is_published'] ? '<span class="badge badge-diterima">Publik</span>' : '<span class="badge badge-ditolak">Draft</span>' ?></td>
                    <td>
                        <a href="<?= APP_URL ?>/admin/testimonial/edit/<?= $t['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                        <a href="<?= APP_URL ?>/admin/testimonial/hapus/<?= $t['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="Hapus testimoni ini?"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/admin_footer.php'; ?>

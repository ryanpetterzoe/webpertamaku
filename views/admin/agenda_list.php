<?php $adminPageTitle = 'Agenda Kegiatan'; require_once __DIR__ . '/../layouts/admin_header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div></div>
    <a href="<?= APP_URL ?>/admin/agenda/tambah" class="btn btn-primary btn-sm">
        <i class="fas fa-plus me-1"></i> Tambah Agenda
    </a>
</div>

<div class="admin-table-wrapper">
    <div class="admin-table-header">
        <h5><i class="fas fa-calendar-alt me-2"></i>Daftar Agenda</h5>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead><tr><th>Judul</th><th>Tanggal</th><th>Lokasi</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
                <?php if (empty($agendas)): ?>
                <tr><td colspan="5" class="text-center py-4" style="color:var(--text-muted);">Belum ada agenda</td></tr>
                <?php else: ?>
                <?php foreach ($agendas as $ag): ?>
                <tr>
                    <td style="color:var(--text);font-weight:500;"><?= htmlspecialchars($ag['title']) ?></td>
                    <td style="color:var(--text-secondary);">
                        <?= date('d M Y', strtotime($ag['start_date'])) ?>
                        <?php if (!empty($ag['end_date']) && $ag['end_date'] !== $ag['start_date']): ?>
                        <small style="color:var(--text-muted);"> s/d <?= date('d M Y', strtotime($ag['end_date'])) ?></small>
                        <?php endif; ?>
                    </td>
                    <td style="color:var(--text-secondary);"><?= htmlspecialchars($ag['location'] ?? '-') ?></td>
                    <td>
                        <?php
                        $now = strtotime('today');
                        $start = strtotime($ag['start_date']);
                        if (!$ag['is_published']): ?>
                        <span class="badge badge-ditolak">Draft</span>
                        <?php elseif ($start >= $now): ?>
                        <span class="badge badge-verifikasi">Upcoming</span>
                        <?php else: ?>
                        <span class="badge" style="background:rgba(148,163,184,.15);color:var(--text-muted);">Selesai</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?= APP_URL ?>/admin/agenda/edit/<?= $ag['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                        <a href="<?= APP_URL ?>/admin/agenda/hapus/<?= $ag['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="Hapus agenda ini?"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/admin_footer.php'; ?>

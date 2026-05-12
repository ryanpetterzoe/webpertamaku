<?php $adminPageTitle = 'Dashboard'; require_once __DIR__ . '/../layouts/admin_header.php'; ?>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="admin-stat-card">
            <div class="stat-icon icon-blue"><i class="fas fa-newspaper"></i></div>
            <div class="stat-info"><div class="num"><?= $stats['news'] ?? 0 ?></div><div class="label">Total Berita</div></div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="admin-stat-card">
            <div class="stat-icon icon-purple"><i class="fas fa-images"></i></div>
            <div class="stat-info"><div class="num"><?= $stats['gallery'] ?? 0 ?></div><div class="label">Foto Galeri</div></div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="admin-stat-card">
            <div class="stat-icon icon-orange"><i class="fas fa-user-plus"></i></div>
            <div class="stat-info"><div class="num"><?= $stats['spmb_pending'] ?? 0 ?></div><div class="label">SPMB Pending</div></div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="admin-stat-card">
            <div class="stat-icon icon-green"><i class="fas fa-check-circle"></i></div>
            <div class="stat-info"><div class="num"><?= $stats['spmb_accepted'] ?? 0 ?></div><div class="label">SPMB Diterima</div></div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="admin-stat-card">
            <div class="stat-icon icon-red"><i class="fas fa-times-circle"></i></div>
            <div class="stat-info"><div class="num"><?= $stats['spmb_rejected'] ?? 0 ?></div><div class="label">SPMB Ditolak</div></div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="admin-stat-card">
            <div class="stat-icon icon-teal"><i class="fas fa-envelope"></i></div>
            <div class="stat-info"><div class="num"><?= $stats['unread_contacts'] ?? 0 ?></div><div class="label">Pesan Belum Dibaca</div></div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="admin-stat-card">
            <div class="stat-icon icon-blue"><i class="fas fa-user-graduate"></i></div>
            <div class="stat-info"><div class="num"><?= $stats['spmb_total'] ?? 0 ?></div><div class="label">Total Pendaftar</div></div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="admin-stat-card">
            <div class="stat-icon icon-green"><i class="fas fa-book"></i></div>
            <div class="stat-info"><div class="num"><?= $stats['programs'] ?? 0 ?></div><div class="label">Program Keahlian</div></div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent SPMB -->
    <div class="col-lg-8">
        <div class="admin-table-wrapper">
            <div class="admin-table-header">
                <h5><i class="fas fa-user-plus me-2"></i>Pendaftar Terbaru</h5>
                <a href="<?= APP_URL ?>/admin/spmb/pendaftar" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr><th>No. Daftar</th><th>Nama</th><th>Jurusan</th><th>Tgl Daftar</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentSpmb)): ?>
                        <tr><td colspan="5" class="text-center py-3" style="color:var(--text-muted);">Belum ada pendaftar</td></tr>
                        <?php else: ?>
                        <?php foreach ($recentSpmb as $reg): ?>
                        <tr>
                            <td style="font-family:monospace;font-weight:600;"><?= htmlspecialchars($reg['registration_number']) ?></td>
                            <td><?= htmlspecialchars($reg['full_name']) ?></td>
                            <td><?= htmlspecialchars($reg['program_name'] ?? '-') ?></td>
                            <td><?= date('d/m/Y', strtotime($reg['created_at'])) ?></td>
                            <td>
                                <span class="badge badge-<?= $reg['status'] ?>"><?= ucfirst($reg['status']) ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Contacts & Quick Links -->
    <div class="col-lg-4">
        <!-- Quick Links -->
        <div class="admin-card mb-4">
            <h5><i class="fas fa-bolt me-2"></i>Aksi Cepat</h5>
            <div class="d-grid gap-2">
                <a href="<?= APP_URL ?>/admin/berita/tambah" class="btn btn-outline-primary btn-sm text-start">
                    <i class="fas fa-plus me-2"></i>Tambah Berita
                </a>
                <a href="<?= APP_URL ?>/admin/galeri/tambah" class="btn btn-outline-primary btn-sm text-start">
                    <i class="fas fa-image me-2"></i>Upload Foto Galeri
                </a>
                <a href="<?= APP_URL ?>/admin/spmb/pendaftar" class="btn btn-outline-warning btn-sm text-start">
                    <i class="fas fa-user-check me-2"></i>Verifikasi Pendaftar
                </a>
                <a href="<?= APP_URL ?>/admin/settings/umum" class="btn btn-outline-secondary btn-sm text-start">
                    <i class="fas fa-cog me-2"></i>Pengaturan Sekolah
                </a>
            </div>
        </div>

        <!-- Recent Contacts -->
        <div class="admin-card">
            <h5><i class="fas fa-envelope me-2"></i>Pesan Terbaru</h5>
            <?php if (empty($recentContacts)): ?>
            <p style="color:var(--text-muted);font-size:0.9rem;">Belum ada pesan masuk</p>
            <?php else: ?>
            <?php foreach ($recentContacts as $msg): ?>
            <div class="d-flex gap-2 mb-3 pb-3" style="border-bottom:1px solid var(--border);">
                <div style="width:36px;height:36px;border-radius:50%;background:rgba(26,86,219,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;color:var(--primary);">
                    <i class="fas fa-user" style="font-size:0.8rem;"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                    <div style="color:var(--text);font-size:0.85rem;font-weight:600;"><?= htmlspecialchars($msg['name']) ?></div>
                    <div style="color:var(--text-muted);font-size:0.8rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars($msg['subject'] ?? '') ?></div>
                </div>
                <?php if (!$msg['is_read']): ?>
                <span class="badge bg-danger align-self-center" style="font-size:0.65rem;">Baru</span>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
            <a href="<?= APP_URL ?>/admin/kontak" class="btn btn-sm btn-outline-primary w-100 mt-2">Lihat Semua Pesan</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/admin_footer.php'; ?>

<?php $adminPageTitle = 'Data Pendaftar SPMB'; require_once __DIR__ . '/../layouts/admin_header.php'; ?>

<!-- Stats -->
<div class="row g-3 mb-4">
    <?php
    $statusStats = ['pending'=>['label'=>'Menunggu','color'=>'icon-orange'],'verifikasi'=>['label'=>'Verifikasi','color'=>'icon-blue'],'diterima'=>['label'=>'Diterima','color'=>'icon-green'],'ditolak'=>['label'=>'Ditolak','color'=>'icon-red']];
    foreach ($statusStats as $st => $info):
    ?>
    <div class="col-6 col-md-3">
        <div class="admin-stat-card">
            <div class="stat-icon <?= $info['color'] ?>"><i class="fas fa-user"></i></div>
            <div class="stat-info">
                <div class="num"><?= $statusCount[$st] ?? 0 ?></div>
                <div class="label"><?= $info['label'] ?></div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Filter & Export -->
<div class="admin-table-wrapper">
    <div class="admin-table-header">
        <h5><i class="fas fa-user-plus me-2"></i>Daftar Pendaftar</h5>
        <div class="d-flex gap-2 flex-wrap">
            <form method="GET" class="d-flex gap-2 flex-wrap">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" style="width:140px;">
                    <option value="">Semua Status</option>
                    <option value="pending" <?= ($_GET['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="verifikasi" <?= ($_GET['status'] ?? '') === 'verifikasi' ? 'selected' : '' ?>>Verifikasi</option>
                    <option value="diterima" <?= ($_GET['status'] ?? '') === 'diterima' ? 'selected' : '' ?>>Diterima</option>
                    <option value="ditolak" <?= ($_GET['status'] ?? '') === 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                </select>
                <select name="program" class="form-select form-select-sm" onchange="this.form.submit()" style="width:160px;">
                    <option value="">Semua Jurusan</option>
                    <?php foreach ($programs ?? [] as $prog): ?>
                    <option value="<?= $prog['id'] ?>" <?= ($_GET['program'] ?? '') == $prog['id'] ? 'selected' : '' ?>><?= htmlspecialchars($prog['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="q" class="form-control form-control-sm" placeholder="Cari nama..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" style="width:180px;">
                <button class="btn btn-sm btn-outline-primary">Cari</button>
            </form>
            <a href="<?= APP_URL ?>/admin/spmb/export" class="btn btn-sm btn-success">
                <i class="fas fa-file-csv me-1"></i>Export CSV
            </a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>No. Daftar</th>
                    <th>Nama</th>
                    <th>Jurusan</th>
                    <th>NISN</th>
                    <th>Asal Sekolah</th>
                    <th>Tgl Daftar</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($registrations)): ?>
                <tr><td colspan="8" class="text-center py-4" style="color:var(--text-muted);">Belum ada pendaftar</td></tr>
                <?php else: ?>
                <?php foreach ($registrations as $reg): ?>
                <tr>
                    <td style="font-family:monospace;font-size:0.85rem;"><?= htmlspecialchars($reg['registration_number']) ?></td>
                    <td>
                        <div style="font-weight:500;color:var(--text);"><?= htmlspecialchars($reg['full_name']) ?></div>
                        <small style="color:var(--text-muted);"><?= $reg['gender'] === 'L' ? 'Laki-laki' : 'Perempuan' ?></small>
                    </td>
                    <td style="color:var(--text-muted);"><?= htmlspecialchars($reg['program_name'] ?? '-') ?></td>
                    <td style="color:var(--text-muted);"><?= htmlspecialchars($reg['nisn'] ?? '-') ?></td>
                    <td style="color:var(--text-muted);max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars($reg['school_origin'] ?? '-') ?></td>
                    <td style="color:var(--text-muted);"><?= date('d/m/Y', strtotime($reg['created_at'])) ?></td>
                    <td>
                        <span class="badge badge-<?= $reg['status'] ?>"><?= ucfirst($reg['status']) ?></span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="<?= APP_URL ?>/admin/spmb/detail/<?= $reg['id'] ?>" class="btn btn-xs btn-outline-primary" style="padding:3px 8px;font-size:0.75rem;"><i class="fas fa-eye"></i></a>
                            <button type="button" class="btn btn-xs btn-outline-success" style="padding:3px 8px;font-size:0.75rem;" onclick="updateStatus(<?= $reg['id'] ?>,'<?= htmlspecialchars($reg['full_name']) ?>','<?= $reg['status'] ?>')"><i class="fas fa-edit"></i></button>
                            <a href="<?= APP_URL ?>/admin/spmb/hapus/<?= $reg['id'] ?>" class="btn btn-xs btn-outline-danger" style="padding:3px 8px;font-size:0.75rem;" data-confirm="Hapus data pendaftar ini?"><i class="fas fa-trash"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background:var(--card-bg);border:1px solid var(--border);">
            <div class="modal-header" style="border-color:var(--border);">
                <h5 class="modal-title" style="color:var(--text);">Update Status Pendaftar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="statusForm" action="">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <div class="modal-body">
                    <p id="statusModalName" style="color:var(--text);font-weight:600;"></p>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" id="statusSelect" class="form-select">
                            <option value="pending">Menunggu Verifikasi</option>
                            <option value="verifikasi">Sedang Diverifikasi</option>
                            <option value="diterima">Diterima</option>
                            <option value="ditolak">Ditolak</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Catatan untuk pendaftar (opsional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-color:var(--border);">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateStatus(id, name, status) {
    document.getElementById('statusForm').action = '<?= APP_URL ?>/admin/spmb/status/' + id;
    document.getElementById('statusModalName').textContent = 'Pendaftar: ' + name;
    document.getElementById('statusSelect').value = status;
    new bootstrap.Modal(document.getElementById('statusModal')).show();
}
</script>

<?php require_once __DIR__ . '/../layouts/admin_footer.php'; ?>

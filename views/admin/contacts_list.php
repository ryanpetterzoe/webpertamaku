<?php $adminPageTitle = 'Pesan Masuk'; require_once __DIR__ . '/../layouts/admin_header.php'; ?>

<div class="admin-table-wrapper">
    <div class="admin-table-header">
        <h5><i class="fas fa-envelope me-2"></i>Pesan Masuk</h5>
        <span style="color:var(--text-muted);font-size:0.85rem;"><?= $unreadCount ?? 0 ?> pesan belum dibaca</span>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead><tr><th>Nama</th><th>Email</th><th>Subjek</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
                <?php if (empty($contacts)): ?>
                <tr><td colspan="6" class="text-center py-4" style="color:var(--text-muted);">Belum ada pesan masuk</td></tr>
                <?php else: ?>
                <?php foreach ($contacts as $msg): ?>
                <tr style="<?= !$msg['is_read'] ? 'font-weight:600;' : '' ?>">
                    <td>
                        <div style="color:var(--text);"><?= htmlspecialchars($msg['name']) ?></div>
                        <?php if (!empty($msg['phone'])): ?><small style="color:var(--text-muted);"><?= htmlspecialchars($msg['phone']) ?></small><?php endif; ?>
                    </td>
                    <td style="color:var(--text-muted);"><?= htmlspecialchars($msg['email']) ?></td>
                    <td style="color:var(--text);max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars($msg['subject'] ?? '') ?></td>
                    <td style="color:var(--text-muted);"><?= date('d/m/Y H:i', strtotime($msg['created_at'])) ?></td>
                    <td>
                        <span class="badge <?= $msg['is_read'] ? 'bg-success' : 'bg-danger' ?>">
                            <?= $msg['is_read'] ? 'Dibaca' : 'Baru' ?>
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <button class="btn btn-xs btn-outline-primary" style="padding:3px 8px;font-size:0.75rem;" onclick="viewMessage(<?= htmlspecialchars(json_encode($msg)) ?>)" title="Lihat"><i class="fas fa-eye"></i></button>
                            <a href="<?= APP_URL ?>/admin/kontak/hapus/<?= $msg['id'] ?>" class="btn btn-xs btn-outline-danger" style="padding:3px 8px;font-size:0.75rem;" data-confirm="Hapus pesan ini?"><i class="fas fa-trash"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- View Message Modal -->
<div class="modal fade" id="msgModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background:var(--card-bg);border:1px solid var(--border);">
            <div class="modal-header" style="border-color:var(--border);">
                <h5 class="modal-title" style="color:var(--text);">Detail Pesan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <dl class="row">
                    <dt class="col-sm-3" style="color:var(--text-muted);">Nama</dt><dd class="col-sm-9" id="msgName" style="color:var(--text);"></dd>
                    <dt class="col-sm-3" style="color:var(--text-muted);">Email</dt><dd class="col-sm-9" id="msgEmail" style="color:var(--text);"></dd>
                    <dt class="col-sm-3" style="color:var(--text-muted);">HP</dt><dd class="col-sm-9" id="msgPhone" style="color:var(--text);"></dd>
                    <dt class="col-sm-3" style="color:var(--text-muted);">Subjek</dt><dd class="col-sm-9" id="msgSubject" style="color:var(--text);"></dd>
                    <dt class="col-sm-3" style="color:var(--text-muted);">Tanggal</dt><dd class="col-sm-9" id="msgDate" style="color:var(--text);"></dd>
                </dl>
                <hr style="border-color:var(--border);">
                <h6 style="color:var(--text-muted);">Pesan:</h6>
                <div id="msgContent" style="color:var(--text);line-height:1.8;background:var(--bg-secondary);padding:16px;border-radius:8px;"></div>
            </div>
            <div class="modal-footer" style="border-color:var(--border);">
                <a id="msgReply" href="#" class="btn btn-primary"><i class="fas fa-reply me-1"></i>Balas via Email</a>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
function viewMessage(msg) {
    document.getElementById('msgName').textContent = msg.name || '-';
    document.getElementById('msgEmail').textContent = msg.email || '-';
    document.getElementById('msgPhone').textContent = msg.phone || '-';
    document.getElementById('msgSubject').textContent = msg.subject || '-';
    document.getElementById('msgDate').textContent = msg.created_at || '-';
    document.getElementById('msgContent').textContent = msg.message || '-';
    document.getElementById('msgReply').href = 'mailto:' + (msg.email || '') + '?subject=Re: ' + encodeURIComponent(msg.subject || '');
    // Mark as read via AJAX or redirect
    if (!msg.is_read) {
        fetch('<?= APP_URL ?>/admin/kontak/baca/' + msg.id);
    }
    new bootstrap.Modal(document.getElementById('msgModal')).show();
}
</script>

<?php require_once __DIR__ . '/../layouts/admin_footer.php'; ?>

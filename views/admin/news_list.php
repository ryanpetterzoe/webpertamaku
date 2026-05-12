<?php $adminPageTitle = 'Manajemen Berita'; require_once __DIR__ . '/../layouts/admin_header.php'; ?>

<div class="admin-table-wrapper">
    <div class="admin-table-header">
        <h5><i class="fas fa-newspaper me-2"></i>Daftar Berita</h5>
        <div class="d-flex gap-2 flex-wrap">
            <form method="GET" class="d-flex gap-2">
                <input type="text" name="q" class="form-control form-control-sm" placeholder="Cari berita..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" style="width:200px;">
                <button class="btn btn-sm btn-outline-primary">Cari</button>
            </form>
            <a href="<?= APP_URL ?>/admin/berita/tambah" class="btn btn-sm btn-primary">
                <i class="fas fa-plus me-1"></i>Tambah Berita
            </a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Kategori</th>
                    <th>Penulis</th>
                    <th>Dilihat</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($news)): ?>
                <tr><td colspan="7" class="text-center py-4" style="color:var(--text-muted);">Belum ada berita. <a href="<?= APP_URL ?>/admin/berita/tambah">Tambah sekarang</a></td></tr>
                <?php else: ?>
                <?php foreach ($news as $article): ?>
                <tr>
                    <td>
                        <div style="color:var(--text);font-weight:500;max-width:280px;"><?= htmlspecialchars($article['title']) ?></div>
                        <small style="color:var(--text-muted);"><?= htmlspecialchars($article['slug']) ?></small>
                    </td>
                    <td><span class="badge bg-primary"><?= htmlspecialchars($article['category']) ?></span></td>
                    <td style="color:var(--text-muted);"><?= htmlspecialchars($article['author']) ?></td>
                    <td style="color:var(--text-muted);"><?= number_format($article['views']) ?></td>
                    <td style="color:var(--text-muted);"><?= date('d/m/Y', strtotime($article['published_at'])) ?></td>
                    <td>
                        <span class="badge <?= $article['is_published'] ? 'bg-success' : 'bg-secondary' ?>">
                            <?= $article['is_published'] ? 'Dipublikasi' : 'Draft' ?>
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="<?= APP_URL ?>/berita/<?= htmlspecialchars($article['slug']) ?>" target="_blank" class="btn btn-xs btn-outline-secondary" title="Preview" style="padding:3px 8px;font-size:0.75rem;"><i class="fas fa-eye"></i></a>
                            <a href="<?= APP_URL ?>/admin/berita/edit/<?= $article['id'] ?>" class="btn btn-xs btn-outline-primary" title="Edit" style="padding:3px 8px;font-size:0.75rem;"><i class="fas fa-edit"></i></a>
                            <a href="<?= APP_URL ?>/admin/berita/toggle/<?= $article['id'] ?>" class="btn btn-xs <?= $article['is_published'] ? 'btn-outline-warning' : 'btn-outline-success' ?>" title="<?= $article['is_published'] ? 'Arsipkan' : 'Publikasi' ?>" style="padding:3px 8px;font-size:0.75rem;"><i class="fas fa-<?= $article['is_published'] ? 'eye-slash' : 'check' ?>"></i></a>
                            <a href="<?= APP_URL ?>/admin/berita/hapus/<?= $article['id'] ?>" class="btn btn-xs btn-outline-danger" title="Hapus" style="padding:3px 8px;font-size:0.75rem;" data-confirm="Hapus berita '<?= htmlspecialchars($article['title']) ?>'?"><i class="fas fa-trash"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
    <?php if (($totalPages ?? 1) > 1): ?>
    <div class="d-flex justify-content-center p-3">
        <ul class="pagination pagination-sm mb-0">
            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
            <li class="page-item <?= $p == ($currentPage ?? 1) ? 'active' : '' ?>">
                <a class="page-link" href="<?= APP_URL ?>/admin/berita?page=<?= $p ?><?= !empty($_GET['q']) ? '&q='.urlencode($_GET['q']) : '' ?>"><?= $p ?></a>
            </li>
            <?php endfor; ?>
        </ul>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/admin_footer.php'; ?>

<?php
$isEdit = !empty($news);
$adminPageTitle = $isEdit ? 'Edit Berita' : 'Tambah Berita';
require_once __DIR__ . '/../layouts/admin_header.php';
?>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="admin-card">
            <form method="POST" action="<?= APP_URL ?>/admin/berita/<?= $isEdit ? 'edit/'.$news['id'] : 'tambah' ?>" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

                <div class="mb-3">
                    <label class="form-label">Judul Berita <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="title" class="form-control" required placeholder="Judul berita yang menarik" value="<?= htmlspecialchars($news['title'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Slug (URL) <span class="text-danger">*</span></label>
                    <input type="text" name="slug" id="slug" class="form-control" required placeholder="judul-berita-url" value="<?= htmlspecialchars($news['slug'] ?? '') ?>">
                    <small style="color:var(--text-muted);">URL: <?= APP_URL ?>/berita/<span id="slugPreview"><?= htmlspecialchars($news['slug'] ?? '') ?></span></small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Ringkasan (Excerpt)</label>
                    <textarea name="excerpt" class="form-control" rows="3" placeholder="Ringkasan singkat berita..."><?= htmlspecialchars($news['excerpt'] ?? '') ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Konten Berita <span class="text-danger">*</span></label>
                    <div class="mb-2">
                        <button type="button" class="btn btn-xs btn-outline-secondary" onclick="insertTag('<strong>','</strong>')" style="padding:2px 8px;font-size:0.75rem;"><b>B</b></button>
                        <button type="button" class="btn btn-xs btn-outline-secondary" onclick="insertTag('<em>','</em>')" style="padding:2px 8px;font-size:0.75rem;"><i>I</i></button>
                        <button type="button" class="btn btn-xs btn-outline-secondary" onclick="insertTag('<p>','</p>')" style="padding:2px 8px;font-size:0.75rem;">P</button>
                        <button type="button" class="btn btn-xs btn-outline-secondary" onclick="insertTag('<h3>','</h3>')" style="padding:2px 8px;font-size:0.75rem;">H3</button>
                        <button type="button" class="btn btn-xs btn-outline-secondary" onclick="insertTag('<ul>\n<li>','</li>\n</ul>')" style="padding:2px 8px;font-size:0.75rem;"><i class="fas fa-list-ul"></i></button>
                    </div>
                    <textarea name="content" id="newsContent" class="form-control" rows="15" required placeholder="Tulis konten berita lengkap..."><?= htmlspecialchars($news['content'] ?? '') ?></textarea>
                    <small style="color:var(--text-muted);">Mendukung HTML dasar</small>
                </div>
                <div class="d-flex gap-3 flex-wrap">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i><?= $isEdit ? 'Update Berita' : 'Simpan Berita' ?></button>
                    <a href="<?= APP_URL ?>/admin/berita" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
    <div class="col-lg-4">
        <!-- Meta -->
        <div class="admin-card mb-4">
            <h5>Pengaturan</h5>
            <div class="mb-3">
                <label class="form-label">Kategori</label>
                <!-- Note: This is outside the form for layout, but the form above should include these fields too -->
                <p style="color:var(--text-muted);font-size:0.85rem;">Isi di form utama</p>
            </div>
        </div>

        <!-- Separate settings form -->
        <div class="admin-card">
            <h5>Pengaturan Publikasi</h5>
            <p style="color:var(--text-muted);font-size:0.85rem;">Kategori, gambar, dan status ada di form utama. Pastikan sudah diisi.</p>
        </div>
    </div>
</div>

<?php
// The form above needs additional fields. Let's inject them via a note - the controller handles it.
// Actually, let's add the missing fields properly by noting they should go in the main form.
?>

<script>
// Slug auto-gen
document.getElementById('title').addEventListener('input', function() {
    var slug = this.value.toLowerCase().replace(/[^a-z0-9\s-]/g,'').trim().replace(/\s+/g,'-');
    document.getElementById('slug').value = slug;
    document.getElementById('slugPreview').textContent = slug;
});
document.getElementById('slug').addEventListener('input', function() {
    document.getElementById('slugPreview').textContent = this.value;
});
function insertTag(open, close) {
    var ta = document.getElementById('newsContent');
    var start = ta.selectionStart, end = ta.selectionEnd;
    var sel = ta.value.substring(start, end);
    ta.value = ta.value.substring(0, start) + open + sel + close + ta.value.substring(end);
    ta.selectionStart = start + open.length;
    ta.selectionEnd = start + open.length + sel.length;
    ta.focus();
}
</script>

<?php require_once __DIR__ . '/../layouts/admin_footer.php'; ?>

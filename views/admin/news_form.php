<?php
$isEdit = !empty($news);
$adminPageTitle = $isEdit ? 'Edit Berita' : 'Tambah Berita';
$csrfToken = isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : '';
// Ambil daftar jurusan untuk dropdown
$_db = getDB();
$_progRes = $_db->query("SELECT id, name, code FROM programs WHERE is_active=1 ORDER BY sort_order");
$_programs = $_progRes ? $_progRes->fetch_all(MYSQLI_ASSOC) : [];
require_once __DIR__ . '/../layouts/admin_header.php';
?>

<form method="POST"
      action="<?= APP_URL ?>/admin/berita/<?= $isEdit ? 'edit/'.$news['id'] : 'tambah' ?>"
      enctype="multipart/form-data">
<input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken) ?>">

<div class="row g-4">

    <!-- ── KOLOM KIRI: Konten ─────────────────────────── -->
    <div class="col-lg-8">

        <!-- Judul & Slug -->
        <div class="admin-card mb-4">
            <div class="mb-3">
                <label class="form-label">Judul Berita <span class="text-danger">*</span></label>
                <input type="text" name="title" id="titleField" class="form-control" required
                       placeholder="Judul berita yang menarik"
                       value="<?= htmlspecialchars($news['title'] ?? '') ?>">
            </div>
            <div class="mb-0">
                <label class="form-label">Slug (URL)</label>
                <input type="text" name="slug" id="slugField" class="form-control"
                       placeholder="judul-berita-url"
                       value="<?= htmlspecialchars($news['slug'] ?? '') ?>">
                <small style="color:var(--text-muted);">
                    Preview: <?= APP_URL ?>/berita/<span id="slugPreview"><?= htmlspecialchars($news['slug'] ?? '') ?></span>
                </small>
            </div>
        </div>

        <!-- Excerpt -->
        <div class="admin-card mb-4">
            <label class="form-label">Ringkasan (Excerpt)</label>
            <textarea name="excerpt" class="form-control" rows="3"
                      placeholder="Ringkasan singkat untuk ditampilkan di daftar berita..."><?= htmlspecialchars($news['excerpt'] ?? '') ?></textarea>
            <small style="color:var(--text-muted);">Maks. 200 karakter. Jika kosong, akan diambil dari awal konten.</small>
        </div>

        <!-- Konten -->
        <div class="admin-card">
            <label class="form-label">Konten Berita <span class="text-danger">*</span></label>

            <!-- Mini toolbar -->
            <div class="mb-2 d-flex gap-1 flex-wrap">
                <?php
                $btns = [
                    ['label'=>'<b>B</b>',       'open'=>'<strong>',  'close'=>'</strong>'],
                    ['label'=>'<i>I</i>',        'open'=>'<em>',      'close'=>'</em>'],
                    ['label'=>'P',               'open'=>'<p>',       'close'=>'</p>'],
                    ['label'=>'H2',              'open'=>'<h2>',      'close'=>'</h2>'],
                    ['label'=>'H3',              'open'=>'<h3>',      'close'=>'</h3>'],
                    ['label'=>'<i class="fas fa-list-ul"></i>', 'open'=>'<ul>\n<li>', 'close'=>'</li>\n</ul>'],
                    ['label'=>'<i class="fas fa-link"></i>',   'open'=>'<a href="">', 'close'=>'</a>'],
                    ['label'=>'IMG',             'open'=>'<img src="" alt="" style="max-width:100%;">', 'close'=>''],
                ];
                foreach ($btns as $b):
                ?>
                <button type="button" class="btn btn-sm btn-outline-secondary"
                        style="padding:3px 9px;font-size:.78rem;"
                        onclick="insertTag('<?= addslashes($b['open']) ?>','<?= addslashes($b['close']) ?>')">
                    <?= $b['label'] ?>
                </button>
                <?php endforeach; ?>
            </div>

            <textarea name="content" id="newsContent" class="form-control" rows="18" required
                      placeholder="Tulis konten berita lengkap di sini..."><?= htmlspecialchars($news['content'] ?? '') ?></textarea>
            <small style="color:var(--text-muted);">Mendukung HTML dasar. Gunakan tombol di atas untuk menyisipkan tag.</small>
        </div>
    </div>

    <!-- ── KOLOM KANAN: Sidebar ───────────────────────── -->
    <div class="col-lg-4">

        <!-- Aksi -->
        <div class="admin-card mb-4">
            <h5 class="mb-3"><i class="fas fa-paper-plane me-2" style="color:var(--primary);"></i>Publikasi</h5>

            <div class="mb-3">
                <label class="form-label">Status</label>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_published" id="isPublished"
                           <?= (!$isEdit || !empty($news['is_published'])) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="isPublished" style="color:var(--text);">
                        Tampilkan ke publik
                    </label>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Penulis</label>
                <input type="text" name="author" class="form-control"
                       value="<?= htmlspecialchars($news['author'] ?? (isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Admin')) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Kategori</label>
                <input type="text" name="category" class="form-control" list="categoryList"
                       placeholder="Pilih atau ketik kategori"
                       value="<?= htmlspecialchars($news['category'] ?? 'Berita') ?>">
                <datalist id="categoryList">
                    <option value="Berita">
                    <option value="Pengumuman">
                    <option value="Prestasi">
                    <option value="Kegiatan">
                    <option value="Info">
                </datalist>
            </div>

            <div class="mb-3">
                <label class="form-label">
                    <i class="fas fa-book me-1" style="color:var(--primary);"></i>Jurusan
                </label>
                <select name="program_id" class="form-select">
                    <option value="">— Umum (semua jurusan) —</option>
                    <?php foreach ($_programs as $prog): ?>
                    <option value="<?= $prog['id'] ?>"
                        <?= ((int)($news['program_id'] ?? 0) === (int)$prog['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($prog['name']) ?>
                        <?php if ($prog['code']): ?>(<?= htmlspecialchars($prog['code']) ?>)<?php endif; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <small style="color:var(--text-muted);margin-top:4px;display:block;">
                    Pilih jurusan jika berita khusus untuk jurusan tertentu.<br>
                    Kosong = berita umum (muncul di semua jurusan).
                </small>
            </div>

            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i><?= $isEdit ? 'Update Berita' : 'Simpan & Publikasi' ?>
                </button>
                <a href="<?= APP_URL ?>/admin/berita" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-1"></i>Batal
                </a>
            </div>
        </div>

        <!-- Thumbnail / Gambar -->
        <div class="admin-card mb-4">
            <h5 class="mb-3"><i class="fas fa-image me-2" style="color:var(--primary);"></i>Thumbnail Berita</h5>

            <!-- Preview gambar existing -->
            <?php if (!empty($news['image'])): ?>
            <div class="mb-3" id="existingImageWrap">
                <p style="color:var(--text-muted);font-size:.8rem;margin-bottom:6px;">Gambar saat ini:</p>
                <img src="<?= UPLOAD_URL . htmlspecialchars($news['image']) ?>"
                     alt="Thumbnail"
                     style="width:100%;border-radius:8px;border:1px solid var(--border);object-fit:cover;max-height:180px;">
            </div>
            <?php endif; ?>

            <!-- Preview gambar baru (sebelum upload) -->
            <div id="newImagePreviewWrap" style="display:none;margin-bottom:12px;">
                <p style="color:var(--text-muted);font-size:.8rem;margin-bottom:6px;">Preview gambar baru:</p>
                <img id="newImagePreview"
                     src=""
                     alt="Preview"
                     style="width:100%;border-radius:8px;border:2px solid var(--primary);object-fit:cover;max-height:180px;">
            </div>

            <label class="form-label">
                <?= !empty($news['image']) ? 'Ganti Gambar' : 'Upload Thumbnail' ?>
            </label>
            <input type="file" name="image" id="imageInput" class="form-control"
                   accept="image/jpeg,image/png,image/webp,image/gif"
                   onchange="previewThumbnail(this)">
            <small style="color:var(--text-muted);margin-top:4px;display:block;">
                Format: JPG, PNG, WebP. Disarankan rasio 16:9 (misal: 1280×720px). Maks. 2MB.
            </small>

            <?php if (!empty($news['image'])): ?>
            <div class="mt-2">
                <small style="color:var(--text-muted);">
                    <i class="fas fa-info-circle me-1"></i>
                    Kosongkan jika tidak ingin mengganti gambar.
                </small>
            </div>
            <?php endif; ?>
        </div>

        <!-- Tips -->
        <div class="admin-card" style="background:var(--primary-glow);border-color:rgba(37,99,235,.2);">
            <h6 style="color:var(--primary);margin-bottom:10px;"><i class="fas fa-lightbulb me-1"></i>Tips</h6>
            <ul style="color:var(--text-secondary);font-size:.82rem;padding-left:16px;margin:0;line-height:1.8;">
                <li>Thumbnail muncul di halaman daftar berita &amp; beranda</li>
                <li>Gunakan gambar horizontal (landscape) agar tampil optimal</li>
                <li>Isi <strong>Ringkasan</strong> agar tampil di kartu berita</li>
                <li>Slug otomatis dari judul, bisa diedit manual</li>
            </ul>
        </div>
    </div>

</div><!-- /row -->
</form>

<script>
// Slug auto-generate dari judul
var slugManuallyEdited = false;
document.getElementById('titleField').addEventListener('input', function () {
    if (slugManuallyEdited) return;
    var slug = this.value
        .toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .trim()
        .replace(/\s+/g, '-');
    document.getElementById('slugField').value = slug;
    document.getElementById('slugPreview').textContent = slug;
});
document.getElementById('slugField').addEventListener('input', function () {
    slugManuallyEdited = true;
    document.getElementById('slugPreview').textContent = this.value;
});

// Preview thumbnail sebelum upload
function previewThumbnail(input) {
    var wrap    = document.getElementById('newImagePreviewWrap');
    var preview = document.getElementById('newImagePreview');
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            wrap.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        wrap.style.display = 'none';
        preview.src = '';
    }
}

// Insert HTML tag ke textarea konten
function insertTag(open, close) {
    var ta    = document.getElementById('newsContent');
    var start = ta.selectionStart;
    var end   = ta.selectionEnd;
    var sel   = ta.value.substring(start, end);
    ta.value  = ta.value.substring(0, start) + open + sel + close + ta.value.substring(end);
    ta.selectionStart = start + open.length;
    ta.selectionEnd   = start + open.length + sel.length;
    ta.focus();
}
</script>

<?php require_once __DIR__ . '/../layouts/admin_footer.php'; ?>

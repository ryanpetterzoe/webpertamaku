<?php
$pageTitle = 'Formulir Pendaftaran SPMB - ' . ($settings['school_name'] ?? 'SMK Pertamaku');
require_once __DIR__ . '/../layouts/header.php';
?>

<div style="background:linear-gradient(135deg,var(--primary),var(--primary-dark));padding:60px 0;color:#fff;">
    <div class="container">
        <h1 class="fw-bold mb-2">Formulir Pendaftaran</h1>
        <p class="mb-0 opacity-75">SPMB Tahun Ajaran <?= htmlspecialchars($spmbSettings['academic_year'] ?? date('Y') . '/' . (date('Y')+1)) ?></p>
    </div>
</div>

<section class="section">
<div class="container">
<?php if (!empty($error)): ?>
<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if (!empty($success)): ?>
<div class="alert alert-success text-center py-4">
    <i class="fas fa-check-circle" style="font-size:2.5rem;color:#10b981;display:block;margin-bottom:12px;"></i>
    <h4>Pendaftaran Berhasil!</h4>
    <p>Nomor pendaftaran Anda: <strong style="font-size:1.2rem;"><?= htmlspecialchars($regNumber ?? '') ?></strong></p>
    <p>Simpan nomor ini untuk mengecek status pendaftaran Anda.</p>
    <a href="<?= APP_URL ?>/spmb/cek" class="btn btn-primary me-2">Cek Status</a>
    <a href="<?= APP_URL ?>/spmb" class="btn btn-outline-primary">Kembali ke SPMB</a>
</div>
<?php else: ?>

<!-- Step Progress -->
<div class="step-progress mb-5">
    <?php
    $steps = ['Data Pribadi','Asal Sekolah','Pilih Jurusan','Data Orang Tua','Dokumen','Preview'];
    foreach ($steps as $i => $step): ?>
    <div class="step-item <?= $i === 0 ? 'active' : '' ?>" id="step-item-<?= $i ?>">
        <div class="step-circle"><?= $i < count($steps)-1 ? $i+1 : '<i class="fas fa-eye"></i>' ?></div>
        <span class="step-label"><?= $step ?></span>
    </div>
    <?php endforeach; ?>
</div>

<div class="card">
<div class="card-body p-4">
<form id="spmbForm" method="POST" action="<?= APP_URL ?>/spmb/daftar" enctype="multipart/form-data">
<input type="hidden" name="_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

<!-- Step 1: Data Pribadi -->
<div class="step-panel active" id="panel-0">
    <h5 class="mb-4" style="color:var(--text);border-bottom:2px solid var(--primary);padding-bottom:8px;">
        <span class="badge bg-primary me-2">1</span>Data Pribadi
    </h5>
    <div class="row g-3">
        <div class="col-md-8">
            <label class="form-label" for="full_name">Nama Lengkap <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="full_name" name="full_name" required placeholder="Nama lengkap sesuai akte kelahiran">
        </div>
        <div class="col-md-4">
            <label class="form-label" for="nick_name">Nama Panggilan</label>
            <input type="text" class="form-control" id="nick_name" name="nick_name" placeholder="Nama panggilan">
        </div>
        <div class="col-md-4">
            <label class="form-label" for="gender">Jenis Kelamin <span class="text-danger">*</span></label>
            <select class="form-select" id="gender" name="gender" required>
                <option value="">-- Pilih --</option>
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label" for="birth_place">Tempat Lahir <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="birth_place" name="birth_place" required placeholder="Kota lahir">
        </div>
        <div class="col-md-4">
            <label class="form-label" for="birth_date">Tanggal Lahir <span class="text-danger">*</span></label>
            <input type="date" class="form-control" id="birth_date" name="birth_date" required>
        </div>
        <div class="col-md-4">
            <label class="form-label" for="religion">Agama <span class="text-danger">*</span></label>
            <select class="form-select" id="religion" name="religion" required>
                <option value="">-- Pilih --</option>
                <option>Islam</option><option>Kristen</option><option>Katolik</option>
                <option>Hindu</option><option>Buddha</option><option>Konghucu</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label" for="phone">No HP/WhatsApp</label>
            <input type="text" class="form-control" id="phone" name="phone" placeholder="08xxxxxxxxxx">
        </div>
        <div class="col-md-4">
            <label class="form-label" for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="email@contoh.com">
        </div>
        <div class="col-12">
            <label class="form-label" for="address">Alamat Lengkap <span class="text-danger">*</span></label>
            <textarea class="form-control" id="address" name="address" rows="2" required placeholder="Jalan, nomor rumah, RT/RW, desa/kelurahan, kecamatan, kota, provinsi"></textarea>
        </div>
    </div>
    <div class="d-flex justify-content-end mt-4">
        <button type="button" class="btn btn-primary btn-next">Selanjutnya <i class="fas fa-arrow-right ms-2"></i></button>
    </div>
</div>

<!-- Step 2: Data Asal Sekolah -->
<div class="step-panel" id="panel-1">
    <h5 class="mb-4" style="color:var(--text);border-bottom:2px solid var(--primary);padding-bottom:8px;">
        <span class="badge bg-primary me-2">2</span>Data Asal Sekolah
    </h5>
    <div class="row g-3">
        <div class="col-md-8">
            <label class="form-label" for="school_origin">Nama SMP/MTs Asal <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="school_origin" name="school_origin" required placeholder="Nama lengkap sekolah asal">
        </div>
        <div class="col-md-4">
            <label class="form-label" for="nisn">NISN <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="nisn" name="nisn" required placeholder="Nomor NISN (10 digit)" maxlength="10">
        </div>
        <div class="col-md-4">
            <label class="form-label" for="un_score">Nilai Rata-rata Rapor</label>
            <input type="number" class="form-control" id="un_score" name="un_score" placeholder="Contoh: 85.50" step="0.01" min="0" max="100">
        </div>
    </div>
    <div class="d-flex justify-content-between mt-4">
        <button type="button" class="btn btn-outline-secondary btn-prev"><i class="fas fa-arrow-left me-2"></i>Sebelumnya</button>
        <button type="button" class="btn btn-primary btn-next">Selanjutnya <i class="fas fa-arrow-right ms-2"></i></button>
    </div>
</div>

<!-- Step 3: Pilihan Jurusan -->
<div class="step-panel" id="panel-2">
    <h5 class="mb-4" style="color:var(--text);border-bottom:2px solid var(--primary);padding-bottom:8px;">
        <span class="badge bg-primary me-2">3</span>Pilihan Program Keahlian
    </h5>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label" for="program_id">Pilihan 1 <span class="text-danger">*</span></label>
            <select class="form-select" id="program_id" name="program_id" required>
                <option value="">-- Pilih Jurusan --</option>
                <?php foreach ($programs as $prog): ?>
                <option value="<?= $prog['id'] ?>"><?= htmlspecialchars($prog['name']) ?> (<?= htmlspecialchars($prog['code'] ?? '') ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="program_choice2">Pilihan 2</label>
            <select class="form-select" id="program_choice2" name="program_choice2">
                <option value="">-- Pilih Jurusan (opsional) --</option>
                <?php foreach ($programs as $prog): ?>
                <option value="<?= $prog['id'] ?>"><?= htmlspecialchars($prog['name']) ?> (<?= htmlspecialchars($prog['code'] ?? '') ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="d-flex justify-content-between mt-4">
        <button type="button" class="btn btn-outline-secondary btn-prev"><i class="fas fa-arrow-left me-2"></i>Sebelumnya</button>
        <button type="button" class="btn btn-primary btn-next">Selanjutnya <i class="fas fa-arrow-right ms-2"></i></button>
    </div>
</div>

<!-- Step 4: Data Orang Tua -->
<div class="step-panel" id="panel-3">
    <h5 class="mb-4" style="color:var(--text);border-bottom:2px solid var(--primary);padding-bottom:8px;">
        <span class="badge bg-primary me-2">4</span>Data Orang Tua / Wali
    </h5>
    <div class="row g-3">
        <div class="col-12"><h6 style="color:var(--text-muted);">Data Ayah</h6></div>
        <div class="col-md-6">
            <label class="form-label" for="father_name">Nama Ayah <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="father_name" name="father_name" required placeholder="Nama lengkap ayah">
        </div>
        <div class="col-md-3">
            <label class="form-label" for="father_job">Pekerjaan Ayah</label>
            <input type="text" class="form-control" id="father_job" name="father_job" placeholder="Pekerjaan">
        </div>
        <div class="col-md-3">
            <label class="form-label" for="father_phone">HP Ayah</label>
            <input type="text" class="form-control" id="father_phone" name="father_phone" placeholder="08xxxxxxxxxx">
        </div>
        <div class="col-12"><h6 style="color:var(--text-muted);">Data Ibu</h6></div>
        <div class="col-md-6">
            <label class="form-label" for="mother_name">Nama Ibu <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="mother_name" name="mother_name" required placeholder="Nama lengkap ibu">
        </div>
        <div class="col-md-3">
            <label class="form-label" for="mother_job">Pekerjaan Ibu</label>
            <input type="text" class="form-control" id="mother_job" name="mother_job" placeholder="Pekerjaan">
        </div>
        <div class="col-md-3">
            <label class="form-label" for="parent_income">Penghasilan Orang Tua</label>
            <select class="form-select" id="parent_income" name="parent_income">
                <option value="">-- Pilih --</option>
                <option>< Rp 1.000.000</option><option>Rp 1.000.000 - 3.000.000</option>
                <option>Rp 3.000.000 - 5.000.000</option><option>> Rp 5.000.000</option>
            </select>
        </div>
    </div>
    <div class="d-flex justify-content-between mt-4">
        <button type="button" class="btn btn-outline-secondary btn-prev"><i class="fas fa-arrow-left me-2"></i>Sebelumnya</button>
        <button type="button" class="btn btn-primary btn-next">Selanjutnya <i class="fas fa-arrow-right ms-2"></i></button>
    </div>
</div>

<!-- Step 5: Upload Dokumen -->
<div class="step-panel" id="panel-4">
    <h5 class="mb-4" style="color:var(--text);border-bottom:2px solid var(--primary);padding-bottom:8px;">
        <span class="badge bg-primary me-2">5</span>Upload Dokumen
    </h5>
    <div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>Format: JPG, PNG, PDF. Ukuran maks: 2MB per file.</div>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label" for="photo">Pas Foto 3x4 <span class="text-danger">*</span></label>
            <input type="file" class="form-control image-upload-input" id="photo" name="photo" accept="image/*" data-preview="#previewPhoto">
            <img id="previewPhoto" src="" style="display:none;max-height:120px;margin-top:8px;border-radius:6px;">
        </div>
        <div class="col-md-6">
            <label class="form-label" for="doc_kk">Kartu Keluarga</label>
            <input type="file" class="form-control" id="doc_kk" name="doc_kk" accept="image/*,.pdf">
        </div>
        <div class="col-md-6">
            <label class="form-label" for="doc_akta">Akta Kelahiran</label>
            <input type="file" class="form-control" id="doc_akta" name="doc_akta" accept="image/*,.pdf">
        </div>
        <div class="col-md-6">
            <label class="form-label" for="doc_ijazah">Ijazah/SKL SMP</label>
            <input type="file" class="form-control" id="doc_ijazah" name="doc_ijazah" accept="image/*,.pdf">
        </div>
        <div class="col-md-6">
            <label class="form-label" for="doc_raport">Rapor SMP</label>
            <input type="file" class="form-control" id="doc_raport" name="doc_raport" accept="image/*,.pdf">
        </div>
    </div>
    <div class="d-flex justify-content-between mt-4">
        <button type="button" class="btn btn-outline-secondary btn-prev"><i class="fas fa-arrow-left me-2"></i>Sebelumnya</button>
        <button type="button" class="btn btn-primary btn-next">Preview <i class="fas fa-arrow-right ms-2"></i></button>
    </div>
</div>

<!-- Step 6: Preview & Submit -->
<div class="step-panel" id="panel-5">
    <h5 class="mb-4" style="color:var(--text);border-bottom:2px solid var(--primary);padding-bottom:8px;">
        <span class="badge bg-success me-2"><i class="fas fa-eye"></i></span>Preview & Konfirmasi
    </h5>
    <div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i>Periksa kembali data Anda sebelum mengirim. Data yang sudah dikirim tidak dapat diubah.</div>
    <div id="previewData" class="mb-4" style="color:var(--text);"></div>
    <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" id="agreeCheck" required>
        <label class="form-check-label" for="agreeCheck" style="color:var(--text);">
            Saya menyatakan bahwa data yang saya isi adalah benar dan dapat dipertanggungjawabkan.
        </label>
    </div>
    <div class="d-flex justify-content-between mt-4">
        <button type="button" class="btn btn-outline-secondary btn-prev"><i class="fas fa-arrow-left me-2"></i>Sebelumnya</button>
        <button type="submit" class="btn btn-success btn-lg px-5">
            <i class="fas fa-paper-plane me-2"></i>Kirim Pendaftaran
        </button>
    </div>
</div>

</form>
</div>
</div>
<?php endif; ?>
</div>
</section>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

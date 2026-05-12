<?php $adminPageTitle = 'Detail Pendaftar'; require_once __DIR__ . '/../layouts/admin_header.php'; ?>

<div class="row g-4">
    <div class="col-lg-8">
        <!-- Personal Data -->
        <div class="admin-card mb-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 style="margin:0;"><i class="fas fa-user me-2 text-primary"></i>Data Pribadi</h5>
                <span class="badge badge-<?= $reg['status'] ?> fs-6"><?= ucfirst($reg['status']) ?></span>
            </div>
            <div class="row g-2">
                <?php
                $fields = [
                    'No. Pendaftaran' => $reg['registration_number'],
                    'Nama Lengkap' => $reg['full_name'],
                    'Nama Panggilan' => $reg['nick_name'] ?? '-',
                    'Jenis Kelamin' => $reg['gender'] === 'L' ? 'Laki-laki' : 'Perempuan',
                    'Tempat Lahir' => $reg['birth_place'] ?? '-',
                    'Tanggal Lahir' => !empty($reg['birth_date']) ? formatDate($reg['birth_date']) : '-',
                    'Agama' => $reg['religion'] ?? '-',
                    'Alamat' => $reg['address'] ?? '-',
                    'No HP' => $reg['phone'] ?? '-',
                    'Email' => $reg['email'] ?? '-',
                ];
                foreach ($fields as $label => $val): ?>
                <div class="col-sm-6">
                    <div style="color:var(--text-muted);font-size:0.8rem;"><?= $label ?></div>
                    <div style="color:var(--text);font-weight:500;"><?= htmlspecialchars($val) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- School Data -->
        <div class="admin-card mb-4">
            <h5><i class="fas fa-school me-2 text-primary"></i>Data Asal Sekolah & Jurusan</h5>
            <div class="row g-2">
                <?php
                $schoolFields = [
                    'Asal Sekolah' => $reg['school_origin'] ?? '-',
                    'NISN' => $reg['nisn'] ?? '-',
                    'Nilai Rata-rata' => $reg['un_score'] ? number_format($reg['un_score'], 2) : '-',
                    'Pilihan 1' => $reg['program_name'] ?? '-',
                    'Pilihan 2' => $reg['program_choice2_name'] ?? '-',
                ];
                foreach ($schoolFields as $label => $val): ?>
                <div class="col-sm-6">
                    <div style="color:var(--text-muted);font-size:0.8rem;"><?= $label ?></div>
                    <div style="color:var(--text);font-weight:500;"><?= htmlspecialchars($val) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Parent Data -->
        <div class="admin-card mb-4">
            <h5><i class="fas fa-users me-2 text-primary"></i>Data Orang Tua</h5>
            <div class="row g-2">
                <?php
                $parentFields = [
                    'Nama Ayah' => $reg['father_name'] ?? '-',
                    'Pekerjaan Ayah' => $reg['father_job'] ?? '-',
                    'HP Ayah' => $reg['father_phone'] ?? '-',
                    'Nama Ibu' => $reg['mother_name'] ?? '-',
                    'Pekerjaan Ibu' => $reg['mother_job'] ?? '-',
                    'Penghasilan' => $reg['parent_income'] ?? '-',
                ];
                foreach ($parentFields as $label => $val): ?>
                <div class="col-sm-4">
                    <div style="color:var(--text-muted);font-size:0.8rem;"><?= $label ?></div>
                    <div style="color:var(--text);font-weight:500;"><?= htmlspecialchars($val) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Documents -->
        <div class="admin-card">
            <h5><i class="fas fa-file-alt me-2 text-primary"></i>Dokumen Upload</h5>
            <div class="row g-3">
                <?php
                $docs = [
                    'Pas Foto' => $reg['photo'] ?? '',
                    'Kartu Keluarga' => $reg['doc_kk'] ?? '',
                    'Akta Kelahiran' => $reg['doc_akta'] ?? '',
                    'Ijazah/SKL' => $reg['doc_ijazah'] ?? '',
                    'Rapor' => $reg['doc_raport'] ?? '',
                ];
                foreach ($docs as $docLabel => $docFile): ?>
                <div class="col-sm-4">
                    <div style="color:var(--text-muted);font-size:0.8rem;margin-bottom:6px;"><?= $docLabel ?></div>
                    <?php if (!empty($docFile)): ?>
                        <a href="<?= UPLOAD_URL . htmlspecialchars($docFile) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-download me-1"></i>Lihat Dokumen
                        </a>
                    <?php else: ?>
                        <span style="color:var(--text-muted);font-size:0.85rem;">Tidak diupload</span>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Sidebar: Status Update -->
    <div class="col-lg-4">
        <div class="admin-card mb-4">
            <h5>Update Status</h5>
            <form method="POST" action="<?= APP_URL ?>/admin/spmb/status/<?= $reg['id'] ?>">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <div class="mb-3">
                    <label class="form-label">Status Pendaftaran</label>
                    <select name="status" class="form-select">
                        <?php foreach (['pending'=>'Menunggu Verifikasi','verifikasi'=>'Sedang Diverifikasi','diterima'=>'Diterima','ditolak'=>'Ditolak'] as $val => $label): ?>
                        <option value="<?= $val ?>" <?= $reg['status'] === $val ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Catatan</label>
                    <textarea name="notes" class="form-control" rows="4" placeholder="Catatan untuk calon siswa..."><?= htmlspecialchars($reg['notes'] ?? '') ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
            </form>
        </div>
        <div class="admin-card">
            <h5>Info Pendaftaran</h5>
            <div class="mb-2"><small style="color:var(--text-muted);">Mendaftar</small><div style="color:var(--text);font-weight:500;"><?= formatDate($reg['created_at']) ?></div></div>
            <div class="mb-2"><small style="color:var(--text-muted);">Tahun Ajaran</small><div style="color:var(--text);font-weight:500;"><?= htmlspecialchars($reg['academic_year']) ?></div></div>
            <hr style="border-color:var(--border);">
            <a href="<?= APP_URL ?>/admin/spmb/pendaftar" class="btn btn-outline-secondary w-100 btn-sm">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/admin_footer.php'; ?>

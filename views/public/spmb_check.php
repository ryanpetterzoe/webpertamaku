<?php
$pageTitle = 'Cek Status Pendaftaran - ' . ($settings['school_name'] ?? 'SMK Pertamaku');
require_once __DIR__ . '/../layouts/header.php';
?>

<div style="background:linear-gradient(135deg,var(--primary),var(--primary-dark));padding:60px 0;color:#fff;">
    <div class="container">
        <h1 class="fw-bold mb-2">Cek Status Pendaftaran</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="<?= APP_URL ?>/" style="color:rgba(255,255,255,0.8);">Beranda</a></li><li class="breadcrumb-item"><a href="<?= APP_URL ?>/spmb" style="color:rgba(255,255,255,0.8);">SPMB</a></li><li class="breadcrumb-item active text-white">Cek Status</li></ol>
        </nav>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <!-- Search Form -->
                <div class="contact-card mb-4">
                    <h4 class="fw-bold mb-3" style="color:var(--text);">Masukkan Nomor Pendaftaran</h4>
                    <form method="POST" action="<?= APP_URL ?>/spmb/cek">
                        <input type="hidden" name="_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                        <div class="input-group">
                            <span class="input-group-text" style="background:var(--bg-secondary);border-color:var(--border);color:var(--text-muted);"><i class="fas fa-search"></i></span>
                            <input type="text" name="reg_number" class="form-control" placeholder="Contoh: REG-2025-0001" required value="<?= htmlspecialchars($_POST['reg_number'] ?? '') ?>" style="font-size:1.05rem;">
                            <button class="btn btn-primary px-4" type="submit">Cek</button>
                        </div>
                    </form>
                </div>

                <?php if (!empty($error)): ?>
                <div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <?php if (!empty($registration)): ?>
                <!-- Result Card -->
                <?php
                $statusColors = ['pending'=>'warning','verifikasi'=>'info','diterima'=>'success','ditolak'=>'danger'];
                $statusLabels = ['pending'=>'Menunggu Verifikasi','verifikasi'=>'Sedang Diverifikasi','diterima'=>'Diterima','ditolak'=>'Tidak Diterima'];
                $status = $registration['status'] ?? 'pending';
                $statusColor = $statusColors[$status] ?? 'secondary';
                $statusLabel = $statusLabels[$status] ?? ucfirst($status);
                ?>
                <div class="card" style="border-top:5px solid var(--<?= $statusColor === 'warning' ? 'bs-warning' : ($statusColor === 'success' ? 'bs-success' : ($statusColor === 'danger' ? 'bs-danger' : 'bs-info')) ?>);">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h5 style="color:var(--text);margin:0;">Hasil Pencarian</h5>
                            <span class="badge bg-<?= $statusColor ?> fs-6"><?= $statusLabel ?></span>
                        </div>
                        <table class="table">
                            <tbody>
                                <tr><td style="color:var(--text-muted);width:45%;">No. Pendaftaran</td><td style="color:var(--text);font-weight:700;"><?= htmlspecialchars($registration['registration_number']) ?></td></tr>
                                <tr><td style="color:var(--text-muted);">Nama Lengkap</td><td style="color:var(--text);"><?= htmlspecialchars($registration['full_name']) ?></td></tr>
                                <tr><td style="color:var(--text-muted);">Tahun Ajaran</td><td style="color:var(--text);"><?= htmlspecialchars($registration['academic_year']) ?></td></tr>
                                <tr><td style="color:var(--text-muted);">Pilihan Jurusan</td><td style="color:var(--text);"><?= htmlspecialchars($registration['program_name'] ?? '-') ?></td></tr>
                                <tr><td style="color:var(--text-muted);">Tanggal Daftar</td><td style="color:var(--text);"><?= formatDate($registration['created_at']) ?></td></tr>
                                <tr>
                                    <td style="color:var(--text-muted);">Status</td>
                                    <td><span class="badge bg-<?= $statusColor ?> fs-6"><?= $statusLabel ?></span></td>
                                </tr>
                            </tbody>
                        </table>

                        <?php if (!empty($registration['notes'])): ?>
                        <div class="alert alert-info mt-3">
                            <strong><i class="fas fa-comment me-2"></i>Catatan dari Panitia:</strong><br>
                            <?= nl2br(htmlspecialchars($registration['notes'])) ?>
                        </div>
                        <?php endif; ?>

                        <?php if ($status === 'diterima'): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Selamat!</strong> Anda diterima di <?= htmlspecialchars($registration['program_name'] ?? 'program yang dipilih') ?>. Silakan hubungi sekolah untuk informasi daftar ulang.
                        </div>
                        <?php elseif ($status === 'ditolak'): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-times-circle me-2"></i>
                            <strong>Mohon maaf.</strong> Pendaftaran Anda tidak dapat dilanjutkan. Silakan hubungi pihak sekolah untuk informasi lebih lanjut.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="text-center mt-4">
                    <a href="<?= APP_URL ?>/spmb" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali ke SPMB
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

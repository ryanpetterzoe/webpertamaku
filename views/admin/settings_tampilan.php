<?php $adminPageTitle = 'Pengaturan Tampilan'; require_once __DIR__ . '/../layouts/admin_header.php'; ?>

<div class="admin-card" style="max-width:700px;">

    <h5 class="mb-4"><i class="fas fa-palette me-2" style="color:var(--primary);"></i>Pengaturan Warna & Tampilan</h5>

    <form method="POST" action="<?= APP_URL ?>/admin/settings/tampilan">
        <input type="hidden" name="_token" value="<?= htmlspecialchars(isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : '') ?>">

        <!-- Color Preview -->
        <div class="mb-4 p-3 rounded" style="background:var(--bg-secondary);border:1px solid var(--border);">
            <p style="color:var(--text-muted);font-size:.82rem;margin-bottom:12px;">
                <i class="fas fa-info-circle me-1"></i>
                Preview warna akan terlihat setelah disimpan dan halaman direfresh.
            </p>
            <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
                <div style="width:40px;height:40px;border-radius:10px;background:<?= htmlspecialchars($settings['accent_primary'] ?? '#2563eb') ?>;box-shadow:0 2px 8px rgba(0,0,0,.2);"></div>
                <span style="color:var(--text-secondary);font-size:.85rem;">Warna primer saat ini: <strong><?= htmlspecialchars($settings['accent_primary'] ?? '#2563eb') ?></strong></span>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <!-- Primary Color -->
            <div class="col-md-6">
                <label class="form-label fw-semibold">
                    <i class="fas fa-circle me-1" style="color:var(--primary);"></i>
                    Warna Primer (Accent)
                </label>
                <p style="color:var(--text-muted);font-size:.78rem;margin-bottom:8px;">
                    Warna utama: tombol, link, highlight, badge, icon.
                </p>
                <div style="display:flex;gap:8px;align-items:center;">
                    <input type="color" name="accent_primary" class="form-control form-control-color"
                           style="width:60px;height:40px;padding:2px;border-radius:8px;cursor:pointer;"
                           value="<?= htmlspecialchars($settings['accent_primary'] ?? '#2563eb') ?>">
                    <input type="text" id="accent_primary_hex" class="form-control"
                           style="font-family:monospace;"
                           value="<?= htmlspecialchars($settings['accent_primary'] ?? '#2563eb') ?>"
                           placeholder="#2563eb"
                           oninput="syncColorPicker('accent_primary', this.value)">
                </div>
            </div>

            <!-- Dark variant -->
            <div class="col-md-6">
                <label class="form-label fw-semibold">
                    <i class="fas fa-circle me-1" style="color:var(--primary-dark);"></i>
                    Warna Primer (Gelap)
                </label>
                <p style="color:var(--text-muted);font-size:.78rem;margin-bottom:8px;">
                    Varian gelap untuk hover, gradient, dan dark mode.
                </p>
                <div style="display:flex;gap:8px;align-items:center;">
                    <input type="color" name="accent_dark" class="form-control form-control-color"
                           style="width:60px;height:40px;padding:2px;border-radius:8px;cursor:pointer;"
                           value="<?= htmlspecialchars($settings['accent_dark'] ?? '#1d4ed8') ?>">
                    <input type="text" id="accent_dark_hex" class="form-control"
                           style="font-family:monospace;"
                           value="<?= htmlspecialchars($settings['accent_dark'] ?? '#1d4ed8') ?>"
                           placeholder="#1d4ed8"
                           oninput="syncColorPicker('accent_dark', this.value)">
                </div>
            </div>
        </div>

        <!-- Preset Colors -->
        <div class="mb-4">
            <label class="form-label fw-semibold">Preset Warna Populer</label>
            <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:8px;">
                <?php
                $presets = [
                    ['name'=>'Biru (Default)', 'primary'=>'#2563eb', 'dark'=>'#1d4ed8'],
                    ['name'=>'Ungu',           'primary'=>'#7c3aed', 'dark'=>'#6d28d9'],
                    ['name'=>'Merah',          'primary'=>'#dc2626', 'dark'=>'#b91c1c'],
                    ['name'=>'Hijau',          'primary'=>'#16a34a', 'dark'=>'#15803d'],
                    ['name'=>'Oranye',         'primary'=>'#ea580c', 'dark'=>'#c2410c'],
                    ['name'=>'Teal',           'primary'=>'#0d9488', 'dark'=>'#0f766e'],
                    ['name'=>'Pink',           'primary'=>'#db2777', 'dark'=>'#be185d'],
                    ['name'=>'Hitam',          'primary'=>'#1e293b', 'dark'=>'#0f172a'],
                ];
                foreach ($presets as $p): ?>
                <button type="button" class="preset-btn"
                        data-primary="<?= $p['primary'] ?>"
                        data-dark="<?= $p['dark'] ?>"
                        title="<?= $p['name'] ?>"
                        onclick="applyPreset(this)"
                        style="width:36px;height:36px;border-radius:10px;border:3px solid transparent;
                               background:<?= $p['primary'] ?>;cursor:pointer;transition:all .2s;
                               <?= (($settings['accent_primary'] ?? '#2563eb') === $p['primary']) ? 'border-color:#f1f5f9;transform:scale(1.15);' : '' ?>">
                </button>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Default Theme -->
        <div class="mb-4">
            <label class="form-label fw-semibold">Tema Default Website</label>
            <p style="color:var(--text-muted);font-size:.78rem;margin-bottom:10px;">
                Tema yang digunakan pengunjung baru (sebelum toggle manual).
            </p>
            <div style="display:flex;gap:12px;">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:10px 16px;border-radius:10px;border:2px solid var(--border);background:var(--bg-card);">
                    <input type="radio" name="theme_default" value="light"
                           <?= (($settings['theme_default'] ?? 'light') === 'light') ? 'checked' : '' ?>>
                    <i class="fas fa-sun" style="color:#fbbf24;"></i>
                    <span style="color:var(--text);font-weight:500;">Light Mode</span>
                </label>
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:10px 16px;border-radius:10px;border:2px solid var(--border);background:var(--bg-card);">
                    <input type="radio" name="theme_default" value="dark"
                           <?= (($settings['theme_default'] ?? 'light') === 'dark') ? 'checked' : '' ?>>
                    <i class="fas fa-moon" style="color:#6366f1;"></i>
                    <span style="color:var(--text);font-weight:500;">Dark Mode</span>
                </label>
            </div>
        </div>

        <div class="d-flex gap-2 align-items-center">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Simpan Tampilan
            </button>
            <button type="button" class="btn btn-outline-secondary" onclick="resetToDefault()">
                <i class="fas fa-undo me-1"></i> Reset ke Default
            </button>
        </div>
    </form>
</div>

<!-- CSS Variable Injector -->
<?php
$pri = $settings['accent_primary'] ?? '#2563eb';
$drk = $settings['accent_dark']    ?? '#1d4ed8';
// Convert hex to rgb for glow effects
function hexToRgb($hex) {
    $hex = ltrim($hex, '#');
    if (strlen($hex) === 3) $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
    return [hexdec(substr($hex,0,2)), hexdec(substr($hex,2,2)), hexdec(substr($hex,4,2))];
}
$rgb = hexToRgb($pri);
?>
<style>
/* Live accent color preview for admin panel settings page */
:root {
    --accent-preview: <?= htmlspecialchars($pri) ?>;
}
</style>

<script>
function syncColorPicker(name, hexVal) {
    // Sync color picker with text input
    var picker = document.querySelector('input[name="' + name + '"][type="color"]');
    var text   = document.getElementById(name + '_hex');
    if (/^#[0-9a-fA-F]{6}$/.test(hexVal)) {
        if (picker) picker.value = hexVal;
    }
    // Sync text from picker
    if (picker) {
        picker.addEventListener('input', function() {
            if (text) text.value = this.value;
        });
    }
}

// Init sync on load
document.querySelectorAll('input[type="color"]').forEach(function(picker) {
    var name = picker.name;
    var text = document.getElementById(name + '_hex');
    picker.addEventListener('input', function() {
        if (text) text.value = this.value;
    });
});

function applyPreset(btn) {
    var primary = btn.getAttribute('data-primary');
    var dark    = btn.getAttribute('data-dark');

    // Set color pickers
    var p1 = document.querySelector('input[name="accent_primary"][type="color"]');
    var p2 = document.querySelector('input[name="accent_dark"][type="color"]');
    var t1 = document.getElementById('accent_primary_hex');
    var t2 = document.getElementById('accent_dark_hex');

    if (p1) p1.value = primary;
    if (p2) p2.value = dark;
    if (t1) t1.value = primary;
    if (t2) t2.value = dark;

    // Highlight selected preset
    document.querySelectorAll('.preset-btn').forEach(function(b) {
        b.style.borderColor = 'transparent';
        b.style.transform = 'scale(1)';
    });
    btn.style.borderColor = '#f1f5f9';
    btn.style.transform = 'scale(1.15)';
}

function resetToDefault() {
    var p1 = document.querySelector('input[name="accent_primary"][type="color"]');
    var p2 = document.querySelector('input[name="accent_dark"][type="color"]');
    var t1 = document.getElementById('accent_primary_hex');
    var t2 = document.getElementById('accent_dark_hex');
    if (p1) p1.value = '#2563eb';
    if (p2) p2.value = '#1d4ed8';
    if (t1) t1.value = '#2563eb';
    if (t2) t2.value = '#1d4ed8';
}
</script>

<?php require_once __DIR__ . '/../layouts/admin_footer.php'; ?>

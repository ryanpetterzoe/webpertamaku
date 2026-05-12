<?php
// Standalone login page — no layout needed
$schoolName = getSetting('school_name') ?: 'SMK Pertamaku';
$csrfToken  = isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : '';
$postUser   = isset($_POST['username'])      ? htmlspecialchars($_POST['username']) : '';
?>
<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - <?= htmlspecialchars($schoolName) ?></title>
    <script>(function(){
        var t = localStorage.getItem('smk_theme') || 'dark';
        document.documentElement.setAttribute('data-theme', t);
    })();</script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
        min-height: 100vh;
        background: linear-gradient(135deg, #0b1120 0%, #1e3a8a 50%, #1e1b4b 100%);
        display: flex; align-items: center; justify-content: center;
        font-family: 'Segoe UI', system-ui, sans-serif;
        padding: 24px;
        position: relative;
    }
    body::before {
        content: '';
        position: fixed; inset: 0;
        background:
            radial-gradient(ellipse 60% 50% at 20% 40%, rgba(59,130,246,.15) 0%, transparent 60%),
            radial-gradient(ellipse 40% 40% at 80% 70%, rgba(99,102,241,.12) 0%, transparent 50%);
        pointer-events: none;
    }
    .login-wrap {
        width: 100%; max-width: 420px;
        background: rgba(255,255,255,.06);
        backdrop-filter: blur(24px);
        -webkit-backdrop-filter: blur(24px);
        border: 1px solid rgba(255,255,255,.12);
        border-radius: 20px;
        padding: 40px 36px;
        position: relative; z-index: 1;
        box-shadow: 0 24px 60px rgba(0,0,0,.4);
    }
    .login-logo { text-align: center; margin-bottom: 28px; }
    .login-logo .ico {
        width: 68px; height: 68px;
        background: linear-gradient(135deg, #2563eb, #6366f1);
        border-radius: 18px;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 28px; margin-bottom: 14px;
        box-shadow: 0 8px 28px rgba(37,99,235,.4);
    }
    .login-logo h4 { color: #f1f5f9; font-size: 1.15rem; font-weight: 700; margin-bottom: 4px; }
    .login-logo p  { color: #64748b; font-size: .85rem; }

    /* Form elements — selalu dark di login page */
    .form-label { color: #cbd5e1 !important; font-size: .83rem; font-weight: 600; margin-bottom: 6px; display: block; }
    .input-group-text {
        background: rgba(255,255,255,.07) !important;
        border: 1px solid rgba(255,255,255,.15) !important;
        color: #94a3b8 !important;
    }
    .form-control {
        background: rgba(255,255,255,.07) !important;
        border: 1px solid rgba(255,255,255,.15) !important;
        color: #f1f5f9 !important;
        font-size: .9rem;
    }
    .form-control:focus {
        background: rgba(255,255,255,.1) !important;
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59,130,246,.2) !important;
        color: #f1f5f9 !important;
        outline: none;
    }
    .form-control::placeholder { color: #475569 !important; }
    .btn-eye {
        background: rgba(255,255,255,.07) !important;
        border: 1px solid rgba(255,255,255,.15) !important;
        color: #94a3b8 !important;
    }
    .btn-eye:hover { background: rgba(255,255,255,.12) !important; color: #cbd5e1 !important; }

    .btn-login {
        width: 100%; padding: 12px;
        background: linear-gradient(135deg, #2563eb, #6366f1);
        color: #fff; border: none; border-radius: 10px;
        font-size: .95rem; font-weight: 700; cursor: pointer;
        transition: all .2s;
        box-shadow: 0 4px 16px rgba(37,99,235,.35);
    }
    .btn-login:hover { transform: translateY(-1px); box-shadow: 0 6px 22px rgba(37,99,235,.45); }

    .form-check-label { color: #94a3b8 !important; font-size: .85rem; }
    .form-check-input { background-color: rgba(255,255,255,.1); border-color: rgba(255,255,255,.25); }

    .back-link { color: #64748b; font-size: .83rem; text-decoration: none; transition: color .2s; }
    .back-link:hover { color: #94a3b8; }

    .alert-danger  { background: rgba(239,68,68,.15); border: 1px solid rgba(239,68,68,.3); color: #fca5a5 !important; border-radius: 10px; font-size: .88rem; }
    .alert-success { background: rgba(34,197,94,.1);  border: 1px solid rgba(34,197,94,.25); color: #86efac !important; border-radius: 10px; font-size: .88rem; }

    @media (max-width: 480px) { .login-wrap { padding: 28px 20px; } }
    </style>
</head>
<body>
<div class="login-wrap">
    <div class="login-logo">
        <div class="ico">🎓</div>
        <h4><?= htmlspecialchars($schoolName) ?></h4>
        <p>Panel Administrasi</p>
    </div>

    <?php if (!empty($error)): ?>
    <div class="alert alert-danger mb-3">
        <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['flash_success'])): ?>
    <div class="alert alert-success mb-3">
        <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($_SESSION['flash_success']) ?>
    </div>
    <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <form method="POST" action="<?= APP_URL ?>/admin/login">
        <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken) ?>">

        <div class="mb-3">
            <label class="form-label">Username</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-user"></i></span>
                <input type="text" name="username" class="form-control"
                       placeholder="Masukkan username"
                       value="<?= $postUser ?>"
                       autocomplete="username" autofocus required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" name="password" class="form-control"
                       id="pwdInput" placeholder="Masukkan password"
                       autocomplete="current-password" required>
                <button type="button" class="btn btn-eye" onclick="togglePwd()">
                    <i class="fas fa-eye" id="eyeIcon"></i>
                </button>
            </div>
        </div>

        <div class="mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
                <label class="form-check-label" for="rememberMe">Ingat Saya</label>
            </div>
        </div>

        <button type="submit" class="btn-login">
            <i class="fas fa-sign-in-alt me-2"></i>Masuk ke Admin
        </button>
    </form>

    <div class="text-center mt-4">
        <a href="<?= APP_URL ?>/" class="back-link">
            <i class="fas fa-arrow-left me-1"></i>Kembali ke Website
        </a>
    </div>
</div>

<script>
function togglePwd() {
    var inp  = document.getElementById('pwdInput');
    var icon = document.getElementById('eyeIcon');
    if (inp.type === 'password') {
        inp.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        inp.type = 'password';
        icon.className = 'fas fa-eye';
    }
}
</script>
</body>
</html>

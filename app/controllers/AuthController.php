<?php
/**
 * AuthController - Handles admin authentication
 */
class AuthController {

    public function login() {
        // Already logged in?
        if (isLoggedIn()) {
            redirect('/admin/dashboard');
        }

        // Generate CSRF token
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim(isset($_POST['username']) ? $_POST['username'] : '');
            $password = isset($_POST['password']) ? $_POST['password'] : '';

            if (empty($username) || empty($password)) {
                $error = 'Username dan password wajib diisi.';
            } else {
                $db = getDB();
                $uname = $db->real_escape_string($username);
                // Cek tanpa is_active dulu, lalu cek manual (kolom mungkin tidak ada di semua versi)
                $res = $db->query("SELECT * FROM admins WHERE username='$uname' LIMIT 1");

                if ($res && $admin = $res->fetch_assoc()) {
                    // Cek is_active jika kolom ada
                    if (isset($admin['is_active']) && $admin['is_active'] == 0) {
                        $error = 'Akun tidak aktif. Hubungi administrator.';
                    } elseif (password_verify($password, $admin['password'])) {
                        // Set session
                        $_SESSION['admin_id'] = $admin['id'];
                        $_SESSION['admin_name'] = $admin['name'];
                        $_SESSION['admin_role'] = $admin['role'];
                        $_SESSION['admin_username'] = $admin['username'];

                        // Update last login
                        $db->query("UPDATE admins SET last_login=NOW() WHERE id=" . (int)$admin['id']);

                        // Remember me cookie
                        if (!empty($_POST['remember'])) {
                            $token = bin2hex(random_bytes(32));
                            setcookie('remember_admin', $token, time() + 30 * 24 * 3600, '/');
                        }

                        redirect('/admin/dashboard');
                    } else {
                        $error = 'Password salah.';
                    }
                } else {
                    $error = 'Username tidak ditemukan. Pastikan username sudah benar.';
                }
            }
        }

        require_once __DIR__ . '/../../views/auth/login.php';
    }

    public function logout() {
        $_SESSION = [];
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 42000, '/');
        }
        setcookie('remember_admin', '', time() - 3600, '/');
        session_destroy();
        redirect('/admin/login');
    }
}

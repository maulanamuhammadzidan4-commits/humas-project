<?php
session_start();
require_once '../../backend/connection.php';
require_once '../../backend/helpers.php';
require_once '../../backend/repositories/bootstrap.php';

if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

// Periksa apakah sedang dalam periode lockout
if (isset($_SESSION['lockout_time'])) {
    $time_passed = time() - $_SESSION['lockout_time'];
    if ($time_passed < 60) {
        $_SESSION['login_error'] = "Terlalu banyak percobaan gagal. Akses dibatasi sementara.";
        header("Location: ../login_admin.php");
        exit;
    } else {
        $_SESSION['login_attempts'] = 0;
        unset($_SESSION['lockout_time']);
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../login_admin.php");
    exit;
}

try {
    verifyCsrfToken($_POST['csrf_token'] ?? null);
    $username = validateString($_POST['username'] ?? null, 'Username', 1, 50);
    $password = $_POST['password'] ?? null;

    if (!is_string($password) || $password === '' || strlen($password) > 255) {
        throw new InvalidArgumentException('Username dan password wajib diisi.');
    }

    $user = UserRepository::findByUsername($koneksi, $username);

    $password_benar = false;
    if ($user) {
        $storedPassword = (string) $user['password'];
        if (password_verify($password, $storedPassword)) {
            $password_benar = true;
        } elseif (password_get_info($storedPassword)['algo'] === null && hash_equals($storedPassword, $password)) {
            if (!UserRepository::updatePassword($koneksi, (int) $user['id_user'], password_hash($password, PASSWORD_DEFAULT))) {
                throw new RuntimeException('Gagal memperbarui kredensial akun lama.');
            }
            $password_benar = true;
        }
    }

    if ($password_benar) {
        // Reset percobaan login
        $_SESSION['login_attempts'] = 0;
        unset($_SESSION['lockout_time']);
        unset($_SESSION['login_error']);

        // Regenerasi session untuk keamanan
        session_regenerate_id(true);
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        // Simpan data user ke session
        $_SESSION['user_id']      = $user['id_user'];
        $_SESSION['username']     = $user['username'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
        $_SESSION['jabatan']      = $user['jabatan'];

        // Penanda popup login berhasil
        $_SESSION['login_success_flash'] = true;

        header("Location: ../login_admin.php");
        exit;
    } else {
        $_SESSION['login_attempts']++;
        if ($_SESSION['login_attempts'] >= 3) {
            $_SESSION['lockout_time'] = time();
            $_SESSION['login_error'] = "Login gagal 3x berturut-turut. Akses Anda dikunci selama 1 menit!";
        } else {
            $sisa = 3 - $_SESSION['login_attempts'];
            $_SESSION['login_error'] = "Username atau password salah! Sisa percobaan: {$sisa}x lagi.";
        }
        header("Location: ../login_admin.php");
        exit;
    }
} catch (InvalidArgumentException $e) {
    $_SESSION['login_error'] = $e->getMessage();
    header("Location: ../login_admin.php");
    exit;
} catch (Throwable $e) {
    error_log('Login handler error: ' . $e->getMessage());
    $_SESSION['login_error'] = "Terjadi kesalahan sistem. Silakan coba kembali.";
    header("Location: ../login_admin.php");
    exit;
}

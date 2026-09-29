<?php
session_start();
require_once '../../backend/connection.php';
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

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($username === '' || $password === '') {
    $_SESSION['login_error'] = "Username dan password wajib diisi!";
    header("Location: ../login_admin.php");
    exit;
}

try {
    $user = UserRepository::findByUsername($koneksi, $username);

    $password_benar = false;
    if ($user) {
        if (password_verify($password, $user['password'])) {
            $password_benar = true;
        } elseif ($password === $user['password']) {
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
} catch (Exception $e) {
    $_SESSION['login_error'] = "Terjadi kesalahan sistem: " . $e->getMessage();
    header("Location: ../login_admin.php");
    exit;
}

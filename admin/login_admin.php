<?php

session_start();
require_once '../backend/connection.php';

if (isset($_SESSION['user_id']) && !isset($_SESSION['login_success_flash'])) {
    header("Location: index.php");
    exit;
}
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

$error = "";
$is_locked = false;
$remaining_seconds = 0;
$show_success_popup = false;

if (isset($_SESSION['lockout_time'])) {
    $time_passed = time() - $_SESSION['lockout_time'];
    if ($time_passed < 60) {
        $is_locked = true;
        $remaining_seconds = 60 - $time_passed;
        $error = "Terlalu banyak percobaan gagal. Akses dibatasi sementara.";
    } else {
        // Waktu lockout selesai
        $_SESSION['login_attempts'] = 0;
        unset($_SESSION['lockout_time']);
        $is_locked = false;
        $remaining_seconds = 0;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$is_locked) {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    if ($username === '' || $password === '') {
        $error = "Username dan password wajib diisi!";
    } else {
        try {
            $stmt = mysqli_prepare(
                $koneksi,
                "SELECT id_user, username, password, nama_lengkap, jabatan 
                 FROM users 
                 WHERE username = ? 
                 LIMIT 1"
            );

            if (!$stmt) {
                throw new Exception("Gagal menyiapkan query database.");
            }
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
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

                // Regenerasi session untuk keamanan
                session_regenerate_id(true);

                // Simpan data user ke session
                $_SESSION['user_id']      = $user['id_user'];
                $_SESSION['username']     = $user['username'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                $_SESSION['jabatan']      = $user['jabatan'];

                // Penanda popup login berhasil
                $_SESSION['login_success_flash'] = true;

                $show_success_popup = true;

            } else {
                $_SESSION['login_attempts']++;
                if ($_SESSION['login_attempts'] >= 3) {
                    $_SESSION['lockout_time'] = time();
                    $is_locked = true;
                    $remaining_seconds = 60;
                    $error = "Login gagal 3x berturut-turut. Akses Anda dikunci selama 1 menit!";
                } else {
                    $sisa = 3 - $_SESSION['login_attempts'];
                    $error = "Username atau password salah! Sisa percobaan: {$sisa}x lagi.";
                }
            }
        } catch (Exception $e) {
            $error = "Terjadi kesalahan sistem: " . $e->getMessage();
        }
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Humas SMK</title>
    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../frontend/assets/css/login_admin.css">
</head>
<body>
    <div class="login-card">
        <!-- HEADER -->
        <div class="login-header">
            <div class="login-header-icon">
                <i class="fa-solid fa-user-shield"></i>
            </div>
            <h2>Humas SMK</h2>
            <p>Portal Login Panel Admin</p>
        </div>
        <!-- BODY -->
        <div class="login-body">
            <!-- ERROR -->
            <?php if (!empty($error)): ?>
                <div class="alert-error">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <div>
                        <?= htmlspecialchars($error); ?>
                    </div>
                </div>
            <?php endif; ?>
            <!-- LOCKOUT -->
            <?php if ($is_locked): ?>
                <div class="alert-warning" id="lockout-box">
                    <i class="fa-solid fa-hourglass-half"></i>
                    <div>
                        Akses Terkunci!
                        Silakan tunggu
                        <span id="countdown">
                            <?= $remaining_seconds; ?>
                        </span>
                        detik lagi.
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- FORM LOGIN -->
            <form
                action="login_admin.php"
                method="POST"
                id="loginForm">
                <!-- USERNAME -->
                <div class="form-group">
                    <label for="username">
                        Username Admin
                    </label>
                    <div class="input-wrapper">
                        <input type="text" id="username" name="username" class="form-control" placeholder="Masukkan username" required autofocus autocomplete="username" value="<?= htmlspecialchars($_POST['username'] ?? ''); ?>"
                            <?= $is_locked ? 'disabled' : ''; ?>>
                        <i class="fa-solid fa-user"></i>
                    </div>
                </div>

                <!-- PASSWORD -->
                <div class="form-group">
                    <label for="password">
                        Password
                    </label>
                    <div class="input-wrapper">
                        <input
                       type="password" id="password" name="password" class="form-control" placeholder="Masukkan password" required autocomplete="current-password"
                            <?= $is_locked ? 'disabled' : ''; ?>>
                        <i class="fa-solid fa-lock"></i>
                    </div>
                </div>

                <!-- BUTTON -->
                <button
                    type="submit"
                    id="btnSubmit"
                    class="btn-submit"
                    <?= $is_locked ? 'disabled' : ''; ?>>
                    <i class="fa-solid fa-right-to-bracket"></i>
                    <span>
                        <?= $is_locked
                            ? 'Terkunci (Tunggu Countdown)'
                            : 'Masuk ke Panel';
                        ?>
                    </span>
                </button>
            </form>

            <!-- BACK -->
            <a href="../frontend/index.php" class="back-link"> 
                <i class="fa-solid fa-arrow-left"></i>
                Kembali ke Beranda Utama
            </a>
        </div>
    </div>
    <script>
        <?php if ($show_success_popup): ?>
            Swal.fire({
                title: 'Login Berhasil!',
                text: 'Selamat datang, <?= htmlspecialchars($_SESSION['nama_lengkap']); ?>',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false,
                timerProgressBar: true,
                allowOutsideClick: false
            }).then(function () {
                window.location.href = 'dashboard.php';
            });
        <?php endif; ?>
        <?php if ($is_locked && $remaining_seconds > 0): ?>
            let timeLeft = <?= $remaining_seconds; ?>;
            const countdownEl =
                document.getElementById('countdown');
            const btnSubmit =
                document.getElementById('btnSubmit');
            const usernameInput =
                document.getElementById('username');
            const passwordInput =
                document.getElementById('password');
            const lockoutBox =
                document.getElementById('lockout-box');
            const timerInterval = setInterval(function () {
                timeLeft--;
                if (countdownEl) {
                    countdownEl.textContent = timeLeft;
                }
                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    if (lockoutBox) {
                        lockoutBox.style.display = 'none';
                    }
                    usernameInput.disabled = false;
                    passwordInput.disabled = false;
                    btnSubmit.disabled = false;
                    const btnSpan =
                        btnSubmit.querySelector('span');
                    if (btnSpan) {
                        btnSpan.textContent =
                            'Masuk ke Panel';
                    }
                }
            }, 1000);
        <?php endif; ?>
    </script>
</body>
</html>
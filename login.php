<?php

session_start();
require_once 'koneksi.php';

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['user_id']) && !isset($_SESSION['login_success_flash'])) {
    header("Location: dashboard.php");
    exit;
}

// Inisialisasi variabel session percobaan
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

$error = "";
$is_locked = false;
$remaining_seconds = 0;

// Cek status pembatasan waktu (lockout 1 menit)
if (isset($_SESSION['lockout_time'])) {
    $time_passed = time() - $_SESSION['lockout_time'];
    if ($time_passed < 60) {
        $is_locked = true;
        $remaining_seconds = 60 - $time_passed;
        $error = "Terlalu banyak percobaan gagal (3x). Akses dibatasi sementara.";
    } else {
        // Reset jika sudah lewat 60 detik
        $_SESSION['login_attempts'] = 0;
        unset($_SESSION['lockout_time']);
    }
}



$show_success_popup = false;

// Proses Form Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$is_locked) {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = "Username dan password wajib diisi!";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && (password_verify($password, $user['password']) || $password === $user['password'])) {
                // Login Berhasil - Reset Percobaan Gagal
                $_SESSION['login_attempts'] = 0;
                unset($_SESSION['lockout_time']);

                // Simpan data session
                $_SESSION['user_id']      = $user['id_user'];
                $_SESSION['username']     = $user['username'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                $_SESSION['jabatan']      = $user['jabatan'];
                $_SESSION['login_success_flash'] = true;

                $show_success_popup = true;
            } else {
                // Login Gagal - Tambah Counter Gagal
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
    <title>Login Admin - Humas SMKN 1 Maja</title>
    <!-- SweetAlert2 CDN untuk Pop-up -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary: #1e3a8a;
            --secondary: #0284c7;
            --dark: #1e293b;
            --light: #f8fafc;
            --accent: #f59e0b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #1e3a8a 0%, #0284c7 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .login-card {
            background: white;
            width: 100%;
            max-width: 420px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .login-header {
            background-color: var(--primary);
            color: white;
            text-align: center;
            padding: 2rem 1.5rem;
            border-bottom: 4px solid var(--accent);
        }

        .login-header h2 {
            font-size: 1.5rem;
            margin-bottom: 0.25rem;
        }

        .login-header p {
            font-size: 0.875rem;
            opacity: 0.85;
        }

        .login-body {
            padding: 2rem 1.5rem;
        }

        .alert-error {
            background-color: #fee2e2;
            color: #991b1b;
            padding: 0.75rem 1rem;
            border-radius: 6px;
            border: 1px solid #fca5a5;
            margin-bottom: 1.25rem;
            font-size: 0.875rem;
            line-height: 1.4;
        }

        .alert-warning {
            background-color: #fef3c7;
            color: #92400e;
            padding: 0.75rem 1rem;
            border-radius: 6px;
            border: 1px solid #fcd34d;
            margin-bottom: 1.25rem;
            font-size: 0.875rem;
            font-weight: 600;
            text-align: center;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--dark);
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 0.95rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--secondary);
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.2);
        }

        .form-control:disabled {
            background-color: #f1f5f9;
            cursor: not-allowed;
        }

        .btn-submit {
            width: 100%;
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 0.85rem;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.2s;
            margin-top: 0.5rem;
        }

        .btn-submit:hover:not(:disabled) {
            background-color: #1e3a8a;
            filter: brightness(1.1);
        }

        .btn-submit:disabled {
            background-color: #94a3b8;
            cursor: not-allowed;
        }

        .demo-info {
            margin-top: 1.5rem;
            padding: 0.75rem;
            background-color: #f1f5f9;
            border-radius: 6px;
            font-size: 0.8rem;
            color: #475569;
            text-align: center;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 1.25rem;
            color: var(--secondary);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-header">
            <h2>Humas SMKN 1 Maja</h2>
            <p>Portal Login Panel Administrator</p>
        </div>
        
        <div class="login-body">
            <?php if (!empty($error)): ?>
                <div class="alert-error" id="error-box">
                    ⚠️ <?= htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($is_locked): ?>
                <div class="alert-warning" id="lockout-box">
                    ⏳ Akses Terkunci! Silakan tunggu <span id="countdown"><?= $remaining_seconds; ?></span> detik lagi.
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST" id="loginForm">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Masukkan username" required autofocus value="<?= htmlspecialchars($_POST['username'] ?? ''); ?>" <?= $is_locked ? 'disabled' : ''; ?>>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan password" required <?= $is_locked ? 'disabled' : ''; ?>>
                </div>

                <button type="submit" id="btnSubmit" class="btn-submit" <?= $is_locked ? 'disabled' : ''; ?>>
                    <?= $is_locked ? 'Terkunci (Tunggu Countdown)' : 'Masuk ke Panel'; ?>
                </button>
            </form>

            <div class="demo-info">
                🔑 <strong>Default Admin Login:</strong><br>
                Username: <code>admin</code> | Password: <code>admin123</code>
            </div>

            <a href="index.html" class="back-link">← Kembali ke Beranda Utama</a>
        </div>
    </div>

    <script>
        // Fitur 1: Pop-up Login Berhasil
        <?php if ($show_success_popup): ?>
            Swal.fire({
                title: 'Login Berhasil!',
                text: 'Selamat datang, <?= htmlspecialchars($_SESSION['nama_lengkap']); ?>',
                icon: 'success',
                timer: 1800,
                showConfirmButton: false,
                timerProgressBar: true,
                allowOutsideClick: false
            }).then(function() {
                <?php unset($_SESSION['login_success_flash']); ?>
                window.location.href = 'dashboard.php';
            });
        <?php endif; ?>

        // Fitur 2: Hitung Mundur 60 Detik Pembatasan Salah 3x
        <?php if ($is_locked && $remaining_seconds > 0): ?>
            let timeLeft = <?= $remaining_seconds; ?>;
            const countdownEl = document.getElementById('countdown');
            const btnSubmit = document.getElementById('btnSubmit');
            const usernameInput = document.getElementById('username');
            const passwordInput = document.getElementById('password');
            const lockoutBox = document.getElementById('lockout-box');

            const timerInterval = setInterval(function() {
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
                    btnSubmit.textContent = 'Masuk ke Panel';
                }
            }, 1000);
        <?php endif; ?>
    </script>

</body>
</html>
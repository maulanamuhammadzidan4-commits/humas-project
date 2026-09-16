<?php

session_start();
require_once '../../backend/connection.php';

if (isset($_SESSION['user_id']) && !isset($_SESSION['login_success_flash'])) {
    header("Location: ../frontend/index.php");
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
    <style>
        :root {
            --font-primary: 'Plus Jakarta Sans', sans-serif;
            --primary-navy: #0f172a;
            --primary-slate: #1e293b;
            --brand-blue: #2563eb;
            --brand-blue-hover: #1d4ed8;
            --brand-gold: #f59e0b;
            --bg-main: #f8fafc;
            --radius-md: 12px;
            --radius-lg: 20px;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: var(--font-primary);
        }

        body {
            background:
                linear-gradient(
                    135deg,
                    rgba(15, 23, 42, 0.95) 0%,
                    rgba(30, 41, 59, 0.90) 100%
                ),
                url("frontend/assets/img/smk1maja.jpeg");
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background:
                radial-gradient(
                    circle at 50% 30%,
                    rgba(37, 99, 235, 0.25) 0%,
                    transparent 70%
                );
            pointer-events: none;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(16px);
            width: 100%;
            max-width: 440px;
            border-radius: var(--radius-lg);
            box-shadow:
                0 20px 40px rgba(15, 23, 42, 0.25);
            overflow: hidden;
            border:
                1px solid rgba(255, 255, 255, 0.3);
            position: relative;
            z-index: 2;
        }

        .login-header {
            background:
                linear-gradient(
                    135deg,
                    var(--primary-navy) 0%,
                    var(--primary-slate) 100%
                );
            color: white;
            text-align: center;
            padding: 2.25rem 1.5rem;
            position: relative;
        }

        .login-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background:
                linear-gradient(
                    90deg,
                    var(--brand-blue),
                    var(--brand-gold)
                );
        }

        .login-header-icon {
            width: 56px;
            height: 56px;
            background:
                rgba(37, 99, 235, 0.2);
            border:
                1px solid rgba(96, 165, 250, 0.4);
            color: #60a5fa;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin: 0 auto 12px;
        }

        .login-header h2 {
            font-size: 1.4rem;
            font-weight: 800;
            letter-spacing: -0.3px;
            margin-bottom: 0.35rem;
        }

        .login-header p {
            font-size: 0.875rem;
            color: #94a3b8;
            font-weight: 500;
        }

        .login-body {
            padding: 2rem 1.75rem;
        }

        .alert-error {
            background-color: #fef2f2;
            color: #991b1b;
            padding: 0.85rem 1rem;
            border-radius: var(--radius-md);
            border:
                1px solid #fecaca;
            margin-bottom: 1.25rem;
            font-size: 0.875rem;
            line-height: 1.4;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
        }

        .alert-warning {
            background-color: #fffbeb;
            color: #92400e;
            padding: 0.85rem 1rem;
            border-radius: var(--radius-md);
            border:
                1px solid #fde68a;
            margin-bottom: 1.25rem;
            font-size: 0.875rem;
            font-weight: 700;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            font-weight: 700;
            color: #0f172a;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 15px;
            transition: color 0.2s;
        }

        .form-control {
            width: 100%;
            padding:
                0.8rem
                1rem
                0.8rem
                2.6rem;
            border:
                1.5px solid #cbd5e1;
            border-radius: var(--radius-md);
            font-size: 0.95rem;
            color: #0f172a;
            background: #f8fafc;
            outline: none;
            transition: all 0.2s;
        }

        .form-control:focus {
            background: white;
            border-color: var(--brand-blue);
            box-shadow:
                0 0 0 4px rgba(37, 99, 235, 0.15);
        }

        .input-wrapper:focus-within i {
            color: var(--brand-blue);
        }

        .form-control:disabled {
            background-color: #f1f5f9;
            cursor: not-allowed;
            opacity: 0.7;
        }

        .btn-submit {
            width: 100%;
            background:
                linear-gradient(
                    135deg,
                    var(--brand-blue) 0%,
                    var(--brand-blue-hover) 100%
                );
            color: white;
            border: none;
            padding: 0.9rem;
            border-radius: var(--radius-md);
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow:
                0 4px 14px rgba(37, 99, 235, 0.3);
        }

        .btn-submit:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow:
                0 6px 20px rgba(37, 99, 235, 0.4);
        }

        .btn-submit:disabled {
            background: #94a3b8;
            box-shadow: none;
            cursor: not-allowed;
        }

        .demo-info {
            margin-top: 1.5rem;
            padding: 0.85rem;
            background-color: #f1f5f9;
            border-radius: var(--radius-md);
            font-size: 0.825rem;
            color: #475569;
            text-align: center;
            border:
                1px solid #e2e8f0;
            line-height: 1.5;
        }

        .demo-info code {
            background: white;
            padding: 2px 6px;
            border-radius: 4px;
            border:
                1px solid #cbd5e1;
            font-weight: 700;
            color: var(--brand-blue);
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
            text-align: center;
            margin-top: 1.25rem;
            color: #64748b;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 600;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: var(--brand-blue);
        }
    </style>
</head>
<body>
    <div class="login-card">
        <!-- HEADER -->
        <div class="login-header">
            <div class="login-header-icon">
                <i class="fa-solid fa-user-shield"></i>
            </div>
            <h2>Humas SMK</h2>
            <p>Portal Login Panel Administrator</p>
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
            <form action="login.php" method="POST" id="loginForm">
                <!-- username-->
                <div class="form-group">

                    <label for="username">
                        Username Admin
                    </label>

                    <div class="input-wrapper">

                        <input type="text" id="username" name="username"
                            class="form-control"
                            placeholder="Masukkan username"
                            required
                            autofocus
                            autocomplete="username"
                            value="<?= htmlspecialchars($_POST['username'] ?? ''); ?>"
                            <?= $is_locked ? 'disabled' : ''; ?>
                        >

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
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            placeholder="Masukkan password"
                            required
                            autocomplete="current-password"
                            <?= $is_locked ? 'disabled' : ''; ?>>
                        <i class="fa-solid fa-lock"></i>

                    </div>
                </div>
                <!-- BUTTON -->
                <button
                    type="submit"
                    id="btnSubmit"
                    class="btn-submit"
                    <?= $is_locked ? 'disabled' : ''; ?>
                >

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
            <a
                href="../index.php" class="back-link">
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
                window.location.href = 'index.html';
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
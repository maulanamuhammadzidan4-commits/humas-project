<?php
session_start();

if (isset($_SESSION['user_id']) && !isset($_SESSION['login_success_flash'])) {
    header("Location: dashboard.php");
    exit;
}

$error = $_SESSION['login_error'] ?? "";
unset($_SESSION['login_error']);

$is_locked = false;
$remaining_seconds = 0;
$show_success_popup = false;

if (!empty($_SESSION['login_success_flash'])) {
    $show_success_popup = true;
    unset($_SESSION['login_success_flash']);
}

if (isset($_SESSION['lockout_time'])) {
    $time_passed = time() - $_SESSION['lockout_time'];
    if ($time_passed < 60) {
        $is_locked = true;
        $remaining_seconds = 60 - $time_passed;
        if (empty($error)) {
            $error = "Terlalu banyak percobaan gagal. Akses dibatasi sementara.";
        }
    } else {
        $_SESSION['login_attempts'] = 0;
        unset($_SESSION['lockout_time']);
        $is_locked = false;
        $remaining_seconds = 0;
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../frontend/assets/css/login_admin.css">
</head>
<body>
    <div class="login-card" id="loginCard"
         data-success="<?= $show_success_popup ? 'true' : 'false'; ?>"
         data-user-name="<?= htmlspecialchars($_SESSION['nama_lengkap'] ?? ''); ?>"
         data-locked="<?= $is_locked ? 'true' : 'false'; ?>"
         data-remaining="<?= (int)$remaining_seconds; ?>">
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
                        Akses Terkunci! Silakan tunggu
                        <span id="countdown"><?= $remaining_seconds; ?></span> detik lagi.
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- FORM LOGIN -->
            <form action="backend/login_handler.php" method="POST" id="loginForm">
                <!-- USERNAME -->
                <div class="form-group">
                    <label for="username">Username Admin</label>
                    <div class="input-wrapper">
                        <input
                            type="text"
                            id="username"
                            name="username"
                            class="form-control"
                            placeholder="Masukkan username"
                            required
                            autofocus
                            autocomplete="username"
                            <?= $is_locked ? 'disabled' : ''; ?>>
                        <i class="fa-solid fa-user"></i>
                    </div>
                </div>

                <!-- PASSWORD -->
                <div class="form-group">
                    <label for="password">Password</label>
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
                    <?= $is_locked ? 'disabled' : ''; ?>>
                    <i class="fa-solid fa-right-to-bracket"></i>
                    <span>
                        <?= $is_locked ? 'Terkunci (Tunggu Countdown)' : 'Masuk ke Panel'; ?>
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
    <!-- External Login JS -->
    <script src="assets/login.js"></script>
</body>
</html>
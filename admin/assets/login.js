/**
 * Login Page Script — Admin Humas SMK (login.js)
 * Mengelola animasi feedback SweetAlert dan countdown lockout timer.
 */
document.addEventListener('DOMContentLoaded', function () {
    const card = document.getElementById('loginCard');
    if (!card) return;

    const isSuccess = card.dataset.success === 'true';
    const userName  = card.dataset.userName || 'Admin';
    const isLocked  = card.dataset.locked === 'true';
    let timeLeft    = parseInt(card.dataset.remaining, 10) || 0;

    // Notifikasi SweetAlert jika login berhasil
    if (isSuccess && typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Login Berhasil!',
            text: 'Selamat datang, ' + userName,
            icon: 'success',
            timer: 1500,
            showConfirmButton: false,
            timerProgressBar: true,
            allowOutsideClick: false
        }).then(function () {
            window.location.href = 'dashboard.php';
        });
    }

    // Countdown timer lockout bila login gagal beberapa kali
    if (isLocked && timeLeft > 0) {
        const countdownEl   = document.getElementById('countdown');
        const btnSubmit     = document.getElementById('btnSubmit');
        const usernameInput = document.getElementById('username');
        const passwordInput = document.getElementById('password');
        const lockoutBox    = document.getElementById('lockout-box');

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
                if (usernameInput) usernameInput.disabled = false;
                if (passwordInput) passwordInput.disabled = false;
                if (btnSubmit) {
                    btnSubmit.disabled = false;
                    const btnSpan = btnSubmit.querySelector('span');
                    if (btnSpan) {
                        btnSpan.textContent = 'Masuk ke Panel';
                    }
                }
            }
        }, 1000);
    }
});

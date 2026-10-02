<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/connection.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/repositories/bootstrap.php';

$redirect = '../frontend/index.php#kontak';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $redirect);
    exit;
}

try {
    verifyCsrfToken($_POST['csrf_token'] ?? null);

    if (!empty($_POST['website'] ?? '')) {
        $_SESSION['contact_flash'] = ['type' => 'error', 'message' => 'Pesan tidak dapat dikirim.'];
        header('Location: ' . $redirect);
        exit;
    }

    $nama = validateString($_POST['nama'] ?? null, 'Nama', 1, 100);
    $email = validateString($_POST['email'] ?? null, 'Email', 1, 150);
    $subjek = validateString($_POST['subjek'] ?? null, 'Subjek', 1, 200);
    $pesan = validateString($_POST['pesan'] ?? null, 'Pesan', 1, 5000);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new InvalidArgumentException('Format email tidak valid.');
    }

    if (!KontakRepository::create($koneksi, [
        'nama' => $nama,
        'email' => $email,
        'subjek' => $subjek,
        'pesan' => $pesan,
    ])) {
        throw new RuntimeException('Gagal menyimpan pesan.');
    }

    $_SESSION['contact_flash'] = ['type' => 'success', 'message' => 'Terima kasih! Pesan Anda berhasil dikirim.'];
    header('Location: ' . $redirect);
    exit;
} catch (InvalidArgumentException $e) {
    $_SESSION['contact_flash'] = ['type' => 'error', 'message' => $e->getMessage()];
    header('Location: ' . $redirect);
    exit;
} catch (Throwable $e) {
    error_log('Contact handler error: ' . $e->getMessage());
    $_SESSION['contact_flash'] = ['type' => 'error', 'message' => 'Pesan gagal disimpan. Silakan coba kembali.'];
    header('Location: ' . $redirect);
    exit;
}

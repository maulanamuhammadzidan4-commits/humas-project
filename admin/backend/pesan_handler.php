<?php
require_once '../../backend/connection.php';
require_once '../../backend/helpers.php';
require_once '../../backend/repositories/bootstrap.php';
$authLoginPath = '../login_admin.php';
require_once '../includes/auth.php';

function redirectPesan(string $message, string $type = 'success'): void
{
    redirectWithMessage('../pesan.php', $message, $type);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pesan.php');
    exit;
}

try {
    verifyCsrfToken($_POST['csrf_token'] ?? null);

    if (isset($_POST['terima_pesan'])) {
        $id_kontak = validateInteger($_POST['id_kontak'] ?? null, 'ID pesan', 1, 2147483647);

        if (!KontakRepository::markAsRead($koneksi, $id_kontak)) {
            throw new RuntimeException('Gagal memperbarui status pesan.');
        }
        redirectPesan('Pesan berhasil ditandai sebagai sudah diterima.');
    }

    throw new InvalidArgumentException('Aksi pesan tidak valid.');
} catch (Throwable $e) {
    redirectPesan('Gagal: ' . $e->getMessage(), 'danger');
}

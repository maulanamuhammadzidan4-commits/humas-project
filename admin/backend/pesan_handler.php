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

if (isset($_POST['terima_pesan'])) {
    $id_kontak = (int) ($_POST['id_kontak'] ?? 0);

    if ($id_kontak <= 0) {
        redirectPesan('ID pesan tidak valid.', 'danger');
    }

    KontakRepository::markAsRead($koneksi, $id_kontak);
    redirectPesan('Pesan berhasil ditandai sebagai sudah diterima.');
}

header('Location: ../pesan.php');
exit;

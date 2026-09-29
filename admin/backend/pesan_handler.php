<?php
session_start();
require_once '../../backend/connection.php';
require_once '../../backend/repositories/bootstrap.php';
$authLoginPath = '../login_admin.php';
require_once '../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['terima_pesan'])) {
        $id_kontak = (int)($_POST['id_kontak'] ?? 0);

        if ($id_kontak > 0) {
            KontakRepository::markAsRead($koneksi, $id_kontak);

            $msg = urlencode("Pesan berhasil ditandai sebagai sudah diterima.");
            header("Location: ../pesan.php?msg=$msg&type=success");
            exit;
        }
    }
}

header("Location: ../pesan.php");
exit;

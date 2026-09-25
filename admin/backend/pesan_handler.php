<?php
session_start();
require_once '../../backend/connection.php';
$authLoginPath = '../login_admin.php';
require_once '../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['terima_pesan'])) {
        $id_kontak = (int)($_POST['id_kontak'] ?? 0);

        if ($id_kontak > 0) {
            $stmt = mysqli_prepare(
                $koneksi,
                "UPDATE kontak SET status = 'Sudah Dibaca' WHERE id_kontak = ?"
            );
            mysqli_stmt_bind_param($stmt, "i", $id_kontak);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            $msg = urlencode("Pesan berhasil ditandai sebagai sudah diterima.");
            header("Location: ../pesan.php?msg=$msg&type=success");
            exit;
        }
    }
}

header("Location: ../pesan.php");
exit;

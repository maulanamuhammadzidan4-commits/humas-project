<?php

require_once '../../backend/connection.php';

/* =====================================
   PASTIKAN REQUEST BERASAL DARI FORM
===================================== */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php#kontak");
    exit;
}


/* =====================================
   AMBIL DATA DARI FORM
===================================== */

$nama   = trim($_POST['nama'] ?? '');
$email  = trim($_POST['email'] ?? '');
$subjek = trim($_POST['subjek'] ?? '');
$pesan  = trim($_POST['pesan'] ?? '');


/* =====================================
   VALIDASI DATA
===================================== */

if ($nama === '' || $email === '' || $subjek === '' || $pesan === '') {

    echo "<script>
        alert('Semua data wajib diisi!');
        window.history.back();
    </script>";

    exit;
}


/* =====================================
   VALIDASI EMAIL
===================================== */

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    echo "<script>
        alert('Format email tidak valid!');
        window.history.back();
    </script>";

    exit;
}


/* =====================================
   SIMPAN KE DATABASE
===================================== */

try {

    $sql = "INSERT INTO kontak 
            (nama, email, subjek, pesan)
            VALUES (?, ?, ?, ?)";

    $stmt = $koneksi->prepare($sql);

    $stmt->bind_param(
        "ssss",
        $nama,
        $email,
        $subjek,
        $pesan
    );

    $stmt->execute();

    $stmt->close();


    /* =====================================
       BERHASIL
    ===================================== */

    echo "<script>
        alert('Terima kasih! Pesan Anda berhasil dikirim.');
        window.location.href = '../index.php#kontak';
    </script>";

    exit;


} catch (mysqli_sql_exception $e) {

    /* =====================================
       ERROR DATABASE
    ===================================== */

    echo "<script>
        alert('Pesan gagal disimpan ke database.');
        window.history.back();
    </script>";

    exit;
}
?>
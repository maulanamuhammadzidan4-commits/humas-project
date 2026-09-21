<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../backend/connection.php';

/* =====================================
   PASTIKAN REQUEST BERASAL DARI FORM
===================================== */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php#kontak");
    exit;
}

$redirect = '../index.php#kontak';


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
    $_SESSION['contact_flash'] = ['type' => 'error', 'message' => 'Semua data wajib diisi.'];
    header("Location: $redirect");
    exit;
}


/* =====================================
   VALIDASI EMAIL
===================================== */

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['contact_flash'] = ['type' => 'error', 'message' => 'Format email tidak valid.'];
    header("Location: $redirect");
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

    $_SESSION['contact_flash'] = ['type' => 'success', 'message' => 'Terima kasih! Pesan Anda berhasil dikirim.'];
    header("Location: $redirect");
    exit;


} catch (mysqli_sql_exception $e) {

    /* =====================================
       ERROR DATABASE
    ===================================== */

    $_SESSION['contact_flash'] = ['type' => 'error', 'message' => 'Pesan gagal disimpan ke database.'];
    header("Location: $redirect");
    exit;
}
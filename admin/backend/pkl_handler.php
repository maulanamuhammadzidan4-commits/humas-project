<?php
/**
 * Handler CRUD — Penempatan PKL
 */

session_start();

require_once '../../backend/connection.php';

/* =========================
   CEK LOGIN ADMIN
========================= */

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login_admin.php");
    exit;
}

/* =========================
   CEK KONEKSI
========================= */

if (!$koneksi) {
    die("Koneksi database gagal.");
}

$action = $_POST['action'] ?? '';

try {

    /* =====================================================
       TAMBAH DATA PKL
    ===================================================== */

    if ($action === 'tambah') {

        $nama_siswa = trim($_POST['nama_siswa'] ?? '');
        $nama_perusahaan = trim($_POST['nama_perusahaan'] ?? '');
        $pembimbing = trim($_POST['pembimbing'] ?? '');
        $tanggal_mulai = trim($_POST['tanggal_mulai'] ?? '');
        $tanggal_selesai = trim($_POST['tanggal_selesai'] ?? '');
        $status_penempatan = trim($_POST['status_penempatan'] ?? 'Draft');

        /* VALIDASI */

        if (
            $nama_siswa === '' ||
            $nama_perusahaan === '' ||
            $pembimbing === '' ||
            $tanggal_mulai === '' ||
            $tanggal_selesai === ''
        ) {
            throw new Exception("Semua data PKL wajib diisi.");
        }

        /* =========================
           CARI ID SISWA
        ========================= */

        $stmt = mysqli_prepare(
            $koneksi,
            "SELECT id FROM siswa WHERE nama_siswa = ? LIMIT 1"
        );

        if (!$stmt) {
            throw new Exception(mysqli_error($koneksi));
        }

        mysqli_stmt_bind_param(
            $stmt,
            "s",
            $nama_siswa
        );

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $siswa = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);

        if (!$siswa) {
            throw new Exception(
                "Siswa '$nama_siswa' tidak ditemukan."
            );
        }

        $siswa_id = (int)$siswa['id'];


        /* =========================
           CARI ID PERUSAHAAN
        ========================= */

        $stmt = mysqli_prepare(
            $koneksi,
            "SELECT id FROM perusahaan WHERE nama_perusahaan = ? LIMIT 1"
        );

        if (!$stmt) {
            throw new Exception(mysqli_error($koneksi));
        }

        mysqli_stmt_bind_param(
            $stmt,
            "s",
            $nama_perusahaan
        );

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $perusahaan = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);

        if (!$perusahaan) {
            throw new Exception(
                "Perusahaan '$nama_perusahaan' tidak ditemukan."
            );
        }

        $perusahaan_id = (int)$perusahaan['id'];


        /* =========================
           INSERT
        ========================= */

        $stmt = mysqli_prepare(
            $koneksi,
            "INSERT INTO pkl_penempatan
            (
                siswa_id,
                perusahaan_id,
                pembimbing_guru,
                tanggal_mulai,
                tanggal_selesai,
                status_penempatan
            )
            VALUES (?, ?, ?, ?, ?, ?)"
        );

        if (!$stmt) {
            throw new Exception(mysqli_error($koneksi));
        }

        mysqli_stmt_bind_param(
            $stmt,
            "iissss",
            $siswa_id,
            $perusahaan_id,
            $pembimbing,
            $tanggal_mulai,
            $tanggal_selesai,
            $status_penempatan
        );

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception(
                mysqli_stmt_error($stmt)
            );
        }

        mysqli_stmt_close($stmt);

        header(
            "Location: ../pkl.php?msg=" .
            urlencode("Data PKL berhasil ditambahkan!") .
            "&type=success"
        );
        exit;
    }


    /* =====================================================
       EDIT DATA PKL
    ===================================================== */

    elseif ($action === 'edit') {

        $id = (int)($_POST['id'] ?? 0);

        $nama_siswa = trim($_POST['nama_siswa'] ?? '');
        $nama_perusahaan = trim($_POST['nama_perusahaan'] ?? '');
        $pembimbing = trim($_POST['pembimbing'] ?? '');
        $tanggal_mulai = trim($_POST['tanggal_mulai'] ?? '');
        $tanggal_selesai = trim($_POST['tanggal_selesai'] ?? '');
        $status_penempatan = trim($_POST['status_penempatan'] ?? 'Draft');

        if ($id <= 0) {
            throw new Exception("ID PKL tidak valid.");
        }

        if (
            $nama_siswa === '' ||
            $nama_perusahaan === '' ||
            $pembimbing === '' ||
            $tanggal_mulai === '' ||
            $tanggal_selesai === ''
        ) {
            throw new Exception("Semua data PKL wajib diisi.");
        }


        /* =========================
           CARI SISWA
        ========================= */

        $stmt = mysqli_prepare(
            $koneksi,
            "SELECT id FROM siswa WHERE nama_siswa = ? LIMIT 1"
        );

        if (!$stmt) {
            throw new Exception(mysqli_error($koneksi));
        }

        mysqli_stmt_bind_param(
            $stmt,
            "s",
            $nama_siswa
        );

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $siswa = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);

        if (!$siswa) {
            throw new Exception(
                "Siswa '$nama_siswa' tidak ditemukan."
            );
        }

        $siswa_id = (int)$siswa['id'];


        /* =========================
           CARI PERUSAHAAN
        ========================= */

        $stmt = mysqli_prepare(
            $koneksi,
            "SELECT id FROM perusahaan WHERE nama_perusahaan = ? LIMIT 1"
        );

        if (!$stmt) {
            throw new Exception(mysqli_error($koneksi));
        }

        mysqli_stmt_bind_param(
            $stmt,
            "s",
            $nama_perusahaan
        );

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $perusahaan = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);

        if (!$perusahaan) {
            throw new Exception(
                "Perusahaan '$nama_perusahaan' tidak ditemukan."
            );
        }

        $perusahaan_id = (int)$perusahaan['id'];


        /* =========================
           UPDATE
        ========================= */

        $stmt = mysqli_prepare(
            $koneksi,
            "UPDATE pkl_penempatan
             SET
                siswa_id = ?,
                perusahaan_id = ?,
                pembimbing_guru = ?,
                tanggal_mulai = ?,
                tanggal_selesai = ?,
                status_penempatan = ?
             WHERE id = ?"
        );

        if (!$stmt) {
            throw new Exception(mysqli_error($koneksi));
        }

        mysqli_stmt_bind_param(
            $stmt,
            "iissssi",
            $siswa_id,
            $perusahaan_id,
            $pembimbing,
            $tanggal_mulai,
            $tanggal_selesai,
            $status_penempatan,
            $id
        );

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception(
                mysqli_stmt_error($stmt)
            );
        }

        mysqli_stmt_close($stmt);

        header(
            "Location: ../pkl.php?msg=" .
            urlencode("Data PKL berhasil diperbarui!") .
            "&type=success"
        );
        exit;
    }


    /* =====================================================
       HAPUS DATA PKL
    ===================================================== */

    elseif ($action === 'hapus') {

        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            throw new Exception("ID PKL tidak valid.");
        }

        $stmt = mysqli_prepare(
            $koneksi,
            "DELETE FROM pkl_penempatan WHERE id = ?"
        );

        if (!$stmt) {
            throw new Exception(mysqli_error($koneksi));
        }

        mysqli_stmt_bind_param(
            $stmt,
            "i",
            $id
        );

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception(
                mysqli_stmt_error($stmt)
            );
        }

        mysqli_stmt_close($stmt);

        header(
            "Location: ../pkl.php?msg=" .
            urlencode("Data PKL berhasil dihapus.") .
            "&type=success"
        );
        exit;
    }


    /* =====================================================
       ACTION TIDAK DIKENAL
    ===================================================== */

    else {

        header("Location: ../pkl.php");
        exit;
    }


} catch (Exception $e) {

    header(
        "Location: ../pkl.php?msg=" .
        urlencode("Gagal: " . $e->getMessage()) .
        "&type=danger"
    );

    exit;
}
?>

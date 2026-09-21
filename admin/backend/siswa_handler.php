<?php
/**
 * Handler CRUD — Data Siswa
 */

session_start();

require_once '../../backend/connection.php';
$authLoginPath = '../login_admin.php';
require_once '../includes/auth.php';

/* =========================
   CEK KONEKSI DATABASE
========================= */
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

/* =========================
   AMBIL ACTION
========================= */
$action = $_POST['action'] ?? '';

try {

    /* =====================================================
       TAMBAH DATA SISWA
    ===================================================== */
    if ($action === 'tambah') {

        $nisn       = trim($_POST['nisn'] ?? '');
        $nama_siswa = trim($_POST['nama_siswa'] ?? '');
        $kelas      = trim($_POST['kelas'] ?? '');
        $jurusan    = trim($_POST['jurusan'] ?? '');

        $status_alumni = isset($_POST['status_alumni']) ? 1 : 0;

        /* =========================
           VALIDASI
        ========================= */
        if (
            $nisn === '' ||
            $nama_siswa === '' ||
            $kelas === '' ||
            $jurusan === ''
        ) {
            throw new Exception("Semua data siswa wajib diisi.");
        }

        /* =========================
           CEK NISN
        ========================= */
        $cek = mysqli_prepare(
            $koneksi,
            "SELECT id FROM siswa WHERE nisn = ? LIMIT 1"
        );

        if (!$cek) {
            throw new Exception(mysqli_error($koneksi));
        }

        mysqli_stmt_bind_param(
            $cek,
            "s",
            $nisn
        );

        mysqli_stmt_execute($cek);

        $result = mysqli_stmt_get_result($cek);

        if (mysqli_num_rows($result) > 0) {

            mysqli_stmt_close($cek);

            throw new Exception(
                "NISN tersebut sudah terdaftar."
            );
        }

        mysqli_stmt_close($cek);

        /* =========================
           INSERT DATA
        ========================= */
        $stmt = mysqli_prepare(
            $koneksi,
            "INSERT INTO siswa
            (
                nisn,
                nama_siswa,
                kelas,
                jurusan,
                status_alumni
            )
            VALUES (?, ?, ?, ?, ?)"
        );

        if (!$stmt) {
            throw new Exception(
                mysqli_error($koneksi)
            );
        }

        mysqli_stmt_bind_param(
            $stmt,
            "ssssi",
            $nisn,
            $nama_siswa,
            $kelas,
            $jurusan,
            $status_alumni
        );

        if (!mysqli_stmt_execute($stmt)) {

            throw new Exception(
                mysqli_stmt_error($stmt)
            );
        }

        mysqli_stmt_close($stmt);

        header(
            "Location: ../siswa.php?msg=" .
            urlencode("Data siswa berhasil ditambahkan.") .
            "&type=success"
        );

        exit;
    }


    /* =====================================================
       EDIT DATA SISWA
    ===================================================== */
    elseif ($action === 'edit') {

        $id = (int)($_POST['id'] ?? 0);

        $nisn       = trim($_POST['nisn'] ?? '');
        $nama_siswa = trim($_POST['nama_siswa'] ?? '');
        $kelas      = trim($_POST['kelas'] ?? '');
        $jurusan    = trim($_POST['jurusan'] ?? '');

        $status_alumni = isset($_POST['status_alumni']) ? 1 : 0;

        /* =========================
           VALIDASI ID
        ========================= */
        if ($id <= 0) {

            throw new Exception(
                "ID siswa tidak valid."
            );
        }

        /* =========================
           VALIDASI DATA
        ========================= */
        if (
            $nisn === '' ||
            $nama_siswa === '' ||
            $kelas === '' ||
            $jurusan === ''
        ) {

            throw new Exception(
                "Semua data siswa wajib diisi."
            );
        }

        /* =========================
           CEK NISN
           AGAR TIDAK SAMA DENGAN SISWA LAIN
        ========================= */
        $cek = mysqli_prepare(
            $koneksi,
            "SELECT id
             FROM siswa
             WHERE nisn = ?
             AND id != ?
             LIMIT 1"
        );

        if (!$cek) {
            throw new Exception(
                mysqli_error($koneksi)
            );
        }

        mysqli_stmt_bind_param(
            $cek,
            "si",
            $nisn,
            $id
        );

        mysqli_stmt_execute($cek);

        $hasil_cek = mysqli_stmt_get_result($cek);

        if (mysqli_num_rows($hasil_cek) > 0) {

            mysqli_stmt_close($cek);

            throw new Exception(
                "NISN tersebut sudah digunakan siswa lain."
            );
        }

        mysqli_stmt_close($cek);

        /* =========================
           UPDATE DATA
        ========================= */
        $stmt = mysqli_prepare(
            $koneksi,
            "UPDATE siswa
             SET
                nisn = ?,
                nama_siswa = ?,
                kelas = ?,
                jurusan = ?,
                status_alumni = ?,
                updated_at = CURRENT_TIMESTAMP
             WHERE id = ?"
        );

        if (!$stmt) {

            throw new Exception(
                mysqli_error($koneksi)
            );
        }

        mysqli_stmt_bind_param(
            $stmt,
            "ssssii",
            $nisn,
            $nama_siswa,
            $kelas,
            $jurusan,
            $status_alumni,
            $id
        );

        if (!mysqli_stmt_execute($stmt)) {

            throw new Exception(
                mysqli_stmt_error($stmt)
            );
        }

        mysqli_stmt_close($stmt);

        header(
            "Location: ../siswa.php?msg=" .
            urlencode("Data siswa berhasil diperbarui.") .
            "&type=success"
        );

        exit;
    }


    /* =====================================================
       HAPUS DATA SISWA
    ===================================================== */
    elseif ($action === 'hapus') {

        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {

            throw new Exception(
                "ID siswa tidak valid."
            );
        }

        /* =========================
           DELETE DATA
        ========================= */
        $stmt = mysqli_prepare(
            $koneksi,
            "DELETE FROM siswa WHERE id = ?"
        );

        if (!$stmt) {

            throw new Exception(
                mysqli_error($koneksi)
            );
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
            "Location: ../siswa.php?msg=" .
            urlencode("Data siswa berhasil dihapus.") .
            "&type=success"
        );

        exit;
    }


    /* =====================================================
       ACTION TIDAK DIKENAL
    ===================================================== */
    else {

        header(
            "Location: ../siswa.php"
        );

        exit;
    }


} catch (Exception $e) {

    header(
        "Location: ../siswa.php?msg=" .
        urlencode("Gagal: " . $e->getMessage()) .
        "&type=danger"
    );

    exit;
}
?>

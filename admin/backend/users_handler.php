<?php
/**
 * Handler CRUD — Users Admin
 */
session_start();
require_once '../../backend/connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login_admin.php");
    exit;
}

$action = $_POST['action'] ?? '';

try {
    if ($action === 'tambah') {
        if (empty($_POST['password'])) {
            throw new Exception("Password tidak boleh kosong.");
        }
        $hashed = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($koneksi,
            "INSERT INTO users (username, password, nama_lengkap, jabatan) VALUES (?,?,?,?)"
        );
        mysqli_stmt_bind_param($stmt, 'ssss',
            $_POST['username'], $hashed, $_POST['nama_lengkap'], $_POST['jabatan']
        );
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $msg = urlencode("User berhasil ditambahkan!");
        header("Location: ../users.php?msg=$msg&type=success");

    } elseif ($action === 'edit') {
        if (!empty($_POST['password'])) {
            $hashed = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($koneksi,
                "UPDATE users SET username=?, password=?, nama_lengkap=?, jabatan=? WHERE id_user=?"
            );
            mysqli_stmt_bind_param($stmt, 'ssssi',
                $_POST['username'], $hashed, $_POST['nama_lengkap'], $_POST['jabatan'], $_POST['id_user']
            );
        } else {
            $stmt = mysqli_prepare($koneksi,
                "UPDATE users SET username=?, nama_lengkap=?, jabatan=? WHERE id_user=?"
            );
            mysqli_stmt_bind_param($stmt, 'sssi',
                $_POST['username'], $_POST['nama_lengkap'], $_POST['jabatan'], $_POST['id_user']
            );
        }
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $msg = urlencode("Data user berhasil diperbarui!");
        header("Location: ../users.php?msg=$msg&type=success");

    } elseif ($action === 'hapus') {
        if ($_POST['id_user'] == $_SESSION['user_id']) {
            throw new Exception("Tidak bisa menghapus akun sendiri!");
        }
        $stmt = mysqli_prepare($koneksi, "DELETE FROM users WHERE id_user=?");
        mysqli_stmt_bind_param($stmt, 'i', $_POST['id_user']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $msg = urlencode("User berhasil dihapus.");
        header("Location: ../users.php?msg=$msg&type=success");

    } else {
        header("Location: ../users.php");
    }
} catch (Exception $e) {
    $msg = urlencode("Gagal: " . $e->getMessage());
    header("Location: ../users.php?msg=$msg&type=danger");
}
exit;

<?php
/**
 * Handler CRUD — Users Admin
 */
session_start();
require_once '../../backend/connection.php';
require_once '../../backend/repositories/bootstrap.php';
$authLoginPath = '../login_admin.php';
require_once '../includes/auth.php';

$action = $_POST['action'] ?? '';

try {
    if ($action === 'tambah') {
        if (empty($_POST['password'])) {
            throw new Exception("Password tidak boleh kosong.");
        }
        $hashed = password_hash($_POST['password'], PASSWORD_DEFAULT);
        UserRepository::create($koneksi, [
            'username' => $_POST['username'],
            'password' => $hashed,
            'nama_lengkap' => $_POST['nama_lengkap'],
            'jabatan' => $_POST['jabatan'],
        ]);
        $msg = urlencode("User berhasil ditambahkan!");
        header("Location: ../users.php?msg=$msg&type=success");

    } elseif ($action === 'edit') {
        if (!empty($_POST['password'])) {
            $hashed = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $userData = [
                'username' => $_POST['username'],
                'password' => $hashed,
                'nama_lengkap' => $_POST['nama_lengkap'],
                'jabatan' => $_POST['jabatan'],
            ];
        } else {
            $userData = [
                'username' => $_POST['username'],
                'nama_lengkap' => $_POST['nama_lengkap'],
                'jabatan' => $_POST['jabatan'],
            ];
        }
        UserRepository::update($koneksi, (int)$_POST['id_user'], $userData);
        $msg = urlencode("Data user berhasil diperbarui!");
        header("Location: ../users.php?msg=$msg&type=success");

    } elseif ($action === 'hapus') {
        if ($_POST['id_user'] == $_SESSION['user_id']) {
            throw new Exception("Tidak bisa menghapus akun sendiri!");
        }
        UserRepository::delete($koneksi, (int)$_POST['id_user']);
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

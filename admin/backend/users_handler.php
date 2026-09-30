<?php
/**
 * Handler CRUD — Users Admin
 */
require_once '../../backend/connection.php';
require_once '../../backend/helpers.php';
require_once '../../backend/repositories/bootstrap.php';
$authLoginPath = '../login_admin.php';
require_once '../includes/auth.php';

function redirectUsers(string $message, string $type = 'success'): void
{
    redirectWithMessage('../users.php', $message, $type);
}

$action = $_POST['action'] ?? '';

try {
    if ($action === 'tambah') {
        if (empty($_POST['password'])) {
            throw new Exception('Password tidak boleh kosong.');
        }

        $hashed = password_hash($_POST['password'], PASSWORD_DEFAULT);
        UserRepository::create($koneksi, [
            'username' => $_POST['username'],
            'password' => $hashed,
            'nama_lengkap' => $_POST['nama_lengkap'],
            'jabatan' => $_POST['jabatan'],
        ]);

        redirectUsers('User berhasil ditambahkan!');
    }

    if ($action === 'edit') {
        $userData = [
            'username' => $_POST['username'],
            'nama_lengkap' => $_POST['nama_lengkap'],
            'jabatan' => $_POST['jabatan'],
        ];

        if (!empty($_POST['password'])) {
            $userData['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }

        UserRepository::update($koneksi, (int) ($_POST['id_user'] ?? 0), $userData);
        redirectUsers('Data user berhasil diperbarui!');
    }

    if ($action === 'hapus') {
        $id = (int) ($_POST['id_user'] ?? 0);

        if ($id == $_SESSION['user_id']) {
            throw new Exception('Tidak bisa menghapus akun sendiri!');
        }

        UserRepository::delete($koneksi, $id);
        redirectUsers('User berhasil dihapus.');
    }

    header('Location: ../users.php');
    exit;
} catch (Throwable $e) {
    redirectUsers('Gagal: ' . $e->getMessage(), 'danger');
}
exit;

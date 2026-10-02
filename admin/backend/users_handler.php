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
    verifyCsrfToken($_POST['csrf_token'] ?? null);

    if ($action === 'tambah') {
        $username = validateString($_POST['username'] ?? null, 'Username', 3, 50);
        if (!preg_match('/^[A-Za-z0-9_.-]+$/D', $username)) {
            throw new InvalidArgumentException('Username hanya boleh berisi huruf, angka, titik, garis bawah, dan tanda hubung.');
        }

        $password = validatePassword($_POST['password'] ?? null);

        if (UserRepository::usernameExistsExcept($koneksi, $username)) {
            throw new InvalidArgumentException('Username sudah digunakan.');
        }

        $hashed = password_hash($password, PASSWORD_DEFAULT);
        if (!UserRepository::create($koneksi, [
            'username' => $username,
            'password' => $hashed,
            'nama_lengkap' => validateString($_POST['nama_lengkap'] ?? null, 'Nama lengkap', 1, 100),
            'jabatan' => validateString($_POST['jabatan'] ?? null, 'Jabatan', 1, 50),
        ])) {
            throw new RuntimeException('Gagal menyimpan user.');
        }

        redirectUsers('User berhasil ditambahkan!');
    }

    if ($action === 'edit') {
        $id = validateInteger($_POST['id_user'] ?? null, 'ID user', 1, 2147483647);
        $username = validateString($_POST['username'] ?? null, 'Username', 3, 50);
        if (!preg_match('/^[A-Za-z0-9_.-]+$/D', $username)) {
            throw new InvalidArgumentException('Username hanya boleh berisi huruf, angka, titik, garis bawah, dan tanda hubung.');
        }

        if (UserRepository::usernameExistsExcept($koneksi, $username, $id)) {
            throw new InvalidArgumentException('Username sudah digunakan.');
        }

        $userData = [
            'username' => $username,
            'nama_lengkap' => validateString($_POST['nama_lengkap'] ?? null, 'Nama lengkap', 1, 100),
            'jabatan' => validateString($_POST['jabatan'] ?? null, 'Jabatan', 1, 50),
        ];

        if (isset($_POST['password']) && $_POST['password'] !== '') {
            $password = validatePassword($_POST['password']);
            $userData['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if (!UserRepository::update($koneksi, $id, $userData)) {
            throw new RuntimeException('Gagal memperbarui user.');
        }
        redirectUsers('Data user berhasil diperbarui!');
    }

    if ($action === 'hapus') {
        $id = validateInteger($_POST['id_user'] ?? null, 'ID user', 1, 2147483647);

        if ($id == $_SESSION['user_id']) {
            throw new Exception('Tidak bisa menghapus akun sendiri!');
        }

        if (!UserRepository::delete($koneksi, $id)) {
            throw new RuntimeException('Gagal menghapus user.');
        }
        redirectUsers('User berhasil dihapus.');
    }

    throw new InvalidArgumentException('Aksi user tidak valid.');
} catch (Throwable $e) {
    redirectUsers('Gagal: ' . $e->getMessage(), 'danger');
}
exit;

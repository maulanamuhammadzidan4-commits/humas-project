<?php
session_start();
require_once '../backend/connection.php';
require_once 'includes/auth.php';

$search = trim($_GET['search'] ?? '');
$page   = max(1, (int)($_GET['page'] ?? 1));
$limit  = 10;
$offset = ($page - 1) * $limit;

$where = $search ? "WHERE username LIKE ? OR nama_lengkap LIKE ? OR jabatan LIKE ?" : "";

$stmt_count = mysqli_prepare($koneksi, "SELECT COUNT(*) as n FROM users $where");
if ($search) { $q="%$search%"; mysqli_stmt_bind_param($stmt_count,'sss',$q,$q,$q); }
mysqli_stmt_execute($stmt_count);
$total = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_count))['n'];
$total_pages = ceil($total / $limit);

$stmt_data = mysqli_prepare($koneksi, "SELECT * FROM users $where ORDER BY created_at DESC LIMIT ? OFFSET ?");
if ($search) { $q="%$search%"; mysqli_stmt_bind_param($stmt_data,'sssii',$q,$q,$q,$limit,$offset); }
else { mysqli_stmt_bind_param($stmt_data,'ii',$limit,$offset); }
mysqli_stmt_execute($stmt_data);
$data = mysqli_fetch_all(mysqli_stmt_get_result($stmt_data), MYSQLI_ASSOC);

$page_title = "Manajemen User";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User — Admin Humas SMK</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/admin-style.css">
</head>
<body>
<?php include 'includes/sidebar.php'; ?>
<div class="admin-main">
    <?php include 'includes/header.php'; ?>
    <main class="admin-content">
        <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-<?= htmlspecialchars($_GET['type'] ?? 'success') ?> flash-alert">
            <i class="fa-solid fa-circle-check"></i>
            <?= htmlspecialchars(urldecode($_GET['msg'])) ?>
        </div>
        <?php endif; ?>

        <div class="page-header">
            <div class="page-header-left">
                <h2><i class="fa-solid fa-users-gear" style="color:var(--slate-600);margin-right:8px;"></i>Manajemen User</h2>
                <p>Kelola akun admin yang dapat mengakses panel ini</p>
            </div>
            <button class="btn btn-primary" onclick="openModal('modalTambah')">
                <i class="fa-solid fa-user-plus"></i> Tambah User
            </button>
        </div>

        <!-- Info card current user -->
        <div class="card" style="border-left:4px solid var(--blue);">
            <div class="card-body" style="display:flex;align-items:center;gap:12px;">
                <div class="user-avatar-cell" style="width:48px;height:48px;font-size:1.1rem;">
                    <?= strtoupper(substr($_SESSION['nama_lengkap'] ?? 'A', 0, 1)) ?>
                </div>
                <div>
                    <p style="font-weight:700;color:var(--navy);"><?= htmlspecialchars($_SESSION['nama_lengkap'] ?? '-') ?></p>
                    <p style="font-size:.8rem;color:var(--slate-600);">@<?= htmlspecialchars($_SESSION['username'] ?? '-') ?> — <?= htmlspecialchars($_SESSION['jabatan'] ?? '-') ?></p>
                </div>
                <span class="badge badge-blue" style="margin-left:auto;">Akun Aktif</span>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-list"></i> Daftar User (<?= $total ?>)</span>
                <form method="GET" style="display:flex;gap:.5rem;align-items:center;">
                    <div class="search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari username, nama...">
                    </div>
                    <button type="submit" class="btn btn-outline btn-sm"><i class="fa-solid fa-search"></i></button>
                    <?php if ($search): ?><a href="users.php" class="btn btn-outline btn-sm"><i class="fa-solid fa-xmark"></i></a><?php endif; ?>
                </form>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Lengkap</th>
                            <th>Username</th>
                            <th>Jabatan</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($data)): ?>
                        <tr><td colspan="6"><div class="table-empty"><i class="fa-solid fa-users-slash"></i><p>Tidak ada user.</p></div></td></tr>
                    <?php else: foreach ($data as $i => $row):
                        $is_me = $row['id_user'] == $_SESSION['user_id'];
                    ?>
                        <tr <?= $is_me ? 'style="background:var(--blue-light);"' : '' ?>>
                            <td class="td-no"><?= $offset+$i+1 ?></td>
                            <td>
                                <div class="user-cell">
                                    <div class="user-avatar-cell"><?= strtoupper(substr($row['nama_lengkap'],0,1)) ?></div>
                                    <div>
                                        <span style="font-weight:700;"><?= htmlspecialchars($row['nama_lengkap']) ?></span>
                                        <?= $is_me ? '<span class="badge badge-blue" style="margin-left:6px;font-size:.65rem;">Anda</span>' : '' ?>
                                    </div>
                                </div>
                            </td>
                            <td style="font-family:monospace;font-weight:600;">@<?= htmlspecialchars($row['username']) ?></td>
                            <td><?= htmlspecialchars($row['jabatan'] ?? 'Staf Humas') ?></td>
                            <td style="color:var(--slate-600);font-size:.82rem;"><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                            <td>
                                <div class="action-btns">
                                    <button class="btn btn-warning btn-sm btn-icon" onclick='editUser(<?= json_encode($row) ?>)' title="Edit"><i class="fa-solid fa-pen"></i></button>
                                    <?php if (!$is_me): ?>
                                    <button class="btn btn-danger btn-sm btn-icon" onclick="hapusUser(<?= $row['id_user'] ?>, '<?= htmlspecialchars($row['nama_lengkap'], ENT_QUOTES) ?>')" title="Hapus"><i class="fa-solid fa-trash"></i></button>
                                    <?php else: ?>
                                    <button class="btn btn-sm btn-icon" style="background:var(--slate-200);color:var(--slate-400);cursor:not-allowed;" title="Tidak bisa hapus akun sendiri" disabled><i class="fa-solid fa-trash"></i></button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <span class="pagination-info">Menampilkan <?= $offset+1 ?>–<?= min($offset+$limit,$total) ?> dari <?= $total ?></span>
                <div class="pagination-btns">
                    <?php if ($page > 1): ?><a href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>" class="page-btn"><i class="fa-solid fa-chevron-left"></i></a><?php endif; ?>
                    <?php for ($p=max(1,$page-2); $p<=min($total_pages,$page+2); $p++): ?>
                        <a href="?page=<?= $p ?>&search=<?= urlencode($search) ?>" class="page-btn <?= $p==$page?'active':'' ?>"><?= $p ?></a>
                    <?php endfor; ?>
                    <?php if ($page < $total_pages): ?><a href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>" class="page-btn"><i class="fa-solid fa-chevron-right"></i></a><?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<!-- Modal Tambah -->
<div class="modal-overlay" id="modalTambah">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title"><i class="fa-solid fa-user-plus"></i> Tambah User Admin</span>
            <button class="modal-close" onclick="closeModal('modalTambah')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" action="backend/users_handler.php">
            <input type="hidden" name="action" value="tambah">
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label>Nama Lengkap <span class="required">*</span></label>
                        <input type="text" name="nama_lengkap" class="form-control" required placeholder="Nama lengkap admin">
                    </div>
                    <div class="form-group">
                        <label>Jabatan</label>
                        <input type="text" name="jabatan" class="form-control" placeholder="Staf Humas" value="Staf Humas">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Username <span class="required">*</span></label>
                        <input type="text" name="username" class="form-control" required placeholder="username unik">
                    </div>
                    <div class="form-group">
                        <label>Password <span class="required">*</span></label>
                        <input type="password" name="password" class="form-control" required placeholder="Min. 6 karakter">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalTambah')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal-overlay" id="modalEdit">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title"><i class="fa-solid fa-user-pen"></i> Edit User Admin</span>
            <button class="modal-close" onclick="closeModal('modalEdit')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" action="backend/users_handler.php">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id_user" id="e_id_user">
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label>Nama Lengkap <span class="required">*</span></label>
                        <input type="text" name="nama_lengkap" id="e_nama_lengkap" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Jabatan</label>
                        <input type="text" name="jabatan" id="e_jabatan" class="form-control">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Username <span class="required">*</span></label>
                        <input type="text" name="username" id="e_username" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password Baru <small style="color:var(--slate-400);">(kosongkan jika tidak diganti)</small></label>
                        <input type="password" name="password" class="form-control" placeholder="Biarkan kosong jika tidak diubah">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalEdit')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Perbarui</button>
            </div>
        </form>
    </div>
</div>

<form method="POST" action="backend/users_handler.php" id="formHapus">
    <input type="hidden" name="action" value="hapus">
    <input type="hidden" name="id_user" id="hapus_id">
</form>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/admin.js"></script>
<script>
function editUser(d) {
    document.getElementById('e_id_user').value     = d.id_user;
    document.getElementById('e_nama_lengkap').value = d.nama_lengkap;
    document.getElementById('e_jabatan').value     = d.jabatan || 'Staf Humas';
    document.getElementById('e_username').value    = d.username;
    openModal('modalEdit');
}
function hapusUser(id, nama) {
    Swal.fire({
        title: 'Hapus User?',
        html: `Akun <strong>${nama}</strong> akan dihapus permanen!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then(r => {
        if (r.isConfirmed) {
            document.getElementById('hapus_id').value = id;
            document.getElementById('formHapus').submit();
        }
    });
}
</script>
</body>
</html>

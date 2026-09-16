<?php
session_start();
require_once '../backend/connection.php';
require_once 'includes/auth.php';

$search = trim($_GET['search'] ?? '');
$page   = max(1, (int)($_GET['page'] ?? 1));
$limit  = 10;
$offset = ($page - 1) * $limit;

$where = $search ? "WHERE nisn LIKE ? OR nama LIKE ? OR kelas LIKE ? OR jurusan LIKE ?" : "";

$stmt_count = mysqli_prepare($koneksi, "SELECT COUNT(*) as n FROM siswa $where");
if ($search) { $s = "%$search%"; mysqli_stmt_bind_param($stmt_count, 'ssss', $s,$s,$s,$s); }
mysqli_stmt_execute($stmt_count);
$total = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_count))['n'];
$total_pages = ceil($total / $limit);

$stmt_data = mysqli_prepare($koneksi, "SELECT * FROM siswa $where ORDER BY created_at DESC LIMIT ? OFFSET ?");
if ($search) { $s = "%$search%"; mysqli_stmt_bind_param($stmt_data, 'ssssii', $s,$s,$s,$s,$limit,$offset); }
else { mysqli_stmt_bind_param($stmt_data, 'ii', $limit, $offset); }
mysqli_stmt_execute($stmt_data);
$data = mysqli_fetch_all(mysqli_stmt_get_result($stmt_data), MYSQLI_ASSOC);

$page_title = "Data Siswa";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Siswa — Admin Humas SMK</title>
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
                <h2><i class="fa-solid fa-user-graduate" style="color:#0ea5e9;margin-right:8px;"></i>Data Siswa</h2>
                <p>Kelola data siswa dan status alumni</p>
            </div>
            <button class="btn btn-primary" onclick="openModal('modalTambah')">
                <i class="fa-solid fa-user-plus"></i> Tambah Siswa
            </button>
        </div>

        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-list"></i> Daftar Siswa (<?= $total ?>)</span>
                <form method="GET" style="display:flex;gap:.5rem;align-items:center;">
                    <div class="search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari NISN, nama, kelas...">
                    </div>
                    <button type="submit" class="btn btn-outline btn-sm"><i class="fa-solid fa-search"></i></button>
                    <?php if ($search): ?><a href="siswa.php" class="btn btn-outline btn-sm"><i class="fa-solid fa-xmark"></i></a><?php endif; ?>
                </form>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NISN</th>
                            <th>Nama Lengkap</th>
                            <th>Kelas</th>
                            <th>Jurusan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($data)): ?>
                        <tr><td colspan="7"><div class="table-empty"><i class="fa-solid fa-user-slash"></i><p>Belum ada data siswa.</p></div></td></tr>
                    <?php else: foreach ($data as $i => $row): ?>
                        <tr>
                            <td class="td-no"><?= $offset + $i + 1 ?></td>
                            <td style="font-family:monospace;font-weight:600;letter-spacing:1px;"><?= htmlspecialchars($row['nisn']) ?></td>
                            <td style="font-weight:600;"><?= htmlspecialchars($row['nama']) ?></td>
                            <td><?= htmlspecialchars($row['kelas']) ?></td>
                            <td><?= htmlspecialchars($row['jurusan']) ?></td>
                            <td>
                                <span class="badge <?= $row['status_alumni'] ? 'badge-blue' : 'badge-green' ?>">
                                    <?= $row['status_alumni'] ? 'Alumni' : 'Aktif' ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <button class="btn btn-warning btn-sm btn-icon" onclick='editSiswa(<?= json_encode($row) ?>)' title="Edit">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm btn-icon" onclick="hapusSiswa(<?= $row['id'] ?>, '<?= htmlspecialchars($row['nama'], ENT_QUOTES) ?>')" title="Hapus">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
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
                    <?php for ($p = max(1,$page-2); $p <= min($total_pages,$page+2); $p++): ?>
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
            <span class="modal-title"><i class="fa-solid fa-user-plus"></i> Tambah Siswa</span>
            <button class="modal-close" onclick="closeModal('modalTambah')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" action="backend/siswa_handler.php">
            <input type="hidden" name="action" value="tambah">
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label>NISN <span class="required">*</span></label>
                        <input type="text" name="nisn" class="form-control" required maxlength="10" placeholder="10 digit NISN">
                    </div>
                    <div class="form-group">
                        <label>Nama Lengkap <span class="required">*</span></label>
                        <input type="text" name="nama" class="form-control" required placeholder="Nama siswa">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Kelas <span class="required">*</span></label>
                        <input type="text" name="kelas" class="form-control" required placeholder="XII TKJ 1">
                    </div>
                    <div class="form-group">
                        <label>Jurusan <span class="required">*</span></label>
                        <input type="text" name="jurusan" class="form-control" required placeholder="TKJ / RPL / Akuntansi...">
                    </div>
                </div>
                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                        <input type="checkbox" name="status_alumni" style="width:16px;height:16px;accent-color:var(--blue);">
                        Tandai sebagai Alumni
                    </label>
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
            <span class="modal-title"><i class="fa-solid fa-user-pen"></i> Edit Data Siswa</span>
            <button class="modal-close" onclick="closeModal('modalEdit')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" action="backend/siswa_handler.php">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="e_id">
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label>NISN <span class="required">*</span></label>
                        <input type="text" name="nisn" id="e_nisn" class="form-control" required maxlength="10">
                    </div>
                    <div class="form-group">
                        <label>Nama Lengkap <span class="required">*</span></label>
                        <input type="text" name="nama" id="e_nama" class="form-control" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Kelas <span class="required">*</span></label>
                        <input type="text" name="kelas" id="e_kelas" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Jurusan <span class="required">*</span></label>
                        <input type="text" name="jurusan" id="e_jurusan" class="form-control" required>
                    </div>
                </div>
                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                        <input type="checkbox" name="status_alumni" id="e_status_alumni" style="width:16px;height:16px;accent-color:var(--blue);">
                        Tandai sebagai Alumni
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalEdit')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Perbarui</button>
            </div>
        </form>
    </div>
</div>

<form method="POST" action="backend/siswa_handler.php" id="formHapus">
    <input type="hidden" name="action" value="hapus">
    <input type="hidden" name="id" id="hapus_id">
</form>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/admin.js"></script>
<script>
function editSiswa(d) {
    document.getElementById('e_id').value             = d.id;
    document.getElementById('e_nisn').value           = d.nisn;
    document.getElementById('e_nama').value           = d.nama;
    document.getElementById('e_kelas').value          = d.kelas;
    document.getElementById('e_jurusan').value        = d.jurusan;
    document.getElementById('e_status_alumni').checked = d.status_alumni == 1;
    openModal('modalEdit');
}
function hapusSiswa(id, nama) {
    Swal.fire({
        title: 'Hapus Siswa?',
        html: `Data <strong>${nama}</strong> dan semua data terkait akan dihapus!`,
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

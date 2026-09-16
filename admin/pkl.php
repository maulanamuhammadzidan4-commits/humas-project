<?php
session_start();
require_once '../backend/connection.php';
require_once 'includes/auth.php';

$search = trim($_GET['search'] ?? '');
$page   = max(1, (int)($_GET['page'] ?? 1));
$limit  = 10;
$offset = ($page - 1) * $limit;

$where = $search ? "WHERE s.nama LIKE ? OR p.nama LIKE ? OR pk.pembimbing LIKE ?" : "";

$stmt_count = mysqli_prepare($koneksi,
    "SELECT COUNT(*) as n FROM penempatan_pkl pk
     JOIN siswa s ON s.id = pk.id_siswa
     JOIN perusahaan p ON p.id = pk.id_perusahaan $where"
);
if ($search) { $s="%$search%"; mysqli_stmt_bind_param($stmt_count,'sss',$s,$s,$s); }
mysqli_stmt_execute($stmt_count);
$total = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_count))['n'];
$total_pages = ceil($total / $limit);

$stmt_data = mysqli_prepare($koneksi,
    "SELECT pk.*, s.nama AS nama_siswa, p.nama AS nama_perusahaan
     FROM penempatan_pkl pk
     JOIN siswa s ON s.id = pk.id_siswa
     JOIN perusahaan p ON p.id = pk.id_perusahaan
     $where ORDER BY pk.created_at DESC LIMIT ? OFFSET ?"
);
if ($search) { $s="%$search%"; mysqli_stmt_bind_param($stmt_data,'sssii',$s,$s,$s,$limit,$offset); }
else { mysqli_stmt_bind_param($stmt_data,'ii',$limit,$offset); }
mysqli_stmt_execute($stmt_data);
$data = mysqli_fetch_all(mysqli_stmt_get_result($stmt_data), MYSQLI_ASSOC);

$siswa_list = mysqli_fetch_all(mysqli_query($koneksi, "SELECT id, nama, kelas FROM siswa ORDER BY nama"), MYSQLI_ASSOC);
$perusahaan_list = mysqli_fetch_all(mysqli_query($koneksi, "SELECT id, nama FROM perusahaan ORDER BY nama"), MYSQLI_ASSOC);

$page_title = "Penempatan PKL";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penempatan PKL — Admin Humas SMK</title>
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
                <h2><i class="fa-solid fa-map-location-dot" style="color:var(--orange);margin-right:8px;"></i>Penempatan PKL</h2>
                <p>Kelola data penempatan Praktik Kerja Lapangan siswa</p>
            </div>
            <button class="btn btn-primary" onclick="openModal('modalTambah')">
                <i class="fa-solid fa-plus"></i> Tambah PKL
            </button>
        </div>

        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-list"></i> Data PKL (<?= $total ?>)</span>
                <form method="GET" style="display:flex;gap:.5rem;align-items:center;">
                    <div class="search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari siswa / perusahaan...">
                    </div>
                    <button type="submit" class="btn btn-outline btn-sm"><i class="fa-solid fa-search"></i></button>
                    <?php if ($search): ?><a href="pkl.php" class="btn btn-outline btn-sm"><i class="fa-solid fa-xmark"></i></a><?php endif; ?>
                </form>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Siswa</th>
                            <th>Perusahaan</th>
                            <th>Pembimbing</th>
                            <th>Mulai</th>
                            <th>Selesai</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($data)): ?>
                        <tr><td colspan="8"><div class="table-empty"><i class="fa-solid fa-map"></i><p>Belum ada data PKL.</p></div></td></tr>
                    <?php else: foreach ($data as $i => $row):
                        $badge = match($row['status_penempatan']) {
                            'Disetujui' => 'badge-green',
                            'Selesai'   => 'badge-blue',
                            default     => 'badge-gold',
                        };
                    ?>
                        <tr>
                            <td class="td-no"><?= $offset+$i+1 ?></td>
                            <td style="font-weight:600;"><?= htmlspecialchars($row['nama_siswa']) ?></td>
                            <td><?= htmlspecialchars($row['nama_perusahaan']) ?></td>
                            <td><?= htmlspecialchars($row['pembimbing']) ?></td>
                            <td><?= date('d/m/Y', strtotime($row['tanggal_mulai'])) ?></td>
                            <td><?= date('d/m/Y', strtotime($row['tanggal_selesai'])) ?></td>
                            <td><span class="badge <?= $badge ?>"><?= $row['status_penempatan'] ?></span></td>
                            <td>
                                <div class="action-btns">
                                    <button class="btn btn-warning btn-sm btn-icon" onclick='editPkl(<?= json_encode($row) ?>)' title="Edit"><i class="fa-solid fa-pen"></i></button>
                                    <button class="btn btn-danger btn-sm btn-icon" onclick="hapusPkl(<?= $row['id'] ?>, '<?= htmlspecialchars($row['nama_siswa'], ENT_QUOTES) ?>')" title="Hapus"><i class="fa-solid fa-trash"></i></button>
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
    <div class="modal modal-lg">
        <div class="modal-header">
            <span class="modal-title"><i class="fa-solid fa-map-location-dot"></i> Tambah Penempatan PKL</span>
            <button class="modal-close" onclick="closeModal('modalTambah')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" action="backend/pkl_handler.php">
            <input type="hidden" name="action" value="tambah">
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label>Siswa <span class="required">*</span></label>
                        <select name="id_siswa" class="form-control" required>
                            <option value="">-- Pilih Siswa --</option>
                            <?php foreach ($siswa_list as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nama']) ?> (<?= htmlspecialchars($s['kelas']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Perusahaan <span class="required">*</span></label>
                        <select name="id_perusahaan" class="form-control" required>
                            <option value="">-- Pilih Perusahaan --</option>
                            <?php foreach ($perusahaan_list as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Nama Pembimbing <span class="required">*</span></label>
                    <input type="text" name="pembimbing" class="form-control" required placeholder="Nama pembimbing PKL dari perusahaan">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Tanggal Mulai <span class="required">*</span></label>
                        <input type="date" name="tanggal_mulai" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Selesai <span class="required">*</span></label>
                        <input type="date" name="tanggal_selesai" class="form-control" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Status Penempatan</label>
                    <select name="status_penempatan" class="form-control">
                        <option value="Draft">Draft</option>
                        <option value="Disetujui">Disetujui</option>
                        <option value="Selesai">Selesai</option>
                    </select>
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
    <div class="modal modal-lg">
        <div class="modal-header">
            <span class="modal-title"><i class="fa-solid fa-pen-to-square"></i> Edit Penempatan PKL</span>
            <button class="modal-close" onclick="closeModal('modalEdit')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" action="backend/pkl_handler.php">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="e_id">
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label>Siswa <span class="required">*</span></label>
                        <select name="id_siswa" id="e_id_siswa" class="form-control" required>
                            <?php foreach ($siswa_list as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nama']) ?> (<?= htmlspecialchars($s['kelas']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Perusahaan <span class="required">*</span></label>
                        <select name="id_perusahaan" id="e_id_perusahaan" class="form-control" required>
                            <?php foreach ($perusahaan_list as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Nama Pembimbing <span class="required">*</span></label>
                    <input type="text" name="pembimbing" id="e_pembimbing" class="form-control" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Tanggal Mulai <span class="required">*</span></label>
                        <input type="date" name="tanggal_mulai" id="e_tanggal_mulai" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Selesai <span class="required">*</span></label>
                        <input type="date" name="tanggal_selesai" id="e_tanggal_selesai" class="form-control" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Status Penempatan</label>
                    <select name="status_penempatan" id="e_status_penempatan" class="form-control">
                        <option value="Draft">Draft</option>
                        <option value="Disetujui">Disetujui</option>
                        <option value="Selesai">Selesai</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalEdit')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Perbarui</button>
            </div>
        </form>
    </div>
</div>

<form method="POST" action="backend/pkl_handler.php" id="formHapus">
    <input type="hidden" name="action" value="hapus">
    <input type="hidden" name="id" id="hapus_id">
</form>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/admin.js"></script>
<script>
function editPkl(d) {
    document.getElementById('e_id').value               = d.id;
    document.getElementById('e_id_siswa').value         = d.id_siswa;
    document.getElementById('e_id_perusahaan').value    = d.id_perusahaan;
    document.getElementById('e_pembimbing').value       = d.pembimbing;
    document.getElementById('e_tanggal_mulai').value    = d.tanggal_mulai;
    document.getElementById('e_tanggal_selesai').value  = d.tanggal_selesai;
    document.getElementById('e_status_penempatan').value = d.status_penempatan;
    openModal('modalEdit');
}
function hapusPkl(id, nama) {
    Swal.fire({
        title: 'Hapus Data PKL?',
        html: `Data PKL untuk <strong>${nama}</strong> akan dihapus!`,
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

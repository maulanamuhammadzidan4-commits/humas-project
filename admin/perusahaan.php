<?php
session_start();
require_once '../backend/connection.php';
require_once 'includes/auth.php';

$search = trim($_GET['search'] ?? '');
$page   = max(1, (int)($_GET['page'] ?? 1));
$limit  = 10;
$offset = ($page - 1) * $limit;

$where = $search ? "WHERE nama_perusahaan LIKE ? OR sektor_bidang LIKE ?" : "";
$params_type = "sss";
$params_val  = ["%$search%", "%$search%", "%$search%"];

$count_sql = "SELECT COUNT(*) as n FROM perusahaan $where";
$stmt_count = mysqli_prepare($koneksi, $count_sql);
if ($search) mysqli_stmt_bind_param($stmt_count, $params_type, ...$params_val);
mysqli_stmt_execute($stmt_count);
$total = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_count))['n'];
$total_pages = ceil($total / $limit);

$data_sql = "SELECT * FROM perusahaan $where ORDER BY created_at DESC LIMIT ? OFFSET ?";
$stmt_data = mysqli_prepare($koneksi, $data_sql);
if ($search) {
    $bind_params = array_merge($params_val, [$limit, $offset]);
    mysqli_stmt_bind_param(
        $stmt_data,
        $params_type . 'ii',
        ...$bind_params
    );
} else {
    mysqli_stmt_bind_param(
        $stmt_data,
        'ii',
        $limit,
        $offset
    );
}

mysqli_stmt_execute($stmt_data);
$result = mysqli_stmt_get_result($stmt_data);
$data = mysqli_fetch_all($result, MYSQLI_ASSOC);
$page_title = "Perusahaan Mitra";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perusahaan Mitra — Admin Humas SMK</title>
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
        <!-- Flash -->
        <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-<?= htmlspecialchars($_GET['type'] ?? 'success') ?> flash-alert">
            <i class="fa-solid fa-circle-check"></i>
            <?= htmlspecialchars(urldecode($_GET['msg'])) ?>
        </div>
        <?php endif; ?>
        
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-header-left">
                <h2><i class="fa-solid fa-building" style="color:var(--blue);margin-right:8px;"></i>Perusahaan Mitra</h2>
                <p>Kelola data perusahaan mitra dan status MoU</p>
            </div>
            <button class="btn btn-primary" onclick="openModal('modalTambah')">
                <i class="fa-solid fa-plus"></i> Tambah Perusahaan
            </button>
        </div>

        <!-- Table Card -->
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-list"></i> Daftar Perusahaan (<?= $total ?>)</span>
                <form method="GET" style="display:flex;gap:.5rem;align-items:center;">
                    <div class="search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari perusahaan..." id="searchInput">
                    </div>
                    <button type="submit" class="btn btn-outline btn-sm"><i class="fa-solid fa-search"></i></button>
                    <?php if ($search): ?>
                        <a href="perusahaan.php" class="btn btn-outline btn-sm"><i class="fa-solid fa-xmark"></i></a>
                    <?php endif; ?>
                </form>
            </div>
            <div class="table-wrapper">
                <table id="tabelPerusahaan">
                    <thead>
                        <tr>
                            <th width="40">No</th>
                            <th>Nama Perusahaan</th>
                            <th>Sektor Bidang</th>
                            <th>Jurusan</th>
                            <th>Alamat</th>
                            <th>Penanggung Jawab</th>
                            <th>No. Telepon</th>
                            <th>Status MoU</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($data)): ?>
                        <tr><td colspan="7">
                            <div class="table-empty">
                                <i class="fa-solid fa-building-circle-xmark"></i>
                                <p>Belum ada data perusahaan.</p>
                            </div>
                        </td></tr>
                    <?php else: ?>
                        <?php foreach ($data as $i => $row):
                            $badge = match($row['status_mou']) {
                                'Aktif'     => 'badge-green',
                                'Kadaluarsa'=> 'badge-red',
                                default     => 'badge-gold',
                            };
                        ?>
                        <tr>
                            <td class="td-no"><?= $offset + $i + 1 ?></td>
                            <td style="font-weight:600;"><?= htmlspecialchars($row['nama_perusahaan']) ?></td>
                            <td><?= htmlspecialchars($row['sektor_bidang']) ?></td>
                            <td><?= htmlspecialchars($row['jurusan']) ?></td>
                            <td><?= htmlspecialchars($row['alamat']) ?></td>
                            <td><?= htmlspecialchars($row['penanggung_jawab']) ?></td>
                            <td><?= htmlspecialchars($row['no_telepon']) ?></td>
                            <td><span class="badge <?= $badge ?>"><?= $row['status_mou'] ?></span></td>
                            <td>
                                <div class="action-btns">
                                    <button class="btn btn-warning btn-sm btn-icon"
                                        onclick="editPerusahaan(<?= htmlspecialchars(json_encode($row)) ?>)"
                                        title="Edit">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm btn-icon"
                                        onclick="hapusPerusahaan(<?= $row['id'] ?>, '<?= htmlspecialchars($row['nama_perusahaan'], ENT_QUOTES) ?>')"
                                        title="Hapus">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <span class="pagination-info">
                    Menampilkan <?= $offset+1 ?>–<?= min($offset+$limit, $total) ?> dari <?= $total ?> data
                </span>
                <div class="pagination-btns">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>" class="page-btn"><i class="fa-solid fa-chevron-left"></i></a>
                    <?php endif; ?>
                    <?php for ($p = max(1,$page-2); $p <= min($total_pages,$page+2); $p++): ?>
                        <a href="?page=<?= $p ?>&search=<?= urlencode($search) ?>" class="page-btn <?= $p==$page?'active':'' ?>"><?= $p ?></a>
                    <?php endfor; ?>
                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>" class="page-btn"><i class="fa-solid fa-chevron-right"></i></a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<!-- ══ MODAL TAMBAH ══ -->
<div class="modal-overlay" id="modalTambah">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title"><i class="fa-solid fa-building-circle-arrow-right"></i> Tambah Perusahaan</span>
            <button class="modal-close" onclick="closeModal('modalTambah')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" action="backend/perusahaan_handler.php">
            <input type="hidden" name="action" value="tambah">
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label>Nama Perusahaan <span class="required">*</span></label>
                        <input type="text" name="nama_perusahaan" class="form-control" required placeholder="PT. Contoh Maju">
                    </div>
                    <div class="form-group">
                        <label>Sektor / Bidang <span class="required">*</span></label>
                        <input type="text" name="sektor_bidang" class="form-control" required placeholder="Teknologi Informasi">
                    </div>
                </div>
                <div class="form-group">
                    <label>Jurusan <span class="required">*</span></label>
                    <textarea name="jurusan" class="form-control" required placeholder="Nama Jurusan"></textarea>
                </div>
                <div class="form-group">
                    <label>Alamat <span class="required">*</span></label>
                    <textarea name="alamat" class="form-control" required placeholder="Jl. Contoh No. 1, Kota..."></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Penanggung Jawab <span class="required">*</span></label>
                        <input type="text" name="penanggung_jawab" class="form-control" required placeholder="Nama PIC">
                    </div>
                    <div class="form-group">
                        <label>No. Telepon <span class="required">*</span></label>
                        <input type="text" name="no_telepon" class="form-control" required placeholder="0812-xxxx-xxxx">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Status MoU <span class="required">*</span></label>
                        <select name="status_mou" class="form-control" required>
                            <option value="Proses">Proses</option>
                            <option value="Aktif">Aktif</option>
                            <option value="Kadaluarsa">Kadaluarsa</option>
                        </select>
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

<!-- ══ MODAL EDIT ══ -->
<div class="modal-overlay" id="modalEdit">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title"><i class="fa-solid fa-pen-to-square"></i> Edit Perusahaan</span>
            <button class="modal-close" onclick="closeModal('modalEdit')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" action="backend/perusahaan_handler.php">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="edit_id">
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label>Nama Perusahaan <span class="required">*</span></label>
                        <input type="text" name="nama_perusahaan" id="edit_nama_perusahaan" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Sektor / Bidang <span class="required">*</span></label>
                        <input type="text" name="sektor_bidang" id="edit_sektor_bidang" class="form-control" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Jurusan <span class="required">*</span></label>
                    <textarea name="jurusan" id="edit_jurusan" class="form-control" required></textarea>
                </div>
                <div class="form-group">
                    <label>Alamat <span class="required">*</span></label>
                    <textarea name="alamat" id="edit_alamat" class="form-control" required></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Penanggung Jawab <span class="required">*</span></label>
                        <input type="text" name="penanggung_jawab" id="edit_penanggung_jawab" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>No. Telepon <span class="required">*</span></label>
                        <input type="text" name="no_telepon" id="edit_no_telepon" class="form-control" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Status MoU <span class="required">*</span></label>
                        <select name="status_mou" id="edit_status_mou" class="form-control" required>
                            <option value="Proses">Proses</option>
                            <option value="Aktif">Aktif</option>
                            <option value="Kadaluarsa">Kadaluarsa</option>
                        </select>
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

<!-- ══ FORM HAPUS (hidden) ══ -->
<form method="POST" action="backend/perusahaan_handler.php" id="formHapus">
    <input type="hidden" name="action" value="hapus">
    <input type="hidden" name="id" id="hapus_id">
</form>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/admin.js"></script>
<script>
function editPerusahaan(data) {
    document.getElementById('edit_id').value            = data.id;
    document.getElementById('edit_nama_perusahaan').value = data.nama_perusahaan;
    document.getElementById('edit_sektor_bidang').value = data.sektor_bidang;
    document.getElementById('edit_jurusan').value        = data.jurusan;
    document.getElementById('edit_alamat').value        = data.alamat;
    document.getElementById('edit_penanggung_jawab').value = data.penanggung_jawab;
    document.getElementById('edit_no_telepon').value    = data.no_telepon;
    document.getElementById('edit_status_mou').value    = data.status_mou;
    openModal('modalEdit');
}

function hapusPerusahaan(id, nama_perusahaan) {
    Swal.fire({
        title: 'Hapus Perusahaan?',
        html: `Data <strong>${nama_perusahaan}</strong> akan dihapus beserta semua lowongan terkait!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {

        if (result.isConfirmed) {
            document.getElementById('hapus_id').value = id;
            document.getElementById('formHapus').submit();
        }

    });
}
</script>
</body>
</html>

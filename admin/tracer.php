<?php
session_start();
require_once '../backend/connection.php';
require_once 'includes/auth.php';

$search = trim($_GET['search'] ?? '');
$page   = max(1, (int)($_GET['page'] ?? 1));
$limit  = 10;
$offset = ($page - 1) * $limit;

$where = $search ? "WHERE s.nama LIKE ? OR ts.status_alumni LIKE ? OR ts.nama_instansi LIKE ?" : "";

$stmt_count = mysqli_prepare($koneksi,
    "SELECT COUNT(*) as n FROM tracer_study ts JOIN siswa s ON s.id = ts.id_siswa $where"
);
if ($search) { $q="%$search%"; mysqli_stmt_bind_param($stmt_count,'sss',$q,$q,$q); }
mysqli_stmt_execute($stmt_count);
$total = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_count))['n'];
$total_pages = ceil($total / $limit);

$stmt_data = mysqli_prepare($koneksi,
    "SELECT ts.*, s.nama AS nama_siswa, s.jurusan
     FROM tracer_study ts JOIN siswa s ON s.id = ts.id_siswa
     $where ORDER BY ts.created_at DESC LIMIT ? OFFSET ?"
);
if ($search) { $q="%$search%"; mysqli_stmt_bind_param($stmt_data,'sssii',$q,$q,$q,$limit,$offset); }
else { mysqli_stmt_bind_param($stmt_data,'ii',$limit,$offset); }
mysqli_stmt_execute($stmt_data);
$data = mysqli_fetch_all(mysqli_stmt_get_result($stmt_data), MYSQLI_ASSOC);

// Siswa yang belum ada tracer study
$siswa_tersedia = mysqli_fetch_all(mysqli_query($koneksi,
    "SELECT id, nama, kelas FROM siswa WHERE id NOT IN (SELECT id_siswa FROM tracer_study) ORDER BY nama"
), MYSQLI_ASSOC);
// Semua siswa untuk edit dropdown
$siswa_all = mysqli_fetch_all(mysqli_query($koneksi, "SELECT id, nama, kelas FROM siswa ORDER BY nama"), MYSQLI_ASSOC);

$page_title = "Tracer Study";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracer Study — Admin Humas SMK</title>
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
                <h2><i class="fa-solid fa-route" style="color:#ec4899;margin-right:8px;"></i>Tracer Study</h2>
                <p>Kelola data penelusuran alumni setelah lulus</p>
            </div>
            <button class="btn btn-primary" onclick="openModal('modalTambah')">
                <i class="fa-solid fa-plus"></i> Tambah Data
            </button>
        </div>

        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-list"></i> Data Tracer Study (<?= $total ?>)</span>
                <form method="GET" style="display:flex;gap:.5rem;align-items:center;">
                    <div class="search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nama / instansi...">
                    </div>
                    <button type="submit" class="btn btn-outline btn-sm"><i class="fa-solid fa-search"></i></button>
                    <?php if ($search): ?><a href="tracer.php" class="btn btn-outline btn-sm"><i class="fa-solid fa-xmark"></i></a><?php endif; ?>
                </form>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Alumni</th>
                            <th>Jurusan</th>
                            <th>Thn Lulus</th>
                            <th>Status</th>
                            <th>Instansi</th>
                            <th>Pendapatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($data)): ?>
                        <tr><td colspan="8"><div class="table-empty"><i class="fa-solid fa-route"></i><p>Belum ada data tracer study.</p></div></td></tr>
                    <?php else: foreach ($data as $i => $row):
                        $badge = match($row['status_alumni']) {
                            'Bekerja'       => 'badge-green',
                            'Kuliah'        => 'badge-blue',
                            'Wirausaha'     => 'badge-gold',
                            'Mencari Kerja' => 'badge-red',
                            default         => 'badge-gray',
                        };
                    ?>
                        <tr>
                            <td class="td-no"><?= $offset+$i+1 ?></td>
                            <td style="font-weight:600;"><?= htmlspecialchars($row['nama_siswa']) ?></td>
                            <td><?= htmlspecialchars($row['jurusan']) ?></td>
                            <td style="text-align:center;"><?= $row['tahun_lulus'] ?></td>
                            <td><span class="badge <?= $badge ?>"><?= $row['status_alumni'] ?></span></td>
                            <td><?= htmlspecialchars($row['nama_instansi'] ?? '-') ?></td>
                            <td><?= $row['pendapatan_bulanan'] ? 'Rp ' . number_format($row['pendapatan_bulanan'],0,',','.') : '-' ?></td>
                            <td>
                                <div class="action-btns">
                                    <button class="btn btn-warning btn-sm btn-icon" onclick='editTracer(<?= json_encode($row) ?>)' title="Edit"><i class="fa-solid fa-pen"></i></button>
                                    <button class="btn btn-danger btn-sm btn-icon" onclick="hapusTracer(<?= $row['id'] ?>, '<?= htmlspecialchars($row['nama_siswa'], ENT_QUOTES) ?>')" title="Hapus"><i class="fa-solid fa-trash"></i></button>
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
            <span class="modal-title"><i class="fa-solid fa-route"></i> Tambah Tracer Study</span>
            <button class="modal-close" onclick="closeModal('modalTambah')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" action="backend/tracer_handler.php">
            <input type="hidden" name="action" value="tambah">
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label>Siswa Alumni <span class="required">*</span></label>
                        <select name="id_siswa" class="form-control" required>
                            <option value="">-- Pilih Siswa --</option>
                            <?php foreach ($siswa_tersedia as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nama']) ?> (<?= htmlspecialchars($s['kelas']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tahun Lulus <span class="required">*</span></label>
                        <input type="number" name="tahun_lulus" class="form-control" required min="2000" max="<?= date('Y') ?>" placeholder="<?= date('Y') ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Status Alumni <span class="required">*</span></label>
                        <select name="status_alumni" class="form-control" required>
                            <option value="Bekerja">Bekerja</option>
                            <option value="Kuliah">Kuliah</option>
                            <option value="Wirausaha">Wirausaha</option>
                            <option value="Mencari Kerja">Mencari Kerja</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nama Instansi / Kampus</label>
                        <input type="text" name="nama_instansi" class="form-control" placeholder="Opsional">
                    </div>
                </div>
                <div class="form-group">
                    <label>Pendapatan Bulanan (Rp)</label>
                    <input type="number" name="pendapatan_bulanan" class="form-control" placeholder="Opsional, isi angka saja">
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
            <span class="modal-title"><i class="fa-solid fa-pen-to-square"></i> Edit Tracer Study</span>
            <button class="modal-close" onclick="closeModal('modalEdit')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" action="backend/tracer_handler.php">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="e_id">
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label>Siswa Alumni <span class="required">*</span></label>
                        <select name="id_siswa" id="e_id_siswa" class="form-control" required>
                            <?php foreach ($siswa_all as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nama']) ?> (<?= htmlspecialchars($s['kelas']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tahun Lulus <span class="required">*</span></label>
                        <input type="number" name="tahun_lulus" id="e_tahun_lulus" class="form-control" required min="2000" max="<?= date('Y') ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Status Alumni <span class="required">*</span></label>
                        <select name="status_alumni" id="e_status_alumni" class="form-control" required>
                            <option value="Bekerja">Bekerja</option>
                            <option value="Kuliah">Kuliah</option>
                            <option value="Wirausaha">Wirausaha</option>
                            <option value="Mencari Kerja">Mencari Kerja</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nama Instansi / Kampus</label>
                        <input type="text" name="nama_instansi" id="e_nama_instansi" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label>Pendapatan Bulanan (Rp)</label>
                    <input type="number" name="pendapatan_bulanan" id="e_pendapatan_bulanan" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalEdit')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Perbarui</button>
            </div>
        </form>
    </div>
</div>

<form method="POST" action="backend/tracer_handler.php" id="formHapus">
    <input type="hidden" name="action" value="hapus">
    <input type="hidden" name="id" id="hapus_id">
</form>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/admin.js"></script>
<script>
function editTracer(d) {
    document.getElementById('e_id').value                = d.id;
    document.getElementById('e_id_siswa').value          = d.id_siswa;
    document.getElementById('e_tahun_lulus').value       = d.tahun_lulus;
    document.getElementById('e_status_alumni').value     = d.status_alumni;
    document.getElementById('e_nama_instansi').value     = d.nama_instansi || '';
    document.getElementById('e_pendapatan_bulanan').value = d.pendapatan_bulanan || '';
    openModal('modalEdit');
}
function hapusTracer(id, nama) {
    Swal.fire({
        title: 'Hapus Data Tracer?',
        html: `Data tracer study <strong>${nama}</strong> akan dihapus!`,
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

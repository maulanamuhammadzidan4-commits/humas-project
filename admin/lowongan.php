<?php
session_start();

require_once '../backend/connection.php';
require_once 'includes/auth.php';

/*--PENCARIAN & PAGINATION--*/
$search = trim($_GET['search'] ?? '');
$page   = max(1, (int)($_GET['page'] ?? 1));

$limit  = 10;
$offset = ($page - 1) * $limit;

/*--WHERE--*/
$where = '';
if ($search !== '') {
    $where = "
        WHERE lk.judul_posisi LIKE ?
        OR p.nama_perusahaan LIKE ?
    ";
}

/*--HITUNG TOTAL DATA--*/
$count_sql = "SELECT COUNT(*) AS n
    FROM lowongan_kerja AS lk
    INNER JOIN perusahaan AS p
    ON p.id = lk.perusahaan_id
    $where";

$stmt_count = mysqli_prepare($koneksi, $count_sql);
if (!$stmt_count) {
    die("Gagal menyiapkan query count: " . mysqli_error($koneksi));
}
if ($search !== '') {
    $keyword = "%{$search}%";
    mysqli_stmt_bind_param(
        $stmt_count,
        "ss",
        $keyword,
        $keyword
    );
}

mysqli_stmt_execute($stmt_count);
$result_count = mysqli_stmt_get_result($stmt_count);
$row_count = mysqli_fetch_assoc($result_count);
$total = (int)($row_count['n'] ?? 0);
$total_pages = max(
    1,
    (int)ceil($total / $limit)
);
mysqli_stmt_close($stmt_count);

/*--DATA LOWONGAN--*/
$data_sql = "
    SELECT
        lk.id,
        lk.perusahaan_id,
        lk.judul_posisi,
        lk.deskripsi_pekerjaan,
        lk.kuota,
        lk.batas_pendaftaran,
        lk.status_loker,
        lk.created_at,
        lk.updated_at,

        p.nama_perusahaan
    FROM lowongan_kerja AS lk
    INNER JOIN perusahaan AS p
        ON p.id = lk.perusahaan_id
    $where
    ORDER BY lk.created_at DESC
    LIMIT ? OFFSET ?
";

$stmt_data = mysqli_prepare($koneksi, $data_sql);
if (!$stmt_data) {
    die("Gagal menyiapkan query data: " . mysqli_error($koneksi));
}
if ($search !== '') {
    $keyword = "%{$search}%";
    mysqli_stmt_bind_param(
        $stmt_data,
        "ssii",
        $keyword,
        $keyword,
        $limit,
        $offset
    );

} else {
    mysqli_stmt_bind_param(
        $stmt_data,
        "ii",
        $limit,
        $offset
    );
}
mysqli_stmt_execute($stmt_data);
$result_data = mysqli_stmt_get_result($stmt_data);
$data = mysqli_fetch_all(
    $result_data,
    MYSQLI_ASSOC
);
mysqli_stmt_close($stmt_data);

/*--DATA PERUSAHAAN UNTUK SELECT--*/
$perusahaan_sql = "
    SELECT
        id,
        nama_perusahaan
    FROM perusahaan
    ORDER BY nama_perusahaan ASC
";

$result_perusahaan = mysqli_query(
    $koneksi,
    $perusahaan_sql
);
if (!$result_perusahaan) {
    die(
        "Gagal mengambil data perusahaan: "
        . mysqli_error($koneksi)
    );
}

$perusahaan_list = mysqli_fetch_all(
    $result_perusahaan,
    MYSQLI_ASSOC
);

$page_title = "Lowongan Kerja";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Lowongan Kerja — Admin Humas SMK</title>
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

        <!-- FLASH MESSAGE -->
        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-<?= htmlspecialchars($_GET['type'] ?? 'success') ?> flash-alert">
                <i class="fa-solid fa-circle-check"></i>
                <?= htmlspecialchars(urldecode($_GET['msg'])) ?>
            </div>
        <?php endif; ?>

        <!-- HEADER -->
        <div class="page-header">
            <div class="page-header-left">
                <h2>
                    <i class="fa-solid fa-briefcase" style="color:var(--purple);margin-right:8px;"></i>
                    Lowongan Kerja
                </h2>
                <p>Kelola data lowongan kerja dari perusahaan mitra</p>
            </div>

            <button class="btn btn-primary" onclick="openModal('modalTambah')">
                <i class="fa-solid fa-plus"></i>
                Tambah Lowongan
            </button>
        </div>

        <!-- CARD -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">
                    <i class="fa-solid fa-list"></i>
                    Daftar Lowongan
                    (<?= $total ?>)
                </span>

                <!-- SEARCH -->
                <form method="GET" style="display:flex;gap:.5rem;align-items:center;">
                    <div class="search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>
                            "placeholder="Cari posisi / perusahaan...">
                    </div>

                    <button type="submit" class="btn btn-outline btn-sm">
                        <i class="fa-solid fa-search"></i>
                    </button>

                    <?php if ($search): ?>
                        <a href="lowongan.php" class="btn btn-outline btn-sm">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- TABLE -->
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Posisi</th>
                            <th>Perusahaan</th>
                            <th>Kuota</th>
                            <th>Batas Daftar</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($data)): ?>
                        <tr>
                            <td colspan="7">
                                <div class="table-empty">
                                    <i class="fa-solid fa-briefcase"></i>
                                    <p>Belum ada data lowongan.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data as $i => $row): ?>
                            <?php
                            $status = $row['status_loker'] ?? '';
                            if ($status === 'Buka') {
                                $badge = 'badge-green';
                            } elseif ($status === 'Tutup') {
                                $badge = 'badge-red';
                            } else {
                                $badge = 'badge-gold';
                            }
                            $expired = false;
                            if (
                                !empty($row['batas_pendaftaran'])
                            ) {
                                $expired = strtotime($row['batas_pendaftaran']) < time();
                            }
                            ?>
                            <tr>
                                <td class="td-no">
                                    <?= $offset + $i + 1 ?>
                                </td>
                                <td style="font-weight:600;">
                                    <?= htmlspecialchars($row['judul_posisi']) ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($row['nama_perusahaan']) ?>
                                </td>
                                <td style="text-align:center;">
                                    <?= (int)$row['kuota'] ?>
                                </td>
                                <td style="<?= $expired ? 'color:var(--red);font-weight:700;' : '' ?>">
                                    <?php if (
                                        !empty($row['batas_pendaftaran'])): ?>
                                        <?= date('d M Y',strtotime($row['batas_pendaftaran'])) ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                    <?php if ($expired): ?>
                                        <span class="badge badge-red" style="margin-left:4px;font-size:.65rem;">
                                            Expired
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge <?= $badge ?>">
                                        <?= htmlspecialchars(
                                            $status
                                        ) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-btns">

                                        <!-- EDIT -->
                                        <button type="button" class="btn btn-warning btn-sm btn-icon" onclick='editLoker(<?= json_encode(
                                                $row,
                                                JSON_HEX_TAG |
                                                JSON_HEX_APOS |
                                                JSON_HEX_AMP |
                                                JSON_HEX_QUOT
                                            ) ?>)'title="Edit">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>

                                        <!-- HAPUS -->
                                        <button type="button" class="btn btn-danger btn-sm btn-icon" onclick='hapusLoker(
                                                <?= (int)$row['id'] ?>,
                                                <?= json_encode(
                                                    $row['judul_posisi'],
                                                    JSON_HEX_TAG |
                                                    JSON_HEX_APOS |
                                                    JSON_HEX_AMP |
                                                    JSON_HEX_QUOT
                                                ) ?> )'title="Hapus">
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

            <!-- PAGINATION -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <span class="pagination-info">Menampilkan
                        <?= $total > 0 ? $offset + 1 : 0 ?>
                        –
                        <?= min($offset + $limit, $total) ?>dari <?= $total ?>
                    </span>
                    <div class="pagination-btns">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>"class="page-btn">
                                <i class="fa-solid fa-chevron-left"></i>
                            </a>
                        <?php endif; ?>
                        <?php
                        for (
                            $p = max(1, $page - 2); $p <= min($total_pages, $page + 2); $p++):
                        ?>
                            <a href="?page=<?= $p ?>&search=<?= urlencode($search) ?>" class="page-btn <?= $p == $page ? 'active' : '' ?>"><?= $p ?></a>
                        <?php endfor; ?>
                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>"class="page-btn">
                                <i class="fa-solid fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<!--MODAL TAMBAH -->
<div class="modal-overlay" id="modalTambah">
    <div class="modal modal-lg">
        <div class="modal-header">
            <span class="modal-title">
                <i class="fa-solid fa-briefcase"></i>
                Tambah Lowongan Kerja
            </span>

            <button type="button" class="modal-close" onclick="closeModal('modalTambah')">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form method="POST" action="backend/lowongan_handler.php">
            <input type="hidden" name="action" value="tambah">
            <div class="modal-body">

                <!-- PERUSAHAAN + POSISI -->
                <div class="form-row">
                    <div class="form-group">
                        <label>
                            Perusahaan<span class="required">*</span>
                        </label>

                        <select name="perusahaan_id" class="form-control" required>
                            <option value="">
                                -- Pilih Perusahaan --
                            </option>

                            <?php foreach (
                                $perusahaan_list as $p): ?>
                                <option value="<?= (int)$p['id'] ?>">
                                    <?= htmlspecialchars(
                                        $p['nama_perusahaan']
                                    ) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>
                            Posisi<span class="required">*</span>
                        </label>
                        <input type="text" name="judul_posisi" class="form-control" required maxlength="150" placeholder="Teknisi Komputer">
                    </div>
                </div>

                <!-- DESKRIPSI -->
                <div class="form-group">
                    <label>
                        Deskripsi<span class="required">*</span>
                    </label>
                    <textarea name="deskripsi_pekerjaan" class="form-control" required rows="5" placeholder="Deskripsi pekerjaan dan persyaratan..."></textarea>
                </div>

                <!-- KUOTA + BATAS -->
                <div class="form-row">
                    <div class="form-group">
                        <label>
                            Kuota<span class="required">*</span>
                        </label>
                        <input type="number" name="kuota" class="form-control" required min="1" placeholder="5">
                    </div>
                    <div class="form-group">
                        <label>
                            Batas Pendaftaran<span class="required">*</span>
                        </label>
                        <input type="date" name="batas_pendaftaran" class="form-control" required>
                    </div>
                </div>

                <!-- STATUS -->
                <div class="form-group">
                    <label>
                        Status Lowongan
                    </label>
                    <select name="status_loker" class="form-control">
                        <option value="Buka">
                            Buka
                        </option>
                        <option value="Tutup">
                            Tutup
                        </option>
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalTambah')">
                    Batal
                </button>

                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-save"></i>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!--MODAL EDIT -->
<divclass="modal-overlay"id="modalEdit">
    <div class="modal modal-lg">
        <div class="modal-header">
            <span class="modal-title">
                <i class="fa-solid fa-pen-to-square"></i>
                Edit Lowongan Kerja
            </span>

            <button type="button" class="modal-close" onclick="closeModal('modalEdit')">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form method="POST" action="backend/lowongan_handler.php">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="e_id">
            <div class="modal-body">

                <!-- PERUSAHAAN + POSISI -->
                <div class="form-row">
                    <div class="form-group">
                        <label>
                            Perusahaan<span class="required">*</span>
                        </label>

                        <select name="perusahaan_id" id="e_perusahaan_id" class="form-control" required>
                            <?php foreach ($perusahaan_list as $p): ?>
                                <option value="<?= (int)$p['id'] ?>">
                                    <?= htmlspecialchars(
                                        $p['nama_perusahaan']
                                    ) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>
                            Posisi<span class="required">*</span>
                        </label>
                        <input type="text" name="judul_posisi" id="e_judul_posisi" class="form-control" required maxlength="150">
                    </div>
                </div>

                <!-- DESKRIPSI -->
                <div class="form-group">
                    <label>
                        Deskripsi<span class="required">*</span>
                    </label>
                    <textarea name="deskripsi_pekerjaan" id="e_deskripsi_pekerjaan" class="form-control" required rows="5"></textarea>
                </div>

                <!-- KUOTA + BATAS -->
                <div class="form-row">
                    <div class="form-group">
                        <label>
                            Kuota<span class="required">*</span>
                        </label>
                        <input type="number" name="kuota" id="e_kuota" class="form-control" required min="1">
                    </div>
                    <div class="form-group">
                        <label>
                            Batas Pendaftaran<span class="required">*</span>
                        </label>
                        <input type="date" name="batas_pendaftaran" id="e_batas_pendaftaran" class="form-control" required>
                    </div>
                </div>

                <!-- STATUS -->
                <div class="form-group">
                    <label>Status Lowongan</label>
                    <select name="status_loker" id="e_status_loker" class="form-control">
                        <option value="Buka">
                            Buka
                        </option>
                        <option value="Tutup">
                            Tutup
                        </option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalEdit')">
                    Batal
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-save"></i>
                    Perbarui
                </button>
            </div>
        </form>
    </div>
</div>

<!--FORM HAPUS -->
<form method="POST" action="backend/lowongan_handler.php" id="formHapus">
    <input type="hidden" name="action" value="hapus">
    <input type="hidden" name="id" id="hapus_id">
</form>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/admin.js"></script>
</body>
</html>
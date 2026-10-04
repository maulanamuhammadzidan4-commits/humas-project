<?php
session_start();
require_once '../backend/connection.php';
require_once '../backend/repositories/bootstrap.php';
require_once 'includes/auth.php';
/* =========================
    SEARCH & PAGINATION
========================= */
$search = is_string($_GET['search'] ?? null) ? trim($_GET['search']) : '';
$requestedPage = filter_var($_GET['page'] ?? '1', FILTER_VALIDATE_INT);
$page = $requestedPage !== false && $requestedPage > 0 ? $requestedPage : 1;

$limit = 10;
$total = SiswaRepository::countWithSearch($koneksi, $search);
$total_pages = max(1, (int)ceil($total / $limit));
$page = min($page, $total_pages);
$offset = ($page - 1) * $limit;
$data = SiswaRepository::getPaginated($koneksi, $search, $limit, $offset);
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
        <!-- =========================
             ALERT
        ========================== -->
        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-<?= htmlspecialchars($_GET['type'] ?? 'success') ?> flash-alert">
                <i class="fa-solid fa-circle-check"></i>
                <?= htmlspecialchars(urldecode($_GET['msg'])) ?>
            </div>
        <?php endif; ?>

        <!-- =========================
             HEADER
        ========================== -->
        <div class="page-header">
            <div class="page-header-left">
                <h2>
                    <i class="fa-solid fa-user-graduate" style="color:#0ea5e9;margin-right:8px;"></i>
                    Data Siswa
                </h2>
                <p>
                    Kelola data siswa dan status alumni
                </p>
            </div>

            <button
                class="btn btn-primary"
                onclick="openModal('modalTambah')">
                <i class="fa-solid fa-user-plus"></i>
                Tambah Siswa
            </button>
        </div>

        <!-- =========================
             CARD
        ========================== -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">
                    <i class="fa-solid fa-list"></i>
                    Daftar Siswa (<?= $total ?>)
                </span>

                <!-- SEARCH -->
                <form method="GET" style="display:flex;gap:.5rem;align-items:center;">
                    <div class="search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="search" maxlength="255" value="<?= htmlspecialchars($search) ?>" placeholder="Cari NISN, nama, kelas...">
                    </div>

                    <button type="submit" class="btn btn-outline btn-sm">
                        <i class="fa-solid fa-search"></i>
                    </button>

                    <?php if ($search): ?>
                        <a href="siswa.php" class="btn btn-outline btn-sm">
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
                        <tr>
                            <td colspan="7">
                                <div class="table-empty">
                                    <i class="fa-solid fa-user-slash"></i>
                                    <p>
                                        Belum ada data siswa.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data as $i => $row): ?>
                            <tr>
                                <!-- NO -->
                                <td class="td-no">
                                    <?= $offset + $i + 1 ?>
                                </td>

                                <!-- NISN -->
                                <td style="font-family:monospace;font-weight:600;letter-spacing:1px;">
                                    <?= htmlspecialchars($row['nisn']) ?>
                                </td>

                                <!-- NAMA -->
                                <td style="font-weight:600;">
                                    <?= htmlspecialchars($row['nama_siswa']) ?>
                                </td>

                                <!-- KELAS -->
                                <td>
                                    <?= htmlspecialchars($row['kelas']) ?>
                                </td>

                                <!-- JURUSAN -->
                                <td>
                                    <?= htmlspecialchars($row['jurusan']) ?>
                                </td>

                                <!-- STATUS -->
                                <td>
                                    <span class="badge <?= $row['status_alumni'] ? 'badge-blue' : 'badge-green' ?>">
                                        <?= $row['status_alumni'] ? 'Alumni' : 'Aktif' ?>
                                    </span>
                                </td>

                                <!-- AKSI -->
                                <td>
                                    <div class="action-btns">
                                        <!-- EDIT -->
                                        <button class="btn btn-warning btn-sm btn-icon" onclick='editSiswa(<?= json_encode($row, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' title="Edit">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>

                                        <!-- HAPUS -->
                                        <button class="btn btn-danger btn-sm btn-icon" onclick='hapusSiswa( <?= (int)$row["id"] ?>, <?= json_encode($row["nama_siswa"]) ?> )' title="Hapus">
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
                    <span class="pagination-info">
                        Menampilkan
                        <?= $total > 0 ? $offset + 1 : 0 ?>
                        –
                        <?= min($offset + $limit, $total) ?>
                        dari
                        <?= $total ?>
                    </span>

                    <div class="pagination-btns">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>" class="page-btn">
                                <i class="fa-solid fa-chevron-left"></i>
                            </a>
                        <?php endif; ?>

                        <?php for ($p = max(1, $page - 2); $p <= min($total_pages, $page + 2); $p++): ?>
                            <a href="?page=<?= $p ?>&search=<?= urlencode($search) ?>" class="page-btn <?= $p == $page ? 'active' : '' ?>">
                                <?= $p ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>" class="page-btn">
                                <i class="fa-solid fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<!-- MODAL TAMBAH -->
<div class="modal-overlay" id="modalTambah">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">
                <i class="fa-solid fa-user-plus"></i>
                Tambah Siswa
            </span>

            <button
                class="modal-close"
                onclick="closeModal('modalTambah')">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form method="POST" action="backend/siswa_handler.php">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generateCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="action" value="tambah">
            <div class="modal-body">
                <!-- NISN + NAMA -->
                <div class="form-row">
                    <div class="form-group">
                        <label>NISN <span class="required">*</span></label>
                        <input type="text" name="nisn" class="form-control" required maxlength="10" pattern="[0-9]{10}" inputmode="numeric" placeholder="10 digit NISN">
                    </div>

                    <div class="form-group">
                        <label>Nama Lengkap <span class="required">*</span></label>
                        <input type="text" name="nama_siswa" class="form-control" required maxlength="150" placeholder="Nama siswa">
                    </div>
                </div>

                <!-- KELAS + JURUSAN -->
                <div class="form-row">
                    <div class="form-group">
                        <label>Kelas <span class="required">*</span></label>
                        <select name="kelas" class="form-control" required>
                            <option value="">Pilih kelas</option>
                            <?php foreach (SiswaRepository::getClassOptions() as $kelas): ?>
                                <option value="<?= htmlspecialchars($kelas, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($kelas, ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Jurusan <span class="required">*</span></label>
                        <select name="jurusan" class="form-control" required>
                            <option value="">Pilih jurusan</option>
                            <?php foreach (SiswaRepository::getMajorOptions() as $jurusan): ?>
                                <option value="<?= htmlspecialchars($jurusan, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($jurusan, ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- STATUS -->
                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                        <input type="checkbox" name="status_alumni" value="1" style="width:16px;height:16px;accent-color:var(--blue);">
                        Tandai sebagai Alumni
                    </label>
                </div>
            </div>

            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-outline"
                    onclick="closeModal('modalTambah')">
                    Batal
                </button>

                <button
                    type="submit"
                    class="btn btn-primary">
                    <i class="fa-solid fa-save"></i>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT -->
<div class="modal-overlay" id="modalEdit">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">
                <i class="fa-solid fa-user-pen"></i>
                Edit Data Siswa
            </span>

            <button
                class="modal-close"
                onclick="closeModal('modalEdit')">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form method="POST" action="backend/siswa_handler.php">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generateCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="e_id">
            <div class="modal-body">
                <!-- NISN + NAMA -->
                <div class="form-row">
                    <div class="form-group">
                        <label>NISN <span class="required">*</span></label>
                        <input type="text" name="nisn" id="e_nisn" class="form-control" required maxlength="10" pattern="[0-9]{10}" inputmode="numeric">
                    </div>

                    <div class="form-group">
                        <label>Nama Lengkap <span class="required">*</span></label>
                        <input type="text" name="nama_siswa" id="e_nama_siswa" class="form-control" required maxlength="150">
                    </div>
                </div>

                <!-- KELAS + JURUSAN -->
                <div class="form-row">
                    <div class="form-group">
                        <label>Kelas <span class="required">*</span></label>
                        <select name="kelas" id="e_kelas" class="form-control" required>
                            <option value="">Pilih kelas</option>
                            <?php foreach (SiswaRepository::getClassOptions() as $kelas): ?>
                                <option value="<?= htmlspecialchars($kelas, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($kelas, ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Jurusan <span class="required">*</span></label>
                        <select name="jurusan" id="e_jurusan" class="form-control" required>
                            <option value="">Pilih jurusan</option>
                            <?php foreach (SiswaRepository::getMajorOptions() as $jurusan): ?>
                                <option value="<?= htmlspecialchars($jurusan, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($jurusan, ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- STATUS -->
                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                        <input type="checkbox" name="status_alumni" id="e_status_alumni" value="1" style="width:16px;height:16px;accent-color:var(--blue);">
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

<!-- FORM HAPUS -->
<form method="POST" action="backend/siswa_handler.php" id="formHapus">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generateCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="action" value="hapus">
    <input type="hidden" name="id" id="hapus_id">
</form>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/admin.js"></script>
</body>
</html>
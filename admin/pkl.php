
<?php
session_start();

require_once '../backend/connection.php';
require_once '../backend/repositories/bootstrap.php';
require_once 'includes/auth.php';

/* =========================================
   HELPER
========================================= */

function e($value): string
{
    return htmlspecialchars(
        (string) ($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

/* =========================================
   SEARCH & PAGINATION
========================================= */

$search = is_string($_GET['search'] ?? null)
    ? trim($_GET['search'])
    : '';

$requestedPage = filter_var(
    $_GET['page'] ?? '1',
    FILTER_VALIDATE_INT
);

$page = ($requestedPage !== false && $requestedPage > 0)
    ? $requestedPage
    : 1;

$limit = 10;

$total = PklRepository::countWithSearch($koneksi, $search);

$total_pages = max(1, (int) ceil($total / $limit));

$page = min($page, $total_pages);
$offset = ($page - 1) * $limit;

$data = PklRepository::getPaginated(
    $koneksi,
    $search,
    $limit,
    $offset
);

$siswa_list = SiswaRepository::getOptions($koneksi);
$perusahaan_list = PerusahaanRepository::getOptions($koneksi);

$page_title = 'Penempatan PKL';
$csrfToken = generateCsrfToken();

/* =========================================
   HASIL IMPOR
========================================= */

$hasilImport = null;

if (isset($_GET['import'])) {
    $hasilImport = $_SESSION['pkl_import_result'] ?? null;
    unset($_SESSION['pkl_import_result']);
}

/* =========================================
   NORMALISASI DETAIL IMPOR
========================================= */

$detailImport = [];

if (is_array($hasilImport)) {
    $detail = $hasilImport['detail'] ?? [];

    if (is_string($detail) && trim($detail) !== '') {
        $detailImport = [$detail];
    } elseif (is_array($detail)) {
        $detailImport = $detail;
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Penempatan PKL — Admin Humas SMK</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    >

    <link rel="stylesheet" href="assets/admin-style.css">

    <style>
        .page-header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .import-export-section {
            margin-bottom: 24px;
        }

        .import-export-section:last-child {
            margin-bottom: 0;
        }

        .import-export-section h4 {
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--text-primary, #172033);
        }

        .import-export-section p {
            font-size: 13px;
            line-height: 1.6;
            color: #777;
            margin-bottom: 14px;
        }

        .import-export-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .import-export-divider {
            border: 0;
            border-top: 1px solid #e8edf3;
            margin: 22px 0;
        }

        .import-file-info {
            display: block;
            margin-top: 8px;
            font-size: 12px;
            color: #777;
        }

        @media (max-width: 600px) {
            .page-header {
                align-items: flex-start;
                gap: 14px;
                flex-direction: column;
            }

            .page-header-actions {
                width: 100%;
            }

            .page-header-actions .btn {
                flex: 1;
                justify-content: center;
            }

            .import-export-actions .btn {
                flex: 1;
                justify-content: center;
            }
        }
    </style>
</head>

<body>

<?php include 'includes/sidebar.php'; ?>

<div class="admin-main">

    <?php include 'includes/header.php'; ?>

    <main class="admin-content">

        <!-- =====================================
             PESAN NOTIFIKASI
        ====================================== -->

        <?php if (isset($_GET['msg'])): ?>

            <div class="alert alert-<?= e($_GET['type'] ?? 'success') ?> flash-alert">

                <i class="fa-solid fa-circle-check"></i>

                <?= e($_GET['msg']) ?>

            </div>

        <?php endif; ?>


        <!-- =====================================
             HASIL IMPOR
        ====================================== -->

        <?php if (is_array($hasilImport)): ?>

            <div class="import-result-card" id="importResultCard">

                <div class="import-result-header">

                    <div class="import-result-title">

                        <div class="import-result-icon">
                            <i class="fa-solid fa-file-import"></i>
                        </div>

                        <div>
                            <h3>Hasil Impor Data PKL</h3>
                            <p>Ringkasan proses impor data penempatan PKL.</p>
                        </div>

                    </div>

                    <button
                        type="button"
                        class="import-close-btn"
                        onclick="document.getElementById('importResultCard').remove()"
                        aria-label="Tutup notifikasi"
                    >
                        <i class="fa-solid fa-xmark"></i>
                    </button>

                </div>


                <div class="import-result-stats">

                    <div class="import-stat success">

                        <div class="import-stat-icon">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>

                        <div>
                            <strong>
                                <?= (int)($hasilImport['berhasil'] ?? 0) ?>
                            </strong>
                            <span>Berhasil</span>
                        </div>

                    </div>


                    <div class="import-stat skipped">

                        <div class="import-stat-icon">
                            <i class="fa-solid fa-circle-pause"></i>
                        </div>

                        <div>
                            <strong>
                                <?= (int)($hasilImport['dilewati'] ?? 0) ?>
                            </strong>
                            <span>Dilewati</span>
                        </div>

                    </div>


                    <div class="import-stat failed">

                        <div class="import-stat-icon">
                            <i class="fa-solid fa-circle-exclamation"></i>
                        </div>

                        <div>
                            <strong>
                                <?= (int)($hasilImport['gagal'] ?? 0) ?>
                            </strong>
                            <span>Gagal</span>
                        </div>

                    </div>

                </div>


                <?php if (!empty($detailImport)): ?>

                    <details class="import-error-details">

                        <summary>

                            <span>
                                <i class="fa-solid fa-list-ul"></i>
                                Lihat rincian kesalahan
                            </span>

                            <span class="import-error-count">
                                <?= count($detailImport) ?> detail
                            </span>

                        </summary>

                        <div class="import-error-content">

                            <ul>

                                <?php foreach (array_slice($detailImport, 0, 20) as $pesan): ?>

                                    <li>
                                        <?= e((string)$pesan) ?>
                                    </li>

                                <?php endforeach; ?>

                            </ul>

                            <?php if (count($detailImport) > 20): ?>

                                <p class="import-more-error">

                                    Masih ada
                                    <?= count($detailImport) - 20 ?>
                                    kesalahan lainnya yang tidak ditampilkan.

                                </p>

                            <?php endif; ?>

                        </div>

                    </details>

                <?php endif; ?>

            </div>

        <?php elseif (isset($_GET['import'])): ?>

            <div class="alert alert-info flash-alert">

                <i class="fa-solid fa-triangle-exclamation"></i>

                <strong>Informasi hasil impor tidak ditemukan.</strong>

                Silakan periksa kembali proses impor atau log PHP.

            </div>

        <?php endif; ?>


        <!-- =====================================
             HEADER HALAMAN
        ====================================== -->

        <div class="page-header">

            <div class="page-header-left">

                <h2>

                    <i
                        class="fa-solid fa-map-location-dot"
                        style="color:var(--orange);margin-right:8px;"
                    ></i>

                    Penempatan PKL

                </h2>

                <p>
                    Kelola data penempatan Praktik Kerja Lapangan siswa.
                </p>

            </div>


            <div class="page-header-actions">

                <button
                    class="btn btn-primary"
                    type="button"
                    onclick="openModal('modalTambah')"
                >
                    <i class="fa-solid fa-plus"></i>
                    Tambah PKL
                </button>

                <button
                    class="btn btn-primary"
                    type="button"
                    onclick="openModal('modalImportExport')"
                >
                    <i class="fa-solid fa-file-import"></i>
                    Impor / Ekspor
                </button>

            </div>

        </div>


        <!-- =====================================
             CARD DATA PKL
        ====================================== -->

        <div class="card">

            <div class="card-header">

                <span class="card-title">

                    <i class="fa-solid fa-list"></i>

                    Data PKL (<?= (int)$total ?>)

                </span>


                <!-- SEARCH -->

                <form
                    method="GET"
                    style="display:flex;gap:.5rem;align-items:center;"
                >

                    <div class="search-box">

                        <i class="fa-solid fa-magnifying-glass"></i>

                        <input
                            type="text"
                            name="search"
                            maxlength="255"
                            value="<?= e($search) ?>"
                            placeholder="Cari nama, kelas, jurusan, perusahaan..."
                        >

                    </div>

                    <button
                        type="submit"
                        class="btn btn-outline btn-sm"
                    >
                        <i class="fa-solid fa-search"></i>
                    </button>

                    <?php if ($search !== ''): ?>

                        <a
                            href="pkl.php"
                            class="btn btn-outline btn-sm"
                        >
                            <i class="fa-solid fa-xmark"></i>
                        </a>

                    <?php endif; ?>

                </form>

            </div>


            <!-- =====================================
                 TABEL DATA PKL
            ====================================== -->

            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>
                            <th>No</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Jurusan</th>
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

                        <tr>

                            <td colspan="10">

                                <div class="table-empty">

                                    <i class="fa-solid fa-map"></i>

                                    <p>Belum ada data PKL.</p>

                                </div>

                            </td>

                        </tr>

                    <?php else: ?>

                        <?php foreach ($data as $i => $row): ?>

                            <?php

                            switch ($row['status_penempatan']) {

                                case 'Disetujui':
                                    $badge = 'badge-green';
                                    break;

                                case 'Selesai':
                                    $badge = 'badge-blue';
                                    break;

                                default:
                                    $badge = 'badge-gold';
                                    break;

                            }

                            ?>

                            <tr>

                                <!-- NOMOR -->

                                <td class="td-no">
                                    <?= $offset + $i + 1 ?>
                                </td>


                                <!-- NAMA SISWA -->

                                <td style="font-weight:600;">
                                    <?= e($row['nama_siswa'] ?? '-') ?>
                                </td>


                                <!-- KELAS -->

                                <td>
                                    <?= e($row['kelas'] ?? '-') ?>
                                </td>


                                <!-- JURUSAN -->

                                <td>
                                    <?= e($row['jurusan'] ?? '-') ?>
                                </td>


                                <!-- PERUSAHAAN -->

                                <td>
                                    <?= e($row['nama_perusahaan'] ?? '-') ?>
                                </td>


                                <!-- PEMBIMBING -->

                                <td>
                                    <?= e($row['pembimbing'] ?? '-') ?>
                                </td>


                                <!-- TANGGAL MULAI -->

                                <td>

                                    <?= !empty($row['tanggal_mulai'])
                                        ? date('d/m/Y', strtotime($row['tanggal_mulai']))
                                        : '-' ?>

                                </td>


                                <!-- TANGGAL SELESAI -->

                                <td>

                                    <?= !empty($row['tanggal_selesai'])
                                        ? date('d/m/Y', strtotime($row['tanggal_selesai']))
                                        : '-' ?>

                                </td>


                                <!-- STATUS -->

                                <td>

                                    <span class="badge <?= e($badge) ?>">

                                        <?= e($row['status_penempatan'] ?? '-') ?>

                                    </span>

                                </td>


                                <!-- AKSI -->

                                <td>

                                    <div class="action-btns">

                                        <!-- EDIT -->

                                        <button
                                            type="button"
                                            class="btn btn-warning btn-sm btn-icon"
                                            onclick='editPkl(<?= json_encode(
                                                $row,
                                                JSON_HEX_TAG |
                                                JSON_HEX_APOS |
                                                JSON_HEX_QUOT |
                                                JSON_HEX_AMP
                                            ) ?>)'
                                            title="Edit"
                                        >

                                            <i class="fa-solid fa-pen"></i>

                                        </button>


                                        <!-- HAPUS -->

                                        <button
                                            type="button"
                                            class="btn btn-danger btn-sm btn-icon"
                                            onclick='hapusPkl(
                                                <?= (int)$row['id'] ?>,
                                                <?= json_encode(
                                                    $row['nama_siswa'] ?? '',
                                                    JSON_HEX_TAG |
                                                    JSON_HEX_APOS |
                                                    JSON_HEX_QUOT |
                                                    JSON_HEX_AMP
                                                ) ?>
                                            )'
                                            title="Hapus"
                                        >

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


            <!-- =====================================
                 PAGINATION
            ====================================== -->

            <?php if ($total_pages > 1): ?>

                <div class="pagination">

                    <span class="pagination-info">

                        Menampilkan

                        <?= $total > 0 ? $offset + 1 : 0 ?>

                        –

                        <?= min($offset + $limit, $total) ?>

                        dari <?= (int)$total ?>

                    </span>


                    <div class="pagination-btns">

                        <?php if ($page > 1): ?>

                            <a
                                href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>"
                                class="page-btn"
                            >

                                <i class="fa-solid fa-chevron-left"></i>

                            </a>

                        <?php endif; ?>


                        <?php

                        for (
                            $p = max(1, $page - 2);
                            $p <= min($total_pages, $page + 2);
                            $p++
                        ):

                        ?>

                            <a
                                href="?page=<?= $p ?>&search=<?= urlencode($search) ?>"
                                class="page-btn <?= $p == $page ? 'active' : '' ?>"
                            >

                                <?= $p ?>

                            </a>

                        <?php endfor; ?>


                        <?php if ($page < $total_pages): ?>

                            <a
                                href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>"
                                class="page-btn"
                            >

                                <i class="fa-solid fa-chevron-right"></i>

                            </a>

                        <?php endif; ?>

                    </div>

                </div>

            <?php endif; ?>

        </div>

    </main>

</div>


<!-- =====================================
     MODAL IMPOR DAN EKSPOR
===================================== -->

<div class="modal-overlay" id="modalImportExport">

    <div class="modal modal-lg">

        <div class="modal-header">

            <span class="modal-title">

                <i class="fa-solid fa-file-import"></i>

                Impor dan Ekspor Data PKL

            </span>

            <button
                type="button"
                class="modal-close"
                onclick="closeModal('modalImportExport')"
            >
                <i class="fa-solid fa-xmark"></i>
            </button>

        </div>


        <div class="modal-body">

            <!-- FORM IMPOR -->

            <div class="import-export-section">

                <h4>

                    <i class="fa-solid fa-file-arrow-up"></i>

                    Impor Data Penempatan PKL

                </h4>

                <p>
                    Unggah data penempatan PKL menggunakan file Excel atau CSV.
                    Pastikan format kolom sesuai dengan template impor.
                </p>


                <form
                    method="POST"
                    action="backend/pkl_import.php"
                    enctype="multipart/form-data"
                    id="formImportPkl"
                >

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= e($csrfToken) ?>"
                    >

                    <div class="form-group">

                        <label for="file_import">

                            Pilih File Excel atau CSV

                            <span class="required">*</span>

                        </label>

                        <input
                            type="file"
                            name="file_import"
                            id="file_import"
                            class="form-control"
                            accept=".xlsx,.xls,.csv"
                            required
                        >

                        <small class="import-file-info">

                            Format yang didukung: XLSX, XLS, dan CSV.
                            Maksimal ukuran file 5 MB.

                        </small>

                    </div>


                    <button
                        type="submit"
                        class="btn btn-primary"
                        onclick="return confirm('Apakah kamu yakin ingin mengimpor data PKL dari file ini?');"
                    >

                        <i class="fa-solid fa-upload"></i>

                        Impor Data PKL

                    </button>

                </form>

            </div>


            <hr class="import-export-divider">


            <!-- BAGIAN EKSPOR -->

            <div class="import-export-section">

                <h4>

                    <i class="fa-solid fa-file-arrow-down"></i>

                    Ekspor Data Penempatan PKL

                </h4>

                <p>

                    Unduh seluruh data penempatan PKL yang sudah tersimpan
                    dalam database. Data alamat perusahaan juga disertakan.

                </p>


                <div class="import-export-actions">

                    <a
                        href="backend/pkl_export.php?format=xlsx"
                        class="btn btn-primary"
                    >

                        <i class="fa-solid fa-file-excel"></i>

                        Ekspor Excel

                    </a>


                    <a
                        href="backend/pkl_export.php?format=csv"
                        class="btn btn-outline"
                    >

                        <i class="fa-solid fa-file-csv"></i>

                        Ekspor CSV

                    </a>


                    <a
                        href="backend/pkl_export.php?format=pdf"
                        target="_blank"
                        class="btn btn-outline"
                    >

                        <i class="fa-solid fa-file-pdf"></i>

                        Ekspor PDF

                    </a>

                </div>

            </div>

        </div>


        <div class="modal-footer">

            <button
                type="button"
                class="btn btn-outline"
                onclick="closeModal('modalImportExport')"
            >

                Tutup

            </button>

        </div>

    </div>

</div>


<!-- =====================================
     MODAL TAMBAH
===================================== -->

<div class="modal-overlay" id="modalTambah">

    <div class="modal modal-lg">

        <div class="modal-header">

            <span class="modal-title">

                <i class="fa-solid fa-map-location-dot"></i>

                Tambah Penempatan PKL

            </span>

            <button
                type="button"
                class="modal-close"
                onclick="closeModal('modalTambah')"
            >
                <i class="fa-solid fa-xmark"></i>
            </button>

        </div>


        <form method="POST" action="backend/pkl_handler.php">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= e($csrfToken) ?>"
            >

            <input
                type="hidden"
                name="action"
                value="tambah"
            >


            <div class="modal-body">

                <div class="form-group">

                    <label>

                        Nama Siswa

                        <span class="required">*</span>

                    </label>

                    <input
                        type="text"
                        name="nama_siswa"
                        class="form-control"
                        maxlength="150"
                        placeholder="Ketik nama siswa"
                        autocomplete="off"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>

                        Nama Perusahaan / Instansi

                        <span class="required">*</span>

                    </label>

                    <input
                        type="text"
                        name="nama_perusahaan"
                        class="form-control"
                        maxlength="150"
                        placeholder="Ketik nama perusahaan / instansi"
                        autocomplete="off"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>

                        Nama Pembimbing

                        <span class="required">*</span>

                    </label>

                    <input
                        type="text"
                        name="pembimbing"
                        class="form-control"
                        maxlength="100"
                        placeholder="Nama pembimbing PKL"
                        required
                    >

                </div>


                <div class="form-row">

                    <div class="form-group">

                        <label>

                            Tanggal Mulai

                            <span class="required">*</span>

                        </label>

                        <input
                            type="date"
                            name="tanggal_mulai"
                            class="form-control"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>

                            Tanggal Selesai

                            <span class="required">*</span>

                        </label>

                        <input
                            type="date"
                            name="tanggal_selesai"
                            class="form-control"
                            required
                        >

                    </div>

                </div>


                <div class="form-group">

                    <label>Status Penempatan</label>

                    <select
                        name="status_penempatan"
                        class="form-control"
                    >

                        <option value="Draft">Draft</option>

                        <option value="Disetujui">Disetujui</option>

                        <option value="Selesai">Selesai</option>

                    </select>

                </div>

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-outline"
                    onclick="closeModal('modalTambah')"
                >

                    Batal

                </button>


                <button
                    type="submit"
                    class="btn btn-primary"
                >

                    <i class="fa-solid fa-save"></i>

                    Simpan

                </button>

            </div>

        </form>

    </div>

</div>


<!-- =====================================
     MODAL EDIT
===================================== -->

<div class="modal-overlay" id="modalEdit">

    <div class="modal modal-lg">

        <div class="modal-header">

            <span class="modal-title">

                <i class="fa-solid fa-pen-to-square"></i>

                Edit Penempatan PKL

            </span>

            <button
                type="button"
                class="modal-close"
                onclick="closeModal('modalEdit')"
            >

                <i class="fa-solid fa-xmark"></i>

            </button>

        </div>


        <form method="POST" action="backend/pkl_handler.php">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= e($csrfToken) ?>"
            >

            <input
                type="hidden"
                name="action"
                value="edit"
            >

            <input
                type="hidden"
                name="id"
                id="e_id"
            >


            <div class="modal-body">

                <div class="form-group">

                    <label>

                        Nama Siswa

                        <span class="required">*</span>

                    </label>

                    <input
                        type="text"
                        name="nama_siswa"
                        id="e_nama_siswa"
                        class="form-control"
                        maxlength="150"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>

                        Nama Perusahaan / Instansi

                        <span class="required">*</span>

                    </label>

                    <input
                        type="text"
                        name="nama_perusahaan"
                        id="e_nama_perusahaan"
                        class="form-control"
                        maxlength="150"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>

                        Nama Pembimbing

                        <span class="required">*</span>

                    </label>

                    <input
                        type="text"
                        name="pembimbing"
                        id="e_pembimbing"
                        class="form-control"
                        maxlength="100"
                        required
                    >

                </div>


                <div class="form-row">

                    <div class="form-group">

                        <label>

                            Tanggal Mulai

                            <span class="required">*</span>

                        </label>

                        <input
                            type="date"
                            name="tanggal_mulai"
                            id="e_tanggal_mulai"
                            class="form-control"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>

                            Tanggal Selesai

                            <span class="required">*</span>

                        </label>

                        <input
                            type="date"
                            name="tanggal_selesai"
                            id="e_tanggal_selesai"
                            class="form-control"
                            required
                        >

                    </div>

                </div>


                <div class="form-group">

                    <label>Status Penempatan</label>

                    <select
                        name="status_penempatan"
                        id="e_status_penempatan"
                        class="form-control"
                    >

                        <option value="Draft">Draft</option>

                        <option value="Disetujui">Disetujui</option>

                        <option value="Selesai">Selesai</option>

                    </select>

                </div>

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-outline"
                    onclick="closeModal('modalEdit')"
                >

                    Batal

                </button>


                <button
                    type="submit"
                    class="btn btn-primary"
                >

                    <i class="fa-solid fa-save"></i>

                    Perbarui

                </button>

            </div>

        </form>

    </div>

</div>


<!-- =====================================
     FORM HAPUS
===================================== -->

<form
    method="POST"
    action="backend/pkl_handler.php"
    id="formHapus"
>

    <input
        type="hidden"
        name="csrf_token"
        value="<?= e($csrfToken) ?>"
    >

    <input
        type="hidden"
        name="action"
        value="hapus"
    >

    <input
        type="hidden"
        name="id"
        id="hapus_id"
    >

</form>


<!-- =====================================
     JAVASCRIPT
===================================== -->

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="assets/admin.js"></script>

</body>
</html>

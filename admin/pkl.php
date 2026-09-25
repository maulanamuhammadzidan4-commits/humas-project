
<?php

session_start();

require_once '../backend/connection.php';
require_once 'includes/auth.php';

/* =========================
   SEARCH & PAGINATION
========================= */

$search = trim($_GET['search'] ?? '');

$page = max(
    1,
    (int)($_GET['page'] ?? 1)
);

$limit = 10;

$offset = ($page - 1) * $limit;


/* =========================
   WHERE SEARCH
========================= */

$where = '';

if ($search !== '') {

    $where = "
        WHERE
            s.nama_siswa LIKE ?
            OR p.nama_perusahaan LIKE ?
            OR pk.pembimbing_guru LIKE ?
            OR pk.status_penempatan LIKE ?
    ";
}


/* =========================
   COUNT DATA
========================= */
$count_sql = "
    SELECT COUNT(*) AS n
    FROM pkl_penempatan p

    LEFT JOIN siswa s
        ON p.siswa_id = s.id

    LEFT JOIN perusahaan pr
        ON p.perusahaan_id = pr.id

    $where
";
$stmt_count = mysqli_prepare(
    $koneksi,
    $count_sql
);

if (!$stmt_count) {
    die(
        "Gagal menyiapkan query count: " .
        mysqli_error($koneksi)
    );
}


if ($search !== '') {

    $keyword = "%{$search}%";

    mysqli_stmt_bind_param(
        $stmt_count,
        "ssss",
        $keyword,
        $keyword,
        $keyword,
        $keyword
    );
}


mysqli_stmt_execute($stmt_count);

$result_count = mysqli_stmt_get_result(
    $stmt_count
);

$count_data = mysqli_fetch_assoc(
    $result_count
);

$total = (int)(
    $count_data['n'] ?? 0
);

mysqli_stmt_close(
    $stmt_count
);


$total_pages = max(
    1,
    (int)ceil($total / $limit)
);


/* =========================
   AMBIL DATA PKL
========================= */

$data_sql = "
    SELECT
        p.id,
        p.siswa_id,
        p.perusahaan_id,
        s.nama_siswa,
        pr.nama_perusahaan,
        p.pembimbing_guru AS pembimbing,
        p.tanggal_mulai,
        p.tanggal_selesai,
        p.status_penempatan,
        p.created_at,
        p.updated_at
    FROM pkl_penempatan p

    LEFT JOIN siswa s
        ON p.siswa_id = s.id

    LEFT JOIN perusahaan pr
        ON p.perusahaan_id = pr.id

    $where

    ORDER BY p.created_at DESC

    LIMIT ? OFFSET ?
";


$stmt_data = mysqli_prepare(
    $koneksi,
    $data_sql
);

if (!$stmt_data) {
    die(
        "Gagal menyiapkan query data: " .
        mysqli_error($koneksi)
    );
}


if ($search !== '') {

    $keyword = "%{$search}%";

    mysqli_stmt_bind_param(
        $stmt_data,
        "ssssii",
        $keyword,
        $keyword,
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


mysqli_stmt_execute(
    $stmt_data
);


$result_data = mysqli_stmt_get_result(
    $stmt_data
);


$data = mysqli_fetch_all(
    $result_data,
    MYSQLI_ASSOC
);


mysqli_stmt_close(
    $stmt_data
);


/* =========================
   DATA SISWA UNTUK FORM
========================= */

$siswa_query = mysqli_query(
    $koneksi,
    "
    SELECT
        id,
        nisn,
        nama_siswa,
        kelas,
        jurusan
    FROM siswa
    ORDER BY nama_siswa ASC
    "
);

if (!$siswa_query) {
    die(
        "Gagal mengambil data siswa: " .
        mysqli_error($koneksi)
    );
}

$siswa_list = mysqli_fetch_all(
    $siswa_query,
    MYSQLI_ASSOC
);


/* =========================
   DATA PERUSAHAAN UNTUK FORM
========================= */

$perusahaan_query = mysqli_query(
    $koneksi,
    "
    SELECT
        id,
        nama_perusahaan
    FROM perusahaan
    ORDER BY nama_perusahaan ASC
    "
);

if (!$perusahaan_query) {
    die(
        "Gagal mengambil data perusahaan: " .
        mysqli_error($koneksi)
    );
}

$perusahaan_list = mysqli_fetch_all(
    $perusahaan_query,
    MYSQLI_ASSOC
);


$page_title = "Penempatan PKL";

?>

<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Penempatan PKL — Admin Humas SMK
    </title>

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    >

    <link
        rel="stylesheet"
        href="assets/admin-style.css"
    >

</head>


<body>


<?php include 'includes/sidebar.php'; ?>


<div class="admin-main">

    <?php include 'includes/header.php'; ?>


    <main class="admin-content">


        <!-- ALERT -->

        <?php if (isset($_GET['msg'])): ?>

            <div
                class="alert alert-<?= htmlspecialchars(
                    $_GET['type'] ?? 'success'
                ) ?> flash-alert"
            >

                <i class="fa-solid fa-circle-check"></i>

                <?= htmlspecialchars(
                    urldecode($_GET['msg'])
                ) ?>

            </div>

        <?php endif; ?>


        <!-- HEADER -->

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
                    Kelola data penempatan Praktik Kerja Lapangan siswa
                </p>

            </div>


            <button
                class="btn btn-primary"
                onclick="openModal('modalTambah')"
            >

                <i class="fa-solid fa-plus"></i>

                Tambah PKL

            </button>

        </div>


        <!-- CARD -->

        <div class="card">


            <!-- CARD HEADER -->

            <div class="card-header">

                <span class="card-title">

                    <i class="fa-solid fa-list"></i>

                    Data PKL
                    (<?= $total ?>)

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
                            value="<?= htmlspecialchars($search) ?>"
                            placeholder="Cari siswa / perusahaan..."
                        >

                    </div>


                    <button
                        type="submit"
                        class="btn btn-outline btn-sm"
                    >

                        <i class="fa-solid fa-search"></i>

                    </button>


                    <?php if ($search): ?>

                        <a
                            href="pkl.php"
                            class="btn btn-outline btn-sm"
                        >

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

                        <tr>

                            <td colspan="8">

                                <div class="table-empty">

                                    <i class="fa-solid fa-map"></i>

                                    <p>
                                        Belum ada data PKL.
                                    </p>

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


                                <td class="td-no">

                                    <?= $offset + $i + 1 ?>

                                </td>


                                <td style="font-weight:600;">

                                    <?= htmlspecialchars(
                                        $row['nama_siswa']
                                    ) ?>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $row['nama_perusahaan']
                                    ) ?>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $row['pembimbing']
                                    ) ?>

                                </td>


                                <td>

                                    <?= !empty($row['tanggal_mulai'])
                                        ? date(
                                            'd/m/Y',
                                            strtotime($row['tanggal_mulai'])
                                        )
                                        : '-'
                                    ?>

                                </td>


                                <td>

                                    <?= !empty($row['tanggal_selesai'])
                                        ? date(
                                            'd/m/Y',
                                            strtotime($row['tanggal_selesai'])
                                        )
                                        : '-'
                                    ?>

                                </td>


                                <td>

                                    <span
                                        class="badge <?= $badge ?>"
                                    >

                                        <?= htmlspecialchars(
                                            $row['status_penempatan']
                                        ) ?>

                                    </span>

                                </td>


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
                                                    $row['nama_siswa'],
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


            <!-- PAGINATION -->

            <?php if ($total_pages > 1): ?>

                <div class="pagination">


                    <span class="pagination-info">

                        Menampilkan

                        <?= $total > 0
                            ? $offset + 1
                            : 0
                        ?>

                        –

                        <?= min(
                            $offset + $limit,
                            $total
                        ) ?>

                        dari <?= $total ?>

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



<!-- =====================================================
     MODAL TAMBAH
===================================================== -->

<div
    class="modal-overlay"
    id="modalTambah"
>

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


        <form
            method="POST"
            action="backend/pkl_handler.php"
        >

            <input
                type="hidden"
                name="action"
                value="tambah"
            >


            <div class="modal-body">


                <!-- NAMA SISWA -->

                <div class="form-group">

                    <label>

                        Nama Siswa

                        <span class="required">*</span>

                    </label>

                    <input
                        type="text"
                        name="nama_siswa"
                        class="form-control"
                        maxlength="200"
                        placeholder="Ketik nama siswa"
                        autocomplete="off"
                        required
                    >

                </div>


                <!-- NAMA PERUSAHAAN -->

                <div class="form-group">

                    <label>

                        Nama Perusahaan / Instansi

                        <span class="required">*</span>

                    </label>

                    <input
                        type="text"
                        name="nama_perusahaan"
                        class="form-control"
                        maxlength="200"
                        placeholder="Ketik nama perusahaan / instansi"
                        autocomplete="off"
                        required
                    >

                </div>


                <!-- PEMBIMBING -->

                <div class="form-group">

                    <label>

                        Nama Pembimbing

                        <span class="required">*</span>

                    </label>

                    <input
                        type="text"
                        name="pembimbing"
                        class="form-control"
                        maxlength="200"
                        placeholder="Nama pembimbing PKL"
                        required
                    >

                </div>


                <!-- TANGGAL -->

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


                <!-- STATUS -->

                <div class="form-group">

                    <label>
                        Status Penempatan
                    </label>

                    <select
                        name="status_penempatan"
                        class="form-control"
                    >

                        <option value="Draft">
                            Draft
                        </option>

                        <option value="Disetujui">
                            Disetujui
                        </option>

                        <option value="Selesai">
                            Selesai
                        </option>

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



<!-- =====================================================
     MODAL EDIT
===================================================== -->

<div
    class="modal-overlay"
    id="modalEdit"
>

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


        <form
            method="POST"
            action="backend/pkl_handler.php"
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


                <!-- NAMA SISWA -->

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
                        maxlength="200"
                        required
                    >

                </div>


                <!-- NAMA PERUSAHAAN -->

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
                        maxlength="200"
                        required
                    >

                </div>


                <!-- PEMBIMBING -->

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
                        maxlength="200"
                        required
                    >

                </div>


                <!-- TANGGAL -->

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


                <!-- STATUS -->

                <div class="form-group">

                    <label>
                        Status Penempatan
                    </label>

                    <select
                        name="status_penempatan"
                        id="e_status_penempatan"
                        class="form-control"
                    >

                        <option value="Draft">
                            Draft
                        </option>

                        <option value="Disetujui">
                            Disetujui
                        </option>

                        <option value="Selesai">
                            Selesai
                        </option>

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



<!-- FORM HAPUS -->

<form
    method="POST"
    action="backend/pkl_handler.php"
    id="formHapus"
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



<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/admin.js"></script>
</body>
</html>

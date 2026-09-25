<?php

session_start();

require_once '../backend/connection.php';
require_once 'includes/auth.php';


/*
|--------------------------------------------------------------------------
| PROSES TAMBAH / EDIT / HAPUS
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';


    /*
    |--------------------------------------------------------------------------
    | TAMBAH DATA
    |--------------------------------------------------------------------------
    */

    if ($action === 'tambah') {

        $nama_siswa = trim($_POST['nama_siswa'] ?? '');
        $tahun_lulus = (int)($_POST['tahun_lulus'] ?? 0);
        $status_alumni = trim($_POST['status_alumni'] ?? '');
        $nama_instansi = trim($_POST['nama_instansi'] ?? '');

        $pendapatan_bulanan = (
            isset($_POST['pendapatan_bulanan']) &&
            $_POST['pendapatan_bulanan'] !== ''
        )
            ? (int)$_POST['pendapatan_bulanan']
            : null;


        /*
        | Cari ID siswa berdasarkan nama
        */

        if ($nama_siswa === '') {

            header(
                "Location: tracer.php?msg=" .
                urlencode("Nama alumni wajib diisi.") .
                "&type=danger"
            );
            exit;
        }


        $stmt_siswa = mysqli_prepare(
            $koneksi,
            "SELECT id FROM siswa WHERE nama_siswa = ? LIMIT 1"
        );

        if (!$stmt_siswa) {

            die(
                "Gagal mencari siswa: " .
                mysqli_error($koneksi)
            );
        }


        mysqli_stmt_bind_param(
            $stmt_siswa,
            "s",
            $nama_siswa
        );

        mysqli_stmt_execute($stmt_siswa);

        $result_siswa = mysqli_stmt_get_result($stmt_siswa);

        $siswa = mysqli_fetch_assoc($result_siswa);

        mysqli_stmt_close($stmt_siswa);


        /*
        | Jika nama tidak ditemukan
        */

        if (!$siswa) {

            header(
                "Location: tracer.php?msg=" .
                urlencode(
                    "Nama alumni \"$nama_siswa\" tidak ditemukan di data siswa."
                ) .
                "&type=danger"
            );

            exit;
        }


        $siswa_id = (int)$siswa['id'];


        /*
        | Simpan ke tracer_study
        */

        $sql = "
            INSERT INTO tracer_study
            (
                siswa_id,
                tahun_lulus,
                status_alumni,
                nama_instansi,
                pendapatan_bulanan
            )
            VALUES (?, ?, ?, ?, ?)
        ";


        $stmt = mysqli_prepare($koneksi, $sql);

        if (!$stmt) {

            die(
                "Gagal menyiapkan INSERT: " .
                mysqli_error($koneksi)
            );
        }


        mysqli_stmt_bind_param(
            $stmt,
            "iissi",
            $siswa_id,
            $tahun_lulus,
            $status_alumni,
            $nama_instansi,
            $pendapatan_bulanan
        );


        if (mysqli_stmt_execute($stmt)) {

            mysqli_stmt_close($stmt);

            header(
                "Location: tracer.php?msg=" .
                urlencode(
                    "Data tracer untuk $nama_siswa berhasil ditambahkan."
                ) .
                "&type=success"
            );

            exit;

        } else {

            $error = mysqli_stmt_error($stmt);

            mysqli_stmt_close($stmt);

            die(
                "Gagal menyimpan data tracer: " .
                $error
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT DATA
    |--------------------------------------------------------------------------
    */

    if ($action === 'edit') {

        $id = (int)($_POST['id'] ?? 0);

        $nama_siswa = trim($_POST['nama_siswa'] ?? '');

        $tahun_lulus = (int)($_POST['tahun_lulus'] ?? 0);

        $status_alumni = trim(
            $_POST['status_alumni'] ?? ''
        );

        $nama_instansi = trim(
            $_POST['nama_instansi'] ?? ''
        );

        $pendapatan_bulanan = (
            isset($_POST['pendapatan_bulanan']) &&
            $_POST['pendapatan_bulanan'] !== ''
        )
            ? (int)$_POST['pendapatan_bulanan']
            : null;


        /*
        | Validasi
        */

        if ($id <= 0 || $nama_siswa === '') {

            header(
                "Location: tracer.php?msg=" .
                urlencode("Data edit tidak lengkap.") .
                "&type=danger"
            );

            exit;
        }


        /*
        | Cari ID siswa berdasarkan nama
        */

        $stmt_siswa = mysqli_prepare(
            $koneksi,
            "SELECT id FROM siswa WHERE nama_siswa = ? LIMIT 1"
        );

        if (!$stmt_siswa) {

            die(
                "Gagal mencari siswa: " .
                mysqli_error($koneksi)
            );
        }


        mysqli_stmt_bind_param(
            $stmt_siswa,
            "s",
            $nama_siswa
        );

        mysqli_stmt_execute($stmt_siswa);

        $result_siswa = mysqli_stmt_get_result(
            $stmt_siswa
        );

        $siswa = mysqli_fetch_assoc(
            $result_siswa
        );

        mysqli_stmt_close($stmt_siswa);


        if (!$siswa) {

            header(
                "Location: tracer.php?msg=" .
                urlencode(
                    "Nama alumni \"$nama_siswa\" tidak ditemukan."
                ) .
                "&type=danger"
            );

            exit;
        }


        $siswa_id = (int)$siswa['id'];


        /*
        | Update
        */

        $sql = "
            UPDATE tracer_study
            SET
                siswa_id = ?,
                tahun_lulus = ?,
                status_alumni = ?,
                nama_instansi = ?,
                pendapatan_bulanan = ?,
                updated_at = NOW()
            WHERE id = ?
        ";


        $stmt = mysqli_prepare(
            $koneksi,
            $sql
        );

        if (!$stmt) {

            die(
                "Gagal menyiapkan UPDATE: " .
                mysqli_error($koneksi)
            );
        }


        mysqli_stmt_bind_param(
            $stmt,
            "iissii",
            $siswa_id,
            $tahun_lulus,
            $status_alumni,
            $nama_instansi,
            $pendapatan_bulanan,
            $id
        );


        if (mysqli_stmt_execute($stmt)) {

            mysqli_stmt_close($stmt);

            header(
                "Location: tracer.php?msg=" .
                urlencode(
                    "Data tracer berhasil diperbarui."
                ) .
                "&type=success"
            );

            exit;

        } else {

            $error = mysqli_stmt_error($stmt);

            mysqli_stmt_close($stmt);

            die(
                "Gagal memperbarui data: " .
                $error
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | HAPUS DATA
    |--------------------------------------------------------------------------
    */

    if ($action === 'hapus') {

        $id = (int)($_POST['id'] ?? 0);


        if ($id <= 0) {

            header(
                "Location: tracer.php?msg=" .
                urlencode("ID data tidak valid.") .
                "&type=danger"
            );

            exit;
        }


        $stmt = mysqli_prepare(
            $koneksi,
            "DELETE FROM tracer_study WHERE id = ?"
        );


        if (!$stmt) {

            die(
                "Gagal menyiapkan DELETE: " .
                mysqli_error($koneksi)
            );
        }


        mysqli_stmt_bind_param(
            $stmt,
            "i",
            $id
        );


        if (mysqli_stmt_execute($stmt)) {

            mysqli_stmt_close($stmt);

            header(
                "Location: tracer.php?msg=" .
                urlencode(
                    "Data tracer berhasil dihapus."
                ) .
                "&type=success"
            );

            exit;

        } else {

            $error = mysqli_stmt_error($stmt);

            mysqli_stmt_close($stmt);

            die(
                "Gagal menghapus data: " .
                $error
            );
        }
    }
}


/*
|--------------------------------------------------------------------------
| PENGATURAN
|--------------------------------------------------------------------------
*/

$search = trim(
    $_GET['search'] ?? ''
);

$page = max(
    1,
    (int)($_GET['page'] ?? 1)
);

$limit = 10;

$offset = ($page - 1) * $limit;


/*
|--------------------------------------------------------------------------
| PENCARIAN
|--------------------------------------------------------------------------
*/

$where = "";

if ($search !== '') {

    $where = "
        WHERE
            s.nama_siswa LIKE ?
            OR s.nisn LIKE ?
            OR s.kelas LIKE ?
            OR s.jurusan LIKE ?
            OR ts.nama_instansi LIKE ?
            OR ts.status_alumni LIKE ?
    ";
}


/*
|--------------------------------------------------------------------------
| HITUNG TOTAL DATA
|--------------------------------------------------------------------------
*/

$count_sql = "
    SELECT COUNT(*) AS total
    FROM tracer_study ts
    INNER JOIN siswa s
        ON s.id = ts.siswa_id
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
        "ssssss",
        $keyword,
        $keyword,
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
    $count_data['total'] ?? 0
);


$total_pages = max(
    1,
    (int)ceil($total / $limit)
);


mysqli_stmt_close($stmt_count);


/*
|--------------------------------------------------------------------------
| AMBIL DATA TRACER
|--------------------------------------------------------------------------
*/

$data_sql = "
    SELECT
        ts.id,
        ts.siswa_id,
        ts.tahun_lulus,
        ts.status_alumni,
        ts.nama_instansi,
        ts.pendapatan_bulanan,
        ts.created_at,
        ts.updated_at,

        s.id AS id_siswa,
        s.nisn,
        s.nama_siswa,
        s.kelas,
        s.jurusan,
        s.status_alumni AS status_siswa

    FROM tracer_study ts

    INNER JOIN siswa s
        ON s.id = ts.siswa_id

    $where

    ORDER BY ts.created_at DESC

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
        "ssssssii",
        $keyword,
        $keyword,
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


mysqli_stmt_execute($stmt_data);


$result_data = mysqli_stmt_get_result(
    $stmt_data
);


$data = mysqli_fetch_all(
    $result_data,
    MYSQLI_ASSOC
);


mysqli_stmt_close($stmt_data);


/*
|--------------------------------------------------------------------------
| STATISTIK
|--------------------------------------------------------------------------
*/

$total_tracer_sql = "
    SELECT COUNT(*) AS total
    FROM tracer_study
";

$result = mysqli_query(
    $koneksi,
    $total_tracer_sql
);

$total_tracer = (int)(
    mysqli_fetch_assoc($result)['total'] ?? 0
);


$bekerja_sql = "
    SELECT COUNT(*) AS total
    FROM tracer_study
    WHERE status_alumni = 'Bekerja'
";

$result = mysqli_query(
    $koneksi,
    $bekerja_sql
);

$total_bekerja = (int)(
    mysqli_fetch_assoc($result)['total'] ?? 0
);


$kuliah_sql = "
    SELECT COUNT(*) AS total
    FROM tracer_study
    WHERE status_alumni = 'Kuliah'
";

$result = mysqli_query(
    $koneksi,
    $kuliah_sql
);

$total_kuliah = (int)(
    mysqli_fetch_assoc($result)['total'] ?? 0
);


$wirausaha_sql = "
    SELECT COUNT(*) AS total
    FROM tracer_study
    WHERE status_alumni = 'Wirausaha'
";

$result = mysqli_query(
    $koneksi,
    $wirausaha_sql
);

$total_wirausaha = (int)(
    mysqli_fetch_assoc($result)['total'] ?? 0
);


$mencari_sql = "
    SELECT COUNT(*) AS total
    FROM tracer_study
    WHERE status_alumni = 'Mencari Kerja'
";

$result = mysqli_query(
    $koneksi,
    $mencari_sql
);

$total_mencari = (int)(
    mysqli_fetch_assoc($result)['total'] ?? 0
);


$page_title = "Tracer Study";

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
        Tracer Study — Admin Humas SMK
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

    <div class="alert alert-<?= htmlspecialchars(
        $_GET['type'] ?? 'success'
    ) ?>">

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
                class="fa-solid fa-route"
                style="color:#ec4899;margin-right:8px;"
            ></i>

            Tracer Study

        </h2>

        <p>
            Kelola data penelusuran alumni setelah lulus
        </p>

    </div>


    <button
        class="btn btn-primary"
        onclick="openModal('modalTambah')"
    >

        <i class="fa-solid fa-plus"></i>

        Tambah Data

    </button>

</div>


<!-- DATA CARD -->

<div class="card">


<div class="card-header">


<span class="card-title">

    <i class="fa-solid fa-list"></i>

    Data Tracer Study
    (<?= $total ?>)

</span>


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
            placeholder="Cari nama / instansi..."
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
            href="tracer.php"
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

    <th>Nama Alumni</th>

    <th>Jurusan</th>

    <th>Kelas</th>

    <th>Tahun Lulus</th>

    <th>Status</th>

    <th>Instansi / Kampus</th>

    <th>Pendapatan</th>

    <th>Aksi</th>

</tr>

</thead>


<tbody>


<?php if (empty($data)): ?>

<tr>

<td colspan="9">

<div class="table-empty">

    <i class="fa-solid fa-route"></i>

    <p>
        Belum ada data tracer study.
    </p>

</div>

</td>

</tr>


<?php else: ?>


<?php foreach ($data as $i => $row): ?>


<?php

switch ($row['status_alumni']) {

    case 'Bekerja':
        $badge = 'badge-green';
        break;

    case 'Kuliah':
        $badge = 'badge-blue';
        break;

    case 'Wirausaha':
        $badge = 'badge-gold';
        break;

    case 'Mencari Kerja':
        $badge = 'badge-red';
        break;

    case 'Menikah':
        $badge = 'badge-red';
        break;

    default:
        $badge = 'badge-gray';
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
        $row['jurusan'] ?? '-'
    ) ?>

</td>


<td>

    <?= htmlspecialchars(
        $row['kelas'] ?? '-'
    ) ?>

</td>


<td>

    <?= htmlspecialchars(
        $row['tahun_lulus']
    ) ?>

</td>


<td>

<span class="badge <?= $badge ?>">

    <?= htmlspecialchars(
        $row['status_alumni']
    ) ?>

</span>

</td>


<td>

    <?= htmlspecialchars(
        $row['nama_instansi'] ?? '-'
    ) ?>

</td>


<td>


<?php if (
    $row['pendapatan_bulanan'] !== null &&
    $row['pendapatan_bulanan'] !== ''
): ?>

    Rp
    <?= number_format(
        $row['pendapatan_bulanan'],
        0,
        ',',
        '.'
    ) ?>


<?php else: ?>

    -

<?php endif; ?>


</td>


<td>


<div class="action-btns">


<!-- EDIT -->

<button
    type="button"
    class="btn btn-warning btn-sm btn-icon"
    title="Edit"

    onclick='editTracer(
        <?= json_encode(
            $row,
            JSON_HEX_TAG |
            JSON_HEX_APOS |
            JSON_HEX_QUOT |
            JSON_HEX_AMP
        ) ?>
    )'
>

    <i class="fa-solid fa-pen"></i>

</button>


<!-- HAPUS -->

<button
    type="button"
    class="btn btn-danger btn-sm btn-icon"
    title="Hapus"

    onclick='hapusTracer(
        <?= (int)$row['id'] ?>,
        <?= json_encode(
            $row['nama_siswa'],
            JSON_HEX_TAG |
            JSON_HEX_APOS |
            JSON_HEX_QUOT |
            JSON_HEX_AMP
        ) ?>
    )'
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

<div class="modal">


<div class="modal-header">

<span class="modal-title">

    <i class="fa-solid fa-route"></i>

    Tambah Tracer Study

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
    action="tracer.php"
>


<input
    type="hidden"
    name="action"
    value="tambah"
>


<div class="modal-body">


<!-- NAMA -->

<div class="form-group">

<label>

    Nama Siswa Alumni

    <span class="required">*</span>

</label>


<input
    type="text"
    name="nama_siswa"
    class="form-control"
    maxlength="200"
    placeholder="Ketik nama alumni"
    required
>

</div>


<!-- TAHUN -->

<div class="form-group">

<label>

    Tahun Lulus

    <span class="required">*</span>

</label>


<input
    type="number"
    name="tahun_lulus"
    class="form-control"
    required
    min="2000"
    max="<?= date('Y') ?>"
    placeholder="<?= date('Y') ?>"
>

</div>


<!-- STATUS -->

<div class="form-group">

<label>

    Status Alumni

    <span class="required">*</span>

</label>


<select
    name="status_alumni"
    class="form-control"
    required
>

    <option value="Bekerja">
        Bekerja
    </option>

    <option value="Kuliah">
        Kuliah
    </option>

    <option value="Wirausaha">
        Wirausaha
    </option>

    <option value="Mencari Kerja">
        Mencari Kerja
    </option>

    <option value="Menikah">
        Menikah
    </option>

</select>

</div>


<!-- INSTANSI -->

<div class="form-group">

<label>
    Nama Instansi / Kampus
</label>


<input
    type="text"
    name="nama_instansi"
    class="form-control"
    maxlength="150"
    placeholder="Contoh: PT Maju Jaya / Universitas Indonesia"
>

</div>


<!-- PENDAPATAN -->

<div class="form-group">

<label>
    Pendapatan Bulanan (Rp)
</label>


<input
    type="number"
    name="pendapatan_bulanan"
    class="form-control"
    min="0"
    placeholder="Contoh: 3500000"
>

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

<div class="modal">


<div class="modal-header">

<span class="modal-title">

    <i class="fa-solid fa-pen-to-square"></i>

    Edit Tracer Study

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
    action="tracer.php"
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


<!-- NAMA -->

<div class="form-group">

<label>

    Nama Siswa Alumni

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


<!-- TAHUN -->

<div class="form-group">

<label>

    Tahun Lulus

    <span class="required">*</span>

</label>


<input
    type="number"
    name="tahun_lulus"
    id="e_tahun_lulus"
    class="form-control"
    required
    min="2000"
    max="<?= date('Y') ?>"
>

</div>


<!-- STATUS -->

<div class="form-group">

<label>

    Status Alumni

    <span class="required">*</span>

</label>


<select
    name="status_alumni"
    id="e_status_alumni"
    class="form-control"
    required
>

    <option value="Bekerja">
        Bekerja
    </option>

    <option value="Kuliah">
        Kuliah
    </option>

    <option value="Wirausaha">
        Wirausaha
    </option>

    <option value="Mencari Kerja">
        Mencari Kerja
    </option>

    <option value="Menikah">
        Menikah
    </option>

</select>

</div>


<!-- INSTANSI -->

<div class="form-group">

<label>
    Nama Instansi / Kampus
</label>


<input
    type="text"
    name="nama_instansi"
    id="e_nama_instansi"
    class="form-control"
    maxlength="150"
>

</div>


<!-- PENDAPATAN -->

<div class="form-group">

<label>
    Pendapatan Bulanan (Rp)
</label>


<input
    type="number"
    name="pendapatan_bulanan"
    id="e_pendapatan_bulanan"
    class="form-control"
    min="0"
>

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
    action="tracer.php"
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



<!-- JAVASCRIPT -->

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/admin.js"></script>
</body>
</html>

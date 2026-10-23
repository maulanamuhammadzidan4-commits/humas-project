
<?php
session_start();

require_once __DIR__ . '/backend/connection.php';

mysqli_set_charset($koneksi, 'utf8mb4');

// Token keamanan formulir
if (empty($_SESSION['csrf_tracer'])) {
    $_SESSION['csrf_tracer'] = bin2hex(random_bytes(32));
}

// Variabel formulir
$pesan = '';
$jenisPesan = '';
$nama = '';
$nisn = '';
$tahun = date('Y');
$status = '';
$instansi = '';
$pendapatan = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $nisn = trim($_POST['nisn'] ?? '');
    $tahun = trim($_POST['tahun_lulus'] ?? '');
    $status = trim($_POST['status_alumni'] ?? '');
    $instansi = trim($_POST['nama_instansi'] ?? '');
    $pendapatan = trim($_POST['pendapatan_bulanan'] ?? '');
    $csrf = $_POST['csrf_token'] ?? '';

    // Status yang diperbolehkan
    $statusValid = [
        'Bekerja',
        'Kuliah',
        'Wirausaha',
        'Mencari Kerja',
        'menikah'
    ];

    // Jika menikah, instansi tidak digunakan
    if ($status === 'menikah') {
        $instansi = '';
    }

    // Validasi CSRF
    if (
        !is_string($csrf) ||
        !hash_equals($_SESSION['csrf_tracer'], $csrf)
    ) {
        $pesan = 'Sesi formulir tidak valid. Silakan muat ulang halaman.';
        $jenisPesan = 'error';

    // Validasi data utama
    } elseif (
        $nama === '' ||
        mb_strlen($nama) > 150 ||
        !preg_match('/^[0-9]{10}$/', $nisn) ||
        !ctype_digit($tahun) ||
        (int)$tahun < 2000 ||
        (int)$tahun > (int)date('Y') + 1 ||
        !in_array($status, $statusValid, true)
    ) {
        $pesan = 'Periksa kembali nama, NISN, tahun lulus, dan status.';
        $jenisPesan = 'error';

    // Validasi pendapatan
    } elseif (
        $pendapatan !== '' &&
        (
            !ctype_digit($pendapatan) ||
            (float)$pendapatan > 2147483647
        )
    ) {
        $pesan = 'Pendapatan harus berupa angka yang valid.';
        $jenisPesan = 'error';

    // Validasi instansi
    } elseif (mb_strlen($instansi) > 150) {
        $pesan = 'Nama instansi maksimal 150 karakter.';
        $jenisPesan = 'error';

    } else {
        try {
            // Cari siswa berdasarkan NISN
            $stmt = mysqli_prepare(
                $koneksi,
                "SELECT id, nama_siswa
                 FROM siswa
                 WHERE nisn = ?
                 LIMIT 1"
            );

            mysqli_stmt_bind_param($stmt, 's', $nisn);
            mysqli_stmt_execute($stmt);

            $hasil = mysqli_stmt_get_result($stmt);
            $siswa = mysqli_fetch_assoc($hasil);

            mysqli_stmt_close($stmt);

            if (!$siswa) {
                $pesan = 'NISN tidak ditemukan dalam data siswa sekolah. '
                       . 'Periksa kembali NISN atau hubungi pihak sekolah.';
                $jenisPesan = 'error';

            } else {
                $siswaId = (int)$siswa['id'];
                $tahunLulus = (int)$tahun;

                // Simpan instansi sebagai NULL jika kosong
                $namaInstansi = $instansi === ''
                    ? null
                    : $instansi;

                // Simpan pendapatan sebagai NULL jika kosong
                $pendapatanNilai = $pendapatan === ''
                    ? null
                    : (int)$pendapatan;

                // Simpan data tracer study
                $stmt = mysqli_prepare(
                    $koneksi,
                    "INSERT INTO tracer_study
                    (
                        siswa_id,
                        tahun_lulus,
                        status_alumni,
                        nama_instansi,
                        pendapatan_bulanan
                    )
                    VALUES (?, ?, ?, ?, ?)"
                );

                mysqli_stmt_bind_param(
                    $stmt,
                    'iissi',
                    $siswaId,
                    $tahunLulus,
                    $status,
                    $namaInstansi,
                    $pendapatanNilai
                );

                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                $pesan = 'Data tracer study berhasil dikirim. Terima kasih!';
                $jenisPesan = 'sukses';

                // Kosongkan formulir setelah berhasil
                $nama = '';
                $nisn = '';
                $tahun = date('Y');
                $status = '';
                $instansi = '';
                $pendapatan = '';

                // Perbarui token setelah pengiriman berhasil
                $_SESSION['csrf_tracer'] = bin2hex(random_bytes(32));
            }

        } catch (mysqli_sql_exception $e) {
            error_log('Tracer Study Error: ' . $e->getMessage());

            if ((int)$e->getCode() === 1062) {
                $pesan = 'Data tracer study untuk siswa ini sudah pernah dikirim.';
            } else {
                $pesan = 'Terjadi kesalahan saat menyimpan data. '
                       . 'Silakan periksa database atau hubungi administrator.';
            }

            $jenisPesan = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tracer Study Alumni - Humas SMK</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 30px 15px;
            font-family: Arial, Helvetica, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
        }

        .card {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            padding: 30px;
            background: #ffffff;
            border-radius: 14px;
            box-shadow: 0 8px 30px rgba(15, 23, 42, 0.08);
        }

        h1 {
            margin: 0 0 12px;
            color: #17365d;
            font-size: 28px;
        }

        .intro {
            color: #64748b;
            line-height: 1.6;
        }

        label {
            display: block;
            font-weight: 600;
            margin: 18px 0 7px;
        }

        input,
        select {
            display: block;
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #ffffff;
            color: #1e293b;
            font-size: 15px;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #17365d;
            box-shadow: 0 0 0 3px rgba(23, 54, 93, 0.12);
        }

        button {
            width: 100%;
            padding: 13px;
            margin-top: 24px;
            border: 0;
            border-radius: 8px;
            background: #17365d;
            color: #ffffff;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #244b7a;
        }

        .notice {
            padding: 12px;
            border-radius: 8px;
            margin: 16px 0;
            line-height: 1.5;
        }

        .sukses {
            background: #dcfce7;
            color: #166534;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
        }

        small {
            display: block;
            margin-top: 6px;
            color: #64748b;
            line-height: 1.5;
        }

        @media (max-width: 480px) {
            body {
                padding: 15px 10px;
            }

            .card {
                padding: 20px;
            }

            h1 {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>
<div class="card">
    <h1>Tracer Study Alumni</h1>

    <p class="intro">
        Formulir pendataan alumni Humas SMK.
        Isilah data dengan benar untuk membantu sekolah
        memantau perkembangan lulusan.
    </p>

    <?php if ($pesan !== ''): ?>
        <div class="notice <?= htmlspecialchars($jenisPesan, ENT_QUOTES, 'UTF-8') ?>">
            <?= htmlspecialchars($pesan, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars($_SESSION['csrf_tracer'], ENT_QUOTES, 'UTF-8') ?>"
        >

        <label for="nama">Nama lengkap (untuk konfirmasi) *</label>
        <input
            type="text"
            id="nama"
            name="nama"
            required
            maxlength="150"
            autocomplete="name"
            value="<?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8') ?>"
        >
        <small>
            Masukkan nama lengkap untuk konfirmasi data.
        </small>

        <label for="nisn">NISN (10 digit) *</label>
        <input
            type="text"
            id="nisn"
            name="nisn"
            required
            inputmode="numeric"
            pattern="[0-9]{10}"
            minlength="10"
            maxlength="10"
            value="<?= htmlspecialchars($nisn, ENT_QUOTES, 'UTF-8') ?>"
        >
        <small>
            Pastikan NISN benar. NISN digunakan untuk mencocokkan data siswa.
        </small>

        <label for="tahun_lulus">Tahun lulus *</label>
        <input
            type="number"
            id="tahun_lulus"
            name="tahun_lulus"
            min="2000"
            max="<?= (int)date('Y') + 1 ?>"
            required
            value="<?= htmlspecialchars($tahun, ENT_QUOTES, 'UTF-8') ?>"
        >

        <label for="status_alumni">Status setelah lulus *</label>
        <select id="status_alumni" name="status_alumni" required>
            <option value="">-- Pilih status --</option>

            <?php
            $opsi = [
                'Bekerja' => 'Bekerja',
                'Kuliah' => 'Kuliah',
                'Wirausaha' => 'Wirausaha',
                'Mencari Kerja' => 'Mencari Kerja',
                'menikah' => 'Menikah'
            ];

            foreach ($opsi as $nilai => $label):
            ?>
                <option
                    value="<?= htmlspecialchars($nilai, ENT_QUOTES, 'UTF-8') ?>"
                    <?= $status === $nilai ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>

        <div id="instansiGroup">
            <label for="nama_instansi">
                Nama perusahaan, perguruan tinggi, atau usaha
            </label>

            <input
                type="text"
                id="nama_instansi"
                name="nama_instansi"
                maxlength="150"
                value="<?= htmlspecialchars($instansi, ENT_QUOTES, 'UTF-8') ?>"
            >

            <small id="instansiHint">
                Isi nama instansi sesuai status setelah lulus.
            </small>
        </div>

        <label for="pendapatan_bulanan">
            Pendapatan bulanan (rupiah)
        </label>
        <input
            type="number"
            id="pendapatan_bulanan"
            name="pendapatan_bulanan"
            min="0"
            max="2147483647"
            step="1"
            value="<?= htmlspecialchars($pendapatan, ENT_QUOTES, 'UTF-8') ?>"
        >
        <small>
            Boleh dikosongkan jika tidak relevan.
        </small>

        <button type="submit">Kirim Data Tracer Study</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const statusSelect = document.getElementById('status_alumni');
    const instansiGroup = document.getElementById('instansiGroup');
    const instansiInput = document.getElementById('nama_instansi');
    const instansiHint = document.getElementById('instansiHint');

    function aturInstansi() {
        const status = statusSelect.value;

        if (status === 'menikah') {
            // Sembunyikan kolom instansi dan kosongkan nilainya
            instansiGroup.hidden = true;
            instansiInput.value = '';
            instansiInput.disabled = true;

        } else {
            instansiGroup.hidden = false;
            instansiInput.disabled = false;

            if (status === 'Bekerja') {
                instansiHint.textContent =
                    'Isi nama perusahaan tempat kamu bekerja.';

            } else if (status === 'Kuliah') {
                instansiHint.textContent =
                    'Isi nama perguruan tinggi tempat kamu kuliah.';

            } else if (status === 'Wirausaha') {
                instansiHint.textContent =
                    'Isi nama usaha kamu.';

            } else if (status === 'Mencari Kerja') {
                instansiHint.textContent =
                    'Boleh dikosongkan atau isi tanda strip (-).';

            } else {
                instansiHint.textContent =
                    'Isi jika relevan dengan statusmu.';
            }
        }
    }

    statusSelect.addEventListener('change', aturInstansi);

    // Atur tampilan awal dan setelah validasi PHP
    aturInstansi();
});
</script>
</body>
</html>

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

            // Verifikasi kesesuaian nama alumni dengan data siswa
            } elseif (strcasecmp(trim($nama), trim($siswa['nama_siswa'])) !== 0) {
                $pesan = 'Nama yang dimasukkan tidak sesuai dengan data siswa untuk NISN tersebut. '
                       . 'Pastikan nama lengkap sesuai data sekolah.';
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

    <title>Tracer Study Alumni - Humas SMKN 1 Maja</title>
    <meta name="description" content="Formulir pendataan alumni SMKN 1 Maja. Isi data tracer study untuk membantu sekolah memantau perkembangan lulusan.">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <!-- FontAwesome 6 CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-navy: #0f172a;
            --primary-navy-light: #1e293b;
            --brand-gold: #f59e0b;
            --brand-gold-hover: #d97706;
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --surface: #1e293b;
            --surface-light: #334155;
            --radius-md: 12px;
            --radius-lg: 16px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', Arial, Helvetica, sans-serif;
            background: var(--primary-navy);
            color: var(--text-primary);
            min-height: 100vh;
        }

        /* ── Top Navigation Bar ── */
        .tracer-navbar {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            padding: 16px 24px;
            position: sticky;
            top: 0;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .tracer-navbar .nav-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--text-primary);
            font-weight: 700;
            font-size: 16px;
        }

        .tracer-navbar .nav-brand i {
            color: var(--brand-gold);
            font-size: 20px;
        }

        .tracer-navbar .nav-brand span {
            color: var(--brand-gold);
        }

        .tracer-navbar .nav-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 18px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .tracer-navbar .nav-back:hover {
            background: rgba(245, 158, 11, 0.12);
            border-color: var(--brand-gold);
            color: var(--brand-gold);
        }

        /* ── Page Hero ── */
        .tracer-hero {
            text-align: center;
            padding: 50px 20px 30px;
            background: linear-gradient(180deg, rgba(245, 158, 11, 0.05) 0%, transparent 100%);
        }

        .tracer-hero .hero-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--brand-gold), var(--brand-gold-hover));
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            box-shadow: 0 8px 32px rgba(245, 158, 11, 0.25);
        }

        .tracer-hero .hero-icon i {
            font-size: 28px;
            color: var(--primary-navy);
        }

        .tracer-hero h1 {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 12px;
            background: linear-gradient(135deg, #ffffff 0%, #cbd5e1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .tracer-hero p {
            color: var(--text-secondary);
            max-width: 520px;
            margin: 0 auto;
            line-height: 1.7;
            font-size: 15px;
        }

        /* ── Form Card ── */
        .form-container {
            max-width: 620px;
            margin: 0 auto 50px;
            padding: 0 20px;
        }

        .form-card {
            background: var(--surface);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: var(--radius-lg);
            padding: 36px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.25);
        }

        /* ── Notice Messages ── */
        .notice {
            padding: 14px 18px;
            border-radius: var(--radius-md);
            margin-bottom: 24px;
            line-height: 1.6;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .notice i {
            margin-top: 2px;
            font-size: 16px;
        }

        .notice.sukses {
            background: rgba(34, 197, 94, 0.12);
            border: 1px solid rgba(34, 197, 94, 0.25);
            color: #4ade80;
        }

        .notice.error {
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.25);
            color: #f87171;
        }

        /* ── Form Fields ── */
        .form-group {
            margin-bottom: 22px;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
            color: var(--text-primary);
        }

        label .required {
            color: var(--brand-gold);
        }

        input,
        select {
            display: block;
            width: 100%;
            padding: 13px 16px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-md);
            background: rgba(255, 255, 255, 0.04);
            color: var(--text-primary);
            font-size: 15px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: all 0.3s ease;
        }

        input::placeholder {
            color: var(--text-secondary);
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: var(--brand-gold);
            background: rgba(245, 158, 11, 0.04);
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
        }

        select {
            cursor: pointer;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%2394a3b8' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            padding-right: 38px;
        }

        select option {
            background: var(--surface);
            color: var(--text-primary);
        }

        .field-hint {
            display: block;
            margin-top: 6px;
            color: var(--text-secondary);
            line-height: 1.5;
            font-size: 13px;
        }

        /* ── Submit Button ── */
        .btn-submit {
            width: 100%;
            padding: 14px;
            margin-top: 8px;
            border: 0;
            border-radius: var(--radius-md);
            background: linear-gradient(135deg, var(--brand-gold) 0%, var(--brand-gold-hover) 100%);
            color: var(--primary-navy);
            font-size: 16px;
            font-weight: 700;
            font-family: 'Plus Jakarta Sans', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.25);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(245, 158, 11, 0.4);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        /* ── Footer ── */
        .tracer-footer {
            text-align: center;
            padding: 24px 20px;
            color: var(--text-secondary);
            font-size: 13px;
            border-top: 1px solid rgba(255, 255, 255, 0.04);
        }

        .tracer-footer a {
            color: var(--brand-gold);
            text-decoration: none;
        }

        .tracer-footer a:hover {
            text-decoration: underline;
        }

        /* ── Responsive ── */
        @media (max-width: 480px) {
            .tracer-hero {
                padding: 30px 15px 20px;
            }

            .tracer-hero h1 {
                font-size: 24px;
            }

            .form-card {
                padding: 24px 20px;
            }

            .tracer-navbar {
                padding: 12px 16px;
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>

<body>

<!-- Navigation Bar -->
<header class="tracer-navbar">
    <a href="frontend/index.php" class="nav-brand" id="navBrand">
        <i class="fa-solid fa-graduation-cap"></i>
        HUMAS <span>SMKN 1 MAJA</span>
    </a>
    <a href="frontend/index.php" class="nav-back" id="navBack">
        <i class="fa-solid fa-arrow-left"></i>
        Kembali ke Beranda
    </a>
</header>

<!-- Hero Section -->
<section class="tracer-hero">
    <div class="hero-icon">
        <i class="fa-solid fa-route"></i>
    </div>
    <h1>Tracer Study Alumni</h1>
    <p>
        Formulir pendataan alumni Humas SMKN 1 Maja.
        Isilah data dengan benar untuk membantu sekolah
        memantau perkembangan lulusan.
    </p>
</section>

<!-- Form -->
<div class="form-container">
    <div class="form-card">
        <?php if ($pesan !== ''): ?>
            <div class="notice <?= htmlspecialchars($jenisPesan, ENT_QUOTES, 'UTF-8') ?>">
                <i class="fa-solid <?= $jenisPesan === 'sukses' ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"></i>
                <span><?= htmlspecialchars($pesan, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars($_SESSION['csrf_tracer'], ENT_QUOTES, 'UTF-8') ?>"
            >

            <div class="form-group">
                <label for="nama">Nama lengkap (untuk konfirmasi) <span class="required">*</span></label>
                <input
                    type="text"
                    id="nama"
                    name="nama"
                    required
                    maxlength="150"
                    autocomplete="name"
                    placeholder="Masukkan nama lengkap sesuai data sekolah"
                    value="<?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8') ?>"
                >
                <small class="field-hint">
                    Nama harus sama persis dengan data siswa di sekolah (pencocokan otomatis).
                </small>
            </div>

            <div class="form-group">
                <label for="nisn">NISN (10 digit) <span class="required">*</span></label>
                <input
                    type="text"
                    id="nisn"
                    name="nisn"
                    required
                    inputmode="numeric"
                    pattern="[0-9]{10}"
                    minlength="10"
                    maxlength="10"
                    placeholder="Contoh: 0012345678"
                    value="<?= htmlspecialchars($nisn, ENT_QUOTES, 'UTF-8') ?>"
                >
                <small class="field-hint">
                    Pastikan NISN benar. NISN digunakan untuk mencocokkan data siswa.
                </small>
            </div>

            <div class="form-group">
                <label for="tahun_lulus">Tahun lulus <span class="required">*</span></label>
                <input
                    type="number"
                    id="tahun_lulus"
                    name="tahun_lulus"
                    min="2000"
                    max="<?= (int)date('Y') + 1 ?>"
                    required
                    value="<?= htmlspecialchars($tahun, ENT_QUOTES, 'UTF-8') ?>"
                >
            </div>

            <div class="form-group">
                <label for="status_alumni">Status setelah lulus <span class="required">*</span></label>
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
            </div>

            <div class="form-group" id="instansiGroup">
                <label for="nama_instansi">
                    Nama perusahaan, perguruan tinggi, atau usaha
                </label>

                <input
                    type="text"
                    id="nama_instansi"
                    name="nama_instansi"
                    maxlength="150"
                    placeholder="Isi sesuai status setelah lulus"
                    value="<?= htmlspecialchars($instansi, ENT_QUOTES, 'UTF-8') ?>"
                >

                <small class="field-hint" id="instansiHint">
                    Isi nama instansi sesuai status setelah lulus.
                </small>
            </div>

            <div class="form-group">
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
                    placeholder="Contoh: 3500000"
                    value="<?= htmlspecialchars($pendapatan, ENT_QUOTES, 'UTF-8') ?>"
                >
                <small class="field-hint">
                    Boleh dikosongkan jika tidak relevan.
                </small>
            </div>

            <button type="submit" class="btn-submit" id="btnSubmit">
                <i class="fa-solid fa-paper-plane"></i>
                Kirim Data Tracer Study
            </button>
        </form>
    </div>
</div>

<!-- Footer -->
<footer class="tracer-footer">
    <p>&copy; <?= date('Y') ?> Humas SMKN 1 Maja — <a href="frontend/index.php">Kembali ke Beranda</a></p>
</footer>

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

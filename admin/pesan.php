<?php
session_start();
require_once '../backend/connection.php';
require_once 'includes/auth.php';

/* PROSES TANDAI PESAN SUDAH DITERIMA */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['terima_pesan'])) {
        $id_kontak = (int)($_POST['id_kontak'] ?? 0);
        if ($id_kontak > 0) {
            $stmt = mysqli_prepare(
                $koneksi,
                "UPDATE kontak
                 SET status = 'Sudah Dibaca'
                 WHERE id_kontak = ?"
            );
            mysqli_stmt_bind_param(
                $stmt,
                "i",
                $id_kontak
            );
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        header("Location: pesan.php");
        exit;
    }
}

/*AMBIL DATA PESAN */
$sql = "
    SELECT
        id_kontak,
        nama,
        email,
        subjek,
        pesan,
        tanggal_kirim,
        status
    FROM kontak
    ORDER BY tanggal_kirim DESC
";
$result = mysqli_query($koneksi, $sql);
if (!$result) {
    die(
        "Gagal mengambil data pesan: "
        . mysqli_error($koneksi)
    );
}
$data = mysqli_fetch_all(
    $result,
    MYSQLI_ASSOC
);

/* STATISTIK*/
$totalPesan = count($data);
$belumDibaca = 0;
$sudahDibaca = 0;
foreach ($data as $row) {
    if ($row['status'] === 'Sudah Dibaca') {
        $sudahDibaca++;
    } else {
        $belumDibaca++;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Pesan Masuk — Admin Humas SMK</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/admin-style.css">
    <style>
        /*STATISTIK PESAN*/
        .message-stats {
            display: grid;
            grid-template-columns:
                repeat(3, 1fr);
            gap: 18px;
            margin-bottom: 22px;
        }

        .message-stat {
            background: #fff;
            border: 1px solid #e8ecf3;
            border-radius: 16px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .message-stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f0f4f9;
            color: var(--blue);
            font-size: 19px;
        }

        .message-stat-info span {
            display: block;
            font-size: 12px;
            color: #7b8495;
            margin-bottom: 4px;
        }

        .message-stat-info strong {
            font-size: 22px;
            color: #172033;
        }

        /* PESAN*/
        .message-text {
            max-width: 350px;
            line-height: 1.7;
            color: #687386;
        }

        .message-name {
            font-weight: 700;
            color: #172033;
        }

        .message-email {
            color: var(--blue);
            font-size: 13px;
        }

        .message-subject {
            font-weight: 600;
            color: #303b4f;
        }

        .message-date {
            white-space: nowrap;
            color: #7b8495;
            font-size: 12px;
            line-height: 1.6;
        }

        /* BADGE STATUS*/
        .message-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 11px;
            border-radius: 30px;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }

        .message-badge.unread {
            background: #fff4d6;
            color: #9a6900;
        }

        .message-badge.read {
            background: #e7f7ee;
            color: #21804b;
        }

        /*TOMBOL TERIMA */
        .btn-terima-pesan {
            border: none;
            background: var(--blue);
            color: #fff;
            padding: 8px 12px;
            border-radius: 8px;
            font-family: inherit;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            transition: .2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        .btn-terima-pesan:hover {
            transform: translateY(-1px);
            opacity: .9;
        }

        .pesan-diterima {
            color: #21804b;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }

        .pesan-empty {
            text-align: center;
            padding: 60px 20px;
            color: #8992a3;
        }

        .pesan-empty i {
            font-size: 45px;
            margin-bottom: 15px;
            opacity: .4;
        }

        .pesan-empty p {
            margin-top: 5px;
            font-size: 13px;
        }

        @media (max-width: 900px) {
            .message-stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<?php include 'includes/sidebar.php'; ?>
<div class="admin-main">
    <?php include 'includes/header.php'; ?>
    <main class="admin-content">

        <!--HEADER HALAMAN -->
        <div class="page-header">
            <div class="page-header-left">
                <h2><i class="fa-solid fa-envelope" style="color:var(--blue);margin-right:8px;"></i>
                    Pesan Masuk</h2>
                <p>Kelola pesan yang dikirim melalui formulir kontak website.</p>
            </div>
        </div>

        <!--STATISTIK -->
        <div class="message-stats">

            <!-- TOTAL -->
            <div class="message-stat">
                <div class="message-stat-icon">
                    <i class="fa-solid fa-envelope"></i>
                </div>
                <div class="message-stat-info">
                    <span>Total Pesan</span>
                    <strong><?= $totalPesan ?></strong>
                </div>
            </div>

            <!-- SUDAH -->
            <div class="message-stat">
                <div class="message-stat-icon">
                    <i class="fa-solid fa-envelope-open"></i>
                </div>
                <div class="message-stat-info">
                    <span>Sudah Diterima</span>
                    <strong><?= $sudahDibaca ?></strong>
                </div>
            </div>

            <!-- BELUM -->
            <div class="message-stat">
                <div class="message-stat-icon">
                    <i class="fa-solid fa-bell"></i>
                </div>
                <div class="message-stat-info">
                    <span>Belum Diterima</span>
                    <strong><?= $belumDibaca ?></strong>
                </div>
            </div>
        </div>

        <!--TABLE CARD-->
        <div class="card">
            <!-- CARD HEADER -->
            <div class="card-header">
                <span class="card-title">
                    <i class="fa-solid fa-list"></i>
                    Daftar Pesan
                    (<?= $totalPesan ?>)
                </span>
            </div>

            <!-- TABLE -->
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th width="40">No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Subjek</th>
                            <th>Pesan</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($data)): ?>
                        <tr>
                            <td colspan="8">
                                <div class="pesan-empty">
                                    <i class="fa-regular fa-envelope"></i>
                                    <h3>Belum Ada Pesan</h3>
                                    <p>Pesan dari website akan muncul di sini.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data as $i => $row): ?>
                            <tr>
                                <!-- NO -->
                                <td class="td-no">
                                    <?= $i + 1 ?>
                                </td>

                                <!-- NAMA -->
                                <td>
                                    <div class="message-name">
                                        <?= htmlspecialchars(
                                            $row['nama']
                                        ) ?>
                                    </div>
                                </td>

                                <!-- EMAIL -->
                                <td>
                                    <div class="message-email">
                                        <?= htmlspecialchars(
                                            $row['email']
                                        ) ?>
                                    </div>
                                </td>

                                <!-- SUBJEK -->
                                <td>
                                    <div class="message-subject">
                                        <?= htmlspecialchars(
                                            $row['subjek']
                                        ) ?>
                                    </div>
                                </td>

                                <!-- PESAN -->
                                <td>
                                    <div class="message-text">
                                        <?= nl2br(
                                            htmlspecialchars(
                                                $row['pesan']
                                            )
                                        ) ?>
                                    </div>
                                </td>

                                <!-- TANGGAL -->
                                <td>
                                    <div class="message-date">
                                        <?= date('d M Y', strtotime($row['tanggal_kirim'])) ?>
                                        <br>
                                        <?= date('H:i', strtotime($row['tanggal_kirim'])) ?>
                                        WIB
                                    </div>
                                </td>

                                <!-- STATUS -->
                                <td>
                                    <?php if (
                                        $row['status']
                                        === 'Sudah Dibaca'
                                    ): ?>
                                        <span class="message-badge read">
                                            <i class="fa-solid fa-check"></i>
                                            Sudah Diterima
                                        </span>
                                    <?php else: ?>
                                        <span class="message-badge unread">
                                            <i class="fa-solid fa-envelope"></i>
                                            Belum Diterima
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- AKSI -->
                                <td>
                                    <?php if (
                                        $row['status']
                                        !== 'Sudah Dibaca'
                                    ): ?>
                                        <form method="POST" onsubmit="return confirm('Apakah pesan ini sudah diterima?');">
                                            <input type="hidden" name="id_kontak" value="<?= $row['id_kontak'] ?>">
                                            <button type="submit" name="terima_pesan" class="btn-terima-pesan">
                                                <i class="fa-solid fa-check"></i>
                                                Terima
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="pesan-diterima">
                                            <i class="fa-solid fa-circle-check"></i>
                                            Diterima
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</body>
</html>
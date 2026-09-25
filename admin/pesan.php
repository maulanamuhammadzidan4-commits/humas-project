<?php
session_start();

require_once '../backend/connection.php';
require_once 'includes/auth.php';
require_once 'backend/pesan_data.php';

$page_title = "Pesan Masuk";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Masuk — Admin Humas SMK</title>
    <!-- GOOGLE FONT -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- CSS ADMIN -->
    <link rel="stylesheet" href="assets/admin-style.css">
</head>


<body>


<?php include 'includes/sidebar.php'; ?>


<div class="admin-main">


    <?php include 'includes/header.php'; ?>


    <main class="admin-content">


        <!-- =================================================
             HEADER HALAMAN
        ================================================= -->

        <div class="page-header">

            <div class="page-header-left">

                <h2>

                    <i
                        class="fa-solid fa-envelope"
                        style="color:var(--blue);margin-right:8px;"
                    ></i>

                    Pesan Masuk

                </h2>

                <p>

                    Kelola pesan yang dikirim melalui formulir kontak website.

                </p>

            </div>

        </div>



        <!-- =================================================
             STATISTIK
        ================================================= -->

        <div class="message-stats">


            <!-- TOTAL -->

            <div class="message-stat">

                <div class="message-stat-icon">

                    <i class="fa-solid fa-envelope"></i>

                </div>

                <div class="message-stat-info">

                    <span>
                        Total Pesan
                    </span>

                    <strong>
                        <?= $totalPesan ?>
                    </strong>

                </div>

            </div>



            <!-- SUDAH -->

            <div class="message-stat">

                <div class="message-stat-icon">

                    <i class="fa-solid fa-envelope-open"></i>

                </div>

                <div class="message-stat-info">

                    <span>
                        Sudah Diterima
                    </span>

                    <strong>
                        <?= $sudahDibaca ?>
                    </strong>

                </div>

            </div>



            <!-- BELUM -->

            <div class="message-stat">

                <div class="message-stat-icon">

                    <i class="fa-solid fa-bell"></i>

                </div>

                <div class="message-stat-info">

                    <span>
                        Belum Diterima
                    </span>

                    <strong>
                        <?= $belumDibaca ?>
                    </strong>

                </div>

            </div>


        </div>



        <!-- =================================================
             TABLE CARD
        ================================================= -->

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

                            <th width="40">
                                No
                            </th>

                            <th>
                                Nama
                            </th>

                            <th>
                                Email
                            </th>

                            <th>
                                Subjek
                            </th>

                            <th>
                                Pesan
                            </th>

                            <th>
                                Tanggal
                            </th>

                            <th>
                                Status
                            </th>

                            <th width="120">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php if (empty($data)): ?>


                        <tr>

                            <td colspan="8">

                                <div class="pesan-empty">

                                    <i
                                        class="fa-regular fa-envelope"
                                    ></i>

                                    <h3>
                                        Belum Ada Pesan
                                    </h3>

                                    <p>
                                        Pesan dari website akan muncul di sini.
                                    </p>

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
                                        <form method="POST" action="backend/pesan_handler.php" onsubmit="return confirm('Apakah pesan ini sudah diterima?');">
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/admin.js"></script>
</body>
</html>
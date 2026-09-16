<?php
require_once __DIR__ . '/../../backend/connection.php';

$data_loker = mysqli_query($koneksi, "SELECT l.*, p.nama AS nama_perusahaan FROM lowongan_kerja l LEFT JOIN perusahaan p ON l.id_perusahaan = p.id ORDER BY l.id DESC");
?>

<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
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
            <?php
            if (mysqli_num_rows($data_loker) > 0):
                $no = 1;
                while ($row = mysqli_fetch_assoc($data_loker)):
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td class="fw-bold"><?= htmlspecialchars($row['posisi']) ?></td>
                    <td><?= htmlspecialchars($row['nama_perusahaan'] ?? '-') ?></td>
                    <td><?= intval($row['kuota']) ?></td>
                    <td><?= date('d M Y', strtotime($row['batas_daftar'])) ?></td>
                    <td>
                        <span class="badge <?= ($row['status_loker'] === 'Buka') ? 'bg-success' : 'bg-secondary' ?>">
                            <?= htmlspecialchars($row['status_loker']) ?>
                        </span>
                    </td>
                    <td>
                        <a href="form/lowongan-kerja-form.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-warning"><i class="fa-solid fa-pen"></i></a>
                        <a href="../backend/form/lowongan_kerja/proses-hapus.php?id=<?= $row['id'] ?>" onclick="return confirm('Hapus data?');" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></a>
                    </td>
                </tr>
            <?php 
                endwhile;
            else:
            ?>
                <tr><td colspan="7" class="text-center text-muted py-3">Belum ada data lowongan kerja.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
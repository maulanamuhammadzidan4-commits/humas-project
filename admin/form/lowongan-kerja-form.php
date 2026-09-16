<?php
require_once "../../backend/connection.php";

$id = intval($_GET['id'] ?? 0);
$is_edit = $id > 0;
$data = [];

if ($is_edit) {
    $data = find_by_id('lowongan_kerja', $id);
    if (!$data) {
        $_SESSION['flash_error'] = "Data lowongan kerja tidak ditemukan!";
        header("Location: ../lowongan_kerja.php");
        exit();
    }
}

// Ambil data perusahaan untuk dropdown relasi ERD
$perusahaan_list = mysqli_query($koneksi, "SELECT id, nama, sektor_bidang FROM perusahaan ORDER BY nama ASC");

$page_title = $is_edit ? "Edit Lowongan Kerja" : "Tambah Lowongan Kerja";
require_once "../components/header.php";
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h3 class="fw-bold mb-0">
                <i class="fa-solid <?= $is_edit ? 'fa-pen-to-square text-warning' : 'fa-briefcase text-success' ?> me-2"></i>
                <?= $is_edit ? "Edit Lowongan Kerja" : "Tambah Lowongan Kerja Baru" ?>
            </h3>
            <a href="../lowongan_kerja.php" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <?php require_once "../components/alerts.php"; ?>

        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form action="../../backend/form/lowongan_kerja/<?= $is_edit ? 'proses-edit.php' : 'proses-tambah.php' ?>" method="POST">
                    <?php if ($is_edit): ?>
                        <input type="hidden" name="id" value="<?= $data['id'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="id_perusahaan" class="form-label">Perusahaan Penyedia Lowongan <span class="text-danger">*</span></label>
                        <select class="form-select" id="id_perusahaan" name="id_perusahaan" required>
                            <option value="">-- Pilih Perusahaan Mitra --</option>
                            <?php while ($p = mysqli_fetch_assoc($perusahaan_list)): ?>
                                <option value="<?= $p['id'] ?>" <?= (($data['id_perusahaan'] ?? 0) == $p['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($p['nama']) ?> (<?= htmlspecialchars($p['sektor_bidang']) ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <small class="text-muted">Perusahaan belum terdaftar? <a href="perusahaan-form.php" target="_blank">Tambah perusahaan baru di sini</a></small>
                    </div>

                    <div class="mb-3">
                        <label for="posisi" class="form-label">Posisi / Jabatan Pekerjaan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="posisi" name="posisi" required placeholder="Contoh: Junior Web Developer, Teknisi Jaringan..." value="<?= htmlspecialchars($data['posisi'] ?? '') ?>">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="kuota" class="form-label">Kuota Penerimaan (Orang) <span class="text-danger">*</span></label>
                            <input type="number" min="1" class="form-control" id="kuota" name="kuota" required placeholder="Contoh: 3" value="<?= htmlspecialchars($data['kuota'] ?? 1) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="batas_daftar" class="form-label">Batas Akhir Pendaftaran <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="batas_daftar" name="batas_daftar" required value="<?= htmlspecialchars($data['batas_daftar'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="status_loker" class="form-label">Status Lowongan <span class="text-danger">*</span></label>
                        <select class="form-select" id="status_loker" name="status_loker" required>
                            <option value="Buka" <?= (($data['status_loker'] ?? 'Buka') === 'Buka') ? 'selected' : '' ?>>Buka (Menerima Pelamar)</option>
                            <option value="Tutup" <?= (($data['status_loker'] ?? '') === 'Tutup') ? 'selected' : '' ?>>Tutup (Pendaftaran Berakhir)</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="deskripsi" class="form-label">Deskripsi Pekerjaan & Kualifikasi <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="5" required placeholder="Tuliskan persyaratan, kualifikasi jurusan, deskripsi tugas, dan benefit..."><?= htmlspecialchars($data['deskripsi'] ?? '') ?></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="../lowongan_kerja.php" class="btn btn-light">Batal</a>
                        <button type="submit" name="submit" class="btn <?= $is_edit ? 'btn-warning' : 'btn-success' ?> px-4">
                            <i class="fa-solid fa-save me-1"></i> <?= $is_edit ? "Simpan Perubahan" : "Simpan Lowongan" ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once "../components/footer.php"; ?>

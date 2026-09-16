<?php
require_once "../../backend/connection.php";

$id = intval($_GET['id'] ?? 0);
$is_edit = $id > 0;
$data = [];

if ($is_edit) {
    $data = find_by_id('penempatan_pkl', $id);
    if (!$data) {
        $_SESSION['flash_error'] = "Data penempatan PKL tidak ditemukan!";
        header("Location: ../penempatan_pkl.php");
        exit();
    }
}

// Ambil list Siswa dan Perusahaan untuk foreign keys
$siswa_list = mysqli_query($koneksi, "SELECT id, nisn, nama, kelas FROM siswa ORDER BY nama ASC");
$perusahaan_list = mysqli_query($koneksi, "SELECT id, nama FROM perusahaan ORDER BY nama ASC");

$page_title = $is_edit ? "Edit Penempatan PKL" : "Tambah Penempatan PKL";
require_once "../components/header.php";
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h3 class="fw-bold mb-0">
                <i class="fa-solid <?= $is_edit ? 'fa-pen-to-square text-warning' : 'fa-id-card-clip text-warning' ?> me-2"></i>
                <?= $is_edit ? "Edit Penempatan PKL" : "Tambah Penempatan PKL Baru" ?>
            </h3>
            <a href="../penempatan_pkl.php" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <?php require_once "../components/alerts.php"; ?>

        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form action="../../backend/form/penempatan_pkl/<?= $is_edit ? 'proses-edit.php' : 'proses-tambah.php' ?>" method="POST">
                    <?php if ($is_edit): ?>
                        <input type="hidden" name="id" value="<?= $data['id'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="id_siswa" class="form-label">Siswa PKL <span class="text-danger">*</span></label>
                        <select class="form-select" id="id_siswa" name="id_siswa" required>
                            <option value="">-- Pilih Siswa --</option>
                            <?php while ($s = mysqli_fetch_assoc($siswa_list)): ?>
                                <option value="<?= $s['id'] ?>" <?= (($data['id_siswa'] ?? 0) == $s['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($s['nama']) ?> (NISN: <?= htmlspecialchars($s['nisn']) ?> - Kelas: <?= htmlspecialchars($s['kelas']) ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="id_perusahaan" class="form-label">Perusahaan Mitra Tempat PKL <span class="text-danger">*</span></label>
                        <select class="form-select" id="id_perusahaan" name="id_perusahaan" required>
                            <option value="">-- Pilih Perusahaan --</option>
                            <?php while ($p = mysqli_fetch_assoc($perusahaan_list)): ?>
                                <option value="<?= $p['id'] ?>" <?= (($data['id_perusahaan'] ?? 0) == $p['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($p['nama']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="pembimbing" class="form-label">Guru / Instruktur Pembimbing <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="pembimbing" name="pembimbing" required placeholder="Contoh: Bpk. Budi Santoso, S.Kom" value="<?= htmlspecialchars($data['pembimbing'] ?? '') ?>">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_mulai" class="form-label">Tanggal Mulai PKL <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" required value="<?= htmlspecialchars($data['tanggal_mulai'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_selesai" class="form-label">Tanggal Selesai PKL <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" required value="<?= htmlspecialchars($data['tanggal_selesai'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="status_penempatan" class="form-label">Status Penempatan <span class="text-danger">*</span></label>
                        <select class="form-select" id="status_penempatan" name="status_penempatan" required>
                            <option value="Draft" <?= (($data['status_penempatan'] ?? 'Draft') === 'Draft') ? 'selected' : '' ?>>Draft (Pengajuan Awal)</option>
                            <option value="Disetujui" <?= (($data['status_penempatan'] ?? '') === 'Disetujui') ? 'selected' : '' ?>>Disetujui (Sedang Berjalan)</option>
                            <option value="Selesai" <?= (($data['status_penempatan'] ?? '') === 'Selesai') ? 'selected' : '' ?>>Selesai (Telah Tuntas PKL)</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="../penempatan_pkl.php" class="btn btn-light">Batal</a>
                        <button type="submit" name="submit" class="btn <?= $is_edit ? 'btn-warning' : 'btn-warning text-dark' ?> px-4">
                            <i class="fa-solid fa-save me-1"></i> <?= $is_edit ? "Simpan Perubahan" : "Simpan Penempatan PKL" ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once "../components/footer.php"; ?>

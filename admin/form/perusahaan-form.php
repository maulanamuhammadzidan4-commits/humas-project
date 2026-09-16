<?php
require_once "../../backend/connection.php";

$id = intval($_GET['id'] ?? 0);
$is_edit = $id > 0;
$data = [];

if ($is_edit) {
    $data = find_by_id('perusahaan', $id);
    if (!$data) {
        $_SESSION['flash_error'] = "Data perusahaan tidak ditemukan!";
        header("Location: ../perusahaan.php");
        exit();
    }
}

$page_title = $is_edit ? "Edit Perusahaan" : "Tambah Perusahaan";
require_once "../components/header.php";
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h3 class="fw-bold mb-0">
                <i class="fa-solid <?= $is_edit ? 'fa-pen-to-square text-warning' : 'fa-building text-primary' ?> me-2"></i>
                <?= $is_edit ? "Edit Data Mitra Perusahaan" : "Tambah Mitra Perusahaan Baru" ?>
            </h3>
            <a href="../perusahaan.php" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <?php require_once "../components/alerts.php"; ?>

        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form action="../../backend/form/perusahaan/<?= $is_edit ? 'proses-edit.php' : 'proses-tambah.php' ?>" method="POST">
                    <?php if ($is_edit): ?>
                        <input type="hidden" name="id" value="<?= $data['id'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Perusahaan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama" name="nama" required placeholder="Contoh: PT Telkom Indonesia" value="<?= htmlspecialchars($data['nama'] ?? '') ?>">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="sektor_bidang" class="form-label">Sektor / Bidang Usaha <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="sektor_bidang" name="sektor_bidang" required placeholder="Contoh: Teknologi Informasi / Jaringan" value="<?= htmlspecialchars($data['sektor_bidang'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="penanggung_jawab" class="form-label">Penanggung Jawab (PIC) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="penanggung_jawab" name="penanggung_jawab" required placeholder="Contoh: Bpk. Hendra Pratama" value="<?= htmlspecialchars($data['penanggung_jawab'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email Perusahaan <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required placeholder="hrd@perusahaan.com" value="<?= htmlspecialchars($data['email'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="no_telepon" class="form-label">Nomor Telepon / WhatsApp <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="no_telepon" name="no_telepon" required placeholder="08123456789" value="<?= htmlspecialchars($data['no_telepon'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat Kantor / Operasional <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="3" required placeholder="Alamat lengkap jalan, kota/kabupaten..."><?= htmlspecialchars($data['alamat'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="status_mou" class="form-label">Status Kerja Sama (MoU) <span class="text-danger">*</span></label>
                        <select class="form-select" id="status_mou" name="status_mou" required>
                            <option value="Proses" <?= (($data['status_mou'] ?? 'Proses') === 'Proses') ? 'selected' : '' ?>>Proses (Dalam Penjajakan / Pengajuan)</option>
                            <option value="Aktif" <?= (($data['status_mou'] ?? '') === 'Aktif') ? 'selected' : '' ?>>Aktif (MoU Masih Berlaku)</option>
                            <option value="Kadaluarsa" <?= (($data['status_mou'] ?? '') === 'Kadaluarsa') ? 'selected' : '' ?>>Kadaluarsa (Perlu Pembaruan)</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="../perusahaan.php" class="btn btn-light">Batal</a>
                        <button type="submit" name="submit" class="btn <?= $is_edit ? 'btn-warning' : 'btn-primary' ?> px-4">
                            <i class="fa-solid fa-save me-1"></i> <?= $is_edit ? "Simpan Perubahan" : "Tambah Perusahaan" ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once "../components/footer.php"; ?>

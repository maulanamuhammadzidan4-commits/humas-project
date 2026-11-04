<?php
session_start();
require_once '../backend/connection.php';
require_once '../backend/repositories/bootstrap.php';
require_once 'includes/auth.php';

$search = is_string($_GET['search'] ?? null) ? trim($_GET['search']) : '';
$requestedPage = filter_var($_GET['page'] ?? '1', FILTER_VALIDATE_INT);
$page = $requestedPage !== false && $requestedPage > 0 ? $requestedPage : 1;
$limit = 10;

$total = PerusahaanMouRepository::countWithSearch($koneksi, $search);
$totalPages = max(1, (int)ceil($total / $limit));
$page = min($page, $totalPages);
$offset = ($page - 1) * $limit;
$data = PerusahaanMouRepository::getPaginated($koneksi, $search, $limit, $offset);
$companyOptions = PerusahaanRepository::getOptions($koneksi);
$page_title = 'Status MoU';
$flashMessage = is_string($_GET['msg'] ?? null) ? $_GET['msg'] : '';
$flashType = is_string($_GET['type'] ?? null) ? $_GET['type'] : 'success';
if (!in_array($flashType, ['success', 'danger', 'warning'], true)) {
    $flashType = 'success';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status MoU — Admin Humas SMK</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/admin-style.css">
</head>
<body>
<?php include 'includes/sidebar.php'; ?>
<div class="admin-main">
    <?php include 'includes/header.php'; ?>
    <main class="admin-content">
        <?php if ($flashMessage !== ''): ?>
            <div class="alert alert-<?= htmlspecialchars($flashType, ENT_QUOTES, 'UTF-8') ?> flash-alert">
                <i class="fa-solid <?= $flashType === 'success' ? 'fa-circle-check' : 'fa-triangle-exclamation' ?>"></i>
                <?= htmlspecialchars($flashMessage, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <div class="page-header">
            <div class="page-header-left">
                <h2><i class="fa-solid fa-file-signature" style="color:var(--blue);margin-right:8px;"></i>Status MoU</h2>
                <p>Kelola status dan dokumen MoU perusahaan mitra</p>
            </div>
        </div>

        <div class="card" style="margin-bottom:1.5rem;">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-pen-to-square"></i> Perbarui Status dan Dokumen</span>
            </div>
            <div class="card-body">
                <?php if (empty($companyOptions)): ?>
                    <p>Belum ada perusahaan. Tambahkan perusahaan terlebih dahulu di halaman Perusahaan Mitra.</p>
                <?php else: ?>
                    <form method="POST" action="backend/status_mou_handler.php" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="save">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generateCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="perusahaan_id">Perusahaan <span class="required">*</span></label>
                                <select class="form-control" id="perusahaan_id" name="perusahaan_id" required>
                                    <option value="">Pilih perusahaan</option>
                                    <?php foreach ($companyOptions as $company): ?>
                                        <option value="<?= (int)$company['id'] ?>"><?= htmlspecialchars($company['nama_perusahaan'], ENT_QUOTES, 'UTF-8') ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="status">Status MoU <span class="required">*</span></label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="Proses">Proses</option>
                                    <option value="Aktif">Aktif</option>
                                    <option value="Kadaluarsa">Kadaluarsa</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" style="margin-top:1rem;">
                            <label for="mou_file">Dokumen MoU <span style="font-weight:400;color:var(--slate-600);">(opsional)</span></label>
                            <input class="form-control" type="file" id="mou_file" name="mou_file" accept=".pdf,.jpg,.jpeg,.png,.webp,application/pdf,image/jpeg,image/png,image/webp">
                            <small style="color:var(--slate-600);">Format PDF, JPEG, PNG, atau WebP. Maksimal 5 MB. Kosongkan jika hanya mengubah status.</small>
                        </div>
                        <div style="display:flex;justify-content:flex-end;margin-top:1rem;">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-floppy-disk"></i> Simpan Status
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-list"></i> Daftar MoU (<?= $total ?>)</span>
                <form method="GET" style="display:flex;gap:.5rem;align-items:center;">
                    <div class="search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="search" maxlength="255" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>" placeholder="Cari perusahaan...">
                    </div>
                    <button type="submit" class="btn btn-outline btn-sm" aria-label="Cari"><i class="fa-solid fa-search"></i></button>
                    <?php if ($search !== ''): ?>
                        <a href="status_mou.php" class="btn btn-outline btn-sm" aria-label="Hapus pencarian"><i class="fa-solid fa-xmark"></i></a>
                    <?php endif; ?>
                </form>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Perusahaan</th>
                            <th>Sektor Bidang</th>
                            <th>Status MoU</th>
                            <th>Dokumen</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($data)): ?>
                        <tr>
                            <td colspan="5">
                                <div class="table-empty">
                                    <i class="fa-solid fa-file-circle-xmark"></i>
                                    <p><?= $search !== '' ? 'Perusahaan tidak ditemukan.' : 'Belum ada data perusahaan.' ?></p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data as $index => $row):
                            $badge = match ($row['status']) {
                                'Aktif' => 'badge-green',
                                'Kadaluarsa' => 'badge-red',
                                default => 'badge-gold',
                            };
                            $mimeLabel = match ($row['mime_type'] ?? null) {
                                'application/pdf' => 'PDF',
                                'image/jpeg' => 'JPEG',
                                'image/png' => 'PNG',
                                'image/webp' => 'WebP',
                                default => 'Tipe tidak diketahui',
                            };
                            $fileSize = filter_var($row['file_size'] ?? null, FILTER_VALIDATE_INT);
                            $fileSizeLabel = $fileSize === false || $fileSize < 0
                                ? null
                                : ($fileSize < 1048576
                                    ? number_format($fileSize / 1024, 1, ',', '.') . ' KB'
                                    : number_format($fileSize / 1048576, 1, ',', '.') . ' MB');
                        ?>
                            <tr>
                                <td class="td-no"><?= $offset + $index + 1 ?></td>
                                <td style="font-weight:600;"><?= htmlspecialchars($row['nama_perusahaan'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($row['sektor_bidang'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><span class="badge <?= $badge ?>"><?= htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8') ?></span></td>
                                <td>
                                    <?php if (is_string($row['file_path']) && $row['file_path'] !== ''): ?>
                                        <div style="display:flex;flex-direction:column;align-items:flex-start;gap:.35rem;">
                                            <a class="btn btn-outline btn-sm"
                                               href="backend/status_mou_handler.php?action=download&perusahaan_id=<?= (int)$row['id'] ?>">
                                                <i class="fa-solid fa-download"></i>
                                                <?= htmlspecialchars($row['original_filename'] ?: 'Unduh dokumen', ENT_QUOTES, 'UTF-8') ?>
                                            </a>
                                            <small style="color:var(--slate-600);">
                                                <?= htmlspecialchars($mimeLabel, ENT_QUOTES, 'UTF-8') ?>
                                                <?php if ($fileSizeLabel !== null): ?> · <?= htmlspecialchars($fileSizeLabel, ENT_QUOTES, 'UTF-8') ?><?php endif; ?>
                                            </small>
                                        </div>
                                    <?php else: ?>
                                        <span style="color:var(--slate-600);">Dokumen belum tersedia.</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <span class="pagination-info">
                        Menampilkan <?= $offset + 1 ?>–<?= min($offset + $limit, $total) ?> dari <?= $total ?> data
                    </span>
                    <div class="pagination-btns">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>" class="page-btn" aria-label="Halaman sebelumnya"><i class="fa-solid fa-chevron-left"></i></a>
                        <?php endif; ?>
                        <?php for ($number = max(1, $page - 2); $number <= min($totalPages, $page + 2); $number++): ?>
                            <a href="?page=<?= $number ?>&search=<?= urlencode($search) ?>" class="page-btn <?= $number === $page ? 'active' : '' ?>"><?= $number ?></a>
                        <?php endfor; ?>
                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>" class="page-btn" aria-label="Halaman berikutnya"><i class="fa-solid fa-chevron-right"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>
</body>
</html>

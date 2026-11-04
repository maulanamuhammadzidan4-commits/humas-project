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
    <style>
        .mou-upload {
            position: relative;
            display: block;
            padding: 1.25rem;
            border: 1.5px dashed var(--slate-200);
            border-radius: var(--radius);
            background: var(--slate-100);
            text-align: center;
            cursor: pointer;
            transition: border-color .2s, background .2s;
        }

        .mou-upload:hover,
        .mou-upload:focus-within {
            border-color: var(--blue);
            background: var(--blue-light);
        }

        .mou-upload-icon {
            display: grid;
            place-items: center;
            width: 42px;
            height: 42px;
            margin: 0 auto .65rem;
            border-radius: 12px;
            background: white;
            color: var(--blue);
            font-size: 1.1rem;
        }

        .mou-upload-title {
            color: var(--navy);
            font-weight: 700;
        }

        .mou-upload-hint,
        .mou-file-meta {
            display: block;
            margin-top: .3rem;
            color: var(--slate-600);
            font-size: .78rem;
        }

        .mou-file-input {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        .mou-preview {
            margin-top: .85rem;
            padding: .75rem;
            border: 1px solid var(--slate-200);
            border-radius: var(--radius);
            background: white;
        }

        .mou-preview[hidden] {
            display: none;
        }

        .mou-preview img {
            display: block;
            width: 100%;
            max-height: 260px;
            object-fit: contain;
            border-radius: 8px;
            background: var(--slate-100);
        }

        .mou-pdf-preview {
            display: flex;
            align-items: center;
            gap: .75rem;
            color: var(--navy);
            font-weight: 700;
        }

        .mou-pdf-preview i {
            color: var(--red);
            font-size: 1.6rem;
        }
    </style>
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
            <?php if (!empty($companyOptions)): ?>
                <button class="btn btn-primary" type="button" onclick="openModal('modalMou')">
                    <i class="fa-solid fa-plus"></i> Input Status MoU
                </button>
            <?php endif; ?>
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

<?php if (!empty($companyOptions)): ?>
<div class="modal-overlay" id="modalMou" onclick="if (event.target === this) closeMouModal()">
    <div class="modal modal-lg">
        <div class="modal-header">
            <span class="modal-title"><i class="fa-solid fa-file-signature"></i> Input Status MoU</span>
            <button class="modal-close" type="button" onclick="closeMouModal()" aria-label="Tutup">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form method="POST" action="backend/status_mou_handler.php" enctype="multipart/form-data">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generateCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">
            <div class="modal-body">
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
                <div class="form-group">
                    <label for="mou_file">Dokumen MoU <span style="font-weight:400;color:var(--slate-600);">(opsional)</span></label>
                    <label class="mou-upload" for="mou_file">
                        <input class="mou-file-input" type="file" id="mou_file" name="mou_file"
                               accept=".pdf,.jpg,.jpeg,.png,.webp,application/pdf,image/jpeg,image/png,image/webp"
                               aria-describedby="mouFileHint">
                        <span class="mou-upload-icon"><i class="fa-solid fa-cloud-arrow-up"></i></span>
                        <span class="mou-upload-title">Pilih dokumen MoU</span>
                        <span class="mou-upload-hint" id="mouFileHint">PDF, JPEG, PNG, atau WebP · Maksimal 5 MB</span>
                        <span class="mou-file-meta" id="mouFileMeta">Belum ada file dipilih</span>
                    </label>
                    <div class="mou-preview" id="mouPreview" hidden>
                        <img id="mouPreviewImage" alt="Pratinjau gambar dokumen MoU" hidden>
                        <div class="mou-pdf-preview" id="mouPdfPreview" hidden>
                            <i class="fa-solid fa-file-pdf"></i>
                            <span>Dokumen PDF dipilih. Pratinjau tersedia setelah dokumen diunduh.</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" type="button" onclick="closeMouModal()">Batal</button>
                <button class="btn btn-primary" type="submit">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Status
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<script src="assets/admin.js"></script>
<script>
    let mouPreviewUrl = null;
    const mouFileInput = document.getElementById('mou_file');

    function clearMouPreview() {
        if (mouPreviewUrl !== null) {
            URL.revokeObjectURL(mouPreviewUrl);
            mouPreviewUrl = null;
        }
        mouFileInput.value = '';
        document.getElementById('mouFileMeta').textContent = 'Belum ada file dipilih';
        document.getElementById('mouPreview').hidden = true;
        document.getElementById('mouPreviewImage').hidden = true;
        document.getElementById('mouPreviewImage').removeAttribute('src');
        document.getElementById('mouPdfPreview').hidden = true;
    }

    function closeMouModal() {
        closeModal('modalMou');
        document.getElementById('modalMou').querySelector('form').reset();
        clearMouPreview();
    }

    if (mouFileInput) {
        mouFileInput.addEventListener('change', () => {
            const file = mouFileInput.files?.[0];
            const preview = document.getElementById('mouPreview');
            const previewImage = document.getElementById('mouPreviewImage');
            const pdfPreview = document.getElementById('mouPdfPreview');

            if (mouPreviewUrl !== null) {
                URL.revokeObjectURL(mouPreviewUrl);
                mouPreviewUrl = null;
            }
            preview.hidden = true;
            previewImage.hidden = true;
            previewImage.removeAttribute('src');
            pdfPreview.hidden = true;

            if (!file) {
                document.getElementById('mouFileMeta').textContent = 'Belum ada file dipilih';
                return;
            }

            const fileSize = file.size < 1048576
                ? `${(file.size / 1024).toLocaleString('id-ID', { maximumFractionDigits: 1 })} KB`
                : `${(file.size / 1048576).toLocaleString('id-ID', { maximumFractionDigits: 1 })} MB`;
            document.getElementById('mouFileMeta').textContent = `${file.name} · ${fileSize}`;

            if (['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
                mouPreviewUrl = URL.createObjectURL(file);
                previewImage.src = mouPreviewUrl;
                previewImage.hidden = false;
                preview.hidden = false;
            } else if (file.type === 'application/pdf') {
                pdfPreview.hidden = false;
                preview.hidden = false;
            }
        });
    }
</script>
</body>
</html>

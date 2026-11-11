<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/../../backend/connection.php';
require_once __DIR__ . '/../../backend/repositories/bootstrap.php';
require_once __DIR__ . '/../includes/auth.php';

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

try {

    // Format ekspor
    $format = strtolower(trim($_GET['format'] ?? 'xlsx'));

    $formatDiizinkan = ['xlsx', 'csv', 'pdf'];

    if (!in_array($format, $formatDiizinkan, true)) {
        http_response_code(400);
        exit('Format ekspor tidak valid.');
    }

    // Mengambil seluruh data PKL
    $data = PklRepository::getAllForExport($koneksi);

    // Nama file
    $namaFile = 'Data_Penempatan_PKL_' . date('Ymd_His');


    /*
    |--------------------------------------------------------------------------
    | KOLOM DATA EKSPOR
    |--------------------------------------------------------------------------
    */

    $headers = [
        'No',
        'NISN',
        'Nama Siswa',
        'Nama Perusahaan / Instansi',
        'Alamat Penempatan PKL',
        'Guru Pembimbing',
        'Tanggal Mulai',
        'Tanggal Selesai',
        'Status Penempatan'
    ];


    /*
    |--------------------------------------------------------------------------
    | FORMAT DATA
    |--------------------------------------------------------------------------
    */

    $rows = [];

    foreach ($data as $index => $row) {

        $rows[] = [
            $index + 1,
            $row['nisn'] ?? '-',
            $row['nama_siswa'] ?? '-',
            $row['nama_perusahaan'] ?? '-',
            $row['alamat_penempatan'] ?? '-',
            $row['pembimbing'] ?? '-',
            !empty($row['tanggal_mulai'])
                ? date('d-m-Y', strtotime($row['tanggal_mulai']))
                : '-',
            !empty($row['tanggal_selesai'])
                ? date('d-m-Y', strtotime($row['tanggal_selesai']))
                : '-',
            $row['status_penempatan'] ?? '-'
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | EKSPOR CSV
    |--------------------------------------------------------------------------
    */

    if ($format === 'csv') {

        header('Content-Type: text/csv; charset=UTF-8');

        header(
            'Content-Disposition: attachment; filename="' .
            $namaFile . '.csv"'
        );

        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // BOM agar karakter Indonesia terbaca baik di Excel
        fwrite($output, "\xEF\xBB\xBF");

        fputcsv($output, $headers, ';');

        foreach ($rows as $row) {
            fputcsv($output, $row, ';');
        }

        fclose($output);
        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | EKSPOR EXCEL
    |--------------------------------------------------------------------------
    */

    if ($format === 'xlsx') {

        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('Data PKL');

        // Judul
        $sheet->setCellValue('A1', 'DATA PENEMPATAN PRAKTIK KERJA LAPANGAN');

        $sheet->mergeCells('A1:I1');

        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getFont()->setSize(14);

        $sheet->getStyle('A1')->getAlignment()
            ->setHorizontal('center');

        // Header tabel
        foreach ($headers as $index => $header) {

            $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(
                $index + 1
            );

            $sheet->setCellValue($column . '3', $header);
        }

        // Data
        $baris = 4;

        foreach ($rows as $row) {

            foreach ($row as $index => $value) {

                $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(
                    $index + 1
                );

               $sheet->setCellValueExplicit(
    $column . $baris,
    (string) $value,
    DataType::TYPE_STRING
);
            }

            $baris++;
        }

        // Styling header
        $sheet->getStyle('A3:I3')->getFont()->setBold(true);

        $sheet->getStyle('A3:I3')->getAlignment()
            ->setHorizontal('center')
            ->setVertical('center')
            ->setWrapText(true);

        // Border tabel
        $barisAkhir = max(3, $baris - 1);

        $sheet->getStyle('A3:I' . $barisAkhir)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(
                \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
            );

        // Lebar kolom otomatis
        foreach (range('A', 'I') as $column) {
            $sheet->getColumnDimension($column)
                ->setAutoSize(true);
        }

        // Tinggi header
        $sheet->getRowDimension(3)->setRowHeight(35);

        // Filter tabel
        $sheet->setAutoFilter('A3:I' . $barisAkhir);

        // Freeze header
        $sheet->freezePane('A4');

        // Download
        header(
            'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );

        header(
            'Content-Disposition: attachment; filename="' .
            $namaFile . '.xlsx"'
        );

        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);

        $writer->save('php://output');

        $spreadsheet->disconnectWorksheets();

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | EKSPOR PDF MELALUI CETAK BROWSER
    |--------------------------------------------------------------------------
    */

    if ($format === 'pdf') {

        header('Content-Type: text/html; charset=UTF-8');

        ?>

        <!DOCTYPE html>
        <html lang="id">

        <head>

            <meta charset="UTF-8">

            <meta name="viewport" content="width=device-width, initial-scale=1.0">

            <title>Data Penempatan PKL</title>

            <style>

                body {
                    font-family: Arial, sans-serif;
                    margin: 20px;
                    color: #222;
                }

                h2 {
                    text-align: center;
                    margin-bottom: 5px;
                }

                .subtitle {
                    text-align: center;
                    margin-bottom: 20px;
                    font-size: 12px;
                }

                .info {
                    margin-bottom: 15px;
                    font-size: 12px;
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                    font-size: 10px;
                }

                th,
                td {
                    border: 1px solid #333;
                    padding: 6px;
                    text-align: left;
                    vertical-align: top;
                }

                th {
                    background: #e9eef5;
                    text-align: center;
                }

                .footer {
                    margin-top: 20px;
                    font-size: 11px;
                    text-align: right;
                }

                .print-button {
                    padding: 10px 18px;
                    border: none;
                    background: #1d4ed8;
                    color: white;
                    cursor: pointer;
                    margin-bottom: 15px;
                    border-radius: 5px;
                }

                @media print {

                    body {
                        margin: 10mm;
                    }

                    .print-button {
                        display: none;
                    }

                    @page {
                        size: landscape;
                        margin: 10mm;
                    }

                    thead {
                        display: table-header-group;
                    }

                    tr {
                        page-break-inside: avoid;
                    }

                }

            </style>

        </head>

        <body>

            <button
                class="print-button"
                onclick="window.print()"
            >
                Cetak / Simpan PDF
            </button>

            <h2>DATA PENEMPATAN PRAKTIK KERJA LAPANGAN</h2>

            <div class="subtitle">
                Data Penempatan PKL Siswa
            </div>

            <div class="info">
                Tanggal ekspor:
                <?= htmlspecialchars(date('d-m-Y H:i:s')) ?>
                <br>
                Jumlah peserta:
                <?= count($rows) ?>
            </div>

            <table>

                <thead>

                    <tr>

                        <?php foreach ($headers as $header): ?>

                            <th>
                                <?= htmlspecialchars($header) ?>
                            </th>

                        <?php endforeach; ?>

                    </tr>

                </thead>

                <tbody>

                    <?php if (empty($rows)): ?>

                        <tr>
                            <td colspan="9" style="text-align:center;">
                                Belum ada data penempatan PKL.
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php foreach ($rows as $row): ?>

                            <tr>

                                <?php foreach ($row as $value): ?>

                                    <td>
                                        <?= htmlspecialchars((string) $value) ?>
                                    </td>

                                <?php endforeach; ?>

                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </tbody>

            </table>

            <div class="footer">
                Dokumen Data Penempatan PKL
            </div>

        </body>

        </html>

        <?php

        exit;
    }

} catch (Throwable $e) {

    error_log('PKL Export Error: ' . $e->getMessage());

    http_response_code(500);

    exit(
        'Ekspor data PKL gagal. Periksa konfigurasi database dan log PHP.'
    );
}
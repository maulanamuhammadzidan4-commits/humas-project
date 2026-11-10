
<?php

session_start();

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../../vendor/autoload.php';

$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

$sheet = $spreadsheet->getActiveSheet();

$sheet->setTitle('Data Perusahaan');

$headers = [
    'Nama Perusahaan',
    'Sektor Bidang',
    'Jurusan',
    'Alamat',
    'Penanggung Jawab',
    'No. Telepon',
    'Status MOU'
];

$sheet->fromArray($headers, null, 'A1');

// Format header.
$sheet->getStyle('A1:G1')->getFont()->setBold(true);

$sheet->getStyle('A1:G1')->getAlignment()
    ->setHorizontal(
        \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
    );

// Lebar kolom otomatis.
foreach (range('A', 'G') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// Membuat filter.
$sheet->setAutoFilter('A1:G1');

// Download Excel.
$filename = 'Template_Import_Status_MOU.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

header(
    'Content-Disposition: attachment; filename="' .
    $filename . '"'
);

header('Cache-Control: max-age=0');

$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

$writer->save('php://output');

$spreadsheet->disconnectWorksheets();

exit;

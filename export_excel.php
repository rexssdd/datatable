<?php
require 'db.php';
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Create Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set Header Row
$sheet->setCellValue('A1', 'Full Name');
$sheet->setCellValue('B1', 'Email');
$sheet->setCellValue('C1', 'Address');
$sheet->setCellValue('D1', 'Age');
$sheet->setCellValue('E1', 'Date of Birth');
$sheet->setCellValue('F1', 'Contact Number');

// Make header bold
$sheet->getStyle('A1:F1')->getFont()->setBold(true);

// Fetch Data
$stmt = $pdo->query("SELECT full_name,email,address,age,dob,contact_number FROM users");

$rowNumber = 2;

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $sheet->setCellValue('A' . $rowNumber, $row['full_name']);
    $sheet->setCellValue('B' . $rowNumber, $row['email']);
    $sheet->setCellValue('C' . $rowNumber, $row['address']);
    $sheet->setCellValue('D' . $rowNumber, $row['age']);
    $sheet->setCellValue('E' . $rowNumber, $row['dob']);
    $sheet->setCellValue('F' . $rowNumber, $row['contact_number']);
    $rowNumber++;
}

// Auto-size columns
foreach(range('A','F') as $col){
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// File Name
$filename = "users_" . date('Y-m-d_H-i-s') . ".xlsx";

// Headers for download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

// Write File
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;

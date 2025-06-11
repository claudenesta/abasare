
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../lib/db_function.php'; // Corrected path
require '../vendor/autoload.php'; // Corrected path

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Report');

// Set header row
$sheet->setCellValue('A1', 'No');
$sheet->setCellValue('B1', 'Names');
$sheet->setCellValue('C1', 'Job Title');
$sheet->setCellValue('D1', 'Civil status');
$sheet->setCellValue('E1', 'Phone / Cell');
$sheet->setCellValue('F1', 'Join Date');

$query = "SELECT id, CONCAT(fname, ' ', lname) AS names, job_title, civil_status, phone_cell, rdate FROM member WHERE status='" . $_GET['stat'] . "'";
$rows = returnAllData($db, $query);

$rowNumber = 2;
foreach ($rows as $row) {
    $sheet->setCellValue('A' . $rowNumber, $row['id']);
    $sheet->setCellValue('B' . $rowNumber, $row['names']);
    $sheet->setCellValue('C' . $rowNumber, $row['job_title']);
    $sheet->setCellValue('D' . $rowNumber, $row['civil_status']);
    $sheet->setCellValue('E' . $rowNumber, $row['phone_cell']);
    $sheet->setCellValue('F' . $rowNumber, $row['rdate']);
    $rowNumber++;
}

$writer = new Xlsx($spreadsheet);
$filename = 'report.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
$writer->save('php://output');
exit();
?>
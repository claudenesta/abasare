<?php
require_once '../lib/db_function.php'; // Corrected path
require '../vendor/autoload.php'; // Corrected path
use TCPDF;

class MYPDF extends TCPDF {
    public function Header() {
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(0, 15, 'Report', 0, 1, 'C');
    }
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('Report');
$pdf->SetSubject('Report');
$pdf->SetKeywords('TCPDF, PDF, report');

$pdf->setPrintHeader(true);
$pdf->setPrintFooter(false);
$pdf->AddPage();

$pdf->SetFont('helvetica', '', 10);

$html = '<table border="1" cellpadding="4">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Names</th>
                    <th>Job Title</th>
                    <th>Civil status</th>
                    <th>Phone / Cell</th>
                    <th>Join Date</th>
                </tr>
            </thead>
            <tbody>';

$query = "SELECT id, CONCAT(fname, ' ', lname) AS names, job_title, civil_status, phone_cell, rdate FROM member WHERE status='" . $_GET['stat'] . "'";
$rows = returnAllData($db, $query);

foreach ($rows as $row) {
    $html .= '<tr>
                <td>' . $row['id'] . '</td>
                <td>' . $row['names'] . '</td>
                <td>' . $row['job_title'] . '</td>
                <td>' . $row['civil_status'] . '</td>
                <td>' . $row['phone_cell'] . '</td>
                <td>' . $row['rdate'] . '</td>
              </tr>';
}

$html .= '</tbody></table>';

$pdf->writeHTML($html, true, false, true, false, '');

$pdf->Output('report.pdf', 'D');
exit();
?>

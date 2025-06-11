<?php
require('../fpdf/fpdf.php');
include('../DBController.php');

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Get the selected year from the query parameter or default to the current year
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');

// Test database connection
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Create a new PDF instance
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);

// Add title
$pdf->Cell(0, 10, "Savings Report for $year", 0, 1, 'C');
$pdf->Ln(10);

// Add table headers
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 10, 'No', 1);
$pdf->Cell(60, 10, "Member's Name", 1);

for ($month = 1; $month <= 12; $month++) {
    $monthName = DateTime::createFromFormat('!m', $month)->format('M');
    $pdf->Cell(20, 10, $monthName, 1);
}

$pdf->Cell(30, 10, 'Total', 1);
$pdf->Ln();

// Fetch members
$membersQuery = "SELECT id, CONCAT(fname, ' ', lname) AS fullname FROM member";
$membersResult = mysqli_query($con, $membersQuery);

if (!$membersResult) {
    die("Error fetching members: " . mysqli_error($con));
}

// Check if there are members
if (mysqli_num_rows($membersResult) == 0) {
    $pdf->Cell(0, 10, "No data found for the selected year.", 1, 1, 'C');
    $pdf->Output('D', "savings_report_$year.pdf");
    exit;
}

// Initialize row counter
$i = 1;

// Initialize totals array
$totals = array_fill(1, 12, 0);
$grandTotal = 0;

// Loop through members and fetch their savings data
while ($member = mysqli_fetch_assoc($membersResult)) {
    $pdf->Cell(10, 10, $i++, 1);
    $pdf->Cell(60, 10, $member['fullname'], 1);

    $memberTotal = 0;

    for ($month = 1; $month <= 12; $month++) {
        $savingsQuery = "
            SELECT SUM(sav_amount) AS amount
            FROM saving
            WHERE member_id = " . $member['id'] . " 
            AND year = $year 
            AND month = $month
        ";
        $savingsResult = mysqli_query($con, $savingsQuery);

        if (!$savingsResult) {
            die("Error fetching savings: " . mysqli_error($con));
        }

        $savings = mysqli_fetch_assoc($savingsResult);
        $amount = isset($savings['amount']) ? $savings['amount'] : 0;

        // Add to totals
        $totals[$month] += $amount;
        $memberTotal += $amount;

        $pdf->Cell(20, 10, number_format($amount, 2), 1);
    }

    // Add member total
    $grandTotal += $memberTotal;
    $pdf->Cell(30, 10, number_format($memberTotal, 2), 1);
    $pdf->Ln();
}

// Add totals row
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(70, 10, 'Total', 1);
for ($month = 1; $month <= 12; $month++) {
    $pdf->Cell(20, 10, number_format($totals[$month], 2), 1);
}
$pdf->Cell(30, 10, number_format($grandTotal, 2), 1);
$pdf->Ln();

// Output the PDF
$pdf->Output('D', "savings_report_$year.pdf");
?>
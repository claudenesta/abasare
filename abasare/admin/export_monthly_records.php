<?php
include('../DBController.php');

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$output = '';

// Get the selected year from the query parameter or default to the current year
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');

// Add headers for the Excel file
$output .= '
    <table border="1" cellspacing="0" cellpadding="3">
        <tr>
            <td colspan="20" style="font-size: 16px; font-weight: bold; border: 0px solid #000;">&nbsp;Abasare Group</td>
        </tr>
        <tr>
            <td colspan="20" style="font-size: 16px; font-weight: bold; border: 0px solid #000;">&nbsp;' . (new DateTime())->format('jS F Y') . '</td>
        </tr>
        <tr>
            <td colspan="20" style="text-align: center; font-size: 16px; border: 0px solid #000; font-weight: bold;">
                Monthly Detailed loan Report for ' . $year . '
            </td>
        </tr>
        <tr>
            <th style="text-align: center; font-size: 16px">N<sup><u>0</u></sup></th>
            <th style="text-align: center; font-size: 16px">Loan ID</th>
            <th style="text-align: center; font-size: 16px">Member Name</th>
            <th style="text-align: center; font-size: 16px">Loan Amount Taken</th>
            <th style="text-align: center; font-size: 16px">Monthly Installment</th>
            <th style="text-align: center; font-size: 16px">Loan Approval Date</th>
            <th style="text-align: center; font-size: 16px">Loan Balance</th>
            <th style="text-align: center; font-size: 16px">Loan Status</th>
            <th style="text-align: center; font-size: 16px">Before ' . $year . '</th>
';

// Dynamically add columns for each month of the selected year
for ($month = 1; $month <= 12; $month++) {
    $monthName = DateTime::createFromFormat('!m', $month)->format('F');
    $output .= '<th style="text-align: center; font-size: 16px">' . $monthName . ' ' . $year . '</th>';
}

$output .= '</tr>';

// SQL query to fetch records data
$query = "
    SELECT 
        a.id AS loanID,
        CONCAT(b.fname, ' ', COALESCE(b.lname, '')) AS memberName,
        a.loan_amount AS loanAmount,
        a.loan_amount_term AS monthlyInstallment,
        a.loan_date AS loanApprovalDate,
        (a.loan_amount - IFNULL(SUM(c.amount), 0)) AS loanBalance,
        a.status AS loanStatus,
        SUM(CASE WHEN YEAR(c.payment_sched) < $year THEN c.amount ELSE 0 END) AS beforeYear,
";

// Dynamically add columns for each month
for ($month = 1; $month <= 12; $month++) {
    $query .= "SUM(CASE WHEN YEAR(c.payment_sched) = $year AND MONTH(c.payment_sched) = $month THEN c.amount ELSE 0 END) AS month_$month, ";
}

$query = rtrim($query, ', '); // Remove the trailing comma
$query .= "
    FROM member_loans AS a
    INNER JOIN member AS b ON a.member_id = b.id
    LEFT JOIN lend_payments AS c ON a.id = c.borrower_loan_id
    WHERE a.reject = 0 AND a.status = 'ACTIVE' -- Include only active loans
    GROUP BY a.id
    ORDER BY b.fname ASC, b.lname ASC
";

try {
    // Prepare and execute the query
    $statement = $db->prepare($query);
    $statement->execute();
    $records_info = $statement->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

// Check if data is empty
if (empty($records_info)) {
    die("No data found for the report.");
}

// Initialize row counter
$i = 1;

// Loop through the records data and generate rows
foreach ($records_info as $record) {
    $output .= '<tr>';
    $output .= '<td>' . $i++ . '</td>';
    $output .= '<td>' . htmlspecialchars($record['loanID']) . '</td>';
    $output .= '<td>' . htmlspecialchars($record['memberName']) . '</td>';
    $output .= '<td style="text-align: right;">' . number_format($record['loanAmount'], 2) . '</td>';
    $output .= '<td style="text-align: right;">' . number_format($record['monthlyInstallment'], 2) . '</td>';
    $output .= '<td>' . htmlspecialchars($record['loanApprovalDate']) . '</td>';
    $output .= '<td style="text-align: right;">' . number_format($record['loanBalance'], 2) . '</td>';
    $output .= '<td>' . htmlspecialchars($record['loanStatus']) . '</td>';
    $output .= '<td style="text-align: right;">' . number_format($record['beforeYear'], 2) . '</td>';

    // Add data for each month
    for ($month = 1; $month <= 12; $month++) {
        $output .= '<td style="text-align: right;">' . number_format($record["month_$month"], 2) . '</td>';
    }

    $output .= '</tr>';
}

// Close the table
$output .= '</table>';

// Set headers for Excel file download
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=records_report_' . $year . '.xls");

// Output the Excel file
echo $output;
?>
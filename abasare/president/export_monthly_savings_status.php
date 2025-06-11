<?php
include('../DBController.php');

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Get the selected year from the query parameter or default to the current year
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');

// Add headers for the Excel file
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=savings_report_$year.xls");

$output = '';

// Add the table headers
$output .= '
    <table border="1" cellspacing="0" cellpadding="3">
        <tr>
            <td colspan="15" style="font-size: 16px; font-weight: bold; border: 0px solid #000;">&nbsp;Abasare Group</td>
        </tr>
        <tr>
            <td colspan="15" style="font-size: 16px; font-weight: bold; border: 0px solid #000;">&nbsp;' . (new DateTime())->format('jS F Y') . '</td>
        </tr>
        <tr>
            <td colspan="15" style="text-align: center; font-size: 16px; border: 0px solid #000; font-weight: bold;">
                Savings Report for ' . $year . '
            </td>
        </tr>
        <tr>
            <th style="text-align: center; font-size: 16px">No</th>
            <th style="text-align: center; font-size: 16px">Member\'s Name</th>
';

// Dynamically add columns for each month
for ($month = 1; $month <= 12; $month++) {
    $monthName = DateTime::createFromFormat('!m', $month)->format('F');
    $output .= '<th style="text-align: center; font-size: 16px">' . $monthName . '</th>';
}

$output .= '</tr>';

// Fetch members
$membersQuery = "SELECT id, CONCAT(fname, ' ', lname) AS fullname FROM member";
$membersResult = mysqli_query($con, $membersQuery);

if (!$membersResult) {
    die("Error fetching members: " . mysqli_error($con));
}

// Initialize row counter
$i = 1;

// Initialize totals array
$totals = array_fill(1, 12, 0);

// Loop through members and fetch their savings data
while ($member = mysqli_fetch_assoc($membersResult)) {
    $output .= '<tr>';
    $output .= '<td>' . $i++ . '</td>';
    $output .= '<td>' . htmlspecialchars($member['fullname']) . '</td>';

    // Fetch savings for each month
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

        $output .= '<td style="text-align: right;">' . number_format($amount, 2) . '</td>';
    }

    $output .= '</tr>';
}

// Add totals row
$output .= '<tr>';
$output .= '<td colspan="2" style="text-align: right; font-weight: bold;">Total</td>';
for ($month = 1; $month <= 12; $month++) {
    $output .= '<td style="text-align: right; font-weight: bold;">' . number_format($totals[$month], 2) . '</td>';
}
$output .= '</tr>';

// Close the table
$output .= '</table>';

// Output the Excel file
echo $output;
?>
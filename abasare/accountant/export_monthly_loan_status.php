<?php
include('../DBController.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();

$output = '';

$output .= '
    <table border="1" cellspacing="0" cellpadding="3">
        <tr>
            <td colspan="7" style="font-size: 16px; font-weight: bold; border: 0px solid #000;">&nbsp;Abasare Group</td>
        </tr>
        <tr>
            <td colspan="7" style="font-size: 16px; font-weight: bold; border: 0px solid #000;">&nbsp;' . (new DateTime())->format('jS F Y') . ' </td>
        </tr>
        <tr>
            <td colspan="7" style="text-align: center; font-size: 16px; border: 0px solid #000; font-weight: bold;">
                Active Loans Status
            </td>
        </tr>
        <tr>
            <th style="text-align: center; font-size: 16px">N<sup><u>0</u></sup></th>
            <th style="text-align: center; font-size: 16px">Member Name</th>
            <th style="text-align: center; font-size: 16px">Type</th>
            <th style="text-align: center; font-size: 16px">Date</th>
            <th style="text-align: center; font-size: 16px">Loan Amount</th>
            <th style="text-align: center; font-size: 16px">Paid</th>
            <th style="text-align: center; font-size: 16px">Unpaid Balance</th>
        </tr>
';

$query = "
    SELECT  CONCAT(b.fname, ' ', COALESCE(b.lname, '')) AS memberName,
            SUM(a.loan_amount) AS totalLoan,
            SUM(c.paidAmount) AS paidAmount,
            d.lname AS loanType,
            a.loan_date
    FROM member_loans AS a
    INNER JOIN member AS b ON a.member_id = b.id
    LEFT JOIN (
        SELECT  a.borrower_loan_id,
                SUM(amount) AS paidAmount
        FROM lend_payments AS a
        INNER JOIN member_loans AS b ON a.borrower_loan_id = b.id
        WHERE b.status = ?
        GROUP BY a.borrower_loan_id
    ) AS c ON a.id = c.borrower_loan_id
    INNER JOIN loan_type AS d ON a.loan_id = d.id
    WHERE a.reject = 0 AND a.status = ?
    GROUP BY b.id, a.loan_id
    ORDER BY loanType ASC, a.loan_date ASC, memberName ASC
";

try {
    $statement = $db->prepare($query);
    $statement->execute(['ACTIVE', 'ACTIVE']);
    $loans_info = $statement->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Error fetching loan data: " . $e->getMessage());
}

$i = 1;
$total_loans = 0;
$total_payments = 0;
$total_balance = 0;

foreach ($loans_info as $loan) {
    $pending = $loan['totalLoan'] - $loan['paidAmount'];
    $total_loans += $loan['totalLoan'];
    $total_payments += $loan['paidAmount'];
    $total_balance += $pending;

    $output .= '<tr>';
    $output .= '<td>' . $i++ . '</td>';
    $output .= '<td>' . $loan['memberName'] . '</td>';
    $output .= '<td>' . $loan['loanType'] . '</td>';
    $output .= '<td>' . $loan['loan_date'] . '</td>';
    $output .= '<td>' . number_format($loan['totalLoan']) . '</td>';
    $output .= '<td>' . number_format($loan['paidAmount']) . '</td>';
    $output .= '<td>' . number_format($pending) . '</td>';
    $output .= '</tr>';
}

$output .= '<tr>';
$output .= '<td colspan="4" style="text-align: right;">Total</td>';
$output .= '<td>' . number_format($total_loans) . '</td>';
$output .= '<td>' . number_format($total_payments) . '</td>';
$output .= '<td>' . number_format($total_balance) . '</td>';
$output .= '</tr>';
$output .= '</table>';

header("Content-Type: application/xls");
header("Content-Disposition: attachment; filename=monthly_loan_status.xls");
echo $output;

ob_end_flush();
?>

<?php
include('db_function.php'); 

// Initialize variables
$condition = [];
$monthly_query = "";
$additional_columns = "";
$balance_field = "a.loan_amount - (";

// Get the selected year from the query parameter
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');

// Start from January of the selected year
$month_object = new \DateTime($year . "-01-01");

// Initialize columns for the selected year
$columns = [];

for ($counter = 1; $counter <= 12; $counter++) {
    $keyword = $month_object->format('Y_m'); // Format as "2025_01", "2025_02", etc.
    $columns[$keyword] = $month_object->format('F Y'); // e.g., "January 2025"

    $monthly_query .= "LEFT JOIN (
                        SELECT a.id AS loan_id,
                               SUM(b.amount) AS amount,
                               b.status
                        FROM member_loans AS a
                        INNER JOIN lend_payments AS b
                        ON a.id = b.borrower_loan_id
                        WHERE b.payment_sched LIKE(?)
                        GROUP BY a.id
                      ) AS {$keyword}
                      ON a.id = {$keyword}.loan_id";
    $additional_columns .= "{$keyword}.amount AS paid_{$keyword}" . ($counter < 12 ? ", " : "");
    $balance_field .= "COALESCE(" . $keyword . ".amount, 0)" . ($counter < 12 ? " + " : "");
    $condition[] = $month_object->format('Y-m') . "%"; // Add condition for each month
    $month_object->modify("+1 month"); // Move to the next month
}
$balance_field .= ") AS loan_balance";

// Modify the query to filter loans approved in the selected year
$query = "SELECT a.id,
                 a.member_id,
                 a.status,
                 a.loan_amount,
                 a.loan_amount_term,
                 a.loan_date,
                 {$balance_field},
                 b.fname,
                 b.lname,{$additional_columns}
          FROM member_loans AS a
          INNER JOIN member AS b
          ON a.member_id = b.id
          LEFT JOIN lend_payments AS c
          ON a.id = c.borrower_loan_id
          {$monthly_query}
          WHERE a.reject = ? AND YEAR(a.loan_date) = ?
          GROUP BY a.id 
          ORDER BY a.status ASC, b.fname ASC, b.lname ASC";

// Add conditions for rejected loans and the selected year
$condition[] = 0; // Reject condition
$condition[] = $year; // Filter for the selected year

// Execute the query and fetch data
$data = returnAllData($db, $query, $condition);
?>

<div class="col-xs-12">
    <div class="table-responsive" data-pattern="priority-columns">
        <table class="table table-bordered table-hover" id="example1">
            <caption class="text-center">Monthly Loan Payment Report for <?= $year ?></caption>
            <thead style="background-color:orange">
                <tr>
                    <th>No</th>
                    <th>Loan ID</th>
                    <th>Members Name</th>
                    <th>Loan amount Taken</th>
                    <th>Monthly Installment</th>
                    <th>Loan Approval Date</th>
                    <th>Loan Balance</th>
                    <th>Loan Status</th>
                    <?php foreach ($columns as $date => $display): ?>
                        <th><?= $display ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data)): ?>
                    <?php $count = 1; ?>
                    <?php foreach ($data as $row): ?>
                        <tr>
                            <td><?= $count++ ?></td>
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['fname'] ?> <?= $row['lname'] ?></td>
                            <td style="text-align: right;"><?= number_format($row['loan_amount'] ?? 0) ?></td>
                            <td style="text-align: right;"><?= number_format($row['loan_amount_term'] ?? 0) ?></td>
                            <td><?= $row['loan_date'] ?></td>
                            <td style="text-align: right;"><?= number_format($row['loan_balance'] ?? 0) ?></td>
                            <td><?= $row['status'] ?></td>
                            <?php foreach ($columns as $date => $display): ?>
                                <td style="text-align: right;"><?= isset($row['paid_' . $date]) ? number_format($row['paid_' . $date]) : "" ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="<?= count($columns) + 8 ?>" style="text-align: center;">No data available for <?= $year ?>.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4">Total</td>
                    <td style="text-align: right;"></td>
                    <td style="text-align: right;"></td>
                    <td style="text-align: right;"></td>
                </tr>
            </tfoot>
        </table>
        <a href="./export_monthly_records.php?year=<?= $year ?>" target="_blank" class="btn btn-success">Download</a>
    </div>
</div>
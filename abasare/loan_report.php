<?php
include('db_function.php'); 

//Here get all posible columns
$condition = [];
$monthly_query = "";
$additional_columns = "";
$balance_field = "a.loan_amount - (";

$keyword = ($_GET['year'] - 1 )."_12_31";
$columns = [
$keyword => "Before ".$_GET['year'],
];
  $monthly_query .= "LEFT JOIN (
                                SELECT  a.id AS loan_id,
                                        SUM(b.amount) AS amount,
                                        b.status
                                        FROM member_loans AS a
                                        INNER JOIN lend_payments AS b
                                        ON a.id = b.borrower_loan_id
                                        WHERE b.payment_sched <= ?
                                        GROUP BY a.id
                              ) AS {$keyword}
                              ON a.id = {$keyword}.loan_id
                              ";
  $additional_columns .= "
                  {$keyword}.amount AS paid_{$keyword}, ";
  $condition[] = ($_GET['year'] - 1 )."-12-31";
  $balance_field .= "COALESCE(".$keyword.".amount,0) + ";
$month_object = new \DateTime($_GET['year']."-01-01");

for($counter = 1; $counter <= 12; $counter++){
  $keyword = $month_object->format('Y_m');
  $columns[$keyword] = $month_object->format('F Y');

  $monthly_query .= "LEFT JOIN (
                                SELECT  a.id AS loan_id,
                                        SUM(b.amount) AS amount,
                                        b.status
                                        FROM member_loans AS a
                                        INNER JOIN lend_payments AS b
                                        ON a.id = b.borrower_loan_id
                                        WHERE b.payment_sched LIKE(?)
                                        GROUP BY a.id
                              ) AS {$keyword}
                              ON a.id = {$keyword}.loan_id
                              ";
  $additional_columns .= "
                  {$keyword}.amount AS paid_{$keyword}".($counter < 12 ? ", ":"");
  $balance_field .= "COALESCE(".$keyword.".amount, 0)".($counter < 12 ? " + ":"");
  $condition[] = $month_object->format('Y-m')."%";
  $month_object->modify("+1 month");
}
$balance_field .= ") AS loan_balance";
$query = "SELECT  a.id,
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
                  WHERE a.reject = ? GROUP BY a.id ORDER BY a.status ASC, b.fname ASC, b.lname ASC";

// $condition[] = 'ACTIVE';
$condition[] = 0;
// echo $query;
// var_dump($condition); die();
// var_dump("<pre>", $query, $condition, "</pre>");
$data = returnAllData($db, $query, $condition);
?>


<div class="col-xs-12">
              <div class="table-responsive" data-pattern="priority-columns">
                              <table class="table table-bordered table-hover" id="example1">
                  <caption class="text-center">Monthly Loan Payment Report's <a href="#" target="_blank"> info</a>:</caption>
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
                      <?php
                        foreach($columns AS $date => $display){
                          ?>
                          <th><?= $display ?></th>
                          <?php
                        }
                      ?>

                     
                      </tr>
                  </thead>
                  
                  
                  <tbody>
                      <?php
                      $count = 1;
                      foreach($data AS $row){
                        // var_dump($row);
                        ?>
                        <tr>
                          <td><?= $count++ ?></td>
                          <td><?= $row['id'] ?></td>
                          <td><?= $row['fname'] ?> <?= $row['lname'] ?></td>
                          <td style="text-align: right;"><?= number_format($row['loan_amount']) ?></td>
                          <td style="text-align: right;"><?= number_format($row['loan_amount_term'])?></td>
                          <td><?= $row['loan_date'] ?></td>
                          <td style="text-align: right;"><?= number_format($row['loan_balance']) ?></td>
                          <td><?= $row['status'] ?></td>

                          <?php
                            foreach($columns AS $date => $display){
                              ?>
                              <td style="text-align: right;"><?= $row['paid_'.$date]?number_format($row['paid_'.$date]):"" ?></td>
                              <?php
                            }
                          ?>
                        </tr>
                        <?php
                      }
                      ?>
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

                
                <a href="./export_monthly_records.php?year=<?= $_GET['year'] ?>" target="_blank" class="btn btn-success">Download</a>
              </div>
            </div>
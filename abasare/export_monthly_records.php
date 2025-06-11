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

$output = '
          <table border="1" cellspacing="0" cellpadding="3">
            <tr>
                <td colspan="5" style="font-size: 16px; font-weight: bold; border: 0px solid #000;">&nbsp;Abasare Group</td>
            </tr>
            <tr>
                <td colspan="5" style="font-size: 16px; font-weight: bold; border: 0px solid #000;">&nbsp;'.(new DateTime())->format('jS F Y').' </td>
            </tr>
            <tr>
                <td colspan="5" style="text-align: center; font-size: 16px; border: 0px solid #000; font-weight: bold;">
                    Monthly Loan Payment Report
                </td>
            </tr>
            <tr>
              <th class="col-xs-1"  style="text-align: center; font-size: 16px">N<sup><u>0</u></sup></th>
              <th class="col-xs-1" style="text-align: center; font-size: 16px">Loan ID</th>
              <th class="col-xs-1" style="text-align: center; font-size: 16px">Member Name</th>
              <th class="col-xs-1" style="text-align: center; font-size: 16px">Loan Amount</th>
              <th class="col-xs-1" style="text-align: center; font-size: 16px">Installment</th>
              <th class="col-xs-1" style="text-align: center; font-size: 16px">Loan Date</th>
              <th class="col-xs-1" style="text-align: center; font-size: 16px">Loan Balance</th>
              <th class="col-xs-1" style="text-align: center; font-size: 16px">Status</th>
          ';

          foreach($columns AS $date => $display){
            $output .= '<th>'.$display.'</th>';
          }
          $output .= '</tr>';
          
          $count = 1;
          foreach($data AS $row){
            
            $output .= '<tr>';
              $output .= '<td>'.($count++).'</td>';
              $output .= '<td>'. $row['id'].'</td>';
              $output .= '<td>'. $row['fname'] .' '.$row['lname'].'</td>';
              $output .= '<td style="text-align: right;">'. number_format($row['loan_amount']).'</td>';
              $output .= '<td style="text-align: right;">'. number_format($row['loan_amount_term']).'</td>';
              $output .= '<td>'. $row['loan_date'].'</td>';
              $output .= '<td style="text-align: right;">'. number_format($row['loan_balance']).'</td>';
              $output .= '<td>'. $row['status'].'</td>';
                foreach($columns AS $date => $display){
                  
                  $output .= '<td style="text-align: right;">'. ($row['paid_'.$date]?number_format($row['paid_'.$date]):"").'</td>';
                  
                }
              
            $output .= '</tr>';
            
          }
          $output .= '</table>';

          header("Content-Type: application/xls");
    header("Content-Disposition: attachment; filename=monthly_loan_payment.xls");
    echo $output;
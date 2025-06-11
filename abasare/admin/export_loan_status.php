<?php 
include('../DBController.php');
error_reporting(E_ALL & ~E_NOTICE & E_WARNING & E_PARSE & E_ERROR);
$output = '';

$output .= '
        <table border="1" cellspacing="0" cellpadding="3">
            <tr>
                <td colspan="5" style="font-size: 16px; font-weight: bold; border: 0px solid #000;">&nbsp;Abasare Group</td>
            </tr>
            <tr>
                <td colspan="5" style="font-size: 16px; font-weight: bold; border: 0px solid #000;">&nbsp;'.(new DateTime())->format('jS F Y').' </td>
            </tr>
            <tr>
                <td colspan="5" style="text-align: center; font-size: 16px; border: 0px solid #000; font-weight: bold;">
                    Current Loan Status
                </td>
            </tr>
            <tr>
              <th class="col-xs-1"  style="text-align: center; font-size: 16px">N<sup><u>0</u></sup></th>
              <th class="col-xs-1" style="text-align: center; font-size: 16px">Member Name</th>
              <th class="col-xs-1" style="text-align: center; font-size: 16px">Total Loan Amount</th>
              <th class="col-xs-1" style="text-align: center; font-size: 16px">Total Paid</th>
              <th class="col-xs-1" style="text-align: center; font-size: 16px">Unpaid Balance</th>
            </tr>
        ';
        $query = "SELECT  CONCAT(b.fname, ' ', COALESCE(b.lname)) AS memberName,
                          SUM(a.loan_amount) AS totalLoan,
                          SUM(c.paidAmount) AS paidAmount
                          FROM member_loans AS a
                          INNER JOIN member AS b
                          ON a.member_id = b.id
                          LEFT JOIN (
                            SELECT  a.borrower_loan_id,
                                    SUM(amount) AS paidAmount
                                    FROM lend_payments AS a
                                    INNER JOIN member_loans AS b
                                    ON a.borrower_loan_id = b.id
                                    GROUP BY a.borrower_loan_id
                          ) AS c
                          ON a.id = c.borrower_loan_id
                          WHERE a.reject = 0
                          GROUP BY b.id
                          ORDER BY memberName ASC
                          ";
        try{
          // var_dump($query);
          $statement = $db->prepare($query);
          $statement->execute();
          
          $loans_info = $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch(Exception $e){
          throw new Exception($e->getMessage(), 1);
          
        }

        $i = 1;
        $total_loans = 0;
        $total_payments = 0;
        $total_balance = 0;

        foreach($loans_info AS $loan){
          	$pending = $loan['totalLoan'] - $loan['paidAmount'];
          	$total_loans += $loan['totalLoan'];
          	$total_payments += $loan['paidAmount'];
          	$total_balance += $pending;

          	$output .= '<tr>';
	          	$output .= '<td>';
	          		$output .= $i++;
	          	$output .= '</td>';
	          	$output .= '<td>';
	          		$output .= $loan['memberName'];
	          	$output .= '</td>';
	          	$output .= '<td>';
	          		$output .= number_format($loan['totalLoan']);
	          	$output .= '</td>';
	          	$output .= '<td>';
	          		$output .= number_format($loan['paidAmount']);
	          	$output .= '</td>';
	          	$output .= '<td>';
	          		$output .= number_format($pending);
	          	$output .= '</td>';
          	$output .= '</tr>';
        }
        $output .= '<tr>';
          	$output .= '<td colspan="2">Total</td>';
          	$output .= '<td>';
          		$output .= number_format($total_loans);
          	$output .= '</td>';
          	$output .= '<td>';
          		$output .= number_format($total_payments);
          	$output .= '</td>';
          	$output .= '<td>';
          		$output .= number_format($total_balance);
          	$output .= '</td>';
        $output .= '</tr>';
    $output .= '</table>';
    header("Content-Type: application/xls");
    header("Content-Disposition: attachment; filename=loan_status.xls");
    echo $output;




<?php
header("Content-Type:application/json");
require_once "../../lib/db_function.php";


if(empty($_POST['loan_id'])){
	echo json_encode(['status' => false, "message" => "Unable to approve loan request"]);
	return;
}


$db->beginTransaction();
try{

	$loan = first($db, "SELECT  a.id,
	                            a.loan_amount,
	                            b.fname,
	                            b.lname,
	                            c.lname AS loan_name,
	                            a.loan_date,
	                            c.interest,
	                            c.terms,
	                            c.frequency,
	                            c.late_fee,
	                            a.member_id,
	                            d.id AS topup_detail_id,
	                            d.toped_up_loan_id,
	                            d.total_recovered_amount,
	                            a.loan_amount_interest
	                            FROM member_loans AS a
	                            INNER JOIN member AS b
	                            ON a.member_id = b.id
	                            INNER JOIN loan_type AS c
	                            ON a.loan_id = c.id
	                            LEFT JOIN topup_details AS d
	                            ON a.id = d.loan_id
	                            WHERE a.id = ?
	                            ", [$_POST['loan_id']]);

	$contributions = returnAllData($db, "SELECT * 
							                    FROM (
							                        SELECT  a.sav_amount AS amount,
							                            a.year, a.month,
							                            CONCAT(a.year, '-', IF(a.month < 10, '0',''), a.month, '-01') AS contribution_month
							                            FROM saving AS a
							                            WHERE a.member_id = ?
							                            ORDER BY id DESC
							                    ) AS a
							                    WHERE a.contribution_month <= ?
							                    ORDER BY contribution_month DESC
							                    LIMIT 0,3
							                    ", $condi = [$loan['member_id'], (new \DateTime($loan['loan_date']))->format('Y-m-01')]);
	// var_dump("<pre>", $contributions, $condi, $loan['member_id']); die();
	$average = 0;
	foreach($contributions AS $single_contribution){
	  	$average += $single_contribution['amount'];
	}
	$installment = $loan['loan_amount']/$loan['terms'];
	$average /= 3;

	$savings = returnSingleField($db, "SELECT 	SUM(a.amount) AS saving_amount 
	                                          	FROM (
		                                            SELECT  a.sav_amount AS amount,
			                                                a.year, a.month,
			                                                CONCAT(a.year, '-', IF(a.month < 10, '0',''), a.month, '-01') AS contribution_month
			                                                FROM saving AS a
			                                                WHERE a.member_id = ?
			                                                ORDER BY id DESC
	                                          	) AS a
	                                          	WHERE a.contribution_month <= ?
	                                          	", "saving_amount", [$loan['member_id'], (new \DateTime($loan['loan_date']))->format('Y-m-01')]);

	$loan_limit = $savings*2.5;

	//create loan setting record
	saveData($db, "INSERT INTO member_loan_settings SET loan_id=?, member_loan_id=?, lname=?, interest=?, terms=?, frequency=?, late_fee=?, installment=?, saving=?", 
		[$_POST['loan_id'], $loan['member_id'], $loan['loan_name'], $loan['interest'], $loan['terms'], $loan['frequency'], $loan['late_fee'], $installment, $average]);

	//Create lend_payments records
	$first_payment_date = new \DateTime();
	if($_POST['is_emergency'] == 1 || $first_payment_date->format('j') < 15){
		$first_payment_date->modify("+1 month");
	} else {
		$first_payment_date->modify("+2 months");
	}

	//Get the last payment number recorded before
	$payment_number = returnSingleField($db, "SELECT payment_number FROM lend_payments ORDER BY payment_number DESC LIMIT 0,1", "payment_number");
	$next_payment_id = ++$payment_number;
	for ($counter=1; $count <= $loan['terms'] ; $count++) { 
		// Here create lend payment records lend_payments
		saveData($db, "INSERT INTO lend_payments SET borrower_id = ?, borrower_loan_id=?, payment_sched=?, payment_number=?, amount=?, status=?, overdue_fine=?", 
				[$loan['member_id'], $_POST['loan_id'], $first_payment_date->format('Y-m-d'), $payment_number++,  $installment, "UNPAID", 0]);

		$first_payment_date->modify("+1 month");
	}

	//activate the loan
	saveData($db, "UPDATE member_loans SET status=?, next_payment_id=?, president_status=?, president=?, president_date=? WHERE id = ?", ['ACTIVE', $next_payment_id, 1, $_SESSION['user']['id'], (new \DateTime())->format("Y-m-d") , $_POST['loan_id']]);

	// if the loan is topup make sure to cover old loan an make i
	if(!is_null($loan['topup_detail_id'])){
		//Get the first unpaid payment schedule
		$next_schedule = returnSingleField($db, "SELECT id FROM lend_payments WHERE borrower_loan_id = ? AND status = ? ORDER BY id ASC LIMIT 0, 1", "id", [$loan['id'], "UNPAID"]);
		saveData($db, "UPDATE topup_details SET recovered_from_id = ? WHERE id = ?", [$next_schedule, $loan['topup_detail_id']]);
		//Here make sure to find all lend payments which are unpaid related to the toped up loan
		saveData($db, "UPDATE lend_payments SET status = ?, rdate = ?, comment=? WHERE borrower_loan_id=?", [
			"PAID", (new \DateTime())->format("Y-m-d H:i:s"), "Covered by topup loan ".$loan['id'], $loan['toped_up_loan_id'],
		]);
	}

	//Create Interest record for the loan as the interest is not moved out from the account
	$loan_month = new \DateTime();
	saveData($db, "INSERT INTO interest SET member_id=?, amount=?, loan_id=?, loan_interest=?, loan_ref=?, desciption=?, month=?, year=?, ref_id=? ",[
		$loan['member_id'], $loan['loan_amount_interest'], $loan['id'], $loan['loan_amount_interest'], $loan['id'], "Loan interest", $loan_month->format("n"), $loan_month->format("Y"), $loan['id']
	]);
	$db->commit();
	echo json_encode(['status' => true, "message" => "Loan Request Approved" ]);
	return;
} catch(\Exception $e){
	$db->rollback();
	echo json_encode(['status' => false, "message" => $e->getMessage() ]);
	return;
}
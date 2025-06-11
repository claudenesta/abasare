<?php
header("Content-Type:application/json");
require_once "../../lib/db_function.php";

if(empty($_POST['loan_payment_id'])){
	echo json_encode(['status' => false, "message" => "Unable to request payment"]);
	return;
}

if(empty($_POST['amount'])){
	echo json_encode(['status' => false, "message" => "How much were paid"]);
	return;
}

if(empty($_POST['paid_at'])){
	echo json_encode(['status' => false, "message" => "when have you paid"]);
	return;
}

if(empty($_POST['month'])){
	echo json_encode(['status' => false, "message" => "unable to detect the loan configuration"]);
	return;
}

if(empty($_POST['year'])){
	echo json_encode(['status' => false, "message" => "unable to detect the loan configuration"]);
	return;
}

if(empty($_POST['ref_number'])){
	echo json_encode(['status' => false, "message" => "Please provide the bank slip information!"]);
	return;
}

if($_POST['fines'] > 0){

	if(empty($_POST['fine_amount'])){
		echo json_encode(['status' => false, "message" => "Fine amount is required"]);
		return;
	}

	if($_POST['fine_amount'] < $_POST['fines']){
		echo json_encode(['status' => false, "message" => "Invalid fine amount"]);
		return;
	}

	if(empty($_POST['fine_ref_number'])){
		echo json_encode(['status' => false, "message" => "Please provide the bank slip information for fines!"]);
		return;
	}
}

$db->beginTransaction();
try{
	//
	$payment_data = first($db, "SELECT 	a.*,
										b.id AS overdue_id,
										COALESCE(b.month, a.salary_month) AS month,
										COALESCE(b.year, a.salary_year) AS year,
										COALESCE(b.payment_overdue, a.payment_sched) AS payment_overdue
										FROM (
											SELECT 	a.id,
													a.payment_sched,
													a.payment_number,
													IF(a.amount > 0, a.amount, b.loan_amount_term) AS amount,
													a.status,
													a.rdate,
													a.overdue_fine,
													a.comment,
													c.lname AS loan_name,
													c.is_emergeny,
													d.next_payment_id,
													YEAR(a.payment_sched) AS salary_year,
													MONTH(a.payment_sched) AS salary_month,
													a.borrower_id,
													a.borrower_loan_id,
													b.loan_date
													FROM lend_payments AS a
													INNER JOIN member_loans AS b
													ON a.borrower_loan_id = b.id AND b.status = ?
													INNER JOIN loan_type AS c
													ON b.loan_id = c.id
													LEFT JOIN member_loans AS d
													ON a.payment_number = d.next_payment_id
													WHERE a.borrower_id = ?
													AND a.status IN(?,?)
													AND a.id = ?
													ORDER BY a.payment_sched
										) AS a
										LEFT JOIN overdue_settings AS b
										ON a.salary_year = b.year AND a.salary_month = b.month
										ORDER BY a.payment_sched ASC
										", ["ACTIVE", $_SESSION['acc'], "UNPAID", "PENDING", $_POST['loan_payment_id']]);
	$delays = 0;
	$fine_rate = 0;
	$overdue_check = null;
	$delays_days = 0;
	$now = new \DateTimeImmutable();
	
	$loan_date = new \DateTime();
	if($payment_data['payment_sched'] <= $payment_data['loan_date']){
		$converter = new \DateTime($payment_data['payment_sched']);
		$converter->modify("+1 month");
		$payment_data['payment_sched'] = $converter->format('Y-m-d');
	}
	if($payment_data['is_emergeny']){
		$overdue_check = new \DateTimeImmutable($payment_data['payment_sched']);
		if($overdue_check->getTimestamp() < $now->getTimestamp()){
			$checker = $overdue_check->diff($now);
			if($checker->format("%y") > 0){
				$delays += ($checker->format("%y")*12);
			}
			$delays += $checker->format("%m");
			if($checker->format("%d") > 0) {
				$delays += 1;
			}
			$delays_days = $checker->days;
			$color = "danger";
			$fine_rate = 5/100;
		}
	} else {
		if(!is_null($payment_data['payment_overdue'])){
			$overdue_check = new \DateTimeImmutable($payment_data['payment_overdue']);
			if($overdue_check->getTimestamp() < $now->getTimestamp()){
				$checker = $overdue_check->diff($now);
				if($checker->format("%y") > 0){
					$delays += ($checker->format("%y")*12);
				}
				$delays += $checker->format("%m");
				if($checker->format("%d") > 0) {
					$delays += 1;
				}
				$delays_days = $checker->days;
				$color = "danger";
				$fine_rate = 2/100;
			}
		}
	}

	$fines = 0;
	if($delays_days % 30 != 0){
		$delays++;
	}
	if($delays > 0){
		$fines = $delays * $payment_data['amount'] * $fine_rate;
	}
	if(is_array($_POST['payment_id_info'])) {
		$payment_info = [];
		for($counter=0; $counter < count($_POST['payment_id_info']); $counter++){
			$payment_info[] = [
				"id" => $_POST['payment_id_info'][$counter],
				"amount" => $_POST['payment_amount_info'][$counter],
				"fine" => $_POST['payment_fine_info'][$counter],
				"number" => $_POST['payment_id_number'][$counter],
			];
		}
	} else {
		$payment_info = null;
	}
	$has_fine = 0;
	$fine_data = NULL;
	if($delays > 0){
		$has_fine = 1;
		$fine_data = json_encode([
			"member_id" => $_SESSION['acc'],
			"amount" => $_POST['fine_amount'],
			"lend_payment_id" => $_POST['loan_payment_id'],
			"fine_overdue" => $_POST['fine_amount'],
			"desciption" => sprintf("%s loan payment overdue for %s day%s", $payment_data['loan_name'], $delays, ($delays > 1?"s":"") ),
			"month" => $_POST['month'],
			"year" => $_POST['year'],
			"referance_number" => $_POST['fine_ref_number']
		]);
	}
	$data = [
		"id" => $payment_data['id'],
		"borrower_id" => $payment_data['borrower_id'],
		"borrower_loan_id" => $payment_data['borrower_loan_id'],
		"payment_sched" => $payment_data['payment_sched'],
		"payment_number" => $payment_data['payment_number'],
		"amount" => $_POST['amount'],
		"rdate" => (new \DateTime())->format("Y-m-d H:i:s"),
		"overdue_fine" => $_POST['fines'],
		"payment_details" => $payment_info,
		"remainder" => $_POST['additional_payments'],
	];

	//Here create the request Operation
	saveData($db, "INSERT INTO bank_slip_requests SET member_id=?, type=?, ref_number=?, amount=?, data=?, paid_at=?, created_at=?, has_fine=?, fine_data=?", [$_SESSION['acc'], 'loan payment', $_POST['ref_number'], $_POST['amount'], json_encode($data), $_POST['paid_at'], (new \DateTime())->format("Y-m-d H:i:s"), $has_fine, $fine_data ]);

	//Update the lend_payment information
	saveData($db, "UPDATE lend_payments SET status=? WHERE id=?", ["PENDING", $_POST['loan_payment_id']]);

	$db->commit();
	echo json_encode(['status' => true, "message" => "The payment request is submitted, Please submit the hard copy to accountant", $fines ]);
	return;
} catch(\Exception $e){
	$db->rollback();
	echo json_encode(['status' => false, "message" => $e->getMessage() ]);
	return;
}

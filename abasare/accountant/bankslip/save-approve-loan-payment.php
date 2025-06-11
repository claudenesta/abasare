<?php
header("Content-Type:application/json");
require_once "../../lib/db_function.php";


if(empty($_POST['request_id'])){
	echo json_encode(['status' => false, "message" => "No request to accept"]);
	return;
}


$db->beginTransaction();
try{

	$request = first($db, "SELECT id, member_id, type, ref_number, amount, data, paid_at, has_fine, fine_data FROM bank_slip_requests WHERE id = ?", [$_POST['request_id']]);

	$data = json_decode($request['data']);

	//Now activate the loan payment and clasify the bank slip
	$comment = NULL;
	if($request['has_fine']){
		$comment = "this payment delayed";
	} else {
		$comment = "the payment made successfully.";
	}
	//Make sure to mark multiple lend_payments_once required
	$last_affected = NULL;
	foreach($data->payment_details AS $lend_info){
		if($lend_info->fine > 0){
			$comments = "this payment delayed";
		} else {
			$comments = "the payment made successfully.";
		}
		saveData($db, "UPDATE lend_payments SET amount=?, status=?, rdate=?, overdue_fine=?, comment=? WHERE id=?", [$lend_info->amount, "PAID", $data->rdate, $lend_info->fine, $comments, $lend_info->id]);
		$last_affected = $lend_info->number;
	}
	if(!is_null($last_affected)){
		$next_payment = returnSingleField($db, "SELECT payment_number FROM lend_payments WHERE payment_number > ? AND status = ? AND borrower_loan_id=? ORDER BY id ASC LIMIT 0, 1", "payment_number", [$last_affected, "UNPAID",$data->borrower_loan_id]);
		if(!is_null($next_payment)){
			saveData($db, "UPDATE member_loans SET next_payment_id = ? WHERE id=?", [$next_payment, $data->borrower_loan_id]);
		} else {
			//Here make sure to close the loans as there is no next payment
			saveData($db, "UPDATE member_loans SET status=? WHERE id=?", ["CLOSED", $data->borrower_loan_id]);
		}

		if($request['has_fine'] == 1){
			$fine_info = json_decode($request['fine_data']);

			//here make sure to record fine information
			saveData($db, "INSERT INTO interest SET member_id=?, amount=?, lend_payment_id=?, fine_overdue=?, ref_id=?, desciption=?, month=?, year=?, done_at=?", [$fine_info->member_id, $fine_info->amount, $fine_info->lend_payment_id, $fine_info->fine_overdue, $fine_info->lend_payment_id,$fine_info->desciption, $fine_info->month, $fine_info->year, $data->rdate]);
		}
	} else {
		//Remark the installement as unpaid
		saveData($db, "UPDATE lend_payments SET status=? WHERE payment_number=?", ["UNPAID", $data->payment_number]);
	}

	
	$remainder_info = returnSingleField($db, "SELECT id FROM loan_remainder WHERE loan_id = ?", "id", [$data->borrower_loan_id]);
	if($remainder_info){
		saveData($db, "UPDATE loan_remainder SET amount=? WHERE id =?", [$data->remainder, $remainder_info]);
	} else {
		saveData($db, "INSERT INTO loan_remainder SET loan_id=?, amount=?", [$data->borrower_loan_id, $data->remainder]);
	}

	//finaly update the transaction
	saveData($db, "UPDATE bank_slip_requests SET status=? WHERE id=?", ["Accepted", $_POST['request_id']] );

	$db->commit();
	echo json_encode(['status' => true, "message" => "Now the bankslip is well recorded" ]);
	return;
} catch(\Exception $e){
	$db->rollback();
	echo json_encode(['status' => false, "message" => $e->getMessage() ]);
	return;
}
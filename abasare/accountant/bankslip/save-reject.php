<?php
header("Content-Type:application/json");
require_once "../../lib/db_function.php";


if(empty($_POST['request_id'])){
	echo json_encode(['status' => false, "message" => "No request to accept"]);
	return;
}

if(empty($_POST['comment'])){
	echo json_encode(['status' => false, "message" => "Specify the cause of slip rejection here"]);
	return;
}

$db->beginTransaction();
try{
	//check if the request was made for loan payment and restore the status of the installment so that it can be retried again
	$request = first($db, "SELECT id, member_id, type, ref_number, amount, data, paid_at, has_fine, fine_data, created_at FROM bank_slip_requests WHERE id = ?", [$_POST['request_id']]);
	if($request['type'] == "loan payment"){
		$data = json_decode($request['data']);

		saveData($db, "UPDATE lend_payments SET status = ?, comment=? WHERE id=?", ["Rejected", $_POST['comment'], $data->id]);
	}

	saveData($db, "UPDATE bank_slip_requests SET ref_copy=?, ref_number=NULL, status=?, comment = ? WHERE id = ?", [$request['ref_number'], "Rejected", $_POST['comment'], $_POST['request_id']]);
	$db->commit();
	echo json_encode(['status' => true, "message" => "Bank slip recording process is aborted successfuly" ]);
	return;
} catch(\Exception $e){
	$db->rollback();
	echo json_encode(['status' => false, "message" => $e->getMessage() ]);
	return;
}

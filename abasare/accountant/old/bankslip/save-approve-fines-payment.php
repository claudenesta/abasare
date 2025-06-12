<?php
header("Content-Type:application/json");
require_once "../../lib/db_function.php";


if(empty($_POST['request_id'])){
	echo json_encode(['status' => false, "message" => "No request to accept"]);
	return;
}


$db->beginTransaction();
try{

	$request = first($db, "SELECT id, member_id, type, ref_number, amount, data, paid_at, has_fine, fine_data, created_at FROM bank_slip_requests WHERE id = ?", [$_POST['request_id']]);
	$data = json_decode($request['data']);

	saveData($db, "INSERT INTO interest SET member_id=?, amount=?, ref_id=?, desciption=?, month=?, year=?, done_at=?", [$data->member_id, $data->amount, $data->ref_id,$data->desciption, $data->month, $data->year, $request['created_at']]);
	//Update the fine information to be paid
	saveData($db, "UPDATE special_fines SET status=? WHERE id=?", ["Paid", $data->ref_id]);
	
	saveData($db, "UPDATE bank_slip_requests SET status=? WHERE id=?", ["Accepted", $_POST['request_id']] );

	$db->commit();
	echo json_encode(['status' => true, "message" => "Now the bankslip is well recorded" ]);
	return;
} catch(\Exception $e){

	$db->rollback();
	echo json_encode(['status' => false, "message" => $e->getMessage() ]);
	return;
}
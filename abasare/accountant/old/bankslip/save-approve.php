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

	$comment = "";
	if($data->fine > 0){
		$comment = sprintf("Fine applied due to %s day%s of saving delay", $data->days, ($data->days > 1?"s":""));
	}

	//create saving records
	$overdue_id = $data->overdue_id;
	// var_dump($overdue_id);
	if(!$overdue_id){
		$overdue_id = NULL;
	}
	$fine = $data->fine;
	// var_dump($fine);
	if(is_null($fine)){
		$fine = 0;
	}
	$saving_id = saveAndReturnID($db, "INSERT INTO saving SET member_id =?, overdue_id=?, sav_amount=?, month=?, year=?, fine=?, comment=?, done_at=?", [$request['member_id'], $overdue_id, $data->sav_amount, $data->month, $data->year, $fine , $comment, (new \DateTime())->format("Y-m-d H:i:s")]);

	if($request['has_fine'] == 1){
		$fine_info = json_decode($request['fine_data']);
		saveData($db, "INSERT INTO interest SET member_id=?, amount=?, saving_id=?, saving_overdu=?, ref_id=?, desciption=?, month=?, year=?, done_at=?", [$fine_info->member_id, $fine_info->amount, $saving_id, $fine_info->saving_overdu, $saving_id, $fine_info->desciption, $fine_info->month, $fine_info->year, $request['created_at']]);
	}

	//Update request Operation
	saveData($db, "UPDATE bank_slip_requests SET status = ? WHERE id = ?", ["Accepted", $_POST['request_id']]);

	$db->commit();
	echo json_encode(['status' => true, "message" => "Now the bankslip is well recorded" ]);
	return;
} catch(\Exception $e){
	$db->rollback();
	echo json_encode(['status' => false, "message" => $e->getMessage() ]);
	return;
}
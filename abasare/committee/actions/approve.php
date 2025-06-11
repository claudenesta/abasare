<?php
header("Content-Type:application/json");
require_once "../../lib/db_function.php";


if(empty($_POST['loan_id'])){
	echo json_encode(['status' => false, "message" => "Unable to approve loan request"]);
	return;
}

if($_POST['is_emergency'] == 1){
	if(empty($_POST['approved_amount']) || $_POST['approved_amount'] <= 0){
		echo json_encode(['status' => false, "message" => "Invalid Allowed amount"]);
		return;
	}
}


$db->beginTransaction();
try{
	saveData($db, "UPDATE member_loans SET committee_status=?, committee=?, committee_date=? WHERE id = ?", [1, $_SESSION['user']['id'], (new \DateTime())->format("Y-m-d") , $_POST['loan_id']]);

	if($_POST['is_emergency'] == 1){
		//Here make sure to create required records in emergency details table
		saveData($db, "INSERT INTO emergency_details SET loan_id=?, approved_amount=?", [$_POST['loan_id'], $_POST['approved_amount']]);
	}

	$db->commit();
	echo json_encode(['status' => true, "message" => "Loan Request Approved" ]);
	return;
} catch(\Exception $e){
	$db->rollback();
	echo json_encode(['status' => false, "message" => $e->getMessage() ]);
	return;
}
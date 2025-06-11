<?php
header("Content-Type:application/json");
require_once "../../lib/db_function.php";


if(empty($_POST['loan_id'])){
	echo json_encode(['status' => false, "message" => "Unable to approve loan request"]);
	return;
}
if(empty($_POST['comment'])){
	echo json_encode(['status' => false, "message" => "Please provide comment"]);
	return;
}


$db->beginTransaction();
try{
	saveData($db, "UPDATE member_loans SET status=?, committee_status=?, committee=?, committee_date=?, committee_reject_comment=? WHERE id = ?", ["REJECTED", 0, $_SESSION['user']['id'], (new \DateTime())->format("Y-m-d"), $_POST['comment'] , $_POST['loan_id']]);

	$db->commit();
	echo json_encode(['status' => true, "message" => "Loan Request rejected" ]);
	return;
} catch(\Exception $e){
	$db->rollback();
	echo json_encode(['status' => false, "message" => $e->getMessage() ]);
	return;
}
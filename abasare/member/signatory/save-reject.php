<?php
header("Content-Type:application/json");
require_once "../../lib/db_function.php";


if(empty($_POST['loan_id'])){
	echo json_encode(['status' => false, "message" => "Unable to reject signatory request, Please contact system administrator"]);
	return;
}

if(empty($_POST['position'])){
	echo json_encode(['status' => false, "message" => "Unable to reject signatory request, Please contact system administrator"]);
	return;
}


if(empty($_POST['comment'])){
	echo json_encode(['status' => false, "message" => "Please provide comment"]);
	return;
}


$db->beginTransaction();
try{
	$column_condition = "signatory_".$_POST['position'];
	$column = "signatory_".$_POST['position']."_status";
	$column_comment = "signatory_".$_POST['position']."_comment";
	saveData($db, "UPDATE member_loans SET `{$column}` = ?, `{$column_comment}`=? WHERE `{$column_condition}` = ? AND id = ?", [0, $_POST['comment'], $_SESSION['user']['id'], $_POST['loan_id']]);

	$db->commit();
	echo json_encode(['status' => true, "message" => "Signatory Request Approved" ]);
	return;
} catch(\Exception $e){
	$db->rollback();
	echo json_encode(['status' => false, "message" => $e->getMessage() ]);
	return;
}
<?php
header("Content-Type:application/json");
require_once "../../lib/db_function.php";

if(empty($_POST['loan_id'])){
	echo json_encode(['status' => false, "message" => "Unable to change signatory, Please contact system administrator"]);
	return;
}

if(empty($_POST['position'])){
	echo json_encode(['status' => false, "message" => "Unable to change signatory, Please contact system administrator"]);
	return;
}
if(empty($_POST['signatory'])){
	echo json_encode(['status' => false, "message" => "Select new signatory please"]);
	return;
}

$db->beginTransaction();
try{
	$column_condition = "signatory_".$_POST['position'];
	$column = "signatory_".$_POST['position']."_status";
	$column_signatory = "signatory_".$_POST['position'];
	$column_comment = "signatory_".$_POST['position']."_comment";
	saveData($db, "UPDATE member_loans SET `{$column}` = NULL, `{$column_comment}`=NULL, `{$column_signatory}`=? WHERE  id = ?", [$_POST['signatory'], $_POST['loan_id']]);

	$db->commit();
	echo json_encode(['status' => true, "message" => "Signatory Changed Successfully" ]);
	return;
} catch(\Exception $e){
	$db->rollback();
	echo json_encode(['status' => false, "message" => $e->getMessage() ]);
	return;
}

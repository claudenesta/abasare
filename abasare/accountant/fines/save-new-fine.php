<?php
session_start(); 
header("Content-Type:application/json");
require_once "../../lib/db_function.php";

if(empty($_POST['fine_type_id'])){
	echo json_encode(['status' => false, "message" => "Which fine are you going to apply"]);
	return;
}

if(empty($_POST['member_id'])){
	echo json_encode(['status' => false, "message" => "Select member to apply selected fine"]);
	return;
}

if(count($_POST['member_id']) < 1){
	echo json_encode(['status' => false, "message" => "Make sure to select atleast 1 member"]);
	return;
}

if(empty($_POST['fine_amount'])){
	echo json_encode(['status' => false, "message" => "Please make sure to define fine amount ot be applied"]);
	return;
}

$db->beginTransaction();
try{

	foreach($_POST['member_id'] AS $member_id){
		saveData($db, "INSERT INTO special_fines SET member_id =?, fine_type_id=?, user_id=?, fine_amount=?, date=?", [
			$member_id,
			$_POST['fine_type_id'],
			$_SESSION['id'],
			$_POST['fine_amount'],
			(new \DateTime())->format("Y-m-d H:i:s"),
		]);
	}

	$db->commit();
	echo json_encode(['status' => true, "message" => "New fine applied to selected member" ]);
	return;
} catch(\Exception $e){
	$db->rollback();
	echo json_encode(['status' => false, "message" => $e->getMessage() ]);
	return;
}

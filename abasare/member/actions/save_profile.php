<?php
header("Content-Type:application/json");
require_once "../../lib/db_function.php";

if(empty($_POST['fname'])){
	echo json_encode(['status' => false, "message" => "No Family Name is supplied!"]);
	return;
}

if(empty($_POST['lname'])){
	echo json_encode(['status' => false, "message" => "No Last Name is supplied!"]);
	return;
}

if(empty($_POST['phone_cell'])){
	echo json_encode(['status' => false, "message" => "Every should Have a phone Number"]);
	return;
}

if(!preg_match("/^07[2,3,8,9]{1}[0-9]{7}$/", $_POST['phone_cell'])){
	echo json_encode(['status' => false, "message" => "Invalid Phone Number"]);
	return;
}

if(empty($_POST['email'])){
	echo json_encode(['status' => false, "message" => "Member should have an active email"]);
	return;
}

if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
	echo json_encode(['status' => false, "message" => "Invalid Email is found"]);
	return;
}

if(empty($_POST['id_number'])){
	echo json_encode(['status' => false, "message" => "Valid ID Card Number is required"]);
	return;
}

if(!preg_match("/^\d{16}$/", $_POST['id_number'])){
	echo json_encode(['status' => false, "message" => "Invalid ID Card Number provided"]);
	return;
}

if(empty($_POST['id_location'])){
	echo json_encode(['status' => false, "message" => "Where did the ID card is taken"]);
	return;
}

if(empty($_POST['birth_date'])){
	echo json_encode(['status' => false, "message" => "Birtdate is required"]);
	return;
}

if( (new \DateTime("-21 years"))->getTimestamp() < (new \DateTime($_POST['birth_date']))->getTimestamp() ){
	echo json_encode(['status' => false, "message" => "Member below 21 years old are not accepted"]);
	return;
}

if(empty($_POST['civil_status'])){
	echo json_encode(['status' => false, "message" => "Every member should have cival status"]);
	return;
}

if(empty($_POST['province_id'])){
	echo json_encode(['status' => false, "message" => "Every member should have residence province"]);
	return;
}

if(empty($_POST['district_id'])){
	echo json_encode(['status' => false, "message" => "Please Select district"]);
	return;
}

if(empty($_POST['sector_id'])){
	echo json_encode(['status' => false, "message" => "Please select a sector"]);
	return;
}

if(empty($_POST['cell_id'])){
	echo json_encode(['status' => false, "message" => "Residence Cell is a must"]);
	return;
}

if(empty($_POST['village_id'])){
	echo json_encode(['status' => false, "message" => "Residence Village is a must"]);
	return;
}
if(empty($_POST['address'])){
	echo json_encode(['status' => false, "message" => "Summarized address like hous number, etc"]);
	return;
}
if(empty($_POST['job_title'])){
	echo json_encode(['status' => false, "message" => "Job Title is required to become a member"]);
	return;
}
if(empty($_POST['company'])){
	echo json_encode(['status' => false, "message" => "Where do you work from."]);
	return;
}
if(empty($_POST['member_id'])){
	echo json_encode(['status' => false, "message" => "Unable to update the member's profile, Please contact system admin"]);
	return;
}

//Now start processing the member information
$db->beginTransaction();

try{
	//Check for province id
	$province_id = insertOrReturnID($db, "INSERT INTO provinces SET name = ?", "SELECT id FROM provinces WHERE id =?", "id", [$_POST['province_id']], [$_POST['province_id']]);

	$district_id = insertOrReturnID($db, "INSERT INTO districts SET province_id = ?, name=?", "SELECT id FROM districts WHERE province_id=? AND id=?", "id", [$province_id, $_POST['district_id']], [$province_id, $_POST['district_id']]);

	$sector_id = insertOrReturnID($db, "INSERT INTO sectors SET district_id = ?, name=?", "SELECT id FROM sectors WHERE district_id=? AND id=?", "id", [$district_id, $_POST['sector_id']], [$district_id, $_POST['sector_id']]);

	$cell_id = insertOrReturnID($db, "INSERT INTO cells SET sector_id = ?, name=?", "SELECT id FROM cells WHERE sector_id=? AND id=?", "id", [$sector_id, $_POST['cell_id']], [$sector_id, $_POST['cell_id']]);

	$village_id = insertOrReturnID($db, "INSERT INTO villages SET cell_id = ?, name=?", "SELECT id FROM villages WHERE cell_id=? AND id=?", "id", [$cell_id, $_POST['village_id']], [$cell_id, $_POST['village_id']]);

	//Now Make sure to update the member profile
	saveData($db, "UPDATE member SET fname=?, lname=?, phone_cell=?, id_number=?, id_location=?, email=?, birth_date=?, civil_status=?, sex=?, address=?, village_id=?, job_title=?, company=? WHERE id=?", [
		$_POST['fname'],
		$_POST['lname'],
		$_POST['phone_cell'],
		$_POST['id_number'],
		$_POST['id_location'],
		$_POST['email'],
		$_POST['birth_date'],
		$_POST['civil_status'],
		$_POST['sex'],
		$_POST['address'],
		$village_id,
		$_POST['job_title'],
		$_POST['company'],
		$_POST['member_id'],
	]);

	saveData($db, "UPDATE users SET name=? WHERE member_acc = ?", [sprintf("%s %s", $_POST['fname'], $_POST['lname']), $_POST['member_id']]);
	$db->commit();
	echo json_encode(['status' => true, "message" => "Profile Updated successfuly" ]);
	return;
} catch(\Exception $e){
	$db->rollback();
	echo json_encode(['status' => false, "message" => $e->getMessage() ]);
	return;
}

echo json_encode(['status' => false, "message" => "Please Wait"]);
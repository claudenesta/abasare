<?php
header("Content-Type:application/json");
require_once "../../lib/db_function.php";

if(empty($_POST['month'])){
	echo json_encode(['status' => false, "message" => "Contribution settings not available"]);
	return;
}

if(empty($_POST['year'])){
	echo json_encode(['status' => false, "message" => "Contribution settings not available"]);
	return;
}

if(empty($_POST['ref_number'])){
	echo json_encode(['status' => false, "message" => "Bank slip information"]);
	return;
}

if(empty($_POST['amount'])){
	echo json_encode(['status' => false, "message" => "How much have you contributed"]);
	return;
}

if(empty($_POST['paid_at'])){
	echo json_encode(['status' => false, "message" => "Date on bank slip"]);
	return;
}

$now = new \DateTime();
$payment_date = new \DateTime($_POST['paid_at']);

if($payment_date->getTimestamp() > $now->getTimestamp()){
	echo json_encode(['status' => false, "message" => "Payment date can't be in the future!"]);
	return;
}

if($_POST['fines'] > 0){

	if(empty($_POST['fine_amount'])){
		echo json_encode(['status' => false, "message" => "Fine amount is required"]);
		return;
	}

	if($_POST['fine_amount'] != $_POST['fines']){
		echo json_encode(['status' => false, "message" => "Invalid fine amount"]);
		return;
	}

	if(empty($_POST['fine_ref_number'])){
		echo json_encode(['status' => false, "message" => "Please provide the bank slip information for fines!"]);
		return;
	}
}

//
$db->beginTransaction();
try{

	$saving_year = $_POST['year'];
	$saving_month = $_POST['month'];
	$saving_info = new \DateTime($saving_year."-".($saving_month < 10?"0":"").$saving_month."-01");
	$default_deadline = $saving_info->format("Y-m-t");
	$saving_setting = first($db, "SELECT 	a.*,
		                                    COALESCE(b.social_overdue, '{$default_deadline}') AS saving_overdue,
		                                    COALESCE(b.id, '{$_POST['overdue_id']}') AS overdue_id
		                                    FROM (
		                                      SELECT
		                                              '{$saving_year}' AS year,
		                                              '{$saving_month}' AS month
		                                    ) AS a
		                                    LEFT JOIN overdue_settings AS b
		                                    ON a.year = b.year AND a.month = b.month
		                                    ");
	$delay_days = 0;
	$required_fines = 0;
	if(!is_null($saving_setting) && $saving_setting){
		$contribution_date = new \DateTime($saving_setting['saving_overdue']);
		if($contribution_date->getTimestamp() < $now->getTimestamp()){
			$delay = $contribution_date->diff($now);
			$delay_days = $delay->days;
			$required_fines = $delay_days * 100;
		}
	}

	$has_fine = 0;
	$fine_data = NULL;
	if($_POST['fine_amount'] < $required_fines){
		$db->rollback();
		echo json_encode(['status' => false, "message" => sprintf("paid fines of %s is not application to required fines of %s please revise the form information", number_format($_POST['fine_amount']), number_format($required_fines) ) ]);
		return;
	}

	if($required_fines > 0){
		$has_fine = true;
		$fine_data = json_encode([
			'member_id' => $_SESSION['acc'],
			'amount' => $required_fines,
			'saving_overdu' => $_POST['fine_amount'],
			'desciption' => sprintf('%s saving payment overdue for %s day%s', $saving_info->format('F Y'), $delay_days, $delay_days > 1?"s":""),
			'month' => $saving_month,
			'year' => $saving_year,
			"referance_number" => $_POST['fine_ref_number']
		]);
	}

	$data = [
		"member_id" => $_SESSION['acc'],
		"overdue_id" => $saving_setting['overdue_id'],
		"overdue_date" => $saving_setting['saving_overdue'],
		"sav_amount" => $_POST['amount'],
		"month" => $saving_setting['month'],
		"year" => $saving_setting['year'],
		"fine" => $_POST['fine_amount'],
		"days" => $delay_days,
	];
	
	saveData($db, "INSERT INTO bank_slip_requests SET member_id=?, type=?, ref_number=?, amount=?, data=?, paid_at=?, created_at=?, has_fine=?, fine_data=?", [
		$_SESSION['acc'], 'social savings', $_POST['ref_number'], $_POST['amount'], json_encode($data), $_POST['paid_at'], (new \DateTime())->format('Y-m-d H:i:s'), $has_fine, $fine_data 
	]);

	$db->commit();
	echo json_encode(['status' => true, "message" => "The bank slip recording operation initiated, Please submit the hard copy to the accountant for filing." ]);
	return;
} catch(\Exception $e){
	$db->rollback();
	echo json_encode(['status' => false, "message" => $e->getMessage() ]);
	return;
}
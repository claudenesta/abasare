<?php
header("Content-Type:application/json");
require_once "../../lib/db_function.php";


if(empty($_POST['member_id'])){
	echo json_encode(['status' => false, "message" => "Unable to request loan, Please contact system administrator"]);
	return;
}

if(empty($_POST['loan_type_id'])){
	echo json_encode(['status' => false, "message" => "Select Loan you want."]);
	return;
}

if(empty($_POST['principal'])){
	echo json_encode(['status' => false, "message" => "How much do you want?"]);
	return;
}

if($_POST['emergency_selected'] == 0) {
	if(empty($_POST['signatory_1'])){
		echo json_encode(['status' => false, "message" => "Please select to sign for you in first place"]);
		return;
	}

	if(empty($_POST['signatory_2'])){
		echo json_encode(['status' => false, "message" => "Please select a member to sign for you in second place"]);
		return;
	}

	if($_POST['signatory_2'] == $_POST['signatory_1']){
		echo json_encode(['status' => false, "message" => "You have to selected single signatory, Please choose different ones"]);
		return;
	}
}

if($_POST['top_up_selected'] == 1 && empty($_POST['toped_up_loan'])){
	//Make sure also the laod id is selected
	echo json_encode(['status' => false, "message" => "You have to selected loan to top up please"]);
	return;
}

$db->beginTransaction();
try{
	//Validate the loan Limit Operation
	$signatory_1_status = NULL;
	$signatory_2_status = NULL;
	if($_POST['emergency_selected'] == 1) {
		//make sure to have a user named Emergency which is used for signing for emergency loans
		$_POST['signatory_1'] = $_POST['signatory_2'] = returnSingleField($db, "SELECT id FROM users WHERE username = ?", "id", ['EMERGENCY']);
	}

	if(empty($_POST['signatory_1']) || empty($_POST['signatory_2'])){
		$db->rollback();
		echo json_encode(['status' => false, "message" => "Unable to locate emergency singer!, Please contact system administrator for assistance"]);
		return;
	}
	if($_POST['emergency_selected'] == 1) {

		$signatory_1_status = 1;
		$signatory_2_status = 1;
	}
	//check if the selected load is topup first
	$loan_type_selected = first($db, "SELECT id, lname, interest, is_top_up, is_emergeny, percentage_before_top_op FROM loan_type WHERE id = ?", [$_POST['loan_type_id']]);

	if($loan_type_selected['is_top_up'] && empty($_POST['toped_up_loan'])){
		$db->rollback();
		echo json_encode(['status' => false, "message" => "You have to selected loan to top up please"]);
		return;
	}

	//get saving informatin for loan limit checkinh
	$savings = returnSingleField($db, "SELECT 	SUM(a.amount) AS saving_amount 
	                                          	FROM (
		                                            SELECT  a.sav_amount AS amount,
			                                                a.year, a.month,
			                                                CONCAT(a.year, '-', IF(a.month < 10, '0',''), a.month, '-01') AS contribution_month
			                                                FROM saving AS a
			                                                WHERE a.member_id = ?
			                                                ORDER BY id DESC
	                                          	) AS a
	                                          	WHERE a.contribution_month <= ?
	                                          	", "saving_amount", [$_SESSION['user']['member_acc'], (new \DateTime())->format('Y-m-01')]);
	$loan_limit = $savings*2.5;
	if($_POST['principal'] > $loan_limit){
		$db->rollback();
		echo json_encode(['status' => false, "message" => sprintf("you requested %s RWF which is greater than your loan limit of %s RWF", number_format($_POST['principal']), number_format($loan_limit) )]);
		return;
	}

	//Check if there is no other loan which is still ongoing
	$previous_loan = returnAllData($db, "SELECT id, member_id, loan_id, status FROM member_loans WHERE member_id = ? AND loan_id = ? AND status NOT IN (?,?)", [$_POST['member_id'], $_POST['loan_type_id'], "REJECTED", "CLOSED"]);
	if(count($previous_loan) > 0){
		echo json_encode(['status' => false, "message" => sprintf("You still have %d ongoing loan", count($previous_loan))]);
		$db->rollback();
		return;
	}

	$loan_type = first($db, "SELECT id, interest, terms FROM loan_type WHERE id=?", [$_POST['loan_type_id']]);

	$total_interest = $_POST['principal'] * $loan_type['interest']/100;
	$payment_per_installement = $_POST['principal']/$loan_type['terms'];

	//Save the loan request application
	$loan_id = saveAndReturnID($db, "INSERT INTO member_loans SET member_id=?, loan_id=?, status=?, loan_date=?, loan_amount=?, loan_amount_interest=?, loan_amount_term=?, loan_amount_total=?, signatory_1=?, signatory_1_status=?, signatory_2=?, signatory_2_status=?",[
		$_POST['member_id'],$_POST['loan_type_id'], "OPEN", (new \DateTime())->format("Y-m-d"), $_POST['principal'],
		$total_interest, $payment_per_installement, $_POST['principal'], $_POST['signatory_1'], $signatory_1_status, $_POST['signatory_2'], $signatory_2_status
	]);

	//create top up details
	if($_POST['top_up_selected'] == 1){
		saveData($db, "INSERT INTO topup_details SET loan_id = ?, toped_up_loan_id=?, total_recovered_amount=?", [$loan_id, $_POST['toped_up_loan'], $_POST['toped_up_amount']]);
	}

	$db->commit();
	echo json_encode(['status' => true, "message" => "Loan Request submitted, Please contact your signatories to approve your request, Thanks" ]);
	return;
} catch(\Exception $e){
	$db->rollback();
	echo json_encode(['status' => false, "message" => $e->getMessage() ]);
	return;
}
<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . "../lib/db_function.php";

// Make sure to get the list 
$date_object = new \DateTime("2022-01-01 00:00:00");

//get the sum of all application 
$unshared_interest = returnSingleField($db, "SELECT 	COALESCE(SUM(a.amount),0) AS amount
														FROM interest AS a
														WHERE a.month = ?
														AND a.year = ?
														AND Is_posted IS NULL
														", "amount", [$date_object->format("n"),$date_object->format("Y")]);

// print_r([$unshared_interest]);
if($unshared_interest <= 0){
	//here no Operation as the
	print_r(["unshared_interest" => $unshared_interest, "error" => "Not interest to share"]);
	return;
}
//get the list of active members as of the current Date
$members = returnAllData($db, "SELECT 	a.id,
										COALESCE(SUM(b.amount),0) + COALESCE(SUM(c.sav_amount),0) AS investement
										FROM member AS a
										LEFT JOIN capital_share AS b
										ON a.id = b.member_id AND (b.year < ? OR (b.year = ? AND b.month <?) )
										LEFT JOIN saving AS c
										ON a.id = c.member_id AND (c.year < ? OR (c.year = ? AND c.month <?) )
										WHERE a.status = ?
										GROUP BY a.id
										", $condition = [$date_object->format("Y"), $date_object->format("Y"), $date_object->format("n"),$date_object->format("Y"), $date_object->format("Y"), $date_object->format("n"),1]);
// print_r(["year" => $date_object->format("Y"), "month" => $date_object->format("n")]);
// print_r($members);
$manupulated_data = [];
$total_investement = 0;
foreach ($members as $member) {
	if($member['investement'] > 0){
		$manupulated_data[] = [
			"member_id" => $member['id'],
			"investement" => $member['investement'],
			"interest" => 0
		];
		$total_investement += $member['investement'];
	}
}
// print_r([$manupulated_data, $total_investement]);
$total_shared_interest = 0;
foreach($manupulated_data AS $index => $single_member){
	$interest = floor($single_member['investement'] * $unshared_interest / $total_investement);
	$manupulated_data[$index]['interest'] = $interest;
	$total_shared_interest += $interest;
}

print_r($manupulated_data);
// print_r([$total_shared_interest, $unshared_interest, () ]);
$remaining_interest = $unshared_interest - $total_shared_interest; // this will be inserted in the interest for later sharing
$db->beginTransaction();

try{
	$interest_rate = ($unshared_interest/$total_investement);
	//Create shared interest records
	$shared_interest_id = insertOrReturnID($db, "INSERT INTO shared_interest SET total_investment = ?, total_interest=?, interest_rate=?, year=?, month=?", "SELECT id FROM shared_interest WHERE year=? AND month=?", "id", [$total_investement, $total_shared_interest, $interest_rate, $date_object->format("Y"), $date_object->format("n")], [$date_object->format("Y"), $date_object->format("n")]);
	foreach($manupulated_data AS $single_data){
		$history_id = insertOrReturnID($db, "INSERT INTO shared_interest_histories SET member_id =?, shared_interest_id=?, amount=?, created_at=?, updated_at=?", "SELECT id FROM shared_interest_histories WHERE member_id=? AND shared_interest_id=? ", "id", [$single_data['member_id'], $shared_interest_id, $single_data['interest'], (new \DateTime())->format("Y-m-d H:i:s"),(new \DateTime())->format("Y-m-d H:i:s") ], [$single_data['member_id'], $shared_interest_id]);
	}

	if($remaining_interest > 0){
		$year = $date_object->format("Y");
		$month = $date_object->format("n");
		if($month == 12){
			$month = 1;
			$year++;
		} else {
			$month++;
		}
		saveData($db, "INSERT INTO interest SET amount=?, ref_id=0, desciption=?, month=?, year=?", [$remaining_interest, sprintf("unshare intered in %s", $date_object->format("F Y")), $month, $year]);
	}

	//Make sure to Update interest and mark them as shared
	saveData($db, "UPDATE interest SET Is_posted=? WHERE year=? AND month=?", ['shared',$date_object->format("Y"), $date_object->format("n")]);

	$db->commit();
	print_r(['status' => true, "message" => "Interest shared successfully", "1RWF" => $interest_rate ]);
	return;
} catch(\Exception $e){
	$db->rollback();
	print_r(['status' => false, "message" => $e->getMessage() ]);
	return;
}

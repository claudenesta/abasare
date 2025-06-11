<?php
require_once "lib/db_function.php";

$all_data = returnAllData($db, "SELECT * FROM `bank_slip_requests` WHERE status='accepted' AND data like('%payment_details%')");

// var_dump($all_data);
foreach($all_data AS $data){
	$my_data = json_decode($data['data']);
	foreach($my_data->payment_details AS $row_data){
		// var_dump($row_data);
		$query = "UPDATE lend_payments SET amount=?, overdue_fine=?, comment=CONCAT(COALESCE(comment,''), ?) WHERE id=? AND status=? AND amount=?";
		$query_data = [
			$row_data->amount,
			$row_data->fine,
			" adjusted by script",
			$row_data->id,
			"PAID",
			0
		];
		// var_dump($query, $query_data);
		saveData($db, $query, $query_data);
		echo $row_data->id." is affected by amount ".$row_data->amount.PHP_EOL;
	}
}
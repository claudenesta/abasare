<?php
require_once "../lib/db_function.php";

//Get the due date for social saving
$last_saving = first($db, "SELECT * FROM sacial_saving WHERE member_id = ? ORDER BY year DESC, month DESC LIMIT 0,1", [$_GET['id']]);

//get the value of the next required saving payments
$required_month = $last_saving['month'];
$required_year = $last_saving['year'];
if($required_month < 12){
	$required_month++;
} else {
	$required_year++;
	$required_month=1;
}

$data = first($db, "SELECT * FROM overdue_settings WHERE month = ? AND year = ?", [$required_month, $required_year]);

$saving_due = new \DateTime($data['saving_overdue']);
$now = new \DateTime();
$days = 0;
if($saving_due->getTimestamp() < $now->getTimestamp()){
    //Get the number of days to count fines
    $days = $saving_due->diff($now)->days;
}
$fines = $days * 100;
echo $fines;
<?php 
include('../lib/db_function.php');
$sqi="SELECT * FROM `loan_type` where id=?";
$data = first($db, $sqi, [$_GET['q']]);
echo $data['terms'];
echo "|";
echo $data['interest'];
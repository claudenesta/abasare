<?php 
require_once('../lib/db_function.php');
$type=$_GET['q'];
$sqi="SELECT * FROM `loan_type` where id= ?";
$rowu=first($db, $sqi, [$_GET['q']]);

echo $rowu['terms'];
echo "|";
echo $rowu['interest'];
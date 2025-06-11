<?php
require_once "../lib/db_function.php";

//Here try to check if we can switch accounts
if($_SESSION['role'] == 6){
	// now make sure to save new role temporary
	$_SESSION['temp_role'] = 6;

	//The set the role to be member profile
	$_SESSION['role'] = 5;

	if(array_key_exists($_SESSION['role'], $login_target_switch)) {
		$location = "/".$login_target_switch[$_SESSION['role']]."/";
		header("Location:".$location);
		die();
	}
}

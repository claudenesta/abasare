<?php
require_once "../lib/db_function.php";

//Here try to check if we can switch accounts
if(isset($_SESSION['temp_role']) && $_SESSION['role'] == 5){
	// now make sure to save new role temporary
	$_SESSION['role'] = $_SESSION['temp_role'];
	unset($_SESSION['temp_role']);
	if(array_key_exists($_SESSION['role'], $login_target_switch)) {
		$location = "/".$login_target_switch[$_SESSION['role']]."/";
		header("Location:".$location);
		die();
	}
}
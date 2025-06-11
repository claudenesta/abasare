<?php 
include('DBController.php');
if($_POST['dividand']){
    $a=$_POST['m_id'];
	$b=$_POST['prof'];
	$month=$currentmonth;
	$yaer=$currentyear;
 for($exe = 0; $exe < count($a); $exe++ )
    {
 $sql = "INSERT INTO `profite` (`id`, `m_id`, `amount`, `month`, `year`) VALUES (NULL, '$a[$exe]', '$b[$exe]', '$month', '$yaer')";
 mysqli_query($con, $sql);
       
        
    }
    
}


?>
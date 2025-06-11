<?php
	include("../DBController.php");

$id=$_POST['id'];
$meb_id=$_POST['meb_id'];
$status=$_POST['status']; 
if($status==1){
 $stus=0;   
}else{
 $stus=1;   
}
mysqli_query($con,"update users set status='".$stus."' where id='$id'");
mysqli_query($con,"update member set status='".$stus."' where id='$meb_id'");
echo 'user updated successfull';
?>
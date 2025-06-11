<?php 
include('DBController.php');
$id=$_GET['id'];
$delete="DELETE FROM `loan_type` WHERE `loan_type`.`id` ='$id'";
if(mysqli_query($con,$delete)){
?>
<meta http-equiv="refresh" content="0; URL=legder_setup.php">
<?php } ?>
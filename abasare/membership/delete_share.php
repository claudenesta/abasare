<?php 
include('../DBController.php');
$emp_id=$_GET['id'];
$emp=$_GET['mid'];
$sql = "DELETE FROM `capital_share` WHERE `capital_share`.`id` ='$emp_id'";
$query=MYSQLI_QUERY($con,"$sql") OR DIE(MYSQLI_ERROR());
if($query){
?>
<meta http-equiv="refresh" content="0; URL=capital_share_info.php?m_idi=<?php echo $emp; ?>">
<?php } ?>
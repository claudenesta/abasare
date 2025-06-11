<?php 
include('./DBController.php');
$emp_id=$_GET['id'];
$emp=$_GET['m_id'];
$sql = "DELETE FROM `sacial_saving` WHERE `id` ='$emp_id'";
//echo $sql;
$query=mysqli_query($con,$sql) or die (mysqli_error($con));
if($query){
?>
<meta http-equiv="Refresh" content="0; url=member_info.php?id=<?php echo $emp; ?>">

<?php } ?>
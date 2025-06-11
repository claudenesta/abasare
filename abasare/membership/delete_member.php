<?php 
include('../DBController.php');
$emp_id=$_GET['id'];
$sql = "DELETE FROM member WHERE id= $emp_id" ;
$query=MYSQLI_QUERY($con,"$sql") OR DIE(MYSQLI_ERROR());
if($query){
?>
<meta http-equiv="refresh" content="0; URL=list.php">
<?php } ?>
<?php include('../DBController.php');
$type=$_GET['q'];
$sqi="SELECT * FROM `loan_type` where id='$type'";
	  $query=mysqli_query($con,$sqi);
	  $rowu=mysqli_fetch_array($query);
	  $term=$rowu['terms'];
	  $interst=$rowu['interest'];
	  $id=$rowu['id'];
echo $term;
echo "|";
echo $interst;
echo "|";
echo $id;

	  

?>

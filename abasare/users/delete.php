<?php
	include("../DBController.php");
    require_once "../ShoppingCart.php";
    
	$id=$_GET['id'];
	mysqli_query($con,"delete from users where id='$id'");
	header('location:view.php');
	echo "<script>alert('User removed successfully!')</script>";
 
?>
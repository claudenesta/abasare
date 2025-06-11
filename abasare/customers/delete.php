<?php
	include("../DBController.php");
    require_once "../ShoppingCart.php";
    
	$id=$_GET['id'];
	mysqli_query($con,"delete from customers where id='$id'");
		header('location:customers.php');
		echo "<script>alert('Deleted Successfully!')</script>";
	
 
?>
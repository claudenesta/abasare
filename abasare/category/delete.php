<?php
	include("../DBController.php");
    require_once "../ShoppingCart.php";
    
	$id=$_GET['id'];
	mysqli_query($con,"delete from category_items where id='$id'");
	header('location:view.php');
	echo "<script>alert('Deleted Successfully!')</script>";
 
?>
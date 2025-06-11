<?php
include('DBController.php');
$loan_id=$_GET['idd'];
$sql = "UPDATE `member_loans` SET `reject` = '1' WHERE `member_loans`.`id` = '$loan_id'";
if(mysqli_query($con,$sql)){
 $dele_stin="DELETE FROM `member_loan_settings` WHERE `member_loan_settings`.`loan_id` ='$loan_id'"; 
 $delet="DELETE FROM `lend_payments` WHERE `lend_payments`.`borrower_loan_id` = '$loan_id'";
 mysqli_query($con,$delet);
 mysqli_query($con,$dele_stin);
?>
<meta http-equiv="Refresh" content="0; url=appliedloan.php">
<?php
}
?>
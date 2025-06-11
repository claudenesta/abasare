<?php include('DBController.php');
$qul="SELECT * FROM `member_loans` where MONTH(`loan_date`)=2 AND YEAR(`loan_date`)=2021";
 $query=mysqli_query($con, $qul);
 while($row=mysqli_fetch_array($query)){
  $loan_id=$row['id'];
  $loan_interest=$row['loan_amount_interest'];
  $date=strtotime($row['loan_date']);
  $month=date('m',$date);
  $year=date('Y',$date);
  
  $sqll="INSERT INTO `interest` (`id`, `loan_interest`, `fine_overdue`, `membership_fee`, `saving_overdu`, `ref_id`, `desciption`, `month`, `year`, `done_at`, `loan_ref`, `Is_posted`) 
  VALUES (NULL, '$loan_interest', '', '', '', '$loan_id', 'Loan interest', '$month', '$year', CURRENT_TIMESTAMP, '$loan_id', NULL)";
  mysqli_query($con, $sqll);
  
     
 }
 ?>
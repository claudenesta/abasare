<?php include('../DBController.php');
$loan_id=$_GET['loan_id'];
$loan="SELECT * , ml.id AS borrower_loan_id, mls.lname AS loan_name
FROM member_loans ml
INNER JOIN member_loan_settings mls ON ml.id = mls.`loan_id`
INNER JOIN member m ON m.id = ml.`member_id`
WHERE ml.id = '$loan_id'";
$query=mysqli_query($con,$loan);
$rows=mysqli_fetch_array($query);
$paidloan="select sum(amount) as total from lend_payments  where payment_number IN (SELECT payment_id FROM `payment_transactions`) AND borrower_loan_id='$loan_id' AND status='PAID'";
$qqu=mysqli_query($con,$paidloan);
$result=mysqli_fetch_array($qqu);

if(isset($_POST['botton_move'])){
  $datemov=$_POST['datemov'];
  $payment_idi=$_POST['payment_idi'];
  $sqluuuu= "UPDATE `lend_payments` SET `payment_sched` = '$datemov' WHERE `lend_payments`.`payment_number` ='$payment_idi';";
  mysqli_query($con,$sqluuuu);	
}
 
 //update payment
 
 if(isset($_POST['edit_pay'])){
    $member=$_POST['member_id'];
    $next_ided=$_POST['next_ide'];
    $amount=$_POST['amount'];
    $fine=$_POST['fine'];

    $last=$_POST['member_loan_id'];
    $sql11 = "UPDATE payment_transactions SET payment='$amount' WHERE payment_id='$next_ided'";
    $qp=mysqli_query($con,$sql11);	
    $sqlupda = "UPDATE `lend_payments` SET amount='$amount', overdue_fine='$fine' WHERE `lend_payments`.`payment_number` = '$next_ided'";
    $qi=mysqli_query($con,$sqlupda);
    header("location:Loan_info.php?loan_id=$last");
 }
 
 
 
if(isset($_POST['botton_save'])){
  $member=$_POST['member_id'];
  $next_id=$_POST['next_id'];
  $amount=$_POST['amount'];
  $fine= $_POST['fine'];

  //Get the lend payment to be paid
  mysqli_begin_transaction($con);

    try{

      $myquery = mysqli_query($con, "SELECT * FROM lend_payments WHERE payment_number = '{$next_id}'");
      $lend_payment_info = mysqli_fetch_array($myquery);

      $last=$_POST['member_loan_id'];
      $sql11 = "INSERT INTO `payment_transactions` (`id`, `member_id`, `payment`, `admin_id`, `payment_id`, `rdate`) VALUES (NULL, '$member', '$amount', '1', '$next_id', CURRENT_TIMESTAMP)";
      $qp=mysqli_query($con,$sql11);	
      $sqlupda = "UPDATE `lend_payments` SET `status` = 'PAID',amount='$amount', overdue_fine='$fine' WHERE `lend_payments`.`payment_number` = '$next_id'";
      $qi=mysqli_query($con,$sqlupda) or die(mysqli_error($con));
      if($fine > 0){
        $month = (new DateTime($_POST['duedate']))->format('n');
        $year = (new DateTime($_POST['duedate']))->format('Y');
        $sql = "INSERT INTO `interest` (`id`, `loan_interest`, `lend_payment_id`, `fine_overdue`, `membership_fee`, `saving_overdu`, `ref_id`, `desciption`, `done_at`, `loan_ref`, `month`, `year`) 
              VALUES (NULL, '0', '{$lend_payment_info['id']}', '$fine', '0', '0', '$next_id', 'overdue fine $next_id', CURRENT_TIMESTAMP, $last , $month, $year)";
      
        mysqli_query($con,$sql) or die(mysqli_error($con));
      }
     
  
    $check_nextpyt="select * from lend_payments where borrower_loan_id='$last' AND status='UNPAID' order by payment_sched";
  	$query=mysqli_query($con,$check_nextpyt);
  	$rows=mysqli_fetch_array($query);
  	$iid=$rows['payment_number'];
  	if(mysqli_num_rows($query)==0){
  		$updta = "UPDATE `member_loans` SET `status` = 'CLOSED' WHERE `member_loans`.`id` = '$last'";
  	}else{
  	 $updta="UPDATE `member_loans` SET `next_payment_id` = '$iid' WHERE `member_loans`.`id` ='$last'";
  	}
	  mysqli_query($con,$updta) or die(mysqli_error($con));
    mysqli_commit($con);

    header("location:Loan_info.php?loan_id=$last");	
  } catch(\Exception $e) {
    mysqli_rollback($con);
    throw new Exception($e->getMessage());
  }
}

include('./header.php');

$active = "member-list-active";
include('./menu.php'); 
?>

  <!-- Content Wrapper. Contains page content -->
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Member Details <small>View/Search </small>
      </h1>
      <ol class="breadcrumb">
          <li><a href="/admin/"><i class="fa fa-dashboard"></i>Home</a></li>
          <li><a href="member_info.php?id=<?= $rows['id'] ?>">Member</a></li>
          <li><a href="#">Loans</a></li>
          <li class="active"><a href="Loan_info.php?loan_id=<?= $_GET['loan_id'] ?>">Loan History </a></li>
      </ol>
    </section>

    <!-- Main content -->
    <form action="#" class="" id="table_form" method="post" accept-charset="utf-8">
   
    <section class="content">
      <div class="row">
        <!-- ********** ALERT MESSAGE START******* -->
          <div class="col-md-12">
      
     </div>            <!-- ********** ALERT MESSAGE END******* -->
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header ">
              <h3 class="box-title">&nbsp;</h3>
              <?php if($_SESSION['role']==2){ ?>
               <a class="btn btn-success btn-flat btn-sm" <?php if($rows['status']=='CLOSED'){echo 'disabled';}else{echo 'data-toggle="modal" data-target="#modal-payment"';}?> ><i class="fa fa-plus"></i> Payment </a>
			   <a class="btn btn-primary btn-flat btn-sm" <?php if($rows['status']=='CLOSED'){echo 'disabled';}else{echo 'data-toggle="modal" data-target="#modal-movepayment"';}?> ><i class="fa fa-plus"></i> Move payment </a>
			  <?php } ?>
			   <a class="btn btn-warning btn-flat btn-sm" href="../dompdf/www/loan_statement.php?loan_id=<?php echo $loan_id; ?>"><i class="fa fa-plus"></i> Print Historical Report</a>
                          </div>
            <!-- /.box-header -->
            <div class="box-body">
             <div class="container-fluid">
        <div class="row">
          <div class="col-md-3">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
			 <hr>
     <div class="user-block">
                        <span class="username">
                          <a href="#">Loan Info </a>
                        </span>
                      </div>
				<hr>
                <p class="text"> Loan #: <?php echo $rows['loan_id'];  ?> </p>
                <p class="text"> Loan Type: <?php echo $rows['interest']."% ".$rows['loan_name'];  ?> </p>
                <p class="text"> Borrower: <?php echo $rows['fname']." ".$rows['lname'];  ?> </p>
                <p class="text"> Status: <?php echo $rows['status'];  ?> </p>
                <p class="text"> Remaining Balance: <?php echo $rows['loan_amount']-(int)$result['total'];  ?> </p>
                <p class="text"> Payments Made: <?php echo (int)$result['total'];?> Frw(s) </p>
                <p class="text"> Total Loan Amount: <?php echo (int)$rows['loan_amount'];?> Frw(s)  </p>
 <hr>
                 <div class="user-block">
                        <span class="username">
                          <a href="#">Schedule Payment </a>
                        </span>
                      </div>
 <hr>
               <p class="text"> Payment #: <?php echo $rows['next_payment_id']; 
          $payid=$rows['next_payment_id'];	
          // var_dump($rows);
          // die($payid);
			   $pay="select * from lend_payments lp inner join member_loan_settings ms on ms.loan_id=lp.borrower_loan_id where lp.payment_number='$payid'";
         // echo $pay;
			   $query=mysqli_query($con,$pay);
			   $roww=mysqli_fetch_array($query);
			   ?> </p>
			   
			    
                <p class="text"> Amount : <?php echo $rows['loan_amount_term'];  ?> Frw(s) </p>
                <p class="text"> Due Date : <?php echo $roww['payment_sched'];  ?> </p> 
                <p class="text"> Status: <?php echo $roww['status'];  ?> </p>
            </div>
          </div>
          <div class="col-md-9">
            <div class="card">
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="activity">
                    <!-- Post -->
                    <div class="post">
                      <div class="user-block">
                        <span class="username">
                          <a href="#">Overview of Loan Payment </a>
                        </span>
                      </div>
					  <table id="example" class="table table-striped table-bordered" style="width:100%">
					  <tr>
					  <th>Payment #</th>
					  <th>Loan taken Date </th>
					  <th>Next payment Date </th>
					  <th>Amount</th>
					  <th>Status</th>
					   <?php if($_SESSION['role']==2){ ?>
					  <th>Option</th>
					 
					  <?php } ?>
					  </tr>
					  <?php
					  $iiid=0;
  $sql="select *, datediff(NOW(), lp.payment_sched) as is_due, lp.status as stsa from lend_payments lp inner join member_loans ml on lp.borrower_loan_id=ml.id where lp.borrower_loan_id='$loan_id' order by lp.payment_number";
  $queue=mysqli_query($con,$sql);
  while($row=mysqli_fetch_array($queue)){
	  $iiid++;
	  $is_due=(int)$row['is_due'];
	  $stsa=$row['stsa'];   
	  $amont=ceil($row['amount']);
	  $payid=$row['payment_number'];
		?>
                   <tr>
					  <td><?php echo $iiid; ?></td>
					  <td><?php echo $row ["loan_date"];?></td>
					  <td><?php echo $row['payment_sched']; ?> </td>
					  <td><?php echo $amont; ?> Frws</td>
					  <td><span style="color:<?php echo $stsa=='PAID' ? 'GREEN' : 'RED'?>"><?php 
if($is_due>0 AND $stsa=='UNPAID'){echo "OVER DUE"; }
else if($is_due==0 AND $stsa=='UNPAID'){ echo "DUE TODAY"; }
else if($is_due<0 AND $stsa=='UNPAID'){ echo "UNPAID"; }
else{echo "PAID";}
					  ?></span></td>
					   <?php if($_SESSION['role']==2){ ?>
					   <td>
					       
					      <?php 
if($is_due>0 AND $stsa=='UNPAID'){ echo ""; }
else if($is_due==0 AND $stsa=='UNPAID'){ echo ""; }
else if($is_due<0 AND $stsa=='UNPAID'){ echo ""; }
else{?>

   <a data-toggle="modal" data-target="#modal-<?php echo $payid; ?>"><i class="fa fa-edit"></i> Edit  </a>
 <?php } ?> 
					       
	 <!-- edit loan payment -->
      <div class="modal fade" id="modal-<?php echo $payid; ?>">
<form class="form-horizontal" method="post">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header bg-primary ">
              <h4 class="modal-title"><span style="color:white"> EDIT PAYMENT </span></h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
			<?php
               $payidE=$payid;	
               $pay="select *,datediff(NOW(), lp.payment_sched) as is_due2 from lend_payments lp inner join member_loan_settings ms on ms.loan_id=lp.borrower_loan_id where lp.payment_number='$payidE'";
			   $query=mysqli_query($con,$pay);
			   $roww=mysqli_fetch_array($query);
			   ?> 
            <div class="modal-body">
             <div class="card card-info">
                <div class="card-body">
                  <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-4 col-form-label">Payment:<?= $payidE?></label>
                    <div class="col-sm-2">
                      <input type="text" name="next_ide" readonly  value="<?php echo $payidE; ?>" class="form-control"  placeholder="Loan Amount">
                      <input type="hidden" name="member_id" readonly  value="<?php echo $roww['borrower_id']; ?>" class="form-control"  placeholder="Loan Amount">
                      <input type="hidden" name="member_loan_id" readonly  value="<?php echo $rows['loan_id'];  ?>" class="form-control"  placeholder="Loan Amount">
                    </div>
                  </div>
				  <div class="form-group row">
                    <label for="inputEmail4" class="col-sm-4 col-form-label">Amount:</label>
                    <div class="col-sm-3">
                      <input type="text"  name="amount" value="<?php echo $roww['amount'];  ?>" class="form-control"  placeholder="Loan Amount">
                    </div>
                    <label for="inputEmail3" class="col-sm-2 col-form-label">Fine:</label>
                     <div class="col-sm-3">
                      <input type="text"  name="fine" value="<?php echo $roww['overdue_fine'];?>" class="form-control"  placeholder="Loan Amount">
                    </div>
                     
                  </div>
				  <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-4 col-form-label">Due Date:</label>
                    <div class="col-sm-8">
                      <input type="date" date name="duedate" value="date" class="form-control"  placeholder="Loan Amount">
                    </div>
                  </div>
				   <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-4 col-form-label">Status:</label>
                    <div class="col-sm-8">
                      <input type="text" readonly name="status" value="<?php echo $roww['status'];  ?>" class="form-control"  placeholder="Loan Amount">
                    </div>
                  </div>
				    <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-8 col-form-label"></label>
                    <div class="col-sm-4">
                      <input type="submit" name="edit_pay" value="Modify" class="form-control btn-primary">
                    </div>
                  </div>
                </div>
            </div>
            </div>
          </div>
		  
        </div>
		 </form>
        <!-- /.modal-dialog -->
      </div>
  <!-- End loan edit -->				  

					   </td>
					   <?php } ?>
					  </tr>  
					

  <?php } ?>	
</table>

  </div>
     
                  <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- /.nav-tabs-custom -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
    </form>  </div>
  <!-- /.content-wrapper -->
  
<div class="modal fade" id="modal-payment">
<form class="form-horizontal" method="post">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header bg-primary ">
              <h4 class="modal-title"><span style="color:white"> LOAN PAYMENT </span></h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
			<?php
               $payid=$rows['next_payment_id'];	
               $pay="select *,datediff(NOW(), lp.payment_sched) as is_due2 from lend_payments lp inner join member_loan_settings ms on ms.loan_id=lp.borrower_loan_id where lp.payment_number='$payid'";
			   $query=mysqli_query($con,$pay);
			   $roww=mysqli_fetch_array($query);
			   ?> 
            <div class="modal-body">
             <div class="card card-info">
                <div class="card-body">
                  <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-4 col-form-label">Payment:</label>
                    <div class="col-sm-2">
                      <input type="text" name="next_id" readonly  value="<?php echo $rows['next_payment_id']; ?>" class="form-control"  placeholder="Loan Amount">
                      <input type="hidden" name="member_id" readonly  value="<?php echo $roww['borrower_id']; ?>" class="form-control"  placeholder="Loan Amount">
                      <input type="hidden" name="member_loan_id" readonly  value="<?php echo $rows['loan_id'];  ?>" class="form-control"  placeholder="Loan Amount">
                    </div>
                  </div>
				  <div class="form-group row">
                    <label for="inputEmail4" class="col-sm-4 col-form-label">Amount:</label>
                    <div class="col-sm-3">
                      <input type="text"  name="amount" value="<?php echo $roww['amount'];  ?>" class="form-control"  placeholder="Loan Amount" id="paid_amount">
                    </div>
                    <label for="inputEmail3" class="col-sm-2 col-form-label">Fine:</label>
                     <div class="col-sm-3">
                      <input type="text"  name="fine" value="<?php if($roww['is_due2']>0){echo $roww['amount']*$roww['late_fee']/100;}else{echo '0';}  ?>" class="form-control" id="loan_fine"  placeholder="Loan Amount">
                    </div>
                     
                  </div>
          <?php
          if($roww['payment_sched'] < (new DateTime())->format('Y-m-d')){
            ?>
            <input type="hidden" id="disable_submit" value="1">
            <?php
          } else {
            ?>
            <input type="hidden" id="disable_submit" value="0">
            <?php
          }
          ?>
				  <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-4 col-form-label">Due Date:</label>
                    <div class="col-sm-8">
                      <input type="text" readonly name="duedate" value="<?php echo $roww['payment_sched'];  ?>" class="form-control"  placeholder="Loan Amount">
                    </div>
                  </div>
				   <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-4 col-form-label">Status:</label>
                    <div class="col-sm-8">
                      <input type="text" readonly name="status" value="<?php echo $roww['status'];  ?>" class="form-control"  placeholder="Loan Amount">
                    </div>
                  </div>
				    <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-8 col-form-label"></label>
                    <div class="col-sm-4">
                      <input type="submit" name="botton_save" id="submit_payment" class="form-control btn-primary">
                    </div>
                  </div>
                </div>
            </div>
            </div>
          </div>
		  
        </div>
		 </form>
        <!-- /.modal-dialog -->
      </div>
      
<div class="modal fade" id="modal-movepayment">
<form class="form-horizontal" method="post">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header bg-primary ">
              <h4 class="modal-title"><span style="color:white"> MOVE LOAN PAYMENT  </span></h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
			<?php
$payid=$rows['next_payment_id'];			   
			   $pay="select * from lend_payments where payment_number='$payid'";
			   $query=mysqli_query($con,$pay);
			   $roww=mysqli_fetch_array($query);
			   ?> 
            <div class="modal-body">
             <div class="card card-info">
                <div class="card-body">
                  <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-4 col-form-label">Payment:</label>
                    <div class="col-sm-2">
                      <input type="text" name="payment_idi" readonly  value="<?php echo $rows['next_payment_id']; ?>" class="form-control"  placeholder="Loan Amount">
                    </div>
                  </div>
				  <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-4 col-form-label">Amount:</label>
                    <div class="col-sm-8">
                      <input type="text" readonly name="" value="<?php echo $rows['loan_amount_term'];  ?>" class="form-control"  placeholder="Loan Amount">
                    </div>
                  </div>
				  <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-4 col-form-label">Status:</label>
                    <div class="col-sm-8">
                      <input type="text" readonly name="" value="<?php echo $roww['status'];  ?>" class="form-control"  placeholder="Loan Amount">
                    </div>
                  </div>
				  <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-4 col-form-label">Due Date:</label>
                    <div class="col-sm-8">
                      <input type="text" readonly name="" value="<?php echo $roww['payment_sched'];  ?>" class="form-control"  placeholder="Loan Amount">
                    </div>
                  </div>
				  <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-4 col-form-label">Move-in Date:</label>
                    <div class="col-sm-8">
                      <input type="date"  name="datemov"  class="form-control"  placeholder="Loan Amount">
                    </div>
                  </div>
				    <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-4 col-form-label">Note:</label>
                    <div class="col-sm-8">
					  <textarea class="form-control" name="notes"></textarea>
                    </div>
                  </div>
				    <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-8 col-form-label"></label>
                    <div class="col-sm-4">
                      <input type="submit" name="botton_move" class="form-control btn-primary">
                    </div>
                  </div>
                </div>
            </div>
            </div>
          </div>
		  
        </div>
		 </form>
        <!-- /.modal-dialog -->
      </div>
  
  <!-- /.content-wrapper -->
  <?php include('./footer.php'); ?>
<script>
$(document).ready(function() {
  setTimeout(function(){
    if($("#disable_submit").val() == 1){
      $('#submit_payment').prop("disabled", true);
    }

    if($("#paid_amount").val() <= 0){
      $('#submit_payment').prop("disabled", true);
    }
  }, 100);

  $("#loan_fine,#paid_amount").blur(function(e){
    if(($("#disable_submit").val() == 1 && $(this).val() <= 0) || $("#paid_amount").val() <= 0){
      $('#submit_payment').prop("disabled", false);
    } else if($("#disable_submit").val() == 1 && $("#paid_amount").val() > 0){
      $('#submit_payment').prop("disabled", false);
    } else {
      $('#submit_payment').prop("enabled", false);
    }
  });
    
    // $('#example').DataTable();

} );
</script>
</body>
</html>

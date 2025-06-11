<?php include('DBController.php');
$membe_id=$_SESSION['acc'];
$bank_acc=$_GET['code'];
?>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Dashboard</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
  <!-- Morris chart -->
  <link rel="stylesheet" href="bower_components/morris.js/morris.css">
  <!-- jvectormap -->
  <link rel="stylesheet" href="bower_components/jvectormap/jquery-jvectormap.css">
  <!-- Date Picker -->
  <link rel="stylesheet" href="bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="bower_components/bootstrap-daterangepicker/daterangepicker.css">
  <!-- bootstrap wysihtml5 - text editor -->
  <link rel="stylesheet" href="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
<script>
function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}

function showUser(str) {
  if (str=="") {
    document.getElementById("period").value="";
    return;
  }
  var xmlhttp=new XMLHttpRequest();
  xmlhttp.open("GET","membership/getloantype.php?q="+str,true);
  xmlhttp.send();
  xmlhttp.onreadystatechange=function() {
    if (this.readyState==4 && this.status==200) {
		 var r= this.responseText.split("|");
           document.getElementById("period").value = r[0];
           document.getElementById("percent").value = r[1];
           
    }
  }
  
}

function myFunction() {
  var checkBox = document.getElementById("limiticheck");
  var amount = document.getElementById("limiaount").value;
  var principal = document.getElementById("principal").value;
  if (checkBox.checked == true){
    document.getElementById("principal").value=amount;
  } else {
     document.getElementById("principal").value="";
  }
}
</script>
<style>
.alert {
  padding: 20px;
  background-color: #f44336;
  color: white;
  opacity: 1;
  transition: opacity 0.6s;
  margin-bottom: 15px;
}

.alert.success {background-color: #4CAF50;}
.alert.info {background-color: #2196F3;}
.alert.warning {background-color: #ff9800;}

.closebtn {
  margin-left: 15px;
  color: white;
  font-weight: bold;
  float: right;
  font-size: 22px;
  line-height: 20px;
  cursor: pointer;
  transition: 0.3s;
}

.closebtn:hover {
  color: black;
}
</style>
 
  </head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

 <?php include('header.php'); ?>
  <!-- Left side column. contains the logo and sidebar -->
  
  <?php include('menu.php'); ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Dashboard        <small>Overall Information on Single Screen</small>
      </h1>
      <ol class="breadcrumb">
        <li class="active"><i class="fa fa-dashboard"></i> Home</li>
      </ol>
       <?php 
  if(isset($_POST['botton_save'])){
	  $type=$_POST['interest'];
	  $lamount=$_POST['principal']; 
	  $date=$_POST['date'];
	  $sqi="SELECT * FROM `loan_type` where id='$type'";
	  $query=mysqli_query($con,$sqi);
	  $rowu=mysqli_fetch_array($query);
	  $interest=$rowu['interest'];
	  $late_fee=$rowu['late_fee'];
	  $lname=$rowu['lname'];
	  $loan_amount_interest=(int)$lamount*(int)$interest/100;
	  $terms=$rowu['terms'];
	  $loan_amount_total=(int)$lamount+(int)$loan_amount_interest;
	  $loan_amount_term=(int)$lamount/(int)$terms;
	  $frequency=$rowu['frequency'];
$sql = "INSERT INTO `member_loans` (`id`, `member_id`, `loan_id`, `status`, `loan_date`, `loan_amount`, `loan_amount_interest`, `loan_amount_term`, `loan_amount_total`, `next_payment_id`, `rdate`) 
VALUES (NULL, '$membe_id', '$type', 'ACTIVE', '$date', '$lamount', '$loan_amount_interest', '$loan_amount_term', '$loan_amount_total', '0', CURRENT_TIMESTAMP)";
mysqli_query($con,$sql) or die(mysqli_error($con));
$last=mysqli_insert_id($con);
$sql2 = "INSERT INTO `member_loan_settings` (`id`, `loan_id`, `member_loan_id`, `lname`, `interest`, `terms`, `frequency`, `late_fee`) 
VALUES (NULL,'$last','$membe_id','$lname', '$interest','$terms', '$frequency','$late_fee')";
mysqli_query($con,$sql2);
 for($exe = 0; $exe < $terms; $exe++ )
    {
$sql = "SELECT max(payment_number) as nid FROM `lend_payments`";	
$aa=mysqli_query($con,$sql);
$cou=mysqli_fetch_array($aa);
$payment=$cou['nid']+1;	

$frequency = 30*$exe;
			$newdate = strtotime ('+'.$frequency.' day', strtotime($date)) ;
			//check if payment date landed on weekend
			//if Sunday, make it Monday. If Saturday, make it Friday
			if(date('D', $newdate) == 'Sun') {
				$newdate = strtotime('+1 day', $newdate) ;
			} elseif(date ('D' , $newdate) == 'Sat') {
				$newdate = strtotime('-1 day', $newdate) ;
			}
			
			$newdate = date('Y/m/d', $newdate);
$sql1= "INSERT INTO `lend_payments` (`id`, `borrower_id`, `borrower_loan_id`, `payment_sched`, `payment_number`, `amount`, `status`, `rdate`) 
VALUES (NULL, '$membe_id', '$last', '$newdate', '$payment', '$loan_amount_term', 'UNPAID', CURRENT_TIMESTAMP)";
mysqli_query($con,$sql1);		
	}
	$check_nextpyt="select * from lend_payments where borrower_loan_id='$last' AND status='UNPAID' order by payment_sched";
	$query=mysqli_query($con,$check_nextpyt);
	$rows=mysqli_fetch_array($query);
	$iid=$rows['payment_number'];
	$updta="UPDATE `member_loans` SET `next_payment_id` = '$iid' WHERE `member_loans`.`id` ='$last'";
	mysqli_query($con,$updta);
	echo "<div class='alert alert-success' role='alert'>
 Loan Application successfull aplied !!!!!
</div>";
	
header("location:member_info.php?id=$membe_id");
  }
  
					  $tot=0;
					  $num=0;
					  $month=0;
					   $sql = "SELECT sum(sav_amount) as sam FROM `saving`  where member_id='$membe_id' AND year=YEAR(NOW())";
					   $quer=mysqli_query($con,$sql);
					  $row=mysqli_fetch_array($quer);
						   $tot=$row['sam'];
  ?>
    </section><br/>
    <div class="col-md-12">
      <!-- ********** ALERT MESSAGE START******* -->
       <div class="col-md-12">
      <!-- ********** ALERT MESSAGE START******* -->
        
     </div>       <!-- ********** ALERT MESSAGE END******* -->
     </div>
    <section class="content">
      <div class="row">
        <!-- ********** ALERT MESSAGE START******* -->
          <div class="col-md-12">
      
     </div>            <!-- ********** ALERT MESSAGE END******* -->
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header ">
              <h3 class="box-title">&nbsp;</h3>
               <?php 
$sql="select * from member where id='$membe_id'";
$query=mysqli_query($con,$sql);
$rows=mysqli_fetch_array($query);
			   ?>
     </div>
     <div class="box-body">
     <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="activity">
                    <!-- Post -->
                    <div class="post">
                      <div class="user-block">
                        <span class="username">
                
                        </span>
                      </div>
                        
                    </div>
                    <div class="post clearfix">
                    
<div class="col-md-12 pull-right" style="border:1px solid #1E90FF;">
    <div>
 <table id="example2" class="table table-bordered table-hover">
                  <thead>
                    <tr>
                      <th style="width: 10px">#</th>
                      <th>Date </th>
                      <th>saving ref #</th>
                      <th>From / To</th>		
                      <th>Income Fund</th>		
                      <th>Ledger Code</th>		
                      <th>Expences</th>		
                      <th>Income</th>		
                      <th>Balance</th>		
                   		
					</tr>
                  </thead>
                  <tbody>
				  <?php 
				  $id=0;
				  
				  $sql="select *,inf.name as bank, at.id as ids, inf.name as fund, lcode.name as legder from account_transaction at inner join income_fund inf on at.income_fund=inf.fin_code inner join ledger_code lcode on at.ledger_code=lcode.code  where account_code='$bank_acc' order by at.id";
				  $query=mysqli_query($con,$sql); 
				  while($row=mysqli_fetch_array($query)){
					  $id=$row['ids'];
				  ?>
                    <tr <?php if($row['updated_in']==1){ echo "bgcolor='red'";}?>>
					<th style="width: 10px"><a href=""><?php echo "#".$id; ?></a></th>
                      <td><?php echo $row['date']; ?></td>
                      <td><?php echo $row['saving_ref']; ?></td>
                      <td><?php echo $row['from_to']; ?></td>
                      <td><?php echo $row['fund']; ?></td>
                      <td><?php echo $row['legder']; ?></td>
                      <td>
					  <?php 
					  if($row['expences']==0){
					  
					  }else{
						  echo "- RF ".$row['expences']; 
					  }
					  ?>
					   </td>
                      <td><?php 
					  if($row['income']==0){
					  
					  }else{
						  echo "RF ".$row['income']; 
					  }
					  ?></td>
                      <td><?php echo "RF ".$row['balance']; ?></td>
				 
                    </tr>
					   <tr>
					  <th colspan="10"></th>
                                    
                    </tr>
				  <?php } ?>
                  </tbody>
				  <footer>
				   <tr>
					  <th colspan="10"><a href="#">Reconcile</a></th>          
                    </tr>
				  </footer>
                </table>
 
 
 
 
 
 
 
 
 
 
 
 
 

</div>
             </div>					 
           </div>
         </div>
       </div>
      </div>
     </div>
    </div>
   </div>
 </div>
          </div>
        </div>
      </div>
    </section>

  </div>
 
  <!-- /.content-wrapper -->
  <?php include('footer.php'); ?>

  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

<!-- jQuery 3 -->
<script src="bower_components/jquery/dist/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="bower_components/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Bootstrap 3.3.7 -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- Morris.js charts -->
<script src="bower_components/raphael/raphael.min.js"></script>
<script src="bower_components/morris.js/morris.min.js"></script>
<!-- Sparkline -->
<script src="bower_components/jquery-sparkline/dist/jquery.sparkline.min.js"></script>
<!-- jvectormap -->
<script src="plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<!-- jQuery Knob Chart -->
<script src="bower_components/jquery-knob/dist/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="bower_components/moment/min/moment.min.js"></script>
<script src="bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
<!-- datepicker -->
<script src="bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Slimscroll -->
<script src="bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="dist/js/pages/dashboard.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>
<script language="JavaScript">
function calculate() {
    // Get the user's input from the form. Assume it is all valid.
    // Convert interest from a percentage to a decimal, and convert from
    // an annual rate to a monthly rate. Convert payment period in years
    // to the number of monthly payments.
    var principal = document.loandata.principal.value;
    var interest = document.loandata.percent.value;
    var payments = document.loandata.years.value;

    // Now compute the monthly payment figure, using esoteric math.
    var x = Math.pow(1 + interest, payments);
    var monthly = (principal/payments);

    // Check that the result is a finite number. If so, display the results
    if (!isNaN(monthly) && 
        (monthly != Number.POSITIVE_INFINITY) &&
        (monthly != Number.NEGATIVE_INFINITY)) {
        document.loandata.payment.value = round(monthly);
        document.loandata.total.value = round(monthly * payments);
        document.loandata.totalinterest.value = (principal*5/100);
    }
    else {
        document.loandata.payment.value = "";
        document.loandata.total.value = "";
        document.loandata.totalinterest.value = "";
    }
}
// This simple method rounds a number to two decimal places.
function round(x) {
  return Math.round(x*100)/100;
}

$(document).ready(function() {
    $('#example').DataTable();
} );
</script>
</body>
</html>

<?php include('../DBController.php');
$membe_id=$_GET['id'];
$accloan = "SELECT sum(amount) as gt FROM `lend_payments` where borrower_id='$membe_id' AND  status='UNPAID'";
					   $quer=mysqli_query($con,$accloan);
					   $row0=mysqli_fetch_array($quer);
 $sacccapita = "SELECT sum(s.amount) as gtotal1
                       FROM `capital_share` s inner join member m on s.member_id=m.id
                       where s.member_id='$membe_id' ORDER BY s.date DESC";
					   $quer2=mysqli_query($con,$sacccapita);
					   $row1=mysqli_fetch_array($quer2);
$saving_sql = "SELECT sum(s.sav_amount) as gtotal2 
                       FROM `saving` s inner join member m on s.member_id=m.id 
                       where s.member_id='$membe_id' ORDER BY s.year DESC";
					   $quer3=mysqli_query($con,$saving_sql);
					   $row2=mysqli_fetch_array($quer3);
$profit_sql = "SELECT sum(amount) as prit FROM `profite` where m_id='$membe_id'";
					   $querprofile=mysqli_query($con,$profit_sql);
					   $profi=mysqli_fetch_array($querprofile);
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
	  $loan_amount_total=(int)$lamount;
	  $loan_amount_term=(int)$lamount/(int)$terms;
	  $frequency=$rowu['frequency'];
$sql = "INSERT INTO `member_loans` (`id`, `member_id`, `loan_id`, `status`, `loan_date`, `loan_amount`, `loan_amount_interest`, `loan_amount_term`, `loan_amount_total`, `next_payment_id`, `rdate`) 
VALUES (NULL, '$membe_id', '$type', 'ACTIVE', '$date', '$lamount', '$loan_amount_interest', '$loan_amount_term', '$loan_amount_total', '0', CURRENT_TIMESTAMP)";
mysqli_query($con,$sql) or die(mysqli_error($con));
//echo $loan_amount_interest ;
$last=mysqli_insert_id($con);
$sql2 = "INSERT INTO `member_loan_settings` (`id`, `loan_id`, `member_loan_id`, `lname`, `interest`, `terms`, `frequency`, `late_fee`) 
VALUES (NULL,'$last','$membe_id','$lname', '$interest','$terms', '$frequency','$late_fee')";
mysqli_query($con,$sql2);

if($terms==1){
$sql = "SELECT max(payment_number) as nid FROM `lend_payments`";	
$aa=mysqli_query($con,$sql);
$cou=mysqli_fetch_array($aa);
$payment=$cou['nid']+1;	
$sql1= "INSERT INTO `lend_payments` (`id`, `borrower_id`, `borrower_loan_id`, `payment_sched`, `payment_number`, `amount`, `status`, `rdate`) 
VALUES (NULL, '$membe_id', '$last', '$date', '$payment', '0', 'UNPAID', CURRENT_TIMESTAMP)";
mysqli_query($con,$sql1);
	$check_nextpyt="select * from lend_payments where borrower_loan_id='$last' AND status='UNPAID' order by payment_sched ASC";
	$query=mysqli_query($con,$check_nextpyt);
	$rows=mysqli_fetch_array($query);
	$iid=$rows['payment_number'];
	$updta="UPDATE `member_loans` SET `next_payment_id` = '$iid' WHERE `member_loans`.`id` ='$last'";
	mysqli_query($con,$updta);
}else{
 for($exe = 0; $exe < $terms; $exe++){
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
$newdate = date('Y-m-d', $newdate);
$sql1= "INSERT INTO `lend_payments` (`id`, `borrower_id`, `borrower_loan_id`, `payment_sched`, `payment_number`, `amount`, `status`, `rdate`) 
VALUES (NULL, '$membe_id', '$last', '$newdate', '$payment', '0', 'UNPAID', CURRENT_TIMESTAMP)";
mysqli_query($con,$sql1);		
	}}
	$check_nextpyt="select * from lend_payments where borrower_loan_id='$last' AND status='UNPAID' order by payment_sched";
	$query=mysqli_query($con,$check_nextpyt);
	$rows=mysqli_fetch_array($query);
	$iid=$rows['payment_number'];
	$updta="UPDATE `member_loans` SET `next_payment_id` = '$iid' WHERE `member_loans`.`id` ='$last'";
mysqli_query($con,$updta);
header("location:member_info.php?id=$membe_id");
  }
  if(isset($_POST['fee_save'])){
$next_id=111;
$me_id=$_POST['me_id'];
$member_fee=$_POST['member_fee'];
$month=date('m');
$year=date('Y');
$sql = "INSERT INTO `interest` (`id`, `loan_interest`, `saving_overdu`, `membership_fee`, `ref_id`, `desciption`, `month`, `year`, `done_at`) 
        VALUES (NULL, '0', '0', '$member_fee', '$me_id', 'overdue fine $next_id', '$month', '$year', CURRENT_TIMESTAMP)";
  if(mysqli_query($con,$sql)){
$sql2 = "UPDATE `member` SET `is_new` = '0' WHERE `member`.`id` = '$me_id'";
   mysqli_query($con,$sql2);   
  }
header("location:member_info.php?id=$membe_id");	  
  }
    if(isset($_POST['capital_save'])){
 $me_id=$_POST['me_id'];	  
 $capital_fee=$_POST['amount'];
 $month=$_POST['ukwezi'];
 $year=$_POST['year'];
 $sql = "INSERT INTO `capital_share` (`id`, `member_id`, `amount`,`month`,`year`, `date`) VALUES (NULL, '$me_id', '$capital_fee','$month','$year', CURRENT_TIMESTAMP)";
  mysqli_query($con,$sql);
header("location:member_info.php?id=$membe_id");	  
  }

  if(isset($_POST['saving_save'])){
	  $membe_id=$_POST['me_id'];
	  $month=$_POST['ukwezi'];
	  $year=$_POST['year'];
	  $amonh=$_POST['amoun'];
	  $fine=$_POST['fine'];
    $sql1 = "INSERT INTO `saving` (`id`, `member_id`, `sav_amount`, `month`, `year`, `fine`, `done_at`) VALUES (NULL, '$membe_id', '$amonh', '$month', '$year', $fine, CURRENT_TIMESTAMP)";
	  if(mysqli_query($con,$sql1)){
	    $next_id=mysqli_insert_id($con);
	    if($fine > 0){
        $sql = "INSERT INTO `interest` (`id`, `loan_interest`, `fine_overdue`, `saving_overdu`, `membership_fee`, `ref_id`, `desciption`, `month`, `year`, `done_at`, `loan_ref`) VALUES (NULL, '0', '0', '$fine', '0', '$next_id', 'overdue fine $next_id', '$month', '$year', CURRENT_TIMESTAMP, '$next_id')";
        // die($sql);
        mysqli_query($con,$sql);	    
	    }   
      $acc_name=$_POST['bank_account'];
      $sdate=date("Y/m/d");
      $voucher=$month.rand(0,999);
      $vendor=$_POST['me_name'];
      $ledger=41000;
      $income_code=41;
      $income=$amonh;
      $expen=0;
      $sql1="SELECT * FROM `financial_account` where id='$acc_name'";
      $sqlooo=mysqli_query($con,$sql1);
      $rwo=mysqli_fetch_array($sqlooo);
      $balance=$rwo['Open_balance'];
      $balance+=$amonh;
      $note="Monthly Savings";
      $lastid=mysqli_insert_id($con);
	  }  
    header("location:member_info.php?id=$membe_id");	  
  }
  ?>  
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Member | List</title>
<!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="../bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="../bower_components/Ionicons/css/ionicons.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="../bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="../dist/css/skins/_all-skins.min.css">
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
  xmlhttp.open("GET","getloantype.php?q="+str,true);
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
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
   </head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

 <?php include('../header.php'); ?>
  <?php include('../menu.php'); ?>
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       <a href="member_info.php"> Loan Information </a> <small> Details </small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home </a></li>
        <li class="active"><a href="member_info.php"> Member Details </a></li>
      </ol>
    </section>
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
               <?php 
$sql="select * from member where id='$membe_id'";
$query=mysqli_query($con,$sql);
$rows=mysqli_fetch_array($query);
			   ?>
     </div>
            <div class="box-body">
             <div class="container-fluid">
        <div class="row">
          <div class="col-md-3">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                        <img class="profile-user-img img-fluid img-circle"
                       src= "images/profile-default.png" alt="User profile picture">
                       
                </div>

                <h3 class="profile-username text-center"><?php echo $rows['fname']." ".$rows['lname']; ?></h3>
                <p class="text-muted text-center"><?php echo $rows['job_title']; ?></p>
				<?php if($rows['is_new']==0){?>
				<a class="btn btn-warning btn-flat btn-sm" data-toggle="modal" data-target="#modal-upload_photo"><i class="fa fa-plus"></i> Photo</a>
                <a class="btn btn-success btn-flat btn-sm" data-toggle="modal" data-target="#modal-saving"><i class="fa fa-plus"></i> Savings </a>
				<a class="btn btn-danger btn-flat btn-sm" data-toggle="modal" data-target="#modal-default"><i class="fa fa-plus"></i> Credit </a>
				<a class="btn btn-primary btn-flat btn-sm" data-toggle="modal" data-target="#modal-captal"><i class="fa fa-plus"></i> Share </a>
				<?php }else if($rows['member_fee']!=0 && $rows['is_new']==1){ ?>
				
				<a class="btn btn-warning btn-flat btn-sm" data-toggle="modal" data-target="#modal-upload_photo"><i class="fa fa-plus"></i>Photo</a>
				<a class="btn btn-success btn-flat btn-sm" data-toggle="modal" data-target="#modal-saving"><i class="fa fa-plus"></i> Savings </a>
				<a class="btn btn-danger btn-flat btn-sm" data-toggle="modal" data-target="#modal-default"><i class="fa fa-plus"></i> Credit </a>
				<a class="btn btn-primary btn-flat btn-sm" data-toggle="modal" data-target="#modal-captal"><i class="fa fa-plus"></i> Share </a>
				<?php } ?>
			
				<?php if($rows['is_new']==1 && $rows['member_fee']==0){?>
				<a class="btn btn-primary btn-flat btn-sm" data-toggle="modal" data-target="#modal-share"><i class="fa fa-plus"></i> Account Activation </a>
				<?php } ?>
              </div>
			  <div class="card card-primary">
                <!-- /.card-header -->
              <div class="card-body">
			   <hr>
                <strong><i class="fa fa-plus mr-1"></i> Personal Info </strong>
				<hr>
                <p class="text"> First Name : <?php echo $rows['fname'];  ?> </p>
                <p class="text"> Last Name : <?php echo $rows['lname'];  ?> </p>
                <p class="text"> Middle Name : <?php echo $rows['mi'];  ?> </p>
                <p class="text"> Date of Birth : <?php echo $rows['birth_date'];  ?> </p>
                <p class="text"> Civil Status : <?php echo $rows['civil_status'];  ?> </p>
                <hr>
                <strong><i class="fa fa-plus mr-1"></i> Contact Info</strong>
                <hr>
                <p class="text"> Address : <?php echo $rows['address'];  ?> </p>
                <p class="text"> Phone / Cellphone : <?php echo $rows['phone_cell'];  ?> </p>
                <p class="text"> Email : <?php echo $rows['email'];  ?> </p>
                <hr>
                <strong><i class="fa fa-plus mr-1"></i> Current Employment Info </strong>
                <hr>
                <p class="text"> Employment Status : <?php echo $rows['employment_status'];  ?> </p>
                <p class="text"> Company : <?php echo $rows['company'];  ?> </p>
                <p class="text"> Job Title : <?php echo $rows['job_title'];  ?> </p>
                <p class="text"> Monthly Income : <?php echo $rows['income'];  ?> Frws</p>
              </div>
              <!-- /.card-body -->
            </div>
              <!-- /.card-body -->
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
                        <div class="row">
  <div class="col-sm-3">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Capital Balance</h5>
        <a href="#" class="btn btn-primary"><?php echo number_format(ceil($row1['gtotal1']), 2); ?> Frws</a>
      </div>
    </div>
  </div>
  <div class="col-sm-3">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Savings Balance</h5>
        <a href="#" class="btn btn-primary"><?php echo number_format(ceil($row2['gtotal2']), 2); ?> Frws</a>
      </div>
    </div>
  </div>
   <div class="col-sm-3">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Active Loan Balance</h5>
        <a href="#" class="btn btn-primary"><?php echo number_format(ceil($row1['gt']??0), 2); ?> Frws</a>
      </div>
    </div>
  </div>
   <div class="col-sm-3">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Earnings per share</h5>
        <a href="#" class="btn btn-primary"><?php echo number_format(ceil($profi['prit']),2); ?> Frws</a>
      </div>
    </div>
  </div>
</div>
                      </div>
                       
                    </div>
                    <div class="post clearfix">
	<div class="col-md-12">
	    
            <div class="box-body">
               
			<div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title" style="color:#008080"> LOAN STATEMENT  </h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body ">
              <ul class="products-list product-list-in-box">
              
                      <table class="table table-bordered table-responsive">
                        <tr>
                          <td>Sl.No</td>
                          <td>Loan</td>
                          <td>Loan Amount</td>
                          <td>Period( Month )</td>
                          <td>Status</td>
                          <td>Action</td>
                        </tr>
                       <tbody>
                   <?php 
					   $sql = "SELECT *, ml.id as ididi, CONCAT(lt.lname,' - ',lt.interest,'%') as name 
					   FROM `member_loans` ml inner join loan_type lt on lt.id=ml.loan_id 
					   where member_id='$membe_id' AND ml.president=1 AND ml.accountant=1 AND ml.reject=0 ";
					   $quer=mysqli_query($con,$sql);
					   while($row=mysqli_fetch_array($quer)){ ?>
                        <tr style="color:<?php if($row['status']=="CLOSED"){echo "red";}else{echo "green";}?>">
                          <td><a href="Loan_info.php?loan_id=<?php echo $row['ididi'];?>"># - <?php echo $row['ididi']; ?></a></td>
                          <td><?php echo $row['name']; ?></td>
                          <td><?php echo number_format(ceil($row['loan_amount']), 2); ?>  Frw (s)</td>
                          <td><?php echo $row['terms']; ?> Month( s )</td>
                          <td><?php echo $row['status']; ?> </td>
                          <td><a href="#">Edit</a> | <a href="#">Delete</a></td>
                        </tr>
                    <?php } ?>
                       </tbody>
					   </table>
                
               
              </ul>
            </div>
            <!-- /.box-body -->
          
          </div>
          <!-- /.box -->
        </div>
		<!-- / Savings table-->	
					<div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title" style="color:#008080"> CAPITAL SHARES ACCOUNT </h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body ">
              <ul class="products-list product-list-in-box">
                      <table class="table table-bordered table-responsive">
                        <tr>
                          <td>Sl.No</td>
                          <td>Member Name</td>
                          <td>Amount</td>
                          <td>Date</td>
                         
                        </tr>
                       <tbody>
					  <?php 
					  $tot=0;
					  $num=0;
					  $month=0;
					  $sql = "SELECT *,sum(s.amount) as gtotal, CONCAT(fname,' ',lname) as firstname,m.id as m_idi FROM `capital_share` s inner join member m on s.member_id=m.id where s.member_id='$membe_id' ORDER BY s.date DESC";
					   $quer=mysqli_query($con,$sql);
					   while($row=mysqli_fetch_array($quer)){
						   $month=(int)$row['month'];
						   $tot+=$row['gtotal'];
						   $num++;
						 
					   ?>
                        <tr>
                          <td><a href="capital_share_info.php?m_idi=<?php echo $row['m_idi']; ?>">#-<?php echo $num; ?></a></td>
                          <td><?php echo $row['firstname']; ?></td>
                          <td><?php echo number_format(ceil($row['gtotal']), 2); ?>  Frw (s)</td>
                          <td><?php echo $row['date']; ?> </td>
                        
                          

    </tr>
  <?php } ?>
  <tr>
  <td colspan="6" style="color:#008080">Total: <?php echo number_format(ceil($tot),2)." Frws :".ucwords(convertNumberToWord($tot))."Rwandan francs, On ".$long[$month]." ".date('Y'); ?></td>
  </tr>
                       </tbody>
					   </table>
                
               
              </ul>
            </div>
            <!-- /.box-body -->
          
          </div>
          <!-- /.box -->
        </div>
			
			<div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title" style="color:#008080"> SAVING STATEMENT </h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body ">
              <ul class="products-list product-list-in-box">
                      <table class="table table-bordered table-responsive">
                        <tr>
                          <td>Sl.No</td>
                          <td>Member Name</td>
                          <td>Amount</td>
                          <td>Year</td>
                        </tr>
                       <tbody>
					  <?php 
					  $tot=0;
					  $num=0;
					  $month=0;
					  $sql = "SELECT *,sum(s.sav_amount) as gtotal, CONCAT(fname,' ',lname) as firstname, s.member_id as m_idi  FROM `saving` s inner join member m on s.member_id=m.id where s.member_id='$membe_id' GROUP BY s.year ORDER BY s.year DESC";
					   $quer=mysqli_query($con,$sql);
					   while($row=mysqli_fetch_array($quer)){
						   $month=(int)$row['month'];
						   $tot+=$row['gtotal'];
						   $num++;
						   
					   ?>
                        <tr>
                          <td><a href="savings_info.php?m_id=<?php echo $row['member_id']; ?>&&year=<?php echo $row['year']; ?>">#-<?php echo $num; ?></a></td>
                          <td><?php echo $row['firstname']; ?></td>
                          <td><?php echo number_format(ceil($row['gtotal']), 2); ?>  Frw (s)</td>
                          <td><?php echo $row['year']; ?> </td>
    </tr>
  <?php } ?>
  <tr>
  <td colspan="6" style="color:#008080">Total: <?php echo number_format(ceil($tot),2)." Frws :".ucwords(convertNumberToWord($tot))."Rwandan francs, On ".$long[$month]." ".date('Y'); ?></td>
  </tr>
                       </tbody>
					   </table>
					   
					   
					   
		<!--SOCIAL SAVING STATEMENT-->	
		
		
		
		<div class="box-header with-border">
              <h3 class="box-title" style="color:#FF00FF"> SOCIAL SAVING STATEMENT </h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body ">
              <ul class="products-list product-list-in-box">
					   
 <table class="table table-bordered table-responsive">
                                            <tr>
                                              <td>Sl.No</td>
                                              <td>Member Name</td>
                                              <td>Amount</td>
                                              <td>Year</td>
                                              <td>Action</td>
                                              
                                              
                                            </tr>
                                          <tbody>
                                					  <?php 
                                					  $tot=0;
                                					  $num=0;
                                					  $month=0;
                                            $sql = "SELECT  a.amount AS gtotal,
                                                            CONCAT(fname,' ',lname) as firstname,
                                                            CONCAT(a.year, '-', IF(a.month<10,0,''), a.month) AS formatted_month,
                                                            b.id as m_idi,
                                                            a.month,
                                                            a.year,a.id
                                                            FROM sacial_saving AS a
                                                            INNER JOIN member AS b
                                                            ON a.m_id = b.id
                                                            WHERE a.m_id = '{$membe_id}'
                                                            ORDER BY formatted_month ASC
                                                            ";
                                            // echo $sql;
                                					  // $sql = "SELECT *,sum(s.sav_amount) as gtotal, CONCAT(fname,' ',lname) as firstname, s.member_id as m_idi  FROM `saving` s inner join member m on s.member_id=m.id where s.member_id='$membe_id' GROUP BY s.year ORDER BY s.year DESC";
                                					   $quer=mysqli_query($con,$sql);
                                					   while($row=mysqli_fetch_array($quer)){
                                						   $month=(int)$row['month'];
                                						   $tot+=$row['gtotal'];
                                						   $num++;
                                  					   ?>
                                              <tr>
                                                <td><?php echo $num; ?></td>
                                                <td><?php echo $row['firstname']; ?></td>
                                                <td><?php echo number_format(ceil($row['gtotal']), 2); ?>  Frw (s)</td>
                                                <td><?php echo $row['formatted_month']; ?> </td>
                                                <!--<td> <a href="delete_social.php?id=<?= $row['id'] ?>&m_id=<?= $row['m_idi'] ?>">Delete/Edit</a></td> -->
                                              </tr>
                                              <?php
                                            } 
                                            ?>
                                            <tr>
                                            <td colspan="6" style="color:#008080">Total: <?php echo number_format(ceil($tot),2)." Frws :".ucwords(convertNumberToWord($tot))."Rwandan francs" ?></td>
                                            </tr>
                                          </tbody>
                    					          </table>	   
					   
			<!--SOCIAL SAVING STATEMENT-->		   
					   
					   
					   
					   
					   
					   
					   
					   
					   
					   
					   
					   
					   
					   
					   
                
               
              </ul>
            </div>
            <!-- /.box-body -->
          
          </div>
          <!-- /.box -->
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
      </div>
    </section>
    </form>  </div>
<div class="modal fade" id="modal-default">
<form class="form-horizontal" name="loandata"  method="post">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header bg-primary ">
              <h4 class="modal-title"><span style="color:white"><i class="fa fa-plus"></i> NEW LOAN ( <?php echo $rows['fname']." ".$rows['lname']; ?> ) </span></h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
             <div class="card card-info">
                <div class="card-body">
				 <div class="form-group row">
               <div class="alert warning">
  <input type="checkbox" id="limiticheck" onclick="myFunction()" class="form-check-input" name="limiticheck"> - 
  <strong>Amortization Based On Saving :</strong> <?php echo number_format(ceil($tot*$loan_limity),2); ?>
  </div>
  <p><?php $limit=$tot/$loan_limity; ?> <span></span></p>
  <input type="hidden" name="limiaount" id="limiaount" value="<?php echo $tot*$loan_limity; ?>" class="form-control">
  </div>

 <div class="form-group row">
    <label for="inputPassword3" class="col-sm-8 col-form-label">
      Applied Date :
	</label>
	  <div class="col-sm-4">
      <input type="date" name="date" class="form-control" Required>
	  </div>
	    </div>
    <div class="form-group row">
    <label for="inputPassword3" class="col-sm-8 col-form-label">
      Amount of the loan (any currency):
	</label>
	  <div class="col-sm-4">
      <input type="text" name="principal" id="principal" required size="12" onchange="calculate();" class="form-control">
	  </div>
	    </div>
		 <div class="form-group row">
    <label for="inputPassword3" class="col-sm-8 col-form-label">
    Choose Loan Type:
	</label>
	<div class="col-sm-4">
	<select name="interest" onchange="showUser(this.value)" required class="form-control">
	<option></option>
	<?php 
	$sqi="SELECT * FROM `loan_type`";
	  $query=mysqli_query($con,$sqi);
	  while($rowu=mysqli_fetch_array($query)){
	  ?>
	  <option value="<?php echo $rowu['id']; ?>"><?php echo $rowu['lname']; ?></option>
	  <?php }?>
	</select>
	 </div>
	    </div>
		   <div class="form-group row">
    <label for="inputPassword3" class="col-sm-8 col-form-label">
       Annual percentage rate of interest:
	</label>
	  <div class="col-sm-4">
      <input type="text" name="percent" readonly id="percent"  required class="form-control">
	  </div>
	    </div>
		<div class="form-group row">
    <label for="inputPassword3" class="col-sm-8 col-form-label">
   Repayment period in years:
   </label>
   <div class="col-sm-4">
   <input type="text" name="years" readonly  size="12" id="period" onchange="calculate();" required class="form-control">
   </div></div>
   <div class="form-group row">
   <div class="col-sm-4">
      <input type="button" value="Compute" onclick="calculate();" required class="form-control btn-primary">
	  </div>
	  </div>
    Payment Information:
	
	 <div class="form-group row">
   <div class="col-sm-4">
   <input type="text" readonly name="payment" size="12" class="form-control">
   </div>
   <div class="col-sm-4">
   <input type="text" name="total" size="12" readonly class="form-control">
   </div>
   <div class="col-sm-4">
   <input type="text" name="totalinterest" readonly size="12" class="form-control">
   </div>
   </div>
   <hr>
               <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-8 col-form-label"></label>
                    <div class="col-sm-4">
                      <input type="submit" name="botton_save" class="form-control btn-primary">
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
	  
<div class="modal fade" id="modal-saving">
<form class="form-horizontal" method="post">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header bg-primary ">
              <h4 class="modal-title"><span style="color:white"><i class="fa fa-plus"></i> Member Savings Entry Form  </span></h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
             <div class="card card-info">
                <div class="card-body">
                  <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-6 col-form-label"><?php echo $rows['fname']." ".$rows['lname']; ?></label>
                    <div class="col-sm-2">
                      <input type="text" name="me_id" readonly class="form-control" value="<?php echo $rows['id']; ?>">
                      <input type="hidden" name="me_name" readonly class="form-control" value="<?php echo $rows['fname']." ".$rows['lname']; ?>">
                    </div>
					
                  </div>
                  <div class="form-group row">
                    <label for="inputPassword3" class="col-sm-3 col-form-label"> Date: </label>
					 <div class="col-sm-5">
				    <select size="1" name="ukwezi" class="form-control">
					<?php formMonth(); ?>
					</select>
                    </div>
					 <div class="col-sm-4">
				    <select size="1" name="year" class="form-control">
					<?php formYear(); ?>
					</select>
                    </div>
                  </div>
				   <div class="form-group row">
                    <label for="inputPassword3" class="col-sm-3 col-form-label"> Saving Amount: </label>
                    <div class="col-sm-4">
                     <input type="text" required name="amoun" onkeypress="return isNumber(event)"  class="form-control">
                    </div>
                  <label for="inputEmail3" class="col-sm-1 col-form-label">Fine:</label>
                     <div class="col-sm-4">
                     <input type="text" required name="fine" value="0" onkeypress="return isNumber(event)"  class="form-control">
                    </div>
                  </div>
                     <div class="form-group row">
                    <label for="inputPassword3" class="col-sm-3 col-form-label"> Account : </label>
                    <div class="col-sm-9">
                        <select name="bank_account" class="form-control">
                        <?php 
                        $sql = "SELECT * FROM `financial_account`";
                        $annn=mysqli_query($con,$sql);
                        while($roww=mysqli_fetch_array($annn)){
                        ?>
                        <option value="<?php echo $roww['id']; ?>"><?php echo $roww['Name'];?></option>
                        <?php } ?>
                        </select>
                    </div>
                  </div>
				  <hr>
				    <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-8 col-form-label"></label>
                    <div class="col-sm-4">
                      <input type="submit" name="saving_save" class="form-control btn-primary">
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

<div class="modal fade" id="modal-share">
<form class="form-horizontal" method="post">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header bg-primary ">
              <h4 class="modal-title"><span style="color:white"><i class="fa fa-plus"></i> Membership Fees & Capital share  </span></h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
             <div class="card card-info">
                <div class="card-body">
                  <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-8 col-form-label"><?php echo $rows['fname']." ".$rows['lname']; ?></label>
                    <div class="col-sm-2">
                      <input type="text" name="me_id" readonly class="form-control" value="<?php echo $rows['id']; ?>">
                    </div>
					<div class="col-sm-2">
                      <input type="text" name="amount" readonly class="form-control" value="<?php echo $rows['civil_status']; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputPassword3" class="col-sm-6 col-form-label"> Membership Fee </label>
                    <div class="col-sm-4">
                     <input type="text" name="member_fee" required class="form-control">
                    </div>
					 <div class="col-sm-2">
                     <input type="text" readonly name="amount" value="Frws"  class="form-control">
                    </div>
                  </div>
                  
				   
				    <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-8 col-form-label"></label>
                    <div class="col-sm-4">
                      <input type="submit" name="fee_save" class="form-control btn-primary">
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
      
      
      <div class="modal fade" id="modal-captal">
<form class="form-horizontal" method="post">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header bg-primary ">
              <h4 class="modal-title"><span style="color:white"><i class="fa fa-plus"></i> Upgrade Capital share  </span></h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
             <div class="card card-info">
                <div class="card-body">
                  <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-10 col-form-label"><?php echo $rows['fname']." ".$rows['lname']; ?></label>
                    <div class="col-sm-2">
                      <input type="text" name="me_id" readonly class="form-control" value="<?php echo $rows['id']; ?>">
                    </div>
				
                  </div>
             
				   <div class="form-group row">
                    <label for="inputPassword3" class="col-sm-4 col-form-label"> Add Capital Amount </label>
                    <div class="col-sm-6">
                     <input type="text" required name="amount"  class="form-control">
                    </div>
					 <div class="col-sm-2">
                     <input type="text" readonly name="fee" value="Frws"  class="form-control">
                    </div>
                  </div>
                       <div class="form-group row">
                    <label for="inputPassword3" class="col-sm-4 col-form-label"> Date: </label>
					 <div class="col-sm-4">
				    <select size="1" name="ukwezi" class="form-control">
					<?php formMonth(); ?>
					</select>
                    </div>
					 <div class="col-sm-4">
				    <select size="1" name="year" class="form-control">
					<?php formYear(); ?>
					</select>
                    </div>
                  </div>
				  <hr>
				    <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-8 col-form-label"></label>
                    <div class="col-sm-4">
                      <input type="submit" name="capital_save" class="form-control btn-primary">
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
      
      
      <div class="modal fade" id="modal-upload_photo">
      <form action="upload_photo.php" method="POST" enctype="multipart/form-data">
    <input type="file" name="image" accept="image/*" />
    <button type="submit">Upload</button>
        </div>
        </form>
      
      

  
  <!-- /.content-wrapper -->
  <?php include('../footer.php'); ?>
  <div class="control-sidebar-bg"></div>
</div>
<script src="../bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- DataTables -->
<script src="../bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="../bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<!-- SlimScroll -->
<script src="../bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="../bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../dist/js/demo.js"></script>
<!-- page script -->
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
    var monthly = Math.ceil(principal/payments);

    // Check that the result is a finite number. If so, display the results
    if (!isNaN(monthly) && 
        (monthly != Number.POSITIVE_INFINITY) &&
        (monthly != Number.NEGATIVE_INFINITY)) {
        document.loandata.payment.value = monthly;
        document.loandata.total.value = monthly * payments;
        document.loandata.totalinterest.value = (principal*interest/100);
    }
    else {
        document.loandata.payment.value = "";
        document.loandata.total.value = "";
        document.loandata.totalinterest.value = "";
    }
}
// This simple method rounds a number to two decimal places.
function round(x) {
  return round(x*100)/100;
}

$(document).ready(function() {
    $('#example').DataTable();
} );
</script>
</body>
</html>

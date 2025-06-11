<?php include('DBController.php');
$membe_id=$_SESSION['acc'];
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
    </section><br/>
    <div class="col-md-12">
      <!-- ********** ALERT MESSAGE START******* -->
       <div class="col-md-12">
      <!-- ********** ALERT MESSAGE START******* -->
        
     </div>       <!-- ********** ALERT MESSAGE END******* -->
     </div>
         <?php
         if($_SESSION['role']!=5){
        
         ?>
          <div class="col-md-6">
          <!-- Application buttons -->
          <div class="box box-danger">
           
            <div class="box-body">
              <a class="btn btn-app" href="<?php echo WEB_URL; ?>category/add.php">
              <i class="fa fa-tags text-green"></i>Loan Category</a>
              <a class="btn btn-app" href="<?php echo WEB_URL; ?>membership/list.php">
              <i class="fa fa-cube text-blue"></i> New Loan </a>
              <a class="btn btn-app" href="<?php echo WEB_URL; ?>membership/add_member.php">
              <i class="fa fa-user text-maroon"></i>New Member </a>
              <a class="btn btn-app" href="https:<?php echo WEB_URL; ?>user/user.php">
              <i class="fa fa-user text-green"></i> New User</a>
              <a class="btn btn-app" href="https:<?php echo WEB_URL; ?>membership/list.php">
              <i class="fa fa-calculator text-green"></i> New Saving</a>
				              
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->

        <div class="col-md-6">
          <!-- Application buttons -->
          <div class="box box-danger">
           
            <div class="box-body">
              <a class="btn btn-app" href="<?php echo WEB_URL; ?>membership/list.php">
                <i class="fa fa-calculator text-red"></i> Pay Loan
              </a>
              <a class="btn btn-app" href="<?php echo WEB_URL; ?>membership/list.php">
                <i class="fa fa-ravelry text-green"></i> Ongoing Loan</a>
              <a class="btn btn-app" href="<?php echo WEB_URL; ?>membership/list.php">
                <i class="fa fa-bandcamp text-aqua"></i>Overdue Loan </a>
              <a class="btn btn-app" href="<?php echo WEB_URL; ?>expenses/add.php">
                <i class="fa fa-star text-green"></i>Add Expences </a>
                
             
             
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
          
        

      
    <!-- Main content -->
    <section class="content">
      
       <!-- /.row -->
      <!-- Info boxes -->
      <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
           <a  href="<?php echo WEB_URL; ?>membership/list.php">
           <div class="info-box">
            <span class="info-box-icon bg-red"><i class="fa fa-users"></i></span>
            <div class="info-box-content">
<?php 
$sql ="SELECT id from member";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cust=$query->rowCount();
?>
              <span class="">Members</span>
              <span class="info-box-number"><?php echo htmlentities($cust);?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
           </a> 
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
           <a  href="<?php echo WEB_URL; ?>membership/list.php">           <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="fa fa-automobile"></i></span>
            <div class="info-box-content">
    <?php 
$sql ="SELECT sum(sav_amount) as total from saving WHERE  year = '$currentyear'";
$query = $dbh -> prepare($sql);
$query->execute();
$row = $query->fetch(PDO::FETCH_ASSOC);
$supp=$query->rowCount();
?>
              <span class="">Saving</span>
              <span class="info-box-number"><?php echo number_format($row["total"], 2); ?> Frws</span>
            </div>
            <!-- /.info-box-content -->
          </div>
            </a>           <!-- /.info-box -->
        </div>
        <!-- /.col -->
        
        <div class="col-md-3 col-sm-6 col-xs-12">
           <a  href="<?php echo WEB_URL; ?>membership/list.php"> <div class="info-box">
            <span class="info-box-icon bg-green"><i class="fa fa-suitcase"></i></span>

            <div class="info-box-content">
                    <?php 
$sql ="SELECT SUM(loan_amount) AS tot_loan
FROM member_loans
WHERE MONTH(loan_date) = '$currentmonth'
AND YEAR(loan_date) = '$currentyear' AND president=1 AND accountant=1 AND reject=0";
$query = $dbh -> prepare($sql);
$query->execute();
$results = $query->fetch(PDO::FETCH_ASSOC);
?>
              <span class="">Loan</span>
              <span class="info-box-number"><?php echo number_format($results['tot_loan'], 2);?> Frws</span>
            </div>
            <!-- /.info-box-content -->
          </div>
         </a>           <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
           <a  href="<?php echo WEB_URL; ?>membership/list.php"><div class="info-box">
            <span class="info-box-icon bg-orange"><i class="fa fa-sun-o"></i></span>

            <div class="info-box-content">
                    <?php 
$sql ="SELECT SUM(loan_amount_interest) AS toloan
FROM member_loans
WHERE MONTH(loan_date) = '$currentmonth'
AND YEAR(loan_date) = '$currentyear' AND president=1 AND accountant=1";
$query = $dbh -> prepare($sql);
$query->execute();
$results = $query->fetch(PDO::FETCH_ASSOC);
?>
              <span class="">Interest</span>
              <span class="info-box-number"><?php echo number_format($results['toloan'], 2);?> Frws</span>
            </div>
            <!-- /.info-box-content -->
          </div>
         </a>           <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <!-- fix for small devices only -->
        <div class="clearfix visible-sm-block"></div>

        <!-- /.col -->
        
      </div>
      <!-- /.row -->
     <div class="row">
   <div class="col-md-4">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Past Due Payments</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body ">
              <ul class="products-list product-list-in-box">
              
                      <table class="table table-bordered table-responsive">
                        <tr>
                          <td>Sl.No</td>
                          <td>Member Name</td>
                          <td>Amount</td>
                        </tr>
                       <tbody style="color:#ff0000">
						<?php
					  $iiid=0;
  $sql="SELECT * , DATEDIFF( NOW( ) , lp.payment_sched ) AS is_due, lp.status AS stsa, CONCAT( m.fname, ' ', m.lname ) AS namee
FROM lend_payments lp
INNER JOIN member_loans ml ON lp.borrower_id = ml.id
INNER JOIN member m ON m.id = lp.borrower_id
WHERE MONTH(lp.payment_sched ) < '$currentmonth'
AND YEAR(lp.payment_sched) = '$currentyear'
AND lp.status = 'UNPAID' AND ml.reject=0 AND ml.accountant=1 AND ml.president=1
ORDER BY lp.payment_number";
  $queue=mysqli_query($con,$sql);
  while($row=mysqli_fetch_array($queue)){
	  $iiid++;
	  $is_due=(int)$row['is_due'];
	  $stsa=$row['stsa'];      				
		?>
                                        <tr>
                          <td>1</td>
                          <td><?php echo $row['namee']; ?></td>
                          <td><?php echo $row['amount']; ?>  Frw (s)</td>
                        </tr>
  <?php } ?>
                       </tbody>
                      <tfoot>
                      <tr>
                        <td colspan="3" class="text-center"><a href="#" class="uppercase">View All</a></td>
                      </tr>
                    </tfoot>
                                      </table>
                
               
              </ul>
            </div>
            <!-- /.box-body -->
          
          </div>
          <!-- /.box -->
        </div>
		<div class="col-md-4">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Due Payments This Week</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body ">
              <ul class="products-list product-list-in-box">
              
                      <table class="table table-bordered table-responsive">
                        <tr>
                          <td>Sl.No</td>
                          <td>Member Name</td>
                          <td>Amount</td>
                        </tr>
                        <tbody style="color:#c32148">
						<?php
					  $iiid=0;
  $sql="SELECT * , DATEDIFF( NOW( ) , lp.payment_sched ) AS is_due, lp.status AS stsa, CONCAT( m.fname, ' ', m.lname ) AS namee
FROM lend_payments lp
INNER JOIN member_loans ml ON lp.borrower_id = ml.id
INNER JOIN member m ON m.id = lp.borrower_id
WHERE WEEK(lp.payment_sched) + YEAR(lp.payment_sched) = WEEK(NOW()) + YEAR(NOW()) 
AND lp.status = 'UNPAID' AND ml.reject=0 AND ml.accountant=1 AND ml.president=1
ORDER BY lp.payment_number";
  $queue=mysqli_query($con,$sql);
  while($row=mysqli_fetch_array($queue)){
	  $iiid++;
	  $is_due=(int)$row['is_due'];
	  $stsa=$row['stsa'];      				
		?>
                                        <tr>
                          <td>1</td>
                          <td><?php echo $row['namee']; ?></td>
                          <td><?php echo $row['amount']; ?>  Frw (s)</td>
                        </tr>
  <?php } ?>
                       </tbody>
                      <tfoot>
                      <tr>
                        <td colspan="3" class="text-center"><a href="#" class="uppercase">View All</a></td>
                      </tr>
                    </tfoot>
                                      </table>
                
               
              </ul>
            </div>
            <!-- /.box-body -->
          
          </div>
          <!-- /.box -->
        </div>
		<div class="col-md-4">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Due Payments Today</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body ">
              <ul class="products-list product-list-in-box">
              
                      <table class="table table-bordered table-responsive">
                        <tr>
                          <td>Sl.No</td>
                          <td>Member Name</td>
                          <td>Amount</td>
                        </tr>
                        <tbody style="color:#c32148">
						<?php
					  $iiid=0;
  $sql="SELECT * , DATEDIFF( NOW( ) , lp.payment_sched ) AS is_due, lp.status AS stsa, CONCAT( m.fname, ' ', m.lname ) AS namee
FROM lend_payments lp
INNER JOIN member_loans ml ON lp.borrower_id = ml.id
INNER JOIN member m ON m.id = lp.borrower_id
WHERE datediff(NOW(), lp.payment_sched)=0
AND lp.status = 'UNPAID' AND ml.reject=0 AND ml.accountant=1 AND ml.president=1
ORDER BY lp.payment_number";
  $queue=mysqli_query($con,$sql);
  while($row=mysqli_fetch_array($queue)){
	  $iiid++;
	  $is_due=(int)$row['is_due'];
	  $stsa=$row['stsa'];      				
		?>
                                        <tr>
                          <td>1</td>
                          <td><?php echo $row['namee']; ?></td>
                          <td><?php echo $row['amount']; ?>  Frw (s)</td>
                        </tr>
  <?php } ?>
                       </tbody>
                      <tfoot>
                      <tr>
                        <td colspan="3" class="text-center"><a href="#" class="uppercase">View All</a></td>
                      </tr>
                    </tfoot>
                                      </table>
                
               
              </ul>
            </div>
            <!-- /.box-body -->
          
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
        
        <!-- /.col -->
     </div>
     <?PHP } else{?>

    </section>
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
                       src="membership/images/profile-default.png" alt="User profile picture">
                </div>

                <h3 class="profile-username text-center"><?php echo $rows['fname']." ".$rows['lname']; ?></h3>
                <p class="text-muted text-center"><?php echo $rows['job_title']; ?></p>
				
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
                        <span class="username">
                        
                        </span>
                      </div>
                        
                    </div>
                    <div class="post clearfix">
                    
	<div class="col-md-12">
            <div class="box-body">
			<div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <?php
              $profit_sql = "SELECT sum(amount) as prit FROM `profite` where m_id='$membe_id'";
              $querprofile=mysqli_query($con,$profit_sql);
              $profi=mysqli_fetch_array($querprofile);
              ?>
              <h3> LOAN STATEMENT  |  <a class="btn btn-primary btn-flat btn-sm" href="loan_application.php"><i class="fa fa-plus"></i> Apply for Loan</a> </i>Individual interest</a> <a href="#" class="btn btn-primary"><?php echo number_format(ceil($profi['prit']),2);?> Frws</a></h3>
                
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
                        </tr>
                       <tbody>
  <?php 
					   $sql = "SELECT *, ml.id as ididi, CONCAT(lt.lname,' - ',lt.interest,'%') as name FROM `member_loans` ml inner join loan_type lt on lt.id=ml.loan_id where member_id='$membe_id'";
					   $quer=mysqli_query($con,$sql);
					   while($row=mysqli_fetch_array($quer)){
					   ?>
                          <tr style="color:<?php if($row['status']=="CLOSED"){echo "red";}?>">
                          <td><?php if($row['accountant']==1 && $row['president']==1){ ?>
                              <a href="membership/Loan_info.php?loan_id=<?php echo $row['ididi'];?>"># - <?php echo $row['ididi']; ?></a>
                              <?php }else{ ?>
                              <?php echo $row['ididi']; ?>
                              <?php } ?>
                              </td>
                          <td><?php echo $row['name']; ?></td>
                          <td><?php echo number_format(ceil($row['loan_amount']), 2); ?>  Frw (s)</td>
                          <td><?php echo $row['terms']; ?> Month( s )</td>
                          <td>
						  <?php 
						  IF($row['gran_accept']==null and $row['grant_two_accept']==null){
							 ?>

<span style="color:#008000	">waiting  </span>
<?php							 
							  
						  }else{
						  
						  if($row['accountant']==0 && $row['president']==0 && $row['reject']==0){?>
                        <span style="color:#FFCC00"> Pending </span>
                        <?php }elseif($row['accountant']==0 && $row['president']==1  && $row['reject']==0){ ?>
                        <span style="color:#FFCC00"> wait for Loan Committee  </span>
                        <?php }elseif($row['accountant']==1 && $row['president']==0  && $row['reject']==0){ ?>
                        <span style="color:#FFCC00"> wait for President(s)  </span>
                        <?php }elseif($row['accountant']==1 && $row['president']==1  && $row['reject']==0){ ?>
                        ?>
							 

<?php
                        <?php }elseif($row['accountant']==0 or $row['president']==0 && $row['reject']==1){ ?>
                        <span style="color:#FF0000">Rejected  </span>
						  <?php }} ?>
                         </td>
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
        
		<!-- / Savings table-->	
			
			<div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title" style="color:#008080"> SAVING STATEMENT <?php echo date("Y/m/d");?></h3>
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
					   $sql = "SELECT *,sum(s.sav_amount) as gtotal, CONCAT(fname,' ',lname) as firstname FROM `saving` s inner join member m on s.member_id=m.id where s.member_id='$membe_id' GROUP BY s.year ORDER BY s.year DESC";
					   $quer=mysqli_query($con,$sql);
					   while($row=mysqli_fetch_array($quer)){
						   $month=(int)$row['month'];
						   $tot+=$row['gtotal'];
						   $num++;
						 
					   ?>
                        <tr>
                          <td><a href="savings_info.php?m_id=<?php echo $membe_id; ?>&&year=<?php echo $row['year']; ?>">#-<?php echo $num; ?></a></td>
                          <td><?php echo $row['firstname']; ?></td>
                          <td><?php echo number_format(ceil($row['gtotal']), 2); ?>  Frw (s)</td>
                          <td><?php echo $row['year']; ?> </td>
                        </tr>
  <?php } ?>
  <tr>
  <td colspan="5" style="color:#008080">Total: <?php echo number_format(ceil($tot),2)." Frws :".ucwords(convertNumberToWord($tot))."Rwandan francs, On ".$long[$month]." ".date('Y'); ?></td>
  </tr>
                       </tbody>
					   </table>
					   
					   
					   
					   
					   
			<!-- SOCIAL SAVING STATEMT-->
			
		
		
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
			
		
			
			<!-- SOCIAL SAVING STATEMT-->
					   
					   
 <!--<li><a href="<?php echo WEB_URL; ?>sacial_saving_report.php"><i class="fa fa-list"></i> <span style="color:green"> Click here to check Social Saving Statement</span> <span style="color:orange">(You need to check your name in the report for each month)</span> </p></a></li>-->
               
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
    

    
    
    <?php } ?>
    
    
    <!-- /.content -->
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
</body>
</html>

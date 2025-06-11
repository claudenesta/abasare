<?php include('DBController.php');
			if(isset($_POST["social_save"])){
									$member= $_POST['member'];
								    $saving= $_POST['saving'];
								    $month= $_POST['month'];
								    $year= $_POST['year'];
								    $sql = "INSERT INTO `sacial_saving` (`id`, `m_id`, `amount`, `month`, `year`) VALUES (NULL, '$member', '$saving', '$month', '$year')";

				 MYSQLI_QUERY($con,$sql) OR DIE(MYSQLI_ERROR());
				 ?>
				 <meta http-equiv="refresh" content="0; URL=sacial_saving.php">
				 <?php 
			}
			
			if(isset($_POST['socialbtn'])){
			    $amount=$_POST['amount'];
			    $ref=$_POST['ref'];
			     $month= $_POST['month'];
				 $year= $_POST['year'];
			    $sql = "INSERT INTO `social_logs` (`id`, `saving_ref`, `amount`, `month`, `year`) VALUES (NULL, '$ref', '$amount', '$month', '$year')";
			    MYSQLI_QUERY($con,$sql);
               ?>
            <meta http-equiv="refresh" content="0; URL=sacial_saving.php">
            <?php } ?>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Member | List</title>
<!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">


  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  </head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

 <?php include('header.php'); ?>
  <!-- Left side column. contains the logo and sidebar -->
  
  <?php include('menu.php'); ?>

  <!-- Content Wrapper. Contains page content -->
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Member List <small>View/Search </small>
      </h1>
              <span><?php
        $viewfund="SELECT sum(amount) as fund FROM `sacial_saving`";
        $viewwithdraw="SELECT sum(amount) as withdr FROM `social_logs`";
        $depose=mysqli_query($con,$viewfund);
        $withdraw=mysqli_query($con,$viewwithdraw);
        $row1=mysqli_fetch_array($depose);
        $row2=mysqli_fetch_array($withdraw);
        ?>

<div class="row w-100">
        <div class="col-sm-6">
            <div class="card border-info mx-sm-1 p-1">
                <div class="text-info text-center mt-2"><h5>Social amount =:<?php echo number_format($row1['fund'],2); ?> Frws</h5></div>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="card border-success mx-sm-1 p-3">
                <div class="text-success text-center mt-2"><h5>Withdrown =: <?php echo number_format($row2['withdr'],1); ?> Frws</h5></div>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="card border-danger mx-sm-1 p-3">
                <div class="text-danger text-center mt-2"><h5>Balance =: <?php echo number_format($row1['fund']-$row2['withdr'],2); ?> Frws</h5></div>
            </div>
        </div>
    </div>
    </span>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Member List</li>
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
              <div class="pull-left">
                <a class="btn btn-block btn-info" data-toggle="modal" data-target="#modal-withdr"> <i class="fa fa-plus"></i> Withdraw </a>
              </div>
              <div class="box-tools">
                <a class="btn btn-block btn-info" data-toggle="modal" data-target="#modal-social"> <i class="fa fa-plus"></i> Deposit </a>
                
              </div>
                          </div>
            <!-- /.box-header -->
            <div class="box-body">
                
                
              <table id="example" class="table table-striped table-bordered" style="width:100%">
                <thead class="bg-primary ">
                <tr>
                  <th>No</th>
                  <th>Member Names</th>
                  <th>Amount</th>
                  <!-- <th>Month</th> -->
                  <!-- <th>Year</th> -->
                  <!-- <th>Action</th> -->
                </tr>
                </thead>
                <tbody>
				<?php
				$no=0;
				$sum=0;
$sql="SELECT *, CONCAT(m.fname,' ',m.lname) as fullname,
              SUM(ss.amount) AS total_social_saving,
              m.id AS member_id
              FROM sacial_saving ss 
              INNER JOIN member m 
              ON m.id=ss.m_id
              GROUP BY m.id
              ";
$query=mysqli_query($con,$sql);
while($row=mysqli_fetch_array($query)){
 $sum+=$row['total_social_saving'];   
    
$no++;
				?>
				  <tr>
				  <td><?php echo $no; ?></td>
                  <td>
                    <a href="./member_info.php?id=<?php echo $row['member_id']; ?>">
                      <?php echo $row['fullname']; ?>
                    </a>
                  </td>
                  <td style="text-align: right;"><?php echo number_format($row['total_social_saving']); ?></td>
                  <!-- <td><?php echo $long[$row['month']]; ?></td> -->
                  <!-- <td><?php echo $row['year']; ?></td> -->
                  <!-- <td><a href="#" data-toggle="modal" data-target="#modal-<?php echo $row['id']; ?>"> Edit </a> |  <a href="delete_member.php?id=<?php echo $row['id'];?>" onclick="return confirm('Are you sure you want to delete this Member?');">Delete</a></td> -->
                </tr>
                <?php } ?>
          </tbody>
          <tfoot>
              <tr>
                  <th></th>
                  <th></th>
                  <th style="text-align: right;"><?php echo number_format($sum); ?></th>
                  <!-- <th></th> -->
                  <!-- <th></th> -->
                  <!-- <th></th> -->
                </tr>
              
              
          </tfoot>
          </table>
            </div>
   
         
         <div class="modal fade" id="modal-social">
<form class="form-horizontal" method="post">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header bg-primary ">
              <h4 class="modal-title"><span style="color:white"><i class="fa fa-plus"></i> Add Social Savings </span></h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
             <div class="card card-info">
                <div class="card-body">
                  <div class="form-group row">
					 <div class="col-sm-6">
				    <select class="form-control select2" name="member"  style="width: 100%;" required="" >
<?php
$sql="select * from member";
$query=mysqli_query($con,$sql);
while($row=mysqli_fetch_array($query)){
				?>
				<option value="<?php  echo $row['id'];?>"><?php echo $row['fname'].' '.$row['lname']; ?></option>
				
				<?php } ?>
					</select>
                    </div>
					 <div class="col-sm-6">
				  <input type="" class="form-control" name="saving">
                    </div>
                  </div>
                     <div class="form-group row">
					 <div class="col-sm-6">
				    <select class="form-control select2" name="month"  style="width: 100%;" required="" >
                     <?php formMonth(); ?>
					</select>
                    </div>
					 <div class="col-sm-6">
				    <select class="form-control select2" name="year"  style="width: 100%;" required="" >
					<?php formYear(); ?>
					</select>
                    </div>
                  </div>
				    <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-8 col-form-label"></label>
                    <div class="col-sm-4">
                      <input type="submit" name="social_save" class="form-control btn-primary">
                    </div>
                  </div>
				  
                </div>
            </div>
            </div>
          </div>
		  
        </div>
		 </form>
      </div>       
         
<div class="modal fade" id="modal-withdr">
<form class="form-horizontal" method="post">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header bg-primary ">
              <h4 class="modal-title"><span style="color:white"><i class="fa fa-plus"></i> Add Social Savings </span></h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
      
            <div class="modal-body">
             <div class="card card-info">
                <div class="card-body">
                  <div class="form-group row">
					 <div class="col-sm-4">
		          <input type="text" class="form-control" name="amount" placeholder="Enter cash-out amount">
					</select>
                    </div>
					 <div class="col-sm-6">
					
				  <input type="text" class="form-control" placeholder="Enter Ref" name="ref">
                    </div>
                  </div>
                          <div class="form-group row">
               
					 <div class="col-sm-6">
				    <select class="form-control select2" name="month"  style="width: 100%;" required="" >
                     <?php formMonth(); ?>
					</select>
                    </div>
					 <div class="col-sm-6">
				    <select class="form-control select2" name="year"  style="width: 100%;" required="" >
					<?php formYear(); ?>
					</select>
                    </div>
                  </div>
                  
                <div class="form-group row">

				    <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-8 col-form-label"></label>
                    <div class="col-sm-4">
                      <input type="submit" name="socialbtn" class="form-control btn-primary">
                    </div>
                  </div>
				  
                </div>
            </div>
            </div>
          </div>
      
      
      
          </div>
		  
        </div>
		 </form>
      </div> 
         
         
         
         
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
  <!-- /.content-wrapper -->
  <?php include('footer.php'); ?>
  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->
<!-- jQuery 3 -->
<script src="bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- DataTables -->
<script src="bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<!-- SlimScroll -->
<script src="bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>
<!-- page script -->
<script>
$(document).ready(function() {
    $('#example').DataTable();
} );
</script>
</body>
</html>


<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

<?php include('DBController.php');
$membe_id=$_GET['m_idi'];
$year=$_GET['year'];
$sql="select * from member where id='$membe_id'";
$query=mysqli_query($con,$sql);
$rows=mysqli_fetch_array($query);

 if(isset($_POST['saving_edit'])){
	  $cid=$_POST['iiid'];
	  $amount=$_POST['capitaamounti'];
	 
$sql3 = "UPDATE `capital_share` SET `amount` = '$amount' WHERE `capital_share`.`id` ='$cid'";
mysqli_query($con,$sql3);
header("location:capital_share_info.php?m_idi=$membe_id");
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
  <!-- Google Font -->
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
        Member Details <small>View/Search </small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Member Details</li>
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
			   <a class="btn btn-warning btn-flat btn-sm" href="../dompdf/www/savings_statement.php?m_id=<?php echo $membe_id; ?>"><i class="fa fa-plus"></i> Print Historical Report</a>
                          </div>
            <!-- /.box-header -->
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
                          <a href="#">CAPITAL SHARES STATEMENTS</a>
                        </span>
                        <hr>
                      </div>
					  
			 <table class="table table-bordered table-responsive">
                        <tr>
                          <td>Sl.No</td>
                          <td>Member Name</td>
                          <td>Amount</td>
                          <td>Month</td>
                          <td>Year</td>
                          <td>Action</td>
                        </tr>
                       <tbody>
					  <?php 
					  $tot=0;
					  $num=0;
					  $month=0;
					  $sql = "SELECT *,s.amount as gtotal, CONCAT(fname,' ',lname) as firstname,m.id as m_idi, s.id as cid FROM `capital_share` s inner join member m on s.member_id=m.id where s.member_id='$membe_id' ORDER BY s.date DESC";
					   $quer=mysqli_query($con,$sql);
					   while($row=mysqli_fetch_array($quer)){
						   $gtotal=$row['gtotal'];
						   $tot+=$row['gtotal'];
						   $num++;
						   $month=$row['month'];
					   ?>
                        <tr>
                          <td><a href="#">#-<?php echo $num; ?></a></td>
                          <td><?php echo $row['firstname']; ?></td>
                          <td><?php echo number_format(ceil($row['gtotal']), 2); ?>  Frw (s)</td>
                          <td><?php echo $long[$month]; ?> </td>
                          <td><?php echo $row['year']; ?> </td>
                          <td><a href="#" data-toggle="modal" data-target="#modal-sav_<?php echo $row['cid']; ?>">Edit</a> | <a href="delete_share.php?id=<?php echo $row['cid'];?>&&mid=<?php echo $membe_id; ?>" onclick="return confirm('Are you sure you want to delete this Member?');">Delete</a></td>
                          
<div class="modal fade" id="modal-sav_<?php echo $row['cid']; ?>">
<form class="form-horizontal" method="post">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header bg-primary ">
              <h4 class="modal-title"><span style="color:white"><i class="fa fa-plus"></i> Edit Member Savings  </span></h4>
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
                      <input type="text" name="iiid" readonly class="form-control" value="<?php echo $row['cid']; ?>">
                      <input type="hidden" name="me_id" readonly class="form-control" value="<?php echo $row['m_idi']; ?>">
                      
                      <input type="hidden" name="me_name" readonly class="form-control" value="<?php echo $rows['fname']." ".$rows['lname']; ?>">
                    </div>
					<div class="col-sm-4">
                      <input type="text" name="capitaamounti"  class="form-control" value="<?php echo number_format($gtotal,2); ?>">
                    </div>
                  </div>
           
				  <hr>
				    <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-8 col-form-label"></label>
                    <div class="col-sm-4">
                      <input type="submit" name="saving_edit" class="form-control btn-primary">
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

    </tr>
  <?php } ?>
  <tr>
  <td colspan="6" style="color:#008080">Total: <?php echo number_format(ceil($tot),2)." Frws :".ucwords(convertNumberToWord($tot))."Rwandan francs"; ?></td>
  </tr>
                       </tbody>
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
<script>
$(document).ready(function() {
    $('#example').DataTable();
} );
</script>
</body>
</html>

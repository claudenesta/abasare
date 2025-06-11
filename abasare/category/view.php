<?PHP include("../DBController.php");
$member_id = $_SESSION['id']; // you can your integerate authentication module here to get logged in member
 ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>View | Category </title>
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
  <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include('../header.php'); ?>
  <!-- Left side column. contains the logo and sidebar -->
   <?php include('../menu.php'); ?>

<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Loan type List        <small>View/Search Loan Type</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href=""><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Loan type List</li>
        
      </ol>
    </section>

    <!-- Main content -->
    <form action="" class="" id="table_form" method="post" accept-charset="utf-8">
    <section class="content">
      <div class="row">
              <div class="col-xs-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Loan type List</h3>
                            <div class="box-tools">
                <a class="btn btn-block btn-info" href="../category/add.php">
                <i class="fa fa-plus"></i> New Loan type</a>
              </div>
                          </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example" class="table table-striped table-bordered" style="width:100%">
                <thead class="bg-primary ">
                <tr>
                  <th class="text-center">
                    <input type="checkbox" class="group_check checkbox" >
                  </th>
                  <th>Category Code</th>
                  <th>Category Name</th>
                  <th>Description</th>
                  <th>Status</th>
                  <th>Action</th> 
                </tr>
                </thead>
                <tbody>
                     <?php
                            $result =mysqli_query($con,"SELECT * FROM loan_type ORDER BY id DESC");
                            while($row=mysqli_fetch_array($result)){
                                ?>
                                <tr class="record">
                                    <td>&check;</td>
                                    <td><?php echo $row['lname']; ?></td>
                                    <td><?php echo $row['interest']; ?> %</td>
                                    <td><?php echo $row['terms']; ?> Month</td>
                                    <td> Every  <?php 
									if($row['frequency']==1){ echo "Weekly";}else if($row['frequency']==2){ echo " Two week"; }else if($row['frequency']==3){ echo "Three Week";}else{echo "Monthly";}?> 
									 </td>
									 <td><a href="#">Edit</a> | <a href="#">Delete</a></td>  
                                    </tr>
                                    <?php
                                }
                                ?>
                               
                    </tbody>
               
              </table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
     </form>  </div>
<!-- ./wrapper -->
  <!-- /.content-wrapper -->
 <?php include('../footer.php'); ?>
<!-- ./wrapper -->
  <div class="control-sidebar-bg"></div>
</div>


<!-- jQuery 3 -->
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

<?PHP include("../DBController.php");
require_once "../ShoppingCart.php";

$member_id = $_SESSION['id']; // you can your integerate authentication module here to get logged in member
 	// for activate Subject   	
if(isset($_GET['acid']))
{
$status=1;
mysqli_query($connectdb,"UPDATE customers SET active='$status' where id='".$_GET['acid']."'")or die(mysqli_error($connectdb));
$msg="Customer Activate successfully";
}

 // for Deactivate Subject
    else if(isset($_GET['did']))
{
$status=0;
mysqli_query($connectdb,"UPDATE customers SET active='$status' where id='".$_GET['did']."'")or die(mysqli_error($connectdb));
$msg="Customer Disactivate successfully";
}
 
 
 ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Customers</title>
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
        Customers List <small>View/Search Customers</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="../dashboard.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Customers List</li>
        
      </ol>
    </section>

    <!-- Main content -->
    <form action="" class="" id="table_form" method="post" accept-charset="utf-8">
    <section class="content">
      <div class="row">
              <div class="col-xs-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Customers List</h3>
                            <div class="box-tools">
                <a class="btn btn-block btn-info" href="../customers/add.php">
                <i class="fa fa-plus"></i> New Customer</a>
              </div>
                          </div>
            <!-- /.box-header -->
            <div class="box-body">
 <?php if($msg){?>
<div class="alert alert-success left-icon-alert" role="alert">
 <strong>Well done!</strong> <?php echo htmlentities($msg); ?>
 </div><?php } 
else if($error){?>
    <div class="alert alert-danger left-icon-alert" role="alert">
  <strong>Oh snap!</strong> <?php echo htmlentities($error); ?>
 </div>
   <?php } ?>
              <table id="example" class="table table-striped table-bordered" style="width:100%">
                <thead class="bg-primary ">
                <tr>
                  <th class="text-center">#</th>
                  <th>Customer Name</th>
                  <th>Mobile</th>
                  <th>Email</th>
                  <th>Country</th>
                  <th>Province</th>
                  <th>District</th>
                  <!--<th>Sector</th>
                  <th>Cell</th>
                  <th>Village</th>-->
                  <th>Status</th>
                  <th>Action</th> 
                </tr>
                </thead>
                <tbody>
                     <?php
                            $result = $db->prepare("SELECT * FROM customers c,provinces p, districts d WHERE c.province=p.provincecode AND c.district=d.districtcode ORDER BY c.id DESC");
                            $result->execute();
                            for($i=0; $row = $result->fetch(); $i++){
                                $cust_id= $row['id'];
                                $status = $row['active'];
                                ?>
                                <tr class="record">
                                    <td class=" text-center">
                                        <div class="icheckbox_square-orange">
                                            <input type="checkbox" name="checkbox[]" value="6" class="checkbox column_checkbox">
                                           </div></td>
                                    <td><?php echo $row['customer_name']; ?></td>
                                    <td><?php echo $row['phone']; ?></td>
                                    <td><?php echo $row['email']; ?></td>
                                    <td><?php echo $row['country']; ?></td>
                                    <td><?php echo $row['provincename']; ?></td>
                                    <td><?php echo $row['namedistrict']; ?></td>
                                    <!--<td><?php echo $row['sector']; ?></td>
                                    <td><?php echo $row['cell']; ?></td>
                                    <td><?php echo $row['village']; ?></td>-->
                                    <td>
                                    <?php if($status=='0')
                                   { ?>
                                    <a href="customers.php?acid=<?php echo $cust_id;?>" class="label label-danger" onclick="confirm('Do you really activate this customer?');">Inactive <i class="fa fa-times-circle"></i></a><?php } else {?>
                                    
                                   <a href="customers.php?did=<?php echo $cust_id;?>" class="label label-success" onclick="confirm('Do you really disactivate this customer?');">Active <i class="fa fa-check-circle"></i></a>
                                  <?php }?>
                                    </td>
                                    <td><div class="btn-group" title="View Account">
										<a class="btn btn-primary btn-o dropdown-toggle" data-toggle="dropdown" href="#">
											Action <span class="caret"></span>
										</a>
										<ul role="menu" class="dropdown-menu dropdown-light pull-right"><li>
											<a title="Edit Record ?" href="update.php?id=<?php echo $row['id'];?>">
					                        <i class="fa fa-fw fa-edit text-blue"></i>Edit
			                              	</a
											</li><li>
								<a href="delete.php?id=<?php echo $row['id'];?>" onclick="return confirm('Are you sure you want to delete this customer?');" title="Delete Record ?" >
						    	<i class="fa fa-fw fa-trash text-red"></i>Delete
						 				</a>
											</li>
											
										</ul>
									</div></td>
                                    
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

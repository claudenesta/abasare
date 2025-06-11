<?PHP 
include("../DBController.php");
// require_once "../ShoppingCart.php";

$member_id = $_SESSION['id']; // you can your integerate authentication module here to get logged in member
 ?>
 <?php
    function productcode() {
        $chars = "003232303232023232023456789";
        srand((double)microtime()*1000000);
        $i = 0;
        $pass = '' ;
        while ($i <= 7) {

            $num = rand() % 33;

            $tmp = substr($chars, $num, 1);

            $pass = $pass . $tmp;

            $i++;

        }
        return $pass;
    }
    // $pcode=ITEC.productcode();
    ?>
 
 
 
 
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title> ABASARE </title>
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
  <!-- Responsive -->
  <link rel="stylesheet" href="../bower_components/responsive/responsive.min.css">
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

 <?php include('./header.php'); ?>
  <!-- Left side column. contains the logo and sidebar -->
  
  <?php include('./menu.php'); ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Users List        <small>Add/Update Users</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="/admin/"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Users List</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <!-- ********** ALERT MESSAGE START******* -->
        <div class="col-md-12">
      
     </div>        <!-- ********** ALERT MESSAGE END******* -->
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Users List</h3>
                            <div class="box-tools">
                <a class="btn btn-block btn-info" href="../users/user.php">
                <i class="fa fa-plus"></i> New User</a>
              </div>
                        </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example" class="table table-striped table-bordered" style="width:100%">
                <thead class="bg-primary ">
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Mobile</th>
                  <th>Email</th>
                  <th>Role</th>
                  <th>Created on</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
               	<?php
               	$query = "SELECT * FROM `users`";
    $qu=mysqli_query($con,$query);
  
    while($item=mysqli_fetch_array($qu)) {
        ?>
        <td><?php echo $item["id"]; ?></td>
                  <td><?php echo $item["name"]; ?></td>
                  <td><?php echo $item["phone"]; ?></td>
                  <td><?php echo $item["email"]; ?></td>
                  <td><?php echo $item["Position"]; ?></td>
                  <td><?php echo $item["trn_date"]; ?></td>  
		  <td>
		<span onclick="update_status(<?php echo $item["id"]; ?>,<?php echo $item["status"]; ?>,<?php echo $item["member_acc"]; ?>)" id="span_11" class="label label-<?php if($item["status"]==1){ echo 'success';}else{echo 'danger';} ?>" style="cursor:pointer"><?php if($item["status"]==1){echo 'Active';}else{echo 'Inactive';} ?></span>
			</td>
           <td><div class="btn-group" title="View Account">
				        	<a class="btn btn-primary btn-o dropdown-toggle" data-toggle="dropdown" href="#">
								Action <span class="caret"></span>
									</a>
							<ul role="menu" class="dropdown-menu dropdown-light pull-right"><li>
						<a title="Edit Record ?" href=""> <!--update.php?id=<?php echo $item['id'];?> -->
					<i class="fa fa-fw fa-edit text-blue"></i>Edit
				</a>
						</li><li>
						<a href="delete.php?id=<?php echo $item['id'];?>" onclick="return confirm('Are you sure you want to delete this item?');" title="Delete Record ?" >
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
  </div>
  <!-- /.content-wrapper -->
 
 
 
  <?php include('../footer.php'); ?>

  <!-- Control Sidebar -->
  
  <!-- /.control-sidebar -->
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>
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
    
}); 

 function update_status(id,status,meb_id){
$.ajax({  
    type: 'POST',  
    url: 'active_user.php', 
    data: {id:id,status:status,meb_id:meb_id},
    success: function(response) {
        alert(response);
        location.reload();
    }
});

}


</script>
</body>
</html>

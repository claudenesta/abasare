<?php include('DBController.php');
$membe_id=$_SESSION['acc'];
if(isset($_POST['save'])){
$lname = $_POST['lname'];
$interest = $_POST['interest'];
$terms = $_POST['terms'];
$fine = $_POST['fine'];
$staff = $_SESSION['id'];
$idd=$_POST['idd'];
$sql = "UPDATE `loan_type` SET `lname` = '$lname', `interest` = '$interest', `terms` = '$terms', `late_fee` = '$fine' WHERE `loan_type`.`id` ='$idd'";
mysqli_query($con,$sql);
header("location:legder_setup.php");
}
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
       Ledger Setup<small>Overall Information on Single Screen</small>
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
          <div class="col-md-12">
          <div class="box box-danger">
            <div class="box-body">
              
              <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-body">
               <table id="example" class="table table-striped table-bordered" style="width:100%">
                   <thead class="bg-primary ">
               <tr>
                 <th>Name</th> 
                 <th>Interest (%)</th>
                 <th>Period</th>
                  <th>Fine(%)</th>
                  <th>Action</th>
              </tr>
              </thead>
              <tbody>
            <?php
				$no=0;
$sql="SELECT * FROM `loan_type`";
$query=mysqli_query($con,$sql);
while($row=mysqli_fetch_array($query)){
$no++;
				?>
                  <tr>
                 <td><?php echo $row['lname']; ?></td> 
                 <td><?php echo $row['interest']; ?></td>
                 <td><?php echo $row['terms']; ?></td>
                 <td><?php echo $row['late_fee']; ?></td>
                 <td> <a href="#" data-toggle="modal" data-target="#modal-<?php echo $row['id']; ?>">Edit</a> | <a href="delete_type.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this Savings?');">Delete</a></td>
                 
<div class="modal fade" id="modal-<?php echo $row['id']; ?>">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header bg-primary ">
              <h4 class="modal-title"><span style="color:white"><i class="fa fa-plus"></i> Edit Loan Type:  </span></h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
             <div class="card card-info">
                <div class="card-body">
			   <form action="" method="POST" class="form-horizontal" id="category-form">
           <div class="box-body">
                    <input type="hidden" name="idd" value="<?php echo $row['id']; ?>">      
      <div class="form-group row">
            <label for="category" class="col-sm-4 control-label"> Loan Name: <label class="text-danger">*</label></label>
           <div class="col-sm-7">
		   <input type="text" name="lname" value="<?php echo $row['lname']; ?>" class="form-control input-sm"/>
              <span id="category_msg" style="display:none" class="text-danger"></span>
            </div>
      </div>
        <div class="form-group row">
          <label for="description" class="col-sm-4 control-label"> Interest Rate (%): </label>
           <div class="col-sm-7">
           <input type="text" name="interest" value="<?php echo $row['interest']; ?>" class="form-control input-sm"/>
              <span id="description_msg" style="display:none" class="text-danger"></span>
                  </div>
                </div>
		<div class="form-group row">
          <label for="description" class="col-sm-4 control-label"> Period (Month): </label>
           <div class="col-sm-7">
           <input type="text" name="terms"  value="<?php echo $row['terms']; ?>" class="form-control input-sm"/>
              <span id="description_msg" style="display:none" class="text-danger"></span>
                  </div>
                </div>
	<div class="form-group row">
          <label for="description" class="col-sm-4 control-label"> Fine Rate (%): </label>
           <div class="col-sm-7">
                        <input type="text" name="fine"  value="<?php echo $row['late_fee']; ?>" class="form-control input-sm"/>
              <span id="description_msg" style="display:none" class="text-danger"></span>
                  </div>
                </div>
              </div>
                 <!-- /.box-footer -->
              <div class="box-footer">
                <div class="col-sm-8 col-sm-offset-3 text-center">
                   <div class="col-md-6 col-md-offset-6">
                      <button type="submit" id="save" name="save" class=" btn btn-block btn-primary" title="Save Data"> <i class="fa fa-save"></i>    Save</button>
                   </div>
                </div>
             </div>
             </form>
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
              </tbody>
              </table>
				              
            </div>
          </div>
        </div>

            </div>
          </div>
        </div>

    
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

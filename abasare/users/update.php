<?PHP 
include("../DBController.php");
require_once "../ShoppingCart.php";
	$shoppingCart = new ShoppingCart();
   	$id=$_GET['id'];
    $product_array = $shoppingCart->getUserUpdate($id);
  
    foreach ($product_array as $item) {
 ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>ITEC | RWANDA</title>
<!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="../bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Select2 -->
  <link rel="stylesheet" href="../bower_components/select2/dist/css/select2.min.css">
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
        Create User        <small>Enter User Information</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="../dashboard.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="../users/view.php">View Users</a></li>
        <li class="active">Create User</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <!-- ********** ALERT MESSAGE START******* -->
        <div class="col-md-12">
   
        </div>        <!-- ********** ALERT MESSAGE END******* -->
        <!-- right column -->
        <div class="col-md-12">
          <!-- Horizontal Form -->
          <div class="box box-info ">
            <!-- /.box-header -->
            <!-- form start -->
            <form action="user.php" method="POST" class="form-horizontal" id="category-form" enctype="multipart/form-data">

              <div class="box-body">
                  <div class="row">
                <div class="col-md-5">
				<div class="form-group">
				  <label for="new_user" class="col-sm-2 control-label">Name<label class="text-danger">*</label></label>
                  <div class="col-sm-8">
                    <input type="text" class="form-control input-sm" id="name" name="name" placeholder="" required="" onkeyup="shift_cursor(event,'mobile')" value="<?php echo $item['name']; ?>"  autofocus >
					<span id="new_user_msg" style="display:none" class="text-danger"></span>
                  </div>
                </div>
                <div class="form-group ">
          <label for="mobile" class="col-sm-2 control-label">Mobile<label class="text-danger">*</label></label>
                 <div class="col-sm-8">
                    <input type="text" class="form-control input-sm no_special_char_no_space" required="" id="mobile" name="mobile" placeholder="" value="<?php echo $item['phone']; ?>" onkeyup="shift_cursor(event,'email')"  >
          <span id="mobile_msg" style="display:none" class="text-danger"></span>
                  </div>
                </div>
                <div class="form-group">
          <label for="email" class="col-sm-2 control-label">Email<label class="text-danger">*</label></label>
                  <div class="col-sm-8">
                    <input type="email" class="form-control input-sm" value=""  id="email" name="email" placeholder="" value="<?php echo $item['email']; ?>" onkeyup="shift_cursor(event,'pass')">
          <span id="email_msg" style="display:none" class="text-danger"></span>
                  </div>
                </div>
                <div class="form-group">
                 <label for="role_id" class="col-sm-2 control-label">Role<label class="text-danger">*</label> </label>
                 <div class="col-sm-8">
                    <select class="form-control select2"  id="role_id" name="role_id" required="" value="<?php echo $item['Position']; ?>">
                       <option value=''>-Select-</option>
                       <option  value='1'>Admin</option>
                       <option  value='2'>Purchase</option>
                       <option  value='3'>Sales</option>
                       </select>
                    <span id="role_id_msg" style="display:none" class="text-danger"></span>
                 </div>
              </div>
           
				<div class="form-group">
				  <label for="pass" class="col-sm-2 control-label">Username<label class="text-danger">*</label></label>
                  <div class="col-sm-8">
                    <input type="text" class="form-control input-sm"   id="new_user" name="new_user" value="<?php echo $item['username']; ?>" onkeyup="shift_cursor(event,'confirm')"  >
					<span id="pass_msg" style="display:none" class="text-danger"></span>
                  </div>
                </div>
                 </div>

               <div class="col-md-5">
              
                
            
				<div class="form-group">
				  <label for="pass" class="col-sm-2 control-label">Password<label class="text-danger">*</label></label>
                  <div class="col-sm-8">
                    <input type="password" class="form-control input-sm"   id="pass" name="pass" value="<?php echo $item['password']; ?>" onkeyup="shift_cursor(event,'confirm')"  >
					<span id="pass_msg" style="display:none" class="text-danger"></span>
                  </div>
                </div>
                
				<div class="form-group">
				  <label for="confirm" class="col-sm-2 control-label">Confirm Password<label class="text-danger">*</label></label>
                  <div class="col-sm-8">
                    <input type="password" class="form-control input-sm"  id="confirm" name="confirm" value="<?php echo $item['comfirm']; ?>">
					<span id="confirm_msg" style="display:none" class="text-danger"></span>
                  </div>
                </div>
               
                       <div class="form-group">
                                 <label for="item_image" class="col-sm-2 control-label">Select Image<label class="text-danger">*</label></label>
                                 <div class="col-sm-8">
                                 <input type="file" name="image" id="item_image" value="<?php echo $item['photo']; ?>">
                                 <span id="item_image_msg" style="display:none" class="text-danger"></span>
                              </div>
                           </div>
                     
                
                <div class="form-group">
				  <label for="confirm" class="col-sm-2 control-label">About<label class="text-danger">*</label></label>
                  <div class="col-sm-8">
                    <textarea class="form-control input-sm"  id="about" name="about" value="<?php echo $item['about']; ?>"></textarea>
					<span id="confirm_msg" style="display:none" class="text-danger"></span>
                  </div>
                </div>

              </div>
              <!-- /.box-body -->
              </div>

              <div class="box-footer">
                <div class="col-sm-8 col-sm-offset-2 text-center">
                   <!-- <div class="col-sm-4"></div> -->
                                                    <input type="hidden" name="q_id" id="q_id" value=""/>
                   <div class="col-md-3 col-md-offset-3">
                      <button type="submit" id="save" class=" btn btn-block btn-primary" title="Save Data" name="save"><i class="fa fa-upload"></i>  Save</button>
                   </div>
                   <div class="col-sm-3">
                    <a href="../dashboard.php">
                      <button type="button" class="col-sm-3 btn btn-block btn-default close_btn" title="Go Dashboard"><i class="fa fa-close"></i> Close</button>
                    </a>
                   </div>
                </div>
             </div>
             </div>
             <!-- /.box-footer -->

            </form>
          </div>
          <!-- /.box -->
          
          <?php 
          }
          ?>
          
            
<?php
if(isset($_POST['save'])){
    
move_uploaded_file($_FILES["image"]["tmp_name"],"images/" . $_FILES["image"]["name"]);			
									$location="images/" .$_FILES["image"]["name"];
$username = $_POST['new_user'];
$mobile = $_POST['mobile'];
$email= $_POST['email'];
$role_id = $_POST['role_id'];
$pass = md5($_POST['pass']);
$confirm = md5($_POST['confirm']);
$about = $_POST['about'];
$name = $_POST['name'];
$staff = $_SESSION['id'];
//query

$sql = "INSERT INTO users (	username,phone,email,password,comfirm,name,photo,about,Position,staff_id) VALUES (:a,:x,:b,:c,:y,:d,:e,:f,:h,:i)";
$q = $db->prepare($sql);
$q->execute(array(':a'=>$username,':x'=>$mobile,':b'=>$email,':c'=>$pass,':y'=>$confirm,':d'=>$name,':e'=>$location,':f'=>$about,':h'=>$role_id,':i'=>$staff));
echo "<script>alert('User inserted Successfully')</script>";
header("location: user.php");
}
?>
  

        </div>
        <!--/.col (right) -->
      </div>
      <!-- /.row -->

    </section>
    <!-- /.content -->
  </div>
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
<!-- Page script -->

<!-- Select2 -->
<script src="../bower_components/select2/dist/js/select2.full.min.js"></script>
<script>
  $(function () {
    //Initialize Select2 Elements
    $('.select2').select2()

    //Datemask dd/mm/yyyy
    $('#datemask').inputmask('dd/mm/yyyy', { 'placeholder': 'dd/mm/yyyy' })
    //Datemask2 mm/dd/yyyy
    $('#datemask2').inputmask('mm/dd/yyyy', { 'placeholder': 'mm/dd/yyyy' })
    //Money Euro
    $('[data-mask]').inputmask()

    //Date range picker
    $('#reservation').daterangepicker()
    //Date range picker with time picker
    $('#reservationtime').daterangepicker({ timePicker: true, timePickerIncrement: 30, locale: { format: 'MM/DD/YYYY hh:mm A' }})
    //Date range as a button
    $('#daterange-btn').daterangepicker(
      {
        ranges   : {
          'Today'       : [moment(), moment()],
          'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month'  : [moment().startOf('month'), moment().endOf('month')],
          'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        startDate: moment().subtract(29, 'days'),
        endDate  : moment()
      },
      function (start, end) {
        $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'))
      }
    )

    //Date picker
    $('#datepicker').datepicker({
      autoclose: true
    })

    //iCheck for checkbox and radio inputs
    $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
      checkboxClass: 'icheckbox_minimal-blue',
      radioClass   : 'iradio_minimal-blue'
    })
    //Red color scheme for iCheck
    $('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({
      checkboxClass: 'icheckbox_minimal-red',
      radioClass   : 'iradio_minimal-red'
    })
    //Flat red color scheme for iCheck
    $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass   : 'iradio_flat-green'
    })

    //Colorpicker
    $('.my-colorpicker1').colorpicker()
    //color picker with addon
    $('.my-colorpicker2').colorpicker()

    //Timepicker
    $('.timepicker').timepicker({
      showInputs: false
    })
  })
  
  
  // spinners
  
  
  $(document).on('click', '.number-spinner button', function () {    
	var btn = $(this),
		oldValue = btn.closest('.number-spinner').find('input').val().trim(),
		newVal = 0;
	
	if (btn.attr('data-dir') == 'up') {
		newVal = parseInt(oldValue) + 1;
	} else {
		if (oldValue > 1) {
			newVal = parseInt(oldValue) - 1;
		} else {
			newVal = 1;
		}
	}
	btn.closest('.number-spinner').find('input').val(newVal);
});
  
  
</script>
</body>
</html>

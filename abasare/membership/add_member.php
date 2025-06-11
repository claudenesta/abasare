<?php 
include('../DBController.php');
function phpAlert($msg) {
    echo '<script type="text/javascript">alert("' . $msg . '")</script>';
}
	?>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Membership | Add </title>
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
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
<script type="text/javascript">
  jQuery(document).ready(function($) {
    $('a[rel*=facebox]').facebox({
      loadingImage : 'src/loading.gif',
      closeImage   : 'src/closelabel.png'
    })
  })

  $(function() {
  enable_cb5();
  $("#isnew").click(enable_cb5);
});
function enable_cb5() {
  if (this.checked) {
    $("input.isnew").removeAttr("readonly");
  } else {
    $("input.isnew").attr("readonly", true);
  }
}

</script>
<link href="../style.css" media="screen" rel="stylesheet" type="text/css" />

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
               Membership</h1>
            <ol class="breadcrumb">
               <li><a href="../dashboard"><i class="fa fa-dashboard"></i>Home</a></li>
               <li><a href="#">Member List</a></li>
               <li class="active">Member</li>
            </ol>
         </section>
         <!-- Main content -->
         <section class="content">
            <div class="row">
               <!-- ********** ALERT MESSAGE START******* -->
               <div class="col-md-12">
          </div>               <!-- ********** ALERT MESSAGE END******* -->
               <!-- right column -->
               <div class="col-md-12">
                  <!-- Horizontal Form -->
                  <div class="box box-info ">
                     
                      <form action="" class="form" id="items-form" enctype="multipart/form-data" method="POST" accept-charset="utf-8">
                          <input type="hidden" name="code" value = "<?php echo $pcode ?>" class = "form-control" />
                          
                        <div class="box-body">
                           <div class="row">
                              <div class="form-group col-md-4">
                                 <label for="item_name">First Name<span class="text-danger">*</span></label>
                                <input type="text" name="fname"  class="form-control" value="" />
                                 <span id="item_name_msg" style="display:none" class="text-danger"></span>
                              </div>
                              <div class="form-group col-md-4">
                                 <label for="category_id">Last Name <span class="text-danger">*</span></label>
                                 <input type="text" name="lname" class="form-control" value="" />
                               <span id="category_id_msg" style="display:none" class="text-danger"></span>
                              </div>
                             
                              <div class="form-group col-md-4">
                                 <label for="sku">DOB </label>
                                <input type="date" name="age" value="" class="form-control" />
                                 <span id="sku_msg" style="display:none" class="text-danger"></span>
                              </div>
                              <div class="form-group col-md-4">
                                 <label for="alert_qty" >Civil Status <span class="text-danger">*</span></label>
                                 <select class="form-control select2" id="item_type" name="civ_status"  style="width: 100%;" required="">
								   <option  selected="selected">Single</option>
								   <option>Married</option>
								   <option>Widow</option>
								   <option>Divorced</option>
								   
                                    </select>
								<span id="alert_qty_msg" style="display:none" class="text-danger">*</span>
                              </div>
							  
							   <div class="form-group col-md-4">
                                 <label for="unit_id" class="control-label">Is New Member <span class="text-danger">*</span></label>
                                  <input type="checkbox" name="isnew" value="1"  id="isnew" class="checkbox" value="" />
                                 <span id="unit_id_msg" style="display:none" class="text-danger"></span>
                              </div>					  
                      
                           </div>
                          <fieldset><legend>Contact Info :</legend>
                           <div class="row">
                              <div class="form-group col-md-4">
                                 <label for="price">Address<span class="text-danger">*</span></label>
                                 <textarea rows="2" cols="50" name="address" class="form-control"></textarea>
                                 <span id="price_msg" style="display:none"  class="text-danger"></span>
                              </div>
                              <div class="form-group col-md-4">
                                 <label for="tax_id" >Phone / Cellphone<span class="text-danger">*</span></label>
                                 <input type="text" name="phone_cell" class="form-control" value=""/>
                                 <span id="tax_id_msg" style="display:none" class="text-danger"></span>
                              </div>
                              <div class="form-group col-md-4">
                                 <label for="purchase_price">Email<span class="text-danger">*</span></label>
                                 <input type="text" name="email" value="" class="form-control" />
                                 <span id="purchase_price_msg" style="display:none" class="text-danger"></span>
                              </div>
                           </div>
						   </fieldset>
						   <fieldset><legend>Current Employment Info :</legend>
                           <!-- /row -->
                           <div class="row">
                              <div class="form-group col-md-2">
                                 <label for="tax_type">Employment Status <span class="text-danger">*</span></label>
                                 <select class="form-control select2" name="employment_status"  style="width: 100%;" required="" >
                                    <option> Employeed </option>
                                    <option> Unemployeed </option>
                                 </select>
                                 <span id="tax_type_msg" style="display:none" class="text-danger"></span>
                                 
                              </div>
                              <div class="form-group col-md-4">
                                 <label for="profit_margin">Company <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="company" value="" />
                                 <span id="profit_margin_msg" style="display:none" class="text-danger"></span>
                              </div>
                              <div class="form-group col-md-4">
                                 <label for="sales_price" class="control-label">Job Title <span class="text-danger">*</span></label>
                                 <input type="text" name="job_title" value="" class="form-control" />
                                 <span id="sales_price_msg" style="display:none" class="text-danger"></span>
                              </div>
							       
                               <div class="form-group col-md-2">
                                 <label for="opening_stock">Monthly Income</label>
                                   <input type="text" name="income" value="" class="form-control" />
                               <span id="opening_stock_msg" style="display:none" class="text-danger"></span>
							   <div>
                           </div>
						
                            
                         </fieldset>
                        
                              
                           <!-- /.box-body -->
                           <div class="box-footer">
                              <div class="col-sm-8 col-sm-offset-2 text-center">
                                 <!-- <div class="col-sm-4"></div> -->
                                                                  <div class="col-md-3 col-md-offset-3">
                                    <button type="submit" id="save" name="save" class=" btn btn-block btn-primary" title="Save Data"><i class="fa fa-upload"></i>  Save</button>
                                 </div>
                                 <div class="col-sm-3">
                                    <a href="../dashboard.php">
                                    <button type="button" class="btn bg-gray btn-block btn-flat btn-lg" title="Go Dashboard"><i class="fa fa-close"></i>  Close</button>
                                  </a>
                                 </div>
                              </div>
                           </div>
                           <!-- /.box-footer -->
                     </form>                     </div>
                     <!-- /.box -->
                  </div>
                  <!--/.col (right) -->
               </div>
               <!-- /.row -->
         </section>
         <!-- /.content -->
         </div>
         
           <?php 
			if(isset($_POST["save"])){
									$fname= $_POST['fname'];
									$lname = $_POST['lname'];
									$mi = $_POST['mi'];
									$age = $_POST['age'];
									$civ_status = $_POST['civ_status'];
									$address = $_POST['address'];
									$phone_cell = $_POST['phone_cell'];
									$email = $_POST['email'];
									$employment_status = $_POST['employment_status'];
									$company = $_POST['company'];
									$job_title = $_POST['job_title'];
									$income = $_POST['income'];
									$isnew = $_POST['isnew'];
									$date = date('Y/m/d');
				 $SQL_STRING ="INSERT INTO `member` (`id`, `company`, `address`, `phone_cell`, `email`, `income`, `civil_status`, `sex`, `age`, `employment_status`, `job_title`, `fname`, `lname`, `mi`, `rdate`, `birth_date`, `is_new`) 
				 VALUES (NULL, '$company', '$address', '$phone_cell', '$email', '$income', '$civ_status', 'NA', '0', '$employment_status', '$job_title', '$fname', '$lname', '$mi', '$date', '$age', '$isnew');";
				 MYSQLI_QUERY($con,"$SQL_STRING") OR DIE(MYSQLI_ERROR());
				 $lastid = mysqli_insert_id($con);
				 
$location="images/sam.jpg";
$username = $email;
$role_id = '5';
$pass = md5($phone_cell);
$confirm = md5($phone_cell);
$about = $fname.$lname;
$name = $fname;
$mobile=$phone_cell;
$staff = $_SESSION['id'];
//query

$sql = "INSERT INTO users (	username,phone,email,password,comfirm,name,photo,about,Position,staff_id,member_acc) VALUES (:a,:x,:b,:c,:y,:d,:e,:f,:h,:i,:j)";
$q = $db->prepare($sql);
$q->execute(array(':a'=>$username,':x'=>$mobile,':b'=>$username,':c'=>$pass,':y'=>$confirm,':d'=>$name,':e'=>$location,':f'=>$about,':h'=>$role_id,':i'=>$staff,':j'=>$lastid));
phpAlert("Member added successfull !!!! ");
?>

				<meta http-equiv="refresh" content="0; URL=list.php">
              <?PHP  
			 }
			  
			  ?>
         
         
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
  
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-orange',
      /*uncheckedClass: 'bg-white',*/
      radioClass: 'iradio_square-orange',
      increaseArea: '10%' // optional
    });
  });
</script>
<!-- CSRF Token Protection -->
<script type="text/javascript" >
$(function($) { // this script needs to be loaded on every page where an ajax POST may happen
    $.ajaxSetup({ data: {'csrf_test_name' : '9df202e6ee4c75a2f31f477f867ed5e1' }  }); });
</script>
<!-- Initialize Select2 Elements -->
<script type="text/javascript"> $(".select2").select2(); </script>
<!-- Initialize date with its Format -->
<script type="text/javascript">
  //Date picker
    $('.datepicker').datepicker({
      autoclose: true,
    format: 'dd-mm-yyyy',
     todayHighlight: true
    });
</script>
<script type="text/javascript">
$(document).ajaxStart(function() { Pace.restart(); }); 
</script>        <script src="js/items.js"></script>
      <!-- Make sidebar menu hughlighter/selector -->
      <script>$(".items-active-li").addClass("active");</script>
      
      <script type="text/javascript">
  //Date picker
    $('.datepicker').datepicker({
      autoclose: true,
    format: 'dd-mm-yyyy',
     todayHighlight: true
    });
</script>
<!-- Initialize toggler -->
<script type="text/javascript">
  $(document).ready(function(){
      $('[data-toggle="popover"]').popover();   
  });
</script>
<!-- start pace loader -->
<script type="text/javascript">
$(document).ajaxStart(function() { Pace.restart(); }); 
</script>  
<script type="text/javascript">
$(document).ready(function () { setTimeout(function() {$( ".alert-dismissable" ).fadeOut( 1000, function() {});}, 10000); });
</script>
      <!-- Make sidebar menu hughlighter/selector -->
      <script>$(".items-active-li").addClass("active");</script>
   </body>
</html>

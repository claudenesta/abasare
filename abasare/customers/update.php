<!DOCTYPE html>
<?php include('../DBController.php');
$member_id = $_SESSION['id'];
$get_province = mysqli_query($con,"SELECT * FROM provinces");
?>

<?php
   $result = $db->prepare("SELECT * FROM customers ORDER BY id DESC");
    $result->execute();
     for($i=0; $row = $result->fetch(); $i++){
     ?>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>ITEC  | Rwanda</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="../bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="../bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="../dist/css/skins/_all-skins.min.css">
  <!-- Morris chart -->
  <link rel="stylesheet" href="../bower_components/morris.js/morris.css">
  <!-- jvectormap -->
  <link rel="stylesheet" href="../bower_components/jvectormap/jquery-jvectormap.css">
  <!-- Date Picker -->
  <link rel="stylesheet" href="../bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="../bower_components/bootstrap-daterangepicker/daterangepicker.css">
  <!-- bootstrap wysihtml5 - text editor -->
  <link rel="stylesheet" href="../plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
 <!-- Select2 -->
  <link rel="stylesheet" href="../bower_components/select2/dist/css/select2.min.css">
  
  
<script type="text/javascript">
	  
	  //Get districts list
	function showResult(){
		var provincecode=document.getElementById('provincecode').value;
		var params = "&provincecode="+provincecode+"";
		http=new XMLHttpRequest();
		http.open("POST","../get/getdistrict.php",true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
		http.send(params);
		http.onreadystatechange = function() 
		{//Call a function when the province changes.
			
		document.getElementById("districtcode").innerHTML=http.responseText;
				
		if(document.getElementById('districtcode').value!=="No District Available")
		document.post_form.name.disabled=false;
		
		}		
	}
	    
		
	    //Get sectors list
		function showResult2(){
		var districtcode=document.getElementById('districtcode').value;
		var params = "&districtcode="+districtcode+"";
		http=new XMLHttpRequest();
		http.open("POST","../get/getsector.php",true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
		http.send(params);
		http.onreadystatechange = function() 
		{//Call a function when the district changes.
			
		document.getElementById("sectorcode").innerHTML=http.responseText;
				
		if(document.getElementById('sectorcode').value!=="No Sector Available")
		document.post_form.name.disabled=false;
		
		}		
	}
	
	//Get cell list
		function showResult3(){
		var sectorcode=document.getElementById('sectorcode').value;
		var params = "&sectorcode="+sectorcode+"";
		http=new XMLHttpRequest();
		http.open("POST","../get/getcell.php",true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
		http.send(params);
		http.onreadystatechange = function() 
		{//Call a function when the sector changes.
			
		document.getElementById("codecell").innerHTML=http.responseText;
				
		if(document.getElementById('codecell').value!=="No Cell Available")
		document.post_form.name.disabled=false;
		
		}		
	}
	
	//Get Villages list
		function showResult4(){
		var codecell=document.getElementById('codecell').value;
		var params = "&codecell="+codecell+"";
		http=new XMLHttpRequest();
		http.open("POST","../get/getvillage.php",true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
		http.send(params);
		http.onreadystatechange = function() 
		{//Call a function when the cell changes.
			
		document.getElementById("CodeVillage").innerHTML=http.responseText;
				
		if(document.getElementById('CodeVillage').value!=="No village Available")
		document.post_form.name.disabled=false;
		
		}		
	}
</script>

  
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
        Customers        <small>Add/Update Customer</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="..dashboard.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="../customers/customers.php">Customers List</a></li>
        <li class="active">Customers</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <!-- right column -->
        <div class="col-md-12">
          <!-- Horizontal Form -->
          <div class="box box-info ">
            <div class="box-header with-border">
              <h3 class="box-title">Please Enter Valid Data</h3>
            </div>
            <!-- /.box-header -->
       <!-- form start -->
            <form action="add.php" class="form-horizontal" name="form" id="form" method="POST">
              <div class="box-body">
                <div class="row">
                <div class="col-md-5">
                  <div class="form-group">
                      <label for="customer_name" class="col-sm-4 control-label">Customer Name<label class="text-danger">*</label></label>

                  <div class="col-sm-8">
                    <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder=""  value="<?php echo $row['customer_name']; ?>"required="" >
          <span id="customer_name_msg" style="display:none" class="text-danger"></span>
                  </div>
                  </div>
                  

                
                   <div class="form-group">
                  <label for="email" class="col-sm-4 control-label">Email</label>
                  <div class="col-sm-8">
                    <input type="email" class="form-control" id="email" name="email" placeholder="" value="<?php echo $row['email']; ?>" required="" >
          <span id="email_msg" style="display:none" class="text-danger"></span>
                  </div>
                  </div>
                  <div class="form-group">
                  <label for="phone" class="col-sm-4 control-label">Phone</label>

                  <div class="col-sm-8">
                    <input type="text" class="form-control no_special_char_no_space" id="phone" name="phone" placeholder="" value="<?php echo $row['phone']; ?>" required="" >
          <span id="phone_msg" style="display:none" class="text-danger"></span>
                  </div>
                  </div>
                   
                    <div class="form-group">
                  <label for="tax_number" class="col-sm-4 control-label">TAX Number</label>
                  <div class="col-sm-8">
                    <input type="text" class="form-control" id="tax_number" name="tax_number" required="" placeholder="" value="<?php echo $row['tax_nmbr']; ?>" >
          <span id="tax_number_msg" style="display:none" class="text-danger"></span>
                  </div>
                  </div>
                  <!-- ########### -->
               </div>


               <div class="col-md-5">

                  <div class="form-group">
                  <label for="country" class="col-sm-4 control-label">Country</label>
          
                  <div class="col-sm-8">
         <select class="form-control select2"  name="country" required>
             <option selected="selected"><?php echo $row['country']; ?></option>
                <?php
          $result = $db->prepare("SELECT * FROM countries ORDER BY `countries`.`country_name` ASC");
                $result->bindParam(':id', $id);
                $result->execute();
                for($i=0; $row = $result->fetch(); $i++){
               
                ?>
                  
                  <option><?php echo $row['country_name'];  ?></option>
                 <?php } ?>
                 
                </select>
          
          
          <span id="country_msg" style="display:none" class="text-danger"></span>
                  </div>
                  </div>
                  
                  <div class="form-group">
        <label for="state" class="col-sm-4 control-label">Province<label class="text-danger">*</label></label>
          <div class="col-sm-8">
            <select name="provincecode"  id="provincecode" onchange="showResult();" class="form-control select2">
			<option value="" selected="selected"><?php echo $row['province']; ?></option>
				<?php while($show_province = mysqli_fetch_array($get_province)) { ?>
					<option value="<?php echo $show_province['provincecode'] ?>"><?php echo $show_province['provincename'] ?></option>
						<?php } ?>
					</select>
          
          
          <span id="country_msg" style="display:none" class="text-danger"></span>
                  </div>
                  </div>
                  
        <div class="form-group">
        <label for="state" class="col-sm-4 control-label">District<label class="text-danger">*</label></label>
          <div class="col-sm-8">
              <select name="districtcode" id="districtcode" class="entrytext form-control select2 " onchange="showResult2();">
			<option ><?php echo $row['district']; ?></option>
			</select>
          <span id="postcode_msg" style="display:none" class="text-danger"></span>
                    
                  </div>
                  </div>
                  
                 
                   <div class="form-group">
                  <label for="postcode" class="col-sm-4 control-label">Sector</label>
                  <div class="col-sm-8">
                    <select name="sectorcode" id="sectorcode" class="entrytext form-control select2" onchange="showResult3();">
					<option> <?php echo $row['sector']; ?></option>
					</select>
          <span id="postcode_msg" style="display:none" class="text-danger"></span>
                  </div>
                  </div>
                  
                 <div class="form-group">
                  <label for="postcode" class="col-sm-4 control-label">Cell</label>
                  <div class="col-sm-8">
                    <select  name="codecell" id="codecell" class="entrytext form-control select2" onchange="showResult4();">
				<option> <?php echo $row['cell']; ?></option>
			</select>
          <span id="postcode_msg" style="display:none" class="text-danger"></span>
                  </div>
                  </div>
                  
                  <div class="form-group">
                  <label for="postcode" class="col-sm-4 control-label">Village</label>
                  <div class="col-sm-8">
                    <select name="CodeVillage" id="CodeVillage" class="entrytext form-control select2">
				<option><?php echo $row['village']; ?> </option>
			</select>
          <span id="postcode_msg" style="display:none" class="text-danger"></span>
                  </div>
                  </div>
                   
                </div>
                  <!-- ########### -->
    </div>
              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                              <div class="col-sm-8 col-sm-offset-2 text-center">
                                 <!-- <div class="col-sm-4"></div> -->
                                                                  <div class="col-md-3 col-md-offset-3">
                                    <button type="submit" name="update" id="update" class=" btn btn-block btn-primary" title="Save Data"><i class="fa fa-save"></i> Save</button>
                                 </div>
                                 <div class="col-sm-3">
                                    <button type="button" class="col-sm-3 btn btn-block btn-default close_btn" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
                                 </div>
                              </div>
                           </div>
                           <!-- /.box-footer -->
            </form>
          </div>
          <!-- /.box -->

        </div>
        <!--/.col (right) -->
      </div>
      <!-- /.row -->

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  
  <?php 
         
     } 
  
  ?>
  
  
  <?php
      if(isset($_POST['update']))
      {
        // new data
        $name = $_POST['customer_name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $tax = $_POST['tax_number'];
        $country = $_POST['country'];
        $province = $_POST['provincecode'];
        $district = $_POST['districtcode'];
        $sector = $_POST['sectorcode'];
        $cell = $_POST['codecell'];
        $village = $_POST['CodeVillage'];
        $staff = $_SESSION['id'];



// query
$sql = "UPDATE products 
SET customer_name=?, phone=?, email=?, tax_nmbr=?, country=?, province=?, district=?, sector=?,cell=?,village=?,staff=?
WHERE id=?";
$q = $db->prepare($sql);
$q->execute(array($name,$phone,$email,$tax,$country,$province,$district,$sector,$cell,$village,$staff));
header("location: customers.php");
echo "<script>alert('Successfully Updated!')</script>";

}

?>
  
  
  
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
<!-- jQuery UI 1.11.4 -->
<script src="../bower_components/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Bootstrap 3.3.7 -->
<script src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- Morris.js charts -->
<script src="../bower_components/raphael/raphael.min.js"></script>
<script src="../bower_components/morris.js/morris.min.js"></script>
<!-- Sparkline -->
<script src="../bower_components/jquery-sparkline/dist/jquery.sparkline.min.js"></script>
<!-- jvectormap -->
<script src="../plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="../plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<!-- jQuery Knob Chart -->
<script src="../bower_components/jquery-knob/dist/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="../bower_components/moment/min/moment.min.js"></script>
<script src="../bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
<!-- datepicker -->
<script src="../bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="../plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Slimscroll -->
<script src="../bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="../bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.min.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="../dist/js/pages/dashboard.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../dist/js/demo.js"></script>
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

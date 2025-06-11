<?php
// var_dump("<pre>", $_SERVER, __FILE__, __DIR__, ); die(); 
include('../DBController.php'); 
$file = str_replace(__DIR__ ."/", "", __FILE__);
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
  <!-- <link rel="stylesheet" href="bower_components/jvectormap/jquery-jvectormap.css"> -->
  <!-- Date Picker -->
  <!-- <link rel="stylesheet" href="bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css"> -->
  <!-- Daterange picker -->
  <!-- <link rel="stylesheet" href="bower_components/bootstrap-daterangepicker/daterangepicker.css"> -->
  <!-- bootstrap wysihtml5 - text editor -->
  <!-- <link rel="stylesheet" href="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css"> -->

  <link rel="stylesheet" type="text/css" href="bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css" />
  <link rel="stylesheet" type="text/css" href="bower_components/select2/dist/css/select2.min.css" />
  <!-- <link rel="stylesheet" type="text/css" href="bower_components/datatables-buttons/css/buttons.bootstrap4.min.css"> -->
<style>
.center {
  line-height: 200px;
  height: 200px;
  border: 3px solid green;
  text-align: center;
}
input[type=button], input[type=submit], input[type=reset] {
  background-color: #4CAF50;
  border: none;
  color: white;
  padding: 16px 32px;
  text-decoration: none;
  margin: 4px 2px;
  cursor: pointer;
}

.center p {
  line-height: 1.5;
  display: inline-block;
  vertical-align: middle;
}

    h2 {
  text-align: center;
  padding: 20px 0;
}

.table-bordered {
  border: 1px solid #ddd !important;
}

table caption {
	padding: .5em 0;
}

@media screen and (max-width: 767px) {
  table caption {
    display: none;
  }
}

.p {
  text-align: center;
  padding-top: 140px;
  font-size: 14px;
}
table {
     border: 1px solid black;
     
  width: 100%;
}

th, td {
  text-align: left;
  padding: 8px;
  
}
tr:nth-child(even) {
  background-color: #D6EEEE;
}

</style>
  
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
       Monthly Loan Payment Report 
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
      <div class="box box-success">
        <div class="box-body">  
          <h2>Monthly Loan Payment Report </h2>
          <div class="row mb-4" style="margin-bottom: 10px;">
            <div class="col-xs-3 col-md-3 col-sm-12">
              <select id="year" class="form-control form-control-sm" name="year">
                <?php
                for($i = (new \DateTime())->format("Y"); $i >= 2019;  $i--){
                  ?>
                  <option value="<?= $i; ?>"><?= $i; ?></option>
                  <?php
                }
                ?>
              </select>
            </div>
          </div>
          <div class="row" id="report_containter">
            
          </div>
        </div>
      </div>
    </div>
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
<!-- <script src="bower_components/raphael/raphael.min.js"></script> -->
<script src="bower_components/morris.js/morris.min.js"></script>
<!-- Sparkline -->
<script src="bower_components/jquery-sparkline/dist/jquery.sparkline.min.js"></script>
<!-- jvectormap -->
<!-- <script src="plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script> -->
<!-- <script src="plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script> -->
<!-- jQuery Knob Chart -->
<script src="bower_components/jquery-knob/dist/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="bower_components/moment/min/moment.min.js"></script>
<!-- <script src="bower_components/bootstrap-daterangepicker/daterangepicker.js"></script> -->
<!-- datepicker -->
<!-- <script src="bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script> -->
<!-- Bootstrap WYSIHTML5 -->
<!-- <script src="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script> -->
<!-- Slimscroll -->
<script src="bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="bower_components/fastclick/lib/fastclick.js"></script>
<script type="text/javascript" src="bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<!-- <script type="text/javascript" src="bower_components/datatables-buttons/js/dataTables.buttons.min.js"></script> -->
<!-- <script type="text/javascript" src="bower_components/pdfmake/pdfmake.min.js"></script> -->
<!-- <script type="text/javascript" src="bower_components/pdfmake/vfs_fonts.js"></script> -->
<!-- <script type="text/javascript" src="bower_components/datatables-buttons/js/buttons.html5.min.js"></script> -->
<script type="text/javascript" src="bower_components/select2/dist/js/select2.full.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<!-- <script src="dist/js/pages/dashboard.js"></script> -->
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>

<script type="text/javascript">
  // $("#example1").DataTable();
  $("#year").select2({
    placeholder: "Select a year"
  }).bind('change', function(e){
    e.preventDefault();
    $("#report_containter").html("Please Wait...");
    $("#report_containter").load("../loan_report.php?year=" + $(this).val(), function(){
      $("#example1").DataTable();
    });
  }).trigger('change');
</script>
</body>
</html>

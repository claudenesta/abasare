<?php
// var_dump("<pre>", $_SERVER, __FILE__, __DIR__, ); die(); 
include('DBController.php'); 
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
       Monthly Report on Social Savings 
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
          <h2>Monthly Report on Social Savings </h2>
          <div class="row">
            <div class="col-xs-12">
              <div class="table-responsive" data-pattern="priority-columns">
                <table  class="table table-bordered table-hover" id="example1">
                  <caption class="text-center"> Sosial saving Status Information by member's <a href="#" target="_blank"> info</a>:</caption>
                  <thead style="background-color:orange">
                    <tr>
                      <th>No</th>
                      <th data-priority="1">Member's Name</th>
<?php
$i = 1;
$yee=date("Y-01-01");
$month = strtotime($yee);
while($i <= 12)
{
    $month_name = date('F', $month);
     echo '<th data-priority="'.$i.'">'.$month_name.'</th>';
    $month = strtotime('+1 month', $month);
    $i++;
}
                      ?>
                    </tr>
                  </thead>
                  <tbody>
                      <?php 
                   $sql_quer=mysqli_query($con,'SELECT CONCAT(fname," ",lname) AS fullname,id FROM `member`');
                      while($row=mysqli_fetch_array($sql_quer)){
                    $aaa++;
                    $iid=$row['id'];
                      ?>
                    <tr>
                      <th><?php echo $aaa; ?></th>

                      <th data-priority="1"><?php echo $row['fullname'] ?></th>
<?php
$i = 1;
$month = date("Y-m-d");
$yaer=date('Y');
while($i <= 12)
{
$sql_q=mysqli_query($con,"SELECT sum(sacial_saving.amount) as amount,CONCAT(member.fname,' ',member.lname) as fullname, sacial_saving.month FROM `sacial_saving`,member WHERE sacial_saving.m_id=member.id and sacial_saving.year='".$yaer."' AND member.id='".$iid."' AND sacial_saving.month='".$i."'  GROUP BY member.id,sacial_saving.month");
$row2=mysqli_fetch_array($sql_q);    
 echo '<td data-priority="'.$i.'">'.$row2 ['amount'].'</td>';
 
    $i++;
}
?>
                    </tr>
                    
                    <?php } ?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td colspan="4">Total</td>
                      <td style="text-align: right;"></td>
                      <td style="text-align: right;"></td>
                      <td style="text-align: right;"></td>
                    </tr>
                  </tfoot>
                </table>
                <a href="./export_monthly_loan_status.php" target="_blank" class="btn btn-success">Download</a>
              </div>
            </div>
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
<script src="bower_components/jquery-knob/dist/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="bower_components/moment/min/moment.min.js"></script>
<script src="bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="bower_components/fastclick/lib/fastclick.js"></script>
<script type="text/javascript" src="bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>
<script src="dist/js/demo.js"></script>

<script type="text/javascript">
  $("#example1").DataTable();
</script>
</body>
</html>

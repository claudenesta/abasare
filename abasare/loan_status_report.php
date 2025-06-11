<?php include('DBController.php'); ?>
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
       Loan Status Report
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
          <h2>Details on Loan status</h2>
          <div class="row">
            <div class="col-xs-12">
              <div class="table-responsive" data-pattern="priority-columns">
                <table  class="table table-bordered table-hover" id="example1">
                  <caption class="text-center">Loan Status Information by member's <a href="#" target="_blank"> info</a>:</caption>
                  <thead>
                    <tr>
                      <th style="background-color:rgb(255, 165, 0);">No</th>
                      <th data-priority="1" style="background-color:rgb(255, 165, 0);">Member's Name</th>
                      <th data-priority="2" style="background-color:rgb(255, 165, 0);">Total Loan Amount</th>
                      <th data-priority="3" style="background-color:rgb(255, 165, 0);">Total Paid Amount</th>
                      <th data-priority="3" style="background-color:rgb(255, 165, 0);">Pending Balance </th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $query = "SELECT  CONCAT(b.fname, ' ', COALESCE(b.lname)) AS memberName,
                                      SUM(a.loan_amount) AS totalLoan,
                                      SUM(c.paidAmount) AS paidAmount
                                      FROM member_loans AS a
                                      INNER JOIN member AS b
                                      ON a.member_id = b.id
                                      LEFT JOIN (
                                        SELECT  a.borrower_loan_id,
                                                SUM(amount) AS paidAmount
                                                FROM lend_payments AS a
                                                INNER JOIN member_loans AS b
                                                ON a.borrower_loan_id = b.id
                                                GROUP BY a.borrower_loan_id
                                      ) AS c
                                      ON a.id = c.borrower_loan_id
                                      WHERE a.reject = 0
                                      GROUP BY b.id
                                      ORDER BY memberName ASC
                                      ";
                    try{
                      // var_dump($query);
                      $statement = $db->prepare($query);
                      $statement->execute();
                      
                      $loans_info = $statement->fetchAll(PDO::FETCH_ASSOC);
                    } catch(Exception $e){
                      throw new Exception($e->getMessage(), 1);
                      
                    }
                    $i = 1;
                    $total_loans = 0;
                    $total_payments = 0;
                    $total_balance = 0;
                    foreach($loans_info AS $loan){
                      $pending = $loan['totalLoan'] - $loan['paidAmount'];
                      $total_loans += $loan['totalLoan'];
                      $total_payments += $loan['paidAmount'];
                      $total_balance += $pending;
                      ?>
                      <tr class="<?= $pending <= 0?'bg-success':''; ?>">
                        <td><?= $i++; ?></td>
                        <td><?= $loan['memberName'] ?></td>
                        <td style="text-align: right;"><?= number_format($loan['totalLoan']) ?> Rwf</td>
                        <td style="text-align: right;"><?= number_format($loan['paidAmount']) ?> Rwf</td>
                        <td style="text-align: right;"><?= number_format($pending) ?> Rwf</td>
                      </tr>
                      <?php
                    }
                    ?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td colspan="2"style="background-color:rgb(238, 130, 238);"><b>Total</b></td>
                      <td style="text-align: right; background-color:rgb(238, 130, 238);"><b><?= number_format($total_loans) ?> Rwf</b></td>
                      <td style="text-align: right; background-color:rgb(238, 130, 238);"><b><?= number_format($total_payments) ?> Rwf</b></td>
                      <td style="text-align: right; background-color:rgb(238, 130, 238);"><b><?= number_format($total_balance) ?> Rwf</b></td>
                    </tr>
                  </tfoot>
                </table>
                <a href="./export_loan_status.php" target="_blank" class="btn btn-block btn-success">Download</a>
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
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<!-- <script src="dist/js/pages/dashboard.js"></script> -->
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>

<script type="text/javascript">
  $("#example1").DataTable();
</script>
</body>
</html>

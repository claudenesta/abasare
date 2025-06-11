<?php
include('../DBController.php'); 
$file = str_replace(__DIR__ ."/", "", __FILE__);
?>
<style>
/* Existing styles remain unchanged */
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
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

 <?php include('./header.php'); 

$_GET['year'] = isset($_GET['year']) && !empty($_GET['year']) ? $_GET['year'] : date('Y', strtotime("-1 year"));
$active = "saving-report";
include('./menu.php'); ?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       Monthly Report on Savings <?= $_GET['year'] ?> 
      </h1>
      <ol class="breadcrumb">
          <li><a href="/admin/"><i class="fa fa-dashboard"></i>Home</a></li>
          <li><a href="#">Reports</a></li>
          <li class="active"><a href="saving_report.php<?= isset($_GET['year']) ? "?year=" . $_GET['year'] : "" ?>">Savings</a></li>
      </ol>
    </section><br/>

    <div class="col-md-12">
      <div class="box box-success">
        <div class="box-body">
          <div class="row">
            <div class="col-xs-12">
              <div class="table-responsive" data-pattern="priority-columns">
                <div class="row mb-4" style="margin-bottom: 10px;">
                  <div class="col-xs-3 col-md-3 col-sm-12">
                    <select id="year" class="form-control form-control-sm" name="year">
                      <?php
                      for($i = (new \DateTime())->format("Y"); $i >= 2019;  $i--){
                        ?>
                        <option value="<?= $i; ?>" <?= $i == $_GET['year'] ? "selected" : "" ?>><?= $i; ?></option>
                        <?php
                      }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="row" id="report_containter">
                </div>
                <table class="table table-bordered table-hover" id="example1">
                  <thead style="background-color:orange">
                    <tr>
                      <th>No</th>
                      <th style="width:100%" data-priority="1">Member's Name</th>
                      <?php
                      $i = 1;
                      $yee = date("Y-01-01");
                      $month = strtotime($yee);
                      $months = [];
                      while($i <= 12)
                      {
                        $month_name = date('M', $month);
                        $months[] = date('n', $month);
                        echo '<th data-priority="'.$i.'">'.$month_name.'</th>';
                        $month = strtotime('+1 month', $month);
                        $i++;
                      }
                      ?>
                    </tr>
                  </thead>
                  <tbody>
                      <?php 
                      $sql_quer = mysqli_query($con, 'SELECT CONCAT(fname," ",lname) AS fullname, id FROM `member`');
                      $totals = [];
                      $aaa = 0;
                      while($row = mysqli_fetch_array($sql_quer)){
                        $aaa++;
                        $iid = $row['id'];
                      ?>
                    <tr>
                      <th><?php echo $aaa; ?></th>
                      <th data-priority="1"><?php echo $row['fullname'] ?></th>
                      <?php
                      $i = 1;
                      $yee = date("Y-01-01");
                      $month = strtotime($yee);
                      $yaer = isset($_GET['year']) && !empty($_GET['year']) ? $_GET['year'] : date('Y', strtotime("-1 year"));
                      while($i <= 12)
                      {
                      $sql_q = mysqli_query($con, "SELECT SUM(saving.sav_amount) AS amount, CONCAT(member.fname,' ',member.lname) AS fullname, saving.month 
                                                   FROM saving, member 
                                                   WHERE saving.member_id = member.id 
                                                   AND saving.year = '".$yaer."' 
                                                   AND member.id = '".$iid."' 
                                                   AND saving.month = '".$i."' 
                                                   GROUP BY member.id, saving.month");
                      if (!$sql_q) {
                          echo '<td>Error: ' . mysqli_error($con) . '</td>';
                          continue;
                      }
                      $row2 = mysqli_fetch_array($sql_q);
                      if(!isset($totals[$i])) {
                        $totals[$i] = 0;
                      }
                      $totals[$i] += isset($row2['amount']) ? $row2['amount'] : 0;

                      echo '<td data-priority="'.$i.'">'.(isset($row2['amount']) ? number_format($row2['amount']) : '0').'</td>';
                          $i++;
                      }
                      ?>
                      </tr><?php } ?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td colspan="2" class="text-right">Total</td>
                      <?php
                      foreach($months AS $column){
                        ?>
                        <td><?= isset($totals[$column]) ? number_format($totals[$column]) : '0'; ?></td>
                        <?php
                      }
                      ?>
                    </tr>
                  </tfoot>
                </table>
                <!-- Add Excel and PDF download buttons -->
                <a href="./export_monthly_savings_status.php?year=<?= $_GET['year'] ?>" target="_blank" class="btn btn-success">Download Excel</a>
                <a href="./export_monthly_savings_status_pdf.php?year=<?= $_GET['year'] ?>" target="_blank" class="btn btn-danger">Download PDF</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- /.content-wrapper -->
  <?php include('./footer.php'); ?>

<script type="text/javascript">
  $("#example1").DataTable();      
  
  $("#year").on('change', function(e){
    e.preventDefault();
    window.location = './saving_report.php?year=' + $(this).val();
  });
</script>
</body>
</html>
<?php 
if(@$_POST['change_settings'] == 'general'){
  require_once("./db_function.php");
  saveData($db, "UPDATE general_setting SET financial_year=?, month=? WHERE id=?", [$_POST['year'], $_POST['month'], 1]);
}
include('DBController.php'); 
?>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Dashboard</title>
  <!-- Tell the bˇrowser to be responsive to screen width -->
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
  <style>
    .center {
      line-height: 200px;
      height: 200px;
      border: 3px solid green;
      text-align: center;
    }

    input[type=button],
    input[type=submit],
    input[type=reset] {
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
          General Report <small>Overall Information on Single Screen</small>
        </h1>
        <ol class="breadcrumb">
          <li class="active"><i class="fa fa-dashboard"></i> Home</li>
        </ol>
      </section><br />
      <div class="col-md-12">
        <!-- ********** ALERT MESSAGE START******* -->
        <div class="col-md-12">
          <!-- ********** ALERT MESSAGE START******* -->

        </div> <!-- ********** ALERT MESSAGE END******* -->
      </div>
      <div class="row">
        <?php

        $info_ = mysqli_query($con, "SELECT * FROM general_setting");
        $info_data = mysqli_fetch_array($info_);
        ?>
        <div class="col-md-12">
          <div class="box box-primary" style="padding: 20px;">
            <form action="./general_report.php" method="POST">
              <input type="hidden" name="change_settings" value="general" />
              <div class="row">
                <div class="col-sm-6 col-md-2">
                  <select name="year" id="year" class="form-control form-control-sm">
                    <?php
                    for($i = (new \DateTime())->format("Y"); $i >= 2019;  $i--){
                      ?>
                      <option value="<?= $i; ?>" <?= $info_data['financial_year'] == $i?"selected":"" ?>><?= $i; ?></option>
                      <?php
                    }
                    ?>
                  </select>
                </div>
                <div class="col-sm-6 col-md-2">
                  <select name="month" id="month" class="form-control form-control-sm">
                    <?php
                    $month = [1 => 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                    for($i=1; $i <= 12; $i++){
                      ?>
                      <option value="<?= $i; ?>" <?= $info_data['month'] == $i?"selected":"" ?> ><?= $month[$i]; ?></option>
                      <?php
                    }
                    ?>
                  </select>
                </div>
                <div class="col-sm-6 col-md-2">
                  <button type="submit" name="update" class="btn btn-primary">Save</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
      <div class="col-md-12">
        <div class="box box-danger">
          <div class="box-body">
            <h2>Long-Term saving and credit General Report</h2>

            <div class="row">
              <div class="col-xs-12">
                <div class="table-responsive" data-pattern="priority-columns">
                  <table class="table table-bordered table-hover">
                    <caption class="text-center">Overview of Monthly Earns from Loan, Savings, Wealth Capital ' <a href="#" target="_blank"> Saving solution</a>:</caption>
                    <thead>
                      <tr>
                        <th>No</th>
                        <th data-priority="1">Investment Source</th>
                        <th data-priority="2">Money Earned From source</th>
                        <th data-priority="3">Compared to Transaction</th>
                        <th data-priority="3">Result </th>
                      </tr>
                    </thead>
                    <tbody>

                      <tr>
                        <td>1</td>
                        <td>Loan Interest Transaction</td>
                        <td> <?php $tot = 0;
                              $totint = 0;
                              $sql = "SELECT sum(loan_amount_interest) as earn FROM `member_loans` where MONTH(loan_date) = '$currentmonth' AND YEAR(loan_date) = '$currentyear' AND reject=0 && president=1 && accountant=1";
                              $qury = mysqli_query($con, $sql);
                              $rows = mysqli_fetch_array($qury);
                              $source = number_format(ceil($rows['earn']), 2);
                              $tot = $rows['earn'];

                              echo number_format(ceil($rows['earn']), 2);
                              ?></td>
                        <td><?php
                            $sql = "SELECT sum(loan_interest) as earn FROM `interest` where month = '$currentmonth' AND year= '$currentyear'";
                            $qury = mysqli_query($con, $sql);
                            $rows = mysqli_fetch_array($qury);
                            $interes = number_format(ceil($rows['earn']), 2);
                            $totint = $rows['earn'];
                            echo number_format(ceil($rows['earn']), 2);
                            ?></td>
                        <td <?php if ($interes != $source) { ?>bgcolor="red" style="color:#FFF;" <?php } else { ?>bgcolor="green" style="color:#FFF;" <?php } ?>><?php if ($interes != $source) { ?>
                            <span class="glyphicon glyphicon-remove"></label>
                            <?php } else { ?><span class="glyphicon glyphicon-ok"></label><?php } ?>
                        </td>
                      </tr>
                      <tr>
                        <td>1</td>
                        <td>Overdue Loan Payment</td>
                        <td><?php

                            $sql = "SELECT sum(overdue_fine) as earn FROM `lend_payments` where MONTH(payment_sched) = '$currentmonth' AND YEAR(payment_sched) = '$currentyear'";
                            $qury = mysqli_query($con, $sql);
                            $rows = mysqli_fetch_array($qury);
                            $source = number_format(ceil($rows['earn']), 2);
                            $tot += $rows['earn'];

                            echo number_format(ceil($rows['earn']), 2);
                            ?></td>
                        <td>
                          <?php
                          $sql = "SELECT sum(fine_overdue) as earn FROM `interest` where month= '$currentmonth' AND year = '$currentyear'";
                          $qury = mysqli_query($con, $sql);
                          $rows = mysqli_fetch_array($qury);
                          $interes = number_format(ceil($rows['earn']), 2);
                          $totint += $rows['earn'];
                          echo number_format(ceil($rows['earn']), 2);
                          ?>

                        </td>
                        <td <?php if ($interes != $source) { ?>bgcolor="red" style="color:#FFF;" <?php } else { ?>bgcolor="green" style="color:#FFF;" <?php } ?>><?php if ($interes != $source) { ?>
                            <span class="glyphicon glyphicon-remove"></label><?php  } else { ?><span class="glyphicon glyphicon-ok"></label><?php } ?>

                        </td>
                      </tr>
                      <tr>
                        <td>1</td>
                        <td>Overdue Savings Transaction</td>
                        <td><?php
                            $sql = "SELECT sum(fine) as earn FROM `saving` where month = '$currentmonth' AND year = '$currentyear'";
                            $qury = mysqli_query($con, $sql);
                            $rows = mysqli_fetch_array($qury);
                            $source = number_format(ceil($rows['earn']), 2);
                            $tot += $rows['earn'];
                            echo number_format(ceil($rows['earn']), 2);
                            ?></td>
                        <td>
                          <?php
                          $sql = "SELECT sum(saving_overdu) as earn FROM `interest` where month = '$currentmonth' AND year = '$currentyear'";
                          $qury = mysqli_query($con, $sql);
                          $rows = mysqli_fetch_array($qury);
                          $interes = number_format(ceil($rows['earn']), 2);
                          $totint += $rows['earn'];
                          echo number_format(ceil($rows['earn']), 2);
                          ?>

                        </td>
                        <td <?php if ($interes != $source) { ?>bgcolor="red" style="color:#FFF;" <?php } else { ?>bgcolor="green" style="color:#FFF;" <?php } ?>><?php if ($interes != $source) { ?>
                            <span class="glyphicon glyphicon-remove"></label><?php  } else { ?><span class="glyphicon glyphicon-ok"></label><?php } ?>

                        </td>
                      </tr>
                      <tr>
                        <td>1</td>
                        <td>New Membership </td>
                        <td><?php
                            $sql = "SELECT sum(membership_fee) as earn FROM `interest` where month = '$currentmonth' AND year = '$currentyear'";
                            $qury = mysqli_query($con, $sql);
                            $rows = mysqli_fetch_array($qury);
                            $source = number_format(ceil($rows['earn']), 2);
                            $tot += $rows['earn'];
                            echo number_format(ceil($rows['earn']), 2);
                            ?> </td>
                        <td>
                          <?php
                          $interes = number_format(ceil($rows['earn']), 2);
                          $totint += $rows['earn'];
                          echo $interes;
                          ?>

                        </td>
                        <td <?php if ($interes != $source) { ?>bgcolor="red" style="color:#FFF;" <?php } else { ?>bgcolor="green" style="color:#FFF;" <?php } ?>><?php if ($interes != $source) { ?>
                            <span class="glyphicon glyphicon-remove"></label><?php  } else { ?><span class="glyphicon glyphicon-ok"></label><?php } ?>

                        </td>
                      </tr>
                      <tr class="bg-primary">
                        <td colspan="2"> Total Earn For <?php echo $long[$currentmonth] . '  ' . $currentyear; ?>)</td>
                        <td><?php echo number_format(ceil($tot), 2); ?></td>
                        <td><?php echo number_format(ceil($totint), 2); ?></td>
                        <td <?php if (ceil($tot) != ceil($totint)) { ?>bgcolor="red" style="color:#FFF;" <?php } else { ?>bgcolor="green" style="color:#FFF;" <?php } ?>><?php if (ceil($tot) != ceil($totint)) { ?>
                            <span class="glyphicon glyphicon-remove"></label><?php  } else { ?><span class="glyphicon glyphicon-ok"></label><?php } ?>
                        </td>
                      </tr>

                    </tbody>

                  </table>
                  <table class="table table-bordered table-responsive">
                    <tr>
                      <td colspan="6">
                        <h2>Details of Interest Earned in <?php echo $long[$currentmonth] . '  ' . $currentyear; ?></h2>

                      </td>

                    </tr>
                    <tr>
                      <td colspan="6" align="center" style="background-color:#F2F3F5; background-color:#F2F3F5">
                        <h4><b> Incomes from Interest Loan taken </b></h4>
                      </td>
                    </tr>
                    <tr>
                      <td>N<sup><u>o</u></sup></td>
                      <td>Member Name</td>
                      <td>Loan</td>
                      <td>Loan Date </td>
                      <td>Loan Amount</td>
                      <td>Interest</td>
                    </tr>
                    <tbody>
                      <?php
                      $totl_loan = 0;
                      $totlinter = 0;
                      $sql = "SELECT *, ml.id as ididi, CONCAT(lt.lname,' - ',lt.interest,'%') as name, ml.rdate as target, ml.loan_amount_interest as interes, CONCAT(m.fname,' ',m.lname) as fullname, ml.loan_date as datte 
					   FROM `member_loans` ml inner join loan_type lt on lt.id=ml.loan_id inner join member m on m.id=ml.member_id
					   where ml.president=1 AND ml.accountant=1 AND ml.reject=0 AND MONTH(ml.loan_date)='$currentmonth' AND YEAR(ml.loan_date)='$currentyear'";
                      $quer = mysqli_query($con, $sql);
                      $totllll = 0;
                      while ($row = mysqli_fetch_array($quer)) {
                        $totl_loan += $row['loan_amount'];
                        $totlinter += $row['interes'];
                        $totllll += $row['loan_amount'];
                      ?>
                        <tr>
                          <td><?php echo $row['ididi']; ?></td>
                          <td><?php echo $row['fullname']; ?></td>
                          <td><?php echo $row['name']; ?></td>
                          <td><?php echo $row['datte']; ?></td>
                          <td><?php echo number_format(ceil($row['loan_amount']), 2); ?> Frws </td>
                          <td><?php echo number_format(ceil($row['interes']), 2); ?> Frws</td>
                        </tr>
                      <?php
                      } ?>
                      <tr style="color:#0933EF">
                        <td colspan="4" align="right">TOTAL: </td>
                        <td><?php echo number_format($totl_loan, 2); ?></td>
                        <td><?php echo number_format(ceil($totlinter), 2); ?></td>
                      </tr>

                      <tr>
                        <td colspan="6" align="center" style="background-color:#F2F3F5">
                          <h4><b>Incomes from Overdue Loan Payment </b></h4>
                        </td>
                      </tr>
                      <tr>
                        <td>N<sup><u>o</u></sup></td>
                        <td>Member Name</td>
                        <td>Loan</td>
                        <td>Installement </td>
                        <td>Date</td>
                        <td>Fine</td>
                      </tr>
                    <tbody>
                      <?php
                      $totl_loan = 0;
                      $totlinter = 0;
                      $sql = "SELECT *, ml.id as ididi, CONCAT(lt.lname,' - ',lt.interest,'%') as name, ml.rdate as target, ml.loan_amount_interest as interes, CONCAT(m.fname,' ',m.lname) as fullname, ml.loan_date as datte, lp.payment_sched as schedu, lp.amount as instal, lp.overdue_fine as fine 
					   FROM `member_loans` ml inner join loan_type lt on lt.id=ml.loan_id inner join member m on m.id=ml.member_id inner join lend_payments lp on lp.borrower_loan_id=ml.id
					   where ml.president=1 AND ml.accountant=1 AND ml.reject=0 AND MONTH(lp.payment_sched)='$currentmonth' AND YEAR(lp.payment_sched)='$currentyear' AND lp.overdue_fine!=0";
                      $quer = mysqli_query($con, $sql);
                      while ($row = mysqli_fetch_array($quer)) {
                        $totl_loan += $row['fine'];
                      ?>
                        <tr>
                          <td><?php echo $row['ididi']; ?></td>
                          <td><?php echo $row['fullname']; ?></td>
                          <td><?php echo $row['name']; ?></td>
                          <td><?php echo $row['instal']; ?></td>
                          <td><?php echo $row['schedu']; ?></td>
                          <td><?php echo $row['fine']; ?> Frws</td>
                        </tr>
                      <?php } ?>
                      <tr style="color:#0933EF">
                        <td colspan="5" align="right">TOTAL: </td>
                        <td><?php echo number_format(ceil($totl_loan), 2); ?></td>
                      </tr>
                      <tr>
                        <td colspan="6" align="center" style="background-color:#F2F3F5">
                          <h4><b>Overdue Savings Payment</b></h4>
                        </td>
                      </tr>
                      <tr>
                        <td>N<sup><u>o</u></sup></td>
                        <td>Member Name</td>
                        <td>Saved Amount</td>
                        <td>Saving month </td>
                        <td>Year</td>
                        <td>Fine</td>
                      </tr>
                    <tbody>
                      <?php
                      $samue = 0;
                      $sql = "SELECT *, CONCAT(fname,' ',lname) as firstname, s.id as iid, s.fine as fines, s.fine as d FROM `saving` s inner join member m on s.member_id=m.id where s.year='$currentyear' AND s.month='$currentmonth' AND fine!=0";
                      $quer = mysqli_query($con, $sql);
                      $num = 0;
                      while ($row = mysqli_fetch_array($quer)) {
                        $month = (int)$row['month'];
                        $samue += (int)$row['d'];
                        $num++;
                      ?>
                        <tr>
                          <td><?php echo $num; ?></td>
                          <td><?php echo $row['firstname']; ?></td>
                          <td><?php echo number_format(ceil($row['d']), 2); ?> Frw (s)</td>
                          <td><?php echo $long[$month]; ?> </td>
                          <td><?php echo $row['year']; ?> </td>
                          <td><?php echo $row['fines']; ?> Frws</td>
                        </tr>
                      <?php } ?>
                      <tr style="color:#0933EF">
                        <td colspan="5" align="right">TOTAL: </td>
                        <td><?php echo number_format($samue, 2); ?></td>
                      </tr>
                      <tr>
                        <td colspan="6" align="center" style="background-color:#F2F3F5">
                          <h4><b>Membership Fees</b></h4>
                        </td>
                      </tr>
                      <tr>
                        <td>N<sup><u>o</u></sup></td>
                        <td colspan="3">Member Name</td>
                        <td>Amount</td>
                        <td>Date</td>

                      </tr>
                    <tbody>
                      <?php
                      $samue = 0;
                      $sql = "SELECT CONCAT(m.fname,' ',m.lname) as fullname, i.membership_fee as fee, i.done_at as do FROM member m inner join interest i on i.ref_id=m.id AND i.membership_fee!=0 AND i.month ='$currentmonth' AND i.year ='$currentyear'";
                      $quer = mysqli_query($con, $sql);
                      while ($row = mysqli_fetch_array($quer)) {
                        $samue += (int)$row['fee'];
                        $num++;
                      ?>
                        <tr>
                          <td><?php echo $num; ?></td>
                          <td colspan="3"><?php echo $row['fullname']; ?></td>
                          <td><?php echo $row['fee']; ?></td>
                          <td><?php echo $row['do']; ?></td>

                        </tr>
                      <?php } ?>
                      <tr style="color:#0933EF">
                        <td colspan="4" align="right">TOTAL: </td>
                        <td><?php echo number_format($samue, 2); ?></td>
                        <td></td>
                      </tr>
                    </tbody>
                  </table>

                  <table class="table table-bordered table-responsive">
                    <tr class="bg-primary">
                      <td>Overall Capital =
                        <?php
                        $totalcapital = 0;
                        $sqlu = "SELECT sum(amount) as totscapital 
                                        FROM `capital_share` 
                                        where STR_TO_DATE(CONCAT(year,'/',month),'%Y/%m')<=STR_TO_DATE(CONCAT(" . $currentyear . ",'/'," . $currentmonth . "),'%Y/%m')";
                        // echo $sqlu;
                        $saving = mysqli_query($con, $sqlu);
                        $rowd = mysqli_fetch_array($saving);
                        $totalcapital += $rowd['totscapital'];
                        echo number_format($rowd['totscapital']) . " frws";
                        ?>

                      </td>
                      <td>Overall Saving :
                        <?php
                        $ggg = 0;
                        $sqlu = "SELECT sum(sav_amount) as totsaving FROM `saving` where  STR_TO_DATE(CONCAT(year,'/',month),'%Y/%m')<=STR_TO_DATE(CONCAT(" . $currentyear . ",'/'," . $currentmonth . "),'%Y/%m')";
                        // echo $sqlu;
                        $saving = mysqli_query($con, $sqlu);
                        while ($rowd = mysqli_fetch_array($saving)) {
                          echo number_format($rowd['totsaving']) . " frws <br>";
                          $totalcapital += $rowd['totsaving'];
                          $ggg += $rowd['totsaving'];
                        }
                        ?>

                      </td>
                      <td>Total Profit of Previous Month :
                        <?php
                        $sqlcapinte = "SELECT sum(amount) as prof FROM `profite` where STR_TO_DATE(CONCAT(year,'/',month),'%Y/%m')<=STR_TO_DATE(CONCAT(" . $currentyear . ",'/'," . $currentmonth . "),'%Y/%m')";
                        $quinter = mysqli_query($con, $sqlcapinte);
                        $row = mysqli_fetch_array($quinter);
                        echo number_format ($row['prof']) . " frws <br>";
                        $totalcapital += $row['prof'];
                        ?>

                      </td>
                      <td>A/R = <?php echo number_format(ceil($totint), 2); ?> frw</td>
                    </tr>
                    <tr>
                      <td colspan="3">
                        <ul>
                          <li>Total account Fund :(<?php echo number_format($totalcapital, 2); ?> Frws ) <?php echo ucwords(convertNumberToWord($totalcapital)); ?></li>
                          <hr>
                          <li>Total Investment In <?php echo $long[$currentmonth] . '  ' . $currentyear . " : (" . number_format($totllll, 2); ?>: Frws) <?php echo ucwords(convertNumberToWord($totllll)); ?></li>
                          <hr>
                          <?php
                          $aaaa = "SELECT sum(amount) as amountt FROM `expenses` where year='$currentyear' AND month='$currentmonth'";
                          $quer = mysqli_query($con, $aaaa);
                          $rrow = mysqli_fetch_array($quer);
                          $expence = $rrow['amountt'];
                          ?>
                          <li>Total Exepences in <?php echo $long[$currentmonth] . '  ' . $currentyear . " : (" . number_format($expence, 2); ?>: Frws) <?php echo ucwords(convertNumberToWord($expence)); ?></li>
                          <hr>
                          <li>Total Profit Earned based on Investment in <?php echo $long[$currentmonth] . '  ' . $currentyear . " : (" . number_format($totint - $expence, 2); ?>: Frws) <?php echo ucwords(convertNumberToWord($totint - $expence)); ?></li>
                          <ul>
                      </td>
                      <td>
                        <div class="center">
                          <p> Investment rate on market</p>
                          <p><?php echo $totalcapital != 0 ?round(($totllll) / $totalcapital, 2):""; ?></p>

                        </div>

                      </td>
                    </tr>
                  </table>
                  <div style="padding-left:50%">
                    <form target="_blank" method="post" action="individual_share.php">
                      <input type="hidden" name="tot_capital" value="<?php echo $totalcapital; ?>">
                      <input type="hidden" name="toinv" value="<?php echo $totllll; ?>">
                      <input type="hidden" name="profit" value="<?php echo $totint - $expence; ?>">
                      <input type="hidden" name="inves_rate" value="<?php echo $totalcapital != 0?($totllll / $totalcapital):""; ?>">
                      <input type="submit" class="class=" btn btn-primary" value="individual share >>>>>>>>">
                    </form>
                  </div>
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
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ...existing code...

include('./header.php');

$active="dashboard";
// var_dump($_SESSION);
include('menu.php'); 
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Dashboard
        <small>Accountant</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="/accountant/"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Dashboard</li>
      </ol>
    </section>
    
    <section class="content">
      <div class="row">
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">
              <?php
              $active_amount = returnSingleField($db, "SELECT SUM(a.loan_amount) - COALESCE(b.paid_amount,0) AS amount
                                                              FROM member_loans AS a
                                                              LEFT JOIN (
                                                                SELECT  a.id AS loan_id,
                                                                        SUM(b.amount) AS paid_amount
                                                                        FROM member_loans AS a
                                                                        INNER JOIN lend_payments AS b
                                                                        ON a.id = b.borrower_loan_id
                                                                        WHERE a.status = ?
                                                                        AND b.status = ?
                                                                ) AS b
                                                              ON a.id = b.loan_id
                                                              WHERE a.status = ?", "amount", ['ACTIVE', 'PAID', 'ACTIVE']);
              if(is_null($active_amount)){
                $active_amount = 0;
              }
              ?>
              <h3><?= formatNumberShort($active_amount) ?><sup style="font-size: 20px">RWF</sup></h3>

              <p>Active Loans</p>
            </div>
            <div class="icon">
              <i class="ion ion-bag"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-green">
            <div class="inner">
              <?php
              $interest = returnSingleField($db, "SELECT  SUM(a.amount) AS amount
                                                          FROM interest AS a
                                                          WHERE a.Is_posted IS NULL", "amount");
              if(is_null($interest)){
                $interest = 0;
              }
              ?>
              <h3><?= formatNumberShort($interest) ?><sup style="font-size: 20px">RWF</sup></h3>

              <p>Unshared Interest</p>
            </div>
            <div class="icon">
              <i class="ion ion-stats-bars"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-yellow">
            <div class="inner">
              <?php
              $fines = returnSingleField($db, "SELECT SUM(a.fine_amount) AS amount
                                                      FROM special_fines AS a
                                                      WHERE a.status IN (?,?)", "amount",['Active', 'Pending']);
              if(is_null($fines)){
                $fines = 0;
              }
              ?>
              <h3><?= formatNumberShort($fines) ?><sup style="font-size: 20px">RWF</sup></h3>

              <p>Unpaid Fines</p>
            </div>
            <div class="icon">
              <i class="ion ion-person-add"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-red">
            <div class="inner">
              <?php
              $overdues = returnSingleField($db, $s = "SELECT  SUM(IF(a.amount = 0, b.loan_amount_term, a.amount)) AS amount
                                                          FROM lend_payments AS a
                                                          INNER JOIN member_loans AS b
                                                          ON a.borrower_loan_id = b.id
                                                          WHERE a.status IN(?,?) AND a.payment_sched < ?
                                                          ", "amount", ['UNPAID', 'Pending', (new \DateTime())->format('Y-m-d')]);
              if(is_null($overdues)){
                $overdues = 0;
              }
              ?>
              <h3><?= formatNumberShort($overdues) ?><sup style="font-size: 20px">RWF</sup></h3>

              <p>Overdue Payments</p>
            </div>
            <div class="icon">
              <i class="ion ion-pie-graph"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
      </div>

      <div class="row">
        <div class="col-xs-12">
          <!-- BAR CHART -->
          <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title">Loan Payment progress</h3>
            </div>
            <div class="box-body">
              <div class="chart">
                <canvas id="barChart" style="height:230px"></canvas>
              </div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
      </div>
    </section>
    
    <!-- /.content -->
  </div>
 
  <!-- /.content-wrapper -->
  <?php include('./footer.php'); ?>

  <script type="text/javascript">
    $(document).ready(function(){
      var areaChartData = {
      labels  : ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
      datasets: [
        {
          label               : 'Electronics',
          fillColor           : 'rgba(210, 214, 222, 1)',
          strokeColor         : 'rgba(210, 214, 222, 1)',
          pointColor          : 'rgba(210, 214, 222, 1)',
          pointStrokeColor    : '#c1c7d1',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(220,220,220,1)',
          data                : [65, 59, 80, 81, 56, 55, 40]
        },
        {
          label               : 'Digital Goods',
          fillColor           : 'rgba(60,141,188,0.9)',
          strokeColor         : 'rgba(60,141,188,0.8)',
          pointColor          : '#3b8bba',
          pointStrokeColor    : 'rgba(60,141,188,1)',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(60,141,188,1)',
          data                : [28, 48, 40, 19, 86, 27, 90]
        }
      ]
    }
    var barChartCanvas                   = $('#barChart').get(0).getContext('2d')
    var barChart                         = new Chart(barChartCanvas)
    var barChartData                     = areaChartData
    barChartData.datasets[1].fillColor   = '#00a65a'
    barChartData.datasets[1].strokeColor = '#00a65a'
    barChartData.datasets[1].pointColor  = '#00a65a'
    var barChartOptions                  = {
      //Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
      scaleBeginAtZero        : true,
      //Boolean - Whether grid lines are shown across the chart
      scaleShowGridLines      : true,
      //String - Colour of the grid lines
      scaleGridLineColor      : 'rgba(0,0,0,.05)',
      //Number - Width of the grid lines
      scaleGridLineWidth      : 1,
      //Boolean - Whether to show horizontal lines (except X axis)
      scaleShowHorizontalLines: true,
      //Boolean - Whether to show vertical lines (except Y axis)
      scaleShowVerticalLines  : true,
      //Boolean - If there is a stroke on each bar
      barShowStroke           : true,
      //Number - Pixel width of the bar stroke
      barStrokeWidth          : 2,
      //Number - Spacing between each of the X value sets
      barValueSpacing         : 5,
      //Number - Spacing between data sets within X values
      barDatasetSpacing       : 1,
      //String - A legend template
      legendTemplate          : '<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<datasets.length; i++){%><li><span style="background-color:<%=datasets[i].fillColor%>"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>',
      //Boolean - whether to make the chart responsive
      responsive              : true,
      maintainAspectRatio     : true
    }

    barChartOptions.datasetFill = false
    barChart.Bar(barChartData, barChartOptions)
    });
  </script>
</body>
</html>

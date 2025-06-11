<?php

include('./header.php');

$active="dashboard";
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
        <div class="col-lg-4 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">
              <?php
              //Get all social saving amount in the database
              $social_savings = returnSingleField($db, "SELECT SUM(amount) AS amount FROM sacial_saving", "amount");
              ?>
              <h3><?= number_format($social_savings) ?><sup style="font-size: 20px">RWF</sup></h3>

              <p>Total Social Savings</p>
            </div>
            <div class="icon">
              <i class="ion ion-bag"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
      </div>

    </section>
    
    <!-- /.content -->
  </div>
 
  <!-- /.content-wrapper -->
  <?php include('./footer.php'); ?>

  <script type="text/javascript">
    // $(document).ready(function(){
    //   var areaChartData = {
    //   labels  : ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
    //   datasets: [
    //     {
    //       label               : 'Electronics',
    //       fillColor           : 'rgba(210, 214, 222, 1)',
    //       strokeColor         : 'rgba(210, 214, 222, 1)',
    //       pointColor          : 'rgba(210, 214, 222, 1)',
    //       pointStrokeColor    : '#c1c7d1',
    //       pointHighlightFill  : '#fff',
    //       pointHighlightStroke: 'rgba(220,220,220,1)',
    //       data                : [65, 59, 80, 81, 56, 55, 40]
    //     },
    //     {
    //       label               : 'Digital Goods',
    //       fillColor           : 'rgba(60,141,188,0.9)',
    //       strokeColor         : 'rgba(60,141,188,0.8)',
    //       pointColor          : '#3b8bba',
    //       pointStrokeColor    : 'rgba(60,141,188,1)',
    //       pointHighlightFill  : '#fff',
    //       pointHighlightStroke: 'rgba(60,141,188,1)',
    //       data                : [28, 48, 40, 19, 86, 27, 90]
    //     }
    //   ]
    // }
    // var barChartCanvas                   = $('#barChart').get(0).getContext('2d')
    // var barChart                         = new Chart(barChartCanvas)
    // var barChartData                     = areaChartData
    // barChartData.datasets[1].fillColor   = '#00a65a'
    // barChartData.datasets[1].strokeColor = '#00a65a'
    // barChartData.datasets[1].pointColor  = '#00a65a'
    // var barChartOptions                  = {
    //   //Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
    //   scaleBeginAtZero        : true,
    //   //Boolean - Whether grid lines are shown across the chart
    //   scaleShowGridLines      : true,
    //   //String - Colour of the grid lines
    //   scaleGridLineColor      : 'rgba(0,0,0,.05)',
    //   //Number - Width of the grid lines
    //   scaleGridLineWidth      : 1,
    //   //Boolean - Whether to show horizontal lines (except X axis)
    //   scaleShowHorizontalLines: true,
    //   //Boolean - Whether to show vertical lines (except Y axis)
    //   scaleShowVerticalLines  : true,
    //   //Boolean - If there is a stroke on each bar
    //   barShowStroke           : true,
    //   //Number - Pixel width of the bar stroke
    //   barStrokeWidth          : 2,
    //   //Number - Spacing between each of the X value sets
    //   barValueSpacing         : 5,
    //   //Number - Spacing between data sets within X values
    //   barDatasetSpacing       : 1,
    //   //String - A legend template
    //   legendTemplate          : '<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<datasets.length; i++){%><li><span style="background-color:<%=datasets[i].fillColor%>"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>',
    //   //Boolean - whether to make the chart responsive
    //   responsive              : true,
    //   maintainAspectRatio     : true
    // }

    // barChartOptions.datasetFill = false
    // barChart.Bar(barChartData, barChartOptions)
    // });
  </script>
</body>
</html>

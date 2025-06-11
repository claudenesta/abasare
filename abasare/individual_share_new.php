<?php include('DBController.php');

if($_POST['dividand']){
    $a=$_POST['m_id'];
	$b=$_POST['prof'];
	$month=$currentmonth;
	$yaer=$currentyear;
 for($exe = 0; $exe < count($a); $exe++ )
    {
 $sql = "INSERT INTO `profite` (`id`, `m_id`, `amount`, `month`, `year`) VALUES (NULL, '$a[$exe]', '$b[$exe]', '$month', '$yaer')";
 mysqli_query($con, $sql);
     header("location:general_report.php");  
    }
}

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
    </section><br/>
    <div class="col-md-12">
      <!-- ********** ALERT MESSAGE START******* -->
       <div class="col-md-12">
      <!-- ********** ALERT MESSAGE START******* -->
        
     </div>       <!-- ********** ALERT MESSAGE END******* -->
     </div>
          <div class="col-md-12">
          <div class="box box-danger">
            <div class="box-body">
              
    <h2>Dividant of Interest Earned in <?php echo $long[$currentmonth].'  '.$currentyear;?></h2>

  <div class="row">
    <div class="col-xs-12">
      <div class="table-responsive" data-pattern="priority-columns">
          <form  method="post" action="#"> 
           <table  class="table table-bordered table-hover" >
        <thead>
            <tr class="bg-primary">
              <th data-priority="1">N<sup><u>0</u></sup></th>
              <th data-priority="2">Member Name</th>
              <th data-priority="3">Savings</th>
              <th data-priority="3">Capital </th>
              <th data-priority="3">Interst </th>
              <th data-priority="3">G.Total </th>
              <th data-priority="3">Month</th>
              <th data-priority="3">Year</th>
              <th data-priority="3">Invested </th>
              <th data-priority="3">Earned</th>

            </tr>
          </thead>
           <tbody>
        <?php 
        $mid=0;
        $id=0;
        $sql = "SELECT *, CONCAT(fname,' ',lname) as fullname  FROM `member` order by fname";
$qu=mysqli_query($con,$sql);
$total_earnings = 0;
$total_invested = 0;
while($row=mysqli_fetch_array($qu)){
    $mid=$row['id'];
    $id++;
        ?>
          <tr>
              <td data-priority="1"><?php  echo $id; ?></td>
              <td data-priority="2"><?php echo $row['fullname']; ?></td>
              <td data-priority="3">
                 <?php 
        $sqlsavi = "SELECT sum(sav_amount) as saving FROM `saving` where member_id='$mid' AND STR_TO_DATE(CONCAT(year,'/',month),'%Y/%m')<=STR_TO_DATE(CONCAT(".$currentyear.",'/',".$currentmonth."),'%Y/%m')";
        $qusaving=mysqli_query($con,$sqlsavi);
        $saving=mysqli_fetch_array($qusaving);
        if($saving['saving']!=0){
            
        echo $saving['saving'];
        $savi=$saving['saving'];
        }else{
          echo 0;
        }
        ?>
                  </td>
              <td data-priority="3">
              <?php  $sqlcap= "SELECT sum(amount) as capt FROM `capital_share`  where member_id='$mid' AND STR_TO_DATE(CONCAT(year,'/',month),'%Y/%m')<=STR_TO_DATE(CONCAT(".$currentyear.",'/',".$currentmonth."),'%Y/%m')";
        $qucap=mysqli_query($con,$sqlcap);
        $capita=mysqli_fetch_array($qucap);
        if($capita['capt']!=0){
          echo $capita['capt'];
          $capt=$capita['capt'];
        } else{
          echo 0;
        }
                ?>
        </td>
<td data-priority="3">
                  
        <?php 
$sqlcapinte= "SELECT sum(amount) as inves FROM `profite` where m_id='$mid' AND STR_TO_DATE(CONCAT(year,'/',month),'%Y/%m')<=STR_TO_DATE(CONCAT(".$currentyear.",'/',".$currentmonth."),'%Y/%m')";
$quinter=mysqli_query($con,$sqlcapinte);
$row=mysqli_fetch_array($quinter);
if($row['inves']!=0){
echo $row['inves'];
$inv=$row['inves'];
}else{echo 0;}
        ?>
         </td>
              <td data-priority="3"><?php echo $saving['saving']+$capita['capt']+$row['inves']; ?> </td>
              <td data-priority="3"><?php echo $long[$currentmonth]; ?></td>
              <td data-priority="3"><?php echo $currentyear; ?></td>
              <td data-priority="3">
                  <?php 
                  $late=$_POST['inves_rate'];
                  $profi=$_POST['profit'];
                  $ttinv=$_POST['toinv'];
                  ($ttinv == 0 ? $ttinv=1 : $ttinv=$_POST['toinv']);
                  $tot=$saving['saving']+$capita['capt']+$row['inves']; 
                  $investi=$tot*$late; echo round($investi);
                  $total_invested += $investi;
                  ?> 
                  </td>
              <td data-priority="3">
                  <input size="2" type="hidden" name="m_id[]"  value="<?php  echo $mid; ?>">
                  <input size="5" type="hidden" name="prof[]"  value="<?php echo round($profi*$investi/$ttinv);  ?>">
                  <?php echo round($profi*$investi/$ttinv); 
                  $inter=$profi*$investi/$ttinv;
                  echo ";".$profi."*".$investi."/".$ttinv;
                  $total_earnings += $inter;
                  ?>
              </td>
            </tr> 
            <?php
            } ?>
 </tbody>
 <tfoot>
   <tr>
     <td></td>
     <td></td>
     <td></td>
     <td></td>
     <td></td>
     <td></td>
     <td></td>
     <td></td>
     <td><?= number_format($total_invested) ?></td>
     <td style="text-align: right;"><?= number_format($total_earnings) ?><br /><?= number_format($profi) ?></td>
   </tr>
 </tfoot>
 </table>
  <div style="padding-left:50%">
      <?php 
      $sq = "SELECT * FROM `profite` where  month='$currentmonth' AND year='$currentyear'";
      $quy=mysqli_query($con,$sq);
      if(!mysqli_num_rows($quy)){
?>
<input type="submit" class="btn btn-primary" name="dividand" value="Closing Month ">
<a href="export_to_excel_indivi.php?inves_rate=<?php echo $late; ?>&&profit=<?php echo $profi;?>&&toinv=<?php echo$ttinv; ?>">Export to excel</a>
<?php } else{?>
<a href="export_to_excel_indivi.php?inves_rate=<?php echo $late; ?>&&profit=<?php echo $profi;?>&&toinv=<?php echo$ttinv; ?>">Export to excel</a>
<?php } ?>
</div>
</form>
        
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

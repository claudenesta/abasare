<?php include('../DBController.php');

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
  
 <?php include('./header.php');
 $active = 'general-report';
 include('./menu.php'); ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       Dividant of Interest Earned in <?php echo $long[$currentmonth].'  '.$currentyear;?>
      </h1>
      <ol class="breadcrumb">
          <li><a href="/admin/"><i class="fa fa-dashboard"></i>Home</a></li>
          <li><a href="#">Reports</a></li>
          <li class="active"><a href="general_report.php">Individual Share</a></li>
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
$sql = "SELECT *, CONCAT(fname,' ',lname) as fullname  FROM `member`order by fname";
$qu=mysqli_query($con,$sql);
$total_earnings = 0;
$total_invested = 0;
$global_tot = 0;
$total_savings = 0;
$total_capital = 0;
$total_earned = 0;
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
          // echo $sqlsavi; die();
        $qusaving=mysqli_query($con,$sqlsavi);
        $saving=mysqli_fetch_array($qusaving);
        if($saving['saving']!=0){
            
        echo $saving['saving'];
        $savi=$saving['saving'];
        $total_savings += $savi;
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
          $total_capital += $capt;
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

  $total_earned += $row['inves'];
}else{echo 0;}
  $my_tot = $saving['saving']+$capita['capt']+$row['inves'];
  $global_tot += $my_tot;
        ?>
         </td>
              <td data-priority="3"><?php echo $my_tot ?> </td>
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
                  // echo ";".$profi."*".$investi."/".$ttinv;
                  $total_earnings += $inter;
                  ?>
              </td>
            </tr> 
            <?php
            } 
            ?>
 </tbody>
 <tfoot>
   <tr>
     <td></td>
     <td></td>
     <td><?= number_format($total_savings) ?></td>
     <td><?= number_format($total_capital) ?></td>
     <td><?= number_format($total_earned) ?></td>
     <td><?= number_format($global_tot) ?></td>
     <td></td>
     <td></td>
     <td><?= number_format($total_invested) ?></td>
     <td style="text-align: right;"><?= number_format($total_earnings) ?></td>
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
        <a href="export_to_excel_indivi.php?inves_rate=<?php echo $late; ?>&profit=<?php echo $profi;?>&toinv=<?php echo$ttinv; ?>">Export to excel</a>
        <?php 
      } else{
        ?>
        <a href="export_to_excel_indivi.php?inves_rate=<?php echo $late; ?>&profit=<?php echo $profi;?>&toinv=<?php echo$ttinv; ?>">Export to excel</a>
        <?php 
      }
      ?>
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
  <?php include('./footer.php'); ?>

<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>
</body>
</html>

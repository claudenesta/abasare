<?php include('../DBController.php');
error_reporting(E_ALL & ~E_NOTICE & E_WARNING & E_PARSE & E_ERROR);
  $output = '';
         $output .= '
          <table border="1" cellspacing="0" cellpadding="3">
                <tr>
                    <td colspan="10" style="font-size: 16px; font-weight: bold; border: 0px solid #000;">&nbsp;Abasare Group</td>
                </tr>
                <tr>
                    <td colspan="10" style="font-size: 16px; font-weight: bold; border: 0px solid #000;">&nbsp;'.(new DateTime($currentyear.'-'.$currentmonth.'-01'))->format('F Y').' </td>
                </tr>
                <tr>
                    <td colspan="10" style="text-align: center; font-size: 16px; border: 0px solid #000; font-weight: bold;">
                        Individual Shares Monthly Report
                    </td>
                </tr>
              <tr>
              <th class="col-xs-1"  style="text-align: center; font-size: 16px">N<sup><u>0</u></sup></th>
              <th class="col-xs-1" style="text-align: center; font-size: 16px">Member Name</th>
              <th class="col-xs-1" style="text-align: center; font-size: 16px">Savings</th>
              <th class="col-xs-1" style="text-align: center; font-size: 16px">Capital </th>
              <th class="col-xs-1" style="text-align: center; font-size: 16px">Interst </th>
              <th class="col-xs-1" style="text-align: center; font-size: 16px">G.Total </th>
              <th class="col-xs-1" style="text-align: center; font-size: 16px">Month</th>
              <th class="col-xs-1" style="text-align: center; font-size: 16px">Year</th>
              <th class="col-xs-1" style="text-align: center; font-size: 16px">Invested </th>
              <th class="col-xs-1" style="text-align: center; font-size: 16px">Earned</th>
              </tr>
         ';

        $mid=0;
        $id=0;
        $sql = "SELECT *, CONCAT(fname,' ',lname) as fullname  FROM `member` order by fname";
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
  
    $sqlsavi = "SELECT sum(sav_amount) as saving FROM `saving` where member_id='$mid' AND STR_TO_DATE(CONCAT(year,'/',month),'%Y/%m')<=STR_TO_DATE(CONCAT(".$currentyear.",'/',".$currentmonth."),'%Y/%m')";
    $qusaving=mysqli_query($con,$sqlsavi);
    $saving=mysqli_fetch_array($qusaving); 

    $sqlcap= "SELECT sum(amount) as capt FROM `capital_share`  where member_id='$mid' AND STR_TO_DATE(CONCAT(year,'/',month),'%Y/%m')<=STR_TO_DATE(CONCAT(".$currentyear.",'/',".$currentmonth."),'%Y/%m')";
    $qucap=mysqli_query($con,$sqlcap);
    $capita=mysqli_fetch_array($qucap);

    $sqlcapinte= "SELECT sum(amount) as inves FROM `profite` where m_id='$mid' AND STR_TO_DATE(CONCAT(year,'/',month),'%Y/%m')<=STR_TO_DATE(CONCAT(".$currentyear.",'/',".$currentmonth."),'%Y/%m')";
    $quinter=mysqli_query($con,$sqlcapinte);
    $rowinv=mysqli_fetch_array($quinter);
  
    $output.='<tr>
              <td data-priority="1">'.$id.'</td>
              <td data-priority="2">'.$row['fullname'].'</td>
              <td data-priority="3">';
    if($saving['saving']!=0){
    $savi=number_format($saving['saving']);
        $total_savings += $saving['saving'];
    }else{$savi= 0;}
            $output .=$savi.' </td>
                  <td data-priority="3">';

    if($capita['capt']!=0){
        $capt=number_format($capita['capt']);
        $total_capital += $capita['capt'];
    }else{$capt=0;}
           $output .=$capt.' </td>
    <td data-priority="3">';

        if($rowinv['inves']!=0){
            $inv=number_format($rowinv['inves']);
            $total_earned += $rowinv['inves'];
        }else {
            $inv=0;
        }

        $single_total = $saving['saving']+$capita['capt']+$rowinv['inves'];
        $global_tot += $single_total;

         $output .= $inv.'</td>
              <td data-priority="3">'.number_format($single_total).'</td>
              <td data-priority="3">'.$long[$currentmonth].'</td>
              <td data-priority="3">'.$currentyear.'</td>
              <td data-priority="3">';
      
                  $late=$_GET['inves_rate'];
                  $profi=$_GET['profit'];
                  $ttinv=$_GET['toinv'];
                  ($ttinv == 0 ? $ttinv=1 : $ttinv);
                  $tot=$saving['saving']+$capita['capt']+$rowinv['inves']; 
                  $investi=$tot*$late; 
                  // number_format(round($investi));
                  $total_invested += $investi;
                    $earnings = $profi*$investi/$ttinv;
                   $output .= number_format(round($investi)).'</td>
              <td data-priority="3">'.number_format(round($profi*$investi/$ttinv)).'
              </td></tr>';
              $total_earnings += $earnings;
           
}
$output .= "<tr style='font-weight: bold; font-size: 14px;'>
                <td colspan='2' style='text-align: center; font-weight: bold; font-size: 14px;'>Total</td>
                <td>".(number_format($total_savings))."</td>
                <td>".(number_format($total_capital))."</td>
                <td>".(number_format($total_earned))."</td>
                <td>".(number_format($global_tot))."</td>
                <td colspan=2></td>
                <td>".(number_format($total_invested))."</td>
                <td>".(number_format($total_earnings))."</td>
            </tr>";



          $output .='</table>';
         header("Content-Type: application/xls");
          header("Content-Disposition: attachment; filename=individual_earn.xls");   

          echo $output;

?>
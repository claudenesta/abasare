<?php 
include('./header.php');
$membe_id=$_SESSION['acc'];
?>
<style>
.alert {
  padding: 20px;
  background-color: #f44336;
  color: white;
  opacity: 1;
  transition: opacity 0.6s;
  margin-bottom: 15px;
}

.alert.success {background-color: #4CAF50;}
.alert.info {background-color: #2196F3;}
.alert.warning {background-color: #ff9800;}

.closebtn {
  margin-left: 15px;
  color: white;
  font-weight: bold;
  float: right;
  font-size: 22px;
  line-height: 20px;
  cursor: pointer;
  transition: 0.3s;
}

.closebtn:hover {
  color: black;
}
</style>
  <!-- Left side column. contains the logo and sidebar -->
  
  <?php 
  $active = "savings";
  include('menu.php'); 

  //get the first saving year for this memmber
  $today = new \DateTime();
  $start_year = (int) returnSingleField($db, $sql = "SELECT year_to_save_for FROM saving WHERE member_id = ? ORDER BY year_to_save_for ASC LIMIT 0,1", "year_to_save_for", [$_SESSION['user']['member_acc']]);
  // echo $sql;

  //Get the last unpaid overdue saving record
  $saving_overdone = first($db, "SELECT a.id, 
                                        a.month, 
                                        a.year, 
                                        a.saving_overdue,
                                        CONCAT(a.year, '-', IF(a.month < 10, '0',''), a.month, '-01') AS contribution_month,
                                        b.id AS paid_saving
                                        FROM overdue_settings AS a 
                                        LEFT JOIN saving AS b
                                        ON a.id = b.overdue_id AND b.member_id = ?
                                        WHERE a.saving_overdue < ?
                                        HAVING paid_saving IS NULL
                                        ORDER BY a.saving_overdue ASC
                                        LIMIT 0,1
                                        ", [$_SESSION['user']['member_acc'], $today->format('Y-m-d')]);


// Calculating the next scheduled saving month
  $last_saving = first($db, "SELECT id, member_id, sav_amount, month_to_save_for, year_to_save_for FROM saving WHERE member_id = ? ORDER BY year_to_save_for DESC, month_to_save_for DESC", [$_SESSION['user']['member_acc']]);
  if($last_saving){
    $saving_year = $last_saving['year_to_save_for'];
    $saving_month = $last_saving['month_to_save_for'];
    if(12 == $saving_month){
      $saving_month = 1;
      $saving_year++;
    } else {
      $saving_month++;
    }
  }
// To be displayed to a button and used as deadline
  $saving_info = new \DateTime($saving_year."-".($saving_month < 10?"0":"").$saving_month."-01");
  $default_deadline = $saving_info->format("Y-m-t");

  $saving_overdone = first($db, "SELECT a.*,
                                        COALESCE(b.saving_overdue, '{$default_deadline}') AS saving_overdue
                                        FROM (
                                          SELECT
                                                  '{$saving_year}' AS year,
                                                  '{$saving_month}' AS month
                                        ) AS a
                                        LEFT JOIN overdue_settings AS b
                                        ON a.year = b.year AND a.month = b.month");

  $minimum_saving = returnSingleField($db, "SELECT COALESCE(MAX(a.saving), b.sav_amount) AS required_amount
                                                    FROM (
                                                      SELECT  b.saving,
                                                              a.member_id
                                                              FROM member_loans AS a
                                                              INNER JOIN member_loan_settings AS b
                                                              ON a.id = b.loan_id
                                                              WHERE a.status = ?
                                                              AND a.member_id = ?
                                                      ) AS a
                                                    LEFT JOIN (
                                                      SELECT  a.member_id,
                                                              a.sav_amount
                                                              FROM saving AS a
                                                              WHERE a.member_id = ?
                                                              ORDER BY a.id DESC
                                                              LIMIT 0, 1
                                                      ) AS b
                                                    ON a.member_id = b.member_id
                                                    ", "required_amount", ["ACTIVE", $_SESSION['user']['member_acc'], $_SESSION['user']['member_acc'] ]);
  ?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Savings
      </h1>
      <ol class="breadcrumb">
          <li><a href="/member/"><i class="fa fa-dashboard"></i>Home</a></li>
          <li><a href="#">Payment</a></li>
          <li class="active"><a href="/member/savings.php">Savings</a></li>
      </ol>

    </section><br/>
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box box-success">
            <div class="box-header ">
              <h3 class="box-title">Savings Information</h3>
              <div class="btn-group">
                <select class="select2" style="width: 200px" id="saving_year">
                  <?php
                  for($year = (int) (new \DateTime())->format('Y'); $year >= $start_year; $year--){
                    ?>
                    <option value="<?= $year ?>"><?= $year ?></option>
                    <?php
                  }
                  ?>
                </select>
              </div>
              <div class="btn-group pull-right">
                <button class="btn btn-sm btn-primary">Contribution: <?= number_format($minimum_saving) ?> RWF</button>
                <?php
                // var_dump($saving_overdone);
                if(!is_null($saving_overdone) && $saving_overdone){
                  $contribution_date = new \DateTime($saving_overdone['saving_overdue']);

                  $toDay = new \DateTime();

                  
                  $class = "success";

                  $required_fines = 0;

                  if($contribution_date->getTimestamp() < $toDay->getTimestamp()){
                    $class = "danger";
                    $delay = $contribution_date->diff($toDay);
                    $delay_days = $delay->days;
                    $required_fines = $delay_days * 100;
                    ?>
                    <button class="btn btn-warning btn-sm">Delay Fine: <?= number_format($required_fines) ?> RWF</button>
                    <?php
                  }
                  ?>

                  <!-- <a href="savings/new_saving.php?overdue_id=<?= $saving_overdone['id'] ?>" title="<?= $required_fines > 0?(number_format($required_fines)." RWF fine is required"):"No Fine is required" ?>" class="btn btn-<?= $class ?> btn-sm open_box"><i class="fa fa-plus"></i> Saving for <?= $saving_info->format("F Y") ?></a> -->
                  <a href="savings/new_saving.php" title="<?= $required_fines > 0?(number_format($required_fines)." RWF fine is required"):"No Fine is required" ?>" class="btn btn-<?= $class ?> btn-sm open_box"><i class="fa fa-plus"></i> New Saving</a>
                  <?php
                }
                ?>
                
              </div>
            </div>
            <div class="box-body">
              <div class="container-fluid">
                <div class="row">
                  <div class="col-md-12" id="savings_container">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
  <!-- /.content-wrapper -->
  <?php include('./footer.php'); ?>

  <script type="text/javascript">
    $(document).ready(function(){

      $("#saving_year").select2({
        placeholder: "Select Saving Year"
      }).bind("change", function(e){

        $("#savings_container").load("savings/index.php?year=" + $("#saving_year").val(), function(){

        });
      }).trigger("change");
      // $("#savings_container").load("savings/index.php?member_id=<?= $membe_id ?>");

      $(".open_box").click(function(e){
        e.preventDefault();
        var clicked = $(this);
        var url = clicked.attr("href");
        var old_data = clicked.html();
        $(this).html("Please Wait");
        $("#modal_member").find(".modal-content").load(url, function(){
          clicked.html(old_data);
          refresh_target_containner = '';
          refresh_url= '';
          $("#modal_member").modal("show");
        });
      });
    });
  </script>
</body>
</html>
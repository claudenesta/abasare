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
  $active = "loans_payments";
  include('menu.php'); 

  //get the first saving year for this memmber
  $today = new \DateTime();
  $start_year = (int) returnSingleField($db, $sql = "SELECT YEAR(a.payment_sched) AS first_year
                                                            FROM lend_payments AS a 
                                                            INNER JOIN member_loans AS b
                                                            ON a.borrower_loan_id = b.id AND b.status = ? AND b.member_id = ?
                                                            WHERE a.status = ? ORDER BY payment_sched ASC LIMIT 0,1", "first_year", ["ACTIVE", $_SESSION['acc'], "UNPAID"]);
  // echo $sql;
  ?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Loan Payments
      </h1>
      <ol class="breadcrumb">
          <li><a href="/member/"><i class="fa fa-dashboard"></i>Home</a></li>
          <li><a href="#">Payment</a></li>
          <li class="active"><a href="/member/loan_payments.php">Loans</a></li>
      </ol>

    </section><br/>
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box box-success">
            <div class="box-header ">
              <h3 class="box-title">Loans Payment Information</h3>
              <div class="btn-group">
                <select class="select2" style="width: 200px" id="payment_year">
                  <?php
                  for($year = (int) (new \DateTime())->format('Y'); $year >= $start_year; $year--){
                    ?>
                    <option value="<?= $year ?>"><?= $year ?></option>
                    <?php
                  }
                  ?>
                </select>
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

      $("#payment_year").select2({
        placeholder: "Select Saving Year"
      }).bind("change", function(e){

        $("#savings_container").load("loans/payments.php?year=" + $("#payment_year").val(), function(){

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

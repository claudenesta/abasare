<?php include('../DBController.php');
$membe_id=$_SESSION['acc'];

include('./header.php');

$active="approved_loans";
include('menu.php'); 
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Loan Request
      </h1>
      <ol class="breadcrumb">
        <li><a href="/president/"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Loans</a></li>
        <li class="active"><a href="#">Approved</a></li>
      </ol>
    </section>
    
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box box-primary">
            <div class="box-header ">
              <!-- <h3 class="box-title">Some Data</h3> -->
            </div>
            <div class="box-body">
              <div class="container-fluid">
                <div class="row">
                  <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                      <label>Report Dates:</label>

                      <div class="input-group">
                        <div class="input-group-addon">
                          <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control pull-right" id="reservation">
                      </div>
                      <!-- /.input group -->
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12" id="loans_container">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    
    <!-- /.content -->
  </div>
 
  <!-- /.content-wrapper -->
  <?php include('./footer.php'); ?>

  <script type="text/javaScript"> 
    $(document).ready(function(){

      $('#reservation').daterangepicker({ locale: { format: 'YYYY-MM-DD' }}).bind("change", function(e){
        $("#loans_container").html("<div class='alert alert-warnign'>Please Wait</div>");
        $("#loans_container").load("./actions/approved_loans.php?date=" + $("#reservation").val().replace(/ /g,"%20"), function(){
          //Here the loans table now fully loaded
        });
      });
      $("#loans_container").html("<div class='alert alert-warnign'>Please Wait</div>");
      $("#loans_container").load("./actions/approved_loans.php?date=" + $("#reservation").val().replace(/ /g,"%20"), function(){
        //Here the loans table now fully loaded
      });
    });
  </script>
</body>
</html>

<?php 
include('../DBController.php');
$membe_id=$_SESSION['acc'];

include('./header.php');

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
  $active = "signatory";
  include('menu.php'); ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Signatory
      </h1>
      <ol class="breadcrumb">
          <li><a href="/member/"><i class="fa fa-dashboard"></i>Home</a></li>
          <li class=""><a href="#">Loans</a></li>
          <li class="active"><a href="#">Signatory</a></li>
      </ol>

    </section><br/>
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box box-warning">
            <div class="box-header ">
              <h3 class="box-title">Signatory Request</h3>
            </div>
            <div class="box-body">
              <div class="container-fluid">
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
  </div>
  <!-- /.content-wrapper -->
  <?php include('./footer.php'); ?>

  <script type="text/javascript">
    $(document).ready(function(){
      $("#loans_container").load("signatory/index.php");

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

<?php

include('./header.php');

$active="interest";
include('./menu.php'); 
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Monthly Shared Interest 
      </h1>
      <ol class="breadcrumb">
        <li><a href="/president/"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active"><a href="#">Unpaid Fines</a></li>
      </ol>
    </section>
    
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box box-primary">
            <div class="box-header ">
              <h3 class="box-title">Unpaid Fines Information</h3>
              <div class="btn-group pull-right">
                <select class="form-control" id="select_year">
                  <?php
                  $date = new \DateTime('2021-12-01');
                  $now = new \DateTime();
                  
                  while($date < $now){
                    ?>
                    <option value='<?= $date->format("Y-m") ?>'><?= $date->format("F Y") ?></option>
                    <?php
                    $date->modify("+1 month");

                    
                  }
                  ?>
                </select>
              </div>
            </div>
            <div class="box-body">
              <div class="container-fluid">
                <div class="row">
                  <div class="col-md-12" id="interest_container">
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
      $("#select_year").select2().bind("change", function(e){
        $("#interest_container").load("interest/shared_interest.php?year=" + $(this).val(), function(){
          //
        });
      });
      $("#fines_container").load("./fines/unpaid_fines.php", function(){
        //Here the loans table now fully loaded
      });

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

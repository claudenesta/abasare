<?php include('../DBController.php');
$membe_id=$_SESSION['acc'];

include('./header.php');

$active="loans";
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
        <li class="active"><a href="#">Loans</a></li>
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
      
      $("#loans_container").load("./loans.php", function(){
        //Here the loans table now fully loaded
      })
    });
  </script>
</body>
</html>

<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ...existing code...
include('./header.php');
$member_id = $_SESSION['acc'];
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

<?php 
$active = "fines";
include('menu.php'); 
?>

<div class="content-wrapper">
    <section class="content-header">
      <h1>Fines Payments</h1>
      <ol class="breadcrumb">
          <li><a href="/member/"><i class="fa fa-dashboard"></i> Home</a></li>
          <li><a href="#">Payment</a></li>
          <li class="active"><a href="/member/fines.php">Fines</a></li>
      </ol>
    </section>
    
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box box-warning">
            <div class="box-body">
              <div class="container-fluid">
                <div class="row">
                  <div class="col-md-12" id="savings_container"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
</div>

<?php include('./footer.php'); ?>

<script type="text/javascript">
$(document).ready(function(){
    // Load unpaid fines initially
    loadFinesContainer();
    
    // Handle modal opening
    $(document).on('click', '.open_box', function(e){
        e.preventDefault();
        var clicked = $(this);
        var url = clicked.attr("href");
        var old_data = clicked.html();
        
        clicked.html('<i class="fas fa-spinner fa-spin"></i>');
        $("#modal_member").find(".modal-content").load(url, function(){
            clicked.html(old_data);
            $("#modal_member").modal("show");
        });
    });
    
    // Handle successful payment
    $(document).on('paymentSuccess', function() {
        loadFinesContainer();
    });
});

function loadFinesContainer() {
    $("#savings_container").load("fines/unpaid_fines.php");
}
</script>
</body>
</html>
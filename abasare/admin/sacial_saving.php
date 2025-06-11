<?php 
include('../DBController.php');
			
			if(isset($_POST['socialbtn'])){
			    $amount=$_POST['amount'];
			    $ref=$_POST['ref'];
			     $month= $_POST['month'];
				 $year= $_POST['year'];
			    $sql = "INSERT INTO `social_logs` (`id`, `saving_ref`, `amount`, `month`, `year`) VALUES (NULL, '$ref', '$amount', '$month', '$year')";
			    MYSQLI_QUERY($con,$sql);
               ?>
            <meta http-equiv="refresh" content="0; URL=sacial_saving.php">
            <?php } ?>

<?php 
include('./header.php'); 
if(isset($_POST["social_save"])){

  $db->beginTransaction();
  try{
    saveData($db, "INSERT INTO sacial_saving SET m_id=?, amount=?, month=?, year=?", [
      $_POST['member'],
      $_POST['saving'],
      $_POST['month'],
      $_POST['year']
    ]);
    $db->commit();
    ?>
    <script type="text/javascript">
      alert("New Contribution Saved!");
    </script>
    <meta http-equiv="refresh" content="0; URL=./sacial_saving.php">
    <?php
  } catch(\Exception $e){
    $db->rollBack();
    ?>
    <script type="text/javascript">
      alert("Error:\n<?= $e->getMessage() ?>");
    </script>
    <?php
  }
}

$active = "social-saving";
include('./menu.php'); 
?>

  <!-- Content Wrapper. Contains page content -->
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Social Saving
      </h1>

      <ol class="breadcrumb">
          <li><a href="/admin/"><i class="fa fa-dashboard"></i>Home</a></li>
          <li><a href="#">Savings</a></li>
          <li class="active">Social</li>
      </ol>
    </section>
      <?php
        $viewfund="SELECT sum(amount) as fund FROM `sacial_saving`";
        $viewwithdraw="SELECT sum(amount) as withdr FROM `social_logs`";
        $depose=mysqli_query($con,$viewfund);
        $withdraw=mysqli_query($con,$viewwithdraw);
        $row1=mysqli_fetch_array($depose);
        $row2=mysqli_fetch_array($withdraw);
        ?>


    <!-- Main content -->
    <form action="#" class="" id="table_form" method="post" accept-charset="utf-8">
   
    <section class="content">
      <div class="row mt-2 w-100">
        <div class="col-sm-4">
          <div class="box box-primary">
            <div class="box-body">
              Social amount: <?php echo number_format($row1['fund']); ?> Frws
            </div>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="box box-danger">
            <div class="box-body">
              Withdrown: <?= number_format($row2['withdr']); ?> Frws
            </div>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="box box-success">
            <div class="box-body">
              Balance: <?= number_format($row1['fund']-$row2['withdr']); ?> Frws
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <!-- ********** ALERT MESSAGE START******* -->
          <div class="col-md-12">
      
     </div>            <!-- ********** ALERT MESSAGE END******* -->
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header ">
              <h3 class="box-title">&nbsp;</h3>
              <div class="pull-left">
                <a class="btn btn-block btn-danger btn-flat" data-toggle="modal" data-target="#modal-withdr"> <i class="fa fa-plus"></i> Withdraw </a>
              </div>
              <div class="box-tools">
                <a class="btn btn-block btn-success btn-flat" data-toggle="modal" data-target="#modal-social"> <i class="fa fa-plus"></i> Deposit </a>
                
              </div>
                          </div>
            <!-- /.box-header -->
            <div class="box-body">
                
                
              <table id="example" class="table table-striped table-bordered" style="width:100%">
                <thead class="bg-primary ">
                <tr>
                  <th>No</th>
                  <th>Member Names</th>
                  <th>Amount</th>
                  <!-- <th>Month</th> -->
                  <!-- <th>Year</th> -->
                  <!-- <th>Action</th> -->
                </tr>
                </thead>
                <tbody>
          				<?php
          				$no=0;
          				$sum=0;
                  $sql="SELECT *, CONCAT(m.fname,' ',m.lname) as fullname,
                                SUM(ss.amount) AS total_social_saving,
                                m.id AS member_id
                                FROM sacial_saving ss 
                                INNER JOIN member m 
                                ON m.id=ss.m_id
                                GROUP BY m.id
                                ";
                  $query=mysqli_query($con,$sql);
                  while($row=mysqli_fetch_array($query)){
                   $sum+=$row['total_social_saving'];   
                      
                  $no++;
          				?>
                  <tr>
				            <td><?php echo $no; ?></td>
                  <td>
                    <a href="./member_info.php?id=<?php echo $row['member_id']; ?>">
                      <?php echo $row['fullname']; ?>
                    </a>
                  </td>
                  <td style="text-align: right;"><?php echo number_format($row['total_social_saving']); ?></td>
                  <!-- <td><?php echo $long[$row['month']]; ?></td> -->
                  <!-- <td><?php echo $row['year']; ?></td> -->
                  <!-- <td><a href="#" data-toggle="modal" data-target="#modal-<?php echo $row['id']; ?>"> Edit </a> |  <a href="delete_member.php?id=<?php echo $row['id'];?>" onclick="return confirm('Are you sure you want to delete this Member?');">Delete</a></td> -->
                </tr>
                <?php } ?>
          </tbody>
          <tfoot>
              <tr>
                  <th></th>
                  <th></th>
                  <th style="text-align: right;"><?php echo number_format($sum); ?></th>
                  <!-- <th></th> -->
                  <!-- <th></th> -->
                  <!-- <th></th> -->
                </tr>
              
              
          </tfoot>
          </table>
            </div>
            <div class="modal fade" id="modal-social">
              <form class="form-horizontal" method="post">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header bg-primary ">
                      <h4 class="modal-title"><span style="color:white"><i class="fa fa-plus"></i> Add New Saving Contribution </span>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </h4>
                    </div>
                    <div class="modal-body">
                     <div class="card card-info">
                        <div class="card-body">
                          <div class="form-group row">
                            <div class="col-sm-6">Member</div>
                            <div class="col-sm-6">Amount</div>
                          </div>
                          <div class="form-group row">

                            <div class="col-sm-6">
                              <select class="form-control" id="member_id" name="member"  style="width: 100%;" required="" >
                                <option value=""></option>
                                <?php
                                $sql="select * from member";
                                $data = returnAllData($db, $sql);
                                foreach($data AS $row){
                                	?>
                                  <option data-url="./get_social_fine.php?member_id=<?= $row['id'] ?>" value="<?php  echo $row['id'];?>"><?php echo $row['fname'].' '.$row['lname']; ?></option>
                                  <?php 
                                } ?>
                              </select>
                            </div>
                            <div class="col-sm-6">
                              <input type="text" placeholder="Amount" class="form-control" name="saving" value="2000">
                            </div>
                          </div>
                          <div class="form-group row">
                            <div class="col-sm-6">Month</div>
                            <div class="col-sm-6">Year</div>
                          </div>
                          <div class="form-group row">
                            <div class="col-sm-6">
                              <select class="form-control" id="month" name="month"  style="width: 100%;" required="" >
                                <?php formMonth($currentmonth); ?>
                              </select>
                            </div>
                            <div class="col-sm-6">
                              <select class="form-control" id="year" name="year"  style="width: 100%;" required="" >
                                <?php formYear($currentyear); ?>
                              </select>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                      <input type="submit" name="social_save" class="btn btn-flat btn-sm btn-primary" value="Save Contribution">
                    </div>
                  </div>
                </div>
              </form>
            </div>       
             
            <div class="modal fade" id="modal-withdr">
              <form class="form-horizontal" method="post">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header bg-red ">
                      <h4 class="modal-title"><span style="color:white"><i class="fa fa-plus"></i> Withdraw on Social Account </span>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </h4>
                    </div>
              
                    <div class="modal-body">
                      <div class="card card-info">
                        <div class="card-body">
                          <div class="form-group row">
                            <div class="col-sm-4">
                              <input type="text" class="form-control" name="amount" placeholder="Enter cash-out amount">
                            </div>
                            <div class="col-sm-6">
                              <input type="text" class="form-control" placeholder="Enter Ref" name="ref">
                            </div>
                          </div>
                          <div class="form-group row">
                            <div class="col-sm-6">
                              <select class="form-control select2" name="month"  style="width: 100%;" required="" >
                                <?php formMonth($currentmonth); ?>
                              </select>
                            </div>
                            <div class="col-sm-6">
                              <select class="form-control select2" name="year"  style="width: 100%;" required="" >
                                <?php formYear($currentyear); ?>
                              </select>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                      <input type="submit" name="socialbtn" class="btn btn-flat btn-sm btn-danger btn-primary" value="Withdraw">
                    </div>
                  </div>
                </div>
        		  </form>
            </div> 

          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
    </form>  </div>
  <!-- /.content-wrapper -->
  <!-- /.content-wrapper -->
  <?php include('footer.php'); ?>
  
<script>
$(document).ready(function() {
    $('#example').DataTable();
    $(".select2").select2();
    $("#month").select2();
    $("#year").select2();
    $("#member_id").select2({
      placeholder: "Select a Member"
    }).bind("change", function(e){
      var selected = $("#member_id option:selected");
      alert(selected.val());
    });
} );
</script>
</body>
</html>

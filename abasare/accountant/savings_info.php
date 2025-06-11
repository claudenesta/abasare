<?php include('../DBController.php');
$membe_id=$_GET['m_id'];
$year=$_GET['year'];
$sql="select * from member where id='$membe_id'";
$query=mysqli_query($con,$sql);
$rows=mysqli_fetch_array($query);
  

?>
 <?php 
 include('./header.php'); 

if(isset($_POST['saving_edit'])){

  $db->beginTransaction();
  try{

    $old_data = first($db, "SELECT * FROM saving WHERE id = ?", [$_POST['iiid']]);

    // Update Saving records
    saveData($db, "UPDATE saving SET sav_amount = ? WHERE id = ?", [$_POST['amoun'], $_POST['iiid']]);

    // Update Member Account Balance
    saveData($db, "UPDATE member SET Account_balance = Account_balance - ? + ? WHERE id = ?", [$old_data['sav_amount'], $_POST['amoun'], $old_data['member_id']]);
    $db->commit();
    ?>
    <script type="text/javascript">
      alert("New Amount is well kept")
    </script>
    <meta http-equiv="refresh" content="0; URL=savings_info.php?m_id=<?= $_GET['m_id'] ?>&year=<?= $_GET['year']?>">
    <?php
  } catch(\Exception $e){
    $db->rollBack();
    ?>
    <script type="text/javascript">
      alert("Error\n<?= $e->getMessage(); ?>")
    </script>
    <meta http-equiv="refresh" content="0; URL=savings_info.php?m_id=<?= $_GET['m_id'] ?>&year=<?= $_GET['year']?>">
    <?php
  }
}

 $active = "member-list-active";
 include('./menu.php'); ?>
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Member Details <small>View/Search </small>
      </h1>
      <ol class="breadcrumb">
          <li><a href="/admin/"><i class="fa fa-dashboard"></i>Home</a></li>
          <li><a href="member_info.php?id=<?= $_GET['m_id'] ?>">Member</a></li>
          <li><a href="#">Savings</a></li>
          <li class="active"><a href="savings_info.php?m_id=<?= $_GET['m_id'] ?>&year=<?= $_GET['year']?>">History </a></li>
      </ol>
    </section>

    <!-- Main content -->
    <form action="#" class="" id="table_form" method="post" accept-charset="utf-8">
   
    <section class="content">
      <div class="row">
        <!-- ********** ALERT MESSAGE START******* -->
          <div class="col-md-12">
      
     </div>            <!-- ********** ALERT MESSAGE END******* -->
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header ">
              <h3 class="box-title">&nbsp;</h3>
			   <a class="btn btn-warning btn-flat btn-sm" href="../dompdf/www/savings_statement.php?m_id=<?php echo $membe_id; ?>"><i class="fa fa-plus"></i> Print Historical Report</a>
                          </div>
            <!-- /.box-header -->
            <div class="box-body">
             <div class="container-fluid">
        <div class="row">
          
          <div class="col-md-12">
            <div class="card">
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="activity">
                    <!-- Post -->
                    <div class="post">
                      <div class="user-block">
                        <span class="username">
                          <a href="#">Overview </a>
                        </span>
                      </div>
					  
			              <table class="table table-bordered table-responsive">
                      <tr>
                        <td>Date</td>
                        <td>Member Name</td>
                        <td>Amount</td>
                        <td>Month</td>
                        <td>Year</td>
                        <td>Action</td>
                      </tr>
                      <tbody>
            					  <?php 
            					  $tot=0;
            					  $num=0;
            					  $month=0;
                        $savings = returnAllData($db, "SELECT a.id,
                                                              CONCAT(b.fname, ' ', b.lname) AS member_name,
                                                              a.sav_amount,
                                                              a.month,
                                                              a.year,
                                                              a.done_at
                                                              FROM saving AS a
                                                              INNER JOIN member AS b
                                                              ON a.member_id = b.id
                                                              WHERE a.member_id = ? AND
                                                              a.year = ?
                                                              ", [$_GET['m_id'], $_GET['year']]);
            					  foreach($savings AS $row) {
            						  $month=(int)$row['month'];
            						  $tot+=$row['sav_amount'];
            						  $num++;
            					    ?>
                          <tr>
                            <td><?php echo $row['done_at']; ?></td>
                            <td><?php echo $row['member_name']; ?></td>
                            <td><?php echo number_format(ceil($row['sav_amount'])); ?>  Frw (s)</td>
                            <td><?php echo $long[$month]; ?> </td>
                            <td><?php echo $row['year']; ?> </td>
                            <td>
                              <a href="#" data-toggle="modal" data-target="#modal-sav_<?php echo $row['id']; ?>">Edit</a> | 
                              <a href="delete_savings.php?id=<?php echo $row['id'];?>&mid=<?= $_GET['m_id'] ?>&year=<?= $_GET['year'] ?>" onclick="return confirm('Are you sure you want to delete this Savings?');">Delete</a>
                            </td>
                            <div class="modal fade" id="modal-sav_<?php echo $row['id']; ?>">
                              <form class="form-horizontal" method="post" action="savings_info.php?m_id=<?= $_GET['m_id'] ?>&year=<?= $_GET['year']?>">
                                <div class="modal-dialog">
                                  <div class="modal-content">
                                    <div class="modal-header bg-primary ">
                                      <h4 class="modal-title"><span style="color:white"><i class="fa fa-plus"></i> Edit Member Savings(<?php echo $rows['fname']." ".$rows['lname']; ?>)  </span>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                          <span aria-hidden="true">&times;</span>
                                        </button>
                                      </h4>
                                    </div>
                                    <div class="modal-body">
                                      <div class="card card-info">
                                        <div class="card-body">
                                          <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-6 col-form-label"></label>
                                            <div class="col-sm-2">
                                              <input type="hidden" name="iiid" readonly class="form-control" value="<?php echo $row['id']; ?>">
                                              <input type="hidden" name="me_id" readonly class="form-control" value="<?php echo $membe_id; ?>">
                                              
                                              <input type="hidden" name="me_name" readonly class="form-control" value="<?php echo $rows['fname']." ".$rows['lname']; ?>">
                                            </div>
                                            <div class="col-sm-4">
                                              <input type="text" name="savamounti" readonly class="form-control" value="<?php echo number_format($rows['Account_balance'],2); ?> FRW">
                                            </div>
                                          </div>
                                          <div class="form-group row">
                                            <label for="inputPassword3" class="col-sm-3 col-form-label"> Date: </label>
                                            <div class="col-sm-5">
                                              <select size="1" name="ukwezi" class="form-control">
                                                <?php formMonth($row['month'], $row['month']); ?>
                                              </select>
                                            </div>
                                            <div class="col-sm-4">
                                              <select size="1" name="year" class="form-control">
                                                <?php formYear($row['year'],$row['year']); ?>
                                              </select>
                                            </div>
                                          </div>
                                          <div class="form-group row">
                                            <label for="inputPassword3" class="col-sm-4 col-form-label"> Saving Amount: </label>
                                            <div class="col-sm-8">
                                                <input type="hidden" required name="exitamoun" value="<?php echo $row['sav_amount']; ?>" onkeypress="return isNumber(event)"  class="form-control">
                                             <input type="text" required name="amoun" value="<?php echo $row['sav_amount']; ?>" onkeypress="return isNumber(event)"  class="form-control">
                                            </div>
                                          </div>
                                          <!-- <div class="form-group row">
                                            <label for="inputPassword3" class="col-sm-4 col-form-label"> Account : </label>
                                            <div class="col-sm-8">
                                                <select name="bank_account" class="form-control">
                                                <?php 
                                                $sql = "SELECT * FROM `financial_account`";
                                                $annn=mysqli_query($con,$sql);
                                                while($roww=mysqli_fetch_array($annn)){
                                                ?>
                                                <option value="<?php echo $roww['id']; ?>"><?php echo $roww['Name'];?></option>
                                                <?php } ?>
                                                </select>
                                            </div>
                                          </div> -->
                                        </div>
                                      </div>
                                    </div>
                                    <div class="modal-footer justify-content-between">
                                      <input type="submit" value="Save Update" name="saving_edit" class="btn btn-flat btn-sm btn-primary">
                                    </div>
                                  </div>
                                </div>
                              </form>
                            </div>
                          </tr>
                          <?php 
                        }
                        ?>
                        <tr>
                          <td colspan="6" style="color:#008080">Total: <?php echo number_format(ceil($tot),2)." Frws :".ucwords(convertNumberToWord($tot))."Rwandan francs, in ".date('Y'); ?></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- /.nav-tabs-custom -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
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
  <?php include('./footer.php'); ?>
<!-- page script -->
<script>
$(document).ready(function() {
    $('#example').DataTable();
} );
</script>
</body>
</html>

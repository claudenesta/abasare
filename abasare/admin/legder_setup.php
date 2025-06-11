<?php include('../DBController.php');
$membe_id=$_SESSION['acc'];

?>

<?php 
include('./header.php');

if(isset($_POST['save'])){
  saveData($db, "UPDATE loan_type SET lname = ?, interest=?, terms =?, late_fee =?, is_top_up=?, is_emergeny=?, percentage_before_top_op=? WHERE id = ?", [
    $_POST['lname'], $_POST['interest'], $_POST['terms'], $_POST['fine'], $_POST['is_top_up'], $_POST['is_emergeny'], $_POST['percentage_before_top_op'], $_POST['idd']
  ]);
}
$active = 'ledger-setup';
include('./menu.php'); ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       Loan Type Settings
      </h1>
      <ol class="breadcrumb">
        <li><a href="/admin/"><i class="fa fa-dashboard"></i>Home</a></li>
        <li><a href="#">Settings</a></li>
        <li class="active"><a href="leagder_setup.php">Loan Type </a></li>
      </ol>
    </section><br/>
    <div class="col-md-12">
      <!-- ********** ALERT MESSAGE START******* -->
       <div class="col-md-12">
      <!-- ********** ALERT MESSAGE START******* -->
        
     </div>       <!-- ********** ALERT MESSAGE END******* -->
     </div>
          <div class="col-md-12">
          <div class="box box-danger">
            <div class="box-body">
              
              <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-body">
               <table id="example" class="table table-striped table-bordered" style="width:100%">
                   <thead class="bg-primary ">
               <tr>
                 <th>Name</th> 
                 <th>Interest (%)</th>
                 <th>Period</th>
                  <th>Fine(%)</th>
                  <th>Topup</th>
                  <th>Emergence</th>
                  <th>% before topup</th>
                  <th></th>
              </tr>
              </thead>
              <tbody>
            <?php
				    $no=0;
            $sql="SELECT * FROM `loan_type` ORDER BY terms ASC, lname ASC";
            $query=mysqli_query($con,$sql);
            while($row=mysqli_fetch_array($query)){
              $no++;
				      ?>
                  <tr>
                 <td><?php echo $row['lname']; ?></td> 
                 <td class="text-right"><?php echo $row['interest']; ?></td>
                 <td class="text-right"><?php echo $row['terms']; ?></td>
                 <td class="text-right"><?php echo $row['late_fee']; ?></td>
                 <td class="text-center"><?= $row['is_top_up'] == 1?"<i class='fa fa-check text-green'></i>":"<i class='fa fa-times text-red'></i>" ?></td>
                 <td class="text-center"><?= $row['is_emergeny'] == 1?"<i class='fa fa-check text-green'></i>":"<i class='fa fa-times text-red'></i>" ?></td>
                 <td class="text-center"><?= !is_null($row['percentage_before_top_op'])?($row['percentage_before_top_op']."%"):"" ?></td>
                 <td> <a href="#" data-toggle="modal" data-target="#modal-<?php echo $row['id']; ?>">Edit</a><?php /* | <a href="delete_type.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this Savings?');">Delete</a>*/ ?></td>
                 
<div class="modal fade" id="modal-<?php echo $row['id']; ?>">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header bg-primary ">
              <h4 class="modal-title"><span style="color:white"><i class="fa fa-plus"></i> Edit Loan Type:  </span></h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
             <div class="card card-info">
                <div class="card-body">
			   <form action="" method="POST" class="form-horizontal" id="category-form">
           <div class="box-body">
                    <input type="hidden" name="idd" value="<?php echo $row['id']; ?>">      
      <div class="form-group row">
            <label for="category" class="col-sm-4 control-label"> Loan Name: <label class="text-danger">*</label></label>
           <div class="col-sm-7">
		   <input type="text" name="lname" value="<?php echo $row['lname']; ?>" class="form-control input-sm"/>
              <span id="category_msg" style="display:none" class="text-danger"></span>
            </div>
      </div>
        <div class="form-group row">
          <label for="description" class="col-sm-4 control-label"> Interest Rate (%): </label>
           <div class="col-sm-7">
           <input type="text" name="interest" value="<?php echo $row['interest']; ?>" class="form-control input-sm"/>
              <span id="description_msg" style="display:none" class="text-danger"></span>
                  </div>
                </div>
		<div class="form-group row">
          <label for="description" class="col-sm-4 control-label"> Period (Month): </label>
           <div class="col-sm-7">
           <input type="text" name="terms"  value="<?php echo $row['terms']; ?>" class="form-control input-sm"/>
              <span id="description_msg" style="display:none" class="text-danger"></span>
                  </div>
                </div>
	<div class="form-group row">
          <label for="description" class="col-sm-4 control-label"> Fine Rate (%): </label>
           <div class="col-sm-7">
                        <input type="text" name="fine"  value="<?php echo $row['late_fee']; ?>" class="form-control input-sm"/>
              <span id="description_msg" style="display:none" class="text-danger"></span>
                  </div>
                </div>
                <div class="form-group row">
                  <div class="col-md-4 text-left">
                    Is Topup
                  </div>
                  <div class="col-md-4 text-left">
                    Is Emergency
                  </div>
                  <div class="col-md-4 text-left">
                    % before topup
                  </div>
                </div>
                <div class="form-group row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <div class="radio">
                        <label>
                          <input type="radio" name="is_top_up" id="is_top_up_yes" value="1" <?= $row['is_top_up'] == 1?"checked=''":"" ?>>
                          Yes
                        </label>
                        <label>
                          <input type="radio" name="is_top_up" id="is_top_up_no" value="0" <?= is_null($row['is_top_up']) ||  $row['is_top_up']== 0?"checked=''":"" ?> >
                          No
                        </label>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <div class="radio">
                        <label>
                          <input type="radio" name="is_emergeny" id="is_emergeny_yes" value="1" <?= $row['is_emergeny'] == 1?"checked=''":"" ?> >
                          Yes
                        </label>
                        <label>
                          <input type="radio" name="is_emergeny" id="is_emergeny_no" value="0" <?= is_null($row['is_emergeny']) ||  $row['is_emergeny']== 0?"checked=''":"" ?>>
                          No
                        </label>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <input type="text" name="percentage_before_top_op" id="percentage_before_top_op" value="<?= $row['percentage_before_top_op'] ?>">
                    </div>
                  </div>
                </div>
              </div>
                 <!-- /.box-footer -->
              <div class="box-footer">
                <div class="col-sm-8 col-sm-offset-3 text-center">
                   <div class="col-md-6 col-md-offset-6">
                      <button type="submit" id="save" name="save" class=" btn btn-block btn-primary" title="Save Data"> <i class="fa fa-save"></i>    Save</button>
                   </div>
                </div>
             </div>
             </form>
                </div>
            </div>
            </div>
          </div>
		  
        </div>
		 </form>
        <!-- /.modal-dialog -->
      </div>
                 
                 
                 
              </tr>  
      
              
                 <?php } ?> 
              </tbody>
              </table>
				              
            </div>
          </div>
        </div>

            </div>
          </div>
        </div>

    
    <!-- /.content -->
  </div>
 
  <!-- /.content-wrapper -->
  <?php include('./footer.php'); ?>
</body>
</html>

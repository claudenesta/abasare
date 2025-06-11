<?php 
include('../DBController.php');

include('./header.php');

if(isset($_POST['save']) && !empty($_POST['code'])){
  //Here make sure to save the updated information
  $db->beginTransaction();
  try{
    $sql = "UPDATE member SET company=?, address=?, income=?, civil_status=?, employment_status=?, job_title=?, fname=?, lname=?, birth_date=? WHERE id = ?";
    saveData($db, $sql, [
      $_POST['company'],
      $_POST['address'],
      $_POST['income'],
      $_POST['civ_status'],
      $_POST['employment_status'],
      $_POST['job_title'],
      $_POST['fname'],
      $_POST['lname'],
      $_POST['age'],
      $_POST['code'],
    ]);

    //Update user related Data
    $sql = "UPDATE users SET name=? WHERE member_acc=?";
    saveData($db, $sql, [$_POST['fname']." ".$_POST['lname'], $_POST['code']]);

    //the commit changes to database
    $db->commit();
    ?>
    <meta http-equiv="refresh" content="0; URL=list.php">
    <?php

  } catch(\Exception $e){
    $db->rollBack();
    //Here update had encountered an error
  }

}

$status = isset($_GET['stat'])?$_GET['stat']:1;
$active = "member-list-".($status?"active":"inactive");
include('./menu.php'); ?>

<!-- Content Wrapper. Contains page content -->
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Member List <small>View/Search </small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Member List</li>
    </ol>
  </section>

  <!-- Main content -->
  <form action="#" class="" id="table_form" method="post" accept-charset="utf-8">

    <section class="content">
      <div class="row">
        <!-- ********** ALERT MESSAGE START******* -->
        <div class="col-md-12">

        </div> <!-- ********** ALERT MESSAGE END******* -->
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header ">
              <h3 class="box-title">&nbsp;</h3>
              <div class="box-tools">
                <a class="btn btn-block btn-info" href="add_member.php">
                  <i class="fa fa-plus"></i> New Member</a>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example" class="table table-striped table-bordered" style="width:100%">
                <thead class="bg-primary ">
                  <tr>
                    <th>No</th>
                    <th>Names</th>
                    <th>Job Title</th>
                    <th>Civil status</th>
                    <th>Phone / Cell</th>
                    <th>Join Date</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $no = 0;
                  $sql = "SELECT * FROM member where status=?";
                  
                  $all_info = returnAllData($db, $sql, [$status]);
                  foreach($all_info AS $row){
                    $no++;
                    ?>
                    <tr>
                      <td>
                        <?php
                        if($row['status'] == 1){
                          ?>
                          <a href="./member_info.php?id=<?php echo $row['id']; ?>"><?php echo $no; ?></a>
                          <?php
                        } else {
                          echo $no;
                        }
                        ?>
                      </td>
                      <td>
                        <?php
                        if($row['status'] == 1){
                          ?>
                          <a href="./member_info.php?id=<?php echo $row['id']; ?>"><?php echo $row['fname'] . " " . $row['lname']; ?></a>
                          <?php
                        } else {
                          echo $row['fname'] . " " . $row['lname'];
                        }
                        ?>
                      </td>
                      <td><?php echo $row['job_title']; ?></td>
                      <td><?php echo $row['civil_status']; ?></td>
                      <td><?php echo $row['phone_cell']; ?></td>
                      <td><?php echo $row['rdate']; ?></td>
                      <td class="text-center">
                        <?php 
                        if ($_SESSION['role'] == 1) {
                          if($row['status'] == 1){
                            ?>
                            <a href="#" data-toggle="modal" data-target="#modal-<?php echo $row['id']; ?>"> Edit </a>
                            <!-- | <a href="delete_member.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this Member?');">Delete</a> -->
                            <?php 
                          } else {
                            ?>
                            <a href="#" data-toggle="" data-target="">Re-Enable</a>
                            <?php
                          }
                        } 
                        ?>
                      </td>
                    </tr>
                    <div class="modal fade" id="modal-<?php echo $row['id']; ?>">
                      <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                          <div class="modal-header bg-primary ">
                            <h4 class="modal-title"><span style="color:white">
                              <i class="fa fa-plus"></i> MEMBER EDIT</span>

                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                              </button>
                            </h4>
                          </div>
                          <div class="modal-body">
                            <div class="card card-info">
                              <div class="card-body">

                                <form action="" class="form" id="items-form" enctype="multipart/form-data" method="POST" accept-charset="utf-8">
                                  <input type="hidden" name="code" value="<?php echo $row['id']; ?>" class="form-control" />

                                  <div class="box-body">
                                    <div class="row">
                                      <div class="form-group col-md-4">
                                        <label for="item_name">First Name<span class="text-danger">*</span></label>
                                        <input type="text" name="fname" class="form-control" value="<?php echo $row['fname']; ?>" />
                                        <span id="item_name_msg" style="display:none" class="text-danger"></span>
                                      </div>
                                      <div class="form-group col-md-4">
                                        <label for="category_id">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" name="lname" class="form-control" value="<?php echo $row['lname']; ?>" />
                                        <span id="category_id_msg" style="display:none" class="text-danger"></span>
                                      </div>

                                      <div class="form-group col-md-4">
                                        <label for="sku">DOB </label>
                                        <input type="date" name="age" value="<?php echo $row['birth_date']; ?>" class="form-control" />
                                        <span id="sku_msg" style="display:none" class="text-danger"></span>
                                      </div>
                                      <div class="form-group col-md-4">
                                        <label for="alert_qty">Civil Status <span class="text-danger">*</span></label>
                                        <select class="form-control select2" id="item_type" name="civ_status" style="width: 100%;" required="">
                                          <option <?= $row['civil_status'] == "Single"?"selected":"" ?>>Single</option>
                                          <option <?= $row['civil_status'] == "Married"?"selected":"" ?>>Married</option>
                                          <option <?= $row['civil_status'] == "Widow"?"selected":"" ?>>Widow</option>
                                          <option <?= $row['civil_status'] == "Divorced"?"selected":"" ?>>Divorced</option>
                                        </select>
                                        <span id="alert_qty_msg" style="display:none" class="text-danger">*</span>
                                      </div>

                                    </div>
                                    <fieldset>
                                      <legend>Contact Info :</legend>
                                      <div class="row">
                                        <div class="form-group col-md-4">
                                          <label for="price">Address<span class="text-danger">*</span></label>
                                          <textarea rows="1" cols="50" name="address" class="form-control"><?php echo $row['address']; ?></textarea>
                                          <span id="price_msg" style="display:none" class="text-danger"></span>
                                        </div>
                                        <div class="form-group col-md-4">
                                          <label for="tax_id">Phone / Cellphone<span class="text-danger">*</span></label>
                                          <input type="text" readonly name="phone_cell" class="form-control" value="<?php echo $row['phone_cell']; ?>" />
                                          <span id="tax_id_msg" style="display:none" class="text-danger"></span>
                                        </div>
                                        <div class="form-group col-md-4">
                                          <label for="purchase_price">Email<span class="text-danger">*</span></label>
                                          <input type="text" readonly name="email" value="<?php echo $row['email']; ?>" class="form-control" />
                                          <span id="purchase_price_msg" style="display:none" class="text-danger"></span>
                                        </div>
                                      </div>
                                    </fieldset>
                                    <fieldset>
                                      <legend>Current Employment Info :</legend>
                                      <!-- /row -->
                                      <div class="row">
                                        <div class="form-group col-md-4">
                                          <label for="tax_type">Employment Status <span class="text-danger">*</span></label>
                                          <select class="form-control select2" name="employment_status" style="width: 100%;" required="">
                                            <option><?php echo $row['employment_status']; ?></option>
                                            <option> Employeed </option>
                                            <option> Unemployeed </option>
                                          </select>
                                          <span id="tax_type_msg" style="display:none" class="text-danger"></span>

                                        </div>
                                        <div class="form-group col-md-4">
                                          <label for="profit_margin">Company <span class="text-danger">*</span></label>
                                          <input type="text" class="form-control" name="company" value="<?php echo $row['company']; ?>" />
                                          <span id="profit_margin_msg" style="display:none" class="text-danger"></span>
                                        </div>
                                        <div class="form-group col-md-4">
                                          <label for="sales_price" class="control-label">Job Title <span class="text-danger">*</span></label>
                                          <input type="text" name="job_title" value="<?php echo $row['job_title']; ?>" class="form-control" />
                                          <span id="sales_price_msg" style="display:none" class="text-danger"></span>
                                        </div>

                                    </fieldset>


                                    <!-- /.box-body -->
                                    <div class="box-footer">
                                      <div class="col-sm-12 col-sm-offset-2 text-center">
                                        <!-- <div class="col-sm-4"></div> -->
                                        <div class="col-md-6 col-md-offset-3">
                                          <button type="submit" id="save" name="save" class=" btn btn-block btn-primary" title="Save Data"><i class="fa fa-upload"></i> Save</button>
                                        </div>

                                      </div>
                                    </div>
                                    <!-- /.box-footer -->
                                </form>
                              </div>
                              <!-- /.box -->


                            </div>
                          </div>
                        </div>
                      </div>

                    </div>
                      </div>
                    <?php 
                  } 
                  ?>
          </tbody>

          </table>
          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
      <!-- /.col -->
</div>
<!-- /.row -->
</section>
<!-- /.content -->
</form>
</div>
<!-- /.content-wrapper -->
<!-- /.content-wrapper -->
<?php include('./footer.php'); ?>
<!-- page script -->
<script>
  $(document).ready(function() {
    $('#example').DataTable();
  });
</script>
</body>

</html>
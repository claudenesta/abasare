<?php
include('../DBController.php');
function phpAlert($msg)
{
  echo '<script type="text/javascript">alert("' . $msg . '")</script>';
}
?>

    <?php include('./header.php'); ?>
    <!-- Left side column. contains the logo and sidebar -->

    <?php 
    $active = "new-member";
    include('./menu.php'); ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          Membership</h1>
        <ol class="breadcrumb">
          <li><a href="/admin/"><i class="fa fa-dashboard"></i>Home</a></li>
          <li><a href="#">Members</a></li>
          <li class="active">New Member</li>
        </ol>
      </section>
      <!-- Main content -->
      <section class="content">
        <div class="row">
          <!-- ********** ALERT MESSAGE START******* -->
          <div class="col-md-12">
          </div> <!-- ********** ALERT MESSAGE END******* -->
          <!-- right column -->
          <div class="col-md-12">
            <!-- Horizontal Form -->
            <div class="box box-info ">

              <form action="./add_member.php" class="form" id="items-form" enctype="multipart/form-data" method="POST" accept-charset="utf-8">
                <input type="hidden" name="code" value="<?php echo $pcode ?>" class="form-control" />

                <div class="box-body">
                  <div class="row">
                    <div class="form-group col-md-4">
                      <label for="item_name">First Name<span class="text-danger">*</span></label>
                      <input type="text" name="fname" class="form-control input-sm" value="<?= @$_POST['fname']?>" />
                      <span id="item_name_msg" style="display:none" class="text-danger"></span>
                    </div>
                    <div class="form-group col-md-4">
                      <label for="category_id">Last Name <span class="text-danger">*</span></label>
                      <input type="text" name="lname" class="form-control input-sm" value="<?= @$_POST['lname']?>" />
                      <span id="category_id_msg" style="display:none" class="text-danger"></span>
                    </div>

                    <div class="form-group col-md-4">
                      <label for=" sku">DOB </label>
                      <input type="text" name="age" value="<?= @$_POST['age']?>" class="form-control input-sm" id="datepicker" />
                      <span id="sku_msg" style="display:none" class="text-danger"></span>
                    </div>
                    <div class="form-group col-md-4">
                      <label for="alert_qty">Civil Status <span class="text-danger">*</span></label>
                      <select class="form-control select2" id="item_type" name="civ_status" style="width: 100%;" required="">
                        <option <?= @$_POST['civ_status'] == "Single"?"selected":"" ?>>Single</option>
                        <option <?= @$_POST['civ_status'] == "Married"?"selected":"" ?>>Married</option>
                        <option <?= @$_POST['civ_status'] == "Widow"?"selected":"" ?>>Widow</option>
                        <option <?= @$_POST['civ_status'] == "Divorced"?"selected":"" ?>>Divorced</option>

                      </select>
                      <span id="alert_qty_msg" style="display:none" class="text-danger">*</span>
                    </div>

                    <div class="form-group col-md-4">
                      <label for="unit_id" class="control-label">New member <span class="text-danger">*</span></label>
                      <p>
                        Paid membership fees(<?= number_format(20000) ?> Frw)
                        <input type="checkbox" name="isnew" <?= @$_POST['isnew']?"checked":"" ?> value="1" id="isnew" class="checkbox input-sm" />
                        <span id="unit_id_msg" style="display:none" class="text-danger"></span>
                      </p>
                    </div>

                    <div class="form-group col-md-4">
                      <label for="saving_amount" class="control-label">Savings for <?= $currentyear ?>-<?= $currentmonth < 10?"0":"" ?><?= $currentmonth ?> <span class="text-danger">*</span></label>
                      <input type="text" name="saving_amount" id="saving_amount" class="form-control input-sm" value="<?= @$_POST['saving_amount']?>" required />
                    </div>

                    <div class="form-group col-md-4">
                      <label for="share_capital_amount" class="control-label">Share Capital for <?= $currentyear ?>-<?= $currentmonth < 10?"0":"" ?><?= $currentmonth ?> <span class="text-danger">*</span></label>
                      <input type="text" name="share_capital_amount" id="share_capital_amount" class="form-control input-sm" value="<?= @$_POST['share_capital_amount']?>" required />
                    </div>

                  </div>
                  <fieldset>
                    <legend>Contact Info :</legend>
                    <div class="row">
                      <div class="form-group col-md-4">
                        <label for="price">Address<span class="text-danger">*</span></label>
                        <textarea rows="1" cols="50" name="address" class="form-control input-sm"><?= @$_POST['address']?></textarea>
                        <span id="price_msg" style="display:none" class="text-danger"></span>
                      </div>
                      <div class="form-group col-md-4">
                        <label for="tax_id">Phone / Cellphone<span class="text-danger">*</span></label>
                        <input type="text" name="phone_cell" class="form-control input-sm" value="<?= @$_POST['phone_cell']?>" />
                        <span id="tax_id_msg" style="display:none" class="text-danger"></span>
                      </div>
                      <div class="form-group col-md-4">
                        <label for="purchase_price">Email<span class="text-danger">*</span></label>
                        <input type="text" name="email" value="<?= @$_POST['email']?>" class="form-control input-sm" />
                        <span id="purchase_price_msg" style="display:none" class="text-danger"></span>
                      </div>
                    </div>
                  </fieldset>
                  <fieldset>
                    <legend>Current Employment Info :</legend>
                    <!-- /row -->
                    <div class="row">
                      <div class="form-group col-md-3">
                        <label for="tax_type">Employment Status <span class="text-danger">*</span></label>
                        <select class="form-control select2" name="employment_status" style="width: 100%;" required="">
                          <option <?= @$_POST['employment_status'] == "Employeed"?"selected":"" ?>>Employeed</option>
                          <option <?= @$_POST['employment_status'] == "Unemployeed"?"selected":"" ?>>Unemployeed</option>
                        </select>
                        <span id="tax_type_msg" style="display:none" class="text-danger"></span>

                      </div>
                      <div class="form-group col-md-3">
                        <label for="profit_margin">Company <span class="text-danger">*</span></label>
                        <input type="text" class="form-control input-sm" name="company" value="<?= @$_POST['company']?>" />
                        <span id="profit_margin_msg" style="display:none" class="text-danger"></span>
                      </div>
                      <div class="form-group col-md-4">
                        <label for="sales_price" class="control-label">Job Title <span class="text-danger">*</span></label>
                        <input type="text" name="job_title" value="<?= @$_POST['job_title']?>" class="form-control input-sm" />
                        <span id="sales_price_msg" style="display:none" class="text-danger"></span>
                      </div>

                      <div class="form-group col-md-2">
                        <label for="opening_stock">Monthly Income</label>
                        <input type="text" name="income" value="<?= @$_POST['income']?>" class="form-control input-sm" />
                        <span id="opening_stock_msg" style="display:none" class="text-danger"></span>
                        <div>
                        </div>
                  </fieldset>
                  <!-- /.box-body -->
                  <div class="box-footer">
                    <div class="col-sm-8 col-sm-offset-2 text-center">
                      <!-- <div class="col-sm-4"></div> -->
                      <div class="col-md-3 col-md-offset-3">
                        <button type="submit" id="save" name="save" class=" btn btn-sm btn-block btn-primary" title="Save Data"><i class="fa fa-upload"></i> Save</button>
                      </div>
                      <div class="col-sm-3">
                        <a href="./">
                          <button type="button" class="btn bg-gray btn-block btn-sm btn-flat" title="Go Dashboard"><i class="fa fa-close"></i> Close</button>
                        </a>
                      </div>
                    </div>
                  </div>
                  <!-- /.box-footer -->
              </form>
            </div>
            <!-- /.box -->
          </div>
          <!--/.col (right) -->
        </div>
        <!-- /.row -->
      </section>
      <!-- /.content -->
    </div>

    <?php
    if (isset($_POST["save"])) {

      //Here Make sure to save all required information without any exception
      $db->beginTransaction();
      try{
        //Here Create the record in members table
        $sql = "INSERT INTO member SET company = ?, address = ?, phone_cell=?, email=?, income=?, civil_status=?, employment_status=?, job_title=?, fname=?, lname=?, rdate=?, birth_date=?, Account_balance=?, is_new=?, member_fee=?, status=?";

        $member_id = saveAndReturnID($db, $sql, [
          $_POST['company'],
          $_POST['address'],
          $_POST['phone_cell'],
          $_POST['email'],
          $_POST['income'],
          $_POST['civ_status'],
          $_POST['employment_status'],
          $_POST['job_title'],
          $_POST['fname'],
          $_POST['lname'],
          (new \DateTime())->format('Y-m-d'),
          $_POST['age'],
          $_POST['saving_amount'],
          $_POST['isnew'],
          20000,
          1
        ]);

        //Next Make sure to create the user as he might need to loggin to the system
        $sql = "INSERT INTO users SET username=?, phone=?, email=?, password=?, comfirm=?, name=?, Position=?, staff_id=?, member_acc=?, status=?";
        $user_id = saveAndReturnID($db, $sql, [
          $_POST['email'],
          $_POST['phone_cell'],
          $_POST['email'],
          hash('sha256', $_POST['phone_cell']),
          hash('sha256', $_POST['phone_cell']),
          $_POST['fname']." ".$_POST['lname'],
          5,
          $_SESSION['id'],
          $member_id,
          1
        ]);

        //Now record interest 
        if($_POST['isnew']){
          $sql = "INSERT INTO interest SET member_id=?, amount=?, membership_fee=?, ref_id=?, desciption=?, month=?, year=?";
          saveData($db, $sql, [
            $member_id,
            20000,
            20000,
            $member_id,
            "New Membership fees",
            $currentmonth,
            $currentyear
          ]);
        }

        //Create the Saving record
        $sql = "INSERT INTO saving SET member_id=?, sav_amount=?, month=?, year=?, fine=0";
        saveData($db, $sql, [
          $member_id,
          $_POST['saving_amount'], 
          $currentmonth,
          $currentyear
        ]);

        //Create share capita record to
        $sql = "INSERT INTO capital_share SET staff_id=?, member_id=?, amount=?, month=?, year=?";
        saveData($db, $sql, [
          $_SESSION['id'],
          $member_id,
          $_POST['share_capital_amount'],
          $currentmonth,
          $currentyear
        ]);
        $db->commit();
        phpAlert("Member added successfull !!!! ");
        ?>
        <meta http-equiv="refresh" content="0; URL=list.php">
        <?php
        return;
      } catch(\Exception $e){
        $db->rollBack();
        phpAlert($e->getMessage());
      }
    }
    ?>
    <!-- /.content-wrapper -->
    <?php include('./footer.php'); ?>

  <!-- page script -->
  <script type="text/javascript">

    $(function() {
      enable_cb5();
      $("#isnew").click(enable_cb5);
    });

    function enable_cb5() {
      if (this.checked) {
        $("input.isnew").removeAttr("readonly");
      } else {
        $("input.isnew").attr("readonly", true);
      }
    }
  </script>
  <script>
    $(document).ready(function() {
      $('#example').DataTable();
    });
  </script>
  <!-- Page script -->

  <!-- Select2 -->
  <script>
    $(function() {
      //Initialize Select2 Elements
      $('.select2').select2()

      //Datemask dd/mm/yyyy
      $('#datemask').inputmask('dd/mm/yyyy', {
        'placeholder': 'dd/mm/yyyy'
      })
      //Datemask2 mm/dd/yyyy
      $('#datemask2').inputmask('mm/dd/yyyy', {
        'placeholder': 'mm/dd/yyyy'
      })
      //Money Euro
      $('[data-mask]').inputmask()

      //Date range picker
      $('#reservation').daterangepicker()
      //Date range picker with time picker
      $('#reservationtime').daterangepicker({
        timePicker: true,
        timePickerIncrement: 30,
        locale: {
          format: 'MM/DD/YYYY hh:mm A'
        }
      })
      //Date range as a button
      $('#daterange-btn').daterangepicker({
          ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
          },
          startDate: moment().subtract(29, 'days'),
          endDate: moment()
        },
        function(start, end) {
          $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'))
        }
      )

      //Date picker
      $('#datepicker').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true
      })

      //iCheck for checkbox and radio inputs
      $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
        checkboxClass: 'icheckbox_minimal-blue',
        radioClass: 'iradio_minimal-blue'
      })
      //Red color scheme for iCheck
      $('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({
        checkboxClass: 'icheckbox_minimal-red',
        radioClass: 'iradio_minimal-red'
      })
      //Flat red color scheme for iCheck
      $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
        checkboxClass: 'icheckbox_flat-green',
        radioClass: 'iradio_flat-green'
      })

      //Colorpicker
      $('.my-colorpicker1').colorpicker()
      //color picker with addon
      $('.my-colorpicker2').colorpicker()

      //Timepicker
      $('.timepicker').timepicker({
        showInputs: false
      })
    })


    // spinners


    $(document).on('click', '.number-spinner button', function() {
      var btn = $(this),
        oldValue = btn.closest('.number-spinner').find('input').val().trim(),
        newVal = 0;

      if (btn.attr('data-dir') == 'up') {
        newVal = parseInt(oldValue) + 1;
      } else {
        if (oldValue > 1) {
          newVal = parseInt(oldValue) - 1;
        } else {
          newVal = 1;
        }
      }
      btn.closest('.number-spinner').find('input').val(newVal);
    });

    $(function() {
      $('input').iCheck({
        checkboxClass: 'icheckbox_square-orange',
        /*uncheckedClass: 'bg-white',*/
        radioClass: 'iradio_square-orange',
        increaseArea: '10%' // optional
      });
    });
  </script>
  <!-- CSRF Token Protection -->
  <script type="text/javascript">
    $(function($) { // this script needs to be loaded on every page where an ajax POST may happen
      $.ajaxSetup({
        data: {
          'csrf_test_name': '9df202e6ee4c75a2f31f477f867ed5e1'
        }
      });
    });
  </script>
  <!-- Initialize Select2 Elements -->
  <script type="text/javascript">
    $(".select2").select2();
  </script>
  <!-- Initialize date with its Format -->
  <script type="text/javascript">
    //Date picker
    $('.datepicker').datepicker({
      autoclose: true,
      format: 'dd-mm-yyyy',
      todayHighlight: true
    });
  </script>
  <script type="text/javascript">
    $(document).ajaxStart(function() {
      Pace.restart();
    });
  </script>
  <!-- Initialize toggler -->
  <script type="text/javascript">
    $(document).ready(function() {
      $('[data-toggle="popover"]').popover();
    });
  </script>
  <script type="text/javascript">
    $(document).ready(function() {
      setTimeout(function() {
        $(".alert-dismissable").fadeOut(1000, function() {});
      }, 10000);
    });
  </script>
  <!-- Make sidebar menu hughlighter/selector -->
  <script>
    $(".items-active-li").addClass("active");
  </script>
</body>

</html>
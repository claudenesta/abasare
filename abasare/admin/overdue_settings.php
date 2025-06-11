<?php 
include('../DBController.php');
$membe_id = $_SESSION['acc'];
?>
<?php include('./header.php'); 

if(@$_POST['setting_save']){
    $sql_query = "INSERT INTO overdue_settings SET month=?, year=?, saving_overdue=?, payment_overdue=?";

    saveData($db, $sql_query, [$_POST['ukwezi'], $_POST['year'], $_POST['saving_overdue'], $_POST['payment_overdue']]);
}
$active = "dashboard";
include('./menu.php'); ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Settings<small>Over Due Seetings</small>
        </h1>
        <ol class="breadcrumb">
          <li><a href="/admin/"><i class="fa fa-dashboard"></i>Home</a></li>
          <li><a href="#">Settings</a></li>
          <li class="active"><a href="overdue_settings.php?">Overdue </a></li>
        </ol>
    </section><br />
    <div class="col-md-12">
        <!-- ********** ALERT MESSAGE START******* -->
        <div class="col-md-12">
            <!-- ********** ALERT MESSAGE START******* -->

        </div> <!-- ********** ALERT MESSAGE END******* -->
    </div>
    <?php
    if ($_SESSION['role'] == 1) {
        ?>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">
                                Overdue Settings
                                <a href="#" id="new_setting_btn" data-toggle="modal"  data-target="#modal-settings-overdue" class="btn btn-success btn-flat btn-sm"> <i class="fa fa-plus"></i> New</a>
                            </h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body ">
                            <table class="table table-bordered table-responsive">
                                <tr>
                                    <td>Year</td>
                                    <td>Month</td>
                                    <td>Saving Overdue</td>
                                    <td>Loan Payment Over</td>
                                </tr>
                                <tbody>
                                    <?php
                                    $now = new \DateTime();
                                    $sql1 = "SELECT *
                                                    FROM overdue_settings AS a
                                                    ORDER BY a.year DESC, a.month DESC
                                                ";
                                    $data = returnAllData($db, $sql1);
                                    if(count($data) > 0){
                                        foreach($data AS $setting){
                                            ?>
                                            <tr>
                                                <td><?= $setting['year'] ?></td>
                                                <td><?= $setting['month'] ?></td>
                                                <td><?= $setting['saving_overdue'] ?></td>
                                                <td><?= $setting['payment_overdue'] ?></td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.box-body -->

                    </div>
                    <!-- /.box -->
                </div>
            </div>

        </section>
        <?PHP 
    } 
    ?>


    <!-- /.content -->
</div>

<div class="modal fade" id="modal-settings-overdue">
    <form class="form-horizontal" method="post" action="./overdue_settings.php">
        <div class="modal-dialog modal-sm">
          <div class="modal-content">
            <div class="modal-header bg-green ">
              <h4 class="modal-title">
                <span style="color:white"><i class="fa fa-plus"></i> Overdue Settings  </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </h4>
            </div>
            <div class="modal-body">
             <div class="card card-info">
                <div class="card-body">
                    <div class="form-group row">
                        <label for="inputPassword3" class="col-sm-12 col-form-label"> Month: </label>
                        <div class="col-sm-6">
                          <select size="1" name="ukwezi" class="form-control">
                            <?php formMonth($currentmonth); ?>
                          </select>
                        </div>
                        <div class="col-sm-6">
                          <select size="1" name="year" class="form-control">
                            <?php formYear($currentyear); ?>
                          </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputPassword3" class="col-sm-12 col-form-label">Savings Overdue :</label>
                        <div class="col-sm-12">
                            <input type="date" name="saving_overdue" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputPassword3" class="col-sm-12 col-form-label">Loan Payment Overdue :</label>
                        <div class="col-sm-12">
                            <input type="date" name="payment_overdue" class="form-control" required>
                        </div>
                    </div>
                </div>
              </div>
            </div>
            <div class="modal-footer justify-content-between">
              <input type="submit" value="Save" name="setting_save" class="btn btn-sm btn-flat btn-success">
            </div>
          </div>
        </div>
    </form>
</div>

<!-- /.content-wrapper -->
<?php include('./footer.php'); ?>

<script>
    // $(".pay_due_all").DataTable();

</script>
</body>

</html>
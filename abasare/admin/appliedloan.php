<?php include('../DBController.php');
$membe_id = $_SESSION['acc'];
// var_dump($_POST, $_SESSION); die();
// if(count($_POST)){
//   var_dump($_POST); die();
// }
if (isset($_POST['botton_app'])) {
  // var_dump($_POST, $_SESSION); die();
  // die("WELCOME!");
  $loanid = $_POST['loan_id'];
  $interest = $_POST['interest'];
  if ($_SESSION['role'] == 6) {
    mysqli_begin_transaction($con);

    try {

      $sql = "UPDATE `member_loans` SET `accountant` = '1' WHERE `member_loans`.`id` ='$loanid'";
      if (mysqli_query($con, $sql)) {
        $month = (new DateTime($_POST['date']))->format('n');
        $year = (new DateTime($_POST['date']))->format('Y');
        $sql2 = "INSERT INTO `interest` (`id`, `loan_id`, `loan_interest`, `fine_overdue`, `saving_overdu`, `membership_fee`, `ref_id`, `desciption`, `month`, `year`, `done_at`, `loan_ref`) 
          VALUES (NULL, '$loanid', '$interest', '0', '0', '0', '$loanid', 'Loan Interest $loanid', '$month', '$year', CURRENT_TIMESTAMP, '$loanid')";
        mysqli_query($con, $sql2);
        // echo $sql2; die();
        mysqli_commit($con);
      }
    } catch (\Exception $e) {
      mysqli_rollback($con);
      throw new Exception($e->getMessage());
    }
    // die("END!");
  } elseif (in_array($_SESSION['role'], [3, 4, 1])) {
    $sql = "UPDATE `member_loans` SET `president` = '1' WHERE `member_loans`.`id` = '$loanid'";
    mysqli_query($con, $sql);
  }
?>
  <meta http-equiv="Refresh" content="0; url=appliedloan.php">
<?php } ?>

<?php include('header.php'); ?>
<!-- Left side column. contains the logo and sidebar -->

<?php include('menu.php'); ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Dashboard <small>Loan Aproval </small>
    </h1>
    <ol class="breadcrumb">
      <li class="active"><i class="fa fa-dashboard"></i> Home</li>
    </ol>
  </section><br />
  <div class="col-md-12">
    <!-- ********** ALERT MESSAGE START******* -->
    <div class="col-md-12">
      <!-- ********** ALERT MESSAGE START******* -->

    </div> <!-- ********** ALERT MESSAGE END******* -->
  </div>

  <section class="content">
    <div class="row">
      <!-- ********** ALERT MESSAGE START******* -->
      <div class="col-md-12">

      </div> <!-- ********** ALERT MESSAGE END******* -->
      <div class="col-xs-12">
        <div class="box">
          <div class="box-header ">
            <h3 class="box-title">&nbsp;</h3>

          </div>
          <div class="box-body">
            <div class="container-fluid">
              <div class="row">
                <table id="example" class="table table-striped table-bordered" style="width:100%">
                  <thead class="bg-primary ">
                    <tr>
                      <td>Sl.No</td>
                      <td>Time </td>
                      <td>Member Name</td>
                      <td>Loan Type</td>
                      <td>Amount</td>
                      <td>Date</td>
                      <td>Status</td>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $id = 0;
                    if ($role == 3 || $role == 4 || $role == 1) {
                      $sql = "SELECT *, 
                                    ml.id as ididi, 
                                    CONCAT(lt.lname,' - ',lt.interest,'%') as name, 
                                    CONCAT(m.fname,' ',m.lname) as mname, 
                                    ml.rdate as dates, 
                                    ml.loan_date as datst, 
                                    lt.lname as lnty
                                    FROM `member_loans` ml 
                                    INNER JOIN loan_type lt 
                                    ON lt.id=ml.loan_id 
                                    INNER JOIN member m 
                                    ON m.id=ml.member_id AND (ml.president=0 OR ml.accountant=0) AND ml.reject=0 
                                    ORDER BY ml.president ASC
                                    ";
                    } elseif ($role == 6) {
                      $sql = "SELECT  *, 
                                      ml.id as ididi, 
                                      CONCAT(lt.lname,' - ',lt.interest,'%') as name, 
                                      CONCAT(m.fname,' ',m.lname) as mname, 
                                      ml.rdate as dates , 
                                      ml.loan_date as datst, 
                                      lt.lname as lnty
					                            FROM `member_loans` ml 
                                      INNER JOIN loan_type lt 
                                      ON lt.id=ml.loan_id 
                                      INNER JOIN member m 
                                      ON m.id=ml.member_id AND ml.president=1 AND ml.reject=0 
                                      ORDER BY ml.accountant ASC
                                      ";
                    }
                    $quer = mysqli_query($con, $sql);
                    while ($row = mysqli_fetch_array($quer)) {

                      $id++;
                      $membe_id = $row['ididi'];
                    ?>
                      <tr>
                        <td><?php echo $id; ?></a></td>
                        <td><?php
                            $timestamp = $row['dates'];
                            echo timeAgo($timestamp);
                            ?></td>
                        <td><?php echo $row['mname']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo number_format(ceil($row['loan_amount']), 2); ?> Frw (s)</td>
                        <td><?php echo $row['datst']; ?></td>
                        <td>
                          <?php if ($row['accountant'] == 1 && $row['president'] == 1) {
                            if ($row['lnty'] == "Emmergancy Loan") { ?>
                              <a class="btn btn-primary btn-flat btn-sm" href="../dompdf/www/emergence_repot.php?loaid=<?php echo $membe_id; ?>"><i class="fa fa-eye"></i> Download Contract</a>
                            <?php } else { ?>
                              <a class="btn btn-success btn-flat btn-sm" href="../dompdf/www/Contract.php?loaid=<?php echo $membe_id; ?>"><i class="fa fa-eye"></i> Download Contract </a>
                            <?php
                            }
                          } else {
                            //Check if the President had Accepted approved
                            if($row['president'] == 0){
                              ?>
                              <span title="Waitting for President to approve" class="text-red">Pending</span>
                              <?php
                            } else if($row['accountant'] == 0){
                              ?>
                              <span title="Waitting for Accountant to approve" class="text-yellow">Pending</span>
                              <?php
                            }
                          } ?>
                      </tr>
                      <div class="modal fade" id="modal_<?php echo $row['ididi']; ?>">
                        <form class="form-horizontal approve_form" method="post">
                          <div class="modal-dialog">
                            <div class="modal-content">
                              <div class="modal-header bg-primary ">
                                <h4 class="modal-title"><span style="color:white">Member Loan Approve</span></h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                              <div class="modal-body">
                                <div class="card card-info">
                                  <div class="card-body">
                                    <p><?php echo $row['mname']; ?>( <?php echo $row['name']; ?> )</p>
                                    <div class="form-group row">
                                      <label for="inputPassword3" class="col-sm-4 col-form-label"> Loan Size :</label>
                                      <div class="col-sm-4">
                                        <input type="text" name="totalinterest" value="<?php echo number_format(ceil($row['loan_amount']), 2); ?>" readonly size="12" class="form-control">
                                        <input type="hidden" name="interest" value="<?php echo $row['loan_amount_interest']; ?>" readonly size="12" class="form-control">
                                        <input type="hidden" name="date" value="<?= $row['loan_date'] ?>" readonly />

                                      </div>
                                      <div class="col-sm-2">
                                        <input type="text" style="background-color:green" readonly size="12" class="form-control">
                                        <input type="hidden" name="loan_id" value='<?php echo $membe_id; ?>' class="form-control">
                                      </div>
                                      <div class="col-sm-2">
                                        <input type="text" style="background-color:red" readonly size="12" class="form-control">
                                      </div>
                                    </div>
                                    <div class="form-group row">
                                      <label for="inputEmail3" class="col-sm-8 col-form-label"></label>
                                      <div class="col-sm-4">
                                        <input type="hidden" name="botton_app" value="Approve" />
                                        <input type="submit" value="Approve" class="form-control btn-primary botton_app">

                                        <!-- <button type="submit" name="botton_app" value="Approve" id="botton_app" value="Approve" class="form-control btn-primary" >Approve</button> -->
                                      </div>
                                    </div>

                                  </div>
                                </div>
                              </div>
                            </div>
                        </form>
                      </div>
              </div>
            <?php
                    }
            ?>
            </tbody>
            </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- /.content -->
</div>

<div class="modal fade" id="modal-approve">
  <form class="form-horizontal" method="post">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary ">
          <h4 class="modal-title"><span style="color:white"> MOVE LOAN PAYMENT </span></h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="card card-info">
            <div class="card-body">
              <div class="form-group row">
                <label for="inputEmail3" class="col-sm-8 col-form-label"></label>
                <div class="col-sm-4">
                  <input type="submit" name="botton_move" class="form-control btn-primary">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
  <!-- /.modal-dialog -->
</div>


<!-- /.content-wrapper -->
<?php include('./footer.php'); ?>

<script>
  $(document).ready(function() {
    $('#example').DataTable();

    $(".approve_form").submit(function(e) {
      // $("#botton_app").
      $(".botton_app").val('Saving...');
      $(".botton_app").attr("disabled", "disabled");
      // e.preventDefault();
    });
  });
</script>
</body>

</html>
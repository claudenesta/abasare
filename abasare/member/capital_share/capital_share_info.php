<?php
// We'll keep error reporting on for now.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Use require_once for critical files
require_once('../DBController.php');


//require_once('../../config.php');
// --- THE MAIN FIX: Get the Member ID from the SESSION, not the URL ---
// Check if the user is logged in. If not, redirect them to a login page.
if (!isset($_SESSION['acc']) || empty($_SESSION['acc'])) {
    // You should replace 'login.php' with your actual login page URL
    header("Location: /login.php");
    exit();
}
// We now have the logged-in member's ID securely from the session.
$membe_id = (int)$_SESSION['acc'];


// --- Securely process the edit form submission ---
if (isset($_POST['saving_edit'])) {
    $cid = (int)$_POST['iiid'];
    $amount = str_replace(',', '', $_POST['capitaamounti']);

    // Use prepared statements to prevent SQL injection
    $stmt_update = mysqli_prepare($con, "UPDATE `capital_share` SET `amount` = ? WHERE `capital_share`.`id` = ?");
    mysqli_stmt_bind_param($stmt_update, "di", $amount, $cid);
    mysqli_stmt_execute($stmt_update);

    // Redirect back to this same page (no need to pass the ID in the URL anymore)
    header("Location: capital_share_info.php");
    exit();
}

// Include header and menu
include('header.php');
$active = "capital_share";
include('menu.php');
?>

<div class="wrapper">
  <div class="content-wrapper">
    <section class="content-header">
      <h1>My Capital Shares Statement</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Capital Share</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <!-- The Print button can use the ID from the session variable -->
              <a class="btn btn-warning btn-flat btn-sm" href="../dompdf/www/savings_statement.php?m_id=<?php echo $membe_id; ?>"><i class="fa fa-print"></i> Print My Report</a>
            </div>
            <div class="box-body">
              <?php
                // Securely fetch the data for the logged-in member
                $tot = 0;
                $num = 0;
                
                $stmt_select = mysqli_prepare($con, "SELECT s.id as cid, s.amount, s.month, s.year, CONCAT(m.fname,' ',m.lname) as firstname FROM `capital_share` s INNER JOIN member m ON s.member_id = m.id WHERE s.member_id = ? ORDER BY s.date DESC");
                mysqli_stmt_bind_param($stmt_select, "i", $membe_id);
                mysqli_stmt_execute($stmt_select);
                $quer = mysqli_stmt_get_result($stmt_select);

                if (mysqli_num_rows($quer) > 0):
              ?>
                <table class="table table-bordered table-striped" id="capital_shares_table">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Amount</th>
                      <th>Month</th>
                      <th>Year</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $long = [1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'];

                    while ($row = mysqli_fetch_array($quer)) {
                      $tot += $row['amount'];
                      $num++;
                    ?>
                      <tr>
                        <td><?= $num; ?></td>
                        <td><?= number_format($row['amount'], 2); ?> RWF</td>
                        <td><?= isset($long[$row['month']]) ? $long[$row['month']] : 'N/A'; ?></td>
                        <td><?= htmlspecialchars($row['year']); ?></td>
                        <td>
                          <!-- Admin-only actions. You might want to hide these from regular members -->
                          <a href="#" data-toggle="modal" data-target="#modal-sav_<?= $row['cid']; ?>" class="btn btn-xs btn-warning">Edit</a>
                         <!-- <a href="delete_share.php?id=<?= $row['cid'];?>" onclick="return confirm('Are you sure?');" class="btn btn-xs btn-danger">Delete</a>-->
                        </td>
                      </tr>

                      <!-- MODAL FOR THIS ROW -->
                      <div class="modal fade" id="modal-sav_<?= $row['cid']; ?>">
                        <form class="form-horizontal" method="post" action="capital_share_info.php">
                          <div class="modal-dialog">
                            <div class="modal-content">
                              <div class="modal-header bg-primary">
                                <h4 class="modal-title" style="color:white"><i class="fa fa-edit"></i> Edit Capital Share</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                              </div>
                              <div class="modal-body">
                                <div class="form-group">
                                  <label for="capitaamounti_<?= $row['cid']; ?>" class="col-sm-4 control-label">Amount</label>
                                  <div class="col-sm-8">
                                    <input type="number" step="any" name="capitaamounti" id="capitaamounti_<?= $row['cid']; ?>" class="form-control" value="<?= htmlspecialchars($row['amount']); ?>">
                                    <input type="hidden" name="iiid" value="<?= $row['cid']; ?>">
                                  </div>
                                </div>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
                                <button type="submit" name="saving_edit" class="btn btn-primary">Save Changes</button>
                              </div>
                            </div>
                          </div>
                        </form>
                      </div>
                    <?php } // End while loop ?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td colspan="5" style="text-align:right; font-weight:bold;">Total: <?= number_format($tot, 2); ?> RWF</td>
                    </tr>
                  </tfoot>
                </table>
                <?php else: ?>
                  <div class="alert alert-info text-center">
                    <p>You have not made any capital share contributions yet.</p>
                  </div>
                <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
  <?php include('footer.php'); ?>
</div>
</body>
</html>
<?php
require_once "../../lib/db_function.php";
$request = first($db, "SELECT a.id,
                              a.type, 
                              a.ref_number, 
                              a.amount, 
                              a.data, 
                              a.paid_at,
                              b.fname,
                              b.lname,
                              b.company,
                              DATE(a.created_at) AS created_at_date,
                              a.created_at,
                              a.has_fine,
                              a.fine_data 
                              FROM bank_slip_requests AS a
                              INNER JOIN member AS b
                              ON a.member_id = b.id
                              WHERE a.id = ?", [$_GET['request_id']]);

$paid_at = new \DateTime($request['paid_at']);
$submitted_at = new \DateTime($request['created_at_date']);

$delay_in_day = 0;
$delay_for_submission = 0;

$saving_overdue_fine = 0;
$saving_overdue_delay = 0;
if($paid_at->getTimestamp() < $submitted_at->getTimestamp()){
  //Here we have the delay in submitting the bank slip for filling

  $payment_delay = $paid_at->diff($submitted_at);
  $delay_in_day = $payment_delay->days;

  $now = new DateTime();
  $submission_delay = $paid_at->diff($now);
  $delay_for_submission = $submission_delay->days;

}

$handler_file = "";
if($request['type'] == "social savings"){
  $handler_file = "save-approve.php";
}
?>
<form action="bankslip/<?= $handler_file ?>" method="POST" id="form_apply_loan" >
  <input type="hidden" name="request_id" value="<?= $_GET['request_id'] ?>">
  <div class="modal-header bg-primary ">
      <h4 class="modal-title">
        <span style="color:white">Accept <?= $request['type'] ?> Bank Slip Paid At <?= $request['paid_at'] ?></span>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </h4>
  </div>
  <div class="modal-body">
      <div class="card card-info">
        	<div class="card-body">
            <div class="row">
              <div class="col-sm-12">
                <table class="table table-stripped">
                  <tr>
                    <th>Type</th><td colspan="2"><?= $request['type'] ?></td>
                  </tr>
                  <tr>
                    <th>Reference</th><td colspan="2"><strong><?= $request['ref_number'] ?></strong></td>
                  </tr>
                  <?php
                  $transaction_info = json_decode($request['data']);
                  if($request['type'] == "savings"){
                    ?>
                    <tr>
                      <th>Month:</th>
                      <td><?= $transaction_info->month ?></td>
                      <td rowspan="2" class="text-center">
                        Expected before:<br />
                        <span class="badge bg-blue"><?= $transaction_info->overdue_date ?></span> <br />
                        <?php
                        if($request['has_fine']){
                          $fine_info = json_decode($request['fine_data']);
                          echo "Ref: ".$fine_info->referance_number;
                        }
                        ?>
                      </td>
                    </tr>
                    <tr><th>Year:</th><td><?= $transaction_info->year ?></td></tr>
                    <tr><th>Amount:</th><td colspan="2"><?= number_format($transaction_info->sav_amount) ?> RWF</td></tr>
                    <?php
                    if($request['has_fine']){
                      $saving_overdue_fine = $transaction_info->fine;
                      $saving_overdue_delay = $transaction_info->days;
                      ?>
                      <tr><th>Fine:</th><td colspan="2"><?= number_format($saving_overdue_fine) ?> RWF <span class="badge bg-blue"> <?= $saving_overdue_delay ?> day<?= $saving_overdue_delay > 1?"s":"" ?> of delay</span></td></tr>
                      <?php
                    }
                  }
                  if($request['type'] == 'loan payment'){
                    // $transaction_info = json_decode($request['data']);
                    ?>
                    <tr>
                      <th>ID:</th>
                      <td><?= $transaction_info->payment_number ?></td>
                      <td rowspan="2" class="text-center">
                        Expected before:<br />
                        <span class="badge bg-blue"><?= $transaction_info->payment_sched ?></span> <br />
                        
                      </td>
                    </tr>
                    <tr><th>Has Fine:</th><td><?= !is_null($request['has_fine'])?($request['has_fine']?"<span class='badge bg-red'>Yes</span>":"<span class='badge bg-green'>No</span>"):"<span class='badge bg-blue'>No</span>" ?></td></tr>
                    <tr><th>Amount:</th><td colspan="2"> <?= number_format($request['amount']) ?> RWF</td></tr>
                    <?php
                    if($transaction_info->overdue_fine > 0){
                      $fine_info = json_decode($request['fine_data']);
                      ?>
                      <tr>
                        <td colspan="3">
                          <div class="row">
                            <div class="col-sm-12 bg-red text-center">Fines Information</div>
                            <div class="col-sm-4 bg-green text-center">
                              Ref: <b><?= $fine_info->referance_number ?></b>
                            </div>
                            <div class="col-sm-4 bg-primary text-center">
                              Amount: <?= number_format($fine_info->amount) ?> Rwf
                            </div>
                            <div class="col-sm-4 bg-yellow text-center">
                              <?= (new \DateTime())->format("F Y") ?>
                            </div>
                          </div>
                        </td>
                      </tr>
                    <?php
                    }
                  }
                  if($request['type'] == 'fines payment'){
                    ?>
                    <tr><th>Month:</th><td><?= $transaction_info->month ?></td><td rowspan="2" class="text-center bg-blue"></td></tr>
                    <tr><th>Year:</th><td><?= $transaction_info->year ?></td></tr>
                    <tr><th>Amount:</th><td colspan="2"><?= number_format($transaction_info->amount) ?> RWF</td></tr>
                    <?php
                  }
                  ?>
                </table>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12">
                <div class="alert bg-primary text-center">The request submitted: <?= $request['created_at'] ?></div>
              </div>
            </div>
          </div>
      </div>
  </div>
  <div class="modal-footer justify-content-between">
      <button class="btn btn-sm btn-flat btn-danger" data-dismiss="modal" aria-label="Close" type="button" id="withdrawable">No Cancel</button>
      <button class="btn btn-sm btn-flat btn-primary" id="submit_button" type="submit" name="Accept">Yes Accept Bank Slip</button>
  </div>
</form>

<script type="text/javascript">
  $("#form_apply_loan").submit(function(e){
        e.preventDefault();
        var old_data = $("#submit_button").html();
        //Here make sure to use ajax request to reduce reload operations

        $("#submit_button").html('<i class="fas fa-sync fa-spin"></i> Saving...');
        $("#submit_button").attr("disabled", "disabled");

        $.ajax({
            type: $(this).attr("method"),
            url: $(this).attr("action"),
            data: $(this).serialize(),
            beforeSend: function(){
                $("#submit_button").html('<i class="fas fa-sync fa-spin"></i> Saving...');
            },success: function(data){
                if(data.status == true){
                    refresh_url = "bankslip/index.php";
                    refresh_target_containner = "loans_container";
                    toastr.success(data.message);
                    $("#modal_member").modal("hide");
                } else {
                    //Here Make sure to notify what happens during the processing
                    refresh_url = '';
                    if(data.message){
                      toastr.warning(data.message);
                    } else {
                      toastr.error("The Server Responded with unformatable message");
                    }
                }
                $("#submit_button").html(old_data);
                $("#submit_button").removeAttr("disabled");
            }, error: function(err){
                console.log(err);
                $("#submit_button").html(old_data);
                $("#submit_button").removeAttr("disabled");
                toastr.error("Invalid Response");
            }
        });
    });
</script>
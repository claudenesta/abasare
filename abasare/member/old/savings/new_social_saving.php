<?php
require_once "../../lib/db_function.php";

//check if we have any saving requests
$savings_request = returnAllData($db, "SELECT type, ref_number, amount, data, paid_at FROM bank_slip_requests WHERE member_id = ? AND type = ? AND status=?", [$_SESSION['acc'], "social savings", "Open"]);

if(count($savings_request) > 0){
    ?>
    <div class="modal-header bg-primary">
        <h4 class="modal-title">
          <span style="color:white">Saving Request is pending please wait for its validation</span>
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
                        <table class="table" id="savings_request_table">
                            <thead>
                                <tr>
                                    <th>Payment Date</th>
                                    <th>Reference</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach($savings_request AS $request){
                                    ?>
                                    <tr>
                                        <td><?= $request['paid_at'] ?></td>
                                        <td><?= $request['ref_number'] ?></td>
                                        <td class="text-right"><?= number_format($request['amount']) ?> RWF</td>
                                    </tr>
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
    <div class="modal-footer justify-content-between">
        
    </div>
    <script type="text/javascript">
        $("#savings_request_table").DataTable();
    </script>
    <?php
    return;
}

$last_saving = first($db, "SELECT id, m_id AS member_id, amount AS sav_amount, month, year FROM sacial_saving WHERE m_id = ? ORDER BY year DESC, month DESC LIMIT 0,1", [$_SESSION['user']['member_acc']]);
if($last_saving){
    $saving_year = $last_saving['year'];
    $saving_month = $last_saving['month'];
    if(12 == $saving_month){
        $saving_month = 1;
        $saving_year++;
    } else {
        $saving_month++;
    }
}

$saving_info = new \DateTime($saving_year."-".($saving_month < 10?"0":"").$saving_month."-01");
$default_deadline = $saving_info->format("Y-m-t");
$saving_overdone = first($db, "SELECT a.*,
                                    COALESCE(b.social_overdue, '{$default_deadline}') AS saving_overdue,
                                    b.id AS overdue_id
                                    FROM (
                                      SELECT
                                              '{$saving_year}' AS year,
                                              '{$saving_month}' AS month
                                    ) AS a
                                    LEFT JOIN overdue_settings AS b
                                    ON a.year = b.year AND a.month = b.month");

$minimum_saving = 2000;

$class = "green";
$class_btn = "success";
$overdone = false;
$delay_days = 0;
if(!is_null($saving_overdone) && $saving_overdone){
    $contribution_date = new \DateTime($saving_overdone['saving_overdue']);
    $toDay = new \DateTime();

    $required_fines = 0;
    if($contribution_date->getTimestamp() < $toDay->getTimestamp()){
        $class = "red";
        $class_btn = "danger";
        $overdone = true;
        $delay = $contribution_date->diff($toDay);
        $delay_days = $delay->days;
        $required_fines = $delay_days * 100;
    }
}
?>

<form action="savings/request_social_contribution.php" method="POST" id="form_apply_loan" >
    <input type="hidden" name="overdue_id" value="<?= $saving_overdone['overdue_id'] ?>">
    <input type="hidden" name="fines" value="<?= $required_fines ?>">
    <input type="hidden" name="days" value="<?= $delay_days ?>">
    <input type="hidden" name="month" value="<?= $saving_info->format('n') ?>">
    <input type="hidden" name="year" value="<?= $saving_info->format('Y') ?>">
    <div class="modal-header bg-<?= $class ?> ">
        <h4 class="modal-title">
            <span style="color:white">
                Contribution for <?= $saving_info->format('F Y') ?>
                <?php
                if($required_fines > 0){
                    ?>
                    Fines: <?= number_format($required_fines) ?> RWF
                    <?php
                }
                ?>
            </span>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </h4>
    </div>
    <div class="modal-body">
        <div class="card card-info">
          <div class="card-body">
			<div class="form-group row">
			    <label for="ref_number" class="col-sm-4 col-form-label">
			    	Reference Number:
				</label>
				<div class="col-sm-8">
			    	<input type="text" name="ref_number" id="ref_number" class="form-control">
				</div>
			</div>
			<div class="form-group row">
			    <label for="amount" class="col-sm-4 col-form-label">
			    	Paid Amount:
				</label>
				<div class="col-sm-8">
			    	<input type="text" name="amount" id="amount" class="form-control" value="<?= RoundUp($minimum_saving) ?>">
				</div>
			</div>


			<div class="form-group row">
                <label for="paid_at" class="col-sm-4 col-form-label">Paid on:</label>

                <div class="col-sm-8">
                  <input type="text" name="paid_at" class="form-control pull-right" id="paid_at" value="<?= (new \DateTime())->format("Y-m-d") ?>">
                </div>
                <!-- /.input group -->
            </div>


            <?php
            if($required_fines > 0){
                ?>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="alert alert-danger">You delayed for <?= $delay_days ?> day<?= $delay_days > 1?"s":"" ?> the fine applied is <?= number_format($required_fines) ?>RWF</div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="fine_amount" class="col-sm-4 col-form-label">
                        Fine Amount:
                    </label>
                    <div class="col-sm-8">
                        <input type="text" name="fine_amount" id="fine_amount" class="form-control" value="<?= RoundUp($required_fines) ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="fine_ref_number" class="col-sm-4 col-form-label">
                        Fine Reference Number:
                    </label>
                    <div class="col-sm-8">
                        <input type="text" name="fine_ref_number" id="fine_ref_number" class="form-control">
                    </div>
                </div>
                <?php
            }
            ?>

          </div>
        </div>
    </div>
    <div class="modal-footer justify-content-between">
        <?php
        if($overdone){
            ?>
            <a href="#" class="btn btn-danger btn-flat btn-sm">Fine Required: <?= number_format($required_fines) ?> RWF</a>
            <button class="btn btn-sm btn-flat btn-<?= $class_btn ?> submit_button" id="submit_button" type="submit" name="update_signature">Save Bank Slip</button>
            <?php
        } else {
            ?>
            <button class="btn btn-sm btn-flat btn-<?= $class_btn ?> submit_button" id="submit_button" type="submit" name="update_signature">Save Bank Slip</button>
            <?php
        }
        ?>
    </div>
</form>

<script type="text/javascript">
    $('#paid_at').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd'
    })
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
                    refresh_url = "savings/social.php?member_id=";
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
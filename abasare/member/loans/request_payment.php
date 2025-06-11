<?php

require_once "../../lib/db_function.php";
$class = "green";
$class_btn = "success";


$payment_request = returnAllData($db, "SELECT type, ref_number, amount, data, paid_at FROM bank_slip_requests WHERE member_id = ? AND type = ? AND status=?", [$_SESSION['acc'], "loan payment", "Open"]);
/*
if(count($payment_request) > 0){
    ?>
    <div class="modal-header bg-primary">
        <h4 class="modal-title">
          <span style="color:white">Loan Payment Request is pending please wait for its validation</span>
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
                                foreach($payment_request AS $request){
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
}*/

$loan_payment = first($db, "SELECT 	a.id,
									IF(a.amount > 0, a.amount, b.loan_amount_term) AS amount,
									a.overdue_fine,
									c.lname AS loan_name,
									c.is_emergeny,
									a.payment_sched,
									b.loan_amount,
                                    YEAR(a.payment_sched) AS salary_year,
                                    MONTH(a.payment_sched) AS salary_month,
                                    a.borrower_loan_id,
                                    b.loan_date,
                                    COALESCE(d.amount, 0) AS remainder
									FROM lend_payments AS a
									INNER JOIN member_loans AS b
									ON a.borrower_loan_id = b.id
									INNER JOIN loan_type AS c
									ON b.loan_id = c.id
                                    LEFT JOIN loan_remainder AS d
                                    ON b.id = d.loan_id
									WHERE a.id = ?
									", [$_GET['payment_id']]);

$payment_info = new \DateTime($loan_payment['payment_sched']);

$now = new \DateTime();
$fine_rate = 0;
$delays = $_GET['delays'];
$days = $_GET['days'];

$fines = $_GET['fines'];
?>
<form action="loans/save_request_payment.php" method="POST" id="form_apply_loan" >
    <input type="hidden" name="loan_payment_id" value="<?= $_GET['payment_id'] ?>">
    <input type="hidden" name="days" value="<?= $delays ?>">
    <input type="hidden" name="fines" value="<?= $fines ?>">
    <input type="hidden" name="month" value="<?= $payment_info->format('n') ?>">
    <input type="hidden" name="year" value="<?= $payment_info->format('Y') ?>">
    <input type="hidden" name="installment_info" id="installment_info" value="<?= $loan_payment['amount'] ?>">
    <input type="hidden" name="loan_id" id="loan_id" value="<?= $loan_payment['borrower_loan_id'] ?>">
    <input type="hidden" name="remainder" value="<?= $loan_payment['remainder'] ?>" id="remainder_value">
    <div class="modal-header bg-<?= $class ?> ">
        <h4 class="modal-title">
          <span style="color:white">Loan Payment for <?= $loan_payment['loan_name'] ?>(<?= number_format($loan_payment['loan_amount']) ?> RWF)</span>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </h4>
    </div>
    <div class="modal-body">
        <div class="card card-info">
          	<div class="card-body">
                <?php
                if(count($payment_request) > 0){
                    ?>
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <div class="alert alert-info">
                                <ul>
                                    <?php
                                    foreach($payment_request AS $pr){
                                        ?>
                                        <li><?= $pr['ref_number'] ?>(<?= number_format($pr['amount']) ?>) paid at <?= (new \DateTime($pr['paid_at']))->format("Y-m-d") ?></li>
                                        <?php
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
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
				    	<input type="text" name="amount" id="amount" class="form-control" value="<?= RoundUp( $loan_payment['amount'] - $loan_payment['remainder']) ?>">
					</div>
				</div>


				<div class="form-group row">
                <label for="paid_at" class="col-sm-4 col-form-label">
    Paid on (<span style="color: red;">Payslip-Date <i class="fa fa-exclamation-circle" style="color: red;" aria-hidden="true"></i></span>):
</label>




	                <div class="col-sm-8">
	                  <input type="text" name="paid_at" class="form-control pull-right" id="paid_at" value="<?= (new \DateTime())->format("Y-m-d") ?>">
	                </div>
	                <!-- /.input group -->
	            </div>
                <div class="row">
                    <?php
                    $my_size = 12;
                    if($fines){
                        $my_size = 6;
                        ?>
                        <div class="col-sm-6">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="alert alert-warning" id="delay_warning"><?= $delays ?> month<?= $delays > 1?"s":"" ?> Delay <?= number_format($fines) ?>RWF Applied</div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="fine_amount" class="col-sm-4 col-form-label">
                                    Fine Amount:
                                </label>
                                <div class="col-sm-8">
                                    <input type="text" name="fine_amount" id="fine_amount" class="form-control" value="<?= RoundUp($fines) ?>">
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
                        </div>
                        <?php
                    }
                    ?>
                    <div class="col-sm-<?= $my_size ?>">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="alert alert-success">Affected Installements</div>
                            </div>
                        </div>
                        <div id="paid_installements"></div>
                    </div>
                </div>

          	</div>
        </div>
    </div>
    <div class="modal-footer justify-content-between">
        <button class="btn btn-defalut">Remainder: <?= number_format($loan_payment['remainder']) ?> RWF</button>
        <a href="#" class="btn btn-info btn-flat btn-sm">Supposed to pay before <?= $payment_info->format("Y-m-d") ?></a>
        <button class="btn btn-sm btn-flat btn-<?= $class_btn ?> submit_button" id="submit_button" type="submit" name="update_signature">Save Bank Slip</button>
    </div>
</form>

<script type="text/javascript">
    $('#paid_at').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd'
    });

    var found_inst = 0;
    var request_sent = false;

    $("#amount").keyup(function(e){
        var paid = $("#amount").val()*1 + $("#remainder_value").val()*1;
        var installment = $("#installment_info").val()*1;

        var found_installments = Math.floor(paid/installment);
        // if(found_inst != found_installments){
            //make request to server to check other possible installement to be paid and the corresponding fines at this time

            if(!request_sent){
                request_sent = true;
                setTimeout(function(){
                    $("#paid_installements").load('loans/check_installment.php?loan_id=' + $("#loan_id").val() + '&installments=' + found_installments + '&amount=' + paid, function(){
                        request_sent = false
                    });
                },1000);
            }
            found_inst = found_installments;
        // }
    }).trigger("keyup");
	$("#form_apply_loan").submit(function(e){
        e.preventDefault();
        
        var amount = $("#principal").val()*1;
        // var loan_limit = $("#loan_limit").val()*1;

    	// if(loan_limit < amount){
    	// 	return false;
    	// }

    	// var withdrawable_amount = $("#withdrawable_amount").val();
    	// if(withdrawable_amount <= 0){
    	// 	return false;
    	// }

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
                    refresh_url = "loans/payments.php?year=" + $("#payment_year").val();
                    refresh_target_containner = "savings_container";
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
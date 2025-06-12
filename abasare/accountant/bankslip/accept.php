<?php
require_once "../../lib/db_function.php";

$request_id = $_GET['request_id'] ?? 0;

// First get basic request info that exists for all types
$request = first($db, "SELECT a.id, a.type, a.ref_number, a.amount, a.data, 
                              a.paid_at, a.has_fine, a.fine_data,
                              b.fname, b.lname, b.company,
                              DATE(a.created_at) AS created_at_date, a.created_at
                       FROM bank_slip_requests AS a
                       INNER JOIN member AS b ON a.member_id = b.id
                       WHERE a.id = ?", [$request_id]);

if(!$request) {
    die("<div class='alert alert-danger'>Request not found</div>");
}

// For savings type, get additional month/year fields
if($request['type'] == "savings") {
    $savings_data = first($db, "SELECT month_to_save_for, year_to_save_for 
                               FROM bank_slip_requests 
                               WHERE id = ?", [$request_id]);
    $request = array_merge($request, $savings_data);
    
    // Calculate savings-specific dates and delays
    $paid_at = new DateTime($request['paid_at']);
    $submitted_at = new DateTime($request['created_at_date']);
    
    $currentMonth = (int)date('n');
    $currentYear = (int)date('Y');
    $isCurrentMonth = ($request['month_to_save_for'] == $currentMonth && 
                      $request['year_to_save_for'] == $currentYear);
    
    $delay_in_day = 0;
    if($isCurrentMonth && $paid_at->getTimestamp() < $submitted_at->getTimestamp()) {
        $payment_delay = $paid_at->diff($submitted_at);
        $delay_in_day = $payment_delay->days;
    }
}

// Set handler file based on type
$handler_file = match($request['type']) {
    "savings" => "save-approve.php",
    "loan payment" => "save-approve-loan-payment.php",
    "fines payment" => "save-approve-fines-payment.php",
    default => ""
};
?>

<form action="bankslip/<?= $handler_file ?>" method="POST" id="form_apply_loan">
    <input type="hidden" name="request_id" value="<?= $request_id ?>">
    <div class="modal-header bg-primary">
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
                            if($request['type'] == "savings"): ?>
                                <tr>
                                    <th>Month to save for:</th>
                                    <td><?= date('F Y', mktime(0, 0, 0, $request['month_to_save_for'], 1, $request['year_to_save_for'])) ?></td>
                                    <td rowspan="2" class="text-center">
                                        <?php if($isCurrentMonth): ?>
                                            Expected before:<br />
                                            <span class="badge bg-blue"><?= $transaction_info->overdue_date ?? 'N/A' ?></span> <br />
                                            <?php if($request['has_fine'] && isset($transaction_info->referance_number)): ?>
                                                Ref: <?= $transaction_info->referance_number ?>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge bg-green">Future Month - No Overdue</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr><th>Amount:</th><td><?= number_format($transaction_info->sav_amount) ?> RWF</td></tr>
                                <?php if($request['has_fine'] && $isCurrentMonth): ?>
                                    <tr><th>Fine:</th><td colspan="2">
                                        <?= number_format($transaction_info->fine ?? 0) ?> RWF 
                                        <span class="badge bg-blue"> 
                                            <?= $transaction_info->days ?? 0 ?> day<?= ($transaction_info->days ?? 0) > 1 ? "s" : "" ?> of delay
                                        </span>
                                    </td></tr>
                                <?php endif; ?>
                            
                            <?php elseif($request['type'] == 'loan payment'): ?>
                                <tr>
                                    <th>ID:</th>
                                    <td><?= $transaction_info->payment_number ?? 'N/A' ?></td>
                                    <td rowspan="2" class="text-center">
                                        Expected before:<br />
                                        <span class="badge bg-blue"><?= $transaction_info->payment_sched ?? 'N/A' ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Has Fine:</th>
                                    <td>
                                        <?= $request['has_fine'] ? "<span class='badge bg-red'>Yes</span>" : "<span class='badge bg-green'>No</span>" ?>
                                    </td>
                                </tr>
                                <tr><th>Amount:</th><td colspan="2"><?= number_format($request['amount']) ?> RWF</td></tr>
                                <?php if(isset($transaction_info->overdue_fine) && $transaction_info->overdue_fine > 0): ?>
                                    <tr>
                                        <td colspan="3">
                                            <div class="row">
                                                <div class="col-sm-12 bg-red text-center">Fines Information</div>
                                                <div class="col-sm-4 bg-green text-center">
                                                    Ref: <b><?= $transaction_info->referance_number ?? 'N/A' ?></b>
                                                </div>
                                                <div class="col-sm-4 bg-primary text-center">
                                                    Amount: <?= number_format($transaction_info->amount ?? 0) ?> Rwf
                                                </div>
                                                <div class="col-sm-4 bg-yellow text-center">
                                                    <?= (new DateTime())->format("F Y") ?>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            
                            <?php elseif($request['type'] == 'fines payment'): ?>
                                <tr><th>Month:</th><td><?= $transaction_info->month ?? 'N/A' ?></td><td rowspan="2" class="text-center bg-blue"></td></tr>
                                <tr><th>Year:</th><td><?= $transaction_info->year ?? 'N/A' ?></td></tr>
                                <tr><th>Amount:</th><td colspan="2"><?= number_format($transaction_info->amount ?? 0) ?> RWF</td></tr>
                            <?php endif; ?>
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
        <button class="btn btn-sm btn-flat btn-danger" data-dismiss="modal" aria-label="Close" type="button">Cancel</button>
        <button class="btn btn-sm btn-flat btn-primary" id="submit_button" type="submit" name="Accept">Accept Bank Slip</button>
    </div>
</form>

<script>
$(document).ready(function() {
    $("#form_apply_loan").submit(function(e){
        e.preventDefault();
        var btn = $(this).find("#submit_button");
        var originalText = btn.html();
        
        btn.html('<i class="fas fa-spinner fa-spin"></i> Processing...')
           .prop('disabled', true);
        
        $.ajax({
            type: "POST",
            url: $(this).attr("action"),
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if(response.status) {
                    toastr.success(response.message);
                    $("#modal_member").modal("hide");
                    if(typeof loadPage === 'function') {
                        loadPage("bankslip/index.php", "loans_container");
                    }
                } else {
                    toastr.error(response.message || "Processing failed");
                }
            },
            error: function(xhr) {
                toastr.error("Error: " + xhr.responseText);
            },
            complete: function() {
                btn.html(originalText).prop('disabled', false);
            }
        });
    });
});
</script>
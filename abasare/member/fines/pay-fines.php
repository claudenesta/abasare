<?php
require_once "../../lib/db_function.php";

$fine_to_pay = first($db, "SELECT a.id, a.fine_amount, b.name AS fine_name,
                          CONCAT(c.fname, ' ', c.lname) AS member_name
                          FROM special_fines AS a
                          INNER JOIN fine_types AS b ON a.fine_type_id = b.id
                          INNER JOIN member AS c ON a.member_id = c.id
                          WHERE a.id = ?", [$_GET['fine_id']]);

if(!$fine_to_pay) {
    die("<div class='alert alert-danger'>Fine not found</div>");
}

// Generate CSRF token
if(empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<form action="fines/save_request_payment.php" method="POST" id="payment_form">
    <input type="hidden" name="fine_id" value="<?= $fine_to_pay['id'] ?>">
    <input type="hidden" name="description" value="<?= htmlspecialchars($fine_to_pay['member_name']) . ' ' . htmlspecialchars($fine_to_pay['fine_name']) ?>">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    
    <div class="modal-header bg-yellow">
        <h4 class="modal-title">
            <span style="color:white"><?= htmlspecialchars($fine_to_pay['member_name']) ?> <?= htmlspecialchars($fine_to_pay['fine_name']) ?> Payment</span>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </h4>
    </div>
    
    <div class="modal-body">
        <div class="card card-info">
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Reference Number:</label>
                    <div class="col-sm-8">
                        <input type="text" name="ref_number" class="form-control" required>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Paid Amount:</label>
                    <div class="col-sm-8">
                        <input type="number" name="amount" class="form-control" 
                               value="<?= RoundUp($fine_to_pay['fine_amount']) ?>" required>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Paid on:</label>
                    <div class="col-sm-8">
                        <input type="text" name="paid_at" class="form-control datepicker" 
                               value="<?= (new DateTime())->format("Y-m-d") ?>" required>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-warning" id="submit_btn">Save Bank Slip</button>
    </div>
</form>

<script>
$(document).ready(function() {
    // Initialize datepicker
    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true
    });
    
    // Handle form submission
    $('#payment_form').submit(function(e) {
        e.preventDefault();
        var btn = $('#submit_btn');
        var form = $(this);
        var originalText = btn.html();
        
        btn.html('<i class="fas fa-spinner fa-spin"></i> Processing...')
           .prop('disabled', true);
        
        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if(response && response.status === true) {
                    toastr.success(response.message);
                    $('#modal_member').modal('hide');
                    form[0].reset();
                    if(response.reload) {
                        setTimeout(function() {
                            location.reload(true);
                        }, 1000);
                    }
                } else {
                    toastr.error(response?.message || "Payment failed");
                }
            },
            error: function(xhr) {
                var errorMsg = "Payment error occurred";
                try {
                    var response = JSON.parse(xhr.responseText);
                    errorMsg = response.message || errorMsg;
                } catch(e) {
                    errorMsg = xhr.statusText || errorMsg;
                }
                toastr.error(errorMsg);
                console.error("Payment Error:", xhr);
            },
            complete: function() {
                btn.html(originalText).prop('disabled', false);
            }
        });
    });
});
</script>

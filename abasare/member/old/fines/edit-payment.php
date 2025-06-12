<?php
require_once "../../lib/db_function.php";

$fine = first($db, "SELECT a.id, a.fine_amount, a.reference_number,
                    CONCAT(b.fname, ' ', b.lname) AS member_name, 
                    c.name AS fine_name
                    FROM special_fines a
                    JOIN member b ON a.member_id = b.id
                    JOIN fine_types c ON a.fine_type_id = c.id
                    WHERE a.id = ?", [$_GET['fine_id']]);

if(!$fine) die("<div class='alert alert-danger'>Fine not found</div>");

// Get the existing payment request
$payment = first($db, "SELECT * FROM bank_slip_requests 
                      WHERE ref_number = ? AND type = 'fines payment'
                      ORDER BY created_at DESC LIMIT 1", [$fine['reference_number']]);

if(!$payment) die("<div class='alert alert-danger'>Original payment not found</div>");
?>

<form action="fines/save-edit-payment.php" method="POST" id="payment_form">
    <input type="hidden" name="fine_id" value="<?= $fine['id'] ?>">
    <input type="hidden" name="payment_id" value="<?= $payment['id'] ?>">
    <input type="hidden" name="description" value="<?= htmlspecialchars($fine['member_name'].' - '.$fine['fine_name']) ?>">
    
    <div class="modal-header bg-warning">
        <h5 class="modal-title">Update Payment</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
    </div>
    
    <div class="modal-body">
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-circle"></i> Please correct and resubmit your payment
        </div>
        
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Reference Number:</label>
            <div class="col-sm-8">
                <input type="text" name="ref_number" class="form-control" 
                       value="<?= htmlspecialchars($payment['ref_number']) ?>">
            </div>
        </div>
        
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Amount:</label>
            <div class="col-sm-8">
                <input type="number" name="amount" class="form-control" 
                       value="<?= htmlspecialchars($payment['amount']) ?>" required>
            </div>
        </div>
        
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Payment Date:</label>
            <div class="col-sm-8">
                <input type="text" name="paid_at" class="form-control datepicker" 
                       value="<?= htmlspecialchars($payment['paid_at']) ?>" required>
            </div>
        </div>
    </div>
    
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-warning">Update Payment</button>
    </div>
</form>

<script>
$(document).ready(function() {
    $('.datepicker').datepicker({format: 'yyyy-mm-dd', autoclose: true});
    
    $('#payment_form').submit(function(e) {
        e.preventDefault();
        var btn = $(this).find('[type="submit"]');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating');
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                if(res.status) {
                    toastr.success(res.message);
                    $('#modal_member').modal('hide');
                    $(document).trigger('paymentSuccess');
                } else {
                    toastr.error(res.message || "Payment update failed");
                }
            },
            error: function(xhr, status, error) {
                // Extract the full error response
                var errorMessage = "An error occurred";
                try {
                    // Try to parse the response as JSON
                    var response = JSON.parse(xhr.responseText);
                    errorMessage = response.message || errorMessage;
                    
                    // If there's a validation error, show details
                    if (response.errors) {
                        errorMessage += ": " + JSON.stringify(response.errors);
                    }
                } catch(e) {
                    // If not JSON, show the raw response
                    errorMessage = xhr.responseText || errorMessage;
                }
                
                // Also include the HTTP status
                errorMessage += " (Status: " + xhr.status + ")";
                
                toastr.error(errorMessage);
                console.error("Payment Error:", xhr.responseText, xhr.status, error);
            },
            complete: function() {
                btn.prop('disabled', false).html('Update Payment');
            }
        });
    });
});
</script>
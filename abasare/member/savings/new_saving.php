<!-- New_saving.php -->
<?php
require_once "../../lib/db_function.php";

// Check for pending requests
$savings_request = returnAllData($db, 
    "SELECT type, ref_number, amount, data, paid_at 
     FROM bank_slip_requests 
     WHERE member_id = ? AND type = ? AND status=?", 
    [$_SESSION['acc'], "savings", "Open"]);


// Get minimum saving amount
$minimum_saving = returnSingleField($db, 
    "SELECT COALESCE(MAX(b.saving), 
            (SELECT sav_amount FROM saving WHERE member_id = ? ORDER BY id DESC LIMIT 1)
    ) as required_amount
     FROM member_loans a
     JOIN member_loan_settings b ON a.id = b.loan_id
     WHERE a.status = 'ACTIVE' AND a.member_id = ?",
    "required_amount",
    [$_SESSION['user']['member_acc'], $_SESSION['user']['member_acc']]
);

// Current date for default selection
$currentMonth = (int)date('n');
$currentYear = (int)date('Y');
?>

<form action="savings/request_contribution.php" method="POST" id="form_apply_loan">
    <div class="modal-header bg-green">
        <h4 class="modal-title">
            <span style="color:white">New Savings Contribution</span>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
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
                    <label class="col-sm-4 col-form-label">Amount:</label>
                    <div class="col-sm-8">
                        <input type="number" name="amount" class="form-control" 
                               value="<?= RoundUp($minimum_saving) ?>" required>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Paid on:</label>
                    <div class="col-sm-8">
                        <input type="text" name="paid_at" class="form-control" id="paid_at" 
                               value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Month to Save For:</label>
                    <div class="col-sm-8">
                        <select name="month_to_save_for" class="form-control" style="width:49%;display:inline-block" required>
                            <?php for($i=1; $i<=12; $i++): ?>
                            <option value="<?= $i ?>" <?= $i==$currentMonth?'selected':'' ?>>
                                <?= date('F', mktime(0,0,0,$i,1)) ?>
                            </option>
                            <?php endfor; ?>
                        </select>
                        <select name="year_to_save_for" class="form-control" style="width:49%;display:inline-block" required>
                            <?php for($i=$currentYear; $i<=$currentYear+1; $i++): ?>
                            <option value="<?= $i ?>" <?= $i==$currentYear?'selected':'' ?>>
                                <?= $i ?>
                            </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary" id="submit_button">Submit</button>
    </div>
</form>

<script>
    // Initialize datepicker
    $('#paid_at').datepicker({autoclose: true, format: 'yyyy-mm-dd'});
    
    // Handle form submission
    $('#form_apply_loan').submit(function(e) {
        e.preventDefault();
        
        var oldText = $('#submit_button').html();
        $('#submit_button').html('<i class="fas fa-sync fa-spin"></i> Saving...');
        $('#submit_button').attr('disabled', 'disabled');
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    // Show success toast
                    toastr.success(response.message);
                    
                    // Close modal if it exists
                    $('#modal-default').modal('hide');
                    
                    // Reload parent page after short delay
                    setTimeout(function() {
                        window.location.href = '../member/savings.php';
                    }, 1500);
                } else {
                    // Show error toast
                    toastr.error(response.message);
                }
                
                // Reset button
                $('#submit_button').html(oldText);
                $('#submit_button').removeAttr('disabled');
            },
            error: function() {
                toastr.error('An error occurred while processing your request.');
                $('#submit_button').html(oldText);
                $('#submit_button').removeAttr('disabled');
            }
        });
    });
</script>
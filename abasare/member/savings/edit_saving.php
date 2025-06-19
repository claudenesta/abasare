<?php
require_once "../../lib/db_function.php";

// Get the saving record to edit
$saving = first($db, 
    "SELECT id, ref_number, sav_amount, month_to_save_for, year_to_save_for 
     FROM saving 
     WHERE id = ? AND member_id = ? AND status = 'Rejected'",
    [$_GET['id'], $_SESSION['user']['member_acc']]
);

if (!$saving) {
    header('Content-Type: application/json');
    echo json_encode(['status' => false, 'message' => 'Invalid saving record or not allowed to edit']);
    exit;
}

// Get minimum saving amount (optional, for reference)
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
?>

<form action="savings/request_edit_saving.php" method="POST" id="form_edit_saving">
    <input type="hidden" name="saving_id" value="<?= $saving['id'] ?>">
    
    <div class="modal-body">
        <div class="card card-warning">
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Reference Number:</label>
                    <div class="col-sm-8">
                        <input type="text" name="ref_number" class="form-control" 
                               value="<?= htmlspecialchars($saving['ref_number']) ?>" required>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Amount:</label>
                    <div class="col-sm-8">
                        <input type="number" name="amount" class="form-control" 
                               value="<?= RoundUp($saving['sav_amount']) ?>" required>
                    </div>
                </div>
                
                <!-- <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Paid on:</label>
                    <div class="col-sm-8">
                        <input type="text" name="paid_at" class="form-control" id="paid_at" 
                               value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div> -->
                
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Month to Save For:</label>
                    <div class="col-sm-8">
                        <select name="month_to_save_for" class="form-control" style="width:49%;display:inline-block" required>
                            <?php for($i=1; $i<=12; $i++): ?>
                            <option value="<?= $i ?>" <?= $i==$saving['month_to_save_for']?'selected':'' ?>>
                                <?= date('F', mktime(0,0,0,$i,1)) ?>
                            </option>
                            <?php endfor; ?>
                        </select>
                        <select name="year_to_save_for" class="form-control" style="width:49%;display:inline-block" required>
                            <?php 
                            $currentYear = (int)date('Y');
                            for($i=$currentYear-1; $i<=$currentYear+1; $i++): ?>
                            <option value="<?= $i ?>" <?= $i==$saving['year_to_save_for']?'selected':'' ?>>
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
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" id="submit_button">Submit Edit</button>
    </div>
</form>

<script>
    $('#form_edit_saving').submit(function(e) {
        e.preventDefault();
        
        var oldText = $('#submit_button').html();
        $('#submit_button').html('<i class="fas fa-sync fa-spin"></i> Updating...');
        $('#submit_button').attr('disabled', 'disabled');
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    toastr.success(response.message);
                    $('#editModal').modal('hide');
                    setTimeout(function() {
                        window.location.href = '../member/savings.php';
                    }, 1500);
                } else {
                    toastr.error(response.message);
                }
                $('#submit_button').html(oldText);
                $('#submit_button').removeAttr('disabled');
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error: ' + status + ' - ' + error + ' - ' + xhr.responseText);
                toastr.error('An error occurred while processing your request.');
                $('#submit_button').html(oldText);
                $('#submit_button').removeAttr('disabled');
            }
        });
    });
</script>
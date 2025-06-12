<?php
require_once "../../lib/db_function.php";

$fine_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch fine data using your existing 'first' function
$fine = first($db, "SELECT sf.*, 
                   CONCAT(m.fname, ' ', m.lname) AS member_name,
                   ft.name AS fine_name
                   FROM special_fines sf
                   JOIN member m ON sf.member_id = m.id
                   JOIN fine_types ft ON sf.fine_type_id = ft.id
                   WHERE sf.id = ?", [$fine_id]);

if(!$fine) {
    die("<div class='alert alert-danger'>Fine not found</div>");
}

// Fetch available fine types using your existing 'returnAllData' function
$fine_types = returnAllData($db, "SELECT id, name, default_amount FROM fine_types ORDER BY name ASC");
?>

<form action="fines/save-edit-fine.php" method="POST" id="form_edit_fine">
    <input type="hidden" name="fine_id" value="<?= $fine['id'] ?>">
    
    <div class="modal-header bg-primary">
        <h4 class="modal-title">Edit Fine for <?= htmlspecialchars($fine['member_name']) ?></h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
    </div>
    
    <div class="modal-body">
        <div class="form-group">
            <label>Fine Type</label>
            <select class="form-control" name="fine_type_id" required>
                <?php foreach($fine_types as $type): ?>
                <option value="<?= $type['id'] ?>" 
                    data-amount="<?= $type['default_amount'] ?>"
                    <?= $type['id'] == $fine['fine_type_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($type['name']) ?> (<?= number_format($type['default_amount']) ?> Rwf)
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>Amount (RWF)</label>
            <input type="number" class="form-control" name="fine_amount" 
                   value="<?= $fine['fine_amount'] ?>" required min="0" step="100">
        </div>
    </div>
    
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </div>
</form>

<script>
$(document).ready(function() {
    // Update amount when fine type changes
    $('select[name="fine_type_id"]').change(function() {
        var amount = $(this).find('option:selected').data('amount');
        $('input[name="fine_amount"]').val(amount);
    });
    
    // Form submission handler
    $('#form_edit_fine').submit(function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = form.find('[type="submit"]');
        var originalText = submitBtn.html();
        
        submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            dataType: 'json',
            data: form.serialize(),
            success: function(response) {
                if(response.status) {
                    toastr.success(response.message);
                    $("#modal_member").modal('hide');
                    $("#fines_container").load("./fines/unpaid_fines.php");
                } else {
                    toastr.error(response.message || "Update failed");
                }
            },
            error: function(xhr) {
                toastr.error("Error: " + (xhr.responseJSON?.message || "Request failed"));
            },
            complete: function() {
                submitBtn.html(originalText).prop('disabled', false);
            }
        });
    });
});
</script>
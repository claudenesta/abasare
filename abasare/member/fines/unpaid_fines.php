<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once "../../lib/db_function.php";

$fines = returnAllData($db, "SELECT a.id, a.fine_amount, a.date, a.status,
                            b.fname, b.lname, c.name AS fine_name, d.name AS user_name,
                            COALESCE(e.status, a.status) AS request_status
                            FROM special_fines a
                            JOIN member b ON a.member_id = b.id
                            JOIN fine_types c ON a.fine_type_id = c.id
                            JOIN users d ON a.user_id = d.id
                            LEFT JOIN bank_slip_requests e ON a.reference_number = e.ref_number COLLATE utf8mb4_general_ci
                            WHERE a.status IN (?,?,?,?) AND a.member_id = ?", 
                            ["Active", "Pending", "Rejected", "Accepted", $_SESSION['acc']]);

if(count($fines) > 0): ?>
<table class="table table-stripped" id="fines_table">
    <thead>
        <tr>
            <th>Date</th>
            <th>Type</th>
            <th>Amount</th>
            <th>Agent</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($fines as $fine): ?>
        <tr>
            <td><?= htmlspecialchars($fine['date']) ?></td>
            <td><?= htmlspecialchars($fine['fine_name']) ?></td>
            <td><?= htmlspecialchars($fine['fine_amount']) ?></td>
            <td><?= htmlspecialchars($fine['user_name']) ?></td>
            <td><?= htmlspecialchars($fine['status']) ?></td>
            <td>
                <?php if($fine['status'] == "Active"): ?>
                    <a href="fines/pay-fines.php?fine_id=<?= $fine['id'] ?>" 
                       class="btn btn-sm btn-primary change_request">Pay</a>
                <?php elseif($fine['status'] == "Rejected"): ?>
                    <a href="fines/edit-payment.php?fine_id=<?= $fine['id'] ?>" 
                       class="btn btn-sm btn-warning change_request">Repay</a>
                <?php else: ?>
                    <span class="badge bg-secondary">No action</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<div class="alert alert-info">No pending payments found</div>
<?php endif; ?>

<script>
$(document).ready(function() {
    $('#fines_table').DataTable();
    
    $('#fines_table').on('click', '.change_request', function(e){
        e.preventDefault();
        var link = $(this);
        var oldText = link.html();
        link.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
        
        $("#modal_member").find(".modal-content").load(link.attr('href'), function() {
            link.html(oldText).prop('disabled', false);
            $("#modal_member").modal("show");
        });
    });
});
</script> 
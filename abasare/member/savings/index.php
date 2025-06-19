<?php
require_once "../../lib/db_function.php";

$savings = returnAllData($db, "SELECT 
    a.id,
    a.member_id,
    a.status,
    a.sav_amount,
    a.comment,
    a.month_to_save_for,
    a.year_to_save_for,
    a.ref_number
FROM saving AS a
WHERE a.member_id = ?
ORDER BY a.id DESC", [$_SESSION['user']['member_acc']]);

if (count($savings) > 0) {
	?>
	<table class="table table-bordered table-hover" id="savings_table">
		<thead>
			<tr>
				<th>#</th>
				<th>Contribution Month</th>
				<th>Amount</th>
				<th>Ref number</th>
				<th>Status</th>
				<th>Comment</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$counter = 1;
			foreach ($savings as $saving) {
				$showRepayButton = ($saving['status'] == 'Rejected');
				?>
				<tr>
					<td><?= $counter++ ?></td>
					<td>
						<?= DateTime::createFromFormat('!m', $saving['month_to_save_for'])->format('F') ?>
						<?= $saving['year_to_save_for'] ?>
					</td>
					<td><?= number_format($saving['sav_amount']) ?> RWF</td>
					<td><?= $saving['ref_number'] ?></td>
					<td><?= ucfirst($saving['status']) ?></td>
					<td>
						<?php if ($saving['comment'] == ''): ?>
							<p>
								none
							</p>
						<?php else: ?>
							<span><?= htmlspecialchars($saving['comment']) ?></span>
						<?php endif; ?>

					</td>
					<td>
						<?php if ($saving['status'] == 'Rejected'): ?>
							<a href="javascript:void(0)" class="btn btn-sm btn-warning edit-btn" data-id="<?= $saving['id'] ?>">
								Save again
							</a>
						<?php else: ?>
							<span class="badge bg-secondary">No action</span>
						<?php endif; ?>
					</td>

				</tr>
				<?php
			}
			?>
		</tbody>
	</table>

	<!-- Edit Modal -->
	<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header bg-primary">
					<h4 class="modal-title">
						<span style="color:white">Edit your saving info</span>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</h4>
				</div>
				<div class="modal-body" id="editModalBody">
					<!-- Modal content will be loaded here via AJAX -->
				</div>
			</div>
		</div>
	</div>

	<!-- Repay Modal (unchanged) -->
	<div class="modal fade" id="repayModal" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form id="repayForm" action="savings/repay_saving.php" method="POST">
					<div class="modal-header bg-warning">
						<h5 class="modal-title">Repay Rejected Contribution</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">×</span>
						</button>
					</div>
					<div class="modal-body">
						<input type="hidden" name="saving_id" id="repaySavingId">
						<input type="hidden" name="month_to_save_for" id="repayMonth">
						<input type="hidden" name="year_to_save_for" id="repayYear">

						<div class="form-group">
							<label>Reference Number:</label>
							<input type="text" name="ref_number" id="repayRefNumber" class="form-control" required>
						</div>

						<div class="form-group">
							<label>Amount:</label>
							<input type="number" name="amount" id="repayAmount" class="form-control" required>
						</div>

						<div class="form-group">
							<label>Payment Date:</label>
							<input type="date" name="paid_at" class="form-control" value="<?= date('Y-m-d') ?>" required>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-primary">Submit Repayment</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<script>
		$(document).ready(function () {
			$('#savings_table').DataTable();

			// Handle edit button click
			$('.edit-btn').click(function () {
				var savingId = $(this).data('id');
				$.ajax({
					url: 'savings/edit_saving.php', // Adjust path if needed
					type: 'GET',
					data: { id: savingId },
					success: function (response) {
						if (typeof response === 'string') {
							$('#editModalBody').html(response);
						} else if (response.status === false) {
							toastr.error(response.message);
							return;
						}
						$('#editModal').modal('show');
						// Reinitialize datepicker after loading
						$('#paid_at').datepicker({ autoclose: true, format: 'yyyy-mm-dd' });
					},
					error: function (xhr, status, error) {
						console.log('AJAX Error: Status = ' + status + ', Error = ' + error + ', Response = ' + xhr.responseText);
						toastr.error('Failed to load edit form. Check console for details.');
					}
				});
			});

			// Handle repay button click (unchanged)
			$('.repay-btn').click(function () {
				$('#repaySavingId').val($(this).data('id'));
				$('#repayRefNumber').val($(this).data('ref'));
				$('#repayMonth').val($(this).data('month'));
				$('#repayYear').val($(this).data('year'));
				$('#repayAmount').val($(this).data('amount'));
				$('#repayModal').modal('show');
			});

			// Handle repay form submission (unchanged)
			$('#repayForm').submit(function (e) {
				e.preventDefault();
				var form = $(this);
				var submitBtn = form.find('[type="submit"]');
				var originalText = submitBtn.html();

				submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Processing...');
				submitBtn.prop('disabled', true);

				$.ajax({
					url: form.attr('action'),
					type: 'POST',
					data: form.serialize(),
					dataType: 'json',
					success: function (response) {
						if (response.status) {
							toastr.success(response.message);
							$('#repayModal').modal('hide');
							setTimeout(function () {
								location.reload();
							}, 1500);
						} else {
							toastr.error(response.message);
						}
					},
					error: function () {
						toastr.error('An error occurred while processing your request.');
					},
					complete: function () {
						submitBtn.html(originalText);
						submitBtn.prop('disabled', false);
					}
				});
			});
		});
	</script>
	<?php
} else {
	?>
	<div class="alert alert-info">No savings contributions found</div>
	<?php
}
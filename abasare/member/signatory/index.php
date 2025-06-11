<?php
require_once "../../lib/db_function.php";

// get the list of signatories requested
$signatories = returnAllData($db, "SELECT 	a.id,
											CONCAT(COALESCE(b.fname,''), ' ', COALESCE(b.lname,'')) AS member_name,
											c.lname AS loan_name,
											a.loan_amount,
											a.loan_date,
											a.status,
											a.signatory_1,
											a.signatory_1_status,
											a.signatory_1_comment,
											a.signatory_2,
											a.signatory_2_status,
											a.signatory_2_comment
											FROM member_loans AS a
											INNER JOIN member AS b
											ON a.member_id = b.id
											INNER JOIN loan_type AS c
											ON a.loan_id = c.id
											WHERE a.signatory_1 = ?
											OR (a.signatory_1_status = ? AND a.signatory_2 = ?)
											", [$_SESSION['user']['id'], 1, $_SESSION['user']['id']]);

if(count($signatories) > 0){
	?>
	<table class="table table-bordered table-hover" id="loans_table">
		<thead>
			<tr>
				<th>#</th>
				<th>Date</th>
				<th>Type</th>
				<th>Amount</th>
				<th>Member</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$count = 1;
			$statuses = [
				"OPEN" => "info",
				"ACTIVE" => "success",
				"REJECTED" => "danger",
				"CLOSED" => "success",
			];
			$status_sign = [
				'color' => [
					0 => 'red',
					1 => 'green'
				],
				'data' => [
					0 => 'Rejected',
					1 => 'Accepted'
				],
			];
			foreach($signatories AS $loan){
				?>
				<tr class="bg-<?= array_key_exists($loan['status'], $statuses)?$statuses[$loan['status']]:"yeallow";  ?>">
					<td><?= $count++ ?></td>
					<td><?= $loan['loan_date'] ?></td>
					<td><?= $loan['loan_name'] ?></td>
					<td class="text-right"><?= number_format($loan['loan_amount']) ?> RWF</td>
					<td class=""><?= $loan['member_name'] ?></td>
					<td>
						<?php
						if($loan['signatory_1'] == $_SESSION['user']['id']){
							if(is_null($loan['signatory_1_status'])){
								?>
								<a href="signatory/accept.php?loan_id=<?= $loan['id'] ?>&position=1" class="change_request text-success"><i class="fa fa-check"></i> Accept</a> |
								<a href="signatory/reject.php?loan_id=<?= $loan['id'] ?>&position=1" class="change_request text-danger"><i class="fa fa-check"></i> Reject</a>
								<?php
							} else {
								?>
								<span class="badge bg-<?= $status_sign['color'][$loan['signatory_1_status']] ?>"><?= $status_sign['data'][$loan['signatory_1_status']] ?></span>
								<?php
							}
						} else if($loan['signatory_2'] == $_SESSION['user']['id']){
							if(is_null($loan['signatory_2_status'])){
								?>
								<a href="signatory/accept.php?loan_id=<?= $loan['id'] ?>&position=2" class="change_request text-success"><i class="fa fa-check"></i> Accept</a> |
								<a href="signatory/reject.php?loan_id=<?= $loan['id'] ?>&position=2" class="change_request text-danger"><i class="fa fa-check"></i> Reject</a>
								<?php
							} else {
								?>
								<span class="badge bg-<?= $status_sign['color'][$loan['signatory_2_status']] ?>"><?= $status_sign['data'][$loan['signatory_2_status']] ?></span>
								<?php
							}
						}
						?>
					</td>
				</tr>
				<?php
			}
			?>
		</tbody>
	</table>

	<script type="text/javascript">
		$('#loans_table').DataTable();

		$('#loans_table').on('click', '.change_request', function(e){
			e.preventDefault();
			var link = $(this);
			var old_data = link.html();
			link.attr("disabled", "disabled");
			link.html('<i class="fas fa-sync fa-spin"></i>');

			var url = link.attr("href");
			$("#modal_member").find(".modal-content").load(url, function(){
				link.html(old_data);
		        refresh_target_containner = '';
		        refresh_url= '';
		        $("#modal_member").modal("show");
		    });
		});
	</script>
	<?php
} else {
	?>
	<div class="alert alert-info">No Signatory Request had ever addressed to you!</div>
	<?php
}
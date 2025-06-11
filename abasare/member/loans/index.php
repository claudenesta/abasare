<?php
require_once "../../lib/db_function.php";

//Get the list of loans applied before order by the order date
$loans = returnAllData($db, "SELECT a.id,
									a.loan_date,
									a.status,
									a.loan_amount,
									b.lname AS loan_name,
									c.paid_amount,
									a.signatory_1,
									a.signatory_1_status,
									a.signatory_2,
									a.signatory_2_status,
									a.committee_status AS accountant,
									a.president_status AS president,
									d.name AS signatory_1_name,
									e.name AS signatory_2_name,
									a.committee_reject_comment,
									a.president_reject_comment,
									b.id AS loan_type_id
									FROM member_loans AS a
									INNER JOIN loan_type AS b
									ON a.loan_id = b.id
									LEFT JOIN (
										SELECT 	b.borrower_loan_id,
												(SUM(b.amount) + COALESCE(c.amount,0)) AS paid_amount
												FROM member_loans AS a
												INNER JOIN lend_payments AS b
												ON a.id = b.borrower_loan_id
												LEFT JOIN loan_remainder AS c
												ON a.id = c.loan_id
												WHERE a.member_id = ?
												AND b.status = ?
												GROUP BY b.borrower_loan_id
										) AS c
									ON a.id = c.borrower_loan_id
									LEFT JOIN users AS d
									ON a.signatory_1 = d.id
									LEFT JOIN users AS e
									ON a.signatory_2 = e.id
									WHERE a.member_id = ?
									ORDER BY a.loan_date DESC
									", [$_GET['member_id'], "PAID", $_GET['member_id']]);

// var_dump($loans); die();
if(count($loans) > 0){
	?>
	<table class="table table-bordered table-hover" id="loans_table">
		<thead>
			<tr>
				<th>#</th>
				<th>Date</th>
				<th>Type</th>
				<th>Amount</th>
				<th>Paid</th>
				<th>Remains</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$count = 1;
			$statuses = [
				"OPEN" => "info",
				"ACTIVE" => "warning",
				"REJECTED" => "danger",
				"CLOSED" => "success",
			];
			foreach($loans AS $loan){
				?>
				<tr class="bg-<?= array_key_exists($loan['status'], $statuses)?$statuses[$loan['status']]:"yeallow";  ?>">
					<td><?= $count++ ?></td>
					<td><?= $loan['loan_date'] ?></td>
					<td><?= $loan['loan_name'] ?></td>
					<td class="text-right"><?= number_format($loan['loan_amount']) ?></td>
					<td class="text-right"><?= number_format($loan['paid_amount']) ?></td>
					<td class="text-right"><?= number_format($loan['loan_amount'] - $loan['paid_amount']) ?></td>
					<td>
						<?php


						if($loan['president'] == 1){
							if($loan['accountant'] == 1 && $loan['signatory_2_status'] == 1 && $loan['signatory_1_status'] == 1 ){
								?>
								<a href="loans/<?= array_key_exists($loan['loan_type_id'], $contract_files)?$contract_files[$loan['loan_type_id']]:"contract.php" ?>?loan_id=<?= $loan['id'] ?>" target="_blank" class="btn btn-success btn-xs">Contract</a>
								<?php
							} else {
								if($loan['reject'] == 1){
									?>
									<a href="#" class="badge bg-red">Rejected</a>
									<?php
								} else {
									?>
									<a href="#" class="badge bg-blue">Contract is hardcopy</a>
									<?php
								}
							}
						} else if($loan['accountant'] == 1){
							if(!is_null($loan['president'])){
								?>
								<a href="loans/rejected_preview.php?loan_id=<?= $loan['id'] ?>&position=president_reject_comment" title="Rejected by President: <?= $loan['president_reject_comment'] ?>" class="badge bg-red change_request">Rejected</a>
								<?php
							} else {
								?>
								<a href="#" title="Waitting for President approval" class="badge bg-yellow">President</a>
								<?php
							}
						} else if($loan['signatory_2_status'] == 1){
							if(!is_null($loan['accountant'])){
								?>
								<a href="loans/rejected_preview.php?loan_id=<?= $loan['id'] ?>&position=committee_reject_comment" title="Rejected by Loan Committee: <?= $loan['committee_reject_comment'] ?>" class="badge bg-red change_request">Rejected</a>
								<?php
							} else {
								?>
								<a href="#" title="Waitting for Loan Committee approval" class="badge bg-yellow">Loan Committee</a>
								<?php
							}

						} else if($loan['signatory_1_status'] == 1){
							if(!is_null($loan['signatory_2_status'])){
								?>
								<a href="loans/change_signatory.php?loan_id=<?= $loan['id'] ?>&position=2&signatory_1=<?= $loan['signatory_1'] ?>&signatory_2=<?= $loan['signatory_2'] ?>" title="<?= $loan['signatory_2_name'] ?>" class="badge bg-<?= is_null($loan['signatory_2_status'])?"yellow":"red" ?> change_request">Second Signatory</a>
								<?php
							} else {
								?>
								<a href="loans/change_signatory.php?loan_id=<?= $loan['id'] ?>&position=2&position=2&signatory_1=<?= $loan['signatory_1'] ?>&signatory_2=<?= $loan['signatory_2'] ?>" title="<?= $loan['signatory_2_name'] ?>" class="badge bg-info change_request">Second Signatory</a>
								<?php
							}
						} else {
							if(!is_null($loan['signatory_1'])){
								?>
								<a href="loans/change_signatory.php?loan_id=<?= $loan['id'] ?>&position=1&position=2&signatory_1=<?= $loan['signatory_1'] ?>&signatory_2=<?= $loan['signatory_2'] ?>" title="<?= $loan['signatory_1_name'] ?>" class="badge bg-<?= is_null($loan['signatory_1_status'])?"yellow":"red" ?> change_request">First Signatory</a>
								<?php
							} else {
								?>
								<a href="loans/change_signatory.php?loan_id=<?= $loan['id'] ?>&position=1&position=2&signatory_1=<?= $loan['signatory_1'] ?>&signatory_2=<?= $loan['signatory_2'] ?>" title="<?= $loan['signatory_1_name'] ?>" class="badge bg-info change_request">First Signatory</a>
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
	<div class="alert alert-info">No Loan can be found on the list</div>
	<?php
}
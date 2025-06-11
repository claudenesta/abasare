<?php
require_once "../lib/db_function.php";

$loans = returnAllData($db, $sql = "SELECT a.id,
									a.loan_date,
									a.status,
									a.loan_amount,
									CONCAT(COALESCE(b.fname,''), ' ', COALESCE(b.lname,'')) AS member_name,
									c.lname AS loan_name,
									d.saving_amount,
									a.member_id,
									e.loan_amount AS unpaid_loan,
									f.amount AS capital_share
									FROM member_loans AS a
									INNER JOIN member as b
									ON a.member_id = b.id
									INNER JOIN loan_type AS c
									ON a.loan_id = c.id
									LEFT JOIN (
										SELECT 	a.member_id,
												SUM(sav_amount) AS saving_amount
												FROM saving AS a
												WHERE a.member_id IN (
													SELECT member_id FROM member_loans WHERE president_status IS NULL AND committee_status = ?
												)
												GROUP BY a.member_id
										) AS d
									ON b.id = d.member_id
									LEFT JOIN (
										SELECT 	a.member_id,
												SUM(a.loan_amount) - b.paid_amount AS loan_amount
												FROM member_loans AS a
												LEFT JOIN (
													SELECT 	b.member_id,
															SUM(amount) AS paid_amount
															FROM lend_payments AS a
															INNER JOIN member_loans AS b
															ON a.borrower_loan_id = b.id
															WHERE b.status = ? AND b.member_id IN (SELECT member_id FROM member_loans WHERE president_status IS NULL AND committee_status = ?) AND a.status = ?
															GROUP BY b.member_id
													) AS b
												ON a.member_id = b.member_id
												WHERE a.status = ? AND a.member_id IN (SELECT member_id FROM member_loans WHERE president_status IS NULL AND committee_status = ?)
												GROUP BY a.member_id
										) AS e
									ON b.id = e.member_id
									INNER JOIN (
										SELECT 	a.member_id,
												SUM(amount) AS amount
												FROM capital_share AS a
												WHERE a.member_id IN (
													SELECT member_id FROM member_loans WHERE president_status IS NULL AND committee_status = ?
													)
												GROUP BY a.member_id
										) AS f
									ON a.member_id = f.member_id
									WHERE a.president_status IS NULL
									AND a.committee_status = ?
									", [1, 'ACTIVE', 1, 'PAID', 'ACTIVE', 1, 1, 1]);
// var_dump($loans);
// echo $sql;
if(count($loans) > 0){
	?>
	<table class="table table-bordered table-hover" id="loans_table">
		<thead>
			<tr>
				<th>Date</th>
				<th>Member</th>
				<th>Type</th>
				<th>Amount</th>
				<th>Unpaid</th>
				<th>Savings</th>
				<th>Share</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$count=1;
			foreach($loans AS $loan){
				?>
				<tr>
					<td><?= $loan['loan_date'] ?></td>
					<td><?= $loan['member_name'] ?></td>
					<td><?= $loan['loan_name'] ?></td>
					<td class="text-right"><?= number_format($loan['loan_amount']) ?></td>
					<td class="text-right">
						<a href="actions/pending_loans.php?member_id=<?= $loan['member_id'] ?>" class="text-yellow change_request">
							<?= number_format($loan['unpaid_loan']) ?><i class="fa fa-eye"></i>
						</a>
					</td>
					<td class="text-right">
						<a href="actions/savings.php?member_id=<?= $loan['member_id'] ?>" class="text-success change_request">
							<?= number_format($loan['saving_amount']) ?>
							<i class="fa fa-eye"></i>
						</a>
					</td>
					<td>
						<a href="actions/capital.php?member_id=<?= $loan['member_id'] ?>" class="text-primary change_request">
							<?= number_format($loan['capital_share']) ?>
							<i class="fa fa-eye"></i>
						</a>
					</td>
					<td>
						<a href="actions/approve_loan.php?loan_id=<?= $loan['id'] ?>" class="text-success change_request" title="Approve"><i class="fa fa-check"></i></a> |
						<a href="actions/reject_loan.php?loan_id=<?= $loan['id'] ?>" class="text-danger change_request" title="Reject"><i class="fa fa-minus-circle"></i></a>
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
	<div class="alert alert-info" >
		No Pending Loan request for Loan Committee!
	</div>
	<?php
}
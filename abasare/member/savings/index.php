<?php
require_once "../../lib/db_function.php";

$savings = returnAllData($db, "SELECT 	a.id,
										a.member_id,
										a.amount,
                                        a.type,
                                        a.status,
                                        a.comment,
                                        a.created_at
										FROM bank_slip_requests AS a
										WHERE a.member_id = ?
										AND a.type='savings'
										ORDER BY a.id DESC
										", [$_SESSION['user']['member_acc']]);
                                        // $GET['year']);

if(count($savings) > 0){
	?>
	<table class="table table-bordered table-hover" id="savings_table">
		<thead>
			<tr>
				<th>#</th>
				<th>Contribution Month</th>
				<th>Amount</th>
				<th>Status</th>
				<th>Comment</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$counter = 1;
			foreach($savings AS $saving){
				?>
				<tr>
					<td><?= $counter++ ?></td>
					<td><?= (new DateTime($saving['created_at']))->format('F Y') ?></td>
					<td class="text-right"><?= number_format($saving['amount']) ?> RWF</td>
					<td><?= $saving['status']?></td>
					<td><?= ($saving['status'] == 'Accepted' ? 'none' : $saving['comment'])?></td>

				</tr>
				<?php
			}
			?>
		</tbody>
	</table>

	<script type="text/javascript">
		$('#savings_table').DataTable();
	</script>
	<?php
} else {
	?>
	<div class="alert alert-info">No Saving is being recorded is <?= $_GET['year'] ?></div>
	<?php
}
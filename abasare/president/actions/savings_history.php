<?php
require_once "../../lib/db_function.php";

$savings = returnAllData($db, "SELECT 	a.sav_amount,
										a.month,
										a.year,
										a.fine,
										a.done_at
										FROM saving AS a
										WHERE a.member_id = ? AND a.year = ?
										ORDER BY a.year ASC, a.month ASC
										", [$_GET['member_id'], $_GET['year']]);
if(count($savings) > 0){
	?>
	<table class="table table-bordered table-hover" id="savings_table">
		<thead>
			<tr>
				<th>Date</th>
				<th>Month</th>
				<th>Amount</th>
				<th>Fine</th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach($savings AS $saving){
				?>
				<tr>
					<td><?= $saving['done_at'] ?></td>
					<td><?= $saving['year'] ?>-<?= ($saving['month'] < 10?"0":"").$saving['month'] ?></td>
					<td><?= number_format($saving['sav_amount']) ?></td>
					<td><?= number_format($saving['fine']) ?></td>
				</tr>
				<?php
			}
			?>
		</tbody>
	</table>
	<script type="text/javascript">
		$("#savings_table").DataTable({
			'ordering': false
		});
	</script>
	<?php
} else {
	?>
	<div class="alert alert-info">
		No Savings Found in <?= $_GET['year'] ?>
	</div>
	<?php
}

<?php
require_once "../../lib/db_function.php";

$savings = returnAllData($db, "SELECT 	a.id,
										a.m_id,
										a.amount,
										a.month,
										a.year,
										a.confirmed_date
										FROM sacial_saving AS a
										WHERE a.m_id = ?
										AND a.year = ?
										ORDER BY a.year DESC, a.month DESC
										", [$_SESSION['user']['member_acc'], $_GET['year']]);

if(count($savings) > 0){
	?>
	<table class="table table-bordered table-hover" id="savings_table">
		<thead>
			<tr>
				<th>#</th>
				<th>Contribution Month</th>
				<th>Amount</th>
				<th>Confirmed</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$counter = 1;
			foreach($savings AS $saving){
				?>
				<tr>
					<td><?= $counter++ ?></td>
					<td><?= (new DateTime($saving['year']."-".($saving['month']<10?"0":"").$saving['month']."-01"))->format('F Y') ?></td>
					<td class="text-right"><?= number_format($saving['amount']) ?> RWF</td>
					<td><?= !is_null($saving['confirmed_date'])?(new \DateTime($saving['confirmed_date']))->format('Y-m-d H:i:s'):"" ?></td>
					<td></td>
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
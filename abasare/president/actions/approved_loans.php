<?php
require_once "../../lib/db_function.php";

$dates = explode(" - ", $_GET['date']);
$start_date = (new \DateTime())->format("Y-m-01");
$end_date = (new \DateTime())->format("Y-m-d");
if(!empty($dates[0])){
	$start_date = $dates[0];
}
if(!empty($dates[1])){
	$end_date = $dates[1];
}

$loans = returnAllData($db, $sql = "SELECT a.id,
											a.loan_date,
											a.status,
											a.loan_amount,
											CONCAT(COALESCE(b.fname,''), ' ', COALESCE(b.lname,'')) AS member_name,
											c.lname AS loan_name,
											a.member_id,
											a.president_date
											FROM member_loans AS a
											INNER JOIN member as b
											ON a.member_id = b.id
											INNER JOIN loan_type AS c
											ON a.loan_id = c.id
											WHERE a.president_status = ?
											AND a.committee_status = ?
											AND a.president_date BETWEEN ? AND ?
											", [1, 1, $start_date, $end_date]);
// var_dump($loans);
// echo $sql;
if(count($loans) > 0){
	?>
	<table class="table table-bordered table-hover" id="loans_table">
		<thead>
			<tr>
				<th>Date</th>
				<th>Approved at</th>
				<th>Member</th>
				<th>Type</th>
				<th>Amount</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$count=1;
			foreach($loans AS $loan){
				?>
				<tr>
					<td><?= $loan['loan_date'] ?></td>
					<td><?= $loan['president_date'] ?></td>
					<td><?= $loan['member_name'] ?> <a href="/member/loans/<?= array_key_exists($loan['loan_type_id'], $contract_files)?$contract_files[$loan['loan_type_id']]:"contract.php" ?>?loan_id=<?= $loan['id'] ?>" target="_blank" class="badge bg-green" title="Download Contract"> <i class="fa fa-download"></i> </a></td>
					<td><?= $loan['loan_name'] ?></td>
					<td class="text-right"><?= number_format($loan['loan_amount']) ?></td>
				</tr>
				<?php
			}
			?>
		</tbody>
	</table>

	<div class="row">
		<div class="col-sm-12">
			<a href="actions/print_approved_loans.php?date=<?= $_GET['date'] ?>" class="btn btn-block btn-danger" target="_blank" ><i class="fa fa-file-pdf-o"></i> Download PDF</a>
		</div>
	</div>

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
	<div class="alert alert-danger" >
		No Approved loans in selected time range of <?= $_GET['date'] ?>
	</div>
	<?php
}
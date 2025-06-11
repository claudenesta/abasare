<?php
require_once "../../lib/db_function.php";

$requests = returnAllData($db, "SELECT a.id,
										a.type, 
										a.ref_number, 
										a.amount, 
										a.data, 
										a.paid_at,
										b.fname,
										b.lname,
										b.company 
										FROM bank_slip_requests AS a
										INNER JOIN member AS b
										ON a.member_id = b.id
										WHERE a.status IN(?) AND type = ?", ["Open", "social savings"]);

if(count($requests) > 0){
	?>
	<div class="row">
		<div class="col-sm-12">
			<table class="table table-stripped" id="bankslip_table">
				<thead>
					<tr>
						<th>Date</th>
						<th>Type</th>
						<th>Amount</th>
						<th>Reference</th>
						<th>Member</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach($requests AS $request){
						?>
						<tr>
							<td><?= $request['paid_at'] ?></td>
							<td><?= $request['type'] ?></td>
							<td class="text-right"><?= number_format($request['amount']) ?> RWF</td>
							<td><?= $request['ref_number'] ?></td>
							<td><?= $request['fname'] ?> <?= $request['lname'] ?></td>
							<td>
								<a class="btn btn-sm btn-primary change_request" href="bankslip/accept.php?request_id=<?= $request['id'] ?>"><i class="fa fa-check"></i> Accept</a>
								<a class="btn btn-sm btn-danger change_request" href="bankslip/reject.php?request_id=<?= $request['id'] ?>"><i class="fa fa-trash"></i> Reject</a>
							</td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>
		</div>
	</div>

	<script type="text/javascript">
		$('#bankslip_table').DataTable();

		$('#bankslip_table').on('click', '.change_request', function(e){
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
		        link.removeAttr("disabled");
		        $("#modal_member").modal("show");
		    });
		});
	</script>
	<?php
} else {
	?>
	<div class="alert alert-info">
		No Pending payment request
	</div>
	<?php
}

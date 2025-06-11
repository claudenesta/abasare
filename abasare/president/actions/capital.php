<?php
require_once "../../lib/db_function.php";
$member = first($db, "SELECT fname, lname, Account_balance FROM member WHERE id =?" , [$_GET['member_id']]);
$shares = returnAllData($db, "SELECT 	a.id,
																			a.amount,
																			a.date
																			FROM capital_share AS a
																			WHERE a.member_id =?
																			ORDER BY a.date DESC
																			", [$_GET['member_id']]);
?>
	<div class="modal-header bg-primary ">
        <h4 class="modal-title">
          <span style="color:white">Preview <?= $member['fname'] ?> <?= $member['lname'] ?>'s Capital <i class="fa fa-money"></i></span>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </h4>
    </div>
    <div class="modal-body">
        <div class="card card-info">
          	<div class="card-body">
          		<?php
          		if(count($shares) > 0){
          			?>
          			<table class="table table-bordered table-hover" id="pending_loans_table">
          				<thead>
          					<tr>
          						<th>Date</th>
          						<th>Amount</th>
          					</tr>
          				</thead>
          				<tbody>
          					<?php
          					$total_shares = 0;
          					foreach ($shares as $share) {
          						$total_shares += $share['amount'];
          						?>
          						<tr>
          							<td><?= $share['date'] ?></td>
          							<td class="text-right"><?= number_format($share['amount']) ?></td>
          						</tr>
          						<?php
          					}
          					?>
          				</tbody>
          			</table>
          			<div class="form-group row" style="margin-top: 2px;">
          				<div class="col-sm-12 bg-primary text-center">
          					Total Shares: <?= number_format($total_shares) ?> RWF
          				</div>
          			</div>
          			<script type="text/javascript">
          				$("#pending_loans_table").DataTable({
          					'ordering': false
          				});
          			</script>
          			<?php
          		} else {
          			?>
          			<div class="alert alert-info">
          				No Pending Loans can be found for the selected member
          			</div>
          			<?php
          		}
          		?>
          	</div>
        </div>
    </div>
    <div class="modal-footer justify-content-between">
        <button class="btn btn-xs btn-flat btn-primary" data-dismiss="modal" type="button">Close</button>
    </div>
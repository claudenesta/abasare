<?php
require_once "../../lib/db_function.php";
$member = first($db, "SELECT fname, lname, Account_balance FROM member WHERE id =?" , [$_GET['member_id']]);
$loans = returnAllData($db, "SELECT 	a.id AS loan_id,
										a.loan_amount,
										b.paid_amount,
										a.status,
										a.loan_date,
										c.lname AS loan_name
										FROM member_loans AS a
										LEFT JOIN (
											SELECT 	a.borrower_loan_id,
													SUM(amount) AS paid_amount
													FROM lend_payments AS a
													INNER JOIN member_loans AS b
													ON a.borrower_loan_id = b.id
													WHERE b.status = ? AND b.member_id =? AND a.status = ?
													GROUP BY a.borrower_loan_id
											) AS b
										ON a.id = b.borrower_loan_id
										INNER JOIN loan_type AS c
										ON a.loan_id = c.id
										WHERE a.status = ? AND a.member_id =?
										ORDER BY a.loan_date DESC
										", ['ACTIVE', $_GET['member_id'], "PAID", "ACTIVE", $_GET['member_id']]);
?>
	<div class="modal-header bg-yellow ">
        <h4 class="modal-title">
          <span style="color:white">Preview <?= $member['fname'] ?> <?= $member['lname'] ?>'s unpaid Loans <i class="fa fa-money"></i></span>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </h4>
    </div>
    <div class="modal-body">
        <div class="card card-info">
          	<div class="card-body">
          		<?php
          		if(count($loans) > 0){
          			?>
          			<table class="table table-bordered table-hover" id="pending_loans_table">
          				<thead>
          					<tr>
          						<th>Loan</th>
          						<th>Date</th>
          						<th>Amount</th>
          						<th>Paid</th>
          						<th>Balance</th>
          					</tr>
          				</thead>
          				<tbody>
          					<?php
          					$total_loan = 0;
          					$total_paid = 0;
          					foreach ($loans as $loan) {
          						$total_loan += $loan['loan_amount'];
          						$total_paid += $loan['paid_amount'];
          						?>
          						<tr>
          							<td><?= $loan['loan_name'] ?></td>
          							<td><?= $loan['loan_date'] ?></td>
          							<td class="text-right"><?= number_format($loan['loan_amount']) ?></td>
          							<td class="text-right"><?= number_format($loan['paid_amount']) ?></td>
          							<td class="text-right"><?= number_format($loan['loan_amount'] - $loan['paid_amount']) ?></td>
          						</tr>
          						<?php
          					}
          					?>
          				</tbody>
          			</table>
          			<div class="form-group row" style="margin-top: 2px;">
          				<div class="col-sm-4 bg-yellow text-center">
          					Loan: <?= number_format($total_loan) ?> RWF
          				</div>
          				<div class="col-sm-4 bg-green text-center">
          					Paid: <?= number_format($total_paid) ?> RWF
          				</div>
          				<div class="col-sm-4 bg-red text-center">
          					Balance <?= number_format($total_loan - $total_paid) ?> RWF
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
        <button class="btn btn-xs btn-flat btn-warning" data-dismiss="modal" type="submit" name="update_signature">Close</button>
    </div>
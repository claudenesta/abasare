<?php
require_once "../../lib/db_function.php";

//Get the list of loans applied before order by the order date

$pendings = returnAllData($db, "SELECT 	a.*,
										b.id AS overdue_id,
										COALESCE(b.month, a.salary_month) AS month,
										COALESCE(b.year, a.salary_year) AS year,
										COALESCE(b.payment_overdue, a.payment_sched) AS payment_overdue
										FROM (
											SELECT 	a.id,
													a.payment_sched,
													a.payment_number,
													IF(a.amount > 0, a.amount, b.loan_amount_term) AS amount,
													a.status,
													a.rdate,
													a.overdue_fine,
													a.comment,
													c.lname AS loan_name,
													c.is_emergeny,
													d.next_payment_id,
													YEAR(a.payment_sched) AS salary_year,
													MONTH(a.payment_sched) AS salary_month,
													b.loan_date
													FROM lend_payments AS a
													INNER JOIN member_loans AS b
													ON a.borrower_loan_id = b.id AND b.status = ?
													INNER JOIN loan_type AS c
													ON b.loan_id = c.id
													LEFT JOIN member_loans AS d
													ON a.payment_number = d.next_payment_id
													WHERE a.borrower_id = ?
													AND a.status IN(?,?,?)
													ORDER BY a.payment_sched
										) AS a
										LEFT JOIN overdue_settings AS b
										ON a.salary_year = b.year AND a.salary_month = b.month
										ORDER BY a.payment_sched ASC
										", ["ACTIVE", $_SESSION['acc'], "UNPAID", "PENDING", "Rejected"]);

if(count($pendings) > 0){
	?>
	<table class="table table-bordered table-hover" id="loans_table">
		<thead>
			<tr>
				<th>#</th>
				<th>Date</th>
				<th>Dead Line</th>
				<th>Amount</th>
				<th>Loan</th>
				<th>Status</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$count = 1;
			$now = new \DateTimeImmutable();
			foreach($pendings AS $pending){
				
				$color = "success";
				$delays = 0;
				$fine_rate = 0;
				$overdue_check = null;
				$delays_days = 0;
				$checker = null;
				$loan_date = new \DateTime();
				if($pending['payment_sched'] <= $pending['loan_date']){
					$converter = new \DateTime($pending['payment_sched']);
					$converter->modify("+1 month");
					$pending['payment_sched'] = $converter->format('Y-m-d');
				}

				if($pending['is_emergeny']){
					$overdue_check = new \DateTimeImmutable($pending['payment_sched']);
					if($overdue_check->getTimestamp() < $now->getTimestamp()){
						$checker = $overdue_check->diff($now);
						if($checker->format("%y") > 0){
							$delays += ($checker->format("%y")*12);
						}
						$delays += $checker->format("%m");
						if($checker->format("%d") > 0) {
							$delays += 1;
						}
						// $delays = $checker->m;
						$delays_days = $checker->days;
						$color = "danger";
						$fine_rate = 5/100;
					}
				} else {
					if(!is_null($pending['payment_overdue'])){
						$overdue_check = new \DateTimeImmutable($pending['payment_overdue']);
						if($overdue_check->getTimestamp() < $now->getTimestamp()){
							$checker = $overdue_check->diff($now);
							if($checker->format("%y") > 0){
								$delays += ($checker->format("%y")*12);
							}
							$delays += $checker->format("%m");
							if($checker->format("%d") > 0) {
								$delays += 1;
							}
							$delays_days = $checker->days;
							$color = "danger";
							$fine_rate = 2/100;
						}
					}
				}

				$fines = 0;
				
				if($delays > 0){
					$fines = $delays * $pending['amount'] * $fine_rate;
				}
				
				// $payment_sched = new \DateTime($pending['payment_sched']);
				?>
				<tr class="bg-">
					<td><?= $count++ ?></td>
					<td><?= $pending['payment_sched'] ?></td>
					<td>
						<?php
						if(!is_null($overdue_check)){
							echo $overdue_check->format("Y-m-d");
						} else {
							echo $pending['payment_sched'];
						}
						?>
					</td>
					<td class="text-right"><?= number_format($pending['amount']) ?></td>
					<td><?= $pending['loan_name'] ?></td>
					<td  title="<?= $pending['comment'] ?>" class="text-<?= $pending['status'] =="Rejected"?"danger":"success" ?>"><?= $pending['status'] ?></td>
					<td>
						<?php
						// echo "m:". $delays." d:".$delays_days." sch:".$overdue_check->getTimestamp()." now:".$now->getTimestamp();
						if(in_array($pending['status'], ["UNPAID", "Rejected"]) && !is_null($pending['next_payment_id']) && ($pending['is_emergeny'] == 1 || ($pending['is_emergeny'] != 1 && !is_null($pending['payment_overdue']) )  ) ){
							?>
							<a class="btn btn-sm btn-<?= $color ?> change_request" href="loans/request_payment.php?payment_id=<?= $pending['id'] ?>&delays=<?= $delays ?>&fines=<?= $fines ?>&days=<?= !is_null($checker)?$checker->format("%a"):"0" ?>"><i class="fa fa-check"></i> Pay <?= $fines > 0?(" with ".number_format($fines)." RWF "):"" ?></a>
							<?php
						} else {
							?>
							<button class="btn btn-sm btn-flat btn-default disabled"><?= $delays ?> Month<?= $delays > 1?"s":"" ?> delayed</button>
							<?php
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
		        link.removeAttr("disabled");
		        $("#modal_member").modal("show");
		    });
		});
	</script>
	<?php
} else {
	?>
	<div class="alert alert-info">No Pending Payment could be found in the system</div>
	<?php
}
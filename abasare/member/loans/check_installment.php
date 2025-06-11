<?php
header("Content-Type:application/json");
require_once "../../lib/db_function.php";

//get list of payments required
$limit = (int) $_GET['installments'];
$affected_lend_payments = returnAllData($db, "SELECT 	a.*,
														COALESCE(b.payment_overdue, a.payment_sched) AS payment_overdue
														FROM (
															SELECT   	a.id,
																		a.payment_sched,
																		a.payment_number,
																		YEAR(a.payment_sched) AS salary_year,
																		MONTH(a.payment_sched) AS salary_month,
																		c.is_emergeny,
																		b.loan_date,
																		b.loan_amount_term
																		FROM lend_payments AS a
																		INNER JOIN member_loans AS b
																		ON a.borrower_loan_id = b.id
																		INNER JOIN loan_type AS c
																		ON b.loan_id = c.id
																		WHERE a.borrower_loan_id = ?
																		AND a.status = ?
																		ORDER BY a.id ASC
																		LIMIT 0, {$limit}
														) AS a
														LEFT JOIN overdue_settings AS b
														ON a.salary_year = b.year AND a.salary_month = b.month
														", [$_GET['loan_id'], 'UNPAID']);
// echo $sql;
// var_dump($affected_lend_payments);
// echo json_encode(['status' => false, "message" => "Select Loan you want.", "fines" => 0, "data" => $_POST]);
if(count($affected_lend_payments) > 0){
	$remainder = 0;
	$total_required = 0;
	$total_fines = 0;
	$emergency_found = false;
	$required_amount = 0;
	?>
	<table class="table table-bordered table-hover table-stripped">
		<thead>
			<tr>
				<th>Month</th>
				<th>Amount</th>
				<th>Fine</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$now = new \DateTimeImmutable();
			foreach($affected_lend_payments AS $installement){
				$month = new \DateTime($installement['salary_year'].'-'.($installement['salary_month']<10?'0':'').$installement['salary_month']);

				$fine = 0;
				$delays = 0;
				$fine_rate = 0;
				if($installement['payment_sched'] <= $installement['loan_date']){
					$converter = new \DateTime($installement['payment_sched']);
					$converter->modify("+1 month");
					$installement['payment_sched'] = $converter->format('Y-m-d');
				}
				if($installement['is_emergeny']){
					$emergency_found = true;
					$required_amount = $installement['loan_amount_term'];
					$overdue_check = new \DateTimeImmutable($installement['payment_sched']);
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
					if(!is_null($installement['payment_overdue'])){
						$overdue_check = new \DateTimeImmutable($installement['payment_overdue']);
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
				if($delays > 0){
					$fine = $delays * $installement['loan_amount_term'] * $fine_rate;
					$total_fines += $fine;
				}

				$total_required += $installement['loan_amount_term'];
				?>
				<tr>
					<td>
						<input type="hidden" name="payment_id_number[]" value="<?= $installement['payment_number'] ?>">
						<input type="hidden" name="payment_id_info[]" value="<?= $installement['id'] ?>"> 
						<?= $month->format('F Y') ?>
					</td>
					<td>
						<?= number_format($installement['loan_amount_term']) ?>
						<input type="hidden" name="payment_amount_info[]" value="<?= $installement['loan_amount_term'] ?>">
					</td>
					<td>
						<?= number_format($fine) ?>
						<input type="hidden" name="payment_fine_info[]" value="<?= $fine ?>">
					</td>
				</tr>
				<?php
			}
			?>
		</tbody>
	</table>
	<?php
	$remainder = $_GET['amount'] - $total_required;
	$total_fines = RoundUp($total_fines);
	?>
	<input type="hidden" name="additional_payments" value="<?= $remainder ?>">
	<?php
	if($remainder > 0){
		?>

		<button class="btn btn-block btn-success">Remainder: <?= number_format($remainder) ?></button>
		<?php
	}
	if($emergency_found && $limit != 1){
		?>
		<script type="text/javascript">
			alert("For Emergency loans you have to pay exactly the given calculated amount!!\nPlease pay only <?= number_format($required_amount) ?> RWF");
			$("#amount").val("<?= $required_amount ?>");
			setTimeout(function(){
				$("#amount").trigger("keyup");
			},100);
		</script>
		<?php
	}
	if($total_fines > 0){
		?>
		<script type="text/javascript">
			$("#fine_amount").val("<?= $total_fines; ?>");
			$("#delay_warning").html("<?= number_format($total_fines); ?> RWF delay fines");
		</script>
		<?php
	}
} else {
	$remainder = $_GET['amount'];
	?>
	<table class="table table-bordered table-hover table-stripped">
		<thead>
			<tr>
				<th>Month</th>
				<th>Amount</th>
				<th>Fine</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="3">No Coverable installment found!</td>
			</tr>
		</tbody>
	</table>
	<input type="hidden" name="payment_id_number[]" >
	<input type="hidden" name="additional_payments" value="<?= $remainder ?>">
	<button class="btn btn-block btn-success">Remainder: <?= number_format($remainder) ?></button>
	<?php
}
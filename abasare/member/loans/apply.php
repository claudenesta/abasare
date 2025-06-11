<?php
require_once "../../lib/db_function.php";

$signatories = returnAllData($db, "SELECT id, name FROM users WHERE id != ? AND signature IS NOT NULL AND status=?", [$_SESSION['id'], 1]);

$savings = returnSingleField($db, "SELECT 	SUM(a.amount) AS saving_amount 
                                          	FROM (
	                                            SELECT  a.sav_amount AS amount,
		                                                a.year, a.month,
		                                                CONCAT(a.year, '-', IF(a.month < 10, '0',''), a.month, '-01') AS contribution_month
		                                                FROM saving AS a
		                                                WHERE a.member_id = ?
		                                                ORDER BY id DESC
                                          	) AS a
                                          	WHERE a.contribution_month <= ?
                                          	", "saving_amount", [$_SESSION['user']['member_acc'], (new \DateTime())->format('Y-m-01')]);

$loan_limit = $savings*2.5;
$all_loan_types = returnAllData($db, "SELECT id, lname, interest, terms, late_fee, is_top_up, is_emergeny, percentage_before_top_op, COALESCE(special_limit, {$loan_limit}) AS special_limit FROM loan_type ORDER BY lname ASC");
$today = new \DateTime();
// Get the last month required for the saving which is overdone
$saving_overdone = first($db, "SELECT id, month, year, saving_overdue FROM overdue_settings WHERE saving_overdue < ? ORDER BY saving_overdue DESC LIMIT 0, 1", [$today->format('Y-m-d')]);

$saving_error = false;
$last_saving = null;
if($saving_overdone){
	//Check the last saving of the member conpared to saving overdue

	//Didi the member paid the last saving configure to be overdone
	$last_saving = first($db, "SELECT id, member_id, sav_amount, month, year, done_at FROM saving WHERE month=? AND year=?", [$saving_overdone['month'],$saving_overdone['year']]);

	if(!$last_saving){
		$saving_error = true;
	} else if( (new \DateTime($last_saving['done_at']) )->getTimestamp() > ( new \DateTime($saving_overdone['saving_overdue']) )->getTimestamp() ){
		$saving_error = true;
	}
}
//check if the member had any other active loan
$loans = returnSingleField($db, "SELECT COUNT(a.id) AS open_loans
										FROM member_loans AS a
										WHERE a.status = ?
										AND a.member_id = ?
										", "open_loans", ['OPEN', $_SESSION['user']['member_acc'] ]);
if($loans > 0){
	?>
	<div class="modal-header bg-yellow ">
        <h4 class="modal-title">
          <span style="color:white">Error!</span>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </h4>
    </div>
    <div class="modal-body">
        <div class="card card-info">
          	<div class="card-body">
          		<div class="alert alert-warning">
          			You have <?= $loans ?> which <?= $loans > 1?"are":"is"; ?> still under processing, Please wait for it to complete before retrying
          		</div>
          	</div>
      	</div>
  	</div>
	<?php
	return;
}

//Check if any active loan and remove ordinary loan in the list
$other_loans_not_emergency = returnSingleField($db, $my_query = "SELECT COUNT(a.id) AS open_loans
															FROM member_loans AS a
															WHERE a.status = ?
															AND a.member_id = ?
															AND a.loan_id IN (SELECT id FROM loan_type WHERE is_emergeny = ?)
															", "open_loans", $cnd_data = ['ACTIVE', $_SESSION['user']['member_acc'],  0]);

//check if the active can accept top
$active_loans = returnAllData($db, "SELECT 	a.id,
											COALESCE(b.percentage_before_top_op, 100) AS minimum,
											a.loan_amount,
											(a.loan_amount * COALESCE(b.percentage_before_top_op, 100) / 100) AS should_be_paid,
											c.paid_amount,
											c.paid_amount * 100 / a.loan_amount AS payment_percentage,
											IF( (c.paid_amount * 100 / a.loan_amount) >= COALESCE(b.percentage_before_top_op, 100), 1, 0 ) AS can_be_toped_up,
											b.lname AS loan_name,
											d.loan_state
											FROM member_loans AS a
											INNER JOIN loan_type AS b
											ON a.loan_id = b.id
											LEFT JOIN (
												SELECT 	a.id AS member_loan_id,
														SUM(b.amount) AS paid_amount
														FROM member_loans AS a
														INNER JOIN lend_payments AS b
														ON a.id = b.borrower_loan_id AND b.status = ?
														WHERE a.status = ?
														AND a.member_id = ?
														AND a.loan_id NOT IN (SELECT id FROM loan_type WHERE is_emergeny = ?)
														GROUP BY a.id 
												) AS c
											ON a.id = c.member_loan_id
											LEFT JOIN (
												SELECT 	a.id AS member_loan_id,
														'overdue' AS loan_state
														FROM member_loans AS a
														INNER JOIN lend_payments AS b
														ON a.id = b.borrower_loan_id
														WHERE a.status = ?
														AND a.member_id = ?
														AND a.loan_id NOT IN (SELECT id FROM loan_type WHERE is_emergeny = ?)
														AND b.payment_sched < ?
														AND b.status = ?
														GROUP BY a.id
												) AS d
											ON a.id = d.member_loan_id
											WHERE a.status = ?
											AND a.member_id = ?
											AND a.loan_id NOT IN (SELECT id FROM loan_type WHERE is_emergeny = ?)
											", ['PAID', 'ACTIVE',  $_SESSION['user']['member_acc'], 1, 'ACTIVE', $_SESSION['user']['member_acc'], 1, (new \DateTime())->format('Y-m-d'), 'UNPAID','ACTIVE', $_SESSION['user']['member_acc'], 1]);
//Check if the the member has an emergency loan with over due
$emergency = returnSingleField($db, "SELECT COUNT(a.id) AS overdue_emergency
											FROM member_loans AS a
											INNER JOIN lend_payments AS b
											ON a.id = b.borrower_loan_id
											WHERE a.status = ?
											AND a.loan_id IN (SELECT id FROM loan_type WHERE is_emergeny = ?)
											AND b.status = ?
											AND b.payment_sched < ?
											AND a.member_id = ?
											", "overdue_emergency", ["ACTIVE", 1, 'UNPAID', (new \DateTime())->format('Y-m-d'), $_SESSION['user']['member_acc']]);
//check if topup loans can display
$topable_loans = [];
$payment_error = false;
$emergency_error = false;
if(count($active_loans) > 0){
	foreach($active_loans AS $active_loan){
		if($emergency > 0){
			$topable_loans = [];
			$payment_error = true;
			break;
		}
		if($active_loan['loan_state'] == 'overdue'){
			$topable_loans = [];
			$payment_error = true;
			break;
		}
		if($active_loan['can_be_toped_up'] == 1){
			$topable_loans[] = $active_loan;
		}
	}
}

if($emergency_error === false && $emergency > 0){
	$emergency_error = true;
}

//check if any overdue payment


$loan_condition = [
	'saving_error' => [
		2, // Emmergancy Loan
		4, // Ordinary loan(24)
		6, // ordinary loan,
		9, // Ordinary Loan(36)
	],
	'had_other_loan' => [
		4, // Ordinary loan(24),
		6, // ordinary loan
		9, // Ordinary Loan(36)
	],
	'top_up' => [
		1, // TOP-UP(24 Months),
		3, // Top-UP LOAN
		10, // (TOP-UP-36 Months)
	],
	'emergency' => [
		1, // TOP-UP(24 Months),
		2, // Emmergancy Loan
		3, // Top-UP LOAN
		4, // Ordinary loan(24),
		6, // ordinary loan
		9, // Ordinary Loan(36)
		10, // (TOP-UP-36 Months)
	],
];

$loan_types = [];
$errors = [];
foreach($all_loan_types AS $loan_type){
	//Here make sure to filter and check if the member is eligible for this loan type
	if($emergency_error && in_array($loan_type['id'], $loan_condition['emergency'])){
		$errors[] = "Overdue Emergency Loan";
		continue;
	}
	//if the mameber has a saving error remove any kind of loan which has that limitation
	if($saving_error && in_array($loan_type['id'], $loan_condition['saving_error'])){
		//$errors[] = "Overdue Savings";
		//continue;
	}

	if($other_loans_not_emergency > 0 && in_array($loan_type['id'], $loan_condition['had_other_loan'])){
		$errors[] = "Hold unpaid loans";
		continue;
	}

	if(count($topable_loans) == 0 && in_array($loan_type['id'], $loan_condition['top_up'])){
		$errors[] = "No loan to topup";
		continue;
	}
	$loan_types[] = $loan_type;
}
$errors = array_unique($errors);
?>
<form action="loans/apply_for_loan.php" method="POST" id="form_apply_loan" >
  	<input type="hidden" name="member_id" value="<?= $_GET['member_id'] ?>">
  	<input type="hidden" name="loan_limit" id="loan_limit" value="<?= $loan_limit ?>">
  	<input type="hidden" name="top_up_selected" value="0" id="top_up_selected" >
  	<input type="hidden" name="emergency_selected" value="0" id="emergency_selected" >
  	<input type="hidden" name="toped_up_amount" value="" id="toped_up_amount" >
  	<input type="hidden" value="0" id="withdrawable_amount" >
    <div class="modal-header bg-yellow ">
        <h4 class="modal-title">
          <span style="color:white">Apply For Loan <?= $_SESSION['user']['name'] ?>'s Limit <span id="loan_limit_info"><?= number_format($loan_limit) ?></span> RWF</span>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </h4>
    </div>
    <div class="modal-body">
        <div class="card card-info">
          <div class="card-body">
          	<?php
          	if(count($errors) > 0){
          		?>
          		<div class="form-group row">
          			<div class="col-sm-12 ">
          				<div class="bg-red disabled color-palette text-center">
          					<i class="fa fa-exclamation-triangle fa-4x"></i><br />
          					Some Loans are removed Due to following reason<?= count($errors)>1?"s":"" ?>

          					<ol type="1" class="text-left">
  								<li>
          							<?= implode("</li>
  									<li>", $errors) ?>
          						</li>
							</ol>
          				</div>
          			</div>
          		</div>
          		<?php
          	}
          	?>
            <div class="form-group row">
              <label for="loan_type_id" class="col-sm-4 col-form-label">Loan: </label>
              <div class="col-sm-8">
                <select class="form-control select2" id="loan_type_id" name="loan_type_id" style="width: 100%;">
                    <option value=""></option>
                    <?php
                    foreach($loan_types AS $loan_type){
                        ?>
                        <option data-interest="<?= $loan_type['interest'] ?>" data-period="<?= $loan_type['terms'] ?>" data-topup="<?= $loan_type['is_top_up'] ?>" data-special_limit="<?= $loan_type['special_limit'] ?>" data-emergency="<?= $loan_type['is_emergeny'] ?>" value="<?= $loan_type['id'] ?>"><?= $loan_type['lname'] ?></option>
                        <?php
                    }
                    ?>
                </select>
              </div>
            </div>
            <?php
            if(count($topable_loans) > 0){
            	?>
            	<div class="form-group row" id="topable_up_loan">
            		<label for="toped_up_loan" class="col-sm-4 col-form-label">which Loan: </label>
            		<div class="col-sm-8">
            			<select class="form-control select2" id="toped_up_loan" name="toped_up_loan" style="width: 100%;">
            				<option value=""></option>
            				<?php
            				foreach($topable_loans AS $my_loan){
            					?>
            					<option data-unpaid="<?= $my_loan['loan_amount'] - $my_loan['paid_amount'] ?>" value="<?= $my_loan['id'] ?>"><?= $my_loan['loan_name'] ?>(<?= number_format($my_loan['loan_amount'] - $my_loan['paid_amount']) ?> RWF)</option>
            					<?php
            				}
            				?>
            			</select>
            		</div>
            	</div>
            	<?php
            }
            ?>
			<div class="form-group row">
			    <label for="principal" class="col-sm-4 col-form-label">
			    	Amount (RWF):
				</label>
				<div class="col-sm-8">
			    	<input type="text" name="principal" onkeyup="calculate()" id="principal" class="form-control">
				</div>
			</div>
			<div class="form-group row signatory_container">
				<label for="signatory_1" class="col-sm-4 col-form-label">First Signatory: </label>
				<div class="col-sm-8">
			      	<select name="signatory_1" class="form-control select2 form-control-sm" id="signatory_1" placeholer="First Signatory" style="width: 100%;">
				        <option value=""></option>
				        <?php
				        foreach($signatories AS $signatory){
				        	?>
				        	<option value="<?= $signatory['id'] ?>"><?= $signatory['name'] ?></option>
				        	<?php
				        }
				        ?>
			      	</select>
			    </div>
			</div>
			<div class="form-group row signatory_container">
				<label for="signatory_2" class="col-sm-4 col-form-label">Second Signatory: </label>
				<div class="col-sm-8">
			      	<select name="signatory_2" class="form-control select2 form-control-sm" id="signatory_2" placeholer="First Signatory" style="width: 100%;">
				        <option value=""></option>
				        <?php
				        foreach($signatories AS $signatory){
				        	?>
				        	<option value="<?= $signatory['id'] ?>"><?= $signatory['name'] ?></option>
				        	<?php
				        }
				        ?>
			      	</select>
			    </div>
			</div>
			<div class="form-group row">
				<div class="col-sm-12">
					<div class="btn-group text-center">
		                <button id="interest_rate" class="btn btn-success btn-sm">Interest Rate</button>
		                <button id="payment_period" class="btn btn-warning btn-sm">Period</button>
		                <button id="monthly_payment" class="btn btn-primary btn-sm">Installement</button>
		                <button id="total_interest" class="btn btn-info btn-sm">Interest</button>
		            </div>
		        </div>
			</div>
			<div class="form-group row">
				<div class="col-sm-12 col-md-6">
					<button class="btn btn-block btn-flat btn-danger" type="button" id="covered">RWF 0</button>
				</div>
				<div class="col-sm-6  col-md-6">
					<button class="btn btn-block btn-flat btn-success" type="button" id="withdrawable">RWF 0</button>
				</div>
			</div>
          </div>
        </div>
    </div>
    <div class="modal-footer justify-content-between">
        <button class="btn btn-sm btn-flat btn-warning submit_button" id="submit_button" type="submit" name="update_signature">Apply</button>
    </div>
</form>

<script type="text/javascript">
	function calculate(){
		var selected = $("#loan_type_id").find(":selected");

		var interest_rate = selected.data("interest") * 1;
		var period = selected.data("period") * 1;

		$("#interest_rate").html("Interest Rate: " + selected.data("interest") + "%");
		$("#payment_period").html("Paid in  " + selected.data("period") + " month" + (selected.data("period") > 1?"s":"") );

		var amount = $("#principal").val()*1;

		var loan_limit = $("#loan_limit").val()*1;
		var button_manupulated = false;

		if(loan_limit < amount){
			$("#submit_button").attr("disabled", "disabled");
			button_manupulated = true;
		} else {
			$("#submit_button").removeAttr("disabled");
		}

		if(amount > 0 && interest_rate > 0 && period > 0){
			//Make sure to calculate 
			var monthly_payments = amount/period;

			$("#monthly_payment").html( "Pay " + 
				new Intl.NumberFormat("en-US", {
					style: "currency", 
					currency: "RWF"
				}).format(monthly_payments) + "/month"
			);

			var total_interest = amount * interest_rate / 100;

			$("#total_interest").html( "Total Interest is " + 
				new Intl.NumberFormat("en-US", {
					style: "currency", 
					currency: "RWF"
				}).format(total_interest)
			);

			var selected_toped_up = $("#toped_up_loan").find(":selected");
			var unpaid_toped_up = 0;
			if(selected_toped_up.data("unpaid") != undefined){
				unpaid_toped_up = selected_toped_up.data("unpaid") * 1;
			}

			if(unpaid_toped_up > 0){
				$("#covered").html("Covered: " + 
					new Intl.NumberFormat("en-US", {
						style: "currency",
						currency: 'RWF'
					}).format(unpaid_toped_up)
				);
				$("#covered").show();
			} else{
				$("#covered").hide();
			}
			var receivable_amount = amount - total_interest - unpaid_toped_up;

			$("#withdrawable").html("You will receive: " + 
				new Intl.NumberFormat("en-US", {
					style: "currency", 
					currency: "RWF"
				}).format(receivable_amount)
			);
			$("#withdrawable_amount").val(receivable_amount);

			if(receivable_amount <= 0){
				$("#submit_button").attr("disabled", "disabled");
			} else {
				if(!button_manupulated){
					$("#submit_button").removeAttr("disabled");
				}
			}
		}
	}
		$("#topable_up_loan").hide();
		$("#covered").hide();
		$("#loan_type_id").select2({
			placeholder: "Select Loan You Want"
		}).bind("change", function(){
			var my_selection = $("#loan_type_id").find(":selected");
			//check if the selected loan type is a topup and allow the selected of the loan to be toped up
			var is_topup = my_selection.data("topup");
			if(is_topup){
				$("#topable_up_loan").show();

			} else {
				$("#topable_up_loan").hide();
			}
			$("#top_up_selected").val(is_topup);

			var is_emergency = my_selection.data("emergency");
			if(is_emergency){
				$(".signatory_container").hide();
			} else {
				$(".signatory_container").show();
			}
			$("#emergency_selected").val(is_emergency);
			var loan_limit_data = my_selection.data("special_limit");
		  	$("#loan_limit").val(loan_limit_data);
			$('#loan_limit_info').html( new Intl.NumberFormat().format(
		    	loan_limit_data,
		  	) );
			//Here make sure to change information
			calculate();
		});

		$("#toped_up_loan").select2({
			placeholder: "Select loan to topup"
		}).bind("change", function(){
			var my_toped_up_selection = $("#toped_up_loan").find(":selected");
			$("#toped_up_amount").val(my_toped_up_selection.data("unpaid"));
			calculate();
		});

		$("#signatory_1").select2({
			placeholder: "Select First Signatory"
		});

		$("#signatory_2").select2({
			placeholder: "Select Second Signatory"
		});

$("#form_apply_loan").submit(function(e) {
    e.preventDefault();

    var amount = $("#principal").val() * 1;
    var loan_limit = $("#loan_limit").val() * 1;

    console.log("Amount:", amount);
    console.log("Loan Limit:", loan_limit);

    if (loan_limit < amount) {
        console.log("Loan amount exceeds limit.");
        return false;
    }

    var withdrawable_amount = $("#withdrawable_amount").val();
    console.log("Withdrawable Amount:", withdrawable_amount);

    if (withdrawable_amount <= 0) {
        console.log("Withdrawable amount is zero or negative.");
        return false;
    }

    var old_data = $("#submit_button").html();
    $("#submit_button").html('<i class="fas fa-sync fa-spin"></i> Saving...');
    $("#submit_button").attr("disabled", "disabled");

    $.ajax({
        type: $(this).attr("method"),
        url: $(this).attr("action"),
        data: $(this).serialize(),
        beforeSend: function() {
            console.log("Sending AJAX request...");
        },
        success: function(data) {
            console.log("AJAX Success:", data);
            if (data.status == true) {
                toastr.success(data.message);
                $("#modal_member").modal("hide");
            } else {
                toastr.warning(data.message || "An error occurred.");
            }
            $("#submit_button").html(old_data);
            $("#submit_button").removeAttr("disabled");
        },
        error: function(err) {
            console.error("AJAX Error:", err);
            $("#submit_button").html(old_data);
            $("#submit_button").removeAttr("disabled");
            toastr.error("Invalid Response");
        }
    });
});

</script>
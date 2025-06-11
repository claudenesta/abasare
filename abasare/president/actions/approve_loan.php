<?php
require_once "../../lib/db_function.php";

$loan = first($db, "SELECT  a.id,
                            a.loan_amount,
                            b.fname,
                            b.lname,
                            c.lname AS loan_name,
                            a.loan_date,
                            c.interest,
                            c.terms,
                            c.frequency,
                            c.late_fee,
                            a.member_id,
                            c.id AS loan_type_id,
                            d.total_recovered_amount
                            FROM member_loans AS a
                            INNER JOIN member AS b
                            ON a.member_id = b.id
                            INNER JOIN loan_type AS c
                            ON a.loan_id = c.id
                            LEFT JOIN topup_details AS d
                            ON a.id = d.loan_id
                            WHERE a.id = ?
                            ", [$_GET['loan_id']]);

$contributions = returnAllData($db, "SELECT * 
                      FROM (
                        SELECT  a.sav_amount AS amount,
                            a.year, a.month,
                            CONCAT(a.year, '-', IF(a.month < 10, '0',''), a.month, '-01') AS contribution_month
                            FROM saving AS a
                            WHERE a.member_id = ?
                            ORDER BY id DESC
                      ) AS a
                      WHERE a.contribution_month <= ?
                      ORDER BY contribution_month DESC
                      LIMIT 0,3
                      ", $condi = [$loan['member_id'], (new \DateTime($loan['loan_date']))->format('Y-m-01')]);
// 
$average = 0;
foreach($contributions AS $single_contribution){
  $average += $single_contribution['amount'];
}
$installment = $loan['loan_amount']/$loan['terms'];
$average /= 3;

$savings = returnSingleField($db, "SELECT SUM(a.amount) AS saving_amount 
                                          FROM (
                                            SELECT  a.sav_amount AS amount,
                                                a.year, a.month,
                                                CONCAT(a.year, '-', IF(a.month < 10, '0',''), a.month, '-01') AS contribution_month
                                                FROM saving AS a
                                                WHERE a.member_id = ?
                                                ORDER BY id DESC
                                          ) AS a
                                          WHERE a.contribution_month <= ?
                                          ", "saving_amount", [$loan['member_id'], (new \DateTime($loan['loan_date']))->format('Y-m-01')]);

$loan_limit = $savings*2.5;

$first_payment_date = new \DateTime();
if( in_array($loan['loan_type_id'], $emergency_loan_ids) || $first_payment_date->format('j') < 15){
  $first_payment_date->modify("+1 month");
} else {
  $first_payment_date->modify("+2 months");
}
?>
<form action="actions/approve.php" method="POST" id="form_apply_loan" >
  <input type="hidden" name="loan_id" value="<?= $_GET['loan_id'] ?>">
  <input type="hidden" name="is_emergency" value="<?= in_array($loan['loan_type_id'], $emergency_loan_ids)?"1":"0" ?>">
    <div class="modal-header bg-green ">
        <h4 class="modal-title">
          <span style="color:white">Approve Loan Request</span>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </h4>
    </div>
    <div class="modal-body">
        <div class="card card-info">
          	<div class="card-body">
          		<div class="row">
          			<div class="col-sm-12">
          				<div class="alert alert-success text-center">
          					<i class="fa  fa-check-circle  fa-3x"></i><br />
          					Do you realy want to approve loan request?
          				</div>

                  <div class="row">
                      <div class="col-sm-6 text-left">
                        Member: <?= $loan['fname'] ?> <?= $loan['lname'] ?>
                      </div>
                      <div class="col-sm-6 text-right">
                        Loan Limit: <span class="badge bg-yellow"><?= number_format($loan_limit) ?> RWF</span>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-sm-12 text-left">
                        Loan Date: <?= $loan['loan_date'] ?>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-sm-6 text-left">
                        Requested Amount: <?= number_format($loan['loan_amount']) ?> RWF
                      </div>
                      <div class="col-sm-6 text-right">
                        <?php
                        if($loan_limit >= $loan['loan_amount']){
                          ?>
                          <span class="badge bg-green"> Loan in range <i class="fa fa-check-circle"></i></span>
                          <?php
                        } else {
                          ?>
                          <span class="badge bg-red"> Loan out of range <i class="fa fa-times-circle"></i></span>
                          <?php
                        }
                        ?>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-sm-12 text-left">
                        Loan: <?= $loan['loan_name'] ?>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-sm-12 text-left">
                        Interest: <?= $loan['interest'] ?>% which is <?= number_format($loan['loan_amount']*$loan['interest']/100) ?> RWF
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-sm-12 text-left">
                        Period: <?= $loan['terms'] ?> Month<?= $loan['terms'] > 1?"s":"" ?> with <?= number_format($installment) ?> RWF every month starting from <span class="badge bg-yellow"><?= $first_payment_date->format("Y-m-d") ?></span>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-sm-12 text-left">
                        Frequency: <?= $loan['frequency'] ?>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-sm-12 text-left">
                        Late Fee: <?= $loan['late_fee'] ?>% of <?= number_format($installment) ?>RWF which is <?= number_format($installment*$loan['late_fee']/100) ?> RWF per day of delay
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-sm-12 text-left">
                        Minimum Contribution: <?= number_format($average) ?> RWF every month
                      </div>
                    </div>
                    <?php
                    if(!is_null($loan['total_recovered_amount'])){
                      ?>
                      <div class="row">
                        <div class="col-sm-12">
                          <a href="#" class="btn btn-block btn-danger">Covered: <?= number_format($loan['total_recovered_amount']) ?></a>
                        </div>
                      </div>
                      <?php
                    }
                    if($loan_limit < $loan['loan_amount']){
                      ?>
                      <div class="alert alert-danger">
                        As the Loan limit is less than request loan amount please consider revising the request as per article number xxx of Ikimina abasare group regarding loan limitation and other articles.
                      </div>
                      <?php
                    }
                    ?>
          			</div>
          		</div>
          	</div>
        </div>
    </div>
    <div class="modal-footer justify-content-between">
        <button class="btn btn-sm btn-flat btn-danger" data-dismiss="modal" aria-label="Close" type="button" id="withdrawable">No Cancel</button>
        <button class="btn btn-sm btn-flat btn-success" id="submit_button" type="submit" name="update_signature">Yes Approve</button>
    </div>
</form>

<script type="text/javascript">
  $("#form_apply_loan").submit(function(e){
        e.preventDefault();
        var old_data = $("#submit_button").html();
        //Here make sure to use ajax request to reduce reload operations

        $("#submit_button").html('<i class="fas fa-sync fa-spin"></i> Saving...');
        $("#submit_button").attr("disabled", "disabled");

        $.ajax({
            type: $(this).attr("method"),
            url: $(this).attr("action"),
            data: $(this).serialize(),
            beforeSend: function(){
                $("#submit_button").html('<i class="fas fa-sync fa-spin"></i> Saving...');
            },success: function(data){
                if(data.status == true){
                    refresh_url = "loans.php";
                    refresh_target_containner = "loans_container";
                    toastr.success(data.message);
                    $("#modal_member").modal("hide");
                } else {
                    //Here Make sure to notify what happens during the processing
                    refresh_url = '';
                    if(data.message){
                      toastr.warning(data.message);
                    } else {
                      toastr.error("The Server Responded with unformatable message");
                    }
                }
                $("#submit_button").html(old_data);
                $("#submit_button").removeAttr("disabled");
            }, error: function(err){
                console.log(err);
                $("#submit_button").html(old_data);
                $("#submit_button").removeAttr("disabled");
                toastr.error("Invalid Response");
            }
        });
    });
</script>

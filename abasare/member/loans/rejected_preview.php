<?php
require_once "../../lib/db_function.php";

$loan = first($db, "SELECT  a.id,
                            a.loan_amount,
                            b.fname,
                            b.lname,
                            c.lname AS loan_name,
                            a.loan_date,
                            a.signatory_1_comment,
                            a.signatory_2_comment,
                            a.president_reject_comment,
                            a.committee_reject_comment
                            FROM member_loans AS a
                            INNER JOIN member AS b
                            ON a.member_id = b.id
                            INNER JOIN loan_type AS c
                            ON a.loan_id = c.id
                            WHERE a.id = ?
                            ", [$_GET['loan_id']]);
?>
<!-- <form action="actions/approve.php" method="POST" id="form_apply_loan" >
  <input type="hidden" name="loan_id" value="<?= $_GET['loan_id'] ?>"> -->
    <div class="modal-header bg-red ">
        <h4 class="modal-title">
          <span style="color:white">Request Rejected</span>
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
          				<div class="alert alert-danger text-center">
          					<i class="fa  fa-times-circle  fa-4x"></i><br />
          					Request Rejected
                    <div class="row">
                      <div class="col-sm-12">
                        Loan: <?= $loan['loan_name'] ?>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-sm-12">
                        Loan Date: <?= $loan['loan_date'] ?>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-sm-12">
                        Requested Amount: <?= number_format($loan['loan_amount']) ?> RWF
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-sm-12">
                        Comment: <?= $loan[$_GET['position']] ?>
                      </div>
                    </div>
          				</div>
          			</div>
          		</div>
          	</div>
        </div>
    </div>
    <!-- <div class="modal-footer justify-content-between">
        <button class="btn btn-sm btn-flat btn-danger" data-dismiss="modal" aria-label="Close" type="button" id="withdrawable">No Cancel</button>
        <button class="btn btn-sm btn-flat btn-success" id="submit_button" type="submit" name="update_signature">Yes Accept</button>
    </div> -->
<!-- </form> -->

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

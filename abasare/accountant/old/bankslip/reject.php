<?php

require_once "../../lib/db_function.php";

$request = first($db, "SELECT a.id,
                              a.type, 
                              a.ref_number, 
                              a.amount, 
                              a.data, 
                              a.paid_at,
                              b.fname,
                              b.lname,
                              b.company,
                              DATE(a.created_at) AS created_at_date,
                              a.created_at,
                              a.has_fine,
                              a.fine_data 
                              FROM bank_slip_requests AS a
                              INNER JOIN member AS b
                              ON a.member_id = b.id
                              WHERE a.id = ?", [$_GET['request_id']]);
?>

<form action="bankslip/save-reject.php" method="POST" id="form_apply_loan" >
  <input type="hidden" name="request_id" value="<?= $_GET['request_id'] ?>">
  <div class="modal-header bg-red ">
      <h4 class="modal-title">
        <span style="color:white">Rejecting <?= $request['type'] ?> Bank Slip Paid At <?= $request['paid_at'] ?></span>
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
	                	<div class="alert bg-primary text-center">The request submitted: <?= $request['created_at'] ?></div>
	              	</div>
	            </div>
	            <div class="row">
	            	<div class="col-sm-12">
		                <table class="table table-stripped">
		                  	<tr>
		                    	<th>Type</th><td><?= $request['type'] ?></td>
		                  	</tr>
		                  	<tr>
		                    	<th>Reference</th><td><strong><?= $request['ref_number'] ?></strong></td>
		                  	</tr>
		                  	<tr>
		                    	<th>Amount</th><td><strong><?= number_format($request['amount']) ?> RWF</strong></td>
		                  	</tr>
		                </table>
	            	</div>
	            </div>
				<div class="form-group row">
				    <label for="comment" class="col-sm-2 col-form-label">
				    	Why?
					</label>
					<div class="col-sm-10">
				    	<input type="text" name="comment" id="comment" required class="form-control">
					</div>
				</div>
          	</div>
      </div>
  </div>
  <div class="modal-footer justify-content-between">
      <button class="btn btn-sm btn-flat btn-success" data-dismiss="modal" aria-label="Close" type="button" id="withdrawable">No Cancel</button>
      <button class="btn btn-sm btn-flat btn-danger" id="submit_button" type="submit" name="Accept">Yes Reject Bank Slip</button>
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
                    refresh_url = "bankslip/index.php";
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
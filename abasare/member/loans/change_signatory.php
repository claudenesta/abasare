<?php
require_once "../../lib/db_function.php";
$signatories = returnAllData($db, "SELECT id, name FROM users WHERE id NOT IN (?,?,?) AND signature IS NOT NULL AND status=?", [$_SESSION['id'], $_GET['signatory_1'], $_GET['signatory_2'], 1]);

?>
<form action="loans/save_change_signatory.php" method="POST" id="form_apply_loan" >
  <input type="hidden" name="loan_id" value="<?= $_GET['loan_id'] ?>">
  <input type="hidden" name="position" value="<?= $_GET['position'] ?>">
    <div class="modal-header bg-green ">
        <h4 class="modal-title">
          <span style="color:white">Change Signatory</span>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </h4>
    </div>
    <div class="modal-body">
        <div class="card card-info">
          <div class="card-body">
			<div class="form-group row">
				<label for="signatory" class="col-sm-4 col-form-label">New Signatory: </label>
				<div class="col-sm-8">
			      	<select name="signatory" class="form-control select2 form-control-sm" id="signatory" placeholer="New Signatory" style="width: 100%;">
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
          </div>
        </div>
    </div>
    <div class="modal-footer justify-content-between">
        <button class="btn btn-sm btn-flat btn-success" type="submit" id="submit_button">Submit</button>
    </div>
</form>

<script type="text/javascript">
		$("#signatory").select2({
			placeholder: "Select New Signatory"
		});
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
                        refresh_url = "loans/index.php?member_id=<?= $_SESSION['acc'] ?>";
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
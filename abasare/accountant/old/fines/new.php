<?php
require_once "../../lib/db_function.php";

$members = returnAllData($db, "SELECT   a.id,
                                        CONCAT(a.fname, ' ', a.lname) AS member_name
                                        FROM member AS a
                                        WHERE a.status = ?
                                        ", [1]);
$fine_types = returnAllData($db, "SELECT id, name, default_amount FROM fine_types ORDER BY name ASC")
?>

<form action="fines/save-new-fine.php" method="POST" id="form_apply_loan" >
    <div class="modal-header bg-red ">
        <h4 class="modal-title">
          <span style="color:white">Create New Fine</span>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </h4>
    </div>
    <div class="modal-body">
        <div class="card card-info">
          	<div class="card-body">
                <div class="form-group row">
                  <label for="member_id" class="col-sm-4 col-form-label">Member: </label>
                  <div class="col-sm-8">
                    <select class="form-control select2" id="member_id" name="member_id[]" style="width: 100%;">
                        <option value=""></option>
                        <?php
                        foreach($members AS $member){
                            ?>
                            <option value="<?= $member['id'] ?>"><?= $member['member_name'] ?></option>
                            <?php
                        }
                        ?>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="fine_type_id" class="col-sm-4 col-form-label">Fine: </label>
                  <div class="col-sm-8">
                    <select class="form-control select2" id="fine_type_id" name="fine_type_id" style="width: 100%;">
                        <option value=""></option>
                        <?php
                        foreach($fine_types AS $fine_type){
                            ?>
                            <option data-amount="<?= $fine_type['default_amount'] ?>" value="<?= $fine_type['id'] ?>"><?= $fine_type['name'] ?>(<?= number_format($fine_type['default_amount']) ?> Rwf)</option>
                            <?php
                        }
                        ?>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                    <label for="fine_amount" class="col-sm-4 col-form-label">
                        Fine Amount (RWF):
                    </label>
                    <div class="col-sm-8">
                        <input type="text" name="fine_amount" id="fine_amount" class="form-control">
                    </div>
                </div>
        	</div>
        </div>
    </div>
    <div class="modal-footer justify-content-between">
        <button class="btn btn-sm btn-flat btn-danger submit_button" id="submit_button" type="submit" name="update_signature">New Fines</button>
    </div>
</form>

<script type="text/javascript">
    $("#member_id").select2({
        placeholder: "Select member",
        closeOnSelect: false
    });

    $("#fine_type_id").select2({
        placeholder: "Select fine to be applied"
    }).bind("change", function(e){
        //Here track the new select and update the UI for the value proposal
        $("#fine_amount").val($("#fine_type_id option:selected").data("amount"));
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
                },
                
                
success: function(data){
                if(data.status == true){
                    // 1. Show the success message
                    toastr.success(data.message);

                    // 2. Hide the pop-up form
                    $("#modal_member").modal("hide");

                    // 3. RELOAD THE FINES LIST <--- THIS IS THE FIX
                    $("#savings_container").load("fines/unpaid_fines.php");
                    
                } else {
                    //Here Make sure to notify what happens during the processing
                    if(data.message){
                      toastr.warning(data.message);
                    } else {
                      toastr.error("The Server Responded with unformattable message");
                    }
                }
                
                $("#submit_button").html(old_data);
                $("#submit_button").removeAttr("disabled");
            },
            //  /\ /\ /\ THIS IS THE BLOCK TO REPLACE /\ /\ /\
            error: function(err){
                console.log(err);
                $("#submit_button").html(old_data);
                $("#submit_button").removeAttr("disabled");
                toastr.error("Invalid Response");
            }
        });
    });
</script>
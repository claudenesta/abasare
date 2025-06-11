<form action="actions/save_photo.php" method="POST" id="form_add_signature" >
    <div class="modal-header bg-primary ">
        <h4 class="modal-title">
          <span style="color:white"><i class="fa fa-pencil"></i> Change Profile Picture  </span>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </h4>
    </div>
    <div class="modal-body">
        <div class="card card-info">
          <div class="card-body">
            <div class="form-group row">
              <label for="image" class="col-sm-3 col-form-label">Photo: </label>
              <div class="col-sm-9">
                <input class="form-control form-control-sm" type="file" name="photo" />
              </div>
            </div>
          </div>
        </div>
    </div>
    <div class="modal-footer justify-content-between">
        <button class="btn btn-sm btn-flat btn-primary submit_button" type="submit" name="update_signature">Upload</button>
    </div>
</form>

<script>
    $(document).ready(function(){
        $("#form_add_signature").submit(function(e){
            e.preventDefault();
            var old_data = $("#submit_button").html();
            //Here make sure to use ajax request to reduce reload operations
            $.ajax({
                type: $(this).attr("method"),
                url: $(this).attr("action"),
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData:false,
                beforeSend: function(){
                    $("#submit_button").html("Wait....");
                },success: function(data){
                    if(data.status == true){
                        refresh_url = "profile.php?member_id=<?= $_GET['member_id'] ?>";
                        refresh_target_containner = "member_profile";
                        $("#modal_member").modal("hide");
                    } else {
                        //Here Make sure to notify what happens during the processing
                        refresh_url = '';
                    }
                    $("#submit_button").html(old_data);
                }, error: function(err){
                    console.log(err);
                    $("#submit_button").html(old_data);
                }
            });
        });
    });
</script>
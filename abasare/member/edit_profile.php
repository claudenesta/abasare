<?php
require_once "../lib/db_function.php";
$data = first($db, "SELECT  a.*,
                            b.name AS village_name,
                            b.id AS village_id,
                            c.name AS cell_name,
                            c.id AS cell_id,
                            d.name AS sector_name,
                            d.id AS sector_id,
                            e.name AS district_name,
                            e.id AS district_id,
                            f.id AS province_id
                            FROM member AS a 
                            LEFT JOIN villages AS b
                            ON a.village_id = b.id
                            LEFT JOIN cells AS c
                            ON b.cell_id = c.id
                            LEFT JOIN sectors AS d
                            ON c.sector_id = d.id
                            LEFT JOIN districts AS e
                            ON d.district_id = e.id
                            LEFT JOIN provinces AS f
                            ON e.province_id = f.id
                            WHERE a.id = ?", [$_GET['member_id']]);

$provinces = returnAllData($db, "SELECT id, name FROM provinces ORDER BY name ASC");
?>
<form action="actions/save_profile.php" method="POST" id="form_add_signature" >
  <input type="hidden" name="member_id" value="<?= $data['id'] ?>">
    <div class="modal-header bg-success ">
        <h4 class="modal-title">
          <span style="color:white"><i class="fa fa-pencil"></i> Change <?= $data['fname'] ?>  <?= $data['lname'] ?>'s Information  </span>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </h4>
    </div>
    <div class="modal-body">
        <div class="card card-info">
          <div class="card-body">
            <div class="form-group row">
              <label for="fname" class="col-sm-1 col-form-label">Name: </label>
              <div class="col-sm-5">
                <input class="form-control form-control-sm" type="text" name="fname" id="fname" value="<?= $data['fname'] ?>" />
              </div>
              <div class="col-sm-6">
                <input class="form-control form-control-sm" type="text" name="lname" id="lname" value="<?= $data['lname'] ?>" />
              </div>
            </div>
            <div class="form-group row">
              <label for="phone_cell" class="col-sm-1 col-form-label">Phone: </label>
              <div class="col-sm-5">
                <input class="form-control form-control-sm" type="text" name="phone_cell" id="phone_cell" value="<?= $data['phone_cell'] ?>" />
              </div>
              <label for="email" class="col-sm-1 col-form-label">Email: </label>
              <div class="col-sm-5">
                <input class="form-control form-control-sm" type="text" name="email" id="email" value="<?= $data['email'] ?>" />
              </div>
            </div>
            <div class="form-group row">
              <label for="id_number" class="col-sm-1 col-form-label">ID: </label>
              <div class="col-sm-5">
                <input class="form-control form-control-sm" type="text" name="id_number" id="id_number" value="<?= $data['id_number'] ?>" />
              </div>
              <label for="id_location" class="col-sm-2 col-form-label text-right">Derivered: </label>
              <div class="col-sm-4">
                <input class="form-control form-control-sm" type="text" name="id_location" id="id_location" value="<?= $data['id_location'] ?>" />
              </div>
            </div>
            <div class="form-group row">
              <label for="birth_date" class="col-sm-4 col-form-label">Birth Date: </label>
              <label for="civil_status" class="col-sm-4 col-form-label">Civil Status: </label>
              <label for="sex" class="col-sm-4 col-form-label">Gender: </label>
            </div>
            <div class="form-group row">
              <div class="col-sm-4">
                <input class="form-control form-control-sm" type="date" name="birth_date" id="birth_date" value="<?= $data['birth_date'] ?>" />
              </div>
              <div class="col-sm-4">
                <select class="form-control select2" id="civil_status" name="civil_status" style="width: 100%;">
                    <option <?= $data['civil_status'] == "Single"?"selected":"" ?>>Single</option>
                    <option <?= $data['civil_status'] == "Married"?"selected":"" ?>>Married</option>
                    <option <?= $data['civil_status'] == "Widow"?"selected":"" ?>>Widow</option>
                    <option <?= $data['civil_status'] == "Divorced"?"selected":"" ?>>Divorced</option>

                </select>
              </div>
              <div class="col-sm-4">
                <select class="form-control select2" id="sex" name="sex" style="width: 100%;">
                    <option <?= $data['sex'] == "Male"?"selected":"" ?>>Male</option>
                    <option <?= $data['sex'] == "Female"?"selected":"" ?>>Female</option>
                </select>
              </div>
            </div>
            <div class="form-group row">
              <label for="province_id" class="col-sm-2 col-form-label">Province: </label>
              <div class="col-sm-4">
                <select class="form-control select2" id="province_id" name="province_id" style="width: 100%;">
                    <option value=""></option>
                    <?php
                    foreach($provinces AS $province){
                        ?>
                        <option value="<?= $province['id'] ?>" <?= $province['id'] == $data['province_id']?"selected":"" ?>><?= $province['name'] ?></option>
                        <?php
                    }
                    ?>

                </select>
              </div>
              <label for="fname" class="col-sm-2 col-form-label">District: </label>
              <div class="col-sm-4" id="district_container">
                
              </div>
            </div>
            <div class="form-group row">
              <label for="fname" class="col-sm-1 col-form-label">Sector: </label>
              <div class="col-sm-5" id="sector_container">
                
              </div>
              <label for="fname" class="col-sm-2 col-form-label">Cell: </label>
              <div class="col-sm-4" id="cell_container">
               
              </div>
            </div>
            <div class="form-group row">
              <label for="fname" class="col-sm-1 col-form-label">Village: </label>
              <div class="col-sm-5" id="village_container">
               
              </div>
              <label for="address" class="col-sm-2 col-form-label">Address: </label>
              <div class="col-sm-4">
                <input class="form-control form-control-sm" type="text" name="address" id="address" value="<?= $data['address'] ?>" />
              </div>
            </div>
            <div class="form-group row">
              <label for="job_title" class="col-sm-1 col-form-label">Title: </label>
              <div class="col-sm-5">
                <input class="form-control form-control-sm" type="text" name="job_title" id="job_title" value="<?= $data['job_title'] ?>" />
              </div>
              <label for="company" class="col-sm-2 col-form-label">Company: </label>
              <div class="col-sm-4">
                <input class="form-control form-control-sm" type="text" name="company" id="company" value="<?= $data['company'] ?>" />
              </div>
            </div>
          </div>
        </div>
    </div>
    <div class="modal-footer justify-content-between">
        <button class="btn btn-sm btn-flat btn-success submit_button" type="submit" name="update_signature">Save Changes</button>
    </div>
</form>

<script>
    $(document).ready(function(){
        $("#civil_status").select2();
        $("#sex").select2();
        $("#province_id").select2({
            placeholder: "Select Province Please"
        }).bind("change", function(){
            //Here process the change operation
          $("#district_container").load("actions/get_district.php?province_id=" + $("#province_id").val() + "&default=<?= $data['district_id'] ?>", function(){
            //Now the new info are well loaded
            $("#district_id").select2({
              placeholder: "Select District Please",
              tags: true
            }).bind("change", function(){
              //Here make sure to load sectors
              $("#sector_container").load("actions/get_sector.php?district_id=" + $("#district_id").val() + "&default=<?= $data['sector_id'] ?>", function(){
                $("#sector_id").select2({
                  placeholder: "Select Sector Please",
                  tags: true
                }).bind("change", function(){
                  //No make sure to load the required cells information
                  $("#cell_container").load("actions/get_cell.php?sector_id=" + $("#sector_id").val() + "&default=<?= $data['cell_id'] ?>", function(){
                    $("#cell_id").select2({
                      placeholder: "Select Cell Please",
                      tags: true
                    }).bind("change", function(){
                      //Here make sure to load Village as the final stage
                      $("#village_container").load("actions/get_village.php?cell_id=" + $("#cell_id").val() + "&default=<?= $data['village_id'] ?>", function(){
                        $("#village_id").select2({
                          placeholder:"Select Village Please",
                          tags: true
                        });
                      });
                    }).trigger("change");
                  });
                }).trigger("change");
              });
            }).trigger("change");
          });
        }).trigger("change");
        $("#form_add_signature").submit(function(e){
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
                        refresh_url = "profile.php?member_id=<?= $_GET['member_id'] ?>";
                        refresh_target_containner = "member_profile";
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
    });
</script>
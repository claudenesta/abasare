<?php
require_once "../lib/db_function.php";
$rows = first($db, "SELECT  a.*,
                            b.name AS village_name,
                            c.name AS cell_name,
                            d.name AS sector_name,
                            e.name AS district_name
                            FROM member AS a 
                            LEFT JOIN villages AS b
                            ON a.village_id = b.id
                            LEFT JOIN cells AS c
                            ON b.cell_id = c.id
                            LEFT JOIN sectors AS d
                            ON c.sector_id = d.id
                            LEFT JOIN districts AS e
                            ON d.district_id = e.id
                            WHERE a.id = ?", [$_GET['member_id']]);
?>
<!-- Profile Image -->
<div class="card card-primary card-outline">
  <div class="card-body box-profile">
    <div class="text-center">
      <img data-url="add_photo.php?member_id=<?= $_GET['member_id'] ?>" id="change_photo" class="profile-user-img img-fluid img-circle" src="/<?= $_SESSION['photo'] ?>" alt="User profile picture">
    </div>

    <h3 class="profile-username text-center">
      <a href="edit_profile.php?member_id=<?= $_GET['member_id'] ?>" class="open_box">
        <?php echo $rows['fname']." ".$rows['lname']; ?>
        <i class="fa fa-pencil"></i>
      </a>
    </h3>
    <p class="text-muted text-center"><?php echo $rows['job_title']; ?></p>

  </div>
  <div class="card card-primary">
      <!-- /.card-header -->
    <div class="card-body">
      <hr>
      <strong><i class="fa fa-plus mr-1"></i> Personal Info </strong>
      <?php
      if($_SESSION['is_ready']){
        ?>
        <span class="badge bg-green">Profile is Ready</span>
        <?php
      } else {
        //Here Make sure to allow uploading the signature
        ?>
        <a class="btn btn-sm btn-primary open_box" id="add_signature" href="add_signature.php?member_id=<?= $_GET['member_id'] ?>">Add Signature</a>
        <?php
      }
      ?>
      <hr>
      <p class="text"> First Name : <?php echo $rows['fname'];  ?> </p>
      <p class="text"> Last Name : <?php echo $rows['lname'];  ?> </p>
      <p class="text"> Middle Name : <?php echo $rows['mi'];  ?> </p>
      <p class="text"> Date of Birth : <?php echo $rows['birth_date'];  ?> </p>
      <p class="text"> Civil Status : <?php echo $rows['civil_status'];  ?> </p>
      <hr>
      <strong><i class="fa fa-plus mr-1"></i> Contact Info</strong>
      <hr>
      <p class="text"> Address : <?php echo $rows['address'];  ?> </p>
      <p class="text"> District : <?php echo $rows['district_name'];  ?> </p>
      <p class="text"> Sector : <?php echo $rows['sector_name'];  ?> </p>
      <p class="text"> Cell : <?php echo $rows['cell_name'];  ?> </p>
      <p class="text"> Village : <?php echo $rows['village_name'];  ?> </p>
      <p class="text"> Phone / Cellphone : <?php echo $rows['phone_cell'];  ?> </p>
      <p class="text"> Email : <?php echo $rows['email'];  ?> </p>
      <p class="text"> ID : <?php echo $rows['id_number'];  ?> </p>
      <p class="text"> Location : <?php echo $rows['id_location'];  ?> </p>
      <hr>
      <strong><i class="fa fa-plus mr-1"></i> Current Employment Info </strong>
      <hr>
      <p class="text"> Employment Status : <?php echo $rows['employment_status'];  ?> </p>
      <p class="text"> Company : <?php echo $rows['company'];  ?> </p>
      <p class="text"> Job Title : <?php echo $rows['job_title'];  ?> </p>
      <p class="text"> Monthly Income : <?php echo $rows['income'];  ?> Frws</p>
    </div>
    <!-- /.card-body -->
  </div>
  <!-- /.card-body -->
</div>
<script>
  $(document).ready(function(){

    $("#change_photo").click(function(e){
      e.preventDefault();
      var url = $(this).data('url');
      $("#modal_member").find(".modal-content").load(url, function(){
        refresh_target_containner = '';
        refresh_url= '';
        $("#modal_member").modal("show");
      });
    });
    $(".open_box").click(function(e){
      e.preventDefault();
      var clicked = $(this);
      var url = clicked.attr("href");
      var old_data = clicked.html();
      $(this).html("Please Wait");
      $("#modal_member").find(".modal-content").load(url, function(){
        clicked.html(old_data);
        refresh_target_containner = '';
        refresh_url= '';
        $("#modal_member").modal("show");
      });
    });
  });
</script>
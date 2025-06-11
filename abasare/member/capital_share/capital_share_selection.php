<?php
require_once('../../DBController.php');
include('../header.php');

$active = "capital_share";
include('../menu.php');

// Fetch all members to populate the dropdown
$members_query = mysqli_query($con, "SELECT id, fname, lname FROM member WHERE status = 1 ORDER BY fname ASC");
?>

<div class="wrapper">
  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Select Member
        <small>to view Capital Shares Statement</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Capital Share</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-md-8 offset-md-2">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Find Member</h3>
            </div>
            <div class="box-body">
              <p>Please select a member from the list below to view their detailed capital shares statement.</p>
              
              <!-- This form will redirect to your statement page -->
              <form action="capital_share_info.php" method="GET" id="member_selection_form">
                <div class="form-group">
                  <label for="member_select">Member:</label>
                  <select name="m_idi" id="member_select" class="form-control select2" style="width: 100%;" required>
                    <option value="">-- Type to search for a member --</option>
                    <?php
                    while ($member = mysqli_fetch_assoc($members_query)) {
                      echo "<option value='" . $member['id'] . "'>" . htmlspecialchars($member['fname'] . ' ' . $member['lname']) . "</option>";
                    }
                    ?>
                  </select>
                </div>
                
                <div class="form-group">
                  <button type="submit" class="btn btn-primary">View Statement</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
  <?php include('../../footer.php'); ?>
</div>

<!-- Include Select2 if not already in footer -->
<script>
$(document).ready(function() {
    // Initialize the searchable dropdown
    $('.select2').select2({
      placeholder: "Type a name to search"
    });
});
</script>
</body>
</html>
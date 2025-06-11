<?php
require_once "../../lib/db_function.php";
$member = first($db, "SELECT fname, lname, Account_balance FROM member WHERE id =?" , [$_GET['member_id']]);
$first_year = returnSingleField($db, "SELECT MIN(year) AS year FROM saving WHERE member_id = ?", "year", [$_GET['member_id']]);
if(!$year) {
	$year = 2018;
}
?>
	<div class="modal-header bg-green ">
        <h4 class="modal-title">
          <span style="color:white">Preview <?= $member['fname'] ?> <?= $member['lname'] ?>'s Saving <i class="fa fa-money"></i></span>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </h4>
    </div>
    <div class="modal-body">
        <div class="card card-info">
          	<div class="card-body">
          		<div class="form-group row">
	              <label for="fname" class="col-sm-4 col-form-label">Financial Year: </label>
	              <div class="col-sm-8">
	                <select class="form-control select2" id="year" name="year" style="width: 100%;">
	                    <?php
	                    $year = (new \DateTime())->format("Y");
	                    for($year; $year >= $first_year; $year--){
	                        ?>
	                        <option value="<?= $year ?>"><?= $year ?></option>
	                        <?php
	                    }
	                    ?>
	                </select>
	              </div>
	            </div>
          		<div class="form-group row">
          			<div class="col-sm-12" id="savings_container">
          			</div>
          		</div>
          	</div>
        </div>
    </div>
    <div class="modal-footer justify-content-between">
        <button class="btn btn-sm btn-flat btn-success" data-dismiss="modal" type="button">Close</button>
    </div>

    <script type="text/javascript">
    	$("#year").select2({
    		placeholder: "Select Financial Year"
    	}).bind("change", function(e){
    		// console.log($(this).val());
    		$("#savings_container").load("actions/savings_history.php?member_id=<?= $_GET['member_id'] ?>&year=" + $(this).val(), function(){
    			//Here everything is loaded now we can make further processing if required
    		});
    	}).trigger("change");
    </script>
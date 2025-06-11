<?php 
include('../lib/db_function.php');

$db->beginTransaction();
try{
	//Get the Saving information
	$data = first($db, "SELECT * FROM saving WHERE id = ?", [$_GET['id']]);

	//Delete Interest that might be caused by the overdue Fines
	if($data['fine'] > 0){
		saveData($db, "DELETE FROM interest WHERE saving_id = ?", [$data['id']]);
	}

	//Delete Saving record
	saveData($db, "DELETE FROM saving WHERE id = ?", [$data['id']]);

	//Update Members Account information
	saveData($db, "UPDATE member SET Account_balance = Account_balance - ? WHERE id = ?", [$data['sav_amount'], $data['member_id']]);

	$db->commit();
	?>
	<script type="text/javascript">
		alert("Success: Saving deleted from the system");
	</script>
	<meta http-equiv="refresh" content="0; URL=savings_info.php?m_id=<?php echo $_GET['mid']; ?>&year=<?= $_GET['year'] ?>">
	<?php
} catch(\Exception $e){
	$db->rollBack();
	?>
	<script type="text/javascript">
		alert("Error: <?= $e->getMessage(); ?>");
	</script>
	<meta http-equiv="refresh" content="0; URL=savings_info.php?m_id=<?php echo $_GET['mid']; ?>&year=<?= $_GET['year'] ?>">
	<?php
}
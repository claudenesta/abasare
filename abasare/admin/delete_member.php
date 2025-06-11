<?php 
include('../lib/db_function.php');
$emp_id=$_GET['id'];
$db->beginTransaction();
try {
    //First disable member operation
    $sql = "UPDATE member SET status = 0 WHERE id = ?";
    saveData($db, $sql, [$emp_id]);
    saveData($db, "UPDATE users SET status = 0 WHERE member_acc = ?", [$emp_id]);
    $db->commit();
    ?>
    <meta http-equiv="refresh" content="0; URL=list.php">
    <?php
} catch(\Exception $e){
    //Here an error occurred
    $db->rollBack();
}

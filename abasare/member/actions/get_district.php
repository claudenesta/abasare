<?php
require_once "../../lib/db_function.php";

$districts = returnAllData($db, "SELECT id, name FROM districts WHERE province_id = ?", [$_GET['province_id']]);
?>
<select class="form-control select2" id="district_id" name="district_id" style="width: 100%;">
    <option value=""></option>
    <?php
    foreach($districts AS $district){
        ?>
        <option value="<?= $district['id'] ?>" <?= $district['id'] == $_GET['default']?"selected":"" ?>><?= $district['name'] ?></option>
        <?php
    }
    ?>
</select>
<?php
require_once "../../lib/db_function.php";

$villages = returnAllData($db, "SELECT id, name FROM villages WHERE cell_id = ?", [$_GET['cell_id']]);
?>
<select class="form-control select2" id="village_id" name="village_id" style="width: 100%;">
    <option value=""></option>
    <?php
    foreach($villages AS $village){
        ?>
        <option value="<?= $village['id'] ?>" <?= $village['id'] == $_GET['default']?"selected":"" ?>><?= $village['name'] ?></option>
        <?php
    }
    ?>
</select>
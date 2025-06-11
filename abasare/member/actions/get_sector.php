<?php
require_once "../../lib/db_function.php";

$sectors = returnAllData($db, "SELECT id, name FROM sectors WHERE district_id = ?", [$_GET['district_id']]);
?>
<select class="form-control select2" id="sector_id" name="sector_id" style="width: 100%;">
    <option value=""></option>
    <?php
    foreach($sectors AS $sector){
        ?>
        <option value="<?= $sector['id'] ?>" <?= $sector['id'] == $_GET['default']?"selected":"" ?>><?= $sector['name'] ?></option>
        <?php
    }
    ?>
</select>
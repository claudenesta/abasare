<?php
require_once "../../lib/db_function.php";

$cells = returnAllData($db, "SELECT id, name FROM cells WHERE sector_id = ?", [$_GET['sector_id']]);
?>
<select class="form-control select2" id="cell_id" name="cell_id" style="width: 100%;">
    <option value=""></option>
    <?php
    foreach($cells AS $cell){
        ?>
        <option value="<?= $cell['id'] ?>" <?= $cell['id'] == $_GET['default']?"selected":"" ?>><?= $cell['name'] ?></option>
        <?php
    }
    ?>
</select>
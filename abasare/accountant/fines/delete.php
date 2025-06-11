<?php
// Inside fines/delete.php
session_start();
require_once "../../lib/db_function.php";

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $fine_id_to_delete = (int)$_GET['id'];
    try {
        saveData($db, "DELETE FROM special_fines WHERE id = ?", [$fine_id_to_delete]);
        
        // THIS LINE CREATES THE GREEN SUCCESS MESSAGE
        $_SESSION['flash_message'] = [
            'type' => 'success',
            'message' => 'Fine successfully deleted.'
        ];

    } catch (Exception $e) { /* ... error handling ... */ }
}
// ...
// THIS LINE REDIRECTS BACK TO THE MAIN PAGE
header("Location: ../fines.php");
exit();
?>
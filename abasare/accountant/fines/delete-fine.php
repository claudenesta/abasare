<?php
header("Content-Type: application/json");
require_once "../../lib/db_function.php";

// Validate input
if(empty($_POST['id']) || !is_numeric($_POST['id'])) {
    echo json_encode(['status' => false, 'message' => 'Invalid fine ID']);
    exit;
}

$db->beginTransaction();
try {
    $stmt = $db->prepare("DELETE FROM special_fines WHERE id = ?");
    $stmt->execute([$_POST['id']]);
    
    if($stmt->rowCount() > 0) {
        $db->commit();
        echo json_encode(['status' => true, 'message' => 'Fine deleted successfully']);
    } else {
        $db->rollBack();
        echo json_encode(['status' => false, 'message' => 'Fine not found or already deleted']);
    }
} catch(PDOException $e) {
    $db->rollBack();
    echo json_encode(['status' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
<?php
header("Content-Type: application/json");
require_once "../../lib/db_function.php";

// Validate input
if(empty($_POST['fine_id']) || !is_numeric($_POST['fine_id'])) {
    echo json_encode(['status' => false, 'message' => 'Invalid fine ID']);
    exit;
}

if(empty($_POST['fine_type_id']) || !is_numeric($_POST['fine_type_id'])) {
    echo json_encode(['status' => false, 'message' => 'Please select a fine type']);
    exit;
}

if(empty($_POST['fine_amount']) || !is_numeric($_POST['fine_amount']) || $_POST['fine_amount'] <= 0) {
    echo json_encode(['status' => false, 'message' => 'Please enter a valid amount']);
    exit;
}

$db->beginTransaction();
try {
    $stmt = $db->prepare("UPDATE special_fines SET 
                         fine_type_id = ?, 
                         fine_amount = ?
                         WHERE id = ?");
    
    $stmt->execute([
        $_POST['fine_type_id'],
        $_POST['fine_amount'],
        $_POST['fine_id']
    ]);
    
    if($stmt->rowCount() > 0) {
        $db->commit();
        echo json_encode(['status' => true, 'message' => 'Fine updated successfully']);
    } else {
        $db->rollBack();
        echo json_encode(['status' => false, 'message' => 'No changes made or fine not found']);
    }
} catch(PDOException $e) {
    $db->rollBack();
    echo json_encode(['status' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
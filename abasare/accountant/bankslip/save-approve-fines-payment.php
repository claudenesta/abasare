<?php
header("Content-Type:application/json");
require_once "../../lib/db_function.php";

if(empty($_POST['request_id'])) {
    echo json_encode(['status' => false, "message" => "No request specified"]);
    exit;
}

$db->beginTransaction();
try {
    // Get request data
    $request = first($db, "SELECT id, member_id, data 
                          FROM bank_slip_requests 
                          WHERE id = ?", [$_POST['request_id']]);
    
    if(!$request) {
        throw new Exception("Request not found");
    }

    $data = json_decode($request['data']);
    
    if(!isset($data->ref_id)) {
        throw new Exception("Invalid fine reference");
    }

    // Update fine status
    saveData($db, "UPDATE special_fines SET status = 'Accepted' WHERE id = ?", [$data->ref_id]);
    
    // Update request status
    saveData($db, "UPDATE bank_slip_requests SET status = 'Accepted' WHERE id = ?", [$_POST['request_id']]);

    $db->commit();
    echo json_encode([
        'status' => true, 
        'message' => "Fine payment successfully approved"
    ]);
} catch(Exception $e) {
    $db->rollBack();
    echo json_encode([
        'status' => false, 
        'message' => $e->getMessage()
    ]);
}
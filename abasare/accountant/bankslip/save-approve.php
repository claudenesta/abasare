<?php
header("Content-Type:application/json");
require_once "../../lib/db_function.php";

if(empty($_POST['request_id'])) {
    echo json_encode(['status'=>false, 'message'=>'Missing request ID']);
    return;
}

$db->beginTransaction();
try {
    // Get request data
    $request = first($db,
        "SELECT id, member_id, data, month_to_save_for, year_to_save_for, ref_number, amount, has_fine, fine_data, paid_at
         FROM bank_slip_requests 
         WHERE id = ?", 
        [$_POST['request_id']]
    );
    
    if(!$request) {
        throw new Exception("Request not found");
    }
    
    // Decode JSON data safely
    $transaction_info = json_decode($request['data']);
    if(!$transaction_info) {
        throw new Exception("Invalid data format in request");
    }
    
    // Ensure all required properties exist
    if(!isset($transaction_info->sav_amount)) {
        throw new Exception("Missing required property: sav_amount");
    }
    
    // Set default values for potentially missing properties to avoid undefined property warnings
    if(!isset($transaction_info->fine)) {
        $transaction_info->fine = 0;
    }
    
    if(!isset($transaction_info->days)) {
        $transaction_info->days = 0;
    }
    
    // Save to savings table
    saveData($db,
        "UPDATE saving 
         SET status = 'Accepted'
         WHERE ref_number = ?",
        [$request['ref_number']]
    );

    // Update request status
    saveData($db,
        "UPDATE bank_slip_requests 
         SET status = 'Accepted'
         WHERE id = ?",
        [$request['id']]
    );

    $db->commit();
    echo json_encode([
        'status' => true, 
        'message' => "Successfully approved savings of " . number_format($request['amount']) . " RWF with reference " . $request['ref_number']
    ]);
} catch(Exception $e) {
    $db->rollback();
    echo json_encode(['status'=>false, 'message'=>$e->getMessage()]);
}
?>
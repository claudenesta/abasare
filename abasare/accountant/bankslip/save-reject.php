<?php
header("Content-Type: application/json");
require_once "../../lib/db_function.php";

// Validate inputs
if (empty($_POST['request_id'])) {
    echo json_encode(['status' => false, "message" => "No request to accept"]);
    exit;
}

if (empty($_POST['comment'])) {
    echo json_encode(['status' => false, "message" => "Specify the cause of slip rejection here"]);
    exit;
}

$db->beginTransaction();
try {
    // Fetch the request
    $request = first($db, "SELECT id, member_id, type, ref_number, amount, data, paid_at, has_fine, fine_data, created_at FROM bank_slip_requests WHERE id = ?", [$_POST['request_id']]);
    
    if (!$request) {
        throw new Exception("Request not found");
    }

    // Handle loan payment rejection
    if ($request['type'] == "loan payment") {
        $data = json_decode($request['data'], true);
        if (!$data || !isset($data['id'])) {
            throw new Exception("Invalid loan payment data");
        }
        saveData($db, "UPDATE lend_payments SET status = ?, comment = ? WHERE id = ?", ["Rejected", $_POST['comment'], $data['id']]);
    }

    // Handle fines payment rejection
    if ($request['type'] == 'fines payment') {
        $data = json_decode($request['data'], true);
        if (!$data || !isset($data['ref_id'])) {
            throw new Exception("Invalid fine payment data");
        }
        saveData($db, "UPDATE special_fines SET status = 'Rejected' WHERE id = ?", [$data['ref_id']]);
    }
    
    // Update bank slip request status
    saveData($db, "UPDATE bank_slip_requests SET status = ?, comment = ? WHERE id = ?", ["Rejected", $_POST['comment'], $_POST['request_id']]);
    
    $db->commit();
    echo json_encode(['status' => true, "message" => "Bank slip recording process is aborted successfully"]);
    exit;
} catch (\Exception $e) {
    $db->rollback();
    echo json_encode(['status' => false, "message" => $e->getMessage()]);
    exit;
}
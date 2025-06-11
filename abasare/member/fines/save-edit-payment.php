<?php
header("Content-Type: application/json");
require_once "../../lib/db_function.php";

try {
    // Validate input
    $errors = [];
    $required = ['fine_id', 'amount', 'paid_at', 'ref_number', 'description', 'payment_id'];
    
    foreach($required as $field) {
        if(empty($_POST[$field])) {
            $errors[$field] = "This field is required";
        }
    }

    if(!is_numeric($_POST['amount']) || $_POST['amount'] <= 0) {
        $errors['amount'] = "Amount must be a positive number";
    }

    try {
        $paid_at = new DateTime($_POST['paid_at']);
    } catch(Exception $e) {
        $errors['paid_at'] = "Invalid date format";
    }

    if(!empty($errors)) {
        http_response_code(400);
        die(json_encode([
            'status' => false,
            'message' => 'Validation failed',
            'errors' => $errors
        ]));
    }

    $db->beginTransaction();
    
    $payment_data = [
        'member_id' => $_SESSION['acc'],
        'ref_id' => $_POST['fine_id'],
        'description' => $_POST['description'],
        'amount' => $_POST['amount'],
        'timestamp' => time()
    ];

    // Update existing payment without updated_at field
    $result = saveData($db, "UPDATE bank_slip_requests SET
                  amount = ?, data = ?, ref_number=?, paid_at = ?, status = 'Open'
                  WHERE id = ?", [
                      $_POST['amount'],
                      json_encode($payment_data),
                      $_POST['ref_number'],
                      $paid_at->format('Y-m-d'),
                      $_POST['payment_id']
                  ]);
    
    if(!$result) {
        throw new Exception("Failed to update payment record");
    }

    // Update fine status
    $result = saveData($db, "UPDATE special_fines SET 
                  status = 'Pending', 
                  fine_amount = ?,
                  date = ?,
                  reference_number = ?
                  WHERE id = ?", [
                      $_POST["amount"],
                      $paid_at->format('Y-m-d'),
                      $_POST['ref_number'],
                      $_POST['fine_id']
                  ]);
    
    if(!$result) {
        throw new Exception("Failed to update fine status");
    }

    $db->commit();
    echo json_encode(['status' => true, 'message' => 'Payment updated successfully']);
    
} catch(Exception $e) {
    if(isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    http_response_code(500);
    echo json_encode([
        'status' => false,
        'message' => $e->getMessage()
    ]);
    error_log("Payment Error: " . $e->getMessage());
}
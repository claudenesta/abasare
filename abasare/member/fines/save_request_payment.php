<?php
header("Content-Type: application/json");
require_once "../../lib/db_function.php";

// Validate CSRF token
if(empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    die(json_encode(['status' => false, 'message' => 'Invalid CSRF token']));
}

// Validate required fields
$required = ['fine_id', 'amount', 'paid_at', 'ref_number', 'description'];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        http_response_code(400);
        die(json_encode([
            'status' => false,
            'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required'
        ]));
    }
}

// Validate amount
if (!is_numeric($_POST['amount']) || $_POST['amount'] <= 0) {
    http_response_code(400);
    die(json_encode([
        'status' => false,
        'message' => 'Amount must be a positive number'
    ]));
}

// Validate date
try {
    $paid_at = new DateTime($_POST['paid_at']);
    if($paid_at > new DateTime()) {
        throw new Exception("Future date not allowed");
    }
} catch(Exception $e) {
    http_response_code(400);
    die(json_encode([
        'status' => false,
        'message' => 'Invalid payment date: ' . $e->getMessage()
    ]));
}

$db->beginTransaction();
try {
    // Check for duplicate reference number
    $refExists = first($db, "SELECT id FROM bank_slip_requests WHERE ref_number = ?", [$_POST['ref_number']]);
    if($refExists) {
        throw new Exception("Reference number already used");
    }

    // Check for existing payment for this fine
    $fineExists = first($db, "SELECT id FROM bank_slip_requests WHERE type = 'fines payment' AND JSON_EXTRACT(data, '$.ref_id') = ?", [$_POST['fine_id']]);
    if($fineExists) {
        throw new Exception("Payment already exists for this fine");
    }

    // Prepare payment data
    $payment_data = [
        'member_id' => $_SESSION['acc'],
        'amount' => $_POST['amount'],
        'ref_id' => $_POST['fine_id'],
        'description' => $_POST['description'],
        'month' => date('n'),
        'year' => date('Y'),
        'timestamp' => time()
    ];

    // Create payment request
    saveData($db, "INSERT INTO bank_slip_requests 
                  (member_id, type, ref_number, amount, data, paid_at, created_at) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)", 
        [
            $_SESSION['acc'],
            'fines payment',
            $_POST['ref_number'],
            $_POST['amount'],
            json_encode($payment_data),
            $paid_at->format('Y-m-d'),
            date("Y-m-d H:i:s")
        ]
    );

    // Update fine status
    saveData($db, "UPDATE special_fines SET 
                  status = 'Pending', 
                  reference_number = ?
                  WHERE id = ?", 
        [
            $_POST['ref_number'],
            $_POST['fine_id']
        ]
    );

    $db->commit();
    
    echo json_encode([
        'status' => true,
        'message' => 'Payment submitted successfully!',
        'reload' => true
    ]);
    
} catch(Exception $e) {
    $db->rollBack();
    http_response_code(500);
    echo json_encode([
        'status' => false,
        'message' => 'Payment error: ' . $e->getMessage()
    ]);
    error_log("Payment Error: " . $e->getMessage());
}

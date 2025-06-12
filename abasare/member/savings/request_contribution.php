<?php
header("Content-Type:application/json");
require_once "../../lib/db_function.php";

// Validate required fields
$required = ['ref_number', 'amount', 'paid_at', 'month_to_save_for', 'year_to_save_for'];
foreach($required as $field) {
    if(empty($_POST[$field])) {
        echo json_encode(['status'=>false, 'message'=>"Missing required field: $field"]);
        return;
    }
}

// Get selected month/year
$monthToSave = (int)$_POST['month_to_save_for'];
$yearToSave = (int)$_POST['year_to_save_for'];

// Validate month is not in past
$currentMonth = (int)date('n');
$currentYear = (int)date('Y');
if($yearToSave < $currentYear || ($yearToSave == $currentYear && $monthToSave < $currentMonth)) {
    echo json_encode(['status'=>false, 'message'=>"Cannot select past months"]);
    return;
}

// Validate payment date
$paymentDate = new DateTime($_POST['paid_at']);
$now = new DateTime();
if($paymentDate > $now) {
    echo json_encode(['status'=>false, 'message'=>"Payment date cannot be in future"]);
    return;
}

$db->beginTransaction();
try {
    // Get overdue settings for selected month
    $overdue = first($db, 
        "SELECT saving_overdue, id FROM overdue_settings 
         WHERE year = ? AND month = ?", 
        [$yearToSave, $monthToSave]
    );
    
    $overdueDate = $overdue['saving_overdue'] ?? date('Y-m-t', strtotime("$yearToSave-$monthToSave-01"));
    
    // Prepare data
    $data = [
        'member_id' => $_SESSION['acc'],
        'sav_amount' => $_POST['amount'],
        'month' => $monthToSave,
        'year' => $yearToSave,
        'overdue_date' => $overdueDate,
        'overdue_id' => $overdue['id'] ?? null
    ];

    // Save request
    saveData($db,
        "INSERT INTO bank_slip_requests 
         SET member_id = ?, type = 'savings', ref_number = ?, amount = ?, 
             data = ?, paid_at = ?, created_at = NOW(),
             month_to_save_for = ?, year_to_save_for = ?",
        [
            $_SESSION['acc'],
            $_POST['ref_number'],
            $_POST['amount'],
            json_encode($data),
            $_POST['paid_at'],
            $monthToSave,
            $yearToSave
        ]
    );

    $db->commit();
    echo json_encode(['status'=>true, 'message'=>'Request submitted successfully']);
} catch(Exception $e) {
    $db->rollback();
    echo json_encode(['status'=>false, 'message'=>$e->getMessage()]);
}
?>
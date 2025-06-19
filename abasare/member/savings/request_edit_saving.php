<?php
header("Content-Type: application/json");
require_once "../../lib/db_function.php";

// Validate required fields
$required = ['saving_id', 'ref_number', 'amount', 'month_to_save_for', 'year_to_save_for'];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        echo json_encode(['status' => false, 'message' => "Missing required field: $field"]);
        exit;
    }
}

// Get selected month/year
$monthToSave = (int) $_POST['month_to_save_for'];
$yearToSave = (int) $_POST['year_to_save_for'];

// Validate month is not in past
$currentMonth = (int) date('n');
$currentYear = (int) date('Y');
if ($yearToSave < $currentYear || ($yearToSave == $currentYear && $monthToSave < $currentMonth)) {
    echo json_encode(['status' => false, 'message' => "Cannot select past months"]);
    exit;
}

$db->beginTransaction();
try {
    $savingId = $_POST['saving_id'];
    
    // 1. Get existing saving record to verify and get original ref_number
    $existingSaving = first($db,
        "SELECT ref_number FROM saving 
         WHERE id = ? AND member_id = ? AND status = 'Rejected'",
        [$savingId, $_SESSION['user']['member_acc']]
    );
    
    if (!$existingSaving) {
        throw new Exception("Invalid saving record or not allowed to edit");
    }
    
    // 2. Update the saving record
    saveData(
        $db,
        "UPDATE saving SET
            sav_amount = ?,
            month_to_save_for = ?,
            year_to_save_for = ?,
            ref_number = ?,
            status = 'Pending',
            comment = ''
         WHERE id = ?",
        [
            $_POST['amount'],
            $monthToSave,
            $yearToSave,
            $_POST['ref_number'],
            $savingId
        ]
    );
    
    // 3. Update the existing bank slip request
    saveData(
        $db,
        "UPDATE bank_slip_requests SET
            amount = ?,
            month_to_save_for = ?,
            year_to_save_for = ?,
            ref_number=?,
            comment='',
            status = 'Open'
         WHERE ref_number = ? AND member_id = ? AND type = 'savings'",
        [
            $_POST['amount'],
            $monthToSave,
            $yearToSave,
            $_POST['ref_number'],
            $existingSaving['ref_number'],
            $_SESSION['user']['member_acc']
        ]
    );
    
    $db->commit();
    echo json_encode(['status' => true, 'message' => 'Savings contribution updated and resubmitted successfully']);
} catch (Exception $e) {
    $db->rollBack();
    echo json_encode(['status' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
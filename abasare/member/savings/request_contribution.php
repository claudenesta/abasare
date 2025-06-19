<?php
header("Content-Type:application/json");
require_once "../../lib/db_function.php";

// Validate required fields
$required = ['ref_number', 'amount', 'paid_at', 'month_to_save_for', 'year_to_save_for'];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        echo json_encode(['status' => false, 'message' => "Missing required field: $field"]);
        return;
    }
}

$is_edit = $_POST['is_edit'] === '1';
$ref_number = $_POST['ref_number'];

$monthToSave = (int) $_POST['month_to_save_for'];
$yearToSave = (int) $_POST['year_to_save_for'];

$currentMonth = (int) date('n');
$currentYear = (int) date('Y');
if ($yearToSave < $currentYear || ($yearToSave == $currentYear && $monthToSave < $currentMonth)) {
    echo json_encode(['status' => false, 'message' => "Cannot select past months"]);
    return;
}

$paymentDate = new DateTime($_POST['paid_at']);
$now = new DateTime();
if ($paymentDate > $now) {
    echo json_encode(['status' => false, 'message' => "Payment date cannot be in future"]);
    return;
}

$db->beginTransaction();
try {
    $overdue = first(
        $db,
        "SELECT saving_overdue, id FROM overdue_settings 
         WHERE year = ? AND month = ?",
        [$yearToSave, $monthToSave]
    );

    $overdueDate = $overdue['saving_overdue'] ?? date('Y-m-t', strtotime("$yearToSave-$monthToSave-01"));

    $data = [
        'member_id' => $_SESSION['acc'],
        'sav_amount' => $_POST['amount'],
        'month' => $monthToSave,
        'year' => $yearToSave,
        'overdue_date' => $overdueDate,
        'overdue_id' => $overdue['id'] ?? null
    ];

    if ($is_edit && $ref_number) {
        // Update existing bank slip request
        saveData(
            $db,
            "UPDATE bank_slip_requests 
             SET amount = ?, data = ?, paid_at = ?, status = 'Open', updated_at = NOW(),
                 month_to_save_for = ?, year_to_save_for = ?
             WHERE ref_number = ? AND member_id = ? AND status = 'Rejected'",
            [
                $_POST['amount'],
                json_encode($data),
                $_POST['paid_at'],
                $monthToSave,
                $yearToSave,
                $ref_number,
                $_SESSION['acc']
            ]
        );

        // Update saving table
        saveData(
            $db,
            "UPDATE saving 
             SET sav_amount = ?, done_at = NOW(), month_to_save_for = ?, year_to_save_for = ?
             WHERE ref_number = ? AND member_id = ?",
            [
                $_POST['amount'],
                $monthToSave,
                $yearToSave,
                $ref_number,
                $_SESSION['acc']
            ]
        );
    } else {
        // New request
        saveData(
            $db,
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

        saveData(
            $db,
            "INSERT INTO saving 
             SET member_id = ?, ref_number = ?, sav_amount = ?, 
                 done_at = NOW(), month_to_save_for = ?, year_to_save_for = ?",
            [
                $_SESSION['acc'],
                $_POST['ref_number'],
                $_POST['amount'],
                $monthToSave,
                $yearToSave
            ]
        );
    }

    $db->commit();
    echo json_encode(['status' => true, 'message' => 'Request ' . ($is_edit ? 'updated' : 'submitted') . ' successfully']);
} catch (Exception $e) {
    $db->rollback();
    echo json_encode(['status' => false, 'message' => $e->getMessage()]);
}
?>
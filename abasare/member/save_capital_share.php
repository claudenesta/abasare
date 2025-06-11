<?php

require_once('../DBController.php');

if (!isset($_SESSION['acc']) || empty($_SESSION['acc'])) {
    header("Location: /login.php");
    exit();
}

$member_id = (int)$_SESSION['acc'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type']; //                               e.g., 'savings' or 'loan payment'
    $ref_number = $_POST['reference_number'];
    $amount = floatval($_POST['paid_amount']);
    $paid_at = $_POST['paid_on'];

    // Insert as pending
    $stmt = mysqli_prepare($con, "INSERT INTO bank_slip_requests (member_id, type, ref_number, amount, paid_at, status, created_at) VALUES (?, ?, ?, ?, ?, 'pending', NOW())");
    mysqli_stmt_bind_param($stmt, "issds", $member_id, $type, $ref_number, $amount, $paid_at);
    mysqli_stmt_execute($stmt);

    header("Location: capital_share_info.php?success=1");
    exit();
}
?>
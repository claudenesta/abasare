<?php
include('../DBController.php');
include('menu.php');
$membe_id = $_SESSION['acc'];

// Check if ID is provided

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid request. No ID provided.");
}

$id = $_GET['id'];

// Fetch the record by ID

$sql_query = "SELECT * FROM overdue_settings WHERE id = ?";
$stmt = $db->prepare($sql_query);
$stmt->execute([$id]);
$setting = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$setting) {
    die("Record not found.");
}
if (!$setting) {
    die("Record not found.");
}

// Handle form submission

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql_update = "UPDATE overdue_settings 
                   SET month = ?, year = ?, saving_overdue = ?, payment_overdue = ?, bank_slip_overdue = ?, social_overdue = ? 
                   WHERE id = ?";
    $params = [
        $_POST['ukwezi'],
        $_POST['year'],
        $_POST['saving_overdue'],
        $_POST['payment_overdue'],
        $_POST['bank_slip_overdue'],
        $_POST['social_overdue'],
        $id
    ];

    // Execute the query
    $stmt = $db->prepare($sql_update);
    $result = $stmt->execute($params);

    if ($result) {
        // Redirect to the main page after a successful update
        header("Location: over_due_settings.php");
        exit;
    } else {
        // Debugging: Output the error if the query fails
        die("Update failed: " . print_r($stmt->errorInfo(), true));
    }
}

include('./header.php');
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1 style="color: green;">Edit Overdue Setting</h1>
        <ol class="breadcrumb">
            <li><a href="/accountant/"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="over_due_settings.php">Overdue Settings</a></li>
            <li class="active">Edit</li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Edit Overdue Setting</h3>
            </div>
            <form class="form-horizontal" method="post" action="">
                <div class="box-body">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Month</label>
                        <div class="col-sm-4">
                            <select name="ukwezi" class="form-control">
                                <?php formMonth($setting['month']); ?>
                            </select>
                        </div>
                        <label class="col-sm-2 control-label">Year</label>
                        <div class="col-sm-4">
                            <select name="year" class="form-control">
                                <?php formYear($setting['year']); ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Savings Overdue</label>
                        <div class="col-sm-4">
                            <input type="date" name="saving_overdue" class="form-control" value="<?= $setting['saving_overdue'] ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Loan Payment Overdue</label>
                        <div class="col-sm-4">
                            <input type="date" name="payment_overdue" class="form-control" value="<?= $setting['payment_overdue'] ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Social Saving Overdue</label>
                        <div class="col-sm-4">
                            <input type="date" name="social_overdue" class="form-control" value="<?= $setting['social_overdue'] ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Bank Slip Submission</label>
                        <div class="col-sm-4">
                            <input type="date" name="bank_slip_overdue" class="form-control" value="<?= $setting['bank_slip_overdue'] ?>" required>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="over_due_settings.php" class="btn btn-default">Cancel</a>
                </div>
            </form>
        </div>
    </section>
</div>

<?php include('./footer.php'); ?>
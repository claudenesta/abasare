<?php 
include('../DBController.php');
$membe_id = $_SESSION['acc'];
?>
<?php include('./header.php'); ?>
<!-- Left side column. contains the logo and sidebar -->

<?php 
$active = "dashboard";
include('./menu.php'); ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php
            $date = $currentyear."-".($currentmonth < 10 ?"0".$currentmonth:$currentmonth)."-01";
            $active_settings = new \DateTime($date);
            ?>
            Dashboard <?= $active_settings->format('F Y') ?><small>Overall Information on Single Screen</small>
        </h1>
        <ol class="breadcrumb">
            <li class="active"><i class="fa fa-dashboard"></i> Home</li>
        </ol>
    </section><br />
    <div class="col-md-12">
        <!-- ********** ALERT MESSAGE START******* -->
        <div class="col-md-12">
            <!-- ********** ALERT MESSAGE START******* -->

        </div> <!-- ********** ALERT MESSAGE END******* -->
    </div>
    <?php
    if ($_SESSION['role'] != 5) {
        ?>
        <div class="col-md-6">
            <!-- Application buttons -->
            <div class="box box-danger">

                <div class="box-body">
                    <a class="btn btn-app" href="/admin/loan_category.php">
                        <i class="fa fa-tags text-green"></i>Category</a>
                    <a class="btn btn-app" href="/admin/list.php">
                        <i class="fa fa-cube text-blue"></i> New Loan </a>
                    <a class="btn btn-app" href="/admin/add_member.php">
                        <i class="fa fa-user text-maroon"></i>Member </a>
                    <a class="btn btn-app" href="/admin/users.php">
                        <i class="fa fa-user text-green"></i> New User</a>
                    <a class="btn btn-app" href="/admin/list.php">
                        <i class="fa fa-calculator text-green"></i>Savings</a>

                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->

        <div class="col-md-6">
            <!-- Application buttons -->
            <div class="box box-danger">

                <div class="box-body">
                    <a class="btn btn-app" href="/admin/list.php">
                        <i class="fa fa-calculator text-red"></i> Pay Loan
                    </a>
                    <a class="btn btn-app" href="/admin/list.php">
                        <i class="fa fa-ravelry text-green"></i> Ongoing Loan</a>
                    <a class="btn btn-app" href="/admin/list.php">
                        <i class="fa fa-bandcamp text-aqua"></i>Overdue Loan </a>
                    <a class="btn btn-app" href="/admin/add_expense.php">
                        <i class="fa fa-star text-green"></i>Add Expences </a>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->




        <!-- Main content -->
        <section class="content">

            <!-- /.row -->
            <!-- Info boxes -->
            <div class="row">
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <a href="/admin/list.php">
                        <div class="info-box">
                            <span class="info-box-icon bg-red"><i class="fa fa-users"></i></span>
                            <div class="info-box-content">
                                <?php
                                $sql = "SELECT COUNT(*) AS members FROM member WHERE status = ?";
                                $members = returnSingleField($db, $sql, 'members', [1]);
                                ?>
                                <span class="">Members</span>
                                <span class="info-box-number"><?= number_format($members); ?></span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                    </a>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <a href="/admin/list.php">
                        <div class="info-box">
                            <span class="info-box-icon bg-aqua"><i class="fa fa-automobile"></i></span>
                            <div class="info-box-content">
                                <?php
                                $sql = "SELECT  SUM(a.sav_amount) AS saving_amount,
                                                SUM(a.fine) AS overdue_fines
                                                FROM saving AS a
                                                WHERE a.year = ?";
                                $savings_data = first($db, $sql, [$currentyear]);
                                ?>
                                <span class="">Saving in <?= $currentyear ?></span>
                                <span class="info-box-number"><?= number_format($savings_data["saving_amount"]); ?> Frws</span>
                                <span class="info-box-number"><span class="badge bg-yellow">Fine: <?= number_format($savings_data["overdue_fines"]); ?> Frws</span></span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                    </a> <!-- /.info-box -->
                </div>
                <!-- /.col -->

                <div class="col-md-3 col-sm-6 col-xs-12">
                    <a href="/admin/list.php">
                        <div class="info-box">
                            <span class="info-box-icon bg-orange"><i class="fa fa-suitcase"></i></span>

                            <div class="info-box-content">
                                <?php
                                $sql = "SELECT  SUM(a.loan_amount) AS amount,
                                                SUM(a.loan_amount_interest) AS interest
                                                FROM member_loans AS a
                                                WHERE a.status = ?";
                                $loan_info = first($db, $sql, ['ACTIVE']);
                                ?>
                                <span class="">Pending Loans as of <?= (new \DateTime())->format('Y-m-d') ?> </span>
                                <span class="info-box-number"><?= number_format($loan_info['amount']); ?> Frws</span>
                                <span class="info-box-number"><span class='badge bg-green'>Interest: <?= number_format($loan_info['interest']); ?> Frws</span></span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                    </a> <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <a href="/admin/list.php">
                        <div class="info-box">
                            <span class="info-box-icon bg-green"><i class="fa fa-check"></i></span>

                            <div class="info-box-content">
                                <?php
                                $sql = "SELECT SUM(a.loan_amount_interest) AS interest
                                                FROM member_loans AS a
                                                WHERE a.loan_date LIKE ?
                                                AND a.status = ?
                                                ";
                                $interest = returnSingleField($db, $sql, "interest", [$currentyear."%", "CLOSED"]);
                                $sql = "SELECT SUM(loan_amount_interest) AS toloan
                                                FROM member_loans
                                                WHERE MONTH(loan_date) = '$currentmonth'
                                                AND YEAR(loan_date) = '$currentyear' AND president_status=1 AND committee_status=1";
                                $query = $dbh->prepare($sql);
                                $query->execute();
                                $results = $query->fetch(PDO::FETCH_ASSOC);
                                ?>
                                <span class="">Recovered Interest of <?= $currentyear ?></span>
                                <span class="info-box-number"><?= number_format($interest); ?> Frws</span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                    </a> <!-- /.info-box -->
                </div>
                <!-- /.col -->

                <!-- fix for small devices only -->
                <div class="clearfix visible-sm-block"></div>

                <!-- /.col -->

            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-md-4">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Past Due Payments</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body ">
                            <ul class="products-list product-list-in-box">

                                <table class="table table-bordered table-responsive">
                                    <tr>
                                        <td>Sl.No</td>
                                        <td>Member Name</td>
                                        <td>Amount[RWF]</td>
                                    </tr>
                                    <tbody>
                                        <?php
                                        $now = new \DateTime();
                                        $sql1 = "SELECT a.amount,
                                                        CONCAT(c.fname, ' ', c.lname) AS name,
                                                        a.payment_sched AS due_date,
                                                        a.overdue_fine
                                                        FROM lend_payments AS a
                                                        INNER JOIN member_loans AS b
                                                        ON a.borrower_loan_id = b.id
                                                        INNER JOIN member AS c
                                                        ON a.borrower_id = c.id
                                                        WHERE a.amount > ? AND a.status = ? AND b.reject = ? AND b.committee_status = ? AND b.president_status = ?
                                                        AND a.payment_sched < ?
                                                        ORDER BY a.payment_sched DESC
                                                    ";
                                        $data = returnAllData($db, $sql1, [0, 'UNPAID', 0, 1,1, $now->format('Y-m-d') ]);
                                        $total_past_due = 0;
                                        $total_fines = 0;
                                        if(count($data) > 0){
                                            $counter = 1;
                                            foreach($data AS $member_loan){
                                                $total_past_due += $member_loan['amount'];
                                                $total_fines += $member_loan['overdue_fine'];
                                                ?>
                                                <tr>
                                                    <td><?= $counter++ ?></td>
                                                    <td>
                                                        <?= $member_loan['name'] ?><br />
                                                        <small>Due: <?= $member_loan['due_date'] ?> 
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                                                        Fine: <?= number_format($member_loan['overdue_fine']) ?> Rwf</small>
                                                    </td>
                                                    <td class="text-right"><?= number_format($member_loan['amount'])?></td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                        
                                        ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="2" class="text-right">Payments</th>
                                            <th class="text-right">
                                                <?= number_format($total_past_due) ?> RWF
                                            </th>
                                        </tr>
                                        <tr>
                                            <th colspan="2" class="text-right">Fines</th>
                                            <th class="text-right">
                                                <?= number_format($total_fines) ?> RWF
                                            </th>
                                        </tr>
                                        <tr>
                                            <th colspan="2" class="text-right">Total</th>
                                            <th class="text-right">
                                                <?= number_format($total_past_due + $total_fines) ?> RWF
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>


                            </ul>
                        </div>
                        <!-- /.box-body -->

                    </div>
                    <!-- /.box -->
                </div>
                <div class="col-md-4">
                    <div class="box box-primary">
                        <?php
                        $dateTime = new \DateTime();
                        $last_monday = clone $dateTime->modify(('Sunday' == $dateTime->format('l')) ? 'Monday last week' : 'Monday this week');
                        ?>
                        <div class="box-header with-border">
                            <h3 class="box-title">Due Payments This Week from <?= $last_monday->format('Y-m-d') ?></h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body ">
                            <ul class="products-list product-list-in-box">

                                <table class="table table-bordered table-responsive pay_due_all">
                                    <tr>
                                        <td>Sl.No</td>
                                        <td>Member Name</td>
                                        <td>Amount</td>
                                    </tr>
                                    <tbody style="">
                                        <?php
                                        $sql1 = "SELECT a.amount,
                                                        CONCAT(c.fname, ' ', c.lname) AS name,
                                                        a.payment_sched AS due_date,
                                                        a.overdue_fine
                                                        FROM lend_payments AS a
                                                        INNER JOIN member_loans AS b
                                                        ON a.borrower_loan_id = b.id
                                                        INNER JOIN member AS c
                                                        ON a.borrower_id = c.id
                                                        WHERE a.amount > ? AND a.status = ? AND b.reject = ? AND b.committee_status = ? AND b.president_status = ?
                                                        AND a.payment_sched < ? AND a.payment_sched >= ?
                                                        ORDER BY a.payment_sched DESC
                                                    ";
                                        $data = returnAllData($db, $sql1, [0, 'UNPAID', 0, 1,1, $now->format('Y-m-d'), $last_monday->format('Y-m-d') ]);
                                        $total_past_due = 0;
                                        $total_fines = 0;

                                        if(count($data) > 0){
                                            $counter = 1;
                                            foreach($data AS $row){
                                                $total_past_due += $row['amount'];
                                                $total_fines += $row['overdue_fine'];
                                                ?>
                                                    <tr>
                                                        <td><?= $counter++ ?></td>
                                                        <td>
                                                            <?= $row['name']; ?><br />
                                                            <small>Due: <?= $row['due_date'] ?> 
                                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                                                            Fine: <?= number_format($row['overdue_fine']) ?> Rwf</small>
                                                        </td>
                                                        <td class="text-right"><?= number_format($row['amount']); ?></td>
                                                    </tr>
                                                <?php 
                                            } 
                                        }
                                        ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="2" class="text-right">Payments</th>
                                            <th class="text-right">
                                                <?= number_format($total_past_due) ?> RWF
                                            </th>
                                        </tr>
                                        <tr>
                                            <th colspan="2" class="text-right">Fines</th>
                                            <th class="text-right">
                                                <?= number_format($total_fines) ?> RWF
                                            </th>
                                        </tr>
                                        <tr>
                                            <th colspan="2" class="text-right">Total</th>
                                            <th class="text-right">
                                                <?= number_format($total_past_due + $total_fines) ?> RWF
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>


                            </ul>
                        </div>
                        <!-- /.box-body -->

                    </div>
                    <!-- /.box -->
                </div>
                <div class="col-md-4">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Due Payments Today <?= $now->format('Y-m-d') ?></h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body ">
                            <ul class="products-list product-list-in-box">

                                <table class="table table-bordered table-responsive">
                                    <tr>
                                        <td>Sl.No</td>
                                        <td>Member Name</td>
                                        <td>Amount</td>
                                    </tr>
                                    <tbody style="">
                                        <?php
                                        $sql1 = "SELECT a.amount,
                                                        CONCAT(c.fname, ' ', c.lname) AS name,
                                                        a.payment_sched AS due_date,
                                                        a.overdue_fine
                                                        FROM lend_payments AS a
                                                        INNER JOIN member_loans AS b
                                                        ON a.borrower_loan_id = b.id
                                                        INNER JOIN member AS c
                                                        ON a.borrower_id = c.id
                                                        WHERE a.amount > ? AND a.status = ? AND b.reject = ? AND b.committee_status = ? AND b.president_status = ?
                                                        AND a.payment_sched = ?
                                                        ORDER BY a.payment_sched DESC
                                                    ";
                                        $data = returnAllData($db, $sql1, [0, 'UNPAID', 0, 1,1, $now->format('Y-m-d') ]);
                                        $total_past_due = 0;
                                        $total_fines = 0;

                                        if(count($data) > 0){
                                            $counter = 1;
                                            foreach($data AS $row){
                                                $total_past_due += $row['amount'];
                                                $total_fines += $row['overdue_fine'];
                                                ?>
                                                    <tr>
                                                        <td><?= $counter++ ?></td>
                                                        <td>
                                                            <?= $row['name']; ?><br />
                                                            <small>Due: <?= $row['due_date'] ?> 
                                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                                                            Fine: <?= number_format($row['overdue_fine']) ?> Rwf</small>
                                                        </td>
                                                        <td class="text-right"><?= number_format($row['amount']); ?></td>
                                                    </tr>
                                                <?php 
                                            } 
                                        }
                                        ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="2" class="text-right">Payments</th>
                                            <th class="text-right">
                                                <?= number_format($total_past_due) ?> RWF
                                            </th>
                                        </tr>
                                        <tr>
                                            <th colspan="2" class="text-right">Fines</th>
                                            <th class="text-right">
                                                <?= number_format($total_fines) ?> RWF
                                            </th>
                                        </tr>
                                        <tr>
                                            <th colspan="2" class="text-right">Total</th>
                                            <th class="text-right">
                                                <?= number_format($total_past_due + $total_fines) ?> RWF
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>


                            </ul>
                        </div>
                        <!-- /.box-body -->

                    </div>
                    <!-- /.box -->
                </div>
                <!-- /.col -->

                <!-- /.col -->
            </div>
        <?PHP } else { ?>

        </section>
        <section class="content">
            <div class="row">
                <!-- ********** ALERT MESSAGE START******* -->
                <div class="col-md-12">

                </div> <!-- ********** ALERT MESSAGE END******* -->
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header ">
                            <h3 class="box-title">&nbsp;</h3>
                            <?php
                            $sql = "select * from member where id='$membe_id'";
                            $query = mysqli_query($con, $sql);
                            $rows = mysqli_fetch_array($query);
                            ?>
                        </div>
                        <div class="box-body">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-3">
                                        <!-- Profile Image -->
                                        <div class="card card-primary card-outline">
                                            <div class="card-body box-profile">
                                                <div class="text-center">
                                                    <img class="profile-user-img img-fluid img-circle" src="membership/images/profile-default.png" alt="User profile picture">
                                                </div>

                                                <h3 class="profile-username text-center"><?php echo $rows['fname'] . " " . $rows['lname']; ?></h3>
                                                <p class="text-muted text-center"><?php echo $rows['job_title']; ?></p>

                                            </div>
                                            <div class="card card-primary">
                                                <!-- /.card-header -->
                                                <div class="card-body">
                                                    <hr>
                                                    <strong><i class="fa fa-plus mr-1"></i> Personal Info </strong>
                                                    <hr>
                                                    <p class="text"> First Name : <?php echo $rows['fname'];  ?> </p>
                                                    <p class="text"> Last Name : <?php echo $rows['lname'];  ?> </p>
                                                    <p class="text"> Middle Name : <?php echo $rows['mi'];  ?> </p>
                                                    <p class="text"> Date of Birth : <?php echo $rows['birth_date'];  ?> </p>
                                                    <p class="text"> Civil Status : <?php echo $rows['civil_status'];  ?> </p>
                                                    <hr>
                                                    <strong><i class="fa fa-plus mr-1"></i> Contact Info</strong>
                                                    <hr>
                                                    <p class="text"> Address : <?php echo $rows['address'];  ?> </p>
                                                    <p class="text"> Phone / Cellphone : <?php echo $rows['phone_cell'];  ?> </p>
                                                    <p class="text"> Email : <?php echo $rows['email'];  ?> </p>
                                                    <hr>
                                                    <strong><i class="fa fa-plus mr-1"></i> Current Employment Info </strong>
                                                    <hr>
                                                    <p class="text"> Employment Status : <?php echo $rows['employment_status'];  ?> </p>
                                                    <p class="text"> Company : <?php echo $rows['company'];  ?> </p>
                                                    <p class="text"> Job Title : <?php echo $rows['job_title'];  ?> </p>
                                                    <p class="text"> Monthly Income : <?php echo $rows['income'];  ?> Frws</p>
                                                </div>
                                                <!-- /.card-body -->
                                            </div>
                                            <!-- /.card-body -->
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="tab-content">
                                                    <div class="active tab-pane" id="activity">
                                                        <!-- Post -->
                                                        <div class="post">
                                                            <div class="user-block">
                                                                <span class="username">

                                                                </span>
                                                            </div>

                                                        </div>
                                                        <div class="post clearfix">

                                                            <div class="col-md-12">
                                                                <div class="box-body">
                                                                    <div class="col-md-12">
                                                                        <div class="box box-primary">
                                                                            <div class="box-header with-border">
                                                                                <?php
                                                                                $profit_sql = "SELECT sum(amount) as prit FROM `profite` where m_id='$membe_id'";
                                                                                $querprofile = mysqli_query($con, $profit_sql);
                                                                                $profi = mysqli_fetch_array($querprofile);
                                                                                ?>
                                                                                <h3> LOAN STATEMENT | <a class="btn btn-primary btn-flat btn-sm" href="loan_application.php"><i class="fa fa-plus"></i> Apply for Loan</a> </i>Individual interest</a> <a href="#" class="btn btn-primary"><?php echo number_format(ceil($profi['prit']), 2); ?> Frws</a></h3>

                                                                            </div>


                                                                            <!-- /.box-header -->
                                                                            <div class="box-body ">
                                                                                <ul class="products-list product-list-in-box">

                                                                                    <table class="table table-bordered table-responsive">
                                                                                        <tr>
                                                                                            <td>Sl.No</td>
                                                                                            <td>Loan</td>
                                                                                            <td>Loan Amount</td>
                                                                                            <td>Period( Month )</td>
                                                                                            <td>Status</td>
                                                                                        </tr>
                                                                                        <tbody>
                                                                                            <?php
                                                                                            $sql = "SELECT *, ml.id as ididi, CONCAT(lt.lname,' - ',lt.interest,'%') as name FROM `member_loans` ml inner join loan_type lt on lt.id=ml.loan_id where member_id='$membe_id'";
                                                                                            $quer = mysqli_query($con, $sql);
                                                                                            while ($row = mysqli_fetch_array($quer)) {
                                                                                            ?>
                                                                                                <tr style="color:<?php if ($row['status'] == "CLOSED") {
                                                                                                                        echo "red";
                                                                                                                    } ?>">
                                                                                                    <td><?php if ($row['accountant'] == 1 && $row['president'] == 1) { ?>
                                                                                                            <a href="membership/Loan_info.php?loan_id=<?php echo $row['ididi']; ?>"># - <?php echo $row['ididi']; ?></a>
                                                                                                        <?php } else { ?>
                                                                                                            <?php echo $row['ididi']; ?>
                                                                                                        <?php } ?>
                                                                                                    </td>
                                                                                                    <td><?php echo $row['name']; ?></td>
                                                                                                    <td><?php echo number_format(ceil($row['loan_amount']), 2); ?> Frw (s)</td>
                                                                                                    <td><?php echo $row['terms']; ?> Month( s )</td>
                                                                                                    <td><?php if ($row['accountant'] == 0 && $row['president'] == 0 && $row['reject'] == 0) { ?>
                                                                                                            <span style="color:#FFCC00"> Pending </span>
                                                                                                        <?php } elseif ($row['accountant'] == 0 && $row['president'] == 1  && $row['reject'] == 0) { ?>
                                                                                                            <span style="color:#FFCC00"> wait for Loan Committee </span>
                                                                                                        <?php } elseif ($row['accountant'] == 1 && $row['president'] == 0  && $row['reject'] == 0) { ?>
                                                                                                            <span style="color:#FFCC00"> wait for President(s) </span>
                                                                                                        <?php } elseif ($row['accountant'] == 1 && $row['president'] == 1  && $row['reject'] == 0) { ?>
                                                                                                            <span style="color:#008000	">Approved </span>
                                                                                                        <?php } elseif ($row['accountant'] == 0 or $row['president'] == 0 && $row['reject'] == 1) { ?>
                                                                                                            <span style="color:#FF0000">Rejected </span>
                                                                                                        <?php } ?>
                                                                                                    </td>
                                                                                                </tr>
                                                                                            <?php } ?>
                                                                                        </tbody>
                                                                                    </table>


                                                                                </ul>
                                                                            </div>
                                                                            <!-- /.box-body -->

                                                                        </div>
                                                                        <!-- /.box -->
                                                                    </div>


                                                                    <div class="col-md-12">
                                                                        <div class="box box-primary">
                                                                            <div class="box-header with-border">
                                                                                <h3 class="box-title" style="color:#008080"> CAPITAL SHARES ACCOUNT </h3>
                                                                            </div>
                                                                            <!-- /.box-header -->
                                                                            <div class="box-body ">
                                                                                <ul class="products-list product-list-in-box">
                                                                                    <table class="table table-bordered table-responsive">
                                                                                        <tr>
                                                                                            <td>Sl.No</td>
                                                                                            <td>Member Name</td>
                                                                                            <td>Amount</td>
                                                                                            <td>Date</td>

                                                                                        </tr>
                                                                                        <tbody>
                                                                                            <?php
                                                                                            $tot = 0;
                                                                                            $num = 0;
                                                                                            $month = 0;
                                                                                            $sql = "SELECT *,sum(s.amount) as gtotal, CONCAT(fname,' ',lname) as firstname,m.id as m_idi FROM `capital_share` s inner join member m on s.member_id=m.id where s.member_id='$membe_id' ORDER BY s.date DESC";
                                                                                            $quer = mysqli_query($con, $sql);
                                                                                            while ($row = mysqli_fetch_array($quer)) {
                                                                                                $month = (int)$row['month'];
                                                                                                $tot += $row['gtotal'];
                                                                                                $num++;

                                                                                            ?>
                                                                                                <tr>
                                                                                                    <td><a href="capital_share_info.php?m_idi=<?php echo $row['m_idi']; ?>">#-<?php echo $num; ?></a></td>
                                                                                                    <td><?php echo $row['firstname']; ?></td>
                                                                                                    <td><?php echo number_format(ceil($row['gtotal']), 2); ?> Frw (s)</td>
                                                                                                    <td><?php echo $row['date']; ?> </td>



                                                                                                </tr>
                                                                                            <?php } ?>
                                                                                            <tr>
                                                                                                <td colspan="6" style="color:#008080">Total: <?php echo number_format(ceil($tot), 2) . " Frws :" . ucwords(convertNumberToWord($tot)) . "Rwandan francs, On " . $long[$month] . " " . date('Y'); ?></td>
                                                                                            </tr>
                                                                                        </tbody>
                                                                                    </table>


                                                                                </ul>
                                                                            </div>
                                                                            <!-- /.box-body -->

                                                                        </div>
                                                                        <!-- /.box -->
                                                                    </div>

                                                                    <!-- / Savings table-->

                                                                    <div class="col-md-12">
                                                                        <div class="box box-primary">
                                                                            <div class="box-header with-border">
                                                                                <h3 class="box-title" style="color:#008080"> SAVING STATEMENT <?php echo date("Y/m/d"); ?></h3>
                                                                                <?php
                                                                                $sql = "SELECT *,sum(s.sav_amount) as gtotal, CONCAT(fname,' ',lname) as firstname FROM `saving` s inner join member m on s.member_id=m.id where s.member_id='$membe_id' GROUP BY s.year ORDER BY s.year DESC";
                                                                                echo $sql;
                                                                                ?>
                                                                            </div>
                                                                            <!-- /.box-header -->
                                                                            <div class="box-body ">
                                                                                <ul class="products-list product-list-in-box">
                                                                                    <table class="table table-bordered table-responsive">
                                                                                        <tr>
                                                                                            <td>Sl.No</td>
                                                                                            <td>Member Name</td>
                                                                                            <td>Amount</td>
                                                                                            <td>Year</td>
                                                                                        </tr>
                                                                                        <tbody>
                                                                                            <?php
                                                                                            $tot = 0;
                                                                                            $num = 0;
                                                                                            $month = 0;
                                                                                            $quer = mysqli_query($con, $sql);
                                                                                            while ($row = mysqli_fetch_array($quer)) {
                                                                                                $month = (int)$row['month'];
                                                                                                $tot += $row['gtotal'];
                                                                                                $num++;

                                                                                            ?>
                                                                                                <tr>
                                                                                                    <td><a href="savings_info.php?m_id=<?php echo $membe_id; ?>&&year=<?php echo $row['year']; ?>">#-<?php echo $num; ?></a></td>
                                                                                                    <td><?php echo $row['firstname']; ?></td>
                                                                                                    <td><?php echo number_format(ceil($row['gtotal']), 2); ?> Frw (s)</td>
                                                                                                    <td><?php echo $row['year']; ?> </td>
                                                                                                </tr>
                                                                                            <?php } ?>
                                                                                            <tr>
                                                                                                <td colspan="5" style="color:#008080">Total: <?php echo number_format(ceil($tot), 2) . " Frws :" . ucwords(convertNumberToWord($tot)) . "Rwandan francs, On " . $long[$month] . " " . date('Y'); ?></td>
                                                                                            </tr>
                                                                                        </tbody>
                                                                                    </table>





                                                                                    <!-- SOCIAL SAVING STATEMT-->



                                                                                    <div class="box-header with-border">
                                                                                        <h3 class="box-title" style="color:#FF00FF"> SOCIAL SAVING STATEMENT </h3>
                                                                                    </div>
                                                                                    <!-- /.box-header -->
                                                                                    <div class="box-body ">
                                                                                        <ul class="products-list product-list-in-box">

                                                                                            <table class="table table-bordered table-responsive">
                                                                                                <tr>
                                                                                                    <td>Sl.No</td>
                                                                                                    <td>Member Name</td>
                                                                                                    <td>Amount</td>
                                                                                                    <td>Year</td>
                                                                                                    <td>Action</td>


                                                                                                </tr>
                                                                                                <tbody>
                                                                                                    <?php
                                                                                                    $tot = 0;
                                                                                                    $num = 0;
                                                                                                    $month = 0;
                                                                                                    $sql = "SELECT  a.amount AS gtotal,
                                                            CONCAT(fname,' ',lname) as firstname,
                                                            CONCAT(a.year, '-', IF(a.month<10,0,''), a.month) AS formatted_month,
                                                            b.id as m_idi,
                                                            a.month,
                                                            a.year,a.id
                                                            FROM sacial_saving AS a
                                                            INNER JOIN member AS b
                                                            ON a.m_id = b.id
                                                            WHERE a.m_id = '{$membe_id}'
                                                            ORDER BY formatted_month ASC
                                                            ";
                                                                                                    // echo $sql;
                                                                                                    // $sql = "SELECT *,sum(s.sav_amount) as gtotal, CONCAT(fname,' ',lname) as firstname, s.member_id as m_idi  FROM `saving` s inner join member m on s.member_id=m.id where s.member_id='$membe_id' GROUP BY s.year ORDER BY s.year DESC";
                                                                                                    $quer = mysqli_query($con, $sql);
                                                                                                    while ($row = mysqli_fetch_array($quer)) {
                                                                                                        $month = (int)$row['month'];
                                                                                                        $tot += $row['gtotal'];
                                                                                                        $num++;
                                                                                                    ?>
                                                                                                        <tr>
                                                                                                            <td><?php echo $num; ?></td>
                                                                                                            <td><?php echo $row['firstname']; ?></td>
                                                                                                            <td><?php echo number_format(ceil($row['gtotal']), 2); ?> Frw (s)</td>
                                                                                                            <td><?php echo $row['formatted_month']; ?> </td>
                                                                                                            <!--<td> <a href="delete_social.php?id=<?= $row['id'] ?>&m_id=<?= $row['m_idi'] ?>">Delete/Edit</a></td> -->
                                                                                                        </tr>
                                                                                                    <?php
                                                                                                    }
                                                                                                    ?>
                                                                                                    <tr>
                                                                                                        <td colspan="6" style="color:#008080">Total: <?php echo number_format(ceil($tot), 2) . " Frws :" . ucwords(convertNumberToWord($tot)) . "Rwandan francs" ?></td>
                                                                                                    </tr>
                                                                                                </tbody>
                                                                                            </table>



                                                                                            <!-- SOCIAL SAVING STATEMT-->


                                                                                            <!--<li><a href="<?php echo WEB_URL; ?>sacial_saving_report.php"><i class="fa fa-list"></i> <span style="color:green"> Click here to check Social Saving Statement</span> <span style="color:orange">(You need to check your name in the report for each month)</span> </p></a></li>-->

                                                                                        </ul>
                                                                                    </div>
                                                                                    <!-- /.box-body -->

                                                                            </div>
                                                                            <!-- /.box -->
                                                                        </div>




                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
        </section>




    <?php } ?>


    <!-- /.content -->
</div>

<!-- /.content-wrapper -->
<?php include('./footer.php'); ?>

</body>

</html>
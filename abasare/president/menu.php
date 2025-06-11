<aside class="main-sidebar">
    <section class="sidebar">
        <!-- User Online -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="/<?= $_SESSION['photo'] ?>" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p><?php echo $row['name']; ?></p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>
        <!-- /.online end-->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" data-widget="tree">
            <li class="<?= $active == "loans" ? "active " : "" ?>">
                <a href="/president/"> <i class="fa fa-dashboard"></i> <span>Loan Request</span></a>
            </li>
            <li class="<?= $active == "approved_loans" ? "active " : "" ?>">
                <a href="/president/approved_loans.php"> <i class="fa fa-check-circle"></i> <span>Approved Loan</span></a>
            </li>



            <div>

            <?php if (!is_null($_SESSION['acc'] == 3)) { ?>
            <li class="<?= $active == "member-dashboard" ? "active " : "" ?>">
            <a href="/president/member.php" style="color: yellow;"><i class="fa fa-dashboard"></i> <span>Dashboard as member</span></a>
            </li>
            
            <?php } 
            ?>
        </div>

            <!-- Membership Section -->
            <li class="treeview <?= in_array($active, ['new-member', 'member-list-inactive', 'member-list-active', 'saving-report']) ? "menu-open" : "" ?>">
                <a href="#">
                    <i class="fa fa-users"></i> <span>Membership</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu" style="<?= in_array($active, ['new-member', 'member-list-inactive', 'member-list-active', 'saving-report']) ? "display:block" : "" ?>">
                    <li class="<?= $active == "new-member" ? "active" : "" ?>">
    <a href="/president/add_member.php"><i class="fa fa-plus-square-o"></i> New Member</a></li>
    <li class="<?= $active == "member-list-inactive" ? "active" : "" ?>">
    <a href="/president/list.php?stat=0"><i class="fa fa-list"></i> Inactive Member</a></li>
    <li class="<?= $active == "member-list-active" ? "active" : "" ?>">
    <a href="/president/list.php?stat=1"><i class="fa fa-list"></i> Active Member</a></li>
    <li class="<?= $active == "saving-report" ? "active" : "" ?>">
    <a href="/president/saving_report.php"><i class="fa fa-list"></i> Savings Report</a></li>
    </ul>
    </li>

            <!-- General Ledger Section -->
            <li class="treeview <?= in_array($active, ['ledger-setup', 'general-report', 'loan-status', 'monthly-loan-status']) ? "menu-open" : "" ?>">
                <a href="#">
                    <i class="fa fa-book"></i> <span>General Ledger</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu" style="<?= in_array($active, ['ledger-setup', 'general-report', 'loan-status', 'monthly-loan-status']) ? "display:block" : "" ?>">
    <li class="<?= $active == "ledger-setup" ? "active" : "" ?>">
    <a href="/president/ledger_setup.php"><i class="fa fa-cogs"></i> Ledger Setup</a></li>
    <li class="<?= $active == "general-report" ? "active" : "" ?>">
    <a href="/president/general_report.php"><i class="fa fa-bar-chart"></i> General Report</a></li>
    <li class="<?= $active == "loan-status" ? "active" : "" ?>">
    <a href="/president/loan_status_report.php"><i class="fa fa-list"></i> Loan Status</a></li>
    <li class="<?= $active == "monthly-loan-status" ? "active" : "" ?>">
    <a href="/president/monthly_loan_status.php"><i class="fa fa-calendar"></i> Monthly Loan Status</a></li>
            </ul>
            </li>

  <!-- Expenses Section -->

   <li class="treeview <?= in_array($active, ['expenses', 'add-expense']) ? "menu-open" : "" ?>">
    <a href="#">
       <i class="fa fa-money"></i> <span>Expenses</span>
            <span class="pull-right-container">
        <i class="fa fa-angle-left pull-right"></i>
            </span>
                </a>
                <ul class="treeview-menu" style="<?= in_array($active, ['expenses', 'add-expense']) ? "display:block" : "" ?>">
                    <li class="<?= $active == "add-expense" ? "active" : "" ?>">
                        <a href="/admin/add_expense.php"><i class="fa fa-plus"></i> Add Expense</a>
                    </li>
                    <li class="<?= $active == "expenses" ? "active" : "" ?>">
                        <a href="/admin/expense.php"><i class="fa fa-list"></i> Expense List</a>
                    </li>
                </ul>
            </li>

            <!-- Users Section -->
            <li class="treeview <?= in_array($active, ['users', 'roles']) ? "menu-open" : "" ?>">
                <a href="#">
                    <i class="fa fa-user"></i> <span>Users</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu" style="<?= in_array($active, ['users', 'roles']) ? "display:block" : "" ?>">
                    <li class="<?= $active == "users" ? "active" : "" ?>">
                        <a href="/admin/users.php"><i class="fa fa-user-plus"></i> Add User</a>
                    </li>
                    <li class="<?= $active == "roles" ? "active" : "" ?>">
                        <a href="/admin/roles_view.php"><i class="fa fa-users"></i> Role List</a>
                    </li>
                </ul>
            </li>
        </ul>

        <!-- Member Dashboard -->

    </section>
    <!-- /.sidebar -->
</aside>

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
        <li class="<?= $active == "dashboard"?"active ":"" ?>">
          <a href="/accountant/"> <i class="fa fa-dashboard"></i> <span>Dashboard</span></a>
        </li>
        <?php
        if(!is_null($_SESSION['acc'])){
          ?>
          <li class="<?= $active == "member-dashboard"?"active ":"" ?>">
            <a href="/accountant/member.php"> <i class="fa fa-dashboard"></i> <span>Member Dashboard</span></a>
          </li>
          <?php
        }
        ?>
        <li class="<?= $active == "over-due-setting"?"active ":"" ?>">
          <a href="/accountant/over_due_settings.php"> <i class="fa fa-minus-circle"></i> <span>Over Due Settings</span></a>
        </li>
        <li class="<?= $active == "bank-slip"?"active ":"" ?>">
          <a href="/accountant/bankslip.php"> <i class="fa fa-bank"></i> <span>Bank Slip</span></a>
        </li>
        <li class="<?= $active == "fines"?"active ":"" ?>">
          <a href="/accountant/fines.php"> <i class="fa fa-question-circle-o"></i> <span>Fines</span></a>
        </li>

        <?php
        if(!is_null($_SESSION['acc'])){
          /*
          ?>
          <li class="treeview <?= in_array($active, ['savings', 'loans_payments','social', 'fines'])?" active ":"" ?>">
            <a href="#">
              <i class="fa fa-users"></i> <span>Payments</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li class="<?= $active == "savings"?"active ":"" ?>"><a href="/member/savings.php"><i class="fa fa-plus-square-o"></i>Savings</a></li>
              <?php
              //check if the member has any pending payment schedule which is configured to be overder or any pending emergency loan payment
              $member_loans_counter = returnSingleField($db, "SELECT  COUNT(a.id) AS activeLoans
                                                  FROM member_loans AS a
                                                  WHERE a.member_id = ?
                                                  AND a.status = ?
                                                  AND a.committee_status = ?
                                                  ", "activeLoans", [$_SESSION['acc'], "ACTIVE", 1]);
              if($member_loans_counter > 0){
                ?>
                <li class="<?= $active == "loans_payments"?"active ":"" ?>"><a href="/member/loan_payments.php"><i class="fa fa-list"></i>Loan Payment</a></li>
                <?php
              }
              ?>
              <li class="<?= $active == "social"?"active ":"" ?>"><a href="/member/social_savings.php?stat=1"><i class="fa fa-users"></i>Social</a></li>
              <?php
              $has_fines = returnSingleField($db, "SELECT COUNT(id) AS fines FROM special_fines WHERE member_id = ?", "fines", [$_SESSION['acc']]);
              if($has_fines > 0){
                ?>
                <li class="<?= $active == "fines"?"active ":"" ?>"><a href="/member/fines.php"><i class="fa fa-question"></i>Special Fines <span class="badge bg-red"><?= $has_fines ?></span></a></li>
                <?php
              }
              ?>
            </ul>
          </li>
          <li class="<?= $active == "loans"?"active ":"" ?>">
            <a href="/member/loans.php"><i class="fa fa-book"></i> <span>Loans</span></a>
          </li> 
          <li class="<?= $active == "signatory"?"active ":"" ?>">
            <a href="/member/signatory.php"><i class="fa fa-thumbs-up"></i> <span>Signatories</span></a>
          </li>
          <?php
          */
        }
        ?>
  <li class="treeview <?= in_array($active, ['new-member', 'member-list-inactive', 'member-list-active', 'saving-report'])?"menu-open":"" ?>">
        <a href="#">
          <i class="fa fa-users"></i> <span>Membership</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu" style="<?= in_array($active, ['new-member', 'member-list-inactive', 'member-list-active', 'saving-report'])?"display:block":"" ?>">
        
        

         
        
        <?php
          if ($_SESSION['role'] ==2) {
          ?>
            <li class="<?= $active == "member-list-inactive"?"active":"" ?>"><a href="/accountant/list.php?stat=0"><i class="fa fa-list"></i>inactive Member </a></li>
            <li class="<?= $active == "member-list-active"?"active":"" ?>"><a href="/accountant/list.php?stat=1"><i class="fa fa-list"></i>Active Member</a></li>
            <li class="<?= $active == "saving-report"?"active":"" ?>"><a href="/accountant/saving_report.php"><i class="fa fa-list"></i>Savings Report</a></li>

          <?php
          } ?>
        </ul>
      </li>


      <?php
      if ($_SESSION['role'] == 2) {
      ?>
        <li class="treeview <?= in_array($active, ['ledger-setup', 'general-report'])?"menu-open":"" ?>">
          <a href="#">
            <i class="fa fa-users"></i> <span>General Ledger (GL) </span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu" style="<?= in_array($active, ['ledger-setup','general-report'])?"display:block":"" ?>">
            <li class="<?= $active == "ledger-setup"?"active":"" ?>"><a href="/accountant/legder_setup.php"><i class="fa fa-list"></i> Loan Types Setting </a></li>
            <li class="<?= $active == "general-report"?"active":"" ?>"><a href="/accountant/general_report.php"><i class="fa fa-list"></i> General Report </a></li>
            <li><a href="/accountant/loan_status_report.php"><i class="fa fa-list"></i>  Sum of Loan Status </a></li>
            <li><a href="/accountant/monthly_loan_status.php"><i class="fa fa-list"></i>Monthly Loan Status </a></li>
            <li><a href="/accountant/monthly_loan_payment_report.php"><i class="fa fa-list"></i>Monthly Payment Report</a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#">
            <i class="fa fa-minus-circle text-aqua"></i> <span>Expenses</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="/admin/add_expense.php"><i class="fa fa-plus-square-o"></i>New Expense </a></li>
            <li><a href="/admin/expense.php"><i class="fa fa-list"></i>Expenses List </a></li>

          </ul>
        </li>

      <?php
      }
      if ($_SESSION['role'] == 1) {
      ?>
        <li class="treeview">
          <a href="#">
            <i class="fa fa-users"></i> <span>Users</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="/admin/users.php"><i class="fa fa-plus-square-o"></i>New User </a></li>
            <li><a href="/admin/users_view.php"><i class="fa fa-book"></i>Users List </a></li>
            <li><a href="/admin/roles_view.php"><i class="fa fa-book"></i>Role List </a></li>

          </ul>
        </li>  
        <?php
        } ?>
    


        <li class="<?= $active == "interest"?"active ":"" ?>">
          <a href="/accountant/interest.php"> <i class="fa fa-check-circle"></i> <span>Shared Interest</span></a>
        </li>
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>
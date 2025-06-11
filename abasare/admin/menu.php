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
        <a href="/admin/"> <i class="fa fa-dashboard"></i> <span>Dashboard</span></a>
      </li>



 <li class="treeview <?= in_array($active, ['new-member', 'member-list-inactive', 'member-list-active', 'saving-report'])?"menu-open":"" ?>">
        <a href="#">
          <i class="fa fa-users"></i> <span>Membership</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu" style="<?= in_array($active, ['new-member', 'member-list-inactive', 'member-list-active', 'saving-report'])?"display:block":"" ?>">
          <?php
          if ($_SESSION['role'] == 1) {
          ?>
            <li class="<?= $active == "new-member"?"active":"" ?>"><a href="/admin/add_member.php"><i class="fa fa-plus-square-o"></i>New Member</a></li>
            <li class="<?= $active == "member-list-inactive"?"active":"" ?>"><a href="/admin/list.php?stat=0"><i class="fa fa-list"></i>inactive Member </a></li>
            <li class="<?= $active == "member-list-active"?"active":"" ?>"><a href="/admin/list.php?stat=1"><i class="fa fa-list"></i>Active Member</a></li>
            <li class="<?= $active == "saving-report"?"active":"" ?>"><a href="/admin/saving_report.php"><i class="fa fa-list"></i>Savings Report</a></li>
          <?php
          } ?>
        </ul>
      </li>

      <?php
      if ($_SESSION['role'] == 1) {
      ?>
        <li class="treeview <?= in_array($active, ['ledger-setup', 'general-report'])?"menu-open":"" ?>">
          <a href="#">
            <i class="fa fa-users"></i> <span>General Ledger (GL) </span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu" style="<?= in_array($active, ['ledger-setup','general-report'])?"display:block":"" ?>">
            <li class="<?= $active == "ledger-setup"?"active":"" ?>"><a href="/admin/legder_setup.php"><i class="fa fa-list"></i> Loan Types Setting </a></li>
            <li class="<?= $active == "general-report"?"active":"" ?>"><a href="/admin/general_report.php"><i class="fa fa-list"></i> General Report </a></li>
            <li><a href="/admin/loan_status_report.php"><i class="fa fa-list"></i> Loan Status </a></li>
            <li><a href="/admin/monthly_loan_status.php"><i class="fa fa-list"></i>Monthly Loan Status </a></li>
            <li><a href="/admin/monthly_loan_payment_report.php"><i class="fa fa-list"></i>Monthly Payment Report</a></li>
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
      }
      if ($_SESSION['role'] == 1 || $_SESSION['role'] == 7) {
      ?>
        <li class="treeview <?= in_array($active, ['social-saving', 'social-report'])?"menu-open":"" ?>">
          <a href="#">
            <i class="fa fa-users"></i> <span>Social saving information</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu" style="<?= in_array($active, ['social-saving','social-report'])?"display:block":"" ?>">
            <li class="<?= $active == "social-saving"?"active":"" ?>"><a href="/admin/sacial_saving.php"><i class="fa fa-list"></i>Social Saving</a></li>
            <li class="<?= $active == "social-report"?"active":"" ?>"><a href="/admin/sacial_saving_report.php"><i class="fa fa-list"></i>Social Saving Report</a></li>
          </ul>
        </li>

      <?php
      }
      ?>
    </ul>
  </section>
  <!-- /.sidebar -->
</aside>
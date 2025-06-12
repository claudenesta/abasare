<aside class="main-sidebar">
    <section class="sidebar">
      <!-- User Online -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="/<?= $_SESSION['photo'] ?>" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p><?php echo $_SESSION['user']['name']; ?></p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>
      <!-- /.online end-->
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="<?= $active == "dashboard"?"active ":"" ?>">
          <a href="/member/"> <i class="fa fa-dashboard"></i> <span>Dashboard</span></a>
        </li>

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
                                                ", "activeLoans", [$_SESSION['acc'], "ACTIVE"]);
            if($member_loans_counter > 0){
              ?>
              <li class="<?= $active == "loans_payments"?"active ":"" ?>"><a href="/member/loan_payments.php"><i class="fa fa-list"></i>Loan Payment</a></li>
              <?php
            }
            ?>
            <li class="<?= $active == "social"?"active ":"" ?>"><a href="/member/social_savings.php?stat=1"><i class="fa fa-users"></i>Social</a></li>
              <?php
             $has_active_fines = returnSingleField($db, "SELECT COUNT(id) AS fines FROM special_fines WHERE member_id = ? AND status = 'Active'", "fines", [$_SESSION['acc']]);
             $has_fines = returnSingleField($db, "SELECT COUNT(id) AS fines FROM special_fines WHERE member_id = ?", "fines", [$_SESSION['acc']]);
              if($has_fines > 0){
                ?>
                <li class="<?= $active == "fines"?"active ":"" ?>"><a href="/member/fines.php"><i class="fa fa-question"></i>Special Fines <span class="badge bg-red"><?= $has_active_fines ?></span></a></li>
                <?php
              }
              ?> 
          </ul>
        </li>


        <li class="<?= $active == "loans"?"active ":"" ?>">
          <a href="/member/loans.php"><i class="fa fa-book"></i> <span>Loans</span></a>
        </li> 

            <li class="<?= $active == "capital_share"?"active ":"" ?>"><a href="/member/capital_share_info.php"><i class="fa fa-list"></i>Capital Share </a></li>
        
        <li class="<?= $active == "signatory"?"active ":"" ?>">
          <a href="/member/signatory.php"><i class="fa fa-thumbs-up"></i> <span>Signatories</span></a>
        </li> 

      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>

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
        <li class="<?= $active == "loans"?"active ":"" ?>">
          <a href="/committee/"> <i class="fa fa-dashboard"></i> <span>Loan Request</span></a>
        </li>
        <li class="<?= $active == "approved_loans"?"active ":"" ?>">
          <a href="/committee/approved_loans.php"> <i class="fa fa-check-circle"></i> <span>Approved Loan</span></a>
        </li>
        <?php /*<li class="<?= $active == "rejected_loans"?"active ":"" ?>">
          <a href="/committee/rejected_loans.php"> <i class="fa fa-dashboard"></i> <span>Rejected Loan</span></a>
        </li> */ ?>

        <?php
        if(!is_null($_SESSION['acc'])){
          ?>
          <li class="<?= $active == "member-dashboard"?"active ":"" ?>">
            <a href="/committee/member.php"> <i class="fa fa-dashboard"></i> <span>Member Dashboard</span></a>
          </li>
          <?php
        }
        ?>
        

      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>
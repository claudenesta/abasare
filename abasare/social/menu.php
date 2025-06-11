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
          <a href="/social/"> <i class="fa fa-dashboard"></i> <span>Dashboard</span></a>
        </li>
        <li class="<?= $active == "bank-slip"?"active ":"" ?>">
          <a href="/social/bankslip.php"> <i class="fa fa-bank"></i> <span>Bank Slip</span></a>
        </li>
        <!-- <li class="<?= $active == "over-due-setting"?"active ":"" ?>">
          <a href="/social/over_due_settings.php"> <i class="fa fa-minus-circle"></i> <span>Over Due Settings</span></a>
        </li>
        <li class="<?= $active == "fines"?"active ":"" ?>">
          <a href="/social/fines.php"> <i class="fa fa-question-circle-o"></i> <span>Fines</span></a>
        </li>

        <li class="<?= $active == "interest"?"active ":"" ?>">
          <a href="/social/interest.php"> <i class="fa fa-check-circle"></i> <span>Shared Interest</span></a>
        </li> -->
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>
<aside class="main-sidebar">
    <section class="sidebar">
      <!-- User Online -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="<?php echo WEB_URL; ?>images/user_logo.png" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p><?php echo $row['name']; ?></p>
          <a href="#"><i class="fa fa-circle text-success"></i>Online</a>
        </div>
      </div>
      <!-- /.online end-->
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="active treeview">
          <a href="<?php echo WEB_URL;?>dashboard.php"> <i class="fa fa-dashboard"></i> <span>Dashboard</span></a>
        </li>

           <li class="treeview">
          <a href="#">
            <i class="fa fa-users"></i> <span>Membership</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
          <?php
         if($_SESSION['role']==1 || $_SESSION['role']==3 || $_SESSION['role']==4 || $_SESSION['role']==2 || $_SESSION['role']==6){
        
         ?>
            <li><a href="<?php echo WEB_URL; ?>membership/add_member.php"><i class="fa fa-plus-square-o"></i>New Member</a></li>
            <li><a href="<?php echo WEB_URL; ?>membership/list.php?stat=0"><i class="fa fa-list"></i>inactive Member </a></li>
            <li><a href="<?php echo WEB_URL; ?>membership/list.php?stat=1"><i class="fa fa-list"></i>Active Member</a></li>
            <!--<li><a href="<?php echo WEB_URL; ?>sacial_saving.php"><i class="fa fa-list"></i>Social Saving</a></li>
            <li><a href="<?php echo WEB_URL; ?>sacial_saving_report.php"><i class="fa fa-list"></i>Social Saving Report</a></li> -->
            <li><a href="<?php echo WEB_URL; ?>saving_report.php"><i class="fa fa-list"></i>Savings Report</a></li>
             <?php }elseif($_SESSION['role']==6){?>
              <li><a href="<?php echo WEB_URL; ?>membership/list.php"><i class="fa fa-list"></i>Member List</a></li>
              
             <?php } ?>
          </ul>
        </li>
       
        <?php
         if($_SESSION['role']==2 || $_SESSION['role']==1 || $_SESSION['role']==3 ){
         ?>
             <li class="treeview">
          <a href="#">
            <i class="fa fa-users"></i> <span>General Ledger (GL) </span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo WEB_URL; ?>legder_setup.php"><i class="fa fa-list"></i> Ledger Setup </a></li>
            <li><a href="<?php echo WEB_URL; ?>general_report.php"><i class="fa fa-list"></i> General Report </a></li>
            <li><a href="<?php echo WEB_URL; ?>loan_status_report.php"><i class="fa fa-list"></i> Loan Status </a></li>
            <li><a href="<?php echo WEB_URL; ?>monthly_loan_status.php"><i class="fa fa-list"></i>Monthly Loan Status </a></li>
            <li><a href="<?php echo WEB_URL; ?>monthly_loan_payment_report.php"><i class="fa fa-list"></i>Monthly Payment Report</a></li>
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
            <li><a href="<?php echo WEB_URL; ?>expenses/add.php"><i class="fa fa-plus-square-o"></i>New Expense </a></li>
            <li><a href="<?php echo WEB_URL; ?>expenses/expense.php"><i class="fa fa-list"></i>Expenses List </a></li>
            
          </ul>
        </li>
        
        <?php
         }
         if($_SESSION['role']==1 || $_SESSION['role']==3  ){
         ?>
       <li class="treeview">
          <a href="#">
            <i class="fa fa-users"></i> <span>Users</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo WEB_URL; ?>users/user.php"><i class="fa fa-plus-square-o"></i>New User </a></li>
            <li><a href="<?php echo WEB_URL; ?>users/view.php"><i class="fa fa-book"></i>Users List </a></li>
            <li><a href="<?php echo WEB_URL; ?>roles/view.php"><i class="fa fa-book"></i>Role List </a></li>
            
          </ul>
        </li>
        
        
        <?php
         }
         if($_SESSION['role']==7 || $_SESSION['role']==1){
         ?>
         <li class="treeview">
          <a href="#">
            <i class="fa fa-users"></i> <span>Social saving information</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo WEB_URL; ?>sacial_saving.php"><i class="fa fa-list"></i>Social Saving</a></li>
            <li><a href="<?php echo WEB_URL; ?>sacial_saving_report.php"><i class="fa fa-list"></i>Social Saving Report</a></li> 
            
            
          </ul>
        </li>
 
       <?php
         }
        ?>
        
        <li class="treeview">
          <a href="<?php echo WEB_URL; ?>help/help.php">
            <i class="fa fa-book"></i> <span>Help</span></a>
        </li> 

      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>
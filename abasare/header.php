<?php
require_once 'DBController.php';
if (!isset($_SESSION['id']) || (trim($_SESSION['id']) == '')) {
    header("location: index.php");
    exit();
}
$user_id= $_SESSION['id'];
$role= $_SESSION['role'];
$sql="SELECT * FROM users WHERE  id='$user_id'";
$qry=mysqli_query($con, $sql);
$row= mysqli_fetch_array($qry);
?>
<style>
   hr{
  border-top: 1px dashed green;
}
</style>
<header class="main-header">
    
    <!-- Logo -->
    <a href="<?php echo WEB_URL; ?>dashboard.php" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>CR</b>SAV</span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>ABASARE</span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
      
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- Messages: style can be found in dropdown.less-->
          <li class="dropdown messages-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-envelope-o"></i>
              <span class="label label-success"></span>
            </a>
            </li>
         <?php if($_SESSION['role']==1 or $_SESSION['role']==6 or $_SESSION['role']==3 or $_SESSION['role']==4){?>
          <li class="dropdown notifications-menu">
            <a href="<?php echo WEB_URL; ?>appliedloan.php">
              <i class="fa fa-bell-o"></i>
              <?php
               if($_SESSION['role']==6){
              $quer=mysqli_query($con,"SELECT count(*) as unapploan FROM `member_loans` where president=1 AND accountant=0 AND reject=0 ");
               }elseif($_SESSION['role']==3 or $_SESSION['role']==4){
              $quer=mysqli_query($con,"SELECT count(*) as unapploan FROM `member_loans` where president=0 AND accountant=0 AND reject=0 ");
               }else{
                $quer=mysqli_query($con,"SELECT count(*) as unapploan FROM `member_loans` where president=0 AND accountant=0 AND reject=0 ");   
               }
              $countn=mysqli_fetch_array($quer); ?>
              <span class="label label-warning"><?php echo $countn['unapploan'];?></span>
            </a>
           
          </li>
          <?PHP } ?>
          <!-- Tasks: style can be found in dropdown.less -->
          <li class="dropdown tasks-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-flag-o"></i>
              <span class="label label-danger"></span>
            </a>
          </li>
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="<?php echo WEB_URL; ?>images/user_logo.png" class="user-image" alt="User Image">
              <span class="hidden-xs"><?php echo $row['name']; ?></span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="<?php echo WEB_URL; ?>images/user_logo.png" class="img-circle" alt="User Image">
                <p>
                  <?php echo $row['name']; ?> - <?php echo $row['Position']; ?>
                  <small><?php echo $row['trn_date']; ?></small>
                </p>
              </li>
              <!-- Menu Body -->
              <li class="user-body">
                <div class="row">
               
              </li>
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="#" class="btn btn-default btn-flat">Profile</a>
                </div>
                <div class="pull-right">
                  <a href="<?php echo WEB_URL; ?>logout.php" class="btn btn-default btn-flat">Sign out</a>
                </div>
              </li>
            </ul>
          </li>
          <!-- Control Sidebar Toggle Button -->
          <li>
            <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
          </li>
        </ul>
      </div>
    </nav>
  </header>
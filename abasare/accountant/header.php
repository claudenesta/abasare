<?php
require_once "../lib/db_function.php";
if (!isset($_SESSION['id']) || (trim($_SESSION['id']) == '') || $_SESSION['role'] != 2) {
    header("location: ../auth/logout.php");
    exit();
}
$user_id= $_SESSION['id'];
$role= $_SESSION['role'];
$row= $_SESSION['user'];
?>

<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Dashboard version: <?= $db->version??"1.0" ?></title>
  
  <link rel="shortcut icon" href="/images/agaseke.png">
  <link rel="icon" href="/images/agaseke.png">

  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="/theme/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="/theme/plugins/toastr/toastr.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="/theme/bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="/theme/bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="/theme/dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="/theme/dist/css/skins/_all-skins.min.css">
  <!-- Morris chart -->
  <link rel="stylesheet" href="/theme/bower_components/morris.js/morris.css">
  <!-- jvectormap -->
  <link rel="stylesheet" href="/theme/bower_components/jvectormap/jquery-jvectormap.css">
  <!-- Date Picker -->
  <link rel="stylesheet" href="/theme/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <!-- Daterange picker -->
  <!-- <link rel="stylesheet" href="/theme/bower_components/bootstrap-daterangepicker/daterangepicker.css"> -->
  <!-- bootstrap wysihtml5 - text editor -->
  <link rel="stylesheet" href="/theme/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
  <link rel="stylesheet" href="/theme/plugins/iCheck/all.css">
  <link rel="stylesheet" href="/theme/bower_components/select2/dist/css/select2.min.css">
  <link rel="stylesheet" href="/theme/bower_components/bootstrap-daterangepicker/daterangepicker.css">
  <link rel="stylesheet" href="/theme/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
  <link rel="stylesheet" href="/theme/bower_components/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css">
  <!-- <link rel="stylesheet" href="/theme/bower_components/bootstrap-timepicker/css/bootstrap-colorpicker.min.css"> -->

  
  </head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
<style>
   hr{
  border-top: 1px dashed green;
}
</style>
<header class="main-header">
    
    <!-- Logo -->
    <a href="/admin/" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>A</b>G</span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>ABASARE</b></span>
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
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="/<?= $_SESSION['photo'] ?>" class="user-image" alt="User Image">
              <span class="hidden-xs"><?php echo $row['name']; ?></span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="/<?= $_SESSION['photo'] ?>" class="img-circle" alt="User Image">
                <p>
                  <?php echo $row['name']; ?> - <?php echo $row['Position']; ?>
                  <small><?php echo $row['trn_date']; ?></small>
                </p>
              </li>
              <!-- Menu Body -->
              <li class="user-body">
                <div class="row"></div>
               
              </li>
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="/member/member_info.php?id=<?= $_SESSION['acc'] ?>" class="btn btn-default btn-flat">Profile</a>
                </div>
                <div class="pull-right">
                  <a href="/auth/logout.php" class="btn btn-default btn-flat">Sign out</a>
                </div>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>
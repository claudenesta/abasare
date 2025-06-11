<?php //include('DBController.php'); ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>ABASARE SACCO version: <?= $db_object->version??"1.0" ?></title>
  
  <link rel="shortcut icon" href="/images/agaseke.png">
  <link rel="icon" href="/images/agaseke.png">
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="/theme/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="/theme/bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="/theme/bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="/theme/dist/css/AdminLTE.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="/theme/plugins/iCheck/square/blue.css">

  <link rel="stylesheet" href="/theme/dist/css/mystyle.css">

<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
<style type="text/css">
  .login-page {
    background-image: url(/images/agaseke.png);
    background-repeat: no-repeat;
    background-position: bottom center;
  }
</style>
</head>
<body class="hold-transition login-page col-12 xs-12">
<div class="login-box">
  <div class="login-logo">
    <a href="./"><b>Login to access</a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg" style="color:Tomato;">Enter username and password</p>
    <form action="./auth/login.php" method="post" id="login_form">
      <div class="form-group has-feedback">
        <input type="text" class="form-control" id="username" name="username" placeholder="username">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" class="form-control" id="password" name="password" placeholder="password">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-xs-8">
          <div class="checkbox icheck">
          </div>
        </div>
        <!-- /.col -->
        <div class="col-xs-4">
        <button type="submit" class="btn btn-primary btn-block btn-flat " name="login"><i class="fa fa-sign-in" aria-hidden="true"></i>  Sign In</button>
        </div>
        <!-- /.col -->
      </div>
      <div class="row">
        <div class="col-sm-12 login_result" id=""></div>
      </div>
    </form>
    <!-- /.social-auth-links -->
    <!-- <a href="#">Forgot password?</a><br> -->
    
  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->
<!-- jQuery 3 -->
<script src="/theme/bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="/theme/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="/theme/plugins/iCheck/icheck.min.js"></script>

<script type="text/javascript">
    $(document).ready(function(){
      $("#login_form").submit(function(e){
        e.preventDefault();

        var username = $("#username").val();
        var password = $("#password").val();
        if(username == ""){
          $("#username").addClass("error");
        }else if(password == ""){
          $("#password").addClass("error");
        } else {
          $.ajax({
            type: "POST",
            url: "./auth/login.php",
            data: $("#login_form").serialize(),
            cache: false,
            success: function(result){
              $(".login_result").html(result);
            }
          });
        }
        console.log("Login form submitted!!!");
      });
    });
</script>
</body>
</html>

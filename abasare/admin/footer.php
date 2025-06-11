<footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version: </b>
      <?php
      if(isset($db_object)){
        echo $db_object->version;
      }
      ?>
    </div>
    <strong>Copyright &copy; <?php echo date("M-Y");?> <a href="http://sarisconsultech.co.rw" target="_blank">ABASARE GROUP</a>.</strong> All rights
    reserved.
  </footer>


  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

<!-- jQuery 3 -->
<script src="/theme/bower_components/jquery/dist/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="/theme/bower_components/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Bootstrap 3.3.7 -->
<script src="/theme/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- Morris.js charts -->
<!-- <script src="/theme/bower_components/raphael/raphael.min.js"></script> -->
<script src="/theme/bower_components/morris.js/morris.min.js"></script>
<!-- Sparkline -->
<script src="/theme/bower_components/jquery-sparkline/dist/jquery.sparkline.min.js"></script>
<!-- jvectormap -->
<script src="/theme/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="/theme/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<!-- jQuery Knob Chart -->
<script src="/theme/bower_components/jquery-knob/dist/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="/theme/bower_components/moment/min/moment.min.js"></script>
<script src="/theme/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<!-- datepicker -->
<script src="/theme/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="/theme/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Slimscroll -->
<script src="/theme/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="/theme/bower_components/fastclick/lib/fastclick.js"></script>

<script src="/theme/plugins/iCheck/icheck.min.js"></script>
<script src="/theme/bower_components/select2/dist/js/select2.full.min.js"></script>
<script src="/theme/bower_components/inputmask/dist/jquery.inputmask.bundle.js"></script>
<script src="/theme/bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
<script src="/theme/bower_components/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js"></script>
<script src="/theme/bower_components/bootstrap-timepicker/js/bootstrap-timepicker.js"></script>
<script src="/theme/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<!-- AdminLTE App -->
<script src="/theme/dist/js/adminlte.min.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="/theme/dist/js/pages/dashboard.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="/theme/dist/js/demo.js"></script>
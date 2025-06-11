<!DOCTYPE html>
<?php include('../DBController.php');
?>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>ITEC  | Dashboard</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="../bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="../bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="../dist/css/skins/_all-skins.min.css">
  <!-- Morris chart -->
  <link rel="stylesheet" href="../bower_components/morris.js/morris.css">
  <!-- jvectormap -->
  <link rel="stylesheet" href="../bower_components/jvectormap/jquery-jvectormap.css">
  <!-- Date Picker -->
  <link rel="stylesheet" href="../bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="../bower_components/bootstrap-daterangepicker/daterangepicker.css">
  <!-- bootstrap wysihtml5 - text editor -->
  <link rel="stylesheet" href="../plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
 
  
  </head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

 <?php include('../header.php'); ?>
  <!-- Left side column. contains the logo and sidebar -->
  
  <?php include('../menu.php'); ?>

 <!-- Content Wrapper. Contains page content -->
         <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
               <h1>
                  New Role                  <small>Add/Update Role</small>
               </h1>
               <ol class="breadcrumb">
                  <li><a href="https://destinytechnologies.in/inventory/dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
                  <li><a href="https://destinytechnologies.in/inventory/roles/view">Roles List</a></li>
                  <li class="active">New Role</li>
               </ol>
            </section>
            <!-- Main content -->
            <section class="content">
               <div class="row">
                  <!-- right column -->
                  <div class="col-md-12">
                     <!-- Horizontal Form -->
                     <div class="box box-info ">
                        <div class="box-header with-border">
                           <h3 class="box-title">Please Enter Valid Data</h3>
                        </div>
                        <!-- /.box-header -->
                        <!-- form start -->
                        <form class="form-horizontal" id="roles-form" onkeypress="return event.keyCode != 13;">
                           <input type="hidden" name="csrf_test_name" value="dd1497a12de7cbab67d8513564ae3cec">
                           <input type="hidden" id="base_url" value="https://destinytechnologies.in/inventory/">
                           <div class="box-body">
                              <div class="form-group">
                                 <label for="role_name" class="col-sm-2 control-label">Role Name<label class="text-danger">*</label></label>
                                 <div class="col-sm-4">
                                    <input type="text" class="form-control input-sm" id="role_name" name="role_name" placeholder="" onkeyup="shift_cursor(event,'description')" value="" autofocus >
                                    <span id="role_name_msg" style="display:none" class="text-danger"></span>
                                 </div>
                              </div>
                              <div class="form-group">
                                 <label for="description" class="col-sm-2 control-label">Description</label>
                                 <div class="col-sm-4">
                                    <textarea type="text" class="form-control" id="description" name="description" placeholder=""></textarea>
                                    <span id="description_msg" style="display:none" class="text-danger"></span>
                                 </div>
                              </div>
                              <div class="form-group">
                                 <div class="col-sm-12">
                                    <table class="table table-bordered">
                                      <thead class="bg-primary">
                                          <tr>
                                            <th>#</th>
                                            <th>Modules</th>
                                            <th>Select All</th>
                                            <th>Specific Permissions</th>
                                          </tr>
                                      </thead>
                                      <tbody>
                                                                                <!-- USERS -->
                                        <tr>
                                          <td>1</td>
                                          <td>Users</td>
                                          <td>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="change_me" id="users" > Select All                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[users]" value="on">
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="users_all" id='users_add' name="permission[users_add]" > Add                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="users_all" id='users_edit' name="permission[users_edit]"> Edit                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="users_all" id='users_delete' name="permission[users_delete]"> Delete                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="users_all" id='users_view' name="permission[users_view]"> View                                              </label></div>
                                          </td>
                                        </tr>
                                        <!-- Roles -->

                                        <tr>
                                          <td>2</td>
                                          <td>Roles</td>
                                          <td>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="change_me" id="roles" > Select All                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[roles]" value="on">
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="roles_all" id='roles_add' name="permission[roles_add]" > Add                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="roles_all" id='roles_edit' name="permission[roles_edit]"> Edit                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="roles_all" id='roles_delete' name="permission[roles_delete]"> Delete                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="roles_all" id='roles_view' name="permission[roles_view]"> View                                              </label></div>
                                          </td>
                                        </tr>
                                        <!-- TAX -->
                                        <tr>
                                          <td>3</td>
                                          <td>Tax</td>
                                          <td>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="change_me" id="tax"> Select All                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[tax]" value="on">
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="tax_all" id='tax_add' name="permission[tax_add]"> Add                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="tax_all" id='tax_edit' name="permission[tax_edit]"> Edit                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="tax_all" id='tax_delete' name="permission[tax_delete]"> Delete                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="tax_all" id='tax_view' name="permission[tax_view]"> View                                              </label></div>
                                          </td>
                                        </tr>
                                       <!-- UNITS -->
                                       <tr>
                                          <td>4</td>
                                          <td>Units</td>
                                          <td>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="change_me" id="units"> Select All                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[units]" value="on">
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="units_all" id='units_add' name="permission[units_add]"> Add                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="units_all" id='units_edit' name="permission[units_edit]"> Edit                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="units_all" id='units_delete' name="permission[units_delete]"> Delete                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="units_all" id='units_view' name="permission[units_view]"> View                                              </label></div>
                                          </td>
                                        </tr>
                                        <!-- SITE SETTINGS -->
                                        <tr>
                                          <td>5</td>
                                          <td>Site Settings</td>
                                          <td>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="change_me" id="site"> Select All                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[site]" value="on">
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="site_all" id='site_edit' name="permission[site_edit]"> Edit                                              </label></div>
                                          </td>
                                        </tr>
                                        <!--COMPANY PROFILE  -->
                                        <tr>
                                          <td>6</td>
                                          <td>Company Profile</td>
                                          <td>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="change_me" id="company"> Select All                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[company]" value="on">
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="company_all" id='company_edit' name="permission[company_edit]"> Edit                                              </label></div>
                                          </td>
                                        </tr>
                                        <!--DASHBOARD  -->
                                        <tr>
                                          <td>7</td>
                                          <td>Dashboard</td>
                                          <td>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="change_me" id="dashboard"> Select All                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[dashboard]" value="on">
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="dashboard_all" id='dashboard_view' name="permission[dashboard_view]"> View Dashboard Data                                              </label></div>
                                          </td>
                                        </tr>
                                        <!-- Places -->
                                        <tr>
                                          <td>8</td>
                                          <td>Places</td>
                                          <td>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="change_me" id="places" > Select All                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[places]" value="on">
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="places_all" id='places_add' name="permission[places_add]" > Add                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="places_all" id='places_edit' name="permission[places_edit]"> Edit                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="places_all" id='places_delete' name="permission[places_delete]"> Delete                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="places_all" id='places_view' name="permission[places_view]"> View                                              </label></div>
                                          </td>
                                        </tr>
                                        <!-- EXPENSES -->
                                        <tr>
                                          <td>9</td>
                                          <td>Expense</td>
                                          <td>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="change_me" id="expense" > Select All                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[expense]" value="on">
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="expense_all" id='expense_add' name="permission[expense_add]" > Add                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="expense_all" id='expense_edit' name="permission[expense_edit]"> Edit                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="expense_all" id='expense_delete' name="permission[expense_delete]"> Delete                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="expense_all" id='expense_view' name="permission[expense_view]"> View                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="expense_all" id='expense_category_add' name="permission[expense_category_add]" > Category Add                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="expense_all" id='expense_category_edit' name="permission[expense_category_edit]"> Category Edit                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="expense_all" id='expense_category_delete' name="permission[expense_category_delete]"> Category Delete                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="expense_all" id='expense_category_view' name="permission[expense_category_view]"> Category View                                              </label></div>
                                          </td>
                                        </tr>
                                        <!-- ITEMS -->
                                        <tr>
                                          <td>10</td>
                                          <td>Items</td>
                                          <td>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="change_me" id="items" > Select All                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[items]" value="on">
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="items_all" id='items_add' name="permission[items_add]" > Add                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="items_all" id='items_edit' name="permission[items_edit]"> Edit                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="items_all" id='items_delete' name="permission[items_delete]"> Delete                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="items_all" id='items_view' name="permission[items_view]"> View                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="items_all" id='items_category_add' name="permission[items_category_add]" > Category Add                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="items_all" id='items_category_edit' name="permission[items_category_edit]"> Category Edit                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="items_all" id='items_category_delete' name="permission[items_category_delete]"> Category Delete                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="items_all" id='items_category_view' name="permission[items_category_view]"> Category View                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="items_all" id='print_labels' name="permission[print_labels]"> Print Labels                                              </label></div>
                                          </td>
                                        </tr>
                                        <!-- Suppliers -->
                                        <tr>
                                          <td>11</td>
                                          <td>Suppliers</td>
                                          <td>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="change_me" id="suppliers" > Select All                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[suppliers]" value="on">
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="suppliers_all" id='suppliers_add' name="permission[suppliers_add]" > Add                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="suppliers_all" id='suppliers_edit' name="permission[suppliers_edit]"> Edit                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="suppliers_all" id='suppliers_delete' name="permission[suppliers_delete]"> Delete                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="suppliers_all" id='suppliers_view' name="permission[suppliers_view]"> View                                              </label></div>
                                          </td>
                                        </tr>
                                        <!-- Customers -->
                                        <tr>
                                          <td>12</td>
                                          <td>Customers</td>
                                          <td>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="change_me" id="customers" > Select All                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[customers]" value="on">
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="customers_all" id='customers_add' name="permission[customers_add]" > Add                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="customers_all" id='customers_edit' name="permission[customers_edit]"> Edit                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="customers_all" id='customers_delete' name="permission[customers_delete]"> Delete                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="customers_all" id='customers_view' name="permission[customers_view]"> View                                              </label></div>
                                          </td>
                                        </tr>
                                        <!-- Purchase -->
                                        <tr>
                                          <td>13</td>
                                          <td>Purchase</td>
                                          <td>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="change_me" id="purchase" > Select All                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[purchase]" value="on">
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="purchase_all" id='purchase_add' name="permission[purchase_add]" > Add                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="purchase_all" id='purchase_edit' name="permission[purchase_edit]"> Edit                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="purchase_all" id='purchase_delete' name="permission[purchase_delete]"> Delete                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="purchase_all" id='purchase_view' name="permission[purchase_view]"> View                                              </label></div>
                                          </td>
                                        </tr>
                                        <!-- Sales -->
                                        <tr>
                                          <td>14</td>
                                          <td>Sales (Include POS)</td>
                                          <td>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="change_me" id="sales" > Select All                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[sales]" value="on">
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="sales_all" id='sales_add' name="permission[sales_add]" > Add                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="sales_all" id='sales_edit' name="permission[sales_edit]"> Edit                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="sales_all" id='sales_delete' name="permission[sales_delete]"> Delete                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="sales_all" id='sales_view' name="permission[sales_view]"> View                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="sales_all" id='sales_payment_view' name="permission[sales_payment_view]"> Payments View                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="sales_all" id='sales_payment_add' name="permission[sales_payment_add]"> Payments Add                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="sales_all" id='sales_payment_delete' name="permission[sales_payment_delete]"> Payments Delete                                              </label></div>
                                          </td>
                                        </tr>
                                        <!-- Reports -->
                                        <tr>
                                          <td>15</td>
                                          <td>Reports</td>
                                          <td>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="change_me" id="reports" > Select All                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[reports]" value="on">
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="reports_all" id='sales_report' name="permission[sales_report]" > Sales Report                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="reports_all" id='purchase_report' name="permission[purchase_report]"> Purchase Report                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="reports_all" id='expense_report' name="permission[expense_report]"> Expense Report                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="reports_all" id='profit_report' name="permission[profit_report]"> Profit Report                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="reports_all" id='stock_report' name="permission[stock_report]"> Stock Report                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="reports_all" id='purchase_payments_report' name="permission[purchase_payments_report]"> Purchase Payments Report                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="reports_all" id='sales_payments_report' name="permission[sales_payments_report]"> Sales Payments Report                                              </label></div>
                                          </td>
                                        </tr>
                                      </tbody>
                                      
                                    </table>
                                 </div>
                              </div>

                           </div>
                           <!-- /.box-footer -->
                           <div class="box-footer">
                              <div class="col-sm-8 col-sm-offset-2 text-center">
                                 <!-- <div class="col-sm-4"></div> -->
                                                                  <div class="col-md-3 col-md-offset-3">
                                    <button type="button" id="save" class=" btn btn-block btn-success" title="Save Data">Save</button>
                                 </div>
                                 <div class="col-sm-3">
                                    <a href="https://destinytechnologies.in/inventory/dashboard">
                                    <button type="button" class="col-sm-3 btn btn-block btn-warning close_btn" title="Go Dashboard">Close</button>
                                    </a>
                                 </div>
                              </div>
                           </div>
                           <!-- /.box-footer -->
                        </form>
                     </div>
                     <!-- /.box -->
                  </div>
                  <!--/.col (right) -->
               </div>
               <!-- /.row -->
            </section>
            <!-- /.content -->
         </div>
         <!-- /.content-wrapper -->
 
 
  <?php include('../footer.php'); ?>

  <!-- Control Sidebar -->
  
  <!-- /.control-sidebar -->
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

<!-- jQuery 3 -->
<script src="../bower_components/jquery/dist/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="../bower_components/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Bootstrap 3.3.7 -->
<script src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- Morris.js charts -->
<script src="../bower_components/raphael/raphael.min.js"></script>
<script src="../bower_components/morris.js/morris.min.js"></script>
<!-- Sparkline -->
<script src="../bower_components/jquery-sparkline/dist/jquery.sparkline.min.js"></script>
<!-- jvectormap -->
<script src="../plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="../plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<!-- jQuery Knob Chart -->
<script src="../bower_components/jquery-knob/dist/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="../bower_components/moment/min/moment.min.js"></script>
<script src="../bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
<!-- datepicker -->
<script src="../bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="../plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Slimscroll -->
<script src="../bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="../bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.min.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="../dist/js/pages/dashboard.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../dist/js/demo.js"></script>
</body>
</html>

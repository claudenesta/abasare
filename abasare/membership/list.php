<?php include('../DBController.php');
			if(isset($_POST["save"])){
									$fname= $_POST['fname'];
									$id=$_POST['code'];
									$lname = $_POST['lname'];
									$mi = $_POST['mi'];
									$age = $_POST['age'];
									$civ_status = $_POST['civ_status'];
									$address = $_POST['address'];
									$phone_cell = $_POST['phone_cell'];
									$email = $_POST['email'];
									$employment_status = $_POST['employment_status'];
									$company = $_POST['company'];
									$job_title = $_POST['job_title'];
									$income = $_POST['income'];
									$isnew = $_POST['isnew'];
									$date = date('Y/m/d');
				 $SQL_STRING ="UPDATE `member` SET company='$company', address='$address', `phone_cell`='$phone_cell', `email`='$email', `civil_status`='$civ_status', `employment_status`='$employment_status', `job_title`='$job_title', `fname`='$fname', `lname`='$lname', `birth_date`='$age' where id=$id";
				 MYSQLI_QUERY($con,"$SQL_STRING") OR DIE(MYSQLI_ERROR());
				 ?>
				 <meta http-equiv="refresh" content="0; URL=list.php">
				 <?php 
			}
       ?>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Member | List</title>
<!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="../bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="../bower_components/Ionicons/css/ionicons.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="../bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="../dist/css/skins/_all-skins.min.css">


  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
 
  
  </head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

 <?php include('../header.php'); ?>
  <!-- Left side column. contains the logo and sidebar -->
  
  <?php include('../menu.php'); ?>

  <!-- Content Wrapper. Contains page content -->
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Member List <small>View/Search </small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Member List</li>
      </ol>
    </section>

    <!-- Main content -->
    <form action="#" class="" id="table_form" method="post" accept-charset="utf-8">
   
    <section class="content">
      <div class="row">
        <!-- ********** ALERT MESSAGE START******* -->
          <div class="col-md-12">
      
     </div>            <!-- ********** ALERT MESSAGE END******* -->
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header ">
              <h3 class="box-title">&nbsp;</h3>
                            <div class="box-tools">
                <a class="btn btn-block btn-info" href="add_member.php">
                <i class="fa fa-plus"></i> New Member</a>
              </div>
                          </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example" class="table table-striped table-bordered" style="width:100%">
                <thead class="bg-primary ">
                <tr>
                  <th class="text-center">
                    <input type="checkbox" class="group_check checkbox" >
                  </th>
                  <th>No</th>
                  <th>Names</th>
                  <th>Job Title</th>
                  <th>Civil status</th>
                  <th>Phone / Cell</th>
                  <th>Join Date</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
				<?php
				$no=0;
$sql="select * from member where status='".$_GET['stat']."'";
$query=mysqli_query($con,$sql);
while($row=mysqli_fetch_array($query)){
$no++;
				?>
				  <tr>
                  <td class="text-center">
                    <input type="checkbox" class="group_check checkbox" >
                  </th>
                  <td><a href="member_info.php?id=<?php echo $row['id']; ?>">#<?php echo $no; ?></a></td>
                  <td><a href="member_info.php?id=<?php echo $row['id']; ?>"><?php echo $row['fname']." ".$row['lname']; ?></a></td>
                  <td><?php echo $row['job_title']; ?></td>
                  <td><?php echo $row['civil_status']; ?></td>
                  <td><?php echo $row['phone_cell']; ?></td>
                  <td><?php echo $row['rdate']; ?></td>
                  <td><?php if($_SESSION['role']==1 || $_SESSION['role']==2 ){?><a href="#" data-toggle="modal" data-target="#modal-<?php echo $row['id']; ?>"> Edit </a> |  <a href="delete_member.php?id=<?php echo $row['id'];?>" onclick="return confirm('Are you sure you want to delete this Member?');">Delete</a><?php } ?></td>
                </tr>
<div class="modal fade" id="modal-<?php echo $row['id']; ?>">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header bg-primary ">
              <h4 class="modal-title"><span style="color:white"><i class="fa fa-plus"></i> MEMBER EDIT</span></h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
             <div class="card card-info">
                <div class="card-body">

<form action="" class="form" id="items-form" enctype="multipart/form-data" method="POST" accept-charset="utf-8">
                          <input type="hidden" name="code" value = "<?php echo $row['id']; ?>" class = "form-control" />
                          
                        <div class="box-body">
                           <div class="row">
                              <div class="form-group col-md-4">
                                 <label for="item_name">First Name<span class="text-danger">*</span></label>
                                <input type="text" name="fname"  class="form-control" value="<?php echo $row['fname']; ?>" />
                                 <span id="item_name_msg" style="display:none" class="text-danger"></span>
                              </div>
                              <div class="form-group col-md-4">
                                 <label for="category_id">Last Name <span class="text-danger">*</span></label>
                                 <input type="text" name="lname" class="form-control" value="<?php echo $row['lname']; ?>" />
                               <span id="category_id_msg" style="display:none" class="text-danger"></span>
                              </div>
                             
                              <div class="form-group col-md-4">
                                 <label for="sku">DOB </label>
                                <input type="date" name="age" value="<?php echo $row['birth_date']; ?>" class="form-control" />
                                 <span id="sku_msg" style="display:none" class="text-danger"></span>
                              </div>
                              <div class="form-group col-md-4">
                                 <label for="alert_qty" >Civil Status <span class="text-danger">*</span></label>
                                 <select class="form-control select2" id="item_type" name="civ_status"  style="width: 100%;" required="">
                                  <option selected><?php echo $row['civil_status']; ?></option>
								   <option>Single</option>
								   <option>Married</option>
								   <option>Widow</option>
                                    </select>
								<span id="alert_qty_msg" style="display:none" class="text-danger">*</span>
                              </div>
                      
                           </div>
                          <fieldset><legend>Contact Info :</legend>
                           <div class="row">
                              <div class="form-group col-md-4">
                                 <label for="price">Address<span class="text-danger">*</span></label>
                                 <textarea rows="2" cols="50" name="address" class="form-control"><?php echo $row['address']; ?></textarea>
                                 <span id="price_msg" style="display:none"  class="text-danger"></span>
                              </div>
                              <div class="form-group col-md-4">
                                 <label for="tax_id" >Phone / Cellphone<span class="text-danger">*</span></label>
                                 <input type="text" name="phone_cell" class="form-control" value="<?php echo $row['phone_cell']; ?>"/>
                                 <span id="tax_id_msg" style="display:none" class="text-danger"></span>
                              </div>
                              <div class="form-group col-md-4">
                                 <label for="purchase_price">Email<span class="text-danger">*</span></label>
                                 <input type="text" name="email" value="<?php echo $row['email']; ?>" class="form-control" />
                                 <span id="purchase_price_msg" style="display:none" class="text-danger"></span>
                              </div>
                           </div>
						   </fieldset>
						   <fieldset><legend>Current Employment Info :</legend>
                           <!-- /row -->
                           <div class="row">
                              <div class="form-group col-md-4">
                                 <label for="tax_type">Employment Status <span class="text-danger">*</span></label>
                                 <select class="form-control select2" name="employment_status"  style="width: 100%;" required="" >
                                     <option><?php echo $row['employment_status']; ?></option>
                                    <option> Employeed </option>
                                    <option> Unemployeed </option>
                                 </select>
                                 <span id="tax_type_msg" style="display:none" class="text-danger"></span>
                                 
                              </div>
                              <div class="form-group col-md-4">
                                 <label for="profit_margin">Company <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="company" value="<?php echo $row['company']; ?>" />
                                 <span id="profit_margin_msg" style="display:none" class="text-danger"></span>
                              </div>
                              <div class="form-group col-md-4">
                                 <label for="sales_price" class="control-label">Job Title <span class="text-danger">*</span></label>
                                 <input type="text" name="job_title" value="<?php echo $row['job_title']; ?>" class="form-control" />
                                 <span id="sales_price_msg" style="display:none" class="text-danger"></span>
                              </div>
						
                         </fieldset>
                        
                              
                           <!-- /.box-body -->
                           <div class="box-footer">
                              <div class="col-sm-12 col-sm-offset-2 text-center">
                                 <!-- <div class="col-sm-4"></div> -->
                                   <div class="col-md-6 col-md-offset-3">
                                    <button type="submit" id="save" name="save" class=" btn btn-block btn-primary" title="Save Data"><i class="fa fa-upload"></i>  Save</button>
                                 </div>
                                 <a href="export_excel.php" class="btn btn-primary">Download Excel List</a>
                                 <a href="export_pdf.php" class="btn btn-primary">Download PDF Report List</a>
                              </div>
                           </div>
                           <!-- /.box-footer -->
                     </form>                     </div>
                     <!-- /.box -->


             </div>
            </div>
            </div>
          </div>
		  
        </div>
      </div>
                
                
<?php } ?>
                </tbody>
               
              </table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
    </form>  </div>
  <!-- /.content-wrapper -->
  <!-- /.content-wrapper -->
  

  <?php include('../footer.php'); ?>
  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->
<!-- jQuery 3 -->
<script src="../bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- DataTables -->
<script src="../bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="../bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<!-- SlimScroll -->
<script src="../bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="../bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../dist/js/demo.js"></script>
<!-- page script -->
<script>
$(document).ready(function() {
    $('#example').DataTable();
} );
</script>
</body>
</html>

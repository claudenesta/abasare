<?php 
include('../DBController.php');
$membe_id=$_GET['id'];

$profit_sql = "SELECT sum(amount) as prit FROM `profite` where m_id='$membe_id'";
					   $querprofile=mysqli_query($con,$profit_sql);
					   $profi=mysqli_fetch_array($querprofile);
  
  if(isset($_POST['fee_save'])){
$next_id=111;
$me_id=$_POST['me_id'];
$member_fee=$_POST['member_fee'];
$month=date('m');
$year=date('Y');
$sql = "INSERT INTO `interest` (`id`, `loan_interest`, `saving_overdu`, `membership_fee`, `ref_id`, `desciption`, `month`, `year`, `done_at`) 
        VALUES (NULL, '0', '0', '$member_fee', '$me_id', 'overdue fine $next_id', '$month', '$year', CURRENT_TIMESTAMP)";
  if(mysqli_query($con,$sql)){
$sql2 = "UPDATE `member` SET `is_new` = '0' WHERE `member`.`id` = '$me_id'";
   mysqli_query($con,$sql2);   
  }
header("location:member_info.php?id=$membe_id");	  
  }
  ?>  

<script>
function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}

function showUser(str) {
  if (str=="") {
    document.getElementById("period").value="";
    return;
  }
  var xmlhttp=new XMLHttpRequest();
  xmlhttp.open("GET","getloantype.php?q="+str,true);
  xmlhttp.send();
  xmlhttp.onreadystatechange=function() {
    if (this.readyState==4 && this.status==200) {
		 var r= this.responseText.split("|");
           document.getElementById("period").value = r[0];
           document.getElementById("percent").value = r[1];
    }
  }
}

function myFunction() {
  var checkBox = document.getElementById("limiticheck");
  var amount = document.getElementById("limiaount").value;
  var principal = document.getElementById("principal").value;
  if (checkBox.checked == true){
    document.getElementById("principal").value=amount;
  } else {
     document.getElementById("principal").value="";
  }
}
</script>
<style>
  
.alert {
  padding: 20px;
  background-color: #f44336;
  color: white;
  opacity: 1;
  transition: opacity 0.6s;
  margin-bottom: 15px;
}

.alert.success {background-color: #4CAF50;}
.alert.info {background-color: #2196F3;}
.alert.warning {background-color: #ff9800;}
.closebtn {
  margin-left: 15px;
  color: white;
  font-weight: bold;
  float: right;
  font-size: 22px;
  line-height: 20px;
  cursor: pointer;
  transition: 0.3s;
}
.closebtn:hover {
  color: black;
}
</style>
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
<?php 
include('./header.php'); 

if(isset($_POST['update_picture'])){
  $fileinfo = explode(".", $_FILES['image']['name']);
  $extension = strtolower($fileinfo[(count($fileinfo) - 1)]);
  $allowedExtensions = array("png", "jpeg", "jpg");
  if(in_array($extension, $allowedExtensions)){
    $filename = $_GET['id'].".".$extension;

    try{
      if(move_uploaded_file($_FILES['image']['tmp_name'], "../images/photo/".$filename)){
        saveData($db, "UPDATE users SET photo=? WHERE member_acc=?", ["images/photo/".$filename, $_GET['id']]);
        $_SESSION['photo'] = "images/".$filename;
        ?>
        <script type="text/javascript">
          alert("Member profile uploaded successfully");
        </script>
        <meta http-equiv="refresh" content="0; URL=member_info.php?id=<?= $_GET['id']?>">
        <?php
      } else{
        ?>
        <script type="text/javascript">
          alert("Unable to upload the member profile");
        </script>
        <?php
        
      }
    } catch(\Exception $e){
      $errorUpload = $e->getMessage();
      die($e->getMessage());
    }
  } else {
    ?>
    <script type="text/javascript">
      alert("Found file is <?= $extension ?>  while allowed files are <?= implode(", ", $allowedExtensions) ?>");
    </script>
    <?php
  }
}

if(isset($_POST['saving_save'])){
  $db->beginTransaction();
  try{
    //check if the member had made saving in the selected month
    $saving = returnSingleField($db, "SELECT id FROM saving WHERE member_id=? AND month = ? AND year= ?", "id", [$_POST['me_id'], $_POST['ukwezi'], $_POST['year']]);
    if($saving){
      ?>
      <script type="text/javascript">
        alert("Contribution for <?= $_POST['year']."-".$_POST['ukwezi'] ?> already saved for the selected member");
      </script>
      <?php
    } else {
      //check if the overdue is done
      $overdue_setting = first($db, "SELECT * FROM overdue_settings WHERE month = ? AND year = ?", [$_POST['ukwezi'], $_POST['year']]);

      $contribution_month = new \DateTime($_POST['year']."-".($_POST['ukwezi'] < 10?"0":"").$_POST['ukwezi']."-01");
      if(!$overdue_setting && $contribution_month->getTimestamp() <= time() ){
        //Here make sure to notify invalid settings
        ?>
        <script type="text/javascript">
          alert("Incomplete System Settings\nUnable to find overdue information for <?= $_POST['year']."-".$_POST['ukwezi'] ?>");
        </script>
        <?php
      } else {
        //Here we can save as some settings were found
        $days = 0;
        if(isset($overdue_setting['saving_overdue'])){
          $saving_due = new \DateTime($overdue_setting['saving_overdue']);
          $now = new \DateTime();
          if($saving_due->getTimestamp() < $now->getTimestamp()){
            //Get the number of days to count fines
            $days = $saving_due->diff($now)->days;
          }
        }

        $fines = $days * 100; //This 100 is the fine per day of delay
        //check if the submit fines is not equal to the calculated ones
        if($_POST['fine'] != $fines){
          ?>
          <script type="text/javascript">
            alert("Required fines are <?= $fines ?>\nPaid are <?= $_POST['fine'] ?>Please Make sure to fill the correct fines");
          </script>
          <?php
        } else {
          //Now we have the fine information well filled
          $create_query = "INSERT INTO saving SET member_id=?, sav_amount=?, month=?, year=?, fine=?";

          $saving_id = saveAndReturnID($db, $create_query, [
            $_POST['me_id'],
            $_POST['amoun'],
            $_POST['ukwezi'],
            $_POST['year'],
            $_POST['fine']
          ]);

          //save fines if they are availabe
          if($fines > 0){
            $interest_query = "INSERT INTO interest SET member_id=?, amount=?, saving_id=?, saving_overdu=?, ref_id=?, desciption=?, month=?, year=?";
            saveData($db, $interest_query, [
              $_POST['me_id'],
              $_POST['fine'],
              $saving_id,
              $_POST['fine'],
              $saving_id,
              sprintf("saving due was %s and payment is made on %s", $overdue_setting['saving_overdue'], $now->format('Y-m-d H:i:s')),
              $_POST['ukwezi'],
              $_POST['year']
            ]);
          }

          //Here make sure to update the members Balance for better data synchronization
          saveData($db, "UPDATE member SET Account_balance = Account_balance + ? WHERE id = ?", [$_POST['amoun'], $_POST['me_id']] );
        }
      }
    }
    $db->commit();
    ?>
    <meta http-equiv="refresh" content="0; URL=member_info.php?id=<?= $_GET['id']?>">
    <?php
  } catch(\Exception $e){
    $db->rollBack();
    ?>
    <script type="text/javascript">
      alert("Error\n <?= $e->getMessage() ?>");
    </script>
    <?php
  } 
}
if(isset($_POST['botton_save'])){
  $db->beginTransaction();
  try{

    //Make sure not to provide loan to the same person on the same date
    $exist = returnSingleField($db, "SELECT id FROM member_loans WHERE member_id=? AND loan_date=?", "id", [$_GET['id'], $_POST['date']]);
    if($exist){
      throw new Exception(sprintf("The member has a loan on %s, The system rejected the request", $_POST['date']));
    }
    //Create member loan record
    $loan_type = first($db, "SELECT * FROM loan_type WHERE id=?", [$_POST['interest']]);

    $interest_amount = $_POST['principal']*$loan_type['interest']/100;

    $amount_per_term = $_POST['principal']/$loan_type['terms'];
    $sql = "INSERT INTO member_loans SET member_id =?, loan_id=?, status=?, loan_date=?, loan_amount=?, loan_amount_interest=?, loan_amount_term=?, loan_amount_total=?, rdate=?, staff_portal=?";
    $member_loan_id = saveAndReturnID($db, $sql, [
      $_GET['id'],
      $_POST['interest'],
      'ACTIVE',
      $_POST['date'],
      $_POST['principal'],
      $interest_amount,
      $amount_per_term,
      $_POST['principal'],
      (new \DateTime())->format('Y-m-d H:i:s'),
      $_SESSION['id'],
    ]);
    //Create member_loan_settings records\
    $sql = "INSERT INTO member_loan_settings SET loan_id=?, member_loan_id=?, lname=?, interest=?, terms=?, frequency=?, late_fee=?";
    $setting = saveAndReturnID($db, $sql, [
      $member_loan_id, 
      $_GET['id'],
      $loan_type['lname'],
      $loan_type['interest'],
      $loan_type['terms'],
      $loan_type['frequency'],
      $loan_type['late_fee'],
    ]);

    //Create lend_payments records based on found terms
    $start_point = $payment_number = returnSingleField($db, "SELECT MAX(payment_number) AS last_payment FROM lend_payments", "last_payment");
    $start_date = new \DateTime($_POST['date']);
    $terms = 0;
    do {
      $create_lend_payment = "INSERT INTO lend_payments SET borrower_id=?, borrower_loan_id=?, payment_sched=?, payment_number=?, amount=?, status=?, overdue_fine=?";
      $start_date->modify("+".(++$terms)." month".($terms > 1?"s":""));
      saveData($db, $create_lend_payment, [
        $_GET['id'],
        $member_loan_id,
        $start_date->format('Y-m-d'),
        ++$payment_number,
        $_POST['payment'],
        'UNPAID',
        0 //<<<<<<<< This will be auto applied when the due date is exceeded 
      ]);
    } while($terms < $loan_type['terms']);

    //Update member_loans records to make the system updated for payment tracked
    saveData($db, "UPDATE member_loans SET next_payment_id=? WHERE id = ?", [++$start_point, $member_loan_id]);
    $db->commit();
    ?>
    <meta http-equiv="refresh" content="0; URL=member_info.php?id=<?= $_GET['id']?>">
    <?php
  } catch(\Exception $e){
    $db->rollBack();
    //Unable to complete the loan savng operation as it encountered some errors
    echo "<script>alert('".$e->getMessage()."');</script>";
  }
}

if(isset($_POST['capital_save'])){
  //Here the capital share record is about to be create
  $create_query = "INSERT INTO capital_share SET staff_id=?, member_id=?, amount=?, month=?, year=?";
  $create_params = [$_SESSION['id'], $_POST['me_id'], $_POST['amount'], $_POST['ukwezi'], $_POST['year']];
  $search_query = "SELECT id FROM capital_share WHERE member_id=? AND month = ? AND year = ?";
  $search_params = [$_POST['me_id'], $_POST['ukwezi'], $_POST['year']];
  $capital_info = insertOrReturnID($db, $create_query, $search_query, "id", $create_params, $search_params);
  ?>
  <meta http-equiv="refresh" content="0; URL=member_info.php?id=<?= $_GET['id']?>">
  <?php

}

$loan_information = "";

 $active = "member-list-active";
 include('./menu.php'); ?>
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       <a href="member_info.php?id=<?= $_GET['id'] ?>"> Member Information </a> <small> Details </small>
      </h1>
      <ol class="breadcrumb">
          <li><a href="/admin/"><i class="fa fa-dashboard"></i>Home</a></li>
          <li><a href="#">Members</a></li>
          <li class="active"><a href="member_info.php?id=<?= $_GET['id'] ?>">Member Details </a></li>
      </ol>
    </section>
    <form action="#" class="" id="table_form" method="post" accept-charset="utf-8">
    <section class="content">
      <div class="row">
        <!-- ********** ALERT MESSAGE START******* -->
        <div class="col-md-12">
      
        </div>
        <!-- ********** ALERT MESSAGE END******* -->
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header ">
              <h3 class="box-title">&nbsp;</h3>
              <?php 
                $sql="SELECT  a.*,
                              COALESCE(b.photo, 'images/user_logo.png') AS photo
                              FROM member AS a
                              INNER JOIN users AS b
                              ON a.id = b.member_acc
                              WHERE a.id=? ";

                $rows = first($db, $sql, [$_GET['id']]);
			        ?>
            </div>
            <div class="box-body">
             <div class="container-fluid">
        <div class="row">
          <div class="col-md-3">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <img class="profile-user-img img-fluid img-circle" src= "/<?= $rows['photo'] ?>" alt="Profile">
                       
                </div>

                <h3 class="profile-username text-center"><?php echo $rows['fname']." ".$rows['lname']; ?></h3>
                <p class="text-muted text-center"><?php echo $rows['job_title']; ?></p>
				        <?php 
                if($rows['is_new']==0){
                  ?>
				          <a class="btn btn-warning btn-flat btn-sm" data-toggle="modal" data-target="#modal-upload_photo"><i class="fa fa-plus"></i> Photo</a>
                  <a class="btn btn-success btn-flat btn-sm" data-toggle="modal" data-target="#modal-saving"><i class="fa fa-plus"></i> Savings </a>
				          <a class="btn btn-danger btn-flat btn-sm" data-toggle="modal" data-target="#modal-default"><i class="fa fa-plus"></i> Credit </a>
				          <a class="btn btn-primary btn-flat btn-sm" data-toggle="modal" data-target="#modal-captal"><i class="fa fa-plus"></i> Share </a>
				          <?php 
                } else if($rows['member_fee']!=0 && $rows['is_new']==1){ 
                  ?>
                  <a class="btn btn-warning btn-flat btn-sm" data-toggle="modal" data-target="#modal-upload_photo"><i class="fa fa-plus"></i>Photo</a>
				          <a class="btn btn-success btn-flat btn-sm" data-toggle="modal" data-target="#modal-saving"><i class="fa fa-plus"></i> Savings </a>
				          <a class="btn btn-danger btn-flat btn-sm" data-toggle="modal" data-target="#modal-default"><i class="fa fa-plus"></i> Credit </a>
				          <a class="btn btn-primary btn-flat btn-sm" data-toggle="modal" data-target="#modal-captal"><i class="fa fa-plus"></i> Share </a>
				          <?php 
                }
                if($rows['is_new']==1 && $rows['member_fee']==0){
                  ?>
				          <a class="btn btn-primary btn-flat btn-sm" data-toggle="modal" data-target="#modal-share"><i class="fa fa-plus"></i> Account Activation </a>
				          <?php 
                }
                ?>
              </div>
			        <div class="card card-primary">
                <!-- /.card-header -->
                <div class="card-body">
                  <hr>
                  <strong><i class="fa fa-plus mr-1"></i> Personal Info </strong>
                  <hr>
                  <p class="text"> First Name : <?php echo $rows['fname'];  ?> </p>
                  <p class="text"> Last Name : <?php echo $rows['lname'];  ?> </p>
                  <p class="text"> Date of Birth : <?php echo $rows['birth_date'];  ?> </p>
                  <p class="text"> Civil Status : <?php echo $rows['civil_status'];  ?> </p>
                  <hr>
                  <strong><i class="fa fa-plus mr-1"></i> Contact Info</strong>
                  <hr>
                  <p class="text"> Address : <?php echo $rows['address'];  ?> </p>
                  <p class="text"> Phone / Cellphone : <?php echo $rows['phone_cell'];  ?> </p>
                  <p class="text"> Email : <?php echo $rows['email'];  ?> </p>
                  <hr>
                  <strong><i class="fa fa-plus mr-1"></i> Current Employment Info </strong>
                  <hr>
                  <p class="text"> Employment Status : <?php echo $rows['employment_status'];  ?> </p>
                  <p class="text"> Company : <?php echo $rows['company'];  ?> </p>
                  <p class="text"> Job Title : <?php echo $rows['job_title'];  ?> </p>
                  <p class="text"> Monthly Income : <?= number_format($rows['income']??0);  ?> Frws</p>
                </div><!-- /.card-body -->
              </div><!-- /.card -->
            </div>
          </div>
          <div class="col-md-9">
            <div class="card">
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="activity">
                    <!-- Post -->
                    <div class="post">
                      <div class="user-block">
                        <div class="row">
                          <div class="col-sm-3">
                            <div class="card">
                              <div class="card-body">
                                <h5 class="card-title">Capital Balance</h5>
                                <?php
                                $share_capital = returnSingleField($db, "SELECT SUM(amount) AS share_capital FROM capital_share WHERE member_id = ? ", "share_capital", [$_GET['id']]);
                                ?>
                                <a href="#" class="btn btn-primary"><?php echo number_format($share_capital); ?> Frws</a>
                              </div>
                            </div>
                          </div>
                          <div class="col-sm-3">
                            <div class="card">
                              <div class="card-body">
                                <h5 class="card-title">Savings Balance</h5>
                                <?php
                                $saving_balance = returnSingleField($db, "SELECT sum(sav_amount) AS savings FROM saving WHERE member_id =? ", "savings", [$_GET['id']]);
                                ?>
                                <a href="#" class="btn btn-success"><?php echo number_format($saving_balance); ?> Frws</a>
                              </div>
                            </div>
                          </div>
                          <div class="col-sm-3">
                            <div class="card">
                              <div class="card-body">
                                <h5 class="card-title">Active Loan Balance</h5>
                                <?php
                                $loan_balance = returnSingleField($db, "SELECT  SUM(a.loan_balance) AS loan_balance
                                                                                FROM (
                                                                                  SELECT  a.id AS loan_id,
                                                                                          a.loan_amount,
                                                                                          b.loan_amount AS b_amount,
                                                                                          (a.loan_amount - COALESCE(b.paid_amount,0)) AS loan_balance
                                                                                          FROM member_loans AS a
                                                                                          LEFT JOIN (
                                                                                            SELECT  SUM(b.amount) AS paid_amount,
                                                                                                    a.loan_amount AS loan_amount,
                                                                                                    a.id AS borrower_loan_id
                                                                                                    FROM member_loans AS a
                                                                                                    LEFT JOIN lend_payments AS b
                                                                                                    ON a.id = b.borrower_loan_id AND b.status = 'PAID'
                                                                                                    WHERE a.status = 'ACTIVE' AND a.member_id = ?
                                                                                                    AND a.reject = 0 AND a.president = 1 AND a.committee_status = 1
                                                                                                    GROUP BY a.id
                                                                                            ) AS b
                                                                                          ON a.id = b.borrower_loan_id
                                                                                          WHERE a.member_id = ?
                                                                                          AND a.status = 'ACTIVE' AND a.reject = 0 AND a.president = 1 AND a.committee_status = 1

                                                                        ) AS a
                                                                        ", "loan_balance", [$_GET['id'], $_GET['id']]);
                                // var_dump($loan_balance);
                                ?>
                                <a href="#" class="btn btn-warning"><?php echo number_format($loan_balance); ?> Frws</a>
                              </div>
                            </div>
                          </div>
                          <div class="col-sm-3">
                            <div class="card">
                              <div class="card-body">
                                <h5 class="card-title">Earnings per share</h5>
                                <a href="#" class="btn btn-primary"><?php echo number_format(ceil($profi['prit']),2); ?> Frws</a>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="post clearfix">
                      <div class="col-md-12">
                        <div class="box-body">
                          <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title" style="color:#008080"> ACTIVE LOAN  </h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body ">
              
                      <table class="table table-bordered table-responsive">
                        <tr>
                          <td>Date</td>
                          <td>Loan</td>
                          <td class="text-center">Loan Amount<br />[Frw]</td>
                          <td class="text-center">Paid Amount<br />[Frw]</td>
                          <td class="text-center">Balance<br />[Frw]</td>
                        </tr>
                        <tbody>
                          <?php

                          $loans_statement = returnAllData($db, "SELECT a.*,
                                                                        b.id AS type_id,
                                                                        b.terms AS terms,
                                                                        CONCAT(b.lname, '-', b.interest, '%') AS loan_name,
                                                                        c.paid_amount
                                                                        FROM member_loans AS a
                                                                        INNER JOIN loan_type AS b
                                                                        ON a.loan_id = b.id
                                                                        LEFT JOIN (
                                                                          SELECT  a.id AS loan_id,
                                                                                  SUM(b.amount) AS paid_amount
                                                                                  FROM member_loans AS a
                                                                                  LEFT JOIN lend_payments AS b
                                                                                  ON a.id = b.borrower_loan_id AND b.status = 'PAID'
                                                                                  WHERE a.member_id = ? AND a.status = 'ACTIVE'
                                                                                  GROUP BY a.id
                                                                          ) AS c
                                                                        ON a.id = c.loan_id
                                                                        WHERE a.member_id = ? AND a.status = 'ACTIVE'
                                                                        ORDER BY a.loan_date ASC
                                                                        ", [$_GET['id'],$_GET['id']]);

              					 foreach($loans_statement AS $row){ 
                            ?>
                            <tr style="color:<?php if($row['status']=="CLOSED"){echo "red";}else{echo "green";}?>">
                              <td><a href="Loan_info.php?loan_id=<?php echo $row['id'];?>"><?php echo $row['loan_date']; ?></a></td>
                              <td><?php echo $row['loan_name']; ?></td>
                              <td class="text-right"><?php echo number_format(ceil($row['loan_amount']), 2); ?></td>
                              <td class="text-right"><?= number_format($row['paid_amount']); ?></td>
                              <td class="text-right">
                                <?= number_format($row['loan_amount'] - $row['paid_amount']) ?>
                              </td>
                            </tr>
                            <?php 
                          }
                          ?>
                        </tbody>
                      </table>
            </div>
            <!-- /.box-body -->
          
          </div>
          <!-- /.box -->
        </div>
		<!-- / Savings table-->	
					<div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title" style="color:#008080"> CAPITAL SHARES ACCOUNT </h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body ">
              <ul class="products-list product-list-in-box">
                      <table class="table table-bordered table-responsive">
                        <tr>
                          <td>Year</td>
                          <td>Member Name</td>
                          <td>Amount</td>
                        </tr>
                       <tbody>
            					  <?php 
            					  $tot=0;
            					  $num=0;
            					  $month=0;
            					  $sql = "SELECT  *,
                                        SUM(a.amount) as gtotal, 
                                        CONCAT(b.fname,' ',b.lname) as firstname,
                                        b.id as m_idi 
                                        FROM capital_share AS a
                                        INNER JOIN member b 
                                        ON a.member_id=b.id 
                                        WHERE a.member_id=? 
                                        GROUP BY a.year
                                        ORDER BY a.date DESC";
                        $data = returnAllData($db, $sql, [$membe_id]);
            					  foreach($data AS $row ){
            						  $month=(int)$row['month'];
            						  $tot+=$row['gtotal'];
            						  $num++;
                          ?>
                          <tr>
                            <td><a href="capital_share_info.php?m_idi=<?php echo $row['m_idi']; ?>"><?php echo $row['year']; ?></a></td>
                            <td><?php echo $row['firstname']; ?></td>
                            <td><?php echo number_format($row['gtotal']); ?>  Frw (s)</td>
                          </tr>
                          <?php 
                        }?>
                        <tr>
                        <td colspan="6" style="color:#008080">Total: <?php echo number_format($tot)." Frws :".ucwords(convertNumberToWord($tot))."Rwandan francs"; ?></td>
                        </tr>
                       </tbody>
					   </table>
                
               
              </ul>
            </div>
            <!-- /.box-body -->
          
          </div>
          <!-- /.box -->
        </div>
			
			<div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title" style="color:#008080"> SAVING STATEMENT </h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body ">
              <ul class="products-list product-list-in-box">
                      <table class="table table-bordered table-responsive">
                        <tr>
                          <td>Year</td>
                          <td>Member Name</td>
                          <td>Amount</td>
                        </tr>
                       <tbody>
            					  <?php 
            					  $tot=0;
            					  $num=0;
            					  $month=0;
            					  $sql = "SELECT *,sum(s.sav_amount) as gtotal, CONCAT(fname,' ',lname) as firstname, s.member_id as m_idi  FROM `saving` s inner join member m on s.member_id=m.id where s.member_id='$membe_id' GROUP BY s.year ORDER BY s.year DESC";
            					   $quer=mysqli_query($con,$sql);
                        $data = returnAllData($db, "SELECT  SUM(a.sav_amount) AS gtotal,
                                                            CONCAT(b.fname, ' ', b.lname) AS firstname,
                                                            a.month,
                                                            a.year,
                                                            count(a.id) AS number_of_savings,
                                                            a.member_id
                                                            FROM saving AS a
                                                            INNER JOIN member AS b
                                                            ON a.member_id = b.id
                                                            WHERE b.id = ?
                                                            GROUP BY a.year
                                                            ORDER BY a.year DESC
                                                            ", [$_GET['id']]);
            					   foreach($data AS $row){
            						   $month=(int)$row['month'];
            						   $tot+=$row['gtotal'];
            						   $num++;
            					   ?>
                        <tr>
                          <td><a href="savings_info.php?m_id=<?php echo $row['member_id']; ?>&year=<?php echo $row['year']; ?>"><?php echo $row['year']; ?></a></td>
                          <td><?php echo $row['firstname']; ?></td>
                          <td><?php echo number_format(ceil($row['gtotal']), 2); ?>  Frw (s)</td>
                        </tr>
                        <?php } ?>
                      <tr>
                      <td colspan="3" style="color:#008080">Total: <?php echo number_format($tot)." Frws :".ucwords(convertNumberToWord($tot))."Rwandan francs, In ".date('Y'); ?></td>
                      </tr>
                       </tbody>
					   </table>
					   
					   
					   
		<!--SOCIAL SAVING STATEMENT-->	
		
		
		
		<div class="box-header with-border">
              <h3 class="box-title" style="color:#FF00FF"> SOCIAL SAVING STATEMENT </h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body ">
              <ul class="products-list product-list-in-box">
					   
                                          <table class="table table-bordered table-responsive">
                                            <tr>
                                              <td>Sl.No</td>
                                              <td>Member Name</td>
                                              <td>Amount</td>
                                              <td>Year</td>
                                              
                                              
                                            </tr>
                                          <tbody>
                                					  <?php 
                                					  $tot=0;
                                					  $num=0;
                                					  $month=0;
                                            $sql = "SELECT  SUM(a.amount) AS gtotal,
                                                            CONCAT(fname,' ',lname) as firstname,
                                                            a.year,
                                                            b.id as m_idi,
                                                            a.month,
                                                            a.year,a.id
                                                            FROM sacial_saving AS a
                                                            INNER JOIN member AS b
                                                            ON a.m_id = b.id
                                                            WHERE a.m_id = ?
                                                            GROUP BY a.year
                                                            ORDER BY a.year DESC
                                                            ";
                                					   $data=returnAllData($db, $sql , [$membe_id]);
                                					   foreach($data AS $row){
                                						   $month=(int)$row['month'];
                                						   $tot+=$row['gtotal'];
                                						   $num++;
                                  					   ?>
                                              <tr>
                                                <td><?php echo $num; ?></td>
                                                <td><?php echo $row['firstname']; ?></td>
                                                <td><?php echo number_format($row['gtotal']); ?>  Frw (s)</td>
                                                <td><?php echo $row['year']; ?> </td>
                                                <!--<td> <a href="delete_social.php?id=<?= $row['id'] ?>&m_id=<?= $row['m_idi'] ?>">Delete/Edit</a></td> -->
                                              </tr>
                                              <?php
                                            } 
                                            ?>
                                            <tr>
                                            <td colspan="6" style="color:#008080">Total: <?php echo number_format(ceil($tot),2)." Frws :".ucwords(convertNumberToWord($tot))."Rwandan francs" ?></td>
                                            </tr>
                                          </tbody>
                    					          </table>	   
					   
			<!--SOCIAL SAVING STATEMENT-->		   
              </ul>
            </div>
            <!-- /.box-body -->
          
          </div>
          <!-- /.box -->
        </div>
			 </div>
             </div>					 
           </div>
         </div>
       </div>
      </div>
     </div>
    </div>
   </div>
 </div>
          </div>
        </div>
      </div>
    </section>
    </form>  
  </div>
<div class="modal fade" id="modal-default">
  <form class="form-horizontal" name="loandata"  method="post">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary ">
          <h4 class="modal-title"><span style="color:white"><i class="fa fa-plus"></i> NEW LOAN ( <?php echo $rows['fname']." ".$rows['lname']; ?> ) </span>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </h4>
        </div>
        <div class="modal-body">
          <div class="card card-info">
            <div class="card-body">
              <div class="form-group row">
                <div class="alert warning">
                  <input type="checkbox" id="limiticheck" onclick="myFunction()" class="form-check-input" name="limiticheck"> - 
                  <strong>Amortization Based On Saving :</strong> <?php echo number_format(ceil($tot*$loan_limity),2); ?>
                </div>
                <p><?php $limit=$tot/$loan_limity; ?> <span></span></p>
                <input type="hidden" name="limiaount" id="limiaount" value="<?php echo $tot*$loan_limity; ?>" class="form-control">
              </div>
              <div class="form-group row">
                <label for="inputPassword3" class="col-sm-8 col-form-label">Applied Date :</label>
                <div class="col-sm-4">
                  <input type="text" id="datepicker" name="date" class="form-control" required>
                </div>
              </div>
              <div class="form-group row">
                <label for="inputPassword3" class="col-sm-8 col-form-label">Amount of the loan (any currency):</label>
                <div class="col-sm-4">
                  <input type="text" name="principal" id="principal" required size="12" onchange="calculate();" class="form-control">
                </div>
              </div>
              <div class="form-group row">
                <label for="inputPassword3" class="col-sm-8 col-form-label">Choose Loan Type:</label>
                <div class="col-sm-4">
                  <select name="interest" onchange="showUser(this.value)" required class="form-control">
                    <option></option>
                  	<?php 
                	  $sqi="SELECT * FROM `loan_type`";
                  	$query=mysqli_query($con,$sqi);
                  	while($rowu=mysqli_fetch_array($query)){
                      ?>
                      <option value="<?php echo $rowu['id']; ?>"><?php echo $rowu['lname']; ?></option>
                      <?php 
                    }
                    ?>
                  </select>
                </div>
              </div>
              <div class="form-group row">
                <label for="inputPassword3" class="col-sm-8 col-form-label">Annual percentage rate of interest:</label>
                <div class="col-sm-4">
                  <input type="text" name="percent" readonly id="percent"  required class="form-control">
                </div>
              </div>
              <div class="form-group row">
                <label for="inputPassword3" class="col-sm-8 col-form-label">Repayment period in years:</label>
                <div class="col-sm-4">
                  <input type="text" name="years" readonly  size="12" id="period" onchange="calculate();" required class="form-control">
                </div>
              </div>
              <div class="form-group row">
                <div class="col-sm-4">
                  <input type="button" value="Compute" onclick="calculate();" required class="form-control btn-primary">
                </div>
              </div>
              Payment Information:
              <div class="form-group row">
                <div class="col-sm-4">
                  <input type="text" readonly name="payment" size="12" class="form-control">
                </div>
                <div class="col-sm-4">
                  <input type="text" name="total" size="12" readonly class="form-control">
                </div>
                <div class="col-sm-4">
                  <input type="text" name="totalinterest" readonly size="12" class="form-control">
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <input type="submit" value="Save Loan" name="botton_save" class="btn btn-flat btn-sm btn-primary">
        </div>
      </div>
    </div>
  </form>
</div>
	  
<div class="modal fade" id="modal-saving">
  <form class="form-horizontal" method="post" action="./member_info.php?id=<?= $_GET['id']?>">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-success ">
          <h4 class="modal-title"><span style=""><i class="fa fa-plus"></i> Member Savings Entry Form(<?php echo $rows['fname']." ".$rows['lname']; ?>)  </span>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </h4>
        </div>
        <div class="modal-body">
          <div class="card card-info">
            <div class="card-body">
              <div class="form-group row">
                <label for="inputEmail3" class="col-sm-6 col-form-label"><?php echo $rows['fname']." ".$rows['lname']; ?></label>
                <div class="col-sm-2">
                  <input type="hidden" name="me_id" readonly class="form-control" value="<?php echo $rows['id']; ?>">
                  <input type="hidden" name="me_name" readonly class="form-control" value="<?php echo $rows['fname']." ".$rows['lname']; ?>">
                </div>
              </div>
              <?php
              $last_saving = first($db, "SELECT * FROM saving WHERE member_id = ? ORDER BY year DESC, month DESC LIMIT 0,1", [$_GET['id']]);

              //get the value of the next required saving payments
              $required_month = $last_saving['month'];
              $required_year = $last_saving['year'];
              if($required_month < 12){
                $required_month++;
              } else {
                $required_year++;
                $required_month=1;
              }

              $data = first($db, "SELECT * FROM overdue_settings WHERE month = ? AND year = ?", [$required_month, $required_year]);

              $saving_due = new \DateTime($data['saving_overdue']);
              $now = new \DateTime();
              $days = 0;
              if($saving_due->getTimestamp() < $now->getTimestamp()){
                //Get the number of days to count fines
                $days = $saving_due->diff($now)->days;
              }

              $fines = $days * 100; //This 100 is the fine per day of delay
              ?>
              <div class="form-group row">
                <label for="inputPassword3" class="col-sm-3 col-form-label"> Date: </label>
                <div class="col-sm-5">
                  <select size="1" name="ukwezi" class="form-control">
                    <?php formMonth($currentmonth, $required_month); ?>
                  </select>
                </div>
                <div class="col-sm-4">
                  <select size="1" name="year" class="form-control">
                    <?php formYear($currentyear, $required_year); ?>
                  </select>
                </div>
              </div>
              <div class="form-group row">
                <label for="inputPassword3" class="col-sm-3 col-form-label"> Saving Amount: </label>
                <div class="col-sm-4">
                 <input type="text" required name="amoun" onkeypress="return isNumber(event)"  class="form-control">
                </div>
                <label for="inputEmail3" class="col-sm-1 col-form-label">Fine:</label>
                <div class="col-sm-4">
                  <input type="text" required name="fine" value="<?= $fines ?>" onkeypress="return isNumber(event)"  class="form-control">
                </div>
              </div>
              <div class="form-group row">
                <label for="inputPassword3" class="col-sm-3 col-form-label"> Account : </label>
                <div class="col-sm-9">
                    <select name="bank_account" class="form-control">
                    <?php 
                    $sql = "SELECT * FROM `financial_account`";
                    $annn=mysqli_query($con,$sql);
                    while($roww=mysqli_fetch_array($annn)){
                      ?>
                      <option value="<?php echo $roww['id']; ?>"><?php echo $roww['Name'];?></option>
                      <?php 
                    }?>
                    </select>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <input type="submit" value="Save Savings" name="saving_save" class="btn btn-flat btn-sm btn-success">
        </div>
      </div>
    </div>
  </form>
</div>

<div class="modal fade" id="modal-share">
<form class="form-horizontal" method="post">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header bg-primary ">
              <h4 class="modal-title"><span style="color:white"><i class="fa fa-plus"></i> Membership Fees & Capital share  </span></h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
             <div class="card card-info">
                <div class="card-body">
                  <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-8 col-form-label"><?php echo $rows['fname']." ".$rows['lname']; ?></label>
                    <div class="col-sm-2">
                      <input type="text" name="me_id" readonly class="form-control" value="<?php echo $rows['id']; ?>">
                    </div>
					<div class="col-sm-2">
                      <input type="text" name="amount" readonly class="form-control" value="<?php echo $rows['civil_status']; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputPassword3" class="col-sm-6 col-form-label"> Membership Fee </label>
                    <div class="col-sm-4">
                     <input type="text" name="member_fee" required class="form-control">
                    </div>
					 <div class="col-sm-2">
                     <input type="text" readonly name="amount" value="Frws"  class="form-control">
                    </div>
                  </div>
                  
				   
				    <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-8 col-form-label"></label>
                    <div class="col-sm-4">
                      <input type="submit" name="fee_save" class="form-control btn-primary">
                    </div>
                  </div>
                </div>
            </div>
            </div>
          </div>
		  
        </div>
		 </form>
        <!-- /.modal-dialog -->
      </div>
      
      
    <div class="modal fade" id="modal-captal">
      <form class="form-horizontal" method="post" action="./member_info.php?id=<?= $_GET['id']?>">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header bg-primary ">
              <h4 class="modal-title">
                <span style="color:white"><i class="fa fa-plus"></i> Upgrade Capital share  </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </h4>
            </div>
            <div class="modal-body">
             <div class="card card-info">
                <div class="card-body">
                  <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-10 col-form-label"><?php echo $rows['fname']." ".$rows['lname']; ?></label>
                    <div class="col-sm-2">
                      <input type="hidden" name="me_id" readonly class="form-control" value="<?php echo $rows['id']; ?>">
                    </div>
				
                  </div>
                  <div class="form-group row">
                    <label for="inputPassword3" class="col-sm-4 col-form-label"> Add Capital Amount </label>
                    <div class="col-sm-6">
                     <input type="text" required name="amount"  class="form-control">
                    </div>
                    <div class="col-sm-2">
                     <input type="text" readonly name="fee" value="Frws"  class="form-control">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputPassword3" class="col-sm-4 col-form-label"> Date: </label>
                    <div class="col-sm-4">
                      <select size="1" name="ukwezi" class="form-control">
                        <?php formMonth($currentmonth); ?>
                      </select>
                    </div>
                    <div class="col-sm-4">
                      <select size="1" name="year" class="form-control">
                        <?php formYear($currentyear); ?>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer justify-content-between">
              <input type="submit" value="Update Share Capital" name="capital_save" class="btn btn-sm btn-flat btn-primary">
            </div>
          </div>
        </div>
		  </form>
    </div>
      
      
      <div class="modal fade" id="modal-upload_photo">
        <form action="./member_info.php?id=<?= $_GET['id'] ?>" method="POST" enctype="multipart/form-data">
          <div class="modal-dialog modal-md">
            <div class="modal-content">
              <div class="modal-header bg-yellow ">
                <h4 class="modal-title">
                  <span style="color:white"><i class="fa fa-pencil"></i> Change Profile Picture  </span>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </h4>
              </div>
              <div class="modal-body">
                <div class="card card-info">
                  <div class="card-body">
                    <div class="form-group row">
                      <label for="image" class="col-sm-3 col-form-label">Image: </label>
                      <div class="col-sm-9">
                        <input class="form-control form-control-sm" type="file" name="image" />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal-footer justify-content-between">
                <input class="btn btn-sm btn-flat btn-warning" type="submit" name="update_picture" value="Upload">
              </div>
            </div>
          </div>
        </form>
      </div>
  
  <!-- /.content-wrapper -->
  <?php include('./footer.php'); ?>
<!-- page script -->
<script language="JavaScript">
function calculate() {
    // Get the user's input from the form. Assume it is all valid.
    // Convert interest from a percentage to a decimal, and convert from
    // an annual rate to a monthly rate. Convert payment period in years
    // to the number of monthly payments.
    var principal = document.loandata.principal.value;
    var interest = document.loandata.percent.value;
    var payments = document.loandata.years.value;

    // Now compute the monthly payment figure, using esoteric math.
    var x = Math.pow(1 + interest, payments);
    var monthly = Math.ceil(principal/payments);

    // Check that the result is a finite number. If so, display the results
    if (!isNaN(monthly) && 
        (monthly != Number.POSITIVE_INFINITY) &&
        (monthly != Number.NEGATIVE_INFINITY)) {
        document.loandata.payment.value = monthly;
        document.loandata.total.value = principal;
        document.loandata.totalinterest.value = (principal*interest/100);
    }
    else {
        document.loandata.payment.value = "";
        document.loandata.total.value = "";
        document.loandata.totalinterest.value = "";
    }
}
// This simple method rounds a number to two decimal places.
function round(x) {
  return round(x*100)/100;
}

$(document).ready(function() {
  $("#datepicker").datepicker({
    format: 'yyyy-mm-dd',
    autoclose: true,
    todayHighlight: true,
  });
    $('#example').DataTable();
} );
</script>
</body>
</html>
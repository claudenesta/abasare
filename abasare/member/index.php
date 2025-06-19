<?php include('../DBController.php');
$membe_id=$_SESSION['acc'];

include('./header.php');
$active = "dashboard";
include('menu.php'); 
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Dashboard
      </h1>
      <ol class="breadcrumb">
        <li class="active"><i class="fa fa-dashboard"></i> Home</li>
      </ol>
    </section>
    <div class="col-md-12">
      <!-- ********** ALERT MESSAGE START******* -->
      <div class="col-md-12">
        <!-- ********** ALERT MESSAGE START******* -->
      </div>       <!-- ********** ALERT MESSAGE END******* -->
    </div>
    
    <section class="content">
      <div class="row">
        <!-- ********** ALERT MESSAGE START******* -->
          <div class="col-md-12">
          </div><!-- ********** ALERT MESSAGE END******* -->
          <div class="col-xs-12">
          <div class="box">
            <div class="box-header ">
              <h3 class="box-title">&nbsp;</h3>
            </div>
            <div class="box-body">
     <div class="container-fluid">
        <div class="row">
          <div class="col-md-3" id="member_profile">
          </div>
          <div class="col-md-9">
            <div class="card">
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="activity">
                    <!-- Post -->
                    <div class="post">
                      <div class="user-block">
                        <?php
                          $sql = "SELECT  sum(s.amount) as gtotal, 
                                          CONCAT(fname,' ',lname) as firstname,
                                          m.id as m_idi 
                                          FROM `capital_share` s 
                                          inner join member m 
                                          on s.member_id=m.id 
                                          where s.member_id=? 
                                          ";
                          // $quer=mysqli_query($con,$sql);
                          $info_data = first($db, $sql, [$membe_id]);
                          // var_dump($info_data);
                        ?>
                        <span class="username">
                          Capital Share: <?= number_format($info_data['gtotal']) ?> RWF
                        </span>
                      </div>
                        
                    </div>
                    <div class="post clearfix">
                    
	<div class="col-md-12">
            <div class="box-body">
			<div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <?php
              $profit_sql = "SELECT sum(amount) as prit FROM `profite` where m_id='$membe_id'";
              $querprofile=mysqli_query($con,$profit_sql);
              $profi=mysqli_fetch_array($querprofile);
              ?>
              <h3> LOAN STATEMENT  |  <a class="btn btn-warning btn-flat btn-sm" href="loans.php"><i class="fa fa-plus"></i> Apply for Loan</a> </i>Individual interest</a> <a href="#" class="btn btn-primary"><?php echo number_format(ceil($profi['prit']),2);?> Frws</a></h3>
                
            </div>
            
            
            <!-- /.box-header -->
            <div class="box-body ">
              <ul class="products-list product-list-in-box">
              
                      <table class="table table-bordered table-responsive" id="loans_table">
                        <thead>
                        <tr>
                          <td>Loan</td>
                          <td>Loan Amount</td>
                          <td>Paid</td>
                          <td>Period(Month)</td>
                          <td>Status</td>
                        </tr>
                      </thead>
                       <tbody>
                        <?php 
          					     $sql = "SELECT ml.loan_amount, 
                                        ml.committee_status, 
                                        ml.president_status, 
                                        ml.reject, 
                                        ml.status, 
                                        lt.terms, 
                                        ml.id as ididi, 
                                        CONCAT(lt.lname,' - ',lt.interest,'%') as name,
                                        SUM(lp.amount) AS paid_amount
                                        FROM `member_loans` ml 
                                        inner join loan_type lt 
                                        on lt.id=ml.loan_id
                                        LEFT JOIN lend_payments AS lp
                                        ON ml.id = lp.borrower_loan_id AND lp.status=?
                                        where member_id=? 
                                        AND ml.status IN (?,?)
                                        AND ml.committee_status = ?
                                        GROUP BY ml.id
                                        ORDER BY ml.id DESC
                                        ";
          					     $loan_info = returnAllData($db, $sql, ["PAID", $membe_id, 'ACTIVE', 'CLOSED', 1]);
                         // $quer=mysqli_query($con,$sql);
          					     foreach($loan_info AS $row){
                          ?>
                          <tr style="color:<?php if($row['status']=="CLOSED"){echo "green";}?>">
                            <td><a href="/member/Loan_info.php?loan_id=<?php echo $row['ididi'];?>"><?php echo $row['name']; ?></a></td>
                            <td><?php echo number_format(ceil($row['loan_amount'])); ?></td>
                            <td><?= number_format($row['paid_amount']) ?></td>
                            <td><?php echo $row['terms']; ?> Month( s )</td>
                            <td>
                              <?php if($row['committee_status']==0 && $row['president_status']==0 && $row['reject']==0){?>
                              <span style="color:#FFCC00"> Pending </span>
                              <?php }elseif($row['committee_status']==0 && $row['president_status']==1  && $row['reject']==0){ ?>
                              <span style="color:#FFCC00"> wait for Loan Committee  </span>
                              <?php }elseif($row['committee_status']==1 && $row['president_status']==0  && $row['reject']==0){ ?>
                              <span style="color:#FFCC00"> wait for President(s)  </span>
                              <?php }elseif($row['committee_status']==1 && $row['president_status']==1  && $row['reject']==0){ ?>
                              <span style="color:#008000	"><?= $row['status'] ?>  </span>
                              <?php }elseif($row['committee_status']==0 or $row['president_status']==0 && $row['reject']==1){ ?>
                              <span style="color:#FF0000">Rejected  </span>
                              <?php } ?>
                            </td>
                          </tr>
                          <?php 
                        }
                        ?>
                       </tbody>
					            </table>

              </ul>
            </div>
            <!-- /.box-body -->
          
          </div>
          <!-- /.box -->
        </div>
        
		<!-- / Savings table-->	
			
			<div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title" style="color:#008080"> SAVING STATEMENT <?php echo date("Y/m/d");?></h3>
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
					   $sql = "SELECT s.year_to_save_for, s.month_to_save_for,sum(s.sav_amount) as gtotal, CONCAT(fname,' ',lname) as firstname FROM `saving` s inner join member m on s.member_id=m.id where s.member_id=? GROUP BY s.year_to_save_for ORDER BY s.year_to_save_for DESC";
					   // echo $sql;
             $info = returnAllData($db, $sql, [$membe_id]);
             // var_dump($info);
             // $quer=mysqli_query($con,$sql);
					   foreach($info AS $row){
						   $month=(int)$row['month_to_save_for'];
						   $tot+=$row['gtotal'];
						   $num++;
               ?>
                        <tr>
                          <td><a href="savings_info.php?m_id=<?php echo $membe_id; ?>&&year=<?php echo $row['year_to_save_for']; ?>">#-<?php echo $num; ?></a></td>
                          <td><?php echo $row['firstname']; ?></td>
                          <td><?php echo number_format(ceil($row['gtotal']), 2); ?>  Frw (s)</td>
                          <td><?php echo $row['year_to_save_for']; ?> </td>
                        </tr>
  <?php } ?>
  <tr>
  <td colspan="5" style="color:#008080">Total: <?php echo number_format(ceil($tot),2)." Frws :".ucwords(convertNumberToWord($tot))."Rwandan francs "; ?></td>
  </tr>
                       </tbody>
					   </table>
                                      <div class="box-header with-border">
                                        <h3 class="box-title" style="color:#FF00FF"> SOCIAL SAVING STATEMENT </h3>
                                      </div>
                                      <div class="box-body ">
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
                                                <td><?php echo number_format(ceil($row['gtotal']), 2); ?>  Frw (s)</td>
                                                <td><?php echo $row['year']; ?> </td>
                                              </tr>
                                              <?php
                                            } 
                                            ?>
                                            <tr>
                                            <td colspan="6" style="color:#008080">Total: <?php echo number_format(ceil($tot),2)." Frws :".ucwords(convertNumberToWord($tot))."Rwandan francs" ?></td>
                                            </tr>
                                          </tbody>
                    					          </table> 
                                      </div>
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
    
    <!-- /.content -->
  </div>
 
  <!-- /.content-wrapper -->
  <?php include('./footer.php'); ?>

  <script type="text/javaScript">
    $(document).ready(function(){

      $("#loans_table").DataTable({
        ordering: false,
        pageLength: 6,
        lengthChange: false,
        info: false,
        bottomStart:null
      });
      
      $("#member_profile").load("./profile.php?member_id=<?= $membe_id ?>", function(){
        console.log("Profile Loaded!");
      })
    });
  </script>
</body>
</html>

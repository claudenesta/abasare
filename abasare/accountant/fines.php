<?php
include('./header.php');
$active = "fines";
include('./menu.php'); 
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Unpaid Fines</h1>
        <ol class="breadcrumb">
            <li><a href="/president/"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active"><a href="#">Unpaid Fines</a></li>
        </ol>
    </section>
    
    <section class="content">
        
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Unpaid Fines Information</h3>
                        <div class="btn-group">
                            <a href="fines/new.php" class="btn btn-danger btn-xs open_box">
                                <i class="fa fa-money"></i> New Fine
                            </a>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-12" id="fines_container"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include('./footer.php'); ?>

<script type="text/javascript">
    $(document).ready(function(){
        // Load initial fines data
        $("#fines_container").load("./fines/unpaid_fines.php");
        
        // New Fine button handler
        $(".open_box").click(function(e){
            e.preventDefault();
            var clicked = $(this);
            var url = clicked.attr("href");
            var old_data = clicked.html();
            clicked.html("Please Wait");
            
            $("#modal_member").find(".modal-content").load(url, function(){
                clicked.html(old_data);
                refresh_target_containner = 'fines_container';
                refresh_url = 'fines/unpaid_fines.php';
                $("#modal_member").modal("show");
            });
        });
    });
</script>
</body>
</html>

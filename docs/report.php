<?php 

$xls = [];
$doc = [];
$hits = 0;
$dir = getcwd() . '/reports/';
$scan = scandir($dir);

foreach($scan as $file)
{
	if (!is_dir($dir.$file) and (strpos($file, '.docx') !== false))
    {
		array_push ($doc, $file);
		$hits ++;
    }
	if (!is_dir($dir.$file) and (strpos($file, '.xlsx') !== false))
    {
		array_push ($xls, $file);
 		$hits ++;
   }
}

$asm = [];
$ltm = []; 
$ltm_go = 0;
$asm_go = 0;
$dir = getcwd() . '/config_files/';
$scan = scandir($dir);

foreach($scan as $file)
{
    if (is_dir($dir.$file) and !($file=="." || $file==".."))
    {
		array_push ($asm, $file);
    }
	if (!is_dir($dir.$file) and !($file=="." || $file==".."))
    {
		array_push ($ltm, $file);
    }
}

$policies_count = sizeof($asm);
if ($policies_count >0 )
{
	$asm_go = 1;
}


if (in_array("device_details.txt", $ltm) and in_array("monitor.txt", $ltm) and in_array("partitions.txt", $ltm) and in_array("pools.txt", $ltm) and in_array("profile.txt", $ltm) and in_array("provisioned_modules.txt", $ltm) and in_array("route_domain.txt", $ltm) and in_array("routes.txt", $ltm) and  in_array("vlans.txt", $ltm) and in_array("virtual_servers.txt", $ltm) and in_array("trunk.txt", $ltm) and in_array("ssl_cert.txt", $ltm)) {
   $ltm_go = 1;
}
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" href="images/favicon.ico" type="image/ico" />

    <title>ASM Policy Review </title>

    <!-- Bootstrap -->
    <link href="vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- Custom Theme Style -->
    <link href="build/css/custom.css" rel="stylesheet">
    <!-- Datatables -->
    <link href="vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="additional.css" />	

  </head>
  <body class="nav-sm">
    <div class="container body">
      <div class="main_container">

		<div class="col-md-3 left_col">
		  <div class="left_col scroll-view">
			<!-- sidebar menu -->
			<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">

			<div class="menu_section active">
				<ul class="nav side-menu">
				  <li><a href="index.php"><img src="images/f5_2.png" height=48px></a>
				  </li>
				  
				  <li class=""><a href="ltm.php"><i class="fa fa-edit"></i> LTM <span class="fa fa-chevron-down"></span></a>
				  </li>
				  <li><a><i class="fa fa-shield"></i> ASM <span class="fa fa-chevron-down"></span></a>
					<ul class="nav child_menu">
					  <?php 
							if($asm_go == 1)
							{
								foreach ($asm as $item)
								{
									echo '<li><a href="asm.php?policy='.$item.'">'.$item.'</a></li>';
								}
							}
							else
							{
								echo '<li><a href="index.php">No Policies</a></li>';
							}
					  ?>
					</ul>
				  </li>
				  <li><a href="report.php"><i class="fa fa-file-text"></i> Report <span class="fa fa-chevron-down"></span></a>
				  </li>
				  <li><a href="settings.php"><i class="fa fa-cog"></i> Settings</a>
				  </li>
				 </ul>
			  </div>
			</div>
			<!-- /sidebar menu -->

			
		  </div>
		</div>	  
	  
        <!-- page content -->
        <div class="right_col" role="main">
          <!-- top tiles -->
		<div class="row">
		<div class="x_title" style="font-size:24px">LTM Policy</div>
		</div>
		<div class="row">
            <div class="col-md-4 col-sm-4 col-xs-12">
              <div class="x_panel">
                <div class="x_title">
                  <h2>Create Report</h2>
                  <ul class="nav navbar-right panel_toolbox">
                    <li><a class="hide filter_icon" id=""><i class="fa fa-filter filter_icon_i"></i></a>
                    </li>
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </li>
                    <li><a class="close-link"><i class="fa fa-close"></i></a>
                    </li>
                  </ul>
                  <div class="clearfix"></div>
                </div>
                <div class="x_content">


					    <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom: 20px; text-align:center">
							<button id="ltm" type="button" class="btn btn-info" <?php if ($ltm_go==0) echo "disabled"; ?>>Create LTM <br> Report</button>						
						</div>

					    <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom: 20px; text-align:center">
							<button id="asm" type="button" class="btn btn-info" <?php if ($asm_go==0) echo "disabled"; ?>>Create ASM <br> Report</button> 
						</div>

					    <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom: 20px; text-align:center">
							<button id="ltm_asm" type="button" class="btn btn-info" <?php if ($asm_go==0 || $ltm_go==0) echo "disabled"; ?>>Create LTM/ASM <br> Report</button>
						</div>

                </div>
              </div>
            </div>
		

            <div class="col-md-8 col-sm-8 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Reports</h2> 
                    <ul class="nav navbar-right panel_toolbox">
                    <li><a class="hide filter_icon" id=""><i class="fa fa-filter filter_icon_i"></i></a>
                    </li>
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                      <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                    </ul>
 					<div class="clearfix"></div>
                  </div>
                  <div class="x_content">
	
					<div class="row tile_count" style="margin-bottom: 20px; text-align:center" >
						
						<?php foreach ($doc as $key) 
						{
							echo '<a href="reports/'.$key.'" download><div class="col-md-2 col-sm-2 col-xs-2 tile_stats_count">
						  <div style="margin-bottom: 10px;"><img src="images/word.png" height="64"/></div>
						  <span class="info" style="margin-top:3px"></i> <b>'.$key.' </b></span>
						  </div> </a>';
						} ?>
					</div>
					<div class="row tile_count" style="margin-bottom: 20px; text-align:center" >

						<?php foreach ($xls as $key) 
						{
							echo '<a href="reports/'.$key.'" download><div class="col-md-2 col-sm-2 col-xs-2 tile_stats_count">
						  <div style="margin-bottom: 10px;"><img src="images/excel.png" height="64"/></div>
						  <span class="info"></i> <b>'.$key.' </b></span>
						  </div> </a>';
						} ?>
						

					</div>
	
					<button id="delete" type="button" class="btn btn-danger" style="float:right">Delete Files</button>
					
                  </div>
                </div>
              </div>
   
          </div>
  

        
		</div>
        <!-- /footer content -->
      </div>
    </div>


   <!-- jQuery -->
    <script src="vendors/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- FastClick -->


    <!-- Custom Theme Scripts -->
    <script src="build/js/custom.js"></script>

    <!-- Datatables -->
    <script src="vendors/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>


  </body>
</html>



<script>
$( "#ltm" ).click(function() {
$("#ltm").html('Please wait <i class="fa fa-spinner fa-spin" style="font-size:24px"></i>');
$("#ltm").attr("disabled","disabled");
var request = $.get("create_report.php?report=ltm", function(data, status) { 
if (status=="success")
{
	location.reload();
}
})
});
</script>

<script>

$( "#asm" ).click(function() {
$("#asm").html('Please wait <i class="fa fa-spinner fa-spin" style="font-size:24px"></i>');
$("#asm").attr("disabled","disabled");
var request = $.get("create_report.php?report=asm", function(data, status) {
if (status=="success")
{
	location.reload();
}
})
});
</script>

<script>

$( "#ltm_asm" ).click(function() {
$("#ltm_asm").html('<i class="fa fa-spinner fa-spin" style="font-size:24px"></i>');
$("#ltm_asm").attr("disabled","disabled");
var request = $.get("create_report.php?report=ltm_asm", function(data, status) { 
if (status=="success")
{
	location.reload();
}
})
});
</script>



<script>
$( "#delete" ).click(function() {
var request = $.get("delete_files.php", function(data, status) { 
if (status=="success")
{
	location.reload();
}
})

});
</script>


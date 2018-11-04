<?php 

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

$string = file_get_contents("violation_list.json");
$violation_list = json_decode($string, true);

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
				<ul class="nav side-menu" style="">
				  <li><a href="index.php"><img src="images/f5_2.png" height=48px></a>
					
				  </li>
				  
				  <li ><a href="ltm.php"><i class="fa fa-edit"></i> LTM <span class="fa fa-chevron-down"></span></a>
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
				  <li class="current-page"><a href="settings.php"><i class="fa fa-cog"></i> Settings</a>
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
		<div class="x_title" style="font-size:24px">Settings</div>
		</div>
		<div class="row">
              <div class="x_panel">
                <div class="x_title">
                  <h2>Modify violation details</h2>
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
					
					<div>
<blockquote>

					<p><code class="highlighter-rouge">[Violation Details]</code> is not compatible with special characters like <b><code class="highlighter-rouge">" ' / # ^ ; & </code></b>. Please dont use these characters</p></blockquote>

					</div>
					<form>
					<?php 
						foreach ($violation_list as $var)
						{
							$var_error = "";
							$var_warning = "";
							$var_info = "";
							
							if ($var['severity']=="error")
								$var_error = "selected";
							if ($var['severity']=="warning")
								$var_warning = "selected";
							if ($var['severity']=="info")
								$var_info = "selected";							

							echo '
							<div class="form-group row">
								<label class="col-sm-1 col-form-label margin_top" id=>'.$var['name'].'</label>
								<label class="col-sm-1 col-form-label text-right margin_top">Severity:</label>
								<div class="col-sm-1">
									<select class="form-control" style="font-size: 16px; width:100%; cursor:pointer" id="'.$var['name'].'_severity">
										<option value="error" '.$var_error.'>Error</option>
										<option value="warning" '.$var_warning.'>Warning</option>
										<option value="info" '.$var_info.'>Info</option>
									</select>
								</div>
								<label class="col-sm-1 col-form-label text-right margin_top">Section:</label>
								<div class="col-sm-1">
									<input class="form-control" id="'.$var['name'].'_section" placeholder="score" value="'.$var['section'].'">
								</div>						
								<label class="col-sm-1 col-form-label text-right margin_top">Score:</label>
								<div class="col-sm-1">
								  <input class="form-control" id="'.$var['name'].'_score" placeholder="score" value='.$var['score'].'>
								</div>
								<label class="col-sm-1 col-form-label text-right margin_top">Violation Details:</label>
								<div class="col-sm-4">
								  <input class="form-control" id="'.$var['name'].'_details" placeholder="score" value="'.$var['txt'].'">
								</div>
							</div>';
						}
					?>
										</form>				  

					  <div class="form-group row">
						<span>
						  <button id="update" class="btn btn-primary">Save</button>
						</span>&nbsp&nbsp
						<span>
						  <button id="default" class="btn btn-warning">Restore Default Settings</button>
						</span>
						
					  </div>
                </div>
              </div>
		
	
        <!-- /page content -->


        
        
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

<script type="text/javascript">
function tooltip_init () {
  $(document).ready(function(){
  $('[data-toggle="tooltip"]').tooltip({
    placement : 'top'
  });
});
}
</script>

<script type="text/javascript">
$( "#update" ).click(function() {

	$("#update").html('<i class="fa fa-spinner fa-spin" style="font-size:24px"></i>');
	$("#update").attr("disabled","disabled");
<?php 
	$i=0;
	$new_violation_list= "{";
	foreach ($violation_list as $var)
	{
		if ($i>0)
			$new_violation_list .=  ",";
		$new_violation_list .= '"'.$var['name'].'":{"name":"'.$var['name'].'","severity":"\'+$("#'.$var['name'].'_severity'.'").val()+\'","section":"\'+$("#'.$var['name'].'_section'.'").val()+\'","score":\'+$("#'.$var['name'].'_score'.'").val()+\',"txt":"\'+$("#'.$var['name'].'_details'.'").val()+\'"}';
		$i++;
	}
	
	$new_violation_list .= "}";
	echo "var viol_list = 'violation_list=".$new_violation_list."'";
	?>	

		 $.post("save_violation_list.php", viol_list, function(data, status){
//			var results = JSON.parse(data);
			$("#update").html('Save');
			$("#update").removeAttr("disabled");
			location.reload();
		});	

});
</script>


<script type="text/javascript">
$( "#default" ).click(function() {

	$("#default").html('<i class="fa fa-spinner fa-spin" style="font-size:24px"></i>');
	$("#default").attr("disabled","disabled");
	$.get("restore_default_violation_list.php", function(data, status){
//			var results = JSON.parse(data);
			$("#default").html('Restore Default Settings');
			$("#default").removeAttr("disabled");
			location.reload();
		});	

});
</script>


  </body>
</html>

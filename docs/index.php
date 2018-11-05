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
	$asm_policies = [];

	foreach($asm as $file)
	{
		$error = 0;
		$info = 0;
		$warning = 0;
		$score = 0;
		
		if(file_exists("config_files/".$file."/suggestions.txt"))
		{
			$string = file_get_contents("config_files/".$file."/suggestions.txt");
			$suggestions = json_decode($string, true);
		
			foreach ($suggestions as $key) {
				if ($key['severity'] == 'info')
					$info++;
				if ($key['severity'] == 'warning')
					$warning++;
				if ($key['severity'] == 'error')
					$error++;
				$score = $score + $key['score'];
			}
		}
		else
		{
			$error = 0;
			$info = 0;
			$warning = 0;
			$score = -100;			
		}
		array_push ($asm_policies, '{"name":"'.$file.'", "info":'.$info.', "warning":'.$warning.', "error":'.$error.', "score":'.$score.'}');
	}
}
else 
{
$asm_policies = [];
array_push ($asm_policies, '{"name":"No Files Found", "info":"-", "warning":"-", "error":"-", "score":-100}');
$image_score = '<span class="badge" style="font-size:32px; padding:8px 15px; background-color:#6a6c6d">-</span>';
$final_score = "-";
}

if (in_array("device_details.txt", $ltm) and in_array("monitor.txt", $ltm) and in_array("partitions.txt", $ltm) and in_array("pools.txt", $ltm) and in_array("profile.txt", $ltm) and in_array("provisioned_modules.txt", $ltm) and in_array("route_domain.txt", $ltm) and in_array("routes.txt", $ltm) and  in_array("vlans.txt", $ltm) and in_array("virtual_servers.txt", $ltm) and in_array("trunk.txt", $ltm) and in_array("ssl_cert.txt", $ltm)) {
   $ltm_go = 1;
   
 	$ltm_policies = [];

	$error = 0;
	$info = 0;
	$warning = 0;
	$score = 0;

	if (file_exists("config_files/suggestions.txt"))
	{	
		$string = file_get_contents("config_files/suggestions.txt");
		$suggestions = json_decode($string, true);
		foreach ($suggestions as $key) {
			if ($key['severity'] == 'info')
				$info++;
			if ($key['severity'] == 'warning')
				$warning++;
			if ($key['severity'] == 'error')
				$error++;
			$score = $score + $key['score'];
		}
		$ltm_policies = '{"name":"LTM Configuration", "info":'.$info.', "warning":'.$warning.', "error":'.$error.', "score":'.$score.'}';

		$result_ltm = json_decode($ltm_policies, true);	
		$final_score_ltm = 100 - $result_ltm['score'];

		if ($final_score_ltm <60)
		{
			$image_score_ltm = '<span class="badge" style="font-size:32px; padding:8px 15px; background-color:red">F</span>';
		}
		if ($final_score_ltm >=60 && $final_score_ltm <70)
		{
			$image_score_ltm = '<span class="badge" style="font-size:32px; padding:8px 15px;background-color:orange ">D</span>';
		}
		if ($final_score_ltm >=70 && $final_score_ltm <80)
		{
			$image_score_ltm = '<span class="badge" style="font-size:32px; padding:8px 15px; background-color:gray;">C</span>';
		}
		if ($final_score_ltm >=80 && $final_score_ltm <90)
		{
			$image_score_ltm = '<span class="badge" style="font-size:32px; padding:8px 15px; background-color:#1D9B1E">B</span>';
		}			
		if ($final_score_ltm >=90 )
		{
			$image_score_ltm = '<span class="badge" style="font-size:32px; padding:8px 15px; background-color:#30CE31">A</span>';
		}	
	}
	else{
		$ltm_policies = '{"name":"LTM - Configuration", "info":0, "warning":0, "error":0, "score":0}';
		$final_score_ltm = 100;
		$result_ltm = json_decode($ltm_policies, true);
		$image_score_ltm = '<span class="badge" style="font-size:32px; padding:8px 15px; background-color:#6a6c6d">-</span>';	
	}
}
else{
	
$ltm_policies = '{"name":"LTM - No Files Found", "info":"-", "warning":"-", "error":"-", "score":"-"}';
$result_ltm = json_decode($ltm_policies, true);	
$image_score_ltm = '<span class="badge" style="font-size:32px; padding:8px 15px; background-color:#6a6c6d">-</span>';
$final_score_ltm = "-";

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

    <title>F5 Configuration Review </title>

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
				  
				  <?php 
						if($ltm_go == 1)
						{
							echo ' <li class=""><a href="ltm.php"><i class="fa fa-edit"></i> LTM <span class="fa fa-chevron-down"></span></a> </li>';
						}
						else{
							echo ' <li class=""><a><i class="fa fa-edit"></i> LTM <span class="fa fa-chevron-down"></span></a> </li>';
						}
				  ?>
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
								echo '<li><a>No Policies</a></li>';
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
				<div class="x_title" style="font-size:24px">LTM & ASM Policy</div>
			</div>
			<div class="row">
				<div class="col-md-8 col-sm-8 col-xs-12">
					<div class="x_panel">
					  <div class="x_title">
						<h2>Overview</h2> 
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

					 <table id="overall" class="table table-striped table-bordered" style="width:100%; font-size:13px">
						<thead>
						  <tr>
							<th>Configuration Items</th>
							<th style="width: 80px; text-align:center; color:#E74C3C;">Error</th>
							<th style="width: 100px; text-align:center; color: #d6c304;">Warning</th>
							<th style="width: 100px; text-align:center; color: #31708f;">Info</th>
							<th style="width: 100px; text-align:center;">Score</th>
						  </tr>
						</thead>
						<tbody style="text-align: center;">
							<tr >
								<td style="text-align: left; font-size:15px"><a href="ltm.php"><?php echo $result_ltm['name']; ?></a></td>
								<td style="color: #E74C3C; font-size:20px; font-weight: bold;"><?php echo $result_ltm['error']; ?></td>
								<td style="color: #d6c304; font-size:20px; font-weight: bold;"><?php echo $result_ltm['warning']; ?></td>
								<td style="color: #31708f; font-size:20px; font-weight: bold;"><?php echo $result_ltm['info']; ?></td>
								<td><?php echo $image_score_ltm; ?></td>
							</tr>
						
							
							<?php 	
								foreach($asm_policies as $key)
								{	
									$result = json_decode($key, true);	
										$final_score = 100 - (int)$result['score'];

										if ($final_score <60)
										{
											$image_score = '<span class="badge" style="font-size:32px; padding:8px 15px; background-color:red">F</span>';
										}
										if ($final_score >=60 && $final_score <70)
										{
											$image_score = '<span class="badge" style="font-size:32px; padding:8px 15px;background-color:orange ">D</span>';
										}
										if ($final_score >=70 && $final_score <80)
										{
											$image_score = '<span class="badge" style="font-size:32px; padding:8px 15px; background-color:gray;">C</span>';
										}
										if ($final_score >=80 && $final_score <90)
										{
											$image_score = '<span class="badge" style="font-size:32px; padding:8px 15px; background-color:#1D9B1E">B</span>';
										}			
										if ($final_score >=90 && $final_score <=100)
										{
											$image_score = '<span class="badge" style="font-size:32px; padding:8px 15px; background-color:#30CE31">A</span>';
										}
										if ($final_score >100 )
										{
											$image_score = '<span class="badge" style="font-size:32px; padding:8px 15px; background-color:#6a6c6d">-</span>';
										}										
									echo '
								<tr >
									<td style="text-align: left; font-size:15px"><a href="asm.php?policy='.$result['name'].'">ASM - '.$result['name'].'</a></td>
									<td style="color: #E74C3C; font-size:20px; font-weight: bold;">'.$result['error'].'</a></td>
									<td style="color: #d6c304; font-size:20px; font-weight: bold;">'.$result['warning'].'</a></td>
									<td style="color: #31708f; font-size:20px; font-weight: bold;">'.$result['info'].'</a></td>
									<td>'.$image_score.'</td>
								</tr>';
								}
								?>
						</tbody>
					 </table>

							<!-- end content  -->

					  </div>
					</div>
				</div>

				<div class="col-md-4 col-sm-4 col-xs-12">
					<div class="x_panel">
					  <div class="x_title">
						<h2>Upload new Files</h2> 
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
					  <div class="x_content" style="text-align:center">
						<form action="upload.php" method="post" enctype="multipart/form-data">
							<h4> Select the zip file with the configuration to upload: </h4>
							<input type="file" name="fileToUpload" id="fileToUpload" style="margin:auto">
							<br>
							<button class="btn btn-info" type="submit" onclick="return confirm('This will delete the existing configuration files')"> Upload new configs</button>
						</form>
						<form action="delete_dir.php" method="get">
							<button class="btn btn-danger" id="delete" onclick="return confirm('This will delete the existing configuration files')"> Delete configs</button>			
						</form>
					  </div>
					</div>
				</div>





			</div>
      </div>
    </div>
</div>

   <!-- jQuery -->
    <script src="vendors/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>


    <!-- Custom Theme Scripts -->
    <script src="../build/js/custom.js"></script>

    <!-- Datatables -->
    <script src="vendors/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>


  </body>
</html>

<script>
	$(document).ready(function() {
		var table = $('#overall').DataTable( {
				"autoWidth": false,
				"processing": true,
				"order": [[0, 'desc']]
		} );	
	} );
</script>


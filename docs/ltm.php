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


if (in_array("device_details.txt", $ltm) and in_array("monitor.txt", $ltm) and in_array("partitions.txt", $ltm) and in_array("pools.txt", $ltm) and in_array("profile.txt", $ltm) and in_array("provisioned_modules.txt", $ltm) and in_array("route_domain.txt", $ltm) and in_array("routes.txt", $ltm) and  in_array("vlans.txt", $ltm) and in_array("virtual_servers.txt", $ltm) and in_array("trunk.txt", $ltm) and in_array("ssl_cert.txt", $ltm)) 
{
   $ltm_go = 1;
}
else
{
	Header("Location: index.php");
	exit();
}


$string = file_get_contents("config_files/device_details.txt");
$device_details = json_decode($string, true);

$string = file_get_contents("config_files/provisioned_modules.txt");
$provisioned_modules = json_decode($string, true);

$string = file_get_contents("config_files/self_ips.txt");
$self_ips = "var self_ips = " . $string . " ;";

$string = file_get_contents("config_files/vlans.txt");
$vlans = "var vlans = " . $string . " ;";

$string = file_get_contents("config_files/virtual_servers.txt");
$vservers = "var vservers = " . $string . " ;";

$string = file_get_contents("config_files/pools.txt");
$pools = "var pools = " . $string . " ;";

$string = file_get_contents("config_files/trunk.txt");
$trunk = "var trunk = " . $string . " ;";

$string = file_get_contents("config_files/routes.txt");
$routes = "var routes = " . $string . " ;";

$string = file_get_contents("config_files/partitions.txt");
$partitions = "var partitions = " . $string . " ;";

$string = file_get_contents("config_files/ssl_cert.txt");
$ssl_cert = "var ssl_cert = " . $string . " ;";

$string = file_get_contents("config_files/rules.txt");
$rules = "var rules = " . $string . " ;";

$string = file_get_contents("config_files/persistence.txt");
$persistence = json_decode($string, true);
$persistence_cookie = "var persistence_cookie = " . json_encode($persistence['cookie']);
$persistence_source = "var persistence_source = " . json_encode($persistence['source']);


$string = file_get_contents("config_files/profile.txt");
$profile = json_decode($string, true);
$profile_http = "var profile_http = " . json_encode($profile['http']);
$profile_tcp = "var profile_tcp = " . json_encode($profile['tcp']);
$profile_ssl = "var profile_ssl = " . json_encode($profile['ssl']);

$string = file_get_contents("config_files/monitor.txt");
$monitor = json_decode($string, true);
$monitor_http = "var monitor_http = " . json_encode($monitor['http']);
$monitor_https = "var monitor_https = " . json_encode($monitor['https']);
$monitor_other = "var monitor_other = " . json_encode($monitor['other']);


if (file_exists("config_files/suggestions.txt"))
{
	$string = file_get_contents("config_files/suggestions.txt");
	$suggestions = "var suggestions = " . $string . " ;";

	$string = file_get_contents("config_files/suggestions.txt");
	$suggestions_initial = json_decode($string, true);

	$error = 0;
	$info = 0;
	$warning = 0;
	$score = 0;
	foreach ($suggestions_initial as $key) {
		if ($key['severity'] == 'info')
			$info++;
		if ($key['severity'] == 'warning')
			$warning++;
		if ($key['severity'] == 'error')
			$error++;
		$score = $score + (int)$key['score'];
	}
	
	$final_score = 100 - $score;
	if ($final_score <60)
	{
		$bar_class = '<span class="badge" style="font-size:128px; padding:20px 36px; background-color:red">F</span>';
	}
	if ($final_score >=60 && $final_score <70)
	{
		$bar_class = '<span class="badge " style="font-size:128px; padding:20px 36px; background-color:orange ">D</span>';
	}
	if ($final_score >=70 && $final_score <80)
	{
		$bar_class = '<span class="badge" style="font-size:128px; padding:20px 36px; background-color:gray;">C</span>';
	}
	if ($final_score >=80 && $final_score <90)
	{
		$bar_class = '<span class="badge" style="font-size:128px; padding:20px 36px; background-color:#1D9B1E">B</span>';
	}			
	if ($final_score >=90)
	{
		$bar_class = '<span class="badge" style="font-size:128px; padding:20px 36px; background-color:#30CE31">A</span>';
	}		
		
}
else
{
	$suggestions_exist=false;
	$suggestions = "var suggestions = []";
	$error = 0;
	$info = 0;
	$warning = 0;
	$score = 0;
	$final_score = 100;
	$bar_class = '<span class="badge" style="font-size:128px; padding:20px 36px; background-color:#6a6c6d">-</span>';	
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
				  <li class="current-page"><a href="ltm.php"><i class="fa fa-edit"></i> LTM <span class="fa fa-chevron-down"></span></a>
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
            <div class="col-md-3 col-sm-3 col-xs-12">
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


		
					<div class="row tile_count" style="margin-bottom: 20px; text-align:center" >
						<div class="col-md-4 col-sm-4 col-xs-4 tile_stats_count">
						  <span class="count_top red"><i class="fa fa-times-circle"></i> <b>Error </b></span>
						  <div id="var_error" class="count red"><?php echo $error; ?></div>
						</div>
						<div class="col-md-4 col-sm-4 col-xs-4 tile_stats_count">
						  <span class="count_top orange" ><i class="fa fa-warning"></i> <b>Warning </b></span>
						  <div id="var_warning" class="count orange"><?php echo $warning; ?></div>
						</div>
						
						<div class="col-md-4 col-sm-4 col-xs-4 tile_stats_count">
						  <span class="count_top text-info"><i class="fa fa-info"></i> <b>Info </b></span>
						  <div id="var_info" class="count text-info"><?php echo $info; ?></div>
						</div>
					</div>

					<div class="row tile_count" style="margin-bottom: 20px; text-align:center" >
						<div class="col-md-4 col-sm-4 col-xs-4" style="padding-top: 62px; font-size: 24px">
							<span class="current_score hidden"><?php echo $final_score; ?></span>Score: 
						</div>
						<div class="col-md-4 col-sm-4 col-xs-4 ">
							<span class="score"> <?php echo $bar_class; ?> </span>
						</div>
						
						<div class="col-md-4 col-sm-4 col-xs-4 ">

						</div>
					</div>
				  
                </div>
              </div>
            </div>
		

            <div class="col-md-9 col-sm-9 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Suggestions</h2> 
                    <ul class="nav navbar-right panel_toolbox">
                    <li><a class="hide filter_icon" id=""><i class="fa fa-filter filter_icon_i"></i></a>
                    </li>
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                      <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                    </ul>
					<ul class="nav navbar-right panel_toolbox" style="margin-right:30px"><button type="button" class="btn btn-success btn-sm" id="save" disabled>Save</button></ul>
                    <ul class="nav navbar-right panel_toolbox" style="margin-right:20px"><button type="button" class="btn btn-info btn-sm"  data-toggle="modal" data-target="#exampleModal">Add New</button></ul>
 					<div class="clearfix"></div>
                  </div>
                  <div class="x_content">

					<table id="suggestions" class="table table-striped table-bordered" style="width:100%">
						<thead>
						  <tr>
							<th style="width: 10px; text-align: center;"></th>
							<th>Suggestions</th>
							<th style="width: 15%; text-align: center;">Severity</th>
							<th style="width: 15%; text-align: center;">Category</th>
							<th style="width: 15px; text-align: center;"></th>
						  </tr>
						</thead>
					 </table>
						<!-- end content  -->

                  </div>
                </div>
              </div>
   
          </div>

		  
	  <div class="row">
		  <div class="x_panel tile">
			<div class="x_title">
			  <h2>Configuration Review</h2>
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
				<div class="" role="tabpanel" data-example-id="togglable-tabs">
					<ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
						<li role="presentation" class="active"><a href="#tab_overview" role="tab" data-toggle="tab" aria-expanded="true">Overview</a>
						</li>
						<li role="presentation" class=""><a href="#tab_networking" role="tab" data-toggle="tab" aria-expanded="true">Networking</a>
						</li>
						<li role="presentation" class=""><a href="#tab_pools" role="tab" data-toggle="tab" aria-expanded="false">Pools/Persistence</a>
						</li>
						<li role="presentation" class=""><a href="#tab_vservers" role="tab" data-toggle="tab" aria-expanded="false">Virtual Servers</a>
						</li>
						<li role="presentation" class=""><a href="#tab_profiles" role="tab" data-toggle="tab" aria-expanded="false">Profiles</a>
						</li>
						<li role="presentation" class=""><a href="#tab_monitors" role="tab" data-toggle="tab" aria-expanded="false">Monitors</a>
						</li>
						<li role="presentation" class=""><a href="#tab_ssl" role="tab" data-toggle="tab" aria-expanded="false">SSL</a>
						</li>
						<li role="presentation" class=""><a href="#tab_rules" role="tab" data-toggle="tab" aria-expanded="false">iRules</a>
						</li>
					</ul>
						<div id="myTabContent" class="tab-content">
							<div role="tabpanel" class="tab-pane fade active in" id="tab_overview" aria-labelledby="home-tab">
							 <!-- start content -->

								 <div class="col-md-4 col-sm-4 col-xs-12">
									<div class="x_panel">
									  <div class="x_title">
										<h2>General</h2>
										<div class="clearfix"></div>
									  </div>
									  <div class="x_content">
											 <table class="table" style="width:100%">
												<thead>
												  <tr> 
														<td style="width:130px">Platform: </td>
														<td><?php echo $device_details['marketingName']; ?></td>
												  </tr>
												  <tr> 
														<td>Hostname: </td>
														<td><?php echo $device_details['hostname']; ?></td>
												  </tr>
												  <tr> 
														<td>Version: </td>
														<td><?php echo $device_details['version']; ?></td>
												  </tr>
												  <tr> 
														<td>Chassis ID: </td>
														<td><?php echo $device_details['chassisId']; ?></td>
												  </tr>
												  <tr> 
														<td>DNS Servers: </td>
														<td><?php echo $device_details['nameServers']; ?></td>
												  </tr>
												  <tr> 
														<td>NTP Servers: </td>
														<td><?php echo $device_details['ntpServers']; ?></td>
												  </tr>											  
												  <tr> 
														<td>Syslog Servers: </td>
														<td><?php echo $device_details['remoteServers']; ?></td>
												  </tr>	
												  <tr> 
														<td>Time Zone: </td>
														<td><?php echo $device_details['timeZone']; ?></td>
												  </tr>	
												  <tr> 
														<td>Licenses Modules: </td>
														<td>
														<?php  
															foreach($device_details['activeModules'] as $key)
															{
																echo str_replace("|","<br>", $key) ;
																echo "<br>";
															}
														
														 ?></td>
												  </tr>	
												  </thead>
											 </table>

									  </div>
									</div>
								</div>

								 <div class="col-md-4 col-sm-4 col-xs-12">
									<div class="x_panel">
									  <div class="x_title">
										<h2>Provisioned Modules</h2>
										<div class="clearfix"></div>
									  </div>
									  <div class="x_content">
											 <table class="table" style="width:100%">
												<thead>
												  <tr> 
														<td>Local Traffic (LTM): </td>
														<td  style="width:80px;"><?php echo $provisioned_modules['ltm']; ?></td>
												  </tr>
												  <tr> 
														<td>Application Security (ASM): </td>
														<td><?php echo $provisioned_modules['asm']; ?></td>
												  </tr>
												  <tr> 
														<td>Access Policy (APM): </td>
														<td><?php echo $provisioned_modules['apm']; ?></td>
												  </tr>
												  <tr> 
														<td>Global Traffic (DNS): </td>
														<td><?php echo $provisioned_modules['gtm']; ?></td>
												  </tr>
												  <tr> 
														<td>Application Visibility and Reporting (AVR): </td>
														<td><?php echo $provisioned_modules['avr']; ?></td>
												  </tr>
												  <tr> 
														<td>Advanced Firewall (AFM): </td>
														<td><?php echo $provisioned_modules['afm']; ?></td>
												  </tr>											  
												  </thead>
											 </table>

									  </div>
									</div>
								</div>
							
								 <div class="col-md-4 col-sm-4 col-xs-12">
								<div class="x_panel">
								  <div class="x_title">
									<h2>HA Configuration</h2>
									<div class="clearfix"></div>
								  </div>
								  <div class="x_content">
										 <table class="table" style="width:100%">
											<thead>
											  <tr> 
													<td>Failover State: </td>
													<td><?php echo $device_details['failoverState']; ?></td>
											  </tr>
											  <tr> 
													<td>Failover Unicast IP: </td>
													<td><?php echo $device_details['unicastAddress']; ?></td>
											  </tr>
											  <tr> 
													<td>Management IP: </td>
													<td><?php echo $device_details['managementIp']; ?></td>
											  </tr>
											  <tr> 
													<td>ConfigSync IP: </td>
													<td><?php echo $device_details['configsyncIp']; ?></td>
											  </tr>
											  <tr> 
													<td>Mirror IP: </td>
													<td><?php echo $device_details['mirrorIP']; ?></td>
											  </tr>
											  <tr> 
													<td>Mirror IP (Secondary): </td>
													<td><?php echo $device_details['mirrorSecondaryIp']; ?></td>
											  </tr>											  
											  </thead>
										 </table>

								  </div>
								</div>
							</div>

							</div>

						<div role="tabpanel" class="tab-pane fade" id="tab_networking" aria-labelledby="profile-tab">
							<!-- start content -->
							
							 <div class="col-md-6 col-sm-6 col-xs-12">
								<div class="x_panel">
								  <div class="x_title">
									<h2>SelfIPs </h2>
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
									 <table id="self_ips" class="table table-striped table-bordered" style="width:100%">
									  <thead>
										<tr>
											<th>SelfIP Name</th>
											<th style="width: 65px;">IP Address</th>
											<th style="width: 70px;">Partition</th>
											<th style="width: 65px;">VLAN</th>
											<th style="width: 45px;">Floating</th>
											<th style="width: 65px;">Lockdown</th>
										</tr>
									  </thead>
									</table>

								  </div>
								</div>

								<div class="x_panel">
								  <div class="x_title">
									<h2>Routes </h2>
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
									 <table id="routes" class="table table-striped table-bordered" style="width:100%">
									  <thead>
										<tr>
											<th style="width: 15px; text-align: center;"></th>
											<th>Name</th>
											<th>Network</th>
											<th>Type</th>
											<th>Resource</th>
										</tr>
									  </thead>
									</table>

								  </div>
								</div>
							  </div>
 
 
							 <div class="col-md-6 col-sm-6 col-xs-12">
								<div class="x_panel">
								  <div class="x_title">
									<h2>VLANs</h2>
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
									<table id="vlans" class="table table-striped table-bordered" style="width:100%">
									  <thead>
										<tr>
											<th style="width: 15px; text-align: center;"></th>
											<th>VLAN Name</th>
											<th style="width: 65px;">Partition</th>
											<th style="width: 85px;">AutoLastHop</th>
											<th style="width: 65px;">Failsafe</th>
											<th style="width: 75px;">Interfaces</th>
										</tr>
									  </thead>
									</table>
										  <!-- end content -->
								  </div>
								</div>
								<div class="x_panel">
									<div class="x_title">
										<h2>Partitions</h2>
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
										<table id="partitions" class="table table-striped table-bordered" style="width:100%">
										  <thead>
											<tr>
												<th>Partition Name</th>
												<th>Default Route Domain </th>
											</tr>
										  </thead>
										</table>
										  <!-- end content -->
									</div>
								</div>

								<div class="x_panel">
									<div class="x_title">
										<h2>Trunks</h2>
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
										<table id="trunk" class="table table-striped table-bordered" style="width:100%">
										  <thead>
											<tr>
												<th>Name</th>
												<th>Distribution</th>
												<th>LACP</th>
												<th>Media</th>
												<th>Interfaces</th>
											</tr>
										  </thead>
										</table>
										  <!-- end content -->
									</div>
								</div>
							</div>							

							<!-- end content -->
						 </div>

						<div role="tabpanel" class="tab-pane fade" id="tab_pools" aria-labelledby="profile-tab">
						<!-- start content -->

							 <div class="col-md-6 col-sm-6 col-xs-12">
								<div class="x_panel">
								  <div class="x_title">
									<h2>Pools</h2>
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

									<table id="pools" class="table table-striped table-bordered" style="width:100%">
											<thead>
											<tr>
												<th style="width: 15px; text-align: center;"></th>
												<th>Pool Name</th>
												<th>Pool Members</th>
												<th>LB Algorithm</th>
												<th>Health Monitors</th>										
								
											</tr>
										</thead>
									</table>
										  <!-- end content -->
								  </div>
								</div>

								<div class="x_panel">
								  <div class="x_title">
									<h2>Cookie Persistence</h2>
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

									<table id="persistence_cookie" class="table table-striped table-bordered" style="width:100%">
											<thead>
											<tr>
												<th style="width: 15px; text-align: center;"></th>
												<th>Name</th>
												<th>Cookie Encryption</th>
												<th>Cookie Names</th>
												<th>Encrypt Pool</th>										
												<th>Parent</th>										
								
											</tr>
										</thead>
									</table>
										  <!-- end content -->
								  </div>
								</div>
								
 							</div>							

							 <div class="col-md-6 col-sm-6 col-xs-12">
							 
								<div class="x_panel">
								  <div class="x_title">
									<h2>Vservers Overview</h2>
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

									<table id="vserver_overview" class="table table-striped table-bordered" style="width:100%">
											<thead>
											<tr>
												<th>Name</th>
												<th>Address</th>
												<th>Pool</th>
												<th>Persitence</th>
											</tr>
										</thead>
									</table>
										  <!-- end content -->
								  </div>
								</div>

								<div class="x_panel">
								  <div class="x_title">
									<h2>Source Persistence</h2>
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

									<table id="persistence_source" class="table table-striped table-bordered" style="width:100%">
											<thead>
											<tr>
												<th style="width: 15px; text-align: center;"></th>
												<th>Name</th>
												<th>Mask</th>
												<th>Mirror</th>
												<th>Parent</th>
											</tr>
										</thead>
									</table>
										  <!-- end content -->
								  </div>
								</div>





    						</div>							

							  
						<!-- end content -->
					</div>

						<div role="tabpanel" class="tab-pane fade" id="tab_vservers" aria-labelledby="profile-tab">
							<!-- start content -->
							
													
							 <div class="col-md-12 col-sm-12 col-xs-12">
								<div class="x_panel">
								  <div class="x_title">
									<h2>Virtual Servers</h2>
									<div class="clearfix"></div>
								  </div>
								  <div class="x_content">

									 <table id="vservers" class="table table-striped table-bordered" style="width:100%">
										<thead>
											<tr>
												<th style="width: 15px; text-align: center;"></th>
												<th>Vserver Name</th>
												<th style="width: 90px;">IP Address:Port</th>
												<th>Pool</th>
												<th>iRules</th>
												<th>Profiles</th>
												<th>Policies</th>
												<th>Persistence</th>
												<th>Log Profiles</th>
												<th style="width: 42px; text-align: center;">Enabled</th>
											</tr>
										</thead>
									</table>
										  <!-- end content -->
								  </div>
								</div>
							  </div>							
						</div>

						<div role="tabpanel" class="tab-pane fade" id="tab_profiles" aria-labelledby="profile-tab">
						<!-- start content -->

							 <div class="col-md-7 col-sm-7 col-xs-12">
								<div class="x_panel">
								  <div class="x_title">
									<h2>HTTP Profiles</h2>
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

									<table id="profile_http" class="table table-striped table-bordered" style="width:100%">
											<thead>
											<tr>
												<th style="width: 15px; text-align: center;"></th>
												<th>Name</th>
												<th>Encrypt Cookies</th>
												<th>Fallback Host</th>
												<th>Parent</th>										
								
											</tr>
										</thead>
									</table>
										  <!-- end content -->
								  </div>
								</div>
								
 							</div>							

							 <div class="col-md-5 col-sm-5 col-xs-12">
								<div class="x_panel">
								  <div class="x_title">
									<h2>TCP Profiles</h2>
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

									<table id="profile_tcp" class="table table-striped table-bordered" style="width:100%">
											<thead>
											<tr>
												<th style="width: 15px; text-align: center;"></th>
												<th>Name</th>
												<th>Idle Timeout</th>
												<th>Keep Alive</th>
												<th>Parent</th>
											</tr>
										</thead>
									</table>
										  <!-- end content -->
								  </div>
								</div>

    						</div>							

							  
						<!-- end content -->
					</div>

						<div role="tabpanel" class="tab-pane fade" id="tab_monitors" aria-labelledby="profile-tab">
						<!-- start content -->

							 <div class="col-md-7 col-sm-7 col-xs-12">
								<div class="x_panel">
								  <div class="x_title">
									<h2>HTTP Monitors</h2>
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

									<table id="monitor_http" class="table table-striped table-bordered" style="width:100%">
											<thead>
											<tr>
												<th style="width: 15px; text-align: center;"></th>
												<th>Name</th>
												<th>Interval</th>
												<th>Timeout</th>
												<th>Send String</th>										
												<th>Receive String</th>										
											</tr>
										</thead>
									</table>
										  <!-- end content -->
								  </div>
								</div>
								<div class="x_panel">
								  <div class="x_title">
									<h2>HTTPS Monitors</h2>
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

									<table id="monitor_https" class="table table-striped table-bordered" style="width:100%">
											<thead>
											<tr>
												<th style="width: 15px; text-align: center;"></th>
												<th>Name</th>
												<th>Interval</th>
												<th>Timeout</th>
												<th>Send String</th>										
												<th>Receive String</th>										
											</tr>
										</thead>
									</table>
										  <!-- end content -->
								  </div>
								</div>
																
 							</div>							

							 <div class="col-md-5 col-sm-5 col-xs-12">
								<div class="x_panel">
								  <div class="x_title">
									<h2>TCP Monitors</h2>
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

									<table id="monitor_other" class="table table-striped table-bordered" style="width:100%">
											<thead>
											<tr>
												<th style="width: 15px; text-align: center;"></th>
												<th>Name</th>
												<th>Interval</th>
												<th>Timeout</th>
												<th>Protocol</th>
											</tr>
										</thead>
									</table>
										  <!-- end content -->
								  </div>
								</div>

    						</div>							

							  
						<!-- end content -->
					</div>

						<div role="tabpanel" class="tab-pane fade" id="tab_ssl" aria-labelledby="profile-tab">
						<!-- start content -->

							 <div class="col-md-7 col-sm-7 col-xs-12">
								<div class="x_panel">
								  <div class="x_title">
									<h2>SSL Client Profiles</h2>
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

									<table id="profile_ssl" class="table table-striped table-bordered" style="width:100%">
											<thead>
											<tr>
												<th style="width: 15px; text-align: center;"></th>
												<th>Name</th>
												<th>Certificate</th>
												<th>Key</th>
												<th>Cipher Group</th>										
												<th>Ciphers</th>										
												<th>Parent</th>										
								
											</tr>
										</thead>
									</table>
										  <!-- end content -->
								  </div>
								</div>
								
 							</div>							

							 <div class="col-md-5 col-sm-5 col-xs-12">
								<div class="x_panel">
								  <div class="x_title">
									<h2>Certificates</h2>
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

									<table id="ssl_cert" class="table table-striped table-bordered" style="width:100%">
											<thead>
											<tr>
												<th style="width: 15px; text-align: center;"></th>
												<th>Name</th>
												<th>Created By</th>
												<th>Expiration Date</th>
											</tr>
										</thead>
									</table>
										  <!-- end content -->
								  </div>
								</div>

    						</div>							

							  
						<!-- end content -->
					</div>
						 
						<div role="tabpanel" class="tab-pane fade" id="tab_rules" aria-labelledby="profile-tab">
						<!-- start content -->

							<div class="x_panel">
								  <div class="x_title">
									<h2>iRules</h2>
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

									<table id="rules" class="table table-striped table-bordered" style="width:100%">
											<thead>
											<tr>
												<th style="width: 15px; text-align: center;"></th>
												<th>iRule Name</th>
											</tr>
										</thead>
									</table>
										  <!-- end content -->
								  </div>
								</div>
								
						<!-- end content -->
					</div>
						 

						 
						</div>
				</div>
			</div>
		  </div>
        </div>
        <!-- /page content -->

	  

		<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		  <div class="modal-dialog" role="document">
			<div class="modal-content">
			  <div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Add New Suggesion</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				 
				</button>
			  </div>
			  <div class="modal-body">
				
				<form>
				  <div class="form-group row">
					<label class="col-sm-2 col-form-label" style="font-size: 14px;">Suggesion</label>
					<div class="col-sm-10">
					  <input class="form-control" id="input_suggestion">
					</div>
				  </div>
				  <div class="form-group row">
					<label class="col-sm-2 col-form-label" style="font-size: 14px;">Score</label>
					<div class="col-sm-10">
					  <input class="form-control" id="input_score">
					</div>
				  </div>
				  <div class="form-group row">
					<label class="col-sm-2  col-form-label" style="font-size: 14px;">Category</label>
					<div class="col-sm-10">
					  <select class="custom-select custom-select-lg mb-3" size="14" style="font-size: 16px; width:100%; cursor:pointer" id="input_category">
						<option value="Overview" selected>Overview</option>
						<option value="HA Configuration">HA Configuration</option>
						<option value="Provisioned Modules">Provisioned Modules</option>
						<option value="Pools">Pools</option>
						<option value="Virtual Servers">Virtual Servers</option>
						<option value="Persistence">Persistence</option>
						<option value="Profiles">Profiles</option>
						<option value="Monitors">Monitors</option>
						<option value="Certs">Certs</option>
						<option value="SelfIPs">SelfIPs</option>
						<option value="VLANs">VLANs</option>
						<option value="Partitions">Partitions</option>
						<option value="Trunks">Trunks</option>
						<option value="Routes">Routes</option>
						</select>
					</div>
				  </div>
				  <div class="form-group row">
					<label class="col-sm-2 col-form-label" style="font-size: 14px;">Severity</label>
					<div class="col-sm-10">
					  <select class="custom-select custom-select-lg mb-3" size="3" style="font-size: 16px; width:100%; cursor:pointer" id="input_severity">
						<option value="error" selected>Error</option>
						<option value="warning">Warning</option>
						<option value="info">Info</option>
						</select>
					</div>
				  </div>
				</form>
							
				
			  </div>
			  <div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" id="add_row" data-dismiss="modal">Save changes</button>
			  </div>
			</div>
		  </div>
		</div>	  
        <!-- footer content -->        
        
        
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




<script>

$( "#save" ).click(function() {
var table = $('#suggestions').DataTable();
var payload = "["
var i;
for (i = 0; i < table.rows().count(); i++) { 
	var txt = table.cell( i, 1).data();
	var severity = table.cell( i, 2).data();
	var section = table.cell( i, 3).data();
	var score = table.cell( i, 5).data();
	if(i>0)
	{
		payload = payload + ', ';
	}
	payload = payload + '{"severity":"'+severity+'","section":"'+section+'","score":'+score+',"txt":'+JSON.stringify(txt)+'}';

}
payload = payload + "]"

 console.log(payload);
 $.post( "save_ltm.php",  { name: payload});
});
</script>


<!-- Fetch the details for the Statistics Panel -->			
<script>

$( "#add_row" ).click(function() {
	var table = $('#suggestions').DataTable();
	var input_category = $( "#input_category option:selected" ).val();
	var input_suggestion = $( "#input_suggestion" ).val();
	var input_severity = $( "#input_severity option:selected" ).val();
	var input_score = $( "#input_score" ).val();
	$( "#input_score" ).val("");
	$( "#input_suggestion" ).val("");

	table.row.add( {
			"severity": input_severity,
			"txt": input_suggestion,
			"severity": input_severity,
			"section": input_category,
			"score": input_score
		} ).draw();
	$('#save').removeAttr("disabled");
	
		var warning = parseInt($("#var_warning").text());
		var error = parseInt($("#var_error").text());
		var info = parseInt($("#var_info").text());
		var current_score = parseInt($(".current_score").text());
		var new_score = current_score - input_score;
		if(input_severity=="error")
		{
			error = error + 1;
			$("#var_error").html(error); 
		}
			   
		if(input_severity=="warning")
		{
			warning = warning + 1;
			$("#var_warning").html(warning); 
		}    	   	
		if(input_severity=="info")
		{
			info = info + 1;
			$("#var_info").html(info); 
		}   
		$(".current_score").html(new_score);
		
		if (new_score <60)
		{
			$(".score").html('<span class="badge" style="font-size:128px; padding:20px 36px;; background-color:red">F</span>');
		}
		if (new_score >=60 && new_score <70)
		{
			$(".score").html('<span class="badge" style="font-size:128px; padding:20px 36px;; background-color:orange ">D</span>');
		}
		if (new_score >=70 && new_score <80)
		{
			$(".score").html('<span class="badge" style="font-size:128px; padding:20px 36px;; background-color:gray;">C</span>');
		}
		if (new_score >=80 && new_score <90)
		{
			$(".score").html('<span class="badge" style="font-size:128px; padding:20px 36px;; background-color:#1D9B1E">B</span>');
		}			
		if (new_score >=90 )
		{
			$(".score").html('<span class="badge" style="font-size:128px; padding:20px 36px;; background-color:#30CE31">A</span>');
		}	

		table.row(this).remove().draw( false );
		$('#save').removeAttr("disabled");	
	
});
</script>


<!-- Fetch the details for the Statistics Panel -->			

<script>

	<?php echo $self_ips; ?>
	
	$(document).ready(function() {
		var table = $('#self_ips').DataTable( {
			"data": self_ips,
			"createdRow": function( row, data, dataIndex ) {
				var remove_partition = "/" + data['partition'] + "/";
				$('td', row).eq(3).html(data['vlan'].replace(remove_partition, ""));
			  },
			  "columns": [
				{ "className": 'bold',"data": "name" },
				{ "data": "address"},
				{ "data": "partition"},
				{ "data": "vlan"},
				{ "data": "floating"},
				{ "data": "allowService"}
				],
				"autoWidth": false,
				"processing": true,
				"language": {"processing": "Waiting.... " }
		} );
	} );
</script>


<script>
function format_vlans ( d ) {
    // `d` is the original data object for the row

    return '<table cellpadding="5" cellspacing="0" border="0" class="table table-bordered">'+
        '<tr>'+
          '<td style="width:150px"><b>Failsafe Action:</b></td>'+
          '<td >'+d.failsafeAction+'</td>'+
        '</tr>'+ 
		'<tr>'+
          '<td style="width:150px"><b>Failsafe Timeout:</b></td>'+
          '<td >'+d.failsafeTimeout+'</td>'+
        '</tr>'+
         '<tr>'+
          '<td style="width:150px"><b>MTU:</b></td>'+
          '<td >'+d.mtu+'</td>'+
        '</tr>'+
        '</table>';
}

	<?php echo $vlans; ?>
	
	$(document).ready(function() {
		var table = $('#vlans').DataTable( {
			"data": vlans,
			"columns": [
				{
					"className":      'details-control',
					"orderable":      false,
					"data":           null,
					"defaultContent": ''
				},
				{ "className": 'bold',"data": "name" },
				{ "data": "partition"},
				{ "data": "autoLasthop"},
				{ "data": "failsafe"},
				{ "data": "interfaces"}
				],
				"autoWidth": false,
				"processing": true,
				"order": [[2, 'asc']]
		} );	
		
    $('#vlans tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );
 
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format_vlans(row.data()) ).show();
            tr.addClass('shown');
        }
    } );
	} );
</script>




<script>
	<?php echo $partitions; ?>
	
	$(document).ready(function() {
		var table = $('#partitions').DataTable( {
			"data": partitions,
			"columns": [
				{ "className": 'bold',"data": "name" },
				{ "data": "defaultRouteDomain"}
				],
				"autoWidth": false,
				"processing": true
		} );	
	} );
</script>



<script>

	<?php echo $trunk; ?>
	
	$(document).ready(function() {
		var table = $('#trunk').DataTable( {
			"data": trunk,
			"createdRow": function( row, data, dataIndex ) {
//				var remove_partition = "/" + data['partition'] + "/";
//				$('td', row).eq(3).html(data['vlan'].replace(remove_partition, ""));
			  },
			  "columns": [
				{ "className": 'bold',"data": "name" },
				{ "data": "distributionHash"},
				{ "data": "lacp"},
				{ "data": "media"},
				{ "data": "interfaces"}
				],
				"autoWidth": false,
				"processing": true,
				"language": {"processing": "Waiting.... " }
		} );
	} );
</script>


<script>
function format_routes ( d ) {
    // `d` is the original data object for the row

    return '<table cellpadding="5" cellspacing="0" border="0" class="table table-bordered">'+
        '<tr>'+
          '<td style="width:150px"><b>Partition:</b></td>'+
          '<td >'+d.partition+'</td>'+
        '</tr>'+ 
         '<tr>'+
          '<td style="width:150px"><b>MTU:</b></td>'+
          '<td >'+d.mtu+'</td>'+
        '</tr>'+
        '</table>';
}

	<?php echo $routes; ?>
	
	$(document).ready(function() {
		var table = $('#routes').DataTable( {
			"data": routes,
			"columns": [
				{
					"className":      'details-control',
					"orderable":      false,
					"data":           null,
					"defaultContent": ''
				},
				{ "className": 'bold',"data": "name" },
				{ "data": "network"},
				{ "data": "type"},
				{ "data": "resource"}
				],
				"autoWidth": false,
				"processing": true,
				"order": [[2, 'asc']]
		} );	
		
    $('#routes tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );
 
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format_routes(row.data()) ).show();
            tr.addClass('shown');
        }
    } );
	} );
</script>


<script>
function format_pools ( d ) {
    // `d` is the original data object for the row

    return '<table cellpadding="5" cellspacing="0" border="0" class="table table-bordered">'+
        '<tr>'+
          '<td style="width:150px"><b>Partition:</b></td>'+
          '<td >'+d.partition+'</td>'+
        '</tr>'+ 
         '<tr>'+
          '<td style="width:150px"><b>SubPath:</b></td>'+
          '<td >'+d.subPath+'</td>'+
        '</tr>'+
        '</table>';
}

	<?php echo $pools; ?>
	
	$(document).ready(function() {
		var table = $('#pools').DataTable( {
			"createdRow": function( row, data, dataIndex ) {
				var remove_partition = "/" + data['partition'] + "/";
			},			
			"data": pools,
			"columns": [
				{
					"className":      'details-control',
					"orderable":      false,
					"data":           null,
					"defaultContent": ''
				},
				{ "className": 'bold',"data": "name" },
				{ "data": "members"},
				{ "data": "loadBalancingMode"},
				{ "data": "pool_monitor"}
				],
				"autoWidth": false,
				"processing": true,
				"order": [[2, 'asc']]
		} );	
		
    $('#pools tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );
 
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format_pools(row.data()) ).show();
            tr.addClass('shown');
        }
    } );
	} );
</script>

<script>
function format_persistence_cookie ( d ) {
    // `d` is the original data object for the row

    return '<table cellpadding="5" cellspacing="0" border="0" class="table table-bordered">'+
        '<tr>'+
          '<td style="width:150px"><b>Partition:</b></td>'+
          '<td >'+d.partition+'</td>'+
        '</tr>'+ 
        '</table>';
}

	<?php echo $persistence_cookie; ?>
	
	$(document).ready(function() {
		var table = $('#persistence_cookie').DataTable( {
			"data": persistence_cookie,
			"columns": [
				{
					"className":      'details-control',
					"orderable":      false,
					"data":           null,
					"defaultContent": ''
				},
				{ "className": 'bold',"data": "name" },
				{ "data": "cookieEncryption"},
				{ "data": "cookieName"},
				{ "data": "encryptCookiePoolname"},
				{ "data": "defaultsFrom"}
				],
				"autoWidth": false,
				"processing": true,
				"order": [[2, 'asc']]
		} );	
		
    $('#persistence_cookie tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );
 
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format_persistence_cookie(row.data()) ).show();
            tr.addClass('shown');
        }
    } );
	} );
</script>



<script>
function format_persistence_source ( d ) {
    // `d` is the original data object for the row

    return '<table cellpadding="5" cellspacing="0" border="0" class="table table-bordered">'+
        '<tr>'+
          '<td style="width:150px"><b>Partition:</b></td>'+
          '<td >'+d.partition+'</td>'+
        '</tr>'+ 
        '<tr>'+
          '<td style="width:150px"><b>Match Across Pools:</b></td>'+
          '<td >'+d.matchAcrossPools+'</td>'+
        '</tr>'+ 
        '<tr>'+
          '<td style="width:150px"><b>Match Across Virtuals:</b></td>'+
          '<td >'+d.matchAcrossVirtuals+'</td>'+
        '</tr>'+ 		
        '</table>';
}

	<?php echo $persistence_source; ?>
	
	$(document).ready(function() {
		var table = $('#persistence_source').DataTable( {
			"data": persistence_source,
			"columns": [
				{
					"className":      'details-control',
					"orderable":      false,
					"data":           null,
					"defaultContent": ''
				},
				{ "className": 'bold',"data": "name" },
				{ "data": "mask"},
				{ "data": "mirror"},
				{ "data": "defaultsFrom"}
				],
				"autoWidth": false,
				"processing": true,
				"order": [[2, 'asc']]
		} );	
		
    $('#persistence_source tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );
 
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format_persistence_source(row.data()) ).show();
            tr.addClass('shown');
        }
    } );
	} );
</script>


<script>

	<?php echo $vservers; ?>
	
	$(document).ready(function() {
		var table = $('#vserver_overview').DataTable( {
			"data": vservers,
			"createdRow": function( row, data, dataIndex ) {
				var remove_partition = "/" + data['partition'] + "/";
				$('td', row).eq(1).html(data['destination'].replace(remove_partition, ""));
				$('td', row).eq(2).html(data['pool'].replace(remove_partition, ""));
			  },
			  "columns": [
				{ "className": 'bold',"data": "name" },
				{ "data": "destination"},
				{ "data": "pool"},
				{ "data": "persistence"}
				],
				"autoWidth": false,
				"processing": true,
				"language": {"processing": "Waiting.... " }
		} );
	} );
</script>





<script>
function format_vservers ( d ) {
    // `d` is the original data object for the row

    return '<table cellpadding="5" cellspacing="0" border="0" class="table table-bordered">'+
         '<tr>'+
          '<td style="width:150px"><b>Partition:</b></td>'+
          '<td >'+d.partition+'</td>'+
        '</tr>'+
		'<tr>'+
          '<td style="width:150px"><b>VLAN Status:</b></td>'+
          '<td >'+d.vlanStatus+'</td>'+
        '</tr>'+
        '<tr>'+
          '<td style="width:150px"><b>VLAN List:</b></td>'+
          '<td >'+d.vlan+'</td>'+
        '</tr>'+        
		'<tr>'+
          '<td style="width:150px"><b>L4 Protocol:</b></td>'+
          '<td >'+d.ipProtocol+'</td>'+
        '</tr>'+
		'<tr>'+
          '<td style="width:150px"><b>Rate Limit:</b></td>'+
          '<td >'+d.rateLimit+'</td>'+
        '</tr>'+
			'<tr>'+
          '<td style="width:150px"><b>Throughput:</b></td>'+
          '<td >'+d.throughputCapacity+'</td>'+
        '</tr>'+
        '<tr>'+
          '<td style="width:150px"><b>Service Down Action:</b></td>'+
          '<td >'+d.serviceDownImmediateAction+'</td>'+
        '</tr>'+        
		'<tr>'+
          '<td style="width:150px"><b>Translate Address:</b></td>'+
          '<td >'+d.translateAddress+'</td>'+
        '</tr>'+
		'<tr>'+
          '<td style="width:150px"><b>Translate Port:</b></td>'+
          '<td >'+d.translatePort+'</td>'+
        '</tr>'+
			'<tr>'+
          '<td style="width:150px"><b>Source Port:</b></td>'+
          '<td >'+d.sourcePort+'</td>'+
        '</tr>'+
		'<tr>'+
          '<td style="width:150px"><b>Source Allowed:</b></td>'+
          '<td >'+d.source+'</td>'+
        '</tr>'+
		'<tr>'+
          '<td style="width:150px"><b>Eviction Policy:</b></td>'+
          '<td >'+d.flowEvictionPolicy+'</td>'+
        '</tr>'+
		'<tr>'+
          '<td style="width:150px"><b>Syn Cookies:</b></td>'+
          '<td >'+d.synCookieStatus+'</td>'+
        '</tr>'+
		'<tr>'+
          '<td style="width:150px"><b>Auto Last Hop:</b></td>'+
          '<td >'+d.autoLasthop+'</td>'+
        '</tr>'+

		
        '</table>';
}

	<?php echo $vservers; ?>
	
	$(document).ready(function() {
		var table = $('#vservers').DataTable( {
			"data": vservers,
			"createdRow": function( row, data, dataIndex ) {
				var remove_partition = "/" + data['partition'] + "/";
				$('td', row).eq(2).html(data['destination'].replace(remove_partition, ""));
				$('td', row).eq(3).html(data['pool'].replace(remove_partition, ""));
				$('td', row).eq(4).html(data['rules'].join('<br>').replace(remove_partition, ""));
				$('td', row).eq(5).html(data['profileName'].join('<br>'));
				$('td', row).eq(6).html(data['policyName'].join('<br>'));
				if (data['enabled']== "True" )
				  $('td', row).eq(9).html("<i class='fa fa-check-circle fa-2x green'></i>");
				else 
				   $('td', row).eq(9).html("<i class='fa fa-minus-circle fa-2x red' ></i>");
			},
			"columns": [
				{
					"className":      'details-control',
					"orderable":      false,
					"data":           null,
					"defaultContent": ''
				},
				{ "className": 'bold',"data": "name" },
				{ "data": "destination"},
				{ "data": "pool"},
				{ "data": null},
				{ "data": null},
				{ "data": null},
				{ "data": "persistence"},
				{ "data": "securityLogProfiles"},
				{ "className": 'attacks',"data": "enabled"}
				],	
				"autoWidth": false,
				"processing": true,
				"order": [[2, 'asc']]
		} );	
		
    $('#vservers tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );
 
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format_vservers(row.data()) ).show();
            tr.addClass('shown');
        }
    } );
	} );
</script>


<script>
function format_profile_http ( d ) {
    // `d` is the original data object for the row

    return '<table cellpadding="5" cellspacing="0" border="0" class="table table-bordered">'+
        '<tr>'+
          '<td style="width:150px"><b>Partition:</b></td>'+
          '<td >'+d.partition+'</td>'+
        '</tr>'+ 
        '<tr>'+
          '<td style="width:150px"><b>X-Forwarded-For:</b></td>'+
          '<td >'+d.xff+'</td>'+
        '</tr>'+ 
        '<tr>'+
          '<td style="width:150px"><b>HSTS:</b></td>'+
          '<td >'+d.hsts+'</td>'+
        '</tr>'+ 		
        '</table>';
}

	<?php echo $profile_http; ?>
	
	$(document).ready(function() {
		var table = $('#profile_http').DataTable( {
			"data": profile_http,
			"columns": [
				{
					"className":      'details-control',
					"orderable":      false,
					"data":           null,
					"defaultContent": ''
				},
				{ "className": 'bold',"data": "name" },
				{ "data": "encryptCookies"},
				{ "data": "fallbackHost"},
				{ "data": "defaultsFrom"}
				],
				"autoWidth": false,
				"processing": true,
				"order": [[2, 'asc']]
		} );	
		
    $('#profile_http tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );
 
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format_profile_http(row.data()) ).show();
            tr.addClass('shown');
        }
    } );
	} );
</script>



<script>
function format_profile_tcp ( d ) {
    // `d` is the original data object for the row

    return '<table cellpadding="5" cellspacing="0" border="0" class="table table-bordered">'+
        '<tr>'+
          '<td style="width:150px"><b>Partition:</b></td>'+
          '<td >'+d.partition+'</td>'+
        '</tr>'+ 
        '</table>';
}

	<?php echo $profile_tcp; ?>
	
	$(document).ready(function() {
		var table = $('#profile_tcp').DataTable( {
			"data": profile_tcp,
			"columns": [
				{
					"className":      'details-control',
					"orderable":      false,
					"data":           null,
					"defaultContent": ''
				},
				{ "className": 'bold',"data": "name" },
				{ "data": "idleTimeout"},
				{ "data": "keepAliveInterval"},
				{ "data": "defaultsFrom"}
				],
				"autoWidth": false,
				"processing": true,
				"order": [[2, 'asc']]
		} );	
		
    $('#profile_tcp tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );
 
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format_profile_tcp(row.data()) ).show();
            tr.addClass('shown');
        }
    } );
	} );
</script>



<script>
function format_profile_ssl ( d ) {
    // `d` is the original data object for the row

    return '<table cellpadding="5" cellspacing="0" border="0" class="table table-bordered">'+
        '<tr>'+
          '<td style="width:150px"><b>Partition:</b></td>'+
          '<td >'+d.partition+'</td>'+
        '</tr>'+ 
        '</table>';
}

	<?php echo $profile_ssl; ?>
	
	$(document).ready(function() {
		var table = $('#profile_ssl').DataTable( {
			"data": profile_ssl,
			"columns": [
				{
					"className":      'details-control',
					"orderable":      false,
					"data":           null,
					"defaultContent": ''
				},
				{ "className": 'bold',"data": "name" },
				{ "data": "cert"},
				{ "data": "key"},
				{ "data": "cipherGroup"},
				{ "data": "ciphers"},
				{ "data": "defaultsFrom"}
				],
				"autoWidth": false,
				"processing": true
		} );	
		
    $('#profile_ssl tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );
 
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format_profile_ssl(row.data()) ).show();
            tr.addClass('shown');
        }
    } );
	} );
</script>



<script>
function format_ssl_cert ( d ) {
    // `d` is the original data object for the row

    return '<table cellpadding="5" cellspacing="0" border="0" class="table table-bordered">'+
        '<tr>'+
          '<td style="width:150px"><b>Partition:</b></td>'+
          '<td >'+d.partition+'</td>'+
        '</tr>'+ 
        '<tr>'+
          '<td style="width:150px"><b>Create Time:</b></td>'+
          '<td >'+d.createTime+'</td>'+
        '</tr>'+ 
        '</table>';
}

	<?php echo $ssl_cert; ?>
	
	$(document).ready(function() {
		var table = $('#ssl_cert').DataTable( {
			"data": ssl_cert,
			"columns": [
				{
					"className":      'details-control',
					"orderable":      false,
					"data":           null,
					"defaultContent": ''
				},
				{ "className": 'bold',"data": "name" },
				{ "data": "createdBy"},
				{ "data": "expirationString"}
				],
				"autoWidth": false,
				"processing": true,
				"order": [[2, 'asc']]
		} );	
		
    $('#ssl_cert tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );
 
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format_ssl_cert(row.data()) ).show();
            tr.addClass('shown');
        }
    } );
	} );
</script>



<script>
function format_monitor_http ( d ) {
    // `d` is the original data object for the row

    return '<table cellpadding="5" cellspacing="0" border="0" class="table table-bordered">'+
        '<tr>'+
          '<td style="width:150px"><b>Partition:</b></td>'+
          '<td >'+d.partition+'</td>'+
        '</tr>'+ 
        '<tr>'+
          '<td style="width:150px"><b>Parent:</b></td>'+
          '<td >'+d.defaultsFrom+'</td>'+
        '</tr>'+ 
        '</table>';
}

	<?php echo $monitor_http; ?>
	
	$(document).ready(function() {
		var table = $('#monitor_http').DataTable( {
			"data": monitor_http,
			"columns": [
				{
					"className":      'details-control',
					"orderable":      false,
					"data":           null,
					"defaultContent": ''
				},
				{ "className": 'bold',"data": "name" },
				{ "data": "interval"},
				{ "data": "timeout"},
				{ "data": "send"},
				{ "data": "recv"}
				],
				"autoWidth": false,
				"processing": true
		} );	
		
    $('#monitor_http tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );
 
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format_monitor_http(row.data()) ).show();
            tr.addClass('shown');
        }
    } );
	} );
</script>


<script>
function format_monitor_https ( d ) {
    // `d` is the original data object for the row

    return '<table cellpadding="5" cellspacing="0" border="0" class="table table-bordered">'+
        '<tr>'+
          '<td style="width:150px"><b>Partition:</b></td>'+
          '<td >'+d.partition+'</td>'+
        '</tr>'+ 
        '<tr>'+
          '<td style="width:150px"><b>Parent:</b></td>'+
          '<td >'+d.defaultsFrom+'</td>'+
        '</tr>'+ 
        '</table>';
}

	<?php echo $monitor_https; ?>
	
	$(document).ready(function() {
		var table = $('#monitor_https').DataTable( {
			"data": monitor_https,
			"columns": [
				{
					"className":      'details-control',
					"orderable":      false,
					"data":           null,
					"defaultContent": ''
				},
				{ "className": 'bold',"data": "name" },
				{ "data": "interval"},
				{ "data": "timeout"},
				{ "data": "send"},
				{ "data": "recv"}
				],
				"autoWidth": false,
				"processing": true
		} );	
		
    $('#monitor_https tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );
 
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format_monitor_https(row.data()) ).show();
            tr.addClass('shown');
        }
    } );
	} );
</script>


<script>
function format_monitor_other ( d ) {
    // `d` is the original data object for the row
	console.log( d );
    return '<table cellpadding="5" cellspacing="0" border="0" class="table table-bordered">'+
        '<tr>'+
          '<td style="width:150px"><b>Partition:</b></td>'+
          '<td >'+d.partition+'</td>'+
        '</tr>'+ 
        '<tr>'+
          '<td style="width:150px"><b>Parent:</b></td>'+
          '<td >'+d.defaultsFrom+'</td>'+
        '</tr>'+ 
        '</table>';
}

	<?php echo $monitor_other; ?>
	
	$(document).ready(function() {
		var table = $('#monitor_other').DataTable( {
			"data": monitor_other,
			"columns": [
				{
					"className":      'details-control',
					"orderable":      false,
					"data":           null,
					"defaultContent": ''
				},
				{ "className": 'bold',"data": "name" },
				{ "data": "interval"},
				{ "data": "timeout"},
				{ "data": "proto"}
				],
				"autoWidth": false,
				"processing": true
		} );	

    $('#monitor_other tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );
 
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format_monitor_other(row.data()) ).show();
            tr.addClass('shown');
        }
    } );
	} );
</script>





<script>
function format_rules ( d ) {
    // `d` is the original data object for the row
	console.log( d );
    return '<table cellpadding="5" cellspacing="0" border="0" class="table table-bordered">'+
        '<tr>'+
          '<td style="width:150px"><b>iRule Details:</b></td>'+
          '<td >'+d.apiAnonymous.replace(/\n/g, "<br>")+'</td>'+
        '</tr>'+ 
        '</table>';
}

	<?php echo $rules; ?>
	
	$(document).ready(function() {
		var table = $('#rules').DataTable( {
			"data": rules,
			"columns": [
				{
					"className":      'details-control',
					"orderable":      false,
					"data":           null,
					"defaultContent": ''
				},
				{ "className": 'bold',"data": "name" }
				],
				"autoWidth": false,
				"processing": true
		} );	

    $('#rules tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );
 
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format_rules(row.data()) ).show();
            tr.addClass('shown');
        }
    } );
	} );
</script>


<script>
	<?php echo $suggestions; ?>
	
	$(document).ready(function() {
			var table = $('#suggestions').DataTable( {
			"data": suggestions,
			"createdRow": function( row, data, dataIndex ) {
				if ( data['severity'] == "error" )
				  $('td', row).eq(0).html("<i class='fa fa-times-circle fa-2x red'></i>");
				if ( data['severity'] == "warning" )
				  $('td', row).eq(0).html("<i class='fa fa-warning fa-2x orange' ></i>");				  
				if ( data['severity'] == "info" )
				  $('td', row).eq(0).html("<i class='fa fa-info-circle fa-2x' ></i>");
				  $('td', row).eq(4).html("<i class='fa fa-trash fa-2x' ></i>");  
			  },

			"columns": [
				{"className":'attacks',"data":"severity"},
				{"data": "txt" },
				{ "className": 'attacks',"data": "severity"},
				{ "className": 'attacks',"data": "section"},
				{ "className": 'delete_button',"data": null},
				{ "data": "score"}
				],
				"columnDefs": [
				{
				  "targets": [5],
				  "visible": false
				}
				],				
				"autoWidth": false,
				"processing": true,
				"order": [[5, 'desc']]
		} );	

		$('#suggestions tbody').on( 'click', '.delete_button', function () {

    	   	var idx = table.row(this).index();
    	   	var data = table.cell( idx, 2).data();
    	   	var delta_score = parseInt(table.cell( idx, 5).data());
    	   	var error = parseInt($("#var_error").text());   	   
    	   	var warning = parseInt($("#var_warning").text());
    	   	var info = parseInt($("#var_info").text());
    	   	var current_score = parseInt($(".current_score").text());
    	   	var new_score = current_score + delta_score;
    	   	if(data=="error")
    	   	{
    	   		error = error - 1;
    	   		$("#var_error").html(error); 
    	   	}
    	   	   	   
    	   	if(data=="warning")
    	   	{
    	   		warning = warning - 1;
    	   		$("#var_warning").html(warning); 
    	   	}    	   	
    	   	if(data=="info")
    	   	{
    	   		info = info - 1;
    	   		$("#var_info").html(info); 
    	   	}   
    	   	$(".current_score").html(new_score);
    	   	
			if (new_score <60)
			{
				$(".score").html('<span class="badge" style="font-size:128px; padding:20px 36px;; background-color:red">F</span>');
			}
			if (new_score >=60 && new_score <70)
			{
				$(".score").html('<span class="badge" style="font-size:128px; padding:20px 36px;; background-color:orange ">D</span>');
			}
			if (new_score >=70 && new_score <80)
			{
				$(".score").html('<span class="badge" style="font-size:128px; padding:20px 36px;; background-color:gray;">C</span>');
			}
			if (new_score >=80 && new_score <90)
			{
				$(".score").html('<span class="badge" style="font-size:128px; padding:20px 36px;; background-color:#1D9B1E">B</span>');
			}			
			if (new_score >=90 )
			{
				$(".score").html('<span class="badge" style="font-size:128px; padding:20px 36px;; background-color:#30CE31">A</span>');
			}	

    	   	table.row(this).remove().draw( false );
			$('#save').removeAttr("disabled");
    	   	
    } );

	} );
</script>


  </body>
</html>
